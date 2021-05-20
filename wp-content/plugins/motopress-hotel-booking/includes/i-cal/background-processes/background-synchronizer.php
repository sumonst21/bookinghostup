<?php

namespace MPHB\iCal\BackgroundProcesses;

use \MPHB\Exceptions\NoEnoughExecutionTimeException;
use \MPHB\Exceptions\RequestException;

class BackgroundSynchronizer extends BackgroundWorker {

	protected $action = 'sync';

	/**
	 * Parse new events immediately and add new "import" tasks.
	 */
	public function parseCalendar( $roomId, $calendarUri ){
		$task = array( 'roomId' => $roomId, 'calendarUri' => $calendarUri );
		$this->taskParse( $task );
	}

	/**
	 * @param string $calendarUri Link to external calendar.
	 * @return string
	 * @throws \MPHB\Exceptions\NoEnoughExecutionTimeException
	 * @throws \MPHB\Exceptions\RequestException
	 */
	protected function retrieveCalendarContentFromSource( $calendarUri ){
		$timeLeft = $this->timeLeft(); // How many seconds left until script termination
		$timeout  = min( $timeLeft - 5, self::MAX_REQUEST_TIMEOUT ); // Leave 5 seconds for parsing/batching/logging

		if ( $timeout <= 0 ) {
			throw new NoEnoughExecutionTimeException( sprintf( __( 'Maximum execution time is set to %d seconds.', 'motopress-hotel-booking' ), $timeout ) );
		}

		$response = wp_remote_get( $calendarUri, array( 'timeout' => $timeout ) );

		if ( is_wp_error( $response ) ) {
			throw new RequestException( $response->get_error_message() );
		}

		$calendarContent = wp_remote_retrieve_body( $response );

		return $calendarContent;
	}

	protected function retrieveCalendarNameFromSource( $calendarUri ){
		return $calendarUri;
	}

	protected function taskPullUrls( $roomId ){
		$room	 = MPHB()->getRoomRepository()->findById( $roomId );
		$urls	 = $room ? $room->getSyncUrls() : array();
		$count	 = count( $urls );

		if ( $count > 0 ) {
			$this->addParseTasks( $roomId, $urls );
			$message = sprintf( _n( '%d URL pulled for parsing.', '%d URLs pulled for parsing.', $count, 'motopress-hotel-booking' ), $count );
		} else {
			$message = sprintf( __( 'Skipped. No URLs found for parsing.', 'motopress-hotel-booking' ) );
		}

		$context = array( 'roomId' => $roomId );
		$this->logger->info( $message, $context );

		return false;
	}

	/**
	 *
	 * @param array $task
	 * @return boolean
	 */
	protected function taskOther( $task ){
		$result = parent::taskOther( $task );

		if ( isset( $task['action'] ) && $task['action'] === self::ACTION_PULL_URLS ) {

			if ( !isset( $task['roomId'] ) ) {
				return false;
			}

			$result = $this->taskPullUrls( $task['roomId'] );
		}

		return $result;
	}

}
