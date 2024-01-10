<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class SiteGuard_WAF_Exclude_Rule_Table extends WP_List_Table {

	function __construct() {
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'rule',   //singular name of the listed records
			'plural'   => 'rules',  //plural name of the listed records
			'ajax'	   => false,    //does this table support ajax?
		) );

	}

	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'filename':
			case 'comment':
				return esc_html( $item[ $column_name ] );
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	function column_sig( $item ) {

		//Build row actions
		$actions = array(
			'edit'   => '<a href="' . esc_url( sprintf( '?page=%s&action=edit&rule=%s', esc_html( $_REQUEST['page'] ), esc_html( $item['ID'] ) ) ) . '">' . esc_html( __( 'Edit' ) ) . '</a>' ,
			'delete' => '<a href="' . esc_url( sprintf( '?page=%s&action=delete&rule=%s', esc_html( $_REQUEST['page'] ), esc_html( $item['ID'] ) ) ) . '">' . esc_html( __( 'Delete' ) ) . '</a>',
		);

		//Return the target contents
		return sprintf( '%1$s%2$s',
			/*$1%s*/ esc_html( $item['sig'] ),
			/*$2%s*/ $this->row_actions( $actions )
		);
	}


	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ esc_attr( $this->_args['singular'] ), //Let's simply repurpose the table's singular label ("rule")
			/*$2%s*/ esc_attr( $item['ID'] )               //The value of the checkbox should be the record's id
		);
	}


	function get_columns( ) {
		$columns = array(
			'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
			'sig'       => esc_html__( 'Signature', 'siteguard' ),
			'filename'  => esc_html__( 'Filename', 'siteguard' ),
			'comment'   => esc_html__( 'Comment', 'siteguard' ),
		);
		return $columns;
	}

	function get_sortable_columns( ) {
		$sortable_columns = array(
			'sig'       => array( 'sig', false ),
			'filename'  => array( 'filename', false ),
			'comment'   => array( 'comment', false ),
		);
		return $sortable_columns;
	}

	function get_bulk_actions( ) {
		$actions = array(
			'delete' => esc_html__( 'Delete' ),
		);
		return $actions;
	}


	function process_bulk_action( ) {

		return;
	}

	function usort_reorder( $a, $b ) {
		$orderby_values = array( 'sig', 'filename', 'comment' );
		$order_values = array( 'asc', 'desc' );
		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? ( in_array( $_REQUEST['orderby'], $orderby_values ) ? $_REQUEST['orderby'] : 'sig' ) : 'sig'; //If no sort, default to filename
		$order = ( ! empty( $_REQUEST['order'] ) ) ? ( in_array( $_REQUEST['order'], $order_values ) ? $_REQUEST['order'] : 'asc' ) : 'asc'; //If no order, default to asc
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] ); //Determine sort order
		return ( 'asc' === $order ) ? $result : -$result; //Send final sort direction to usort
	}

	function prepare_items( ) {
		global $siteguard_waf_exclude_rule;

		$per_page = 5;

		$columns  = $this->get_columns( );
		$hidden   = array();
		$sortable = $this->get_sortable_columns( );

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action( );

		$data = $siteguard_waf_exclude_rule->get_rules( );

		$total_items = count( $data );
		$current_page = $this->get_pagenum( );

		if ( $total_items > 0 ) {
			if ( is_array( $data ) ) {
				usort( $data, array( $this, 'usort_reorder' ) );
				$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
			}
		}

		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,                     //WE have to calculate the total number of items
			'per_page'    => $per_page,                        //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page ), //WE have to calculate the total number of pages
		) );
	}
}
