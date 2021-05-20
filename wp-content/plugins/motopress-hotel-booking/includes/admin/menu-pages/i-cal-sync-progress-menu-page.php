<?php

namespace MPHB\Admin\MenuPages;

use \MPHB\Admin\RoomSyncQueueListTable;
use \MPHB\iCal\LogsHandler;

/**
 * See real onLoad() processing in iCalImportMenuPage.
 */
class iCalSyncProgressMenuPage extends AbstractMenuPage {

	/** @var \MPHB\Admin\RoomSyncQueueListTable */
	private $listTable = null;

	public function __construct( $name, $atts = array() ){
		parent::__construct( $name, $atts );
		add_action( 'admin_bar_menu', array( $this, 'showSyncProgressLink' ), 100 );
	}

	public function addActions(){
		parent::addActions();
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminScripts' ) );
	}

	/**
	 *
	 * @global \WP_Scripts $wp_scripts
	 */
	public function enqueueAdminScripts(){
		if ( !$this->isCurrentPage() ) {
			return;
		}

		wp_enqueue_style( 'mphb-admin-ical-css', MPHB()->getPluginUrl( 'assets/css/admin-ical.min.css' ), array(), MPHB()->getVersion() );

		wp_enqueue_script( 'mphb-admin-ical', MPHB()->getPluginUrl( 'assets/js/admin/admin-ical.min.js' ), array( 'jquery', 'mphb-canjs' ), MPHB()->getVersion(), true );

		wp_localize_script( 'mphb-admin-ical', 'MPHB_iCal', array(
			'ajaxUrl'	 => MPHB()->getAjaxUrl(),
			'actions'	 => array(
				'sync'	 => array(
					'progress'		 => 'mphb_ical_sync_get_progress',
					'abort'			 => 'mphb_ical_sync_abort',
					'remove_item'	 => 'mphb_ical_sync_remove_item',
					'clear_all'		 => 'mphb_ical_sync_clear_all'
				)
			),
			'nonces'	 => MPHB()->getAjax()->getAdminNonces(),
			'i18n'		 => array(
				'abort'			 => __( 'Abort Process', 'motopress-hotel-booking' ),
				'aborting'		 => __( 'Aborting...', 'motopress-hotel-booking' ),
				'clear'			 => __( 'Delete All Logs', 'motopress-hotel-booking' ),
				'clearing'		 => __( 'Deleting...', 'motopress-hotel-booking' ),
				'items_singular' => __( '%d item', 'motopress-hotel-booking' ),
				'items_plural'	 => __( '%d items', 'motopress-hotel-booking' )
			),
			'inProgress' => MPHB()->getSyncRoomsQueueHandler()->isInProgress()
		) );
	}

	/**
	 * See real onLoad() processing in iCalImportMenuPage.
	 *
	 * @see \MPHB\Admin\MenuPages\iCalImportMenuPage::onLoad()
	 */
	public function onLoad(){
		if ( !$this->isCurrentPage() ) {
			return;
		}

		$this->listTable = new RoomSyncQueueListTable();
		$this->listTable->prepare_items();
	}

	public function render(){
		$logsHandler  = new LogsHandler();
		$queueHandler = MPHB()->getSyncRoomsQueueHandler();
		?>
		<div class="wrap mphb-sync-details-wrapper">
			<h1 class="wp-heading-inline"><?php _e( 'Calendars Synchronization Status', 'motopress-hotel-booking' ); ?></h1>
			<hr class="wp-header-end" />
			<p>
				<?php _e( 'Here you can see synchronization status of your external calendars.', 'motopress-hotel-booking' ); ?>
			</p>
			<p>
				<?php
				$syncAllUrl = admin_url( 'admin.php?page=mphb_ical_import&action=sync&accommodation_ids=all' );
				echo '<a href="', esc_url( $syncAllUrl ), '" class="button">', __( 'Sync All External Calendars', 'motopress-hotel-booking' ), '</a>';
				?>
				<?php $logsHandler->displayAbortButton( !$queueHandler->isInProgress() ); ?>
				<?php $logsHandler->displayClearButton( $queueHandler->isQueueEmpty() ); ?>
			</p>
			<p>
				<?php $logsHandler->displayExpandAllButton(); ?>
				<?php $logsHandler->displayCollapseAllButton(); ?>
			</p>
			<?php
			// Render sync queue list if not empty
			echo '<form id="', sanitize_key( $this->listTable->get_plural() ), '-filter" method="POST" action="">';
				echo '<input type="hidden" name="page" value="', esc_attr( $_REQUEST['page'] ), '" />'; // We need to ensure that the form posts back to our current page
				$this->listTable->display();
			echo '</form>';
			?>
		</div>
		<?php
	}

	protected function getMenuTitle(){
		return '';
	}

	protected function getPageTitle(){
		return __( 'Calendars Sync Status', 'motopress-hotel-booking' );
	}

	/**
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar
	 */
	public function showSyncProgressLink( $wp_admin_bar ){
		if (
			MPHB()->getSyncRoomsQueueHandler()->isInProgress() ||
			MPHB()->getSyncRoomsQueueHandler()->hasProcessedData()
		) {
			$args = array(
				'id'	 => 'mphb_ical_show_sync_progress',
				'title'	 => __( 'Calendars Sync Status', 'motopress-hotel-booking' ),
				'href'	 => $this->getUrl(),
				'meta'	 => array(
//					'class'	 => '',
					'title' => __( 'Display calendars synchronization status.', 'motopress-hotel-booking' )
				)
			);
			$wp_admin_bar->add_node( $args );
		}
	}

}
