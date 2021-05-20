<?php

namespace MPHB\Admin;

use \MPHB\iCal\BackgroundProcesses\SyncRoomsQueueHandler;
use \MPHB\iCal\LogsHandler;

if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class RoomSyncQueueListTable extends \WP_List_Table {

	/**
	 *
	 * @var int
	 */
	private $columns_count = 0;

	/**
	 *
	 * @var string
	 */
	private $order_by;

	/**
	 *
	 * @var string
	 */
	private $order;

	public function __construct(){
		parent::__construct( array(
			'singular'	 => 'accommodation',
			'plural'	 => 'accommodations',
			'ajax'		 => false // Does this page support AJAX?
		) );

		$this->order_by	 = ( isset( $_GET['orderby'] ) ? sanitize_sql_orderby( $_GET['orderby'] ) : 'none' );
		$this->order_by	 = preg_replace( '/\s+.*/', '', $this->order_by ); // Remove order, allowed by sanitize_sql_orderby()
		$this->order	 = ( isset( $_GET['order'] ) ? strtoupper( sanitize_text_field( $_GET['order'] ) ) : 'ASC' );

		if ( !in_array( $this->order, array( 'ASC', 'DESC' ) ) ) {
			$this->order = 'ASC';
		}

		// Set descendant order for default case
		if ( $this->order_by == 'date' ) {
			$this->order = 'DESC';
		}
	}

	/**
	 * This required method is where you prepare your data for display. This
	 * method will usually be used to query the database, sort and filter the
	 * data, and generally get it ready to be displayed. At a minimum, we should
	 * set $this->items and $this->set_pagination_args().
	 */
	public function prepare_items(){
		// The $this->_column_headers property takes an array to be used by
		// class for column headers
		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns()
		);

		$this->columns_count = count( $this->_column_headers[0] );

		// Handle bulk actions
		$this->process_bulk_action();

		// $queue = [ %room key% (time + room ID) => [ "roomId", "status", "logs", "stats" ] ];
		$queue = MPHB()->getSyncRoomsQueueHandler()->getFullQueueRoomData();

		$this->items = array();
		foreach ( $queue as $key => $details ) {

			$room  = MPHB()->getRoomRepository()->findById( $details['roomId'] );
			$stats = $details['stats'];
			$time  = SyncRoomsQueueHandler::retrieveTimeFromKey( $key );
			/* translators: This is date and time format 31/12/2017 - 23:59:59' Please set the order for 'd/m/Y - H:i:s' that is most commonly used in the country of your target language. See http://php.net/date */
			$date  = date( _x( 'd/m/Y - H:i:s', 'This is date and time format 31/12/2017 - 23:59:59', 'motopress-hotel-booking' ), $time );

			$this->items[] = array(
				'ID'		 => $details['roomId'],
				'queueId'	 => $key,
				'status'	 => $details['status'],
				'title'		 => $room ? $room->getTitle() : _x('(no title)', 'Placeholder for empty accommodation title', 'motopress-hotel-booking'),
				'total'		 => $stats['total'],
				'succeed'	 => $stats['succeed'],
				'failed'	 => $stats['failed'],
				'skipped'	 => $stats['skipped'],
				'time'		 => $time,
				'date'		 => $date,
				'logs'		 => $details['logs']
			);

		} // For each queue item

		// No pagination for rooms sync queue
		$this->set_pagination_args( array(
			'total_items'	 => count( $this->items ),
			'per_page'		 => count( $this->items ),
			'total_pages'	 => 1
		) );
	}

	/**
	 * Required to dictate the table's columns and titles.
	 *
	 * @return array An associative array [ %slug% => %Title% ].
	 */
	public function get_columns(){
		// Note: WordPress will properly handle only "cb" checkboxes for bulk actions
		$columns = array(
//			'cb'		 => '<input type="checkbox" />',
			'title'		 => __( 'Accommodation', 'motopress-hotel-booking' ),
			'status'	 => __( 'Status', 'motopress-hotel-booking' ),
			'errors'	 => __( 'Errors', 'motopress-hotel-booking' ),
			'total'		 => __( 'Total', 'motopress-hotel-booking' ),
			'succeed'	 => __( 'Succeed', 'motopress-hotel-booking' ),
			'failed'	 => __( 'Failed', 'motopress-hotel-booking' ),
			'skipped'	 => __( 'Skipped', 'motopress-hotel-booking' ),
			'date'		 => __( 'Date', 'motopress-hotel-booking' ),
			'actions'	 => __( 'Actions', 'motopress-hotel-booking' )
		);
		return $columns;
	}

	/**
	 *
	 * @return array
	 */
	public function get_sortable_columns(){
		$sortableColumns = array(
//			'date' => array( 'date', ( $this->order_by == 'date' ) ) // true/false - is it already sorted?
		);
		return $sortableColumns;
	}

	private function column_placeholder(){
		return '<span aria-hidden="true">&#8212;</span>';
	}

	/**
	 * This method is called when the parent class can't find a method
	 * specifically build for a given column.
	 *
	 * @param MPHB\Entities\Room $item A singular item (one full row's worth of data).
	 * @param string $columnName The name/slug of the column to be processed.
	 *
	 * @return string Text or HTML to be placed inside the column &lt;td&gt;.
	 */
	public function column_default( $item, $columnName ){
		switch ( $columnName ) {
			default:
				if ( !empty( $item[$columnName] ) ) {
					return $item[$columnName];
				} else {
					return $this->column_placeholder();
				}
		}
	}

	/**
	 * Required if displaying checkboxes or using bulk actions! The "cb" column
	 * is given special treatment when columns are processed. It always needs to
	 * have it's own method.
	 *
	 * @param MPHB\Entities\Room $item A singular item (one full row's worth of data).
	 *
	 * @return string Text or HTML to be placed inside the column &lt;td&gt;.
	 */
	public function column_cb( $item ){
		return '<input type="checkbox" name="ids[]" value="' . $item['ID'] . '" />';
	}

	/**
	 * Method specially for column "Title".
	 *
	 * @param MPHB\Entities\Room $item A singular item (one full row's worth of data).
	 *
	 * @return string Text or HTML to be placed inside the column &lt;td&gt;.
	 */
	public function column_title( $item ){
		$actions = array(
//			'remove' => sprintf( '<span class="trash"><a href="#">%s</a></span>', __( 'Remove', 'motopress-hotel-booking' ) )
		);
		$title = '<h4><a href="#" class="mphb-expand-item">' . $item['title'] . '</a></h4>';
		return $title . $this->row_actions( $actions );
	}

	public function column_status( $item ){
		return '<span class="' . $item['status']['class'] . '">' . $item['status']['text'] . '</span>';
	}

	public function column_errors( $item ){
		$failed = $item['failed'];
		return $failed > 0 ? $failed : $this->column_placeholder();
	}

	public function column_date( $item ){
		return $item['date'];
	}

	public function column_actions( $item ){
		return '<button class="mphb-remove-item button-link">' . __( 'Delete Log', 'motopress-hotel-booking' ) . '</button>';
	}

	public function single_row( $item ){
		$classAttr = $item['failed'] > 0 ? ' class="mphb-have-errors"' : '';
		echo '<tr' . $classAttr . ' data-sync-status="' . $item['status']['value'] . '" data-item-key="' . $item['queueId'] . '">';
		$this->single_row_columns( $item );
		echo '</tr>';

		// Add logs wrapper
		echo '<tr class="mphb-logs-wrapper hidden">';
		echo '<td colspan="' . $this->columns_count . '">';
		$logsHandler = new \MPHB\iCal\LogsHandler();
		$logsHandler->displayLogs( $item['logs'], false );
		echo '</td>';
		echo '</tr>';
	}

	protected function get_table_classes(){
		$classes = parent::get_table_classes();
		$classes[] = 'mphb-ical-sync-table';
		return $classes;
	}

	/**
	 *
	 * @return array
	 */
	public function get_bulk_actions(){
		$actions = array(
//			'%action%' => __( 'Action', 'motopress-hotel-booking' )
		);
		return $actions;
	}

	public function process_bulk_action(){
		$action = $this->current_action();
		if ( empty( $action ) ) {
			return;
		}

		// Verify the nonce
		check_admin_referer( 'bulk-' . $this->get_plural() );

		switch ( $action ) {
			case 'action':
				break;
		}
	}

	/**
	 * Just a getter. Not required for WP_List_Table.
	 */
	public function get_plural(){
		return $this->_args['plural'];
	}

}
