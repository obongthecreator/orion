<?php
/**
 * Database class for Orion Brothers Inventory System.
 * Handles all table creation and CRUD operations.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Orion_DB {

	// -------------------------------------------------------------------------
	// Table helpers
	// -------------------------------------------------------------------------

	private static function table( $name ) {
		global $wpdb;
		return $wpdb->prefix . 'orion_' . $name;
	}

	// -------------------------------------------------------------------------
	// Table creation
	// -------------------------------------------------------------------------

	public static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$tables = [];

		$tables[] = "CREATE TABLE " . self::table('users') . " (
			id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			username      VARCHAR(100)    NOT NULL,
			password_hash VARCHAR(255)    NOT NULL,
			role          ENUM('admin','staff','super_admin') NOT NULL DEFAULT 'staff',
			full_name     VARCHAR(150)    NOT NULL DEFAULT '',
			email         VARCHAR(150)    NOT NULL DEFAULT '',
			phone         VARCHAR(30)     NOT NULL DEFAULT '',
			address       TEXT            NOT NULL DEFAULT '',
			profile_pic   VARCHAR(255)    NOT NULL DEFAULT '',
			created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY username (username)
		) $charset_collate;";

		$tables[] = "CREATE TABLE " . self::table('categories') . " (
			id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name       VARCHAR(150)    NOT NULL,
			type       ENUM('sales','repairs') NOT NULL DEFAULT 'sales',
			created_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;";

		$tables[] = "CREATE TABLE " . self::table('products') . " (
			id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			category_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			name        VARCHAR(255)    NOT NULL,
			price       DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			type        ENUM('sales','repairs') NOT NULL DEFAULT 'sales',
			created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY category_id (category_id)
		) $charset_collate;";

		$tables[] = "CREATE TABLE " . self::table('sales') . " (
			id                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			customer_name      VARCHAR(150)    NOT NULL DEFAULT '',
			customer_whatsapp  VARCHAR(30)     NOT NULL DEFAULT '',
			payment_method     VARCHAR(50)     NOT NULL DEFAULT '',
			transfer_amount    DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			cash_amount        DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			total_amount       DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			discount_total     DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			staff_id           BIGINT UNSIGNED NOT NULL DEFAULT 0,
			created_at         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
			order_date         DATE            NOT NULL DEFAULT (CURRENT_DATE),
			PRIMARY KEY (id),
			KEY staff_id (staff_id)
		) $charset_collate;";

		$tables[] = "CREATE TABLE " . self::table('sale_items') . " (
			id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			sale_id       BIGINT UNSIGNED NOT NULL,
			product_id    BIGINT UNSIGNED NOT NULL DEFAULT 0,
			product_name  VARCHAR(255)    NOT NULL DEFAULT '',
			category_name VARCHAR(150)    NOT NULL DEFAULT '',
			price         DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			quantity      INT UNSIGNED    NOT NULL DEFAULT 1,
			discount      DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			total         DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			PRIMARY KEY (id),
			KEY sale_id (sale_id)
		) $charset_collate;";

		$tables[] = "CREATE TABLE " . self::table('repairs') . " (
			id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			category          VARCHAR(150)    NOT NULL DEFAULT '',
			complaint         TEXT            NOT NULL DEFAULT '',
			diagnosis         TEXT            NOT NULL DEFAULT '',
			solution          TEXT            NOT NULL DEFAULT '',
			price             DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			total             DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			customer_name     VARCHAR(150)    NOT NULL DEFAULT '',
			customer_whatsapp VARCHAR(30)     NOT NULL DEFAULT '',
			customer_image    VARCHAR(255)    NOT NULL DEFAULT '',
			device_image      VARCHAR(255)    NOT NULL DEFAULT '',
			payment_method    VARCHAR(50)     NOT NULL DEFAULT '',
			transfer_amount   DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			cash_amount       DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			staff_id          BIGINT UNSIGNED NOT NULL DEFAULT 0,
			created_at        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY staff_id (staff_id)
		) $charset_collate;";

		$tables[] = "CREATE TABLE " . self::table('credit_sales') . " (
			id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			customer_name     VARCHAR(150)    NOT NULL DEFAULT '',
			customer_whatsapp VARCHAR(30)     NOT NULL DEFAULT '',
			payment_method    VARCHAR(50)     NOT NULL DEFAULT '',
			transfer_amount   DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			cash_amount       DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			total_amount      DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			discount_total    DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			staff_id          BIGINT UNSIGNED NOT NULL DEFAULT 0,
			created_at        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
			amount_paid       DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			balance           DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			PRIMARY KEY (id),
			KEY staff_id (staff_id),
			KEY customer_whatsapp (customer_whatsapp)
		) $charset_collate;";

		$tables[] = "CREATE TABLE " . self::table('credit_sale_items') . " (
			id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			credit_sale_id BIGINT UNSIGNED NOT NULL,
			product_id     BIGINT UNSIGNED NOT NULL DEFAULT 0,
			product_name   VARCHAR(255)    NOT NULL DEFAULT '',
			category_name  VARCHAR(150)    NOT NULL DEFAULT '',
			price          DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			quantity       INT UNSIGNED    NOT NULL DEFAULT 1,
			discount       DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			total          DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			PRIMARY KEY (id),
			KEY credit_sale_id (credit_sale_id)
		) $charset_collate;";

		$tables[] = "CREATE TABLE " . self::table('financial_summary') . " (
			id                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			date               DATE            NOT NULL,
			total_sales        DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			transfer_card_sales DECIMAL(15,2)  NOT NULL DEFAULT 0.00,
			cash_sales         DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			expense            DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			expense_remarks    TEXT            NOT NULL DEFAULT '',
			old_cash           DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			cash_left          DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			discount           DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			debtors_transfer   DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			debtors_cash       DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
			staff_id           BIGINT UNSIGNED NOT NULL DEFAULT 0,
			created_at         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY date (date)
		) $charset_collate;";

		$tables[] = "CREATE TABLE " . self::table('imports') . " (
			id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			product_id   BIGINT UNSIGNED NOT NULL DEFAULT 0,
			product_name VARCHAR(255)    NOT NULL DEFAULT '',
			quantity     INT             NOT NULL DEFAULT 0,
			staff_id     BIGINT UNSIGNED NOT NULL DEFAULT 0,
			created_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY product_id (product_id)
		) $charset_collate;";

		$tables[] = "CREATE TABLE " . self::table('stock') . " (
			id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			product_id    BIGINT UNSIGNED NOT NULL,
			product_name  VARCHAR(255)    NOT NULL DEFAULT '',
			opening_stock INT             NOT NULL DEFAULT 0,
			import_qty    INT             NOT NULL DEFAULT 0,
			sold_stock    INT             NOT NULL DEFAULT 0,
			closing_stock INT             NOT NULL DEFAULT 0,
			date          DATE            NOT NULL,
			staff_id      BIGINT UNSIGNED NOT NULL DEFAULT 0,
			updated_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY product_date (product_id, date),
			KEY product_id (product_id)
		) $charset_collate;";

		$tables[] = "CREATE TABLE " . self::table('sessions') . " (
			id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id       BIGINT UNSIGNED NOT NULL,
			session_token VARCHAR(64)     NOT NULL,
			expires_at    DATETIME        NOT NULL,
			created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY session_token (session_token),
			KEY user_id (user_id)
		) $charset_collate;";

		foreach ( $tables as $sql ) {
			dbDelta( $sql );
		}
	}

	// -------------------------------------------------------------------------
	// Users
	// -------------------------------------------------------------------------

	public static function insert_user( $data ) {
		global $wpdb;

		$insert = [
			'username'      => sanitize_text_field( $data['username'] ),
			'password_hash' => wp_hash_password( $data['password'] ),
			'role'          => sanitize_text_field( $data['role'] ?? 'staff' ),
			'full_name'     => sanitize_text_field( $data['full_name'] ?? '' ),
			'email'         => sanitize_email( $data['email'] ?? '' ),
			'phone'         => sanitize_text_field( $data['phone'] ?? '' ),
			'address'       => sanitize_textarea_field( $data['address'] ?? '' ),
			'profile_pic'   => esc_url_raw( $data['profile_pic'] ?? '' ),
		];

		$wpdb->insert( self::table('users'), $insert );
		return $wpdb->insert_id;
	}

	public static function get_user_by_username( $username ) {
		global $wpdb;
		return $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM ' . self::table('users') . ' WHERE username = %s LIMIT 1',
				$username
			)
		);
	}

	public static function get_user_by_id( $id ) {
		global $wpdb;
		return $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM ' . self::table('users') . ' WHERE id = %d LIMIT 1',
				$id
			)
		);
	}

	public static function update_user( $id, $data ) {
		global $wpdb;

		$allowed = [ 'full_name', 'email', 'phone', 'address', 'role', 'profile_pic' ];
		$update  = [];

		foreach ( $allowed as $field ) {
			if ( isset( $data[ $field ] ) ) {
				$update[ $field ] = sanitize_text_field( $data[ $field ] );
			}
		}

		if ( ! empty( $data['password'] ) ) {
			$update['password_hash'] = wp_hash_password( $data['password'] );
		}

		if ( empty( $update ) ) {
			return false;
		}

		return $wpdb->update( self::table('users'), $update, [ 'id' => (int) $id ] );
	}

	public static function get_all_users() {
		global $wpdb;
		return $wpdb->get_results(
			'SELECT id, username, role, full_name, email, phone, address, profile_pic, created_at FROM ' . self::table('users') . ' ORDER BY created_at DESC'
		);
	}

	public static function delete_user( $id ) {
		global $wpdb;
		// Also clear their sessions.
		$wpdb->delete( self::table('sessions'), [ 'user_id' => (int) $id ] );
		return $wpdb->delete( self::table('users'), [ 'id' => (int) $id ] );
	}

	// -------------------------------------------------------------------------
	// Categories
	// -------------------------------------------------------------------------

	public static function get_all_categories() {
		global $wpdb;
		return $wpdb->get_results(
			'SELECT * FROM ' . self::table('categories') . ' ORDER BY type, name ASC'
		);
	}

	public static function get_categories_by_type( $type ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . self::table('categories') . ' WHERE type = %s ORDER BY name ASC',
				$type
			)
		);
	}

	public static function insert_category( $data ) {
		global $wpdb;
		$wpdb->insert(
			self::table('categories'),
			[
				'name' => sanitize_text_field( $data['name'] ),
				'type' => sanitize_text_field( $data['type'] ?? 'sales' ),
			]
		);
		return $wpdb->insert_id;
	}

	public static function delete_category( $id ) {
		global $wpdb;
		$id = (int) $id;

		// Remove products belonging to this category first.
		$wpdb->delete( self::table('products'), [ 'category_id' => $id ] );
		return $wpdb->delete( self::table('categories'), [ 'id' => $id ] );
	}

	// -------------------------------------------------------------------------
	// Products
	// -------------------------------------------------------------------------

	public static function get_products_by_category( $category_id ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . self::table('products') . ' WHERE category_id = %d ORDER BY name ASC',
				$category_id
			)
		);
	}

	public static function get_all_products( $type = null ) {
		global $wpdb;

		if ( $type !== null ) {
			return $wpdb->get_results(
				$wpdb->prepare(
					'SELECT p.*, c.name AS category_name FROM ' . self::table('products') . ' p
					LEFT JOIN ' . self::table('categories') . ' c ON c.id = p.category_id
					WHERE p.type = %s ORDER BY c.name, p.name ASC',
					$type
				)
			);
		}

		return $wpdb->get_results(
			'SELECT p.*, c.name AS category_name FROM ' . self::table('products') . ' p
			LEFT JOIN ' . self::table('categories') . ' c ON c.id = p.category_id
			ORDER BY p.type, c.name, p.name ASC'
		);
	}

	public static function insert_product( $data ) {
		global $wpdb;
		$wpdb->insert(
			self::table('products'),
			[
				'category_id' => (int) ( $data['category_id'] ?? 0 ),
				'name'        => sanitize_text_field( $data['name'] ),
				'price'       => (float) ( $data['price'] ?? 0 ),
				'type'        => sanitize_text_field( $data['type'] ?? 'sales' ),
			]
		);
		return $wpdb->insert_id;
	}

	public static function update_product( $id, $data ) {
		global $wpdb;

		$update = [];

		if ( isset( $data['name'] ) ) {
			$update['name'] = sanitize_text_field( $data['name'] );
		}
		if ( isset( $data['price'] ) ) {
			$update['price'] = (float) $data['price'];
		}
		if ( isset( $data['category_id'] ) ) {
			$update['category_id'] = (int) $data['category_id'];
		}

		if ( empty( $update ) ) {
			return false;
		}

		return $wpdb->update( self::table('products'), $update, [ 'id' => (int) $id ] );
	}

	public static function delete_product( $id ) {
		global $wpdb;
		return $wpdb->delete( self::table('products'), [ 'id' => (int) $id ] );
	}

	// -------------------------------------------------------------------------
	// Sales
	// -------------------------------------------------------------------------

	public static function insert_sale( $sale_data, $items ) {
		global $wpdb;

		$wpdb->query( 'START TRANSACTION' );

		$wpdb->insert(
			self::table('sales'),
			[
				'customer_name'     => sanitize_text_field( $sale_data['customer_name'] ?? '' ),
				'customer_whatsapp' => sanitize_text_field( $sale_data['customer_whatsapp'] ?? '' ),
				'payment_method'    => sanitize_text_field( $sale_data['payment_method'] ?? '' ),
				'transfer_amount'   => (float) ( $sale_data['transfer_amount'] ?? 0 ),
				'cash_amount'       => (float) ( $sale_data['cash_amount'] ?? 0 ),
				'total_amount'      => (float) ( $sale_data['total_amount'] ?? 0 ),
				'discount_total'    => (float) ( $sale_data['discount_total'] ?? 0 ),
				'staff_id'          => (int) ( $sale_data['staff_id'] ?? 0 ),
				'order_date'        => sanitize_text_field( $sale_data['order_date'] ?? current_time('Y-m-d') ),
			]
		);

		$sale_id = $wpdb->insert_id;

		if ( ! $sale_id ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		foreach ( $items as $item ) {
			$wpdb->insert(
				self::table('sale_items'),
				[
					'sale_id'       => $sale_id,
					'product_id'    => (int) ( $item['product_id'] ?? 0 ),
					'product_name'  => sanitize_text_field( $item['product_name'] ?? '' ),
					'category_name' => sanitize_text_field( $item['category_name'] ?? '' ),
					'price'         => (float) ( $item['price'] ?? 0 ),
					'quantity'      => (int) ( $item['quantity'] ?? 1 ),
					'discount'      => (float) ( $item['discount'] ?? 0 ),
					'total'         => (float) ( $item['total'] ?? 0 ),
				]
			);
		}

		$wpdb->query( 'COMMIT' );
		return $sale_id;
	}

	public static function get_sales_history( $limit = 50, $offset = 0, $staff_id = null ) {
		global $wpdb;

		$limit  = (int) $limit;
		$offset = (int) $offset;

		if ( $staff_id !== null ) {
			return $wpdb->get_results(
				$wpdb->prepare(
					'SELECT s.*, u.full_name AS staff_name FROM ' . self::table('sales') . ' s
					LEFT JOIN ' . self::table('users') . ' u ON u.id = s.staff_id
					WHERE s.staff_id = %d
					ORDER BY s.created_at DESC LIMIT %d OFFSET %d',
					(int) $staff_id,
					$limit,
					$offset
				)
			);
		}

		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT s.*, u.full_name AS staff_name FROM ' . self::table('sales') . ' s
				LEFT JOIN ' . self::table('users') . ' u ON u.id = s.staff_id
				ORDER BY s.created_at DESC LIMIT %d OFFSET %d',
				$limit,
				$offset
			)
		);
	}

	public static function get_sale_with_items( $sale_id ) {
		global $wpdb;

		$sale = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT s.*, u.full_name AS staff_name FROM ' . self::table('sales') . ' s
				LEFT JOIN ' . self::table('users') . ' u ON u.id = s.staff_id
				WHERE s.id = %d LIMIT 1',
				(int) $sale_id
			)
		);

		if ( ! $sale ) {
			return null;
		}

		$sale->items = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM ' . self::table('sale_items') . ' WHERE sale_id = %d',
				(int) $sale_id
			)
		);

		return $sale;
	}

	public static function delete_sale( $sale_id ) {
		global $wpdb;
		$sale_id = (int) $sale_id;
		$wpdb->delete( self::table('sale_items'), [ 'sale_id' => $sale_id ] );
		return $wpdb->delete( self::table('sales'), [ 'id' => $sale_id ] );
	}

	// -------------------------------------------------------------------------
	// Repairs
	// -------------------------------------------------------------------------

	public static function insert_repair( $data ) {
		global $wpdb;
		$wpdb->insert(
			self::table('repairs'),
			[
				'category'          => sanitize_text_field( $data['category'] ?? '' ),
				'complaint'         => sanitize_textarea_field( $data['complaint'] ?? '' ),
				'diagnosis'         => sanitize_textarea_field( $data['diagnosis'] ?? '' ),
				'solution'          => sanitize_textarea_field( $data['solution'] ?? '' ),
				'price'             => (float) ( $data['price'] ?? 0 ),
				'total'             => (float) ( $data['total'] ?? 0 ),
				'customer_name'     => sanitize_text_field( $data['customer_name'] ?? '' ),
				'customer_whatsapp' => sanitize_text_field( $data['customer_whatsapp'] ?? '' ),
				'customer_image'    => esc_url_raw( $data['customer_image'] ?? '' ),
				'device_image'      => esc_url_raw( $data['device_image'] ?? '' ),
				'payment_method'    => sanitize_text_field( $data['payment_method'] ?? '' ),
				'transfer_amount'   => (float) ( $data['transfer_amount'] ?? 0 ),
				'cash_amount'       => (float) ( $data['cash_amount'] ?? 0 ),
				'staff_id'          => (int) ( $data['staff_id'] ?? 0 ),
			]
		);
		return $wpdb->insert_id;
	}

	public static function get_repairs_history( $limit = 50, $offset = 0 ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT r.*, u.full_name AS staff_name FROM ' . self::table('repairs') . ' r
				LEFT JOIN ' . self::table('users') . ' u ON u.id = r.staff_id
				ORDER BY r.created_at DESC LIMIT %d OFFSET %d',
				(int) $limit,
				(int) $offset
			)
		);
	}

	public static function get_repair( $id ) {
		global $wpdb;
		return $wpdb->get_row(
			$wpdb->prepare(
				'SELECT r.*, u.full_name AS staff_name FROM ' . self::table('repairs') . ' r
				LEFT JOIN ' . self::table('users') . ' u ON u.id = r.staff_id
				WHERE r.id = %d LIMIT 1',
				(int) $id
			)
		);
	}

	public static function delete_repair( $id ) {
		global $wpdb;
		return $wpdb->delete( self::table('repairs'), [ 'id' => (int) $id ] );
	}

	// -------------------------------------------------------------------------
	// Credit Sales
	// -------------------------------------------------------------------------

	public static function insert_credit_sale( $sale_data, $items ) {
		global $wpdb;

		$wpdb->query( 'START TRANSACTION' );

		$total_amount  = (float) ( $sale_data['total_amount'] ?? 0 );
		$amount_paid   = (float) ( $sale_data['amount_paid'] ?? 0 );

		$wpdb->insert(
			self::table('credit_sales'),
			[
				'customer_name'     => sanitize_text_field( $sale_data['customer_name'] ?? '' ),
				'customer_whatsapp' => sanitize_text_field( $sale_data['customer_whatsapp'] ?? '' ),
				'payment_method'    => sanitize_text_field( $sale_data['payment_method'] ?? '' ),
				'transfer_amount'   => (float) ( $sale_data['transfer_amount'] ?? 0 ),
				'cash_amount'       => (float) ( $sale_data['cash_amount'] ?? 0 ),
				'total_amount'      => $total_amount,
				'discount_total'    => (float) ( $sale_data['discount_total'] ?? 0 ),
				'staff_id'          => (int) ( $sale_data['staff_id'] ?? 0 ),
				'amount_paid'       => $amount_paid,
				'balance'           => $total_amount - $amount_paid,
			]
		);

		$credit_sale_id = $wpdb->insert_id;

		if ( ! $credit_sale_id ) {
			$wpdb->query( 'ROLLBACK' );
			return false;
		}

		foreach ( $items as $item ) {
			$wpdb->insert(
				self::table('credit_sale_items'),
				[
					'credit_sale_id' => $credit_sale_id,
					'product_id'     => (int) ( $item['product_id'] ?? 0 ),
					'product_name'   => sanitize_text_field( $item['product_name'] ?? '' ),
					'category_name'  => sanitize_text_field( $item['category_name'] ?? '' ),
					'price'          => (float) ( $item['price'] ?? 0 ),
					'quantity'       => (int) ( $item['quantity'] ?? 1 ),
					'discount'       => (float) ( $item['discount'] ?? 0 ),
					'total'          => (float) ( $item['total'] ?? 0 ),
				]
			);
		}

		$wpdb->query( 'COMMIT' );
		return $credit_sale_id;
	}

	public static function get_credit_sales( $limit = 50, $offset = 0 ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT cs.*, u.full_name AS staff_name FROM ' . self::table('credit_sales') . ' cs
				LEFT JOIN ' . self::table('users') . ' u ON u.id = cs.staff_id
				ORDER BY cs.created_at DESC LIMIT %d OFFSET %d',
				(int) $limit,
				(int) $offset
			)
		);
	}

	public static function get_customer_credit_history( $whatsapp ) {
		global $wpdb;

		$sales = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT cs.*, u.full_name AS staff_name FROM ' . self::table('credit_sales') . ' cs
				LEFT JOIN ' . self::table('users') . ' u ON u.id = cs.staff_id
				WHERE cs.customer_whatsapp = %s ORDER BY cs.created_at DESC',
				sanitize_text_field( $whatsapp )
			)
		);

		foreach ( $sales as $sale ) {
			$sale->items = $wpdb->get_results(
				$wpdb->prepare(
					'SELECT * FROM ' . self::table('credit_sale_items') . ' WHERE credit_sale_id = %d',
					(int) $sale->id
				)
			);
		}

		return $sales;
	}

	public static function update_credit_sale_payment( $id, $amount_paid ) {
		global $wpdb;

		$id          = (int) $id;
		$amount_paid = (float) $amount_paid;

		$sale = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT total_amount FROM ' . self::table('credit_sales') . ' WHERE id = %d LIMIT 1',
				$id
			)
		);

		if ( ! $sale ) {
			return false;
		}

		$balance = (float) $sale->total_amount - $amount_paid;

		return $wpdb->update(
			self::table('credit_sales'),
			[
				'amount_paid' => $amount_paid,
				'balance'     => $balance,
			],
			[ 'id' => $id ]
		);
	}

	// -------------------------------------------------------------------------
	// Product Summary
	// -------------------------------------------------------------------------

	public static function get_product_summary() {
		global $wpdb;

		return $wpdb->get_results(
			'SELECT p.id, p.name, p.price, p.type, c.name AS category_name,
				COALESCE(SUM(st.closing_stock), 0) AS current_stock,
				COALESCE(SUM(si.quantity), 0) AS total_sold
			FROM ' . self::table('products') . ' p
			LEFT JOIN ' . self::table('categories') . ' c ON c.id = p.category_id
			LEFT JOIN ' . self::table('stock') . ' st ON st.product_id = p.id
				AND st.date = (SELECT MAX(date) FROM ' . self::table('stock') . ' WHERE product_id = p.id)
			LEFT JOIN ' . self::table('sale_items') . ' si ON si.product_id = p.id
			GROUP BY p.id, p.name, p.price, p.type, c.name
			ORDER BY c.name, p.name ASC'
		);
	}

	// -------------------------------------------------------------------------
	// Financial Summary
	// -------------------------------------------------------------------------

	public static function get_financial_summary( $limit = 50, $offset = 0 ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT fs.*, u.full_name AS staff_name FROM ' . self::table('financial_summary') . ' fs
				LEFT JOIN ' . self::table('users') . ' u ON u.id = fs.staff_id
				ORDER BY fs.date DESC LIMIT %d OFFSET %d',
				(int) $limit,
				(int) $offset
			)
		);
	}

	public static function insert_financial_summary( $data ) {
		global $wpdb;

		$row = [
			'date'               => sanitize_text_field( $data['date'] ?? current_time('Y-m-d') ),
			'total_sales'        => (float) ( $data['total_sales'] ?? 0 ),
			'transfer_card_sales'=> (float) ( $data['transfer_card_sales'] ?? 0 ),
			'cash_sales'         => (float) ( $data['cash_sales'] ?? 0 ),
			'expense'            => (float) ( $data['expense'] ?? 0 ),
			'expense_remarks'    => sanitize_textarea_field( $data['expense_remarks'] ?? '' ),
			'old_cash'           => (float) ( $data['old_cash'] ?? 0 ),
			'cash_left'          => (float) ( $data['cash_left'] ?? 0 ),
			'discount'           => (float) ( $data['discount'] ?? 0 ),
			'debtors_transfer'   => (float) ( $data['debtors_transfer'] ?? 0 ),
			'debtors_cash'       => (float) ( $data['debtors_cash'] ?? 0 ),
			'staff_id'           => (int) ( $data['staff_id'] ?? 0 ),
		];

		// Upsert by date.
		$existing = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT id FROM ' . self::table('financial_summary') . ' WHERE date = %s LIMIT 1',
				$row['date']
			)
		);

		if ( $existing ) {
			$wpdb->update( self::table('financial_summary'), $row, [ 'date' => $row['date'] ] );
			return (int) $existing;
		}

		$wpdb->insert( self::table('financial_summary'), $row );
		return $wpdb->insert_id;
	}

	// -------------------------------------------------------------------------
	// Imports
	// -------------------------------------------------------------------------

	public static function insert_import( $data ) {
		global $wpdb;
		$wpdb->insert(
			self::table('imports'),
			[
				'product_id'   => (int) ( $data['product_id'] ?? 0 ),
				'product_name' => sanitize_text_field( $data['product_name'] ?? '' ),
				'quantity'     => (int) ( $data['quantity'] ?? 0 ),
				'staff_id'     => (int) ( $data['staff_id'] ?? 0 ),
			]
		);
		return $wpdb->insert_id;
	}

	public static function get_import_history( $limit = 50, $offset = 0 ) {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT i.*, u.full_name AS staff_name FROM ' . self::table('imports') . ' i
				LEFT JOIN ' . self::table('users') . ' u ON u.id = i.staff_id
				ORDER BY i.created_at DESC LIMIT %d OFFSET %d',
				(int) $limit,
				(int) $offset
			)
		);
	}

	// -------------------------------------------------------------------------
	// Stock
	// -------------------------------------------------------------------------

	public static function get_stock( $date = null ) {
		global $wpdb;

		if ( $date !== null ) {
			return $wpdb->get_results(
				$wpdb->prepare(
					'SELECT st.*, u.full_name AS staff_name FROM ' . self::table('stock') . ' st
					LEFT JOIN ' . self::table('users') . ' u ON u.id = st.staff_id
					WHERE st.date = %s ORDER BY st.product_name ASC',
					sanitize_text_field( $date )
				)
			);
		}

		return $wpdb->get_results(
			'SELECT st.*, u.full_name AS staff_name FROM ' . self::table('stock') . ' st
			LEFT JOIN ' . self::table('users') . ' u ON u.id = st.staff_id
			ORDER BY st.date DESC, st.product_name ASC'
		);
	}

	public static function update_stock( $product_id, $date, $data ) {
		global $wpdb;

		$product_id = (int) $product_id;
		$date       = sanitize_text_field( $date );

		$existing = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT id FROM ' . self::table('stock') . ' WHERE product_id = %d AND date = %s LIMIT 1',
				$product_id,
				$date
			)
		);

		$row = [
			'product_id'    => $product_id,
			'product_name'  => sanitize_text_field( $data['product_name'] ?? '' ),
			'opening_stock' => (int) ( $data['opening_stock'] ?? 0 ),
			'import_qty'    => (int) ( $data['import_qty'] ?? 0 ),
			'sold_stock'    => (int) ( $data['sold_stock'] ?? 0 ),
			'closing_stock' => (int) ( $data['closing_stock'] ?? 0 ),
			'date'          => $date,
			'staff_id'      => (int) ( $data['staff_id'] ?? 0 ),
		];

		if ( $existing ) {
			$wpdb->update( self::table('stock'), $row, [ 'product_id' => $product_id, 'date' => $date ] );
			return (int) $existing;
		}

		$wpdb->insert( self::table('stock'), $row );
		return $wpdb->insert_id;
	}

	// -------------------------------------------------------------------------
	// Sessions
	// -------------------------------------------------------------------------

	public static function create_session( $user_id, $token, $expires_at ) {
		global $wpdb;
		$wpdb->insert(
			self::table('sessions'),
			[
				'user_id'       => (int) $user_id,
				'session_token' => sanitize_text_field( $token ),
				'expires_at'    => sanitize_text_field( $expires_at ),
			]
		);
		return $wpdb->insert_id;
	}

	public static function get_session( $token ) {
		global $wpdb;
		return $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM ' . self::table('sessions') . '
				WHERE session_token = %s AND expires_at > %s LIMIT 1',
				sanitize_text_field( $token ),
				current_time( 'mysql' )
			)
		);
	}

	public static function delete_session( $token ) {
		global $wpdb;
		return $wpdb->delete( self::table('sessions'), [ 'session_token' => sanitize_text_field( $token ) ] );
	}

	public static function cleanup_sessions() {
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				'DELETE FROM ' . self::table('sessions') . ' WHERE expires_at <= %s',
				current_time( 'mysql' )
			)
		);
	}
}
