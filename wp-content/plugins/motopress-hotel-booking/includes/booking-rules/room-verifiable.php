<?php

namespace MPHB\BookingRules;

interface RoomVerifiable {

	/**
	 *
	 * @param \DateTime $checkInDate
	 * @param \DateTime $checkOutDate
	 * @param int $roomTypeId
	 * @return array Unique room IDs (<b>locked rooms, not free</b>).
	 */
	public function verifyRooms( \DateTime $checkInDate, \DateTime $checkOutDate, $roomTypeId );

}
