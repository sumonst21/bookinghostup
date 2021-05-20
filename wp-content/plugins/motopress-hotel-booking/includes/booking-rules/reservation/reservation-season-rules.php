<?php

namespace MPHB\BookingRules\Reservation;

use \MPHB\BookingRules\RuleVerifiable;

class ReservationSeasonRules implements RuleVerifiable {

	/**
	 *
	 * @var ReservationRule Rule Type/All.
	 */
	protected $globalRule = null;

	/**
	 *
	 * @var ReservationRule[] Rules Type/X.
	 */
	protected $seasonRules = array();

	protected function __construct( ReservationRule $globalRule, array $seasonRules ){
		$this->globalRule  = $globalRule;
		$this->seasonRules = $seasonRules;
	}

	/**
	 *
	 * @param \DateTime $checkInDate
	 * @param \DateTime $checkOutDate
	 *
	 * @return boolean
	 */
	public function verify( \DateTime $checkInDate, \DateTime $checkOutDate ){
		$verified = true;

		$seasonId = MPHB()->getSeasonPersistence()->searchSeason( array( 'from_date' => $checkInDate ) );

		// Verify Type/X
		if ( $seasonId != 0 && isset($this->seasonRules[$seasonId] ) ) {
			$verified = $verified && $this->seasonRules[$seasonId]->verify( $checkInDate, $checkOutDate );
		}

		// Verify Type/All
		$verified = $verified && $this->globalRule->verify( $checkInDate, $checkOutDate );

		return $verified;
	}

	/**
	 *
	 * @return array Type/All data.
	 */
	public function getData(){
		return $this->globalRule->getData();
	}

	/**
	 *
	 * @param array $seasonRules [%season ID% => [%rules list%]]. See class
	 * RulesBuilder for more details.
	 *
	 * @return ReservationSeasonRules
	 *
	 * @see \MPHB\BookingRules\Reservation\RulesBuilder
	 */
	public static function create( array $seasonRules ){
		$defaultRules = MPHB()->settings()->bookingRules()->getDefaultReservationRules();

		$globalRules = $defaultRules;
		if ( isset( $seasonRules[0] ) ) {
			$globalRules = array_merge( $globalRules, $seasonRules[0] );
		}
		$globalInstance = new ReservationRule( $globalRules );

		// Create season rule instances
		$seasonInstances = array();
		foreach ( $seasonRules as $seasonId => $params ) {
			if ( $seasonId == 0 ) {
				continue;
			}

			$params = array_merge( $defaultRules, $params );
			$seasonInstances[$seasonId] = new ReservationRule( $params );
		}

		return new self( $globalInstance, $seasonInstances );
	}

}
