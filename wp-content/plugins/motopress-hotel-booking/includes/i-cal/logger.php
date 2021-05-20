<?php

namespace MPHB\iCal;

class Logger {

	/**
	 *
	 * @var string
	 */
	protected $id;

	/**
	 *
	 * @var string
	 */
	protected $optionName;

	/**
	 *
	 * @param string $id Used for compose unique option name.
	 */
	public function __construct( $id ){
		$this->id			 = $id;
		$this->optionName	 = $this->id . '_logs';

		add_option( $this->optionName, array(), '', 'no' );
	}

	/**
	 * <b>Warning: it's not a part of PSR-3.</b>
	 *
	 * @param string $message
	 * @param array $context Room ID ["roomId"] or event data:
	 * ["roomId", "prodid", "uid", "checkIn", "checkOut"]
	 */
	public function success( $message, $context = array() ){
		$this->log( 'success', $message, $context );
	}

	/**
	 *
	 * @param string $message
	 * @param array $context Room ID ["roomId"] or event data:
	 * ["roomId", "prodid", "uid", "checkIn", "checkOut"]
	 */
	public function info( $message, $context = array() ){
		$this->log( 'info', $message, $context );
	}

	/**
	 *
	 * @param string $message
	 * @param array $context Room ID ["roomId"] or event data:
	 * ["roomId", "prodid", "uid", "checkIn", "checkOut"]
	 */
	public function warning( $message, $context = array() ){
		$this->log( 'warning', $message, $context );
	}

	/**
	 *
	 * @param string $message
	 * @param array $context Room ID ["roomId"] or event data:
	 * ["roomId", "prodid", "uid", "checkIn", "checkOut"]
	 */
	public function error( $message, $context = array() ){
		$this->log( 'error', $message, $context );
	}

	/**
	 *
	 * @param string $level Type of log. Possible values: success, info, warning, error
	 * @param string $message
	 * @param array $context Room ID ["roomId"] or event data:
	 * ["roomId", "prodid", "uid", "checkIn", "checkOut"]
	 */
	public function log( $level, $message, $context = array() ){
		$entry = array(
			'level'		 => $level,
			'message'	 => $message,
			'context'	 => $context
		);

		$logs	 = $this->getLogs();
		$logs[]	 = $entry;

		update_option( $this->optionName, $logs );
	}

	/**
	 *
	 * @return array
	 */
	public function getLogs(){
		return (array) get_option( $this->optionName, array() );
	}

	/**
	 * Clear logs
	 */
	public function clear(){
		update_option( $this->optionName, array() );
	}

}
