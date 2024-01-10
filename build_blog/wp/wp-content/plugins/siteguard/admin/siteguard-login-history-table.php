<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class SiteGuard_LoginHistory_Table extends WP_List_Table {
	protected $filter_operation;
	protected $filter_type;
	protected $filter_login_name;
	protected $filter_ip_address;
	protected $filter_login_name_not;
	protected $filter_ip_address_not;

	function __construct( ) {
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'event',   //singular name of the listed records
			'plural'   => 'events',  //plural name of the listed records
			'ajax'	   => false,     //does this table support ajax?
		) );
		$referer = wp_get_referer( );
		if ( false === strpos( $referer, 'siteguard_login_history' ) ) {
			unset( $_SESSION['filter_operation'] );
			unset( $_SESSION['filter_type'] );
			unset( $_SESSION['filter_login_name'] );
			unset( $_SESSION['filter_ip_address'] );
			unset( $_SESSION['filter_login_name_not'] );
			unset( $_SESSION['filter_ip_address_not'] );
		}
		if ( isset( $_POST['filter_reset'] ) ) {
			$this->filter_operation      = SITEGUARD_LOGIN_NOSELECT;
			$this->filter_type           = SITEGUARD_LOGIN_TYPE_NOSELECT;
			$this->filter_login_name     = '';
			$this->filter_ip_address     = '';
			$this->filter_login_name_not = false;
			$this->filter_ip_address_not = false;
		} else {
			$this->filter_operation      = $this->get_filter_operation( );
			$this->filter_type           = $this->get_filter_type( );
			$this->filter_login_name     = $this->get_filter_login_name( );
			$this->filter_ip_address     = $this->get_filter_ip_address( );
			$this->filter_login_name_not = $this->get_filter_login_name_not( );
			$this->filter_ip_address_not = $this->get_filter_ip_address_not( );
		}
		if ( '' === $this->filter_login_name ) {
			$this->filter_login_name_not = false;
		}
		if ( '' === $this->filter_ip_address ) {
			$this->filter_ip_address_not = false;
		}
		$_SESSION['filter_operation']      = $this->filter_operation;
		$_SESSION['filter_type']           = $this->filter_type;
		$_SESSION['filter_login_name']     = $this->filter_login_name;
		$_SESSION['filter_ip_address']     = $this->filter_ip_address;
		$_SESSION['filter_login_name_not'] = $this->filter_login_name_not;
		$_SESSION['filter_ip_address_not'] = $this->filter_ip_address_not;
	}

	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'operation':
				return SiteGuard_LoginHistory::convert_operation( $item[ $column_name ] );
			case 'type':
				return SiteGuard_LoginHistory::convert_type( $item[ $column_name ] );
			case 'time':
			case 'login_name':
			case 'ip_address':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	function get_columns( ) {
		$columns = array(
			#'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
			'time'       => esc_html__( 'Date Time',  'siteguard' ),
			'operation'  => esc_html__( 'Operation',  'siteguard' ),
			'login_name' => esc_html__( 'Login Name', 'siteguard' ),
			'ip_address' => esc_html__( 'IP Address', 'siteguard' ),
			'type'       => esc_html__( 'Type', 'siteguard' ),
		);
		return $columns;
	}

	function get_sortable_columns( ) {
		$sortable_columns = array(
			'time'       => array( 'id', true ),    //true means it's already sorted
			'operation'  => array( 'operation', false ), //true means it's already sorted
			'login_name' => array( 'login_name', false ),
			'ip_address' => array( 'ip_address', false ),
			'type'       => array( 'type', false ),
		);
		return $sortable_columns;
	}

	function get_bulk_actions( ) {
		#$actions = array(
		#	'delete' => __( 'Delete' ),
		#);
		$actions = array();
		return $actions;
	}


	function process_bulk_action( ) {
		return;
	}

	function usort_reorder( $a, $b ) {
		$orderby_values = array( 'id', 'operation', 'time', 'login_name', 'ip_address', 'type' );
		$order_values = array( 'asc', 'desc' );
		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? ( in_array( $_REQUEST['orderby'], $orderby_values ) ? $_REQUEST['orderby'] : 'id' ) : 'id'; //If no sort, default to id
		$order = ( ! empty( $_REQUEST['order'] ) ) ? ( in_array( $_REQUEST['order'], $order_values ) ? $_REQUEST['order'] : 'desc' ) : 'desc'; //If no order, default to desc
		if ( 'id' == $orderby ) {
			$result = ( $a > $b ? 1 : ( $a < $b ? -1 : 0 ) );
		} else {
			$result = strcmp( $a[ $orderby ], $b[ $orderby ] ); //Determine sort order
		}
		return ( 'asc' == $order ) ? $result : -$result; //Send final sort direction to usort
	}
	function get_filter_param_normal( $name, $default ) {
		$result = $default;
		if ( isset( $_POST[ $name ] ) ) {
			$result =  $_POST[ $name ];
		} else if ( isset( $_SESSION[ $name ] ) ) {
			$result =  $_SESSION[ $name ];
		}
		return $result;
	}
	function get_filter_param_checkbox( $name, $default ) {
		$result = $default;
		if ( isset( $_POST['filter_action'] ) ) {
			if ( isset( $_POST[ $name ] ) ) {
				$result = true;
			} else {
				$result = false;
			}
			return $result;
		}
		if ( isset( $_SESSION[ $name ] ) ) {
			$result = $_SESSION[ $name ];
		} else {
			$result = $default;
		}
		return $result;
	}
	function get_filter_operation( ) {
		global $siteguard_login_history;
		$result = $this->get_filter_param_normal( 'filter_operation', SITEGUARD_LOGIN_NOSELECT );
		if ( ! $siteguard_login_history->check_operation( $result ) ) {
			$result = SITEGUARD_LOGIN_NOSELECT;
		}
		return $result;
	}
	function get_filter_type( ) {
		global $siteguard_login_history;
		$result = $this->get_filter_param_normal( 'filter_type', SITEGUARD_LOGIN_TYPE_NOSELECT );
		if ( ! $siteguard_login_history->check_type( $result ) ) {
			$result = SITEGUARD_LOGIN_TYPE_NOSELECT;
		}
		return $result;
	}
	function get_filter_login_name( ) {
		return $this->get_filter_param_normal( 'filter_login_name', '' );
	}
	function get_filter_ip_address( ) {
		return $this->get_filter_param_normal( 'filter_ip_address', '' );
	}
	function get_filter_login_name_not( ) {
		return $this->get_filter_param_checkbox( 'filter_login_name_not', false );
	}
	function get_filter_ip_address_not( ) {
		return $this->get_filter_param_checkbox( 'filter_ip_address_not', false );
	}
	function operation_dropdown( ) {
		?>
		<select name="filter_operation" id="filter-by-operation">
		<option <?php selected( $this->filter_operation, SITEGUARD_LOGIN_NOSELECT  ); ?> value="<?php echo SITEGUARD_LOGIN_NOSELECT  ?>"><?php echo esc_html__( 'All Operations', 'siteguard' ); ?></option>
		<option <?php selected( $this->filter_operation, SITEGUARD_LOGIN_SUCCESS   ); ?> value="<?php echo SITEGUARD_LOGIN_SUCCESS   ?>"><?php echo esc_html__( 'Success', 'siteguard' ); ?></option>
		<option <?php selected( $this->filter_operation, SITEGUARD_LOGIN_FAILED    ); ?> value="<?php echo SITEGUARD_LOGIN_FAILED    ?>"><?php echo esc_html__( 'Failed', 'siteguard' ); ?></option>
		<option <?php selected( $this->filter_operation, SITEGUARD_LOGIN_FAIL_ONCE ); ?> value="<?php echo SITEGUARD_LOGIN_FAIL_ONCE ?>"><?php echo esc_html__( 'Fail once', 'siteguard' ); ?></option>
		<option <?php selected( $this->filter_operation, SITEGUARD_LOGIN_LOCKED    ); ?> value="<?php echo SITEGUARD_LOGIN_LOCKED    ?>"><?php echo esc_html__( 'Locked', 'siteguard' ); ?></option>
		</select>
		<?php
	}
	function login_name_input( ) {
		?>
		<input type="text" name="filter_login_name" id="filter-login-name" size="15" value="<?php echo esc_attr( $this->filter_login_name ); ?>">
		<input type="checkbox" name="filter_login_name_not" id="filter-login-name-not" <?php checked( $this->filter_login_name_not, true ); ?> >
		<label for="filter-login-name-not" ><?php echo esc_html__( 'Other', 'siteguard'); ?></label>
		<?php
	}
	function ip_address_input( ) {
		?>
		<input type="text" name="filter_ip_address" id="filter-ip-address" size="15" value="<?php echo esc_attr( $this->filter_ip_address ); ?>">
		<input type="checkbox" name="filter_ip_address_not" id="filter-ip-address-not" <?php checked( $this->filter_ip_address_not, true ); ?> >
		<label for="filter-ip-address-not" ><?php echo esc_html__( 'Other', 'siteguard'); ?></label>
		<?php
	}
	function type_dropdown( ) {
		?>
		<select name="filter_type" id="filter-type">
		<option <?php selected( $this->filter_type, SITEGUARD_LOGIN_TYPE_NOSELECT ); ?> value="<?php echo SITEGUARD_LOGIN_TYPE_NOSELECT ?>"><?php echo esc_html__( 'All Types', 'siteguard' ); ?></option>
		<option <?php selected( $this->filter_type, SITEGUARD_LOGIN_TYPE_NORMAL   ); ?> value="<?php echo SITEGUARD_LOGIN_TYPE_NORMAL   ?>"><?php echo esc_html__( 'Login Page', 'siteguard' ); ?></option>
		<option <?php selected( $this->filter_type, SITEGUARD_LOGIN_TYPE_XMLRPC   ); ?> value="<?php echo SITEGUARD_LOGIN_TYPE_XMLRPC   ?>"><?php echo esc_html__( 'XMLRPC', 'siteguard' ); ?></option>
		</select>
		<?php
	}
	function extra_tablenav( $witch ) {
		if ( 'bottom' == $witch ) {
			return;
		}
		?>
		<div class="alignleft actions bulkactions">
		<table>
		<tr>
		<td><label for="filter-operation"><?php echo esc_html__( 'Operation', 'siteguard') . ':'; ?></label></td>
		<td><?php $this->operation_dropdown( ); ?></td>
		<td width="30px"></td>
		<td><label for="filter-login-name" ><?php echo __( 'Login Name', 'siteguard' ) . ':'; ?></label></td>
		<td><?php $this->login_name_input( ); ?></td>
		</tr><tr>
		<td><label for="filter-type" ><?php echo esc_html__( 'Type', 'siteguard') . ':'; ?></label></td>
		<td><?php $this->type_dropdown( ); ?></td>
		<td></td>
		<td><label for="filter-ip-address" ><?php echo __( 'IP Address', 'siteguard' ) . ':'; ?></label></td>
		<td><?php $this->ip_address_input( ); ?></td>
		</tr>
		</table>
		<input type="submit" name="filter_action" id="post-query-submit" class="button" value="<?php echo __( 'Filter' ); ?>">
		<input type="submit" name="filter_reset"  id="post-query-reset"  class="button" value="<?php echo __( 'All' ); ?>">
		</div>
		<?php
	}

	function prepare_items( ) {
		global $siteguard_login_history;

		$per_page = 10;

		$columns  = $this->get_columns( );
		$hidden   = array();
		$sortable = $this->get_sortable_columns( );

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action( );

		$data = $siteguard_login_history->get_history( $this->filter_operation, $this->filter_login_name, $this->filter_ip_address, $this->filter_type, $this->filter_login_name_not, $this->filter_ip_address_not );

		$total_items = count( $data );
		$current_page = $this->get_pagenum( );

		if ( $total_items <= ( ( $current_page - 1 ) * $per_page ) ) {
			$current_page = 1;
		}
		if ( $total_items > 0 ) {
			usort( $data, array( $this, 'usort_reorder' ) );
			$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		}

		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,                     //WE have to calculate the total number of items
			'per_page'    => $per_page,                        //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page ), //WE have to calculate the total number of pages
		) );
	}
}
