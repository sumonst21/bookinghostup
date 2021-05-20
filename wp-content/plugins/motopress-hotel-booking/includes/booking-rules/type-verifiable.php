<?php

namespace MPHB\BookingRules;

interface TypeVerifiable {

	/**
	 *
	 * @param \DateTime $checkInDate
	 * @param \DateTime $checkOutDate
	 * @param int $roomTypeId
	 * @return bool
	 */
	public function verifyType( \DateTime $checkInDate, \DateTime $checkOutDate, $roomTypeId );

}
