<?php

namespace MPHB\BookingRules\Custom;

use \MPHB\BookingRules\RuleVerifiable;

class CustomTypeRules implements RuleVerifiable {

	/**
	 *
	 * @var CustomRule[] Rules Type/All.
	 */
	protected $globalRules = array();

	/**
	 *
	 * @var array Rules Type/X: [["roomId" => int, "rule" => CustomRule], ...].
	 */
	protected $customRules = array();

	protected function __construct( array $globalRules, array $customRules ){
		$this->globalRules = $globalRules;
		$this->customRules = $customRules;
	}

	public function verify( \DateTime $checkInDate, \DateTime $checkOutDate ){
		foreach ( $this->globalRules as $globalRule ) {
			if ( !$globalRule->verify( $checkInDate, $checkOutDate ) ) {
				return false;
			}
		}

		return true;
	}

	public function getLockedRooms( \DateTime $checkInDate, \DateTime $checkOutDate ){
		$lockedRooms = array();

		foreach ( $this->customRules as $customRuleHolder ) {
			$roomId		 = $customRuleHolder['roomId'];
			$customRule	 = $customRuleHolder['rule'];

			if ( !$customRule->verify( $checkInDate, $checkOutDate ) ) {
				$lockedRooms[] = $roomId;
			}
		}

		$lockedRooms = array_unique( $lockedRooms );
		asort( $lockedRooms );

		return array_values( $lockedRooms ); // Get rid of keys from array_unique
	}

	/**
	 *
	 * @return array ["2017-01-01" => ["not_check_in" => true,
	 * "not_check_out" => true, "not_stay_in" => true], "2017-01-02" => ...]
	 */
	public function getGlobalBlockedDates(){
		$dates = array();

		foreach ( $this->globalRules as $rule ) {
			$ruleDates = $rule->getBlockedDays();
			$dates = self::mergeBlockedDates( $dates, $ruleDates );
		}

		ksort( $dates );

		return $dates;
	}

	/**
	 *
	 * @return array ["2017-01-01" => ["not_check_in" => true,
	 * "not_check_out" => true, "not_stay_in" => true], "2017-01-02" => ...]
	 */
	public function getTypeBlockedDates(){
		$dates = array();

		foreach ( $this->customRules as $rule ) {
			$ruleDates = $rule['rule']->getBlockedDays();
			$dates = self::mergeBlockedDates( $dates, $ruleDates );
		}

		ksort( $dates );

		return $dates;
	}

	/**
	 *
	 * @return array ["2017-01-01" => ["not_check_in" => true,
	 * "not_check_out" => true, "not_stay_in" => true], "2017-01-02" => ...]
	 */
	public function getAllBlockedDates(){
		$typeAll = $this->getGlobalBlockedDates();
		$typeX   = $this->getTypeBlockedDates();
		$dates   = self::mergeBlockedDates( $typeAll, $typeX );

		ksort( $dates );

		return $dates;
	}

	/**
	 *
	 * @param array $dates1 [%date% => %restrictions%]
	 * @param array $dates2 [%date% => %restrictions%]
	 *
	 * @return array [%date% => %merged restrictions%]
	 */
	public static function mergeBlockedDates( $dates1, $dates2 ){
		$dates = $dates1;

		foreach ( $dates2 as $date => $restrictions ) {
			if ( !isset( $dates[$date] ) ) {
				$dates[$date] = $restrictions;
			} else {
				foreach ( $restrictions as $param => $value ) {
					$dates[$date][$param] = $dates[$date][$param] || $value;
				}
			}
		}

		return $dates;
	}

	/**
	 *
	 * @param array $typeRules
	 */
	public static function create( $typeRules ){
		$globalRules = array_filter( $typeRules, function( $rule ) {
			return ( $rule['room_id'] == 0 );
		} );

		// Create global rule instances (Type/All)
		$globalInstances = array();
		foreach ( $globalRules as $globalRule ) {
			$globalInstances[] = CustomRule::create( $globalRule );
		}

		// Create custom rule instances (Type/X)
		$customInstances = array();
		foreach ( $typeRules as $rule ) {
			$typeId = (int)$rule['room_type_id'];
			$roomId = (int)$rule['room_id'];

			if ( $roomId == 0 ) {
				continue;
			}

			$customInstances[] = array(
				'roomId' => $roomId,
				'rule'   => CustomRule::create( $rule )
			);
		}

		return new self( $globalInstances, $customInstances );
	}

}
