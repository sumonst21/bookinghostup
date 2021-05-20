<?php

namespace MPHB\BookingRules\Custom;

use \MPHB\BookingRules\RoomVerifiable;
use \MPHB\BookingRules\RuleVerifiable;
use \MPHB\BookingRules\TypeVerifiable;

class CustomRulesHolder implements RuleVerifiable, TypeVerifiable, RoomVerifiable {

	/**
	 *
	 * @var CustomTypeRules[] [%type ID% => CustomTypeRules]
	 */
	protected $rules = array();

	protected function __construct( array $typeRules ){
		$this->rules = $typeRules;
	}

	public function verify( \DateTime $checkInDate, \DateTime $checkOutDate ){
		return $this->verifyType( $checkInDate, $checkOutDate, 0 );
	}

	public function verifyType( \DateTime $checkInDate, \DateTime $checkOutDate, $roomTypeId ){
		if ( isset( $this->rules[$roomTypeId] ) ) {
			return $this->rules[$roomTypeId]->verify( $checkInDate, $checkOutDate );
		} else {
			return true;
		}
	}

	public function verifyRooms( \DateTime $checkInDate, \DateTime $checkOutDate, $roomTypeId ){
		$lockedRooms = array();

		if ( isset( $this->rules[$roomTypeId] ) ) {
			$lockedRooms = $this->rules[$roomTypeId]->getLockedRooms( $checkInDate, $checkOutDate );
		}

		return $lockedRooms;
	}

	/**
	 *
	 * @return array ["2017-01-01" => ["not_check_in" => true,
	 * "not_check_out" => true, "not_stay_in" => true], "2017-01-02" => ...]
	 */
	public function getGlobalBlockedDates(){
		$dates = array();

		if ( isset( $this->rules[0] ) ) {
			$dates = $this->rules[0]->getGlobalBlockedDates();
		}

		return $dates;
	}

	public function getTypeBlockedDates(){
		$dates = array();

		foreach ( $this->rules as $typeId => $rulesHolder ) {
			if ( $typeId == 0 ) {
				continue;
			}

			$dates[$typeId] = $rulesHolder->getAllBlockedDates();
		}

		return $dates;
	}

	/**
	 *
	 * @param array $customRules
	 */
	public static function create( $customRules ){
		// Get all unique type IDs
		$typeIds = array_map( function( $rule ) {
			return (int)$rule['room_type_id'];
		}, $customRules );
		$typeIds = array_unique( $typeIds );

		// Create CustomTypeRules instance for each type ID
		$typeInstances = array();
		foreach ( $typeIds as $typeId ) {
			$typeRules = array_filter( $customRules, function( $rule ) use ($typeId) {
				return ( $rule['room_type_id'] == $typeId );
			} );

			$typeInstances[$typeId] = CustomTypeRules::create( $typeRules );
		}

		return new self( $typeInstances );
	}

}
