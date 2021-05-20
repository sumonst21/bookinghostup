<?php

namespace MPHB\iCal;

use \MPHB\Entities;
use \MPHB\PostTypes\BookingCPT\Statuses;

class Importer{

	/**
	 *
	 * @var bool
	 */
	private $isImporting = false;

	/**
	 *
	 * @var string
	 */
	private $lastMessage = '';

	public function __construct(){
		add_filter( 'mphb_prevent_handle_booking_status_transition', array( $this, 'isPreventHandleBookingStatusTransition' ), 10, 1 );
	}

	/**
	 *
	 * @param bool $isPrevent
	 * @return bool
	 */
	public function isPreventHandleBookingStatusTransition( $isPrevent ){
		return $isPrevent ? $isPrevent : $this->isImporting;
	}

	/**
	 *
	 * @param int $roomId
	 * @param array $event Parsed event values.
	 *
	 * @return int Import status. See \MPHB\iCal\ImportStatus.
	 */
	public function import( $roomId, $event ){
		$this->setLastMessage( '' );

		/** @var \MPHB\Entities\ReservedRoom|null */
		$roomWithUid = MPHB()->getReservedRoomRepository()->findByUid( $event['uid'] );

		$isNew			 = is_null( $roomWithUid );

		$isOverbooking	 = $this->isOverbooking( $event, $roomWithUid );

//Host-up - Integrated calendar for different properties - 20190706
		  //if ( $isNew && !$isOverbooking ) {
		  if ( $isNew || !$isNew && !$isOverbooking ) {
//End Host-up - Integrated calendar for different properties - 20190706

			// Add fully new event into database
			$wasCreated = $this->createBooking( $roomId, $event );
			return $wasCreated ? ImportStatus::SUCCESS : ImportStatus::FAILED;

		} else if ( !$isOverbooking ) {
			// The event is not new, but still there is no problems/overbooking
			$this->setLastMessage( sprintf( __( 'Skipped. Event with such UID already exists in booking #%d.', 'motopress-hotel-booking' ), $roomWithUid->getBookingId() ) );
			return ImportStatus::SKIPPED;

		} else {
			// Found some conflicts
			return ImportStatus::FAILED;
		}
	}

	/**
	 *
	 * @param array $event
	 * @param \MPHB\Entities\ReservedRoom|null $roomWithUid
	 *
	 * @return bool
	 */
	private function isOverbooking( $event, $roomWithUid = null ){
		// Conflict #1: another room already registered with such UID

//Host-up - Integrated calendar for different properties - 20190706
	//if ( !is_null( $roomWithUid ) && $roomWithUid->getRoomId() != $event['roomId'] ) {
    if ( !is_null( $roomWithUid ) && $roomWithUid->getRoomId() === $event['roomId'] ) {
//End Host-up - Integrated calendar for different properties - 20190706
			$this->setLastMessage( sprintf( __( 'Failure: Event with such UID already registered for accommodation #%d.', 'motopress-hotel-booking' ), $roomWithUid->getRoomId() ) );
			return true;
		}

		$bookings		 = MPHB()->getBookingRepository()->findAll( array(
			'fields'		 => 'ids',
			'room_locked'	 => true,
			'rooms'			 => array( $event['roomId'] ),
			'date_from'		 => $event['checkIn'],
			'date_to'		 => $event['checkOut']
		) );
		$bookingsFound	 = count( $bookings );
		$bookingIds		 = array_map( function( $booking ){
			return $booking->getId();
		}, $bookings );

		// Conflict #2: another booking registered on such date
		if ( $bookingsFound == 1 ) {
			if ( is_null( $roomWithUid ) || $bookingIds[0] != $roomWithUid->getBookingId() ) {
				$this->setLastMessage( sprintf( __( 'Failure: Event conflicts with booking #%d.', 'motopress-hotel-booking' ), $bookingIds[0] ) );
				return true;
			}
		}

		// Conflict #3: a set of bookings registered on such date
		if ( $bookingsFound > 1 ) {
			$this->setLastMessage( sprintf( __( 'Failure: Event conflicts with %1$d other bookings: %2$s.', 'motopress-hotel-booking' ), $bookingsFound, implode( ', ', $bookingIds ) ) );
			return true;
		}

		// No conflicts
		/**
		 * @todo Maybe check that date in $event equal to date in $roomWithUid.
		 * Or is it enough to check only UID?
		 */
		return false;

	}

	/**
	 *
	 * @param int $roomId
	 * @param array $eventValues Parsed event values.
	 *
	 * @return bool Was or not created in database.
	 */
	private function createBooking( $roomId, $eventValues ){
		/**
		 * @var string $uid
		 * @var string $prodid
		 * @var string $checkIn		 2017-08-18
		 * @var string $checkOut	 2017-08-19
		 * @var string $summary		 Empty string or summary wrapped in ""
		 * @var string $description	 Empty string or description wrapped in ""
		 */
		extract( $eventValues );

		$room = MPHB()->getRoomRepository()->findById( $roomId );
		$type = $room ? MPHB()->getRoomTypeRepository()->findById( $room->getRoomTypeId() ) : null;
		$adults = $type ? $type->getAdultsCapacity() : MPHB()->settings()->main()->getMinAdults();
		$children = $type ? $type->getChildrenCapacity() : MPHB()->settings()->main()->getMinChildren();

		// Add time to check-in/check-out dates
		$checkIn  .= ' ' . MPHB()->settings()->dateTime()->getCheckInTime();
		$checkOut .= ' ' . MPHB()->settings()->dateTime()->getCheckOutTime();

		$this->isImporting = true;

		$reservedRoom = Entities\ReservedRoom::create( array(
			'room_id'	 => $roomId,
			'rate_id'	 => 0,
			'adults'	 => $adults,
			'children'	 => $children,
			'uid'		 => $uid
		) );

		$booking = Entities\Booking::create( array(
			'check_in_date'		 => \DateTime::createFromFormat( 'Y-m-d H:i:s', $checkIn ),
			'check_out_date'	 => \DateTime::createFromFormat( 'Y-m-d H:i:s', $checkOut ),
			'reserved_rooms'	 => array( $reservedRoom ),
			'customer'			 => new Entities\Customer(),
			'status'			 => Statuses::STATUS_CONFIRMED,
			'ical_prodid'		 => $prodid,
			'ical_summary'		 => $summary,
			'ical_description'	 => $description
		) );

		$wasCreated = MPHB()->getBookingRepository()->save( $booking );

		if ( $wasCreated ) {
			$booking->addLog( sprintf( __( 'Booking imported with UID %1$s.<br />Summary: %2$s.<br />Description: %3$s.<br />Source: %4$s.', 'motopress-hotel-booking' ), $uid, $summary, $description, $prodid ) );
			$this->setLastMessage( sprintf( __( 'Success. New booking #%d', 'motopress-hotel-booking' ), $booking->getId() ) );
		}

		$this->isImporting = false;

		return $wasCreated;
	}

	public function getLastMessage(){
		return $this->lastMessage;
	}

	/**
	 *
	 * @param string $message
	 */
	private function setLastMessage( $message ){
		$this->lastMessage = $message;
	}

}
