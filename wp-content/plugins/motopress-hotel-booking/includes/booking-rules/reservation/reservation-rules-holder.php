<?php

namespace MPHB\BookingRules\Reservation;

use \MPHB\BookingRules\RuleVerifiable;
use \MPHB\BookingRules\TypeVerifiable;

class ReservationRulesHolder implements RuleVerifiable, TypeVerifiable {

	/**
	 *
	 * @var ReservationSeasonRules|null Rule All/All.
	 */
	protected $globalRules = null;

	/**
	 *
	 * @var ReservationSeasonRules[] [%type ID% => ReservationSeasonRules]
	 */
	protected $typeRules = array();

	protected function __construct( $globalRules, array $typeRules ){
		$this->globalRules = $globalRules;
		$this->typeRules   = $typeRules;
	}

	/**
	 *
	 * @param \DateTime $checkInDate
	 * @param \DateTime $checkOutDate
	 *
	 * @return boolean
	 */
	public function verify( \DateTime $checkInDate, \DateTime $checkOutDate ){
		return $this->verifyType( $checkInDate, $checkOutDate, 0 );
	}

	/**
	 *
	 * @param \DateTime $checkInDate
	 * @param \DateTime $checkOutDate
	 * @param int $roomTypeId
	 *
	 * @return boolean
	 */
	public function verifyType( \DateTime $checkInDate, \DateTime $checkOutDate, $roomTypeId ){
		$verified = true;

		// Verify Type/X and Type/All
		if ( $roomTypeId != 0 && isset( $this->typeRules[$roomTypeId] ) ) {
			$verified = $verified && $this->typeRules[$roomTypeId]->verify( $checkInDate, $checkOutDate );
		}

		// Verify All/All
		if ( !is_null( $this->globalRules ) ) {
			$verified = $verified && $this->globalRules->verify( $checkInDate, $checkOutDate );
		}

		return $verified;
	}

	/**
	 *
	 * @return array All/All data.
	 */
	public function getData(){
		$data = ( !is_null( $this->globalRules ) ? $this->globalRules->getData() : MPHB()->settings()->bookingRules()->getDefaultReservationRules() );

		if ( $data['max_stay_length'] == 0 ) {
			$data['max_stay_length'] = 3652; // 10 years
		}

		return $data;
	}

	/**
	 *
	 * @param array $rules [%room type ID% => [%season ID% => [%rules list%]]].
	 * See class RulesBuilder for more details.
	 *
	 * @return ReservationRulesHolder
	 *
	 * @see \MPHB\BookingRules\Reservation\RulesBuilder
	 */
	public static function create( array $rules ){
		$globalRules = array();
		if ( isset( $rules[0] ) ) {
			$globalRules = $rules[0];
		}
		$globalInstance = ReservationSeasonRules::create( $globalRules );

		// Create type rule instances
		$typeInstances = array();
		foreach ( $rules as $typeId => $seasonRules ) {
			if ( $typeId == 0 ) {
				continue;
			}

			$typeInstances[$typeId] = ReservationSeasonRules::create( $seasonRules );
		}

		return new self( $globalInstance, $typeInstances );
	}

}
