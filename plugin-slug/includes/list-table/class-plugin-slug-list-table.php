<?php
/**
 * List table for displaying things.
 *
 * This class extends the WP_List_Table for a view of things.
 *
 * @since      1.0.0
 * @package    PLUGIN_NAMESPACE
 * @subpackage PLUGIN_NAMESPACE/admin
 * @author     David Baumwald <david@dream-encode.com>
 */

namespace PLUGIN_NAMESPACE\Core\ListTable;

use WP_List_Table;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List table for displaying things.
 *
 * This class extends the WP_List_Table for a view of things.
 *
 * @since      1.0.0
 * @package    PLUGIN_NAMESPACE
 * @subpackage PLUGIN_NAMESPACE/admin
 * @author     David Baumwald <david@dream-encode.com>
 */
class PLUGIN_CLASS_PREFIX_List_Table extends WP_List_Table {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'Thing', 'PLUGIN_SLUG' ),
				'plural'   => __( 'Things', 'PLUGIN_SLUG' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 * Retrieve things data from the database.
	 *
	 * @since  1.0.0
	 * @global $wpdb  Global WordPress database object.
	 * @return mixed
	 */
	public static function get_data() {
		global $wpdb;

		$sql  = "SELECT
					*,
				FROM
					{$wpdb->table}
				WHERE 1=1";

 		// @phpcs:ignore
		if ( isset( $_POST['s'] ) && ! empty( $_POST['s'] ) ) {
			$search = filter_input( INPUT_POST, 's' );

			$sql .= " AND ( po.order_id = '" . esc_sql( wp_unslash( $search ) ) . "' OR pob.bin_name LIKE '%" . esc_sql( wp_unslash( $search ) ) . "%' )"; // @phpcs:ignore @phpstan-ignore-line
		}

		$sql .= ' ORDER BY';

 		// @phpcs:ignore
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ' . esc_sql( $_REQUEST['orderby'] ); // @phpcs:ignore @phpstan-ignore-line
			$sql .= ! empty( $_REQUEST['order'] ) ? ' r.' . esc_sql( wp_unslash( $_REQUEST['order'] ) ) : ' ASC'; // @phpcs:ignore @phpstan-ignore-line
		} else {
			$sql .= ' r.timestamp DESC';
		}

		// @phpcs:ignore
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @since  1.0.0
	 * @global object  $wpdb  Global WordPress database object.
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		return $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->table}" );
	}

	/**
	 * Displayed when no records exist.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No things.', 'PLUGIN_SLUG' );
	}

	/**
	 * Render each table row.
	 *
	 * @since  1.0.0
	 * @param  array  $item  The current item from the database.
	 * @return void
	 */
	public function single_row( $item ) {
		$row_classes = array();

		echo '<tr class="' . esc_attr( implode( ' ', $row_classes ) ) . '">';

		$this->single_row_columns( $item );

		echo '</tr>';
	}

	/**
	 * Render a column when no column specific method exist.
	 *
	 * @since  1.0.0
	 * @param  array   $item         Row data.
	 * @param  string  $column_name  Column name.
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'timestamp':
				$date = wp_date( 'l F j, Y \a\t g:i a', $item['timestamp'] );

				if ( ! $date ) {
					$date = __( 'N/A', 'PLUGIN_SLUG' );
				}

				return sprintf(
					'<a href="%1$s" target="_blank">%2$s</a>',
					esc_url( $item['id'] ),
					esc_html( $date )
				);

			case 'id':
				return esc_html( $item['id'] );

			default:
				if ( is_array( $item[ $column_name ] ) ) {
					return print_r( $item, true ); // @phpcs:ignore
				} else {
					return esc_html( $item[ $column_name ] );
				}
		}
	}

	/**
	 *  Associative array of columns.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_columns() { // @phpcs:ignore @phpstan-ignore-line
		$columns = array(
			'timestamp' => __( 'Date', 'PLUGIN_SLUG' ),
			'id'        => __( 'ID', 'PLUGIN_SLUG' ),
		);

		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'timestamp' => array( 'timestamp', true ),
		);

		return $sortable_columns;
	}

	/**
	 * Define hidden columns.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_hidden_columns() {
		$hidden_columns = array();

		return $hidden_columns;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function prepare_items() { // @phpcs:ignore @phpstan-ignore-line
		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$per_page = 20;

		$data = $this->get_data();

		$current_page = $this->get_pagenum();
		$total_items  = count( $data );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $data;

		$this->_column_headers = $this->get_column_info();
	}
}
