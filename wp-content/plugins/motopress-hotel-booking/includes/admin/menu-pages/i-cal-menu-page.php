<?php

namespace MPHB\Admin\MenuPages;

use \MPHB\Admin\Fields\FieldFactory;

class iCalMenuPage extends AbstractMenuPage {

	/**
	 * Show edit page instead of room list.
	 *
	 * @var bool
	 */
	private $isEdit;

	/**
	 * The list of sync URLs was edited on edit page.
	 *
	 * @var bool
	 */
	private $isEdited;

	/**
	 *
	 * @var \MPHB\Admin\Fields\ComplexHorizontalField
	 */
	private $editField;

	/**
	 *
	 * @var \MPHB\Admin\RoomListTable
	 */
	private $rooms;

	public function __construct( $name, $atts = array() ) {
		parent::__construct( $name, $atts );

		$this->isEdit   = isset( $_GET['accommodation_id'] );
		$this->isEdited = false;
	}

	public function addActions(){
		parent::addActions();

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminScripts' ) );
		add_action( 'admin_notices', array( $this, 'showNotices' ) );
	}

	public function enqueueAdminScripts(){
		if ( $this->isCurrentPage() && $this->isEdit ) {
			MPHB()->getAdminScriptManager()->enqueue();
		}
	}

	public function showNotices(){
		if ( $this->isEdited ) {
			echo '<div class="updated notice notice-success is-dismissible"><p>' . __( 'Accommodation updated.', 'motopress-hotel-booking' ) . '</p></div>';
		}
	}

	public function render(){
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php
				if ( !$this->isEdit ) {
					_e( 'Sync, Import and Export Calendars', 'motopress-hotel-booking' );
				} else {
					$room = MPHB()->getRoomRepository()->findById( absint( $_GET['accommodation_id'] ) );
					/* translators: %s - room name. Example: "Comfort Triple 1" */
					printf( __( 'Edit External Calendars of "%s"', 'motopress-hotel-booking' ), $room->getTitle() );
				}
			?></h1>

			<?php
				if ( !$this->isEdit ) {
					$syncAllUrl = admin_url( 'admin.php?page=mphb_ical_import&action=sync&accommodation_ids=all');
					echo '<a href="', esc_url( $syncAllUrl ), '" class="page-title-action">', __( 'Sync All External Calendars', 'motopress-hotel-booking' ), '</a>';
					echo '<p>', __( 'Sync your bookings across all online channels like Booking.com, TripAdvisor, Airbnb etc. via iCalendar file format.', 'motopress-hotel-booking' ), '</p>';
				}
			?>

			<hr class="wp-header-end" />

			<?php
				if ( !$this->isEdit ) {
					// Render rooms list
					echo '<form id="', sanitize_key( $this->rooms->get_plural() ), '-filter" method="POST" action="">';
						echo '<input type="hidden" name="page" value="', esc_attr( $_REQUEST['page'] ), '" />'; // We need to ensure that the form posts back to our current page
						$this->rooms->display();
					echo '</form>';

				} else {
					// Render edit page
					?>
					<form method="POST" action="">
						<?php
						wp_nonce_field( 'update-calendars' );
						echo $this->editField->render(); 
						?>
						<p>
							<input name="save" type="submit" class="button button-primary" id="publish" value="<?php _e( 'Update', 'motopress-hotel-booking' ); ?>" />
							<a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=mphb_ical' ) ); ?>"><?php _e( 'Back', 'motopress-hotel-booking' ); ?></a>
						</p>
					</form>
					<?php
				}
			?>
		</div>
		<?php
	}

	public function onLoad(){
		if ( !$this->isCurrentPage() ) {
			return;
		}

		if ( !$this->isEdit ) {
			$this->rooms = new \MPHB\Admin\RoomListTable();
			$this->rooms->prepare_items();

		} else {
			if ( isset( $_POST['save'] ) ) {
				check_admin_referer( 'update-calendars' );
			}

			$roomId = intval( $_GET['accommodation_id'] );
			$room   = MPHB()->getRoomRepository()->findById( $roomId );
			$urls   = array();

			if ( !is_null( $room ) ) {
				$urls = $room->getSyncData();
			}

			if ( isset( $_POST['save'] ) ) {
				// Use array_values() to reset numeric indexes
				$urls = is_array( $_POST['mphb_sync_urls'] ) ? array_values( $_POST['mphb_sync_urls'] ) : array();
				array_walk_recursive( $urls, function ( &$url, $key ){
					$url = esc_url_raw( $url );
				} );
				$room->setSyncData( $urls );
				$this->isEdited = true;
			}

			$this->editField = FieldFactory::create(
				'mphb_sync_urls',
				array(
					'type'		 => 'complex',
					'fields'	 => array(
						FieldFactory::create('url', array(
							'type'		 => 'text',
							'default'	 => '',
							'label'		 => __( 'Calendar URL', 'motopress-hotel-booking' ),
							'size'		 => 'large'
						) )
					),
					'default'	 => array(),
					'add_label'	 => __( 'Add New Calendar', 'motopress-hotel-booking' )
				),
				$urls
			);
		} // else if ( $this->isEdit )
	}

	protected function getMenuTitle(){
		return __( 'Sync Calendars', 'motopress-hotel-booking' );
	}

	protected function getPageTitle(){
		return __( 'Sync Calendars', 'motopress-hotel-booking' );
	}

}
