<?php

namespace MPHB\iCal\BackgroundProcesses;

class BackgroundUploader extends BackgroundWorker {

	protected $action = 'upload';

	/**
	 * There are multiple calls of method reset() in uploader. Limit them to 1.
	 *
	 * @var bool
	 */
	protected $wasResetOnce = false;

	public function reset() {
		if ( !$this->wasResetOnce ) {
			parent::reset();
			$this->wasResetOnce = true;
		}
	}

	/**
	 * Parse new events immediately and add new "import" tasks.
	 * @param int $roomId
	 * @param string $calendarUri
	 */
	public function parseCalendar( $roomId, $calendarUri ){
		// Reset stats, don't wait for reset() in BackgroundWorker::addTasks().
		// reset() in BackgroundWorker::addTasks() will not work if the file is
		// bad and there will be no new tasks. So iCalImportMenuPage will
		// display progress bar and logs of the previous import (what is wrong)
		if ( !$this->isInProgress() ) {
			$this->reset();
		}

		$task = array( 'roomId' => $roomId, 'calendarUri' => $calendarUri );
		$this->taskParse( $task );
	}

	/**
	 * @param string $calendarUri
	 * @return string
	 */
	protected function retrieveCalendarContentFromSource( $calendarUri ){
		$calendarContent = @file_get_contents( $calendarUri );
		if ( $calendarContent === false ) {
			/**
			 * @todo add context to log
			 */
			$this->logger->error( __( 'Cannot read uploaded file', 'motopress-hotel-booking' ) );
			return '';
		} else {
			return $calendarContent;
		}
	}

	protected function retrieveCalendarNameFromSource( $calendarUri ){
		if ( isset( $_FILES['import'] ) ) {
			return $_FILES['import']['name'];
		} else {
			return $calendarUri;
		}
	}

}
