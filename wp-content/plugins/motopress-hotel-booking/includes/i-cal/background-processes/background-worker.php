<?php

namespace MPHB\iCal\BackgroundProcesses;

use \MPHB\Exceptions\NoEnoughExecutionTimeException;
use \MPHB\Exceptions\RequestException;
use \MPHB\iCal\ImportStatus;
use \MPHB\Libraries\WP_Background_Processing\WP_Background_Process;

abstract class BackgroundWorker extends WP_Background_Process {

	const BATCH_SIZE = 1000;

	const ACTION_PULL_URLS	 = 'pull-urls';
	const ACTION_PARSE		 = 'parse';
	const ACTION_IMPORT		 = 'import';

	const MAX_REQUEST_TIMEOUT = 30; // 30 seconds

	/**
	 *
	 *  @var string
	 */
	protected $prefix = 'mphb_ical';

	/**
	 *
	 * @var \MPHB\iCal\Importer
	 */
	protected $importer = null;

	/**
	 *
	 * @var \MPHB\iCal\Logger
	 */
	protected $logger = null;

	/**
	 *
	 * @var \MPHB\iCal\OptionsHandler
	 */
	protected $optionsHandler = null;

	/**
	 * @var int
	 */
	protected $maxExecutionTime = 0;

	public function __construct(){

		// Add blog ID to the prefix (only for multisites and only for IDs 2, 3 and so on)
		$blogId = get_current_blog_id();
		if ( $blogId > 1 ) {
			$this->prefix .= '_' . $blogId;
		}

		parent::__construct();

		$this->importer			 = new \MPHB\iCal\Importer();
		$this->logger			 = new \MPHB\iCal\Logger( $this->identifier );
		$this->optionsHandler	 = new \MPHB\iCal\OptionsHandler( $this->identifier );

		$this->maxExecutionTime = intval( ini_get( 'max_execution_time' ) );
	}

	/**
	 *
	 * @return bool
	 */
	public function isInProgress(){
		// The main check is is_queue_empty(). But we also need to check if the
		// process actually stopped (unlocked) - is_process_running()
		return $this->is_process_running() || !$this->is_queue_empty();
	}

	public function isAborting(){
		return $this->optionsHandler->getOptionNoCache( 'abort', false ); // mphb_ical_upload_abort
	}

	public function touch(){
		if ( !$this->is_process_running() && !$this->is_queue_empty() ) {
			// Background process down, but was not finished. Restart it
			$this->dispatch();
		}
	}

	public function reset(){
		$this->optionsHandler->deleteOption( 'abort' );
		$this->logger->clear();
		$this->optionsHandler->updateOption( 'total', 0 );
		$this->optionsHandler->updateOption( 'succeed', 0 );
		$this->optionsHandler->updateOption( 'skipped', 0 );
		$this->optionsHandler->updateOption( 'failed', 0 );
	}

	public function abort(){
		if ( $this->isInProgress() ) {
			$this->optionsHandler->updateOption( 'abort', true ); // mphb_ical_upload_abort
		}
	}

	protected function complete(){
		parent::complete();

		$this->optionsHandler->deleteOption( 'abort' );

		do_action( $this->identifier . '_complete' );

		// Don't delete logs here: there may be some scripts that did not show
		// all log messages yet
	}

	protected function timeLeft(){
		if ( $this->maxExecutionTime > 0 ) {
			return $this->start_time + $this->maxExecutionTime - time();
		} else {
			return self::MAX_REQUEST_TIMEOUT;
		}
	}

	/**
	 *
	 * @return int
	 */
	public function getTotal(){
		return (int) $this->optionsHandler->getOption( 'total', 0 ); // mphb_ical_upload_total
	}

	/**
	 *
	 * @param int $increment
	 */
	protected function increaseTotal( $increment ){
		$this->optionsHandler->updateOption( 'total', $this->getTotal() + $increment ); // mphb_ical_upload_total
	}

	/**
	 *
	 * @return int
	 */
	public function getProcessed(){
		return $this->getSucceed() + $this->getSkipped() + $this->getFailed();
	}

	/**
	 *
	 * @return int
	 */
	public function getSucceed(){
		return (int) $this->optionsHandler->getOption( 'succeed', 0 ); // mphb_ical_upload_succeed
	}

	/**
	 *
	 * @param int $increment
	 */
	protected function increaseSucceed( $increment ){
		$this->optionsHandler->updateOption( 'succeed', $this->getSucceed() + $increment ); // mphb_ical_upload_succeed
	}

	/**
	 *
	 * @return int
	 */
	public function getSkipped(){
		return (int) $this->optionsHandler->getOption( 'skipped', 0 ); // mphb_ical_upload_skipped
	}

	/**
	 *
	 * @param int $increment
	 */
	protected function increaseSkipped( $increment ){
		$this->optionsHandler->updateOption( 'skipped', $this->getSkipped() + $increment ); // mphb_ical_upload_skipped
	}

	/**
	 *
	 * @return int
	 */
	public function getFailed(){
		return (int) $this->optionsHandler->getOption( 'failed', 0 ); // mphb_ical_upload_failed
	}

	/**
	 *
	 * @param int $increment
	 */
	protected function increaseFailed( $increment ){
		$this->optionsHandler->updateOption( 'failed', $this->getFailed() + $increment ); // mphb_ical_upload_failed
	}

	/**
	 *
	 * @return int Progress value in range [0; 100].
	 */
	public function getProgress(){
		$total		 = $this->getTotal();
		$processed	 = $this->getProcessed();

		if ( $total == 0 ) {
			return $this->isInProgress() ? 0 : 100;
		} else {
			return min( round( $processed / $total * 100 ), 100 );
		}
	}

	public function getIdentifier(){
		return $this->identifier;
	}

	/**
	 * @param array $task ["action", "roomId", "calendarUri", "event"]. "calendarUri"
	 * only required for ACTION_PARSE action and "event" required only for ACTION_IMPORT
	 * action.
	 * @return array|false
	 */
	protected function task( $task ){

		if ( $this->isAborting() ) {
			$this->cancel_process();
			return false;
		}

		if ( !isset( $task['action'] ) ) {
			return false;
		}

		switch ( $task['action'] ) {
			case self::ACTION_PARSE:
				$task	 = $this->taskParse( $task );
				break;
			case self::ACTION_IMPORT:
				$task	 = $this->taskImport( $task );
				break;
			default:
				$task	 = $this->taskOther( $task );
				break;
		}

		return $task;
	}

	protected function taskOther( $task ){
		return false;
	}

	/**
	 *
	 * @param array $roomIds
	 */
	public function addPullUrlTasks( $roomIds ){
		$tasks = array_map( function( $roomId ) {
			return array(
				// Cannot access self:: in callback on PHP 5.3 (fatal error)
				'action' => BackgroundWorker::ACTION_PULL_URLS,
				'roomId' => $roomId
			);
		}, $roomIds );

		$this->addTasks( $tasks );
	}

	public function addParseTasks( $roomId, $calendarUris ){
		$tasks = array_map( function( $calendarUri ) use ( $roomId ) {
			return array(
				// Cannot access self:: in callback on PHP 5.3 (fatal error)
				'action'		 => BackgroundWorker::ACTION_PARSE,
				'roomId'		 => $roomId,
				'calendarUri'	 => $calendarUri
			);
		}, $calendarUris );

		$this->addTasks( $tasks );
	}

	public function addImportTasks( $roomId, $eventValues ){
		$tasks = array_map( function( $values ) use ( $roomId ) {
			return array(
				// Cannot access self:: in callback on PHP 5.3 (fatal error)
				'action' => BackgroundWorker::ACTION_IMPORT,
				'roomId' => $roomId,
				'event'	 => $values
			);
		}, $eventValues );

		$this->addTasks( $tasks );
	}

	/**
	 * also reset db structure and dispatch in some cases
	 * @todo describe task structure.
	 *
	 * @param array $tasks
	 */
	protected function addTasks( $tasks ){
		// Save state before adding new tasks into queue
		$isInProgress = $this->isInProgress();

		// Reset values (only for new process)
		if ( !$isInProgress ) {
			$this->reset();
		}

		// Save new batches
		$batches = array_chunk( $tasks, self::BATCH_SIZE );
		foreach ( $batches as $batch ) {
			$this->data( $batch )->save();
		}

		if ( !$isInProgress ) {
			// Start new process or restart current terminated process
			$this->dispatch();
		}
	}

	/**
	 * @throws \MPHB\Exceptions\NoEnoughExecutionTimeException
	 * @throws \MPHB\Exceptions\RequestException
	 */
	abstract protected function retrieveCalendarContentFromSource( $calendarUri );

	/**
	 * Firstly required for uploader: returns real file name instead of tmp
	 * name, like "/tmp/phpPRrGqo".
	 */
	abstract protected function retrieveCalendarNameFromSource( $calendarUri );

	/**
	 * @param array $task ["roomId", "calendarUri"].
	 * @return array|false
	 */
	protected function taskParse( $task ){
		$roomId			 = $task['roomId'];
		$calendarUri	 = $task['calendarUri'];
		$calendarName	 = $this->retrieveCalendarNameFromSource( $calendarUri );
		$logContext		 = array( 'roomId' => $roomId );

		try {
			/**
			 * @throws \MPHB\Exceptions\NoEnoughExecutionTimeException
			 * @throws \MPHB\Exceptions\RequestException
			 */
			$calendarContent = $this->retrieveCalendarContentFromSource( $calendarUri );
			/**
			 * @throws \Exception
			 */
			$ical			 = new \MPHB\iCal\iCal( $calendarContent );
			$eventValues	 = $ical->getEventsData( $roomId );
			$eventsCount	 = count( $eventValues );

			if ( $eventsCount > 0 ) {
				// This info can replace some messages from background process
				// if log it after the process starts
				$message = sprintf( _nx( '%1$d event found in calendar %2$s', '%1$d events found in calendar %2$s', $eventsCount, '%s - calendar URI or calendar filename', 'motopress-hotel-booking' ), $eventsCount, $calendarName );
				$this->logger->info( $message, $logContext );

				//---------------------------------------------------------------------
				//Host-Up - including routine to delete previous integrated bookings //

				 //$this->removeIntBookings( $roomId );

				//Host-Up - including routine to delete previous integrated bookings //
				//---------------------------------------------------------------------

				$this->addImportTasks( $roomId, $eventValues );
				$this->increaseTotal( $eventsCount );

			} else {
				if ( empty( $calendarContent ) ) {
					$this->logger->warning( sprintf( _x( 'Calendar source is empty (%s)', '%s - calendar URI or calendar filename', 'motopress-hotel-booking' ), $calendarName ), $logContext );
				} else {
					$this->logger->warning( sprintf( _x( 'Calendar file is not empty, but there are no events in %s', '%s - calendar URI or calendar filename', 'motopress-hotel-booking' ), $calendarName ), $logContext );
				}
			}

		} catch ( NoEnoughExecutionTimeException $e ) {
			// Stop executing ACTION_PARSE taks, restart the process and give
			// more time to request files
			add_filter( $this->identifier . '_time_exceeded', '__return_true' );

			// Here can be problems on hosts with low max_execution_time:
			// - WP Background Processing library does not check the execution
			//   time option and always schedule 20 seconds for every handle
			//   cycle; so the process can fall and restart only by cron (only
			//   every 5 minutes);
			// - process can go into an infinite loop, restarting every time
			//   because of negative timeout.

			return $task;

		} catch ( RequestException $e ) {
			$this->logger->error( sprintf( __( 'Error while loading calendar (%1$s): %2$s', 'motopress-hotel-booking' ), $calendarUri, $e->getMessage() ) );
		} catch ( \Exception $e ) {
			$this->logger->error( sprintf( _x( 'Parse error. %s', '%s - error description', 'motopress-hotel-booking' ), $e->getMessage() ), $logContext );
		}

		return false;
	}

	/**
	 * @param array $task ["roomId", "event"].
	 * @return array|false
	 */
	protected function taskImport( $task ){
		$roomId			 = $task['roomId'];
		$eventValues	 = $task['event'];

		$importStatus	 = $this->importer->import( $roomId, $eventValues );

		switch ( $importStatus ){
			case ImportStatus::FAILED:
				$this->logger->error( $this->importer->getLastMessage(), $eventValues );
				$this->increaseFailed( 1 );
				break;

			case ImportStatus::SKIPPED:
				$this->logger->info( $this->importer->getLastMessage(), $eventValues );
				$this->increaseSkipped( 1 );
				break;

			case ImportStatus::SUCCESS:
				$this->logger->success( $this->importer->getLastMessage(), $eventValues );
				$this->increaseSucceed( 1 );
				break;
		}

		return false;
	}

	/**
	 *
	 * @return \MPHB\iCal\Logger
	 */
	public function getLogger(){
		return $this->logger;
	}

	/**
	 *
	 * @return array
	 */
	public function getProcessDetails(){
		return array(
			'logs'	 => $this->logger->getLogs(),
			'stats'	 => array(
				'total'		 => $this->getTotal(),
				'succeed'	 => $this->getSucceed(),
				'skipped'	 => $this->getSkipped(),
				'failed'	 => $this->getFailed()
			)
		);
	}


	//---------------------------------------------------------------------
	//Host-Up - including routine to delete previous integrated bookings //

	public function removeIntBookings( $roomId ){

		$huId = array(
			'rooms'			 => $roomId
		);

		$huBookings = MPHB()->getBookingRepository()->findAll( $huId );

		foreach ($huBookings as $huBooking) {

			$bookingId = $huBooking->getID();
			$bookingIcal = get_post_meta( $bookingId, 'mphb_ical_prodid', true );

			if ($bookingIcal) {
				wp_delete_post($bookingId);
			}
		}

	}
	//---------------------------------------------------------------------
	//Host-Up - including routine to delete previous integrated bookings //


}
