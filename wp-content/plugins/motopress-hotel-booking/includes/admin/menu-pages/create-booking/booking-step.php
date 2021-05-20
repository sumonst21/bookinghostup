<?php

namespace MPHB\Admin\MenuPages\CreateBooking;

use \MPHB\Utils\ValidateUtils;

/**
 * Fourth step.
 */
class BookingStep extends Step {

	/** @var \MPHB\Entities\Customer|null */
	protected $customer = null;

	/** @var \MPHB\Entities\Booking|null */
	protected $booking = null;

	protected $allowRedirect = true;

	public function __construct(){
		parent::__construct( 'booking' );
	}

	public function setup(){
		parent::setup();

		if ( !$this->isValidStep ) {
			return;
		}

		$bookingSaved = MPHB()->getBookingRepository()->save( $this->booking );

		if ( !$bookingSaved ) {
			$this->parseError( __( 'Unable to create booking. Please try again.', 'motopress-hotel-booking' ) );
			$this->isValidStep = false;
			return;
		}



		do_action( 'mphb_create_booking_by_user', $this->booking );
		/** HostUP - 05.03.2019 = DocumentId */
		add_action('mphb_create_booking_by_user', $this->mlsc_handle_attachment() );
		/** end */

		// Update price breakdown ("Price Details")
		$priceBreakdown = $this->booking->getPriceBreakdown();
		array_walk_recursive( $priceBreakdown, function( &$value, $key ) {
			$value = addslashes( $value );
		} );
		update_post_meta( $this->booking->getId(), '_mphb_booking_price_breakdown', json_encode( $priceBreakdown ) );

		// Redirect to "Edit Booking"
		if ( $this->allowRedirect ) {
			$redirectTo = get_edit_post_link( $this->booking->getId(), 'raw' );
			wp_redirect( $redirectTo );
			$this->_exit();
		}
	}

	protected function renderValid(){
		$booking = sprintf( __( 'Booking #%s', 'motopress-hotel-booking' ), $this->booking->getId() );
		$link = get_edit_post_link( $this->booking->getId() );

		echo '<h2><a href="' . esc_url( $link ) . '">' . esc_html( $booking ) . '</a></h2>';
	}

	protected function parseFields(){
		if ( apply_filters( 'mphb_block_booking', false ) ) {
			$this->parseError( __( 'Booking is blocked due to maintenance reason. Please try again later.', 'motopress-hotel-booking' ) );
			return;
		}

		$this->checkInDate	 = $this->parseCheckInDate( INPUT_POST );
		$this->checkOutDate	 = $this->parseCheckOutDate( INPUT_POST );
		$this->customer		 = $this->parseCustomer( INPUT_POST );

		if ( $this->checkInDate && $this->checkOutDate && $this->customer ) {
			$this->booking = $this->parseBooking( INPUT_POST );
		}
	}

	/**
	 * @param int $input INPUT_POST (0) or INPUT_GET (1)
	 *
	 * @return \MPHB\Entities\Customer|null
	 */
	protected function parseCustomer( $input ){
		$customerData = array(
			'first_name' => '',
			'last_name'	 => '',
			'email'		 => '',
			'phone'		 => '',
			'country'	 => '',
			'state'		 => '',
			'city'		 => '',
			'zip'		 => '',
			'address1'	 => '',
			'documentId' => '' /** Host-up - 20190228 - Document ID booking form */
		);

		$values = ( $input == INPUT_POST ) ? $_POST : $_GET;

		foreach ( array_keys( $customerData ) as $name ) {
			$field = 'mphb_' . $name;

			if ( isset( $values[$field] ) ) {
				$value = $values[$field];
				$customerData[$name] = ( $name == 'email' ) ? sanitize_email( $value ) : sanitize_text_field( $value );
			}
		}

		$wasErrors = count( $this->parseErrors );

		if ( empty( $customerData['first_name'] ) ) {
			$this->parseError( __( 'First name is required.', 'motopress-hotel-booking' ) );
		}

		if ( empty( $customerData['last_name'] ) ) {
			$this->parseError( __( 'Last name is required.', 'motopress-hotel-booking' ) );
		}

		if ( empty( $customerData['email'] ) ) {
			$this->parseError( __( 'Email is required.', 'motopress-hotel-booking' ) );
		}

		if ( empty( $customerData['phone'] ) ) {
			$this->parseError( __( 'Phone is required.', 'motopress-hotel-booking' ) );
		}

		/** Host-up - 20190228 - Document ID booking form */
		if ( empty( $_FILES['mphb_documentId'] ) ) {
			$errors[] = __( 'Identification is required.', 'motopress-hotel-booking' );
		}
		/** end */

		if ( MPHB()->settings()->main()->isRequireCountry() && empty( $customerData['country'] ) ) {
			$this->parseError( __( 'Country is required.', 'motopress-hotel-booking' ) );
		}

		if ( MPHB()->settings()->main()->isRequireFullAddress() ) {
			if ( empty( $customerData['state'] ) ) {
				$this->parseError( __( 'State is required.', 'motopress-hotel-booking' ) );
			}

			if ( empty( $customerData['city'] ) ) {
				$this->parseError( __( 'City is required.', 'motopress-hotel-booking' ) );
			}

			if ( empty( $customerData['zip'] ) ) {
				$this->parseError( __( 'Postcode is required.', 'motopress-hotel-booking' ) );
			}

			if ( empty( $customerData['city'] ) ) {
				$this->parseError( __( 'Address is required.', 'motopress-hotel-booking' ) );
			}
		}

		if ( count( $this->parseErrors ) == $wasErrors ) {
			return new \MPHB\Entities\Customer( $customerData );
		} else {
			return null;
		}
	}

	/**
	 * @param int $input INPUT_POST (0) or INPUT_GET (1)
	 *
	 * @return \MPHB\Entities\Booking|null
	 */
	protected function parseBooking( $input ){
		/** @var string|false|null */
		$details = filter_input( $input, 'mphb_room_details', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( empty( $details ) ) {
			if ( is_null( $details ) ) {
				$this->parseError( __( 'There are no accommodations selected for reservation.', 'motopress-hotel-booking' ) );
			} else if ( $details === false ) {
				$this->parseError( __( 'Selected accommodations are not valid.', 'motopress-hotel-booking' ) );
			}

			return null;
		}

		$rooms		 = array();
		$roomIds	 = array();
		$typeRates	 = array();
		$wasErrors	 = count( $this->parseErrors );

		foreach ( $details as $number => $roomDetails ) {
			$roomTypeId		 = isset( $roomDetails['room_type_id'] ) ? ValidateUtils::validateInt( $roomDetails['room_type_id'] ) : 0;
			$roomType		 = ( $roomTypeId > 0 ) ? MPHB()->getRoomTypeRepository()->findById( $roomTypeId ) : null;
			$originalTypeId	 = ( !is_null( $roomType ) ) ? $roomType->getOriginalId() : $roomTypeId;
			$roomId			 = isset( $roomDetails['room_id'] ) ? ValidateUtils::validateInt( $roomDetails['room_id'] ) : 0;
			$rateId			 = isset( $roomDetails['rate_id'] ) ? ValidateUtils::validateInt( $roomDetails['rate_id'] ) : 0;
			$adults			 = isset( $roomDetails['adults'] ) ? ValidateUtils::validateInt( $roomDetails['adults'] ) : 0;
			$children		 = isset( $roomDetails['children'] ) ? ValidateUtils::validateInt( $roomDetails['children'] ) : 0;
			$minAdults		 = MPHB()->settings()->main()->getMinAdults();
			$minChildren	 = MPHB()->settings()->main()->getMinChildren();
			$guestName		 = isset( $roomDetails['guest_name'] ) ? mphb_clean( $roomDetails['guest_name'] ) : '';

			if ( !$roomType || $roomType->getStatus() != 'publish' ) {
				$this->parseError( __( 'Accommodation Type is not valid.', 'motopress-hotel-booking' ) );
				break;
			}

			if ( $roomId <= 0 ) {
				$this->parseError( __( 'Selected accommodations are not valid.', 'motopress-hotel-booking' ) );
				break;
			}

			if ( $rateId <= 0 ) {
				$this->parseError( __( 'Rate is not valid.', 'motopress-hotel-booking' ) );
				break;
			}

			// Search allowed rates (IDs)
			$allowedRateIds = array();
			if ( isset( $typeRates[$originalTypeId] ) ) {
				$allowedRateIds = $typeRates[$originalTypeId];
			} else {
				$allowedRates = MPHB()->getRateRepository()->findAllActiveByRoomType( $originalTypeId, array(
					'check_in_date'	 => $this->checkInDate,
					'check_out_date' => $this->checkOutDate,
					'mphb_language'	 => 'original'
				) );
				$allowedRateIds = array_map( function( \MPHB\Entities\Rate $rate ){
					return $rate->getOriginalId();
				}, $allowedRates );
				$typeRates[$originalTypeId] = $allowedRateIds;
			}

			if ( !in_array( $rateId, $allowedRateIds ) ) {
				$this->parseError( __( 'Rate is not valid.', 'motopress-hotel-booking' ) );
				break;
			}

			if ( $adults === false || $adults < $minAdults || $adults > $roomType->getAdultsCapacity() ) {
				$this->parseError( __( 'Adults number is not valid.', 'motopress-hotel-booking' ) );
				break;
			}

			if ( $children === false || $children < $minChildren || $children > $roomType->getChildrenCapacity() ) {
				$this->parseError( __( 'Children number is not valid.', 'motopress-hotel-booking' ) );
				break;
			}

			if ( !MPHB()->getRulesChecker()->verify( $this->checkInDate, $this->checkOutDate, $roomTypeId ) ) {
				$this->parseError( __( 'Selected dates do not meet booking rules for type %s', 'motopress-hotel-booking' ) );
				break;
			}

			$services = array();

			if ( is_array( $roomDetails['services'] ) ) {
				foreach ( $roomDetails['services'] as $serviceDetails ) {
					if ( !isset( $serviceDetails['id'] ) || !isset( $serviceDetails['adults'] ) ) {
						continue;
					}

					$serviceId = ValidateUtils::validateInt( $serviceDetails['id'] );
					$serviceAdults = ValidateUtils::validateInt( $serviceDetails['adults'] );

					if ( $serviceId === false || $serviceAdults === false || !in_array( $serviceId, $roomType->getServices() ) || $serviceAdults <= 0 ) {
						continue;
					}

					$service = \MPHB\Entities\ReservedService::create( array(
						'id'	 => $serviceId,
						'adults' => $serviceAdults
					) );

					if ( !is_null( $service ) ) {
						$services[] = $service;
					}
				} // For each service details
			}

			$rooms[] = array(
				'room_id'			 => $roomId,
				'rate_id'			 => $rateId,
				'adults'			 => $adults,
				'children'			 => $children,
				'reserved_services'	 => $services,
				'guest_name'		 => $guestName
			);

			if ( !isset( $roomIds[$roomTypeId] ) ) {
				$roomIds[$roomTypeId] = array();
			}
			$roomIds[$roomTypeId][] = $roomId;
		} // For each room details

		foreach ( $roomIds as $roomTypeId => $ids ) {
			if ( !MPHB()->getRoomPersistence()->isRoomsFree( $this->checkInDate, $this->checkOutDate, $ids, $roomTypeId ) ) {
				$this->parseError( __( 'Accommodations are not available.', 'motopress-hotel-booking' ) );
				break;
			}
		}

		if ( count( $this->parseErrors ) > $wasErrors ) {
			return null;
		}

		$reservedRooms = array_filter( array_map( array( '\MPHB\Entities\ReservedRoom', 'create'), $rooms ) );

		if ( empty( $reservedRooms ) ) {
			$this->parseError( __( 'There are no accommodations selected for reservation.', 'motopress-hotel-booking' ) );
			return null;
		}

		$values	 = ( $input == INPUT_POST ) ? $_POST : $_GET;
		$note	 = !empty( $values['mphb_note'] ) ? sanitize_textarea_field( $values['mphb_note'] ) : '';
		$booking = \MPHB\Entities\Booking::create( array(
			'check_in_date'	 => $this->checkInDate,
			'check_out_date' => $this->checkOutDate,
			'customer'		 => $this->customer,
			'note'			 => $note,
			'status'		 => \MPHB\PostTypes\BookingCPT\Statuses::STATUS_CONFIRMED,
			'reserved_rooms' => $reservedRooms
		) );

		if ( !empty( $values['mphb_applied_coupon_code'] ) ) {
			$coupon = MPHB()->getCouponRepository()->findByCode( mphb_clean( $values['mphb_applied_coupon_code'] ) );
			if ( $coupon ) {
				$booking->applyCoupon( $coupon );
			}
		}

		return $booking;
	}

	public function disableRedirect(){
		$this->allowRedirect = false;
	}

	protected function _exit(){
		exit;
	}

	/** HostUP - 05.03.2019 = DocumentId */

	public function mlsc_handle_attachment(){

		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/media.php');

    if ( empty($_FILES) )
  	return;

  	//check_admin_referer('custom-background-upload', '_wpnonce-custom-background-upload');
  	$uploaded_file_type = $_FILES['mphb_documentId']['type'];
  	$allowed_file_types = array('image/jpg','image/jpeg','image/gif', 'image/bmp', 'image/png', 'application/pdf');


  	if ($_FILES['mphb_documentId']['error']) {

  		switch ($_FILES['mphb_documentId']['error']) {
  			case UPLOAD_ERR_NO_FILE:
  				wp_die( __( 'No file sent.' ) );
  				case UPLOAD_ERR_INI_SIZE:
  				case UPLOAD_ERR_FORM_SIZE:
  				wp_die( __( 'Exceeded filesize limit.' ) );
  			default:
  				wp_die( __( 'Unknown errors.' ) );
  			}
  		}


  	if(in_array($uploaded_file_type, $allowed_file_types)) {

  			$overrides = array('test_form' => false);
  			$uploaded_file = $_FILES['mphb_documentId'];

  			add_filter( 'upload_dir', array($this, 'mlsc_set_upload_dir') );
  			add_filter( 'sanitize_file_name', array($this, 'mlsc_hash_filename') ) ;

  			$file = wp_handle_upload($uploaded_file, $overrides);

				if ( isset($file['error']) )
					wp_die( $file['error'] );

				$url = $file['url'];
				$type = $file['type'];
				$file = $file['file'];
				$filename = basename($file);

				// Construct the object array
				$object = array(
					'post_title' => $filename,
					'post_content' => $url,
					'post_mime_type' => $type,
					'guid' => $url,
					'context' => 'mlsc-documentid'
				);

				$parent_post_id = $this->booking->getId();

				// Save the data
				$id = wp_insert_attachment($object, $file, $parent_post_id);

				// Add the meta-data
				wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
				update_post_meta( $this->booking->getId(), '_mphb_file_name', 'DocumentId-Booking-' . $this->booking->getId() );

  	} else {
  			wp_die( __( 'The uploaded file is not a valid type. Please try again.' ) );
  	}

  }

  public function mlsc_hash_filename( $filename ) {

			$mlsc_filename = $this->booking->getId();
  		$info = pathinfo($filename);
  		echo  'info:'. $info . "\n";

  		$ext=$info['extension'];
    	$filename  =  'DocumentId-Booking-' . $mlsc_filename .".".$ext;

  		echo $filename . "\n";

  		return $filename;
   }

  public function mlsc_set_upload_dir( $upload ) {


      $upload['subdir'] = '/host-up-booking/documentid' ;
      $upload['path'] = $upload['basedir'] . $upload['subdir'];
      $upload['url']  = $upload['baseurl'] . $upload['subdir'];

      return $upload;
	}

	/** HostUP - end */

}
