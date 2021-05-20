<?php

namespace MPHB\iCal;

use \MPHB\Libraries\iCalendar\ZCiCal;
use \MPHB\Libraries\iCalendar\ZCiCalNode;
use \MPHB\Libraries\iCalendar\ZCiCalDataNode;
use \MPHB\PostTypes\BookingCPT\Statuses as BookingStatuses;

class Exporter{

	public function export( $roomId ){
		$room = MPHB()->getRoomRepository()->findById( $roomId );
		$property = $room ? $room->getTitle() : '';

		$bookings = MPHB()->getBookingRepository()->findAll( array(
			'fields'		 => 'all',
			'post_status'	 => BookingStatuses::STATUS_CONFIRMED,
			'date_from'		 => date( 'Y-m-d H:i:s', 0 ),
			'date_to'		 => '2036-01-01 00:00:01', // Max year for 32 bit systems
			'rooms'			 => array( $roomId )
		) );


		$calendar = new iCal();
		$dtstamp  = ZCiCal::fromUnixDateTime() . 'Z'; // Time, when this file was created. Format: "Ymd\THis\Z"

		// Remove METHOD property
		$calendar->removeMethodProperty();

		// Change default PRODID
		$calendar->setProdid( mphb_current_domain() );

		foreach ( $bookings as $booking ) {

			// Prepare SUMMARY text
			$summary = $booking->getICalSummary();

		// Hostup - do NOT send integrated booking - included WHILE - 20190706
		while ( empty( $summary ) ) {

			if ( empty( $summary ) ) {
				$summary = $this->createSummary( $booking );
			} else {

				$summary = substr( $summary, 1, -1 ); // Remove "", added on import
			}

			// Prepare DESCRIPTION text
			$description = $booking->getICalDescription();
			if ( empty( $description ) ) {
				$description = $this->createDescription( $room, $booking );
			} else {
				$description = substr( $description, 1, -1 ); // Remove "", added on import
				$description = str_replace( PHP_EOL, '\n', $description );
			}

			$reservedRooms = $booking->getReservedRooms();

			foreach ( $reservedRooms as $reservedRoom ) {

				$uid = $reservedRoom->getUid();

				if ( !empty( $uid ) && $reservedRoom->getRoomId() == $roomId ) {
					$event = new ZCiCalNode( 'VEVENT', $calendar->curnode );
					$event->addNode( new ZCiCalDataNode( 'UID:' . $uid ) );
					$event->addNode( new ZCiCalDataNode( 'DTSTART;VALUE=DATE:' . ZCiCal::fromSqlDateTime( $booking->getCheckInDate()->format( 'Y-m-d' ) ) ) );
					$event->addNode( new ZCiCalDataNode( 'DTEND;VALUE=DATE:' . ZCiCal::fromSqlDateTime( $booking->getCheckOutDate()->format( 'Y-m-d' ) ) ) );
					$event->addNode( new ZCiCalDataNode( 'DTSTAMP:' . $dtstamp ) );
					$event->addNode( new ZCiCalDataNode( 'SUMMARY:' . $summary ) );
					// ZCiCal library can limit DESCRIPTION by 80 characters, so
					// some of the content can be pushed on the next line
					$event->addNode( new ZCiCalDataNode( 'DESCRIPTION:' . $description ) );
				}

			} // For each reserved room
		} // Hostup - end WHILE - do NOT send integrated booking - 20190706
		} // For each booking

		$post	  = get_post( $roomId );
		// %domain%-%name%-%date%.ics - booking.dev-comfort-triple-room-1-20170710.ics
		$filename = mphb_current_domain() . '-' . $post->post_name . '-' . date('Ymd') . '.ics';

		header( 'Content-type: text/calendar; charset=utf-8' );
		header( 'Content-Disposition: inline; filename=' . $filename );
		echo $calendar->export();
	}

	/**
	 *
	 * @param \MPHB\Entities\Booking $booking
	 */
	private function createSummary( $booking ){
		$customer	 = $booking->getCustomer();
		$name		 = $customer ? trim( $customer->getFirstName() . ' ' . $customer->getLastName() ) : '';
		$summary	 = trim( sprintf( '%s (%d)', $name, $booking->getId() ) );
		return $summary;
	}

	/**
	 *
	 * @param \MPHB\Entities\Room|null $room
	 * @param \MPHB\Entities\Booking $booking
	 */
	private function createDescription( $room, $booking ){
		$checkIn	 = $booking->getCheckInDate()->format( 'Y-m-d' );
		$checkOut	 = $booking->getCheckOutDate()->format( 'Y-m-d' );
		$nights		 = \MPHB\Utils\DateUtils::calcNights( $booking->getCheckInDate(), $booking->getCheckOutDate() );

		$description = sprintf( 'CHECKIN: %s\nCHECKOUT: %s\nNIGHTS: %d\n', $checkIn, $checkOut, $nights );

		if ( $room ) {
			$description .= sprintf( 'PROPERTY: %s\n', $room->getTitle() );
		}

		return $description;
	}

}
