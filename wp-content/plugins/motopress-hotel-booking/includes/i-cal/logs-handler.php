<?php

namespace MPHB\iCal;

class LogsHandler {

	/**
	 *
	 * @param array $processDetails ["logs", "stats" => ["total", "succeed", "skipped", "failed"]]
	 * @param array $options [ "showLogHeading" ]. All fields are optional.
	 *
	 * @see \MPHB\iCal\Importer::addLog()
	 */
	public function display( $processDetails, $options = array() ){
		$logs	 = $processDetails['logs'];
		$stats	 = $processDetails['stats'];
		$options = array_merge(
			array(
				'showLogHeading'	 => true   // %Room title% (ID: %room ID%)
			),
			$options
		);

		$this->displayTitle();

		$this->displayStats( $stats );

		$this->displayLogs( $logs, $options['showLogHeading'] );
	}

	public function displayTitle(){
		echo '<h3>';
		_e( 'Process Information', 'motopress-hotel-booking' );
		echo '</h3>';
	}

	/**
	 *
	 * @param array $stats Array with keys "total", "succeed", "skipped" and "failed".
	 */
	public function displayStats( $stats ){
		echo '<p class="mphb-import-stats">';
		echo sprintf( __( 'Total bookings: %s', 'motopress-hotel-booking' ), '<span class="mphb-total">' . $stats['total'] . '</span>' );
		echo '<br />';
		echo sprintf( __( 'Success bookings: %s', 'motopress-hotel-booking' ), '<span class="mphb-succeed">' . $stats['succeed'] . '</span>' );
		echo '<br />';
		echo sprintf( __( 'Skipped bookings: %s', 'motopress-hotel-booking' ), '<span class="mphb-skipped">' . $stats['skipped'] . '</span>' );
		echo '<br />';
		echo sprintf( __( 'Failed bookings: %s', 'motopress-hotel-booking' ), '<span class="mphb-failed">' . $stats['failed'] . '</span>' );
		echo '</p>';
	}

	/**
	 *
	 * @param array $logs
	 * @param bool $showLogHeading Optional. TRUE by default.
	 */
	public function displayLogs( $logs = array(), $showLogHeading = true ){
		echo '<ol class="mphb-logs">';
		foreach ( $logs as $log ) {
			echo $this->logToHtml( $log, $showLogHeading );
		}
		echo '</ol>';
	}

	public function displayProgress(){
		echo '<div class="mphb-progress">';
		echo '<div class="mphb-progress__bar"></div>';
		echo '<div class="mphb-progress__text">0%</div>';
		echo '</div>';
	}

	/**
	 *
	 * @param bool $disabled
	 */
	public function displayAbortButton( $disabled = false ){
		$disabledAttr = $disabled ? ' disabled="disabled"' : '';
		echo '<button class="button mphb-abort-process"' . $disabledAttr . '>' . __( 'Abort Process', 'motopress-hotel-booking' ) . '</button>';
	}

	/**
	 *
	 * @param bool $disabled
	 */
	public function displayClearButton( $disabled = false ){
		$disabledAttr = $disabled ? ' disabled="disabled"' : '';
		echo '<button class="button mphb-clear-all"' . $disabledAttr . '>' . __( 'Delete All Logs', 'motopress-hotel-booking' ) . '</button>';
	}

	public function displayExpandAllButton(){
		echo '<button class="button-link mphb-expand-all">' . __( 'Expand All', 'motopress-hotel-booking' ) . '</button>';
	}

	public function displayCollapseAllButton(){
		echo '<button class="button-link mphb-collapse-all">' . __( 'Collapse All', 'motopress-hotel-booking' ) . '</button>';
	}

	/**
	 *
	 * @param array $log Log entry ["level", "message", "context"].
	 * @param bool $showLogHeading Optional. TRUE by default.
	 *
	 * @return string
	 */
	public function logToHtml( $log, $showHeading = true ){

		$log = array_merge(
			array(
				'level'		 => 'info',
				'message'	 => '',
				'context'	 => array()
			),
			$log
		);

		/**
		 * @var string $level "success", "info", "warning", "error" etc.
		 * @var string $message
		 * @var array $context Event info: ["roomId", "uid", "prodid", "checkIn", "checkOut"]; all fields are optional
		 */
		extract( $log );

		$roomId	 = isset( $context['roomId'] ) ? (int) $context['roomId'] : 0;
		// If we have "uid", we also have "checkIn" and "checkOut", see validity check in \MPHB\iCal\iCal::getEventsData()
		$event	 = isset( $context['uid'] ) ? $context : array();

		$html = '';

		// Add title
		if ( $showHeading && $roomId > 0 ) {
			$room = MPHB()->getRoomRepository()->findById( $roomId );
			if ( $room ) {
				$html .= '<b>' . sprintf( '"%1$s" (ID %2$d)', $room->getTitle(), $roomId ) . '</b>';
			} else {
				$html .= '<b>' . sprintf( '(ID %d)', $roomId ) . '</b>';
			}
		}

		// Build "event" part:
		//		%UID%, %checkIn% - %checkOut%
		//		%message%
		$eventHtml = '';
		if ( !empty( $event ) ) {
			$uid		 = $event['uid'];
			$checkIn	 = str_replace( '-', '', $event['checkIn'] );
			$checkOut	 = str_replace( '-', '', $event['checkOut'] );
			$eventHtml .= '<code>' . "{$uid}, {$checkIn} - {$checkOut}" . '</code><br/>';
		}
		$eventHtml .= $message;

		// Add "event" part to result HTML
		if ( !empty( $eventHtml ) ) {
			$html .= '<p class="notice notice-' . $level . '">';
			$html .= $eventHtml;
			$html .= '</p>';
		}

		return !empty( $html ) ? '<li>' . $html . '</li>' : $html;
	}

	/**
	 * Build HTML for each log.
	 *
	 * @param array $logs
	 * @param bool $showLogHeading Optional. TRUE by default
	 *
	 * @return array
	 */
	public function logsToHtml( $logs, $showLogHeading = true ){
		$logsHtml = array();
		foreach ( $logs as $log ) {
			$logsHtml[] = $this->logToHtml( $log, $showLogHeading );
		}
		return $logsHtml;
	}

	public function buildNotice( $succeedCount, $failedCount ){
		$message  = _n( 'All done! %1$d booking was successfully added.', 'All done! %1$d bookings were successfully added.', $succeedCount, 'motopress-hotel-booking');
		$message .= _n( ' There was %2$d failure.', ' There were %2$d failures.', $failedCount, 'motopress-hotel-booking' );
		$message  = sprintf( $message, $succeedCount, $failedCount );

		$notice = '<div class="updated notice notice-success is-dismissible">';
		$notice .= '<p>' . $message . '</p>';
		$notice .= '</div>';

		return $notice;
	}

	/**
	 *
	 * @param array $logs
	 * @return boolean
	 */
	public function checkErrorLogs( $logs ){
		$haveErrors = false;
		foreach ( $logs as $log ) {
			if ( $log['level'] === 'error' ) {
				$haveErrors = true;
				break;
			}
		}
		return $haveErrors;
	}

	/**
	 *
	 * @param array $logs
	 * @param string $status success, info, warning, error
	 * @return int
	 */
	public function calcLogs( $logs, $status ){
		$count = 0;
		foreach ( $logs as $log ) {
			if ( $log['level'] === $status ) {
				$count++;
			}
		}
		return $count;
	}

}
