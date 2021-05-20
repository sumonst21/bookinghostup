<?php

namespace MPHB\iCal\BackgroundProcesses;

class SyncRoomsQueueHandler {

	/**
	 *
	 * @var BackgroundSynchronizer
	 */
	private $bgSyncProcess = null;

	/**
	 *
	 * @var \MPHB\iCal\OptionsHandler
	 */
	private $optionsHandler = null;

	/**
	 *
	 * @param BackgroundSynchronizer $bgSyncProcess
	 */
	public function __construct( $bgSyncProcess ){
		$this->bgSyncProcess	 = $bgSyncProcess;
		$this->optionsHandler	 = new \MPHB\iCal\OptionsHandler( $bgSyncProcess->getIdentifier() . '_rooms_queue' );

		add_action( $bgSyncProcess->getIdentifier() . '_complete', array( $this, 'doNext' ) );
	}

	/**
	 *
	 * @return array
	 * @todo description of structure
	 */
	public function getQueue(){
		$roomsQueue = $this->optionsHandler->getOptionNoCache( 'queue', array() );
		return is_array( $roomsQueue ) ? $roomsQueue : array();
	}

	/**
	 *
	 * @param array $roomIds
	 */
	protected function addToQueue( $roomIds ){
		$roomsQueue	 = $this->getQueue();
		$roomIdsKeys = array_map( function( $roomId ) {
			return time() . '_' . $roomId;
		}, $roomIds );

		$roomsQueue = array_merge( $roomsQueue, $roomIdsKeys );
		$this->optionsHandler->updateOption( 'queue', $roomsQueue );
	}

	/**
	 *
	 * @param string $key
	 * @param array $data
	 */
	protected function storeProcessedData( $key, $data ){
		$processedRooms = $this->getProcessedRoomsData();

		$processedRooms[$key] = $data;

		$this->optionsHandler->updateOption( 'processed_data', $processedRooms );
	}

	/**
	 *
	 * @return array
	 */
	public function getProcessedRoomsData(){
		return $this->optionsHandler->getOptionNoCache( 'processed_data', array() );
	}

	/**
	 *
	 * @return string
	 */
	public function getCurrentRoomKey(){
		return $this->optionsHandler->getOptionNoCache( 'current_key', '' );
	}

	/**
	 *
	 * @param string $roomKey timestamp_roomId
	 * @return int $roomId
	 */
	public static function retrieveTimeFromKey( $roomKey ){
		return (int)preg_replace( '/^(\d+)_\d+/', '$1', $roomKey );
	}

	/**
	 *
	 * @param string $roomKey timestamp_roomId
	 * @return int $roomId
	 */
	public static function retrieveRoomIdFromKey( $roomKey ){
		return (int)preg_replace( '/^\d+_(\d+)/', '$1', $roomKey );
	}

	/**
	 *
	 * @return string key of next room if exists or empty string
	 */
	protected function getNext(){
		$nextRoomKey = '';
		$roomsQueue	 = $this->getQueue();

		if ( $roomsQueue ) {
			$nextRoomKey = array_shift( $roomsQueue );

			$this->optionsHandler->updateOption( 'current_key', $nextRoomKey );
			$this->optionsHandler->updateOption( 'queue', $roomsQueue );
		} else {
			$this->optionsHandler->updateOption( 'current_key', '' );
		}

		return $nextRoomKey;
	}

	public function abort(){
		$this->optionsHandler->updateOption( 'abort', true );
		$this->bgSyncProcess->abort();

		$queueData = $this->getQueueRoomData();
		$this->optionsHandler->updateOption( 'queue', array() );

		if ( !empty( $queueData ) ) {
			$processedData	 = $this->getProcessedRoomsData();
			$processedData	 = array_merge( $processedData, $queueData );
			$this->optionsHandler->updateOption( 'processed_data', $processedData );
		}
	}

	public function clearAll(){
		if ( $this->isInProgress() ) {
			$this->optionsHandler->updateOption( 'clear_all', true );
			$this->abort();
		} else {
			$this->optionsHandler->updateOption( 'processed_data', array() );
		}
	}

	public function removeRoomKey( $roomKey ){

		if ( $this->getCurrentRoomKey() === $roomKey ) {
			$this->optionsHandler->updateOption( 'prevent_store_current', true );
			$this->bgSyncProcess->abort();
			return true;
		}

		$queue		 = $this->getQueue();
		$queueKey	 = array_search( $roomKey, $queue );
		if ( $queueKey !== false ) {
			unset( $queue[$queueKey] );
			$this->optionsHandler->updateOption( 'queue', $queue );
			return true;
		}

		$processedData = $this->getProcessedRoomsData();
		if ( array_key_exists( $roomKey, $processedData ) ) {
			unset( $processedData[$roomKey] );
			$this->optionsHandler->updateOption( 'processed_data', $processedData );
			return true;
		}

		return true;
	}

	/**
	 *
	 * @return bool
	 */
	protected function isClearingAll(){
		return $this->optionsHandler->getOptionNoCache( 'clear_all', false );
	}

	/**
	 *
	 * @return bool
	 */
	protected function isAborting(){
		return $this->optionsHandler->getOptionNoCache( 'abort', false );
	}

	public function doNext(){

		// Prevent process room queue while background synchronizer is in progress
		if ( $this->bgSyncProcess->isInProgress() ) {
			return false;
		}

		$currentRoomKey = $this->getCurrentRoomKey();
		if ( !empty( $currentRoomKey ) ) {

			if ( !$this->optionsHandler->getOptionNoCache( 'prevent_store_current', false ) ) {
				$this->storeProcessedData( $currentRoomKey, $this->getCurrentRoomData() );
			} else {
				$this->optionsHandler->deleteOption( 'prevent_store_current' );
			}

			$this->bgSyncProcess->reset();
			$this->optionsHandler->updateOption( 'current_key', '' );
		}

		if ( $this->isAborting() ) {
			$this->optionsHandler->deleteOption( 'abort' );
			if ( $this->isClearingAll() ) {
				$this->optionsHandler->deleteOption( 'clear_all' );
				$this->optionsHandler->updateOption( 'processed_data', array() );
			}
			return false;
		}

		$nextRoomKey = $this->getNext();

		if ( $nextRoomKey ) {
			$this->bgSyncProcess->addPullUrlTasks( array( self::retrieveRoomIdFromKey( $nextRoomKey ) ) );
		}
	}

	/**
	 *
	 * @return bool
	 */
	public function isInProgress(){
		$queue = $this->getQueue();
		return !empty( $queue ) || $this->bgSyncProcess->isInProgress();
	}

	public function isQueueEmpty(){
		$isEmpty = false;
		if ( !$this->isInProgress() && !$this->hasProcessedData() ) {
			$isEmpty = true;
		}
		return $isEmpty;
	}

	/**
	 *
	 * @param array $roomIds
	 */
	public function sync( $roomIds ){
		$this->addToQueue( $roomIds );
		$this->doNext();
	}

	/**
	 *
	 * @return array
	 */
	public function getCurrentRoomData(){
		$currentRoomKey		 = $this->getCurrentRoomKey();
		$details			 = $this->bgSyncProcess->getProcessDetails();
		$details['roomId']	 = self::retrieveRoomIdFromKey( $currentRoomKey );
		return $details;
	}

	/**
	 *
	 * @return array
	 */
	public function getQueueRoomData(){
		$queueRoomData = array();
		foreach ( $this->getQueue() as $roomKey ) {
			$queueRoomData[$roomKey] = array(
				'roomId' => self::retrieveRoomIdFromKey( $roomKey ),
				'logs'	 => array(),
				'stats'	 => array(
					'total'		 => 0,
					'succeed'	 => 0,
					'failed'	 => 0,
					'skipped'	 => 0
				)
			);
		}
		return $queueRoomData;
	}

	/**
	 * Retrieve queue, current and processed rooms data. Also adds additional
	 * field to each room data: "status".
	 *
	 * @return array
	 */
	public function getFullQueueRoomData(){
		$queued		 = $this->getQueueRoomData();
		$currentKey  = $this->getCurrentRoomKey();
		// Format of the $current differs from $queued and $processed
		$current	 = $this->getCurrentRoomData();
		$processed	 = $this->getProcessedRoomsData();

		// Add "status" and "statusText" to each room
		array_walk( $queued, function( &$element, $key ){
			$element['status'] = array(
				'value' => 'wait',
				'text'  => __( 'Waiting', 'motopress-hotel-booking' ),
				'class' => 'mphb-status-wait'
			);
		} );
		$current['status'] = array(
			'value' => 'in-progress',
			'text'  => __( 'Processing', 'motopress-hotel-booking' ),
			'class' => 'mphb-status-in-progress'
		);
		array_walk( $processed, function( &$element, $key ){
			$element['status'] = array(
				'value' => 'done',
				'text'  => __( 'Done', 'motopress-hotel-booking' ),
				'class' => 'mphb-status-done'
			);
		} );

		// Combine all rooms
		$full = array();
		if ( !empty( $currentKey ) && !isset( $processed[$currentKey] ) ) {
			$full[$currentKey] = $current;
		}
		$full = array_merge( $full, $queued );
		$full = array_merge( $full, $processed );

		return $full;
	}

	/**
	 *
	 * @return bool
	 */
	public function hasProcessedData(){
		$isExists = $this->optionsHandler->checkOption( 'processed_data', array(), 'EXISTS' );
		$isEmpty  = $this->optionsHandler->checkOption( 'processed_data', array() );
		return $isExists && !$isEmpty;
	}

}
