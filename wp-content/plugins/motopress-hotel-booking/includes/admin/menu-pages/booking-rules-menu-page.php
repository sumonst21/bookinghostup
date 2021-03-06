<?php

namespace MPHB\Admin\MenuPages;

use \MPHB\Admin\Fields\FieldFactory;

class BookingRulesMenuPage extends AbstractMenuPage {

	private $fields = array();

	public function addActions(){
		parent::addActions();

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminScripts' ) );
		add_action( 'admin_notices', array( $this, 'showNotices' ) );
	}

	public function enqueueAdminScripts(){
		if ( $this->isCurrentPage() ) {
			MPHB()->getAdminScriptManager()->enqueue();
			wp_enqueue_script( 'mphb-jquery-serialize-json' );
		}
	}

	public function showNotices(){
		if ( $this->isCurrentPage() && isset( $_POST['save'] ) ) {
			echo '<div class="updated notice notice-success is-dismissible"><p>' . __( 'Booking rules saved.', 'motopress-hotel-booking' ) . '</p></div>';
		}
	}

	public function render(){
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php _e( 'Booking Rules', 'motopress-hotel-booking' ); ?></h1>

			<hr class="wp-header-end" />

			<form method="POST" action="" autocomplete="off">

				<!-- HostUP - 20190219 - Check-in/Check-out time -->
				<?php echo $this->fields['mlsc_check_in_times']->render(); ?>
				<br/><hr/>

				<?php echo $this->fields['mlsc_check_out_times']->render(); ?>
				<br/><hr/>
				<!-- end -->

				<?php echo $this->fields['mphb_check_in_days']->render(); ?>
				<br/><hr/>

				<?php echo $this->fields['mphb_check_out_days']->render(); ?>
				<br/><hr/>

				<?php echo $this->fields['mphb_min_stay_length']->render(); ?>
				<br/><hr/>

				<?php echo $this->fields['mphb_max_stay_length']->render(); ?>
				<br/><hr/>

				<?php echo $this->fields['mphb_booking_rules_custom']->render(); ?>

				<p class="submit">
					<input name="save" type="submit" class="button button-primary" id="publish" value="<?php _e( 'Save Changes', 'motopress-hotel-booking' ); ?>" />
				</p>
			</form>
		</div>
		<?php
	}

	public function onLoad(){
		if ( !$this->isCurrentPage() ) {
			return;
		}

		$this->createFields();

		if ( isset( $_POST['save'] ) ) {
			$this->saveCustomRules();
			$this->processReservationRules();
		}
	}

	private function saveCustomRules(){
		$customRules = !empty( $_POST['mphb_booking_rules_custom'] ) ? $_POST['mphb_booking_rules_custom'] : array();
		$customRules = $this->sanitize( 'mphb_booking_rules_custom', $customRules );
		$this->save( 'mphb_booking_rules_custom', $customRules );
	}

	/**
	 * Build reservation rules and prepare season priorities.
	 */
	private function processReservationRules(){

		$postFields = array(
			/** HostUP - 20190219 - Check-in/Check-out time */
			'mlsc_check_in_times',
			'mlsc_check_out_times',
			/** end */
			'mphb_check_in_days',
			'mphb_check_out_days',
			'mphb_min_stay_length',
			'mphb_max_stay_length'
		);

		foreach( $postFields as $postField ) {
			// Use array_values() to reset numeric indexes
			$postValues = !empty( $_POST[$postField] ) ? array_values( $_POST[$postField] ) : array();
			$postValues = $this->sanitize( $postField, $postValues );

			// All values are numbers, so convert all strings in the array into numbers
			array_walk_recursive( $postValues, function ( &$value, $key ) {
				$value = (int)$value;
			} );

			$this->save( $postField, $postValues );

		}
	}

	/**
	 *
	 * @param string $option
	 * @param mixed $value
	 *
	 * @return mixed Sanitized value.
	 */
	private function sanitize( $option, $value ){
		$field = $this->fields[$option];

		$value = wp_unslash( $value );
		$value = $field->sanitize( $value );

		return $value;
	}

	private function save( $option, $value ){
		$this->fields[$option]->setValue( $value );
		update_option( $option, $value, 'no' );
	}

	private function createFields(){
		// Load room types only on default language
		MPHB()->translation()->setupDefaultLanguage();
		$roomTypes = MPHB()->getRoomTypePersistence()->getIdTitleList( array(), array( 0 => __( 'All', 'motopress-hotel-booking' ) ) );
		MPHB()->translation()->restoreLanguage();

		$seasons = MPHB()->getSeasonPersistence()->getIdTitleList( array(), array( 0 => __( 'All', 'motopress-hotel-booking' ) ) );
		$daysOfWeek = \MPHB\Utils\DateUtils::getDaysList();

		/** HostUP - 20190219 - Check-in/Check-out time */
		$checkInTimes  = array(
			__( '02:00 pm', 'motopress-hotel-booking' ),
		  __( '03:00 pm', 'motopress-hotel-booking' ),
			__( '04:00 pm', 'motopress-hotel-booking' ));

		$checkOutTimes  = array(
			__( '11:00 am', 'motopress-hotel-booking' ),
			__( '12:00 pm', 'motopress-hotel-booking' ));
		/** end */

		// Consider first day settings: move first day to the top of the list
		$startDay = MPHB()->settings()->dateTime()->getFirstDay();
		if ( $startDay > 0 ) {
			$startPart	 = array_slice( $daysOfWeek, $startDay, 7 - $startDay, true );
			$endPart	 = array_slice( $daysOfWeek, 0, $startDay, true );
			$daysOfWeek	 = array_replace( $startPart, $endPart );
		}

		/** HostUP - 20190219 - Check-in/Check-out time */
		$this->fields['mlsc_check_in_times'] = FieldFactory::create( 'mlsc_check_in_times', array(
			'type'			 => 'rules-list',
			'label'			 => __( 'Check-in times', 'motopress-hotel-booking' ),
			'empty_label'	 => __( 'Guests can check in any time.', 'motopress-hotel-booking' ),
			'add_label'		 => __( 'Add rule', 'motopress-hotel-booking' ),
			'sortable'		 => true,
			'default'		 => array(),
			'fields'		 => array(
				FieldFactory::create( 'check_in_times', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Times', 'motopress-hotel-booking' ),
					'default'		 => 0,
					'list'			 => $checkInTimes
				) ),
				FieldFactory::create( 'room_type_ids', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Accommodations', 'motopress-hotel-booking' ),
					'all_value'		 => 0,
					'default'		 => array( 0 ),
					'list'			 => $roomTypes
				) )
			)
		), get_option( 'mlsc_check_in_times', array() ) );
		$this->fields['mlsc_check_out_times'] = FieldFactory::create( 'mlsc_check_out_times', array(
			'type'			 => 'rules-list',
			'label'			 => __( 'Check-out times', 'motopress-hotel-booking' ),
			'empty_label'	 => __( 'Guests can check out any time.', 'motopress-hotel-booking' ),
			'add_label'		 => __( 'Add rule', 'motopress-hotel-booking' ),
			'sortable'		 => true,
			'default'		 => array(),
			'fields'		 => array(
				FieldFactory::create( 'check_out_times', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Times', 'motopress-hotel-booking' ),
					'default'		 => 0,
					'list'			 => $checkOutTimes
				) ),
				FieldFactory::create( 'room_type_ids', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Accommodations', 'motopress-hotel-booking' ),
					'all_value'		 => 0,
					'default'		 => array( 0 ),
					'list'			 => $roomTypes
				) )
			)
		), get_option( 'mlsc_check_out_times', array() ) );
		/** end */

		$this->fields['mphb_check_in_days'] = FieldFactory::create( 'mphb_check_in_days', array(
			'type'			 => 'rules-list',
			'label'			 => __( 'Check-in days', 'motopress-hotel-booking' ),
			'empty_label'	 => __( 'Guests can check in any day.', 'motopress-hotel-booking' ),
			'add_label'		 => __( 'Add rule', 'motopress-hotel-booking' ),
			'sortable'		 => true,
			'default'		 => array(),
			'fields'		 => array(
				FieldFactory::create( 'check_in_days', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Days', 'motopress-hotel-booking' ),
					'default'		 => range( 0, 6 ),
					'list'			 => $daysOfWeek
				) ),
				FieldFactory::create( 'room_type_ids', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Accommodations', 'motopress-hotel-booking' ),
					'all_value'		 => 0,
					'default'		 => array( 0 ),
					'list'			 => $roomTypes
				) ),
				FieldFactory::create( 'season_ids', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Seasons', 'motopress-hotel-booking' ),
					'all_value'		 => 0,
					'default'		 => array( 0 ),
					'list'			 => $seasons
				) )
			)
		), get_option( 'mphb_check_in_days', array() ) );

		$this->fields['mphb_check_out_days'] = FieldFactory::create( 'mphb_check_out_days', array(
			'type'			 => 'rules-list',
			'label'			 => __( 'Check-out days', 'motopress-hotel-booking' ),
			'empty_label'	 => __( 'Guests can check out any day.', 'motopress-hotel-booking' ),
			'add_label'		 => __( 'Add rule', 'motopress-hotel-booking' ),
			'sortable'		 => true,
			'default'		 => array(),
			'fields'		 => array(
				FieldFactory::create( 'check_out_days', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Days', 'motopress-hotel-booking' ),
					'default'		 => range( 0, 6 ),
					'list'			 => $daysOfWeek
				) ),
				FieldFactory::create( 'room_type_ids', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Accommodations', 'motopress-hotel-booking' ),
					'all_value'		 => 0,
					'default'		 => array( 0 ),
					'list'			 => $roomTypes
				) ),
				FieldFactory::create( 'season_ids', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Seasons', 'motopress-hotel-booking' ),
					'all_value'		 => 0,
					'default'		 => array( 0 ),
					'list'			 => $seasons
				) )
			)
		), get_option( 'mphb_check_out_days', array() ) );

		$this->fields['mphb_min_stay_length'] = FieldFactory::create( 'mphb_min_stay_length', array(
			'type'			 => 'rules-list',
			'label'			 => __( 'Minimum stay', 'motopress-hotel-booking' ),
			'empty_label'	 => __( 'There are no minimum stay rules.', 'motopress-hotel-booking' ),
			'add_label'		 => __( 'Add rule', 'motopress-hotel-booking' ),
			'sortable'		 => true,
			'default'		 => array(),
			'fields'		 => array(
				FieldFactory::create( 'min_stay_length', array(
					'type'			 => 'number',
					'label'			 => __( 'Minimum stay', 'motopress-hotel-booking' ),
					'inner_label'	 => __( 'nights', 'motopress-hotel-booking' ),
					'default'		 => 1,
					'min'			 => 1
				) ),
				FieldFactory::create( 'room_type_ids', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Accommodations', 'motopress-hotel-booking' ),
					'all_value'		 => 0,
					'default'		 => array( 0 ),
					'list'			 => $roomTypes
				) ),
				FieldFactory::create( 'season_ids', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Seasons', 'motopress-hotel-booking' ),
					'all_value'		 => 0,
					'default'		 => array( 0 ),
					'list'			 => $seasons
				) )
			)
		), get_option( 'mphb_min_stay_length', array() ) );

		$this->fields['mphb_max_stay_length'] = FieldFactory::create( 'mphb_max_stay_length', array(
			'type'			 => 'rules-list',
			'label'			 => __( 'Maximum stay', 'motopress-hotel-booking' ),
			'empty_label'	 => __( 'There are no maximum stay rules.', 'motopress-hotel-booking' ),
			'add_label'		 => __( 'Add rule', 'motopress-hotel-booking' ),
			'sortable'		 => true,
			'default'		 => array(),
			'fields'		 => array(
				FieldFactory::create( 'max_stay_length', array(
					'type'			 => 'number',
					'label'			 => __( 'Maximum stay', 'motopress-hotel-booking' ),
					'inner_label'	 => __( 'nights', 'motopress-hotel-booking' ),
					'default'		 => 15,
					'min'			 => 1
				) ),
				FieldFactory::create( 'room_type_ids', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Accommodations', 'motopress-hotel-booking' ),
					'all_value'		 => 0,
					'default'		 => array( 0 ),
					'list'			 => $roomTypes
				) ),
				FieldFactory::create( 'season_ids', array(
					'type'			 => 'multiple-checkbox',
					'label'			 => __( 'Seasons', 'motopress-hotel-booking' ),
					'all_value'		 => 0,
					'default'		 => array( 0 ),
					'list'			 => $seasons
				) )
			)
		), get_option( 'mphb_max_stay_length', array() ) );

		$this->fields['mphb_booking_rules_custom'] = FieldFactory::create( 'mphb_booking_rules_custom', array(
			'type'			 => 'rules-list',
			'label'			 => __( 'Block accommodation', 'motopress-hotel-booking' ),
			'empty_label'	 => __( 'There are no blocking accommodation rules.', 'motopress-hotel-booking' ),
			'add_label'		 => __( 'Add rule', 'motopress-hotel-booking' ),
			'default'		 => array(),
			'fields'		 => array(
				FieldFactory::create( 'room_type_id', array(
					'type'				 => 'select',
					'label'				 => __( 'Accommodation Type', 'motopress-hotel-booking' ),
					'default'			 => 0,
					'list'				 => $roomTypes
				) ),
				FieldFactory::create( 'room_id', array(
					'type'				 => 'dynamic-select',
					'label'				 => __( 'Accommodation', 'motopress-hotel-booking' ),
					'dependency_input'	 => 'room_type_id',
					'ajax_action'		 => 'mphb_get_accommodations_list',
					'list_callback'		 => 'mphb_get_rooms_select_list',
					'default'			 => 0,
					'list'				 => array( 0 => __( 'All', 'motopress-hotel-booking' ) )
				) ),
				FieldFactory::create( 'date_from', array(
					'type'				 => 'datepicker',
					'label'				 => __( 'From', 'motopress-hotel-booking' ),
					'size'				 => 'wide',
					'required'			 => true,
					'readonly'			 => false
				) ),
				FieldFactory::create( 'date_to', array(
					'type'				 => 'datepicker',
					'label'				 => __( 'Till', 'motopress-hotel-booking' ),
					'size'				 => 'wide',
					'required'			 => true,
					'readonly'			 => false
				) ),
				FieldFactory::create( 'restrictions', array(
					'type'				 => 'multiple-checkbox',
					'label'				 => __( 'Restriction', 'motopress-hotel-booking' ),
					'default'			 => array(),
					'list'				 => array(
						'check-in'  => __( 'Not check-in', 'motopress-hotel-booking' ),
						'check-out' => __( 'Not check-out', 'motopress-hotel-booking' ),
						'stay-in'   => __( 'Not stay-in', 'motopress-hotel-booking' )
					)
				) ),
				FieldFactory::create( 'comment', array(
					'type'				 => 'textarea',
					'label'				 => __( 'Comment', 'motopress-hotel-booking' )
				) )
			)
		), get_option( 'mphb_booking_rules_custom', array() ) );
	}

	protected function getMenuTitle(){
		return __( 'Booking Rules', 'motopress-hotel-booking' );
	}

	protected function getPageTitle(){
		return __( 'Booking Rules', 'motopress-hotel-booking' );
	}

}
