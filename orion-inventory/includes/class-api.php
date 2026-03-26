<?php
/**
 * REST API class for Orion Brothers Inventory System.
 * Registers all API routes under the 'orion/v1' namespace.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Orion_API {

	const NAMESPACE = 'orion/v1';

	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		$ns = self::NAMESPACE;

		// Auth
		register_rest_route( $ns, '/login',  [ 'methods' => 'POST', 'callback' => [ $this, 'handle_login' ],  'permission_callback' => '__return_true' ] );
		register_rest_route( $ns, '/logout', [ 'methods' => 'POST', 'callback' => [ $this, 'handle_logout' ], 'permission_callback' => '__return_true' ] );

		// Categories
		register_rest_route( $ns, '/categories', [
			[ 'methods' => 'GET',  'callback' => [ $this, 'get_categories' ],    'permission_callback' => [ $this, 'check_auth' ] ],
			[ 'methods' => 'POST', 'callback' => [ $this, 'create_category' ],   'permission_callback' => [ $this, 'check_auth' ] ],
		] );
		register_rest_route( $ns, '/categories/(?P<id>\d+)', [
			'methods'             => 'DELETE',
			'callback'            => [ $this, 'delete_category' ],
			'permission_callback' => [ $this, 'check_auth' ],
		] );

		// Products
		register_rest_route( $ns, '/products', [
			[ 'methods' => 'GET',  'callback' => [ $this, 'get_products' ],    'permission_callback' => [ $this, 'check_auth' ] ],
			[ 'methods' => 'POST', 'callback' => [ $this, 'create_product' ],  'permission_callback' => [ $this, 'check_auth' ] ],
		] );
		register_rest_route( $ns, '/products/(?P<id>\d+)', [
			[ 'methods' => 'PUT',    'callback' => [ $this, 'update_product' ], 'permission_callback' => [ $this, 'check_auth' ] ],
			[ 'methods' => 'DELETE', 'callback' => [ $this, 'delete_product' ], 'permission_callback' => [ $this, 'check_auth' ] ],
		] );

		// Sales
		register_rest_route( $ns, '/sales', [
			[ 'methods' => 'GET',  'callback' => [ $this, 'get_sales' ],   'permission_callback' => [ $this, 'check_auth' ] ],
			[ 'methods' => 'POST', 'callback' => [ $this, 'create_sale' ], 'permission_callback' => [ $this, 'check_auth' ] ],
		] );
		register_rest_route( $ns, '/sales/(?P<id>\d+)', [
			[ 'methods' => 'GET',    'callback' => [ $this, 'get_sale' ],    'permission_callback' => [ $this, 'check_auth' ] ],
			[ 'methods' => 'DELETE', 'callback' => [ $this, 'delete_sale' ], 'permission_callback' => [ $this, 'check_auth' ] ],
		] );

		// Repairs
		register_rest_route( $ns, '/repairs', [
			[ 'methods' => 'GET',  'callback' => [ $this, 'get_repairs' ],   'permission_callback' => [ $this, 'check_auth' ] ],
			[ 'methods' => 'POST', 'callback' => [ $this, 'create_repair' ], 'permission_callback' => [ $this, 'check_auth' ] ],
		] );
		register_rest_route( $ns, '/repairs/(?P<id>\d+)', [
			'methods'             => 'DELETE',
			'callback'            => [ $this, 'delete_repair' ],
			'permission_callback' => [ $this, 'check_auth' ],
		] );

		// Credit Sales
		register_rest_route( $ns, '/credit-sales', [
			[ 'methods' => 'GET',  'callback' => [ $this, 'get_credit_sales' ],   'permission_callback' => [ $this, 'check_auth' ] ],
			[ 'methods' => 'POST', 'callback' => [ $this, 'create_credit_sale' ], 'permission_callback' => [ $this, 'check_auth' ] ],
		] );
		register_rest_route( $ns, '/credit-sales/(?P<id>\d+)/payment', [
			'methods'             => 'PUT',
			'callback'            => [ $this, 'update_credit_payment' ],
			'permission_callback' => [ $this, 'check_auth' ],
		] );
		register_rest_route( $ns, '/credit-history', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_credit_history' ],
			'permission_callback' => [ $this, 'check_auth' ],
		] );

		// Product Summary
		register_rest_route( $ns, '/product-summary', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_product_summary' ],
			'permission_callback' => [ $this, 'check_auth' ],
		] );

		// Financial Summary
		register_rest_route( $ns, '/financial-summary', [
			[ 'methods' => 'GET',  'callback' => [ $this, 'get_financial_summary' ],  'permission_callback' => [ $this, 'check_auth' ] ],
			[ 'methods' => 'POST', 'callback' => [ $this, 'save_financial_summary' ], 'permission_callback' => [ $this, 'check_auth' ] ],
		] );

		// Import
		register_rest_route( $ns, '/import', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'save_import' ],
			'permission_callback' => [ $this, 'check_auth' ],
		] );
		register_rest_route( $ns, '/import-history', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_import_history' ],
			'permission_callback' => [ $this, 'check_auth' ],
		] );

		// Stock
		register_rest_route( $ns, '/stock', [
			[ 'methods' => 'GET',  'callback' => [ $this, 'get_stock' ],    'permission_callback' => [ $this, 'check_auth' ] ],
			[ 'methods' => 'POST', 'callback' => [ $this, 'update_stock' ], 'permission_callback' => [ $this, 'check_auth' ] ],
		] );

		// Users (admin only)
		register_rest_route( $ns, '/users', [
			[ 'methods' => 'GET',  'callback' => [ $this, 'get_users' ],   'permission_callback' => [ $this, 'check_admin' ] ],
			[ 'methods' => 'POST', 'callback' => [ $this, 'create_user' ], 'permission_callback' => [ $this, 'check_admin' ] ],
		] );
		register_rest_route( $ns, '/users/(?P<id>\d+)', [
			[ 'methods' => 'PUT',    'callback' => [ $this, 'update_user' ],  'permission_callback' => [ $this, 'check_auth' ] ],
			[ 'methods' => 'DELETE', 'callback' => [ $this, 'delete_user' ],  'permission_callback' => [ $this, 'check_admin' ] ],
		] );

		// Profile — current user CRUD
		register_rest_route( $ns, '/me', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_me' ],
			'permission_callback' => [ $this, 'check_auth' ],
		] );
		register_rest_route( $ns, '/profile', [
			[ 'methods' => 'GET',  'callback' => [ $this, 'get_me' ],          'permission_callback' => [ $this, 'check_auth' ] ],
			[ 'methods' => 'POST', 'callback' => [ $this, 'update_profile' ],  'permission_callback' => [ $this, 'check_auth' ] ],
		] );

		// Profile picture
		register_rest_route( $ns, '/profile/picture', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'upload_profile_picture' ],
			'permission_callback' => [ $this, 'check_auth' ],
		] );

		// Analytics
		register_rest_route( $ns, '/analytics', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_analytics' ],
			'permission_callback' => [ $this, 'check_admin' ],
		] );
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Validates the session token from the Authorization header or cookie.
	 */
	public function check_auth( WP_REST_Request $request ) {
		return $this->get_request_user( $request ) !== null;
	}

	/**
	 * Checks that the user is an admin or super_admin.
	 */
	public function check_admin( WP_REST_Request $request ) {
		$user = $this->get_request_user( $request );
		return $user && in_array( $user->role, [ 'admin', 'super_admin' ], true );
	}

	// -------------------------------------------------------------------------
	// Auth endpoints
	// -------------------------------------------------------------------------

	public function handle_login( WP_REST_Request $request ) {
		$username = sanitize_text_field( $request->get_param( 'username' ) ?? '' );
		$password = $request->get_param( 'password' ) ?? '';

		if ( ! $username || ! $password ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Username and password are required' ], 400 );
		}

		$result = Orion_Auth::login( $username, $password );

		if ( ! $result['success'] ) {
			return new WP_REST_Response( $result, 401 );
		}

		return new WP_REST_Response( $result, 200 );
	}

	public function handle_logout( WP_REST_Request $request ) {
		Orion_Auth::logout();
		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	// -------------------------------------------------------------------------
	// Category endpoints
	// -------------------------------------------------------------------------

	public function get_categories( WP_REST_Request $request ) {
		$type = $request->get_param( 'type' );
		$data = $type ? Orion_DB::get_categories_by_type( $type ) : Orion_DB::get_all_categories();
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ], 200 );
	}

	public function create_category( WP_REST_Request $request ) {
		$name = sanitize_text_field( $request->get_param( 'name' ) ?? '' );
		$type = sanitize_text_field( $request->get_param( 'type' ) ?? 'sales' );

		if ( ! $name ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Category name is required' ], 400 );
		}

		$id = Orion_DB::insert_category( compact( 'name', 'type' ) );
		return new WP_REST_Response( [ 'success' => true, 'id' => $id, 'name' => $name, 'type' => $type ], 201 );
	}

	public function delete_category( WP_REST_Request $request ) {
		$id = (int) $request->get_param( 'id' );
		Orion_DB::delete_category( $id );
		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	// -------------------------------------------------------------------------
	// Product endpoints
	// -------------------------------------------------------------------------

	public function get_products( WP_REST_Request $request ) {
		$category_id = $request->get_param( 'category_id' );
		$type        = $request->get_param( 'type' );

		if ( $category_id ) {
			$data = Orion_DB::get_products_by_category( (int) $category_id );
		} else {
			$data = Orion_DB::get_all_products( $type ?: null );
		}

		// Support both raw-array consumers (sales form) and {success,data} consumers (admin panel).
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ], 200 );
	}

	public function create_product( WP_REST_Request $request ) {
		$data = [
			'category_id' => (int) ( $request->get_param( 'category_id' ) ?? 0 ),
			'name'        => sanitize_text_field( $request->get_param( 'name' ) ?? '' ),
			'price'       => (float) ( $request->get_param( 'price' ) ?? 0 ),
			'type'        => sanitize_text_field( $request->get_param( 'type' ) ?? 'sales' ),
		];

		if ( ! $data['name'] ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Product name is required' ], 400 );
		}

		$id = Orion_DB::insert_product( $data );
		return new WP_REST_Response( array_merge( [ 'success' => true, 'id' => $id ], $data ), 201 );
	}

	public function update_product( WP_REST_Request $request ) {
		$id   = (int) $request->get_param( 'id' );
		$data = array_filter(
			[
				'name'        => $request->get_param( 'name' ) !== null ? sanitize_text_field( $request->get_param( 'name' ) ) : null,
				'price'       => $request->get_param( 'price' ) !== null ? (float) $request->get_param( 'price' ) : null,
				'category_id' => $request->get_param( 'category_id' ) !== null ? (int) $request->get_param( 'category_id' ) : null,
			],
			function ( $v ) {
				return $v !== null;
			}
		);

		Orion_DB::update_product( $id, $data );
		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	public function delete_product( WP_REST_Request $request ) {
		Orion_DB::delete_product( (int) $request->get_param( 'id' ) );
		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	// -------------------------------------------------------------------------
	// Sales endpoints
	// -------------------------------------------------------------------------

	public function create_sale( WP_REST_Request $request ) {
		$user = $this->get_request_user( $request );

		$sale_data = [
			'customer_name'     => sanitize_text_field( $request->get_param( 'customer_name' ) ?? '' ),
			'customer_whatsapp' => sanitize_text_field( $request->get_param( 'customer_whatsapp' ) ?? '' ),
			'payment_method'    => sanitize_text_field( $request->get_param( 'payment_method' ) ?? '' ),
			'transfer_amount'   => (float) ( $request->get_param( 'transfer_amount' ) ?? 0 ),
			'cash_amount'       => (float) ( $request->get_param( 'cash_amount' ) ?? 0 ),
			'total_amount'      => (float) ( $request->get_param( 'total_amount' ) ?? 0 ),
			'discount_total'    => (float) ( $request->get_param( 'discount_total' ) ?? 0 ),
			'staff_id'          => $user ? (int) $user->id : 0,
			'order_date'        => sanitize_text_field( $request->get_param( 'order_date' ) ?? current_time('Y-m-d') ),
		];

		$items = $request->get_param( 'items' ) ?? [];

		if ( empty( $items ) ) {
			return new WP_REST_Response( [ 'message' => 'Sale items are required' ], 400 );
		}

		$id = Orion_DB::insert_sale( $sale_data, $items );

		if ( ! $id ) {
			return new WP_REST_Response( [ 'message' => 'Failed to create sale' ], 500 );
		}

		return new WP_REST_Response( [ 'id' => $id, 'receipt' => orion_generate_receipt_number() ], 201 );
	}

	public function get_sales( WP_REST_Request $request ) {
		$limit    = (int) ( $request->get_param( 'limit' )  ?? 50 );
		$offset   = (int) ( $request->get_param( 'offset' ) ?? 0 );
		$staff_id = $request->get_param( 'staff_id' );
		$data     = Orion_DB::get_sales_history( $limit, $offset, $staff_id ? (int) $staff_id : null );
		return new WP_REST_Response( $data, 200 );
	}

	public function get_sale( WP_REST_Request $request ) {
		$sale = Orion_DB::get_sale_with_items( (int) $request->get_param( 'id' ) );

		if ( ! $sale ) {
			return new WP_REST_Response( [ 'message' => 'Sale not found' ], 404 );
		}

		return new WP_REST_Response( $sale, 200 );
	}

	public function delete_sale( WP_REST_Request $request ) {
		Orion_DB::delete_sale( (int) $request->get_param( 'id' ) );
		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	// -------------------------------------------------------------------------
	// Repairs endpoints
	// -------------------------------------------------------------------------

	public function create_repair( WP_REST_Request $request ) {
		$user = $this->get_request_user( $request );

		$data = [
			'category'          => sanitize_text_field( $request->get_param( 'category' ) ?? '' ),
			'complaint'         => sanitize_textarea_field( $request->get_param( 'complaint' ) ?? '' ),
			'diagnosis'         => sanitize_textarea_field( $request->get_param( 'diagnosis' ) ?? '' ),
			'solution'          => sanitize_textarea_field( $request->get_param( 'solution' ) ?? '' ),
			'price'             => (float) ( $request->get_param( 'price' ) ?? 0 ),
			'total'             => (float) ( $request->get_param( 'total' ) ?? 0 ),
			'customer_name'     => sanitize_text_field( $request->get_param( 'customer_name' ) ?? '' ),
			'customer_whatsapp' => sanitize_text_field( $request->get_param( 'customer_whatsapp' ) ?? '' ),
			'customer_image'    => esc_url_raw( $request->get_param( 'customer_image' ) ?? '' ),
			'device_image'      => esc_url_raw( $request->get_param( 'device_image' ) ?? '' ),
			'payment_method'    => sanitize_text_field( $request->get_param( 'payment_method' ) ?? '' ),
			'transfer_amount'   => (float) ( $request->get_param( 'transfer_amount' ) ?? 0 ),
			'cash_amount'       => (float) ( $request->get_param( 'cash_amount' ) ?? 0 ),
			'staff_id'          => $user ? (int) $user->id : 0,
		];

		$id = Orion_DB::insert_repair( $data );
		return new WP_REST_Response( [ 'id' => $id ], 201 );
	}

	public function get_repairs( WP_REST_Request $request ) {
		$limit  = (int) ( $request->get_param( 'limit' )  ?? 50 );
		$offset = (int) ( $request->get_param( 'offset' ) ?? 0 );
		$data   = Orion_DB::get_repairs_history( $limit, $offset );
		return new WP_REST_Response( $data, 200 );
	}

	public function delete_repair( WP_REST_Request $request ) {
		Orion_DB::delete_repair( (int) $request->get_param( 'id' ) );
		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	// -------------------------------------------------------------------------
	// Credit Sales endpoints
	// -------------------------------------------------------------------------

	public function create_credit_sale( WP_REST_Request $request ) {
		$user = $this->get_request_user( $request );

		$sale_data = [
			'customer_name'     => sanitize_text_field( $request->get_param( 'customer_name' ) ?? '' ),
			'customer_whatsapp' => sanitize_text_field( $request->get_param( 'customer_whatsapp' ) ?? '' ),
			'payment_method'    => sanitize_text_field( $request->get_param( 'payment_method' ) ?? '' ),
			'transfer_amount'   => (float) ( $request->get_param( 'transfer_amount' ) ?? 0 ),
			'cash_amount'       => (float) ( $request->get_param( 'cash_amount' ) ?? 0 ),
			'total_amount'      => (float) ( $request->get_param( 'total_amount' ) ?? 0 ),
			'discount_total'    => (float) ( $request->get_param( 'discount_total' ) ?? 0 ),
			'amount_paid'       => (float) ( $request->get_param( 'amount_paid' ) ?? 0 ),
			'staff_id'          => $user ? (int) $user->id : 0,
		];

		$items = $request->get_param( 'items' ) ?? [];

		if ( empty( $items ) ) {
			return new WP_REST_Response( [ 'message' => 'Sale items are required' ], 400 );
		}

		$id = Orion_DB::insert_credit_sale( $sale_data, $items );

		if ( ! $id ) {
			return new WP_REST_Response( [ 'message' => 'Failed to create credit sale' ], 500 );
		}

		return new WP_REST_Response( [ 'id' => $id ], 201 );
	}

	public function get_credit_sales( WP_REST_Request $request ) {
		$limit  = (int) ( $request->get_param( 'limit' )  ?? 50 );
		$offset = (int) ( $request->get_param( 'offset' ) ?? 0 );
		$data   = Orion_DB::get_credit_sales( $limit, $offset );
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ], 200 );
	}

	public function get_credit_history( WP_REST_Request $request ) {
		$whatsapp = sanitize_text_field( $request->get_param( 'whatsapp' ) ?? '' );

		if ( ! $whatsapp ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'WhatsApp number is required' ], 400 );
		}

		$data = Orion_DB::get_customer_credit_history( $whatsapp );
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ], 200 );
	}

	public function update_credit_payment( WP_REST_Request $request ) {
		$id          = (int) $request->get_param( 'id' );
		$amount_paid = (float) ( $request->get_param( 'amount_paid' ) ?? 0 );

		$result = Orion_DB::update_credit_sale_payment( $id, $amount_paid );

		if ( $result === false ) {
			return new WP_REST_Response( [ 'message' => 'Credit sale not found' ], 404 );
		}

		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	// -------------------------------------------------------------------------
	// Product Summary
	// -------------------------------------------------------------------------

	public function get_product_summary( WP_REST_Request $request ) {
		return new WP_REST_Response( Orion_DB::get_product_summary(), 200 );
	}

	// -------------------------------------------------------------------------
	// Financial Summary
	// -------------------------------------------------------------------------

	public function get_financial_summary( WP_REST_Request $request ) {
		$limit  = (int) ( $request->get_param( 'limit' )  ?? 50 );
		$offset = (int) ( $request->get_param( 'offset' ) ?? 0 );
		return new WP_REST_Response( Orion_DB::get_financial_summary( $limit, $offset ), 200 );
	}

	public function save_financial_summary( WP_REST_Request $request ) {
		$user = $this->get_request_user( $request );

		$data = [
			'date'                => sanitize_text_field( $request->get_param( 'date' ) ?? current_time('Y-m-d') ),
			'total_sales'         => (float) ( $request->get_param( 'total_sales' ) ?? 0 ),
			'transfer_card_sales' => (float) ( $request->get_param( 'transfer_card_sales' ) ?? 0 ),
			'cash_sales'          => (float) ( $request->get_param( 'cash_sales' ) ?? 0 ),
			'expense'             => (float) ( $request->get_param( 'expense' ) ?? 0 ),
			'expense_remarks'     => sanitize_textarea_field( $request->get_param( 'expense_remarks' ) ?? '' ),
			'old_cash'            => (float) ( $request->get_param( 'old_cash' ) ?? 0 ),
			'cash_left'           => (float) ( $request->get_param( 'cash_left' ) ?? 0 ),
			'discount'            => (float) ( $request->get_param( 'discount' ) ?? 0 ),
			'debtors_transfer'    => (float) ( $request->get_param( 'debtors_transfer' ) ?? 0 ),
			'debtors_cash'        => (float) ( $request->get_param( 'debtors_cash' ) ?? 0 ),
			'staff_id'            => $user ? (int) $user->id : 0,
		];

		$id = Orion_DB::insert_financial_summary( $data );
		return new WP_REST_Response( [ 'id' => $id ], 201 );
	}

	// -------------------------------------------------------------------------
	// Import endpoints
	// -------------------------------------------------------------------------

	public function save_import( WP_REST_Request $request ) {
		$user = $this->get_request_user( $request );

		$data = [
			'product_id'   => (int) ( $request->get_param( 'product_id' ) ?? 0 ),
			'product_name' => sanitize_text_field( $request->get_param( 'product_name' ) ?? '' ),
			'quantity'     => (int) ( $request->get_param( 'quantity' ) ?? 0 ),
			'staff_id'     => $user ? (int) $user->id : 0,
		];

		if ( ! $data['product_id'] || $data['quantity'] <= 0 ) {
			return new WP_REST_Response( [ 'message' => 'product_id and a positive quantity are required' ], 400 );
		}

		$id = Orion_DB::insert_import( $data );
		return new WP_REST_Response( [ 'id' => $id ], 201 );
	}

	public function get_import_history( WP_REST_Request $request ) {
		$limit  = (int) ( $request->get_param( 'limit' )  ?? 50 );
		$offset = (int) ( $request->get_param( 'offset' ) ?? 0 );
		return new WP_REST_Response( Orion_DB::get_import_history( $limit, $offset ), 200 );
	}

	// -------------------------------------------------------------------------
	// Stock endpoints
	// -------------------------------------------------------------------------

	public function get_stock( WP_REST_Request $request ) {
		$date = $request->get_param( 'date' );
		return new WP_REST_Response( Orion_DB::get_stock( $date ? sanitize_text_field( $date ) : null ), 200 );
	}

	public function update_stock( WP_REST_Request $request ) {
		$user       = $this->get_request_user( $request );
		$product_id = (int) ( $request->get_param( 'product_id' ) ?? 0 );
		$date       = sanitize_text_field( $request->get_param( 'date' ) ?? current_time('Y-m-d') );

		if ( ! $product_id ) {
			return new WP_REST_Response( [ 'message' => 'product_id is required' ], 400 );
		}

		$data = [
			'product_name'  => sanitize_text_field( $request->get_param( 'product_name' ) ?? '' ),
			'opening_stock' => (int) ( $request->get_param( 'opening_stock' ) ?? 0 ),
			'import_qty'    => (int) ( $request->get_param( 'import_qty' ) ?? 0 ),
			'sold_stock'    => (int) ( $request->get_param( 'sold_stock' ) ?? 0 ),
			'closing_stock' => (int) ( $request->get_param( 'closing_stock' ) ?? 0 ),
			'staff_id'      => $user ? (int) $user->id : 0,
		];

		$id = Orion_DB::update_stock( $product_id, $date, $data );
		return new WP_REST_Response( [ 'id' => $id ], 200 );
	}

	// -------------------------------------------------------------------------
	// User endpoints
	// -------------------------------------------------------------------------

	public function get_users( WP_REST_Request $request ) {
		$users = Orion_DB::get_all_users();
		return new WP_REST_Response( [ 'success' => true, 'data' => $users ], 200 );
	}

	public function create_user( WP_REST_Request $request ) {
		$username = sanitize_text_field( $request->get_param( 'username' ) ?? '' );
		$password = $request->get_param( 'password' ) ?? '';

		if ( ! $username || ! $password ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Username and password are required' ], 400 );
		}

		if ( Orion_DB::get_user_by_username( $username ) ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Username already exists' ], 409 );
		}

		$data = [
			'username'  => $username,
			'password'  => $password,
			'role'      => sanitize_text_field( $request->get_param( 'role' ) ?? 'staff' ),
			'full_name' => sanitize_text_field( $request->get_param( 'full_name' ) ?? '' ),
			'email'     => sanitize_email( $request->get_param( 'email' ) ?? '' ),
			'phone'     => sanitize_text_field( $request->get_param( 'phone' ) ?? '' ),
		];

		$id = Orion_DB::insert_user( $data );
		return new WP_REST_Response( [ 'success' => true, 'id' => $id ], 201 );
	}

	public function update_user( WP_REST_Request $request ) {
		$current_user = $this->get_request_user( $request );
		$target_id    = (int) $request->get_param( 'id' );

		// Non-admin users can only update their own profile.
		if ( ! in_array( $current_user->role, [ 'admin', 'super_admin' ], true ) && (int) $current_user->id !== $target_id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Forbidden' ], 403 );
		}

		$data = array_filter(
			[
				'full_name' => $request->get_param( 'full_name' ) !== null ? sanitize_text_field( $request->get_param( 'full_name' ) ) : null,
				'email'     => $request->get_param( 'email' )     !== null ? sanitize_email( $request->get_param( 'email' ) ) : null,
				'phone'     => $request->get_param( 'phone' )     !== null ? sanitize_text_field( $request->get_param( 'phone' ) ) : null,
				'address'   => $request->get_param( 'address' )   !== null ? sanitize_textarea_field( $request->get_param( 'address' ) ) : null,
				'password'  => $request->get_param( 'password' )  !== null ? $request->get_param( 'password' ) : null,
				// Only admins may change roles.
				'role'      => ( in_array( $current_user->role, [ 'admin', 'super_admin' ], true ) && $request->get_param( 'role' ) !== null )
					? sanitize_text_field( $request->get_param( 'role' ) )
					: null,
			],
			fn( $v ) => $v !== null
		);

		Orion_DB::update_user( $target_id, $data );
		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	public function delete_user( WP_REST_Request $request ) {
		$current_user = $this->get_request_user( $request );
		$target_id    = (int) $request->get_param( 'id' );

		// Prevent self-deletion.
		if ( (int) $current_user->id === $target_id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'You cannot delete your own account' ], 403 );
		}

		// Only super_admin can delete another admin/super_admin.
		$target = Orion_DB::get_user_by_id( $target_id );
		if ( $target && in_array( $target->role, [ 'admin', 'super_admin' ], true )
			&& $current_user->role !== 'super_admin' ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Only super admins can delete admin accounts' ], 403 );
		}

		Orion_DB::delete_user( $target_id );
		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	// -------------------------------------------------------------------------
	// Profile (current user)
	// -------------------------------------------------------------------------

	public function get_me( WP_REST_Request $request ) {
		$user = $this->get_request_user( $request );
		if ( ! $user ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Unauthenticated' ], 401 );
		}
		return new WP_REST_Response( [
			'success'     => true,
			'id'          => (int) $user->id,
			'username'    => $user->username,
			'full_name'   => $user->full_name,
			'email'       => $user->email,
			'phone'       => $user->phone,
			'address'     => $user->address,
			'role'        => $user->role,
			'profile_pic' => $user->profile_pic,
		], 200 );
	}

	public function update_profile( WP_REST_Request $request ) {
		$user = $this->get_request_user( $request );
		if ( ! $user ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Unauthenticated' ], 401 );
		}

		$data = array_filter(
			[
				'full_name' => $request->get_param( 'full_name' ) !== null ? sanitize_text_field( $request->get_param( 'full_name' ) ) : null,
				'email'     => $request->get_param( 'email' )     !== null ? sanitize_email( $request->get_param( 'email' ) ) : null,
				'phone'     => $request->get_param( 'phone' )     !== null ? sanitize_text_field( $request->get_param( 'phone' ) ) : null,
				'address'   => $request->get_param( 'address' )   !== null ? sanitize_textarea_field( $request->get_param( 'address' ) ) : null,
			],
			fn( $v ) => $v !== null
		);

		// Password change with current-password verification.
		$new_password     = $request->get_param( 'new_password' ) ?? '';
		$current_password = $request->get_param( 'current_password' ) ?? '';

		if ( $new_password ) {
			if ( ! $current_password ) {
				return new WP_REST_Response( [ 'success' => false, 'message' => 'Current password is required' ], 400 );
			}
			if ( ! wp_check_password( $current_password, $user->password_hash ) ) {
				return new WP_REST_Response( [ 'success' => false, 'message' => 'Current password is incorrect' ], 403 );
			}
			$data['password'] = $new_password;
		}

		if ( ! empty( $data ) ) {
			Orion_DB::update_user( (int) $user->id, $data );
		}

		return new WP_REST_Response( [ 'success' => true, 'message' => 'Profile updated successfully' ], 200 );
	}

	// -------------------------------------------------------------------------
	// Profile Picture
	// -------------------------------------------------------------------------

	public function upload_profile_picture( WP_REST_Request $request ) {
		$user = $this->get_request_user( $request );

		if ( ! isset( $_FILES['picture'] ) ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'No file uploaded' ], 400 );
		}

		$file = $_FILES['picture']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		// Validate file size (max 5 MB).
		$max_size = 5 * 1024 * 1024;
		if ( $file['size'] > $max_size ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'File size exceeds the 5 MB limit' ], 400 );
		}

		// Validate MIME type — allow only common image formats.
		$allowed_mime_types = [ 'image/jpeg', 'image/png', 'image/gif', 'image/webp' ];
		$file_type          = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );

		if ( empty( $file_type['type'] ) || ! in_array( $file_type['type'], $allowed_mime_types, true ) ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed' ], 400 );
		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$overrides = [ 'test_form' => false, 'mimes' => array_fill_keys( [ 'jpg|jpeg|jpe', 'png', 'gif', 'webp' ], '' ) ];
		$uploaded  = wp_handle_upload( $file, $overrides );

		if ( isset( $uploaded['error'] ) ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => $uploaded['error'] ], 400 );
		}

		Orion_DB::update_user( $user->id, [ 'profile_pic' => $uploaded['url'] ] );

		return new WP_REST_Response( [ 'success' => true, 'url' => $uploaded['url'] ], 200 );
	}

	// -------------------------------------------------------------------------
	// Analytics
	// -------------------------------------------------------------------------

	public function get_analytics( WP_REST_Request $request ) {
		global $wpdb;

		$period = sanitize_text_field( $request->get_param( 'period' ) ?? 'today' );

		switch ( $period ) {
			case 'week':
				$start = gmdate( 'Y-m-d', strtotime( 'monday this week' ) );
				$end   = gmdate( 'Y-m-d', strtotime( 'sunday this week' ) );
				$prev_start = gmdate( 'Y-m-d', strtotime( 'monday last week' ) );
				$prev_end   = gmdate( 'Y-m-d', strtotime( 'sunday last week' ) );
				break;
			case 'month':
				$start = gmdate( 'Y-m-01' );
				$end   = gmdate( 'Y-m-t' );
				$prev_start = gmdate( 'Y-m-01', strtotime( 'first day of last month' ) );
				$prev_end   = gmdate( 'Y-m-t', strtotime( 'last day of last month' ) );
				break;
			case 'year':
				$start = gmdate( 'Y-01-01' );
				$end   = gmdate( 'Y-12-31' );
				$prev_start = gmdate( 'Y-01-01', strtotime( '-1 year' ) );
				$prev_end   = gmdate( 'Y-12-31', strtotime( '-1 year' ) );
				break;
			default: // today
				$start = $end = gmdate( 'Y-m-d' );
				$prev_start = $prev_end = gmdate( 'Y-m-d', strtotime( '-1 day' ) );
		}

		$sales_t = $wpdb->prefix . 'orion_sales';
		$items_t = $wpdb->prefix . 'orion_sale_items';

		// Total revenue in period.
		$revenue = (float) $wpdb->get_var( $wpdb->prepare(
			"SELECT COALESCE(SUM(total_amount),0) FROM {$sales_t} WHERE order_date BETWEEN %s AND %s",
			$start, $end
		) );

		// Prev period revenue.
		$prev_revenue = (float) $wpdb->get_var( $wpdb->prepare(
			"SELECT COALESCE(SUM(total_amount),0) FROM {$sales_t} WHERE order_date BETWEEN %s AND %s",
			$prev_start, $prev_end
		) );

		// Total orders.
		$orders = (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$sales_t} WHERE order_date BETWEEN %s AND %s",
			$start, $end
		) );

		// Average order value.
		$avg_order = $orders > 0 ? round( $revenue / $orders, 2 ) : 0;

		// Revenue by payment method.
		$by_method = $wpdb->get_results( $wpdb->prepare(
			"SELECT payment_method, SUM(total_amount) AS total FROM {$sales_t}
			 WHERE order_date BETWEEN %s AND %s GROUP BY payment_method",
			$start, $end
		), ARRAY_A );

		// Top selling products.
		$top_products = $wpdb->get_results( $wpdb->prepare(
			"SELECT si.product_name, SUM(si.quantity) AS qty_sold, SUM(si.total) AS revenue
			 FROM {$items_t} si
			 JOIN {$sales_t} s ON si.sale_id = s.id
			 WHERE s.order_date BETWEEN %s AND %s
			 GROUP BY si.product_name ORDER BY qty_sold DESC LIMIT 5",
			$start, $end
		), ARRAY_A );

		// Least selling products.
		$least_products = $wpdb->get_results( $wpdb->prepare(
			"SELECT si.product_name, SUM(si.quantity) AS qty_sold, SUM(si.total) AS revenue
			 FROM {$items_t} si
			 JOIN {$sales_t} s ON si.sale_id = s.id
			 WHERE s.order_date BETWEEN %s AND %s
			 GROUP BY si.product_name ORDER BY qty_sold ASC LIMIT 5",
			$start, $end
		), ARRAY_A );

		// Daily revenue trend (last 7 days regardless of period, for chart).
		$daily_trend = $wpdb->get_results( $wpdb->prepare(
			"SELECT order_date AS date, SUM(total_amount) AS revenue, COUNT(*) AS orders
			 FROM {$sales_t}
			 WHERE order_date >= %s
			 GROUP BY order_date ORDER BY order_date ASC",
			gmdate( 'Y-m-d', strtotime( '-6 days' ) )
		), ARRAY_A );

		// Category breakdown.
		$category_breakdown = $wpdb->get_results( $wpdb->prepare(
			"SELECT si.category_name, SUM(si.quantity) AS qty_sold, SUM(si.total) AS revenue
			 FROM {$items_t} si
			 JOIN {$sales_t} s ON si.sale_id = s.id
			 WHERE s.order_date BETWEEN %s AND %s
			 GROUP BY si.category_name ORDER BY revenue DESC",
			$start, $end
		), ARRAY_A );

		// Repairs count & revenue.
		$repairs_t   = $wpdb->prefix . 'orion_repairs';
		$repairs_rev = (float) $wpdb->get_var( $wpdb->prepare(
			"SELECT COALESCE(SUM(total),0) FROM {$repairs_t} WHERE DATE(created_at) BETWEEN %s AND %s",
			$start, $end
		) );
		$repairs_cnt = (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$repairs_t} WHERE DATE(created_at) BETWEEN %s AND %s",
			$start, $end
		) );

		return new WP_REST_Response( [
			'success'            => true,
			'period'             => $period,
			'start'              => $start,
			'end'                => $end,
			'revenue'            => $revenue,
			'prev_revenue'       => $prev_revenue,
			'revenue_change_pct' => $prev_revenue > 0 ? round( ( ( $revenue - $prev_revenue ) / $prev_revenue ) * 100, 1 ) : null,
			'orders'             => $orders,
			'avg_order'          => $avg_order,
			'repairs_revenue'    => $repairs_rev,
			'repairs_count'      => $repairs_cnt,
			'total_combined'     => $revenue + $repairs_rev,
			'by_method'          => $by_method,
			'top_products'       => $top_products,
			'least_products'     => $least_products,
			'daily_trend'        => $daily_trend,
			'category_breakdown' => $category_breakdown,
		], 200 );
	}

	// -------------------------------------------------------------------------
	// Private helpers
	// -------------------------------------------------------------------------

	/**
	 * Retrieves the authenticated user for the given request.
	 * Checks Authorization header first, then falls back to cookie.
	 *
	 * @param WP_REST_Request $request
	 * @return object|null
	 */
	private function get_request_user( WP_REST_Request $request ) {
		$token = null;

		$auth_header = $request->get_header( 'authorization' );
		if ( $auth_header && strpos( $auth_header, 'Bearer ' ) === 0 ) {
			$token = trim( substr( $auth_header, 7 ) );
		}

		if ( ! $token && isset( $_COOKIE[ Orion_Auth::COOKIE_NAME ] ) ) {
			$token = sanitize_text_field( wp_unslash( $_COOKIE[ Orion_Auth::COOKIE_NAME ] ) );
		}

		if ( ! $token ) {
			return null;
		}

		$session = Orion_DB::get_session( $token );
		if ( ! $session ) {
			return null;
		}

		return Orion_DB::get_user_by_id( $session->user_id );
	}
}
