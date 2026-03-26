<?php
/**
 * Plugin Name: Orion Brothers Inventory System
 * Plugin URI:  https://orionbrothers.com
 * Description: Comprehensive business inventory system for Orion Brothers
 * Version:     1.0.0
 * Author:      Orion Brothers
 * License:     GPL-2.0-or-later
 * Text Domain: orion-inventory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -------------------------------------------------------------------------
// Constants
// -------------------------------------------------------------------------

define( 'ORION_VERSION', '1.0.0' );
define( 'ORION_PATH', plugin_dir_path( __FILE__ ) );
define( 'ORION_URL',  plugin_dir_url( __FILE__ ) );

// -------------------------------------------------------------------------
// Include class & helper files
// -------------------------------------------------------------------------

require_once ORION_PATH . 'includes/class-db.php';
require_once ORION_PATH . 'includes/class-auth.php';
require_once ORION_PATH . 'includes/class-api.php';
require_once ORION_PATH . 'includes/helpers.php';

// Initialise the REST API handler.
new Orion_API();

// -------------------------------------------------------------------------
// Activation hook
// -------------------------------------------------------------------------

register_activation_hook( __FILE__, 'orion_activate' );

function orion_activate() {
	// 1. Create all database tables.
	Orion_DB::create_tables();

	// 2. Seed default super-admin user (only if not already present).
	// NOTE: The default password is intentionally set to 'admin123' as specified.
	// It should be changed immediately after the first login.
	if ( ! Orion_DB::get_user_by_username( 'admin' ) ) {
		Orion_DB::insert_user( [
			'username'  => 'admin',
			'password'  => 'admin123',
			'role'      => 'super_admin',
			'full_name' => 'Administrator',
		] );
	}

	// 3. Seed categories and products.
	orion_seed_categories_and_products();

	// 4. Register rewrite rules and flush.
	orion_add_rewrite_rules();
	flush_rewrite_rules();
}

// -------------------------------------------------------------------------
// Seeding helpers
// -------------------------------------------------------------------------

/**
 * Seeds the default product categories and products for sales and repairs.
 * Safe to call on repeated activations — skips existing categories by name+type.
 */
function orion_seed_categories_and_products() {
	$sales_data = [
		'Phones'       => [
			[ 'name' => 'iPhone 11 Pro Max 256GB',   'price' => 285000 ],
			[ 'name' => 'Samsung S22 Ultra 256GB',    'price' => 560000 ],
		],
		'Laptops'      => [
			[ 'name' => 'Lenovo Thinkpad 500GB SSD',  'price' => 300000 ],
		],
		'Chargers'     => [
			[ 'name' => 'Poga Elite 60WD PD',         'price' => 10000 ],
			[ 'name' => 'Oraimo Compact 2A',           'price' => 6000  ],
		],
		'Air pods'     => [
			[ 'name' => 'Redmi Buds 6 Play',          'price' => 20000 ],
			[ 'name' => 'Zealot L2 Buds',             'price' => 19000 ],
		],
		'Boom box'     => [
			[ 'name' => 'Zealot S-67',                'price' => 65000 ],
			[ 'name' => 'H@F Wireless Speaker Gu-133','price' => 26000 ],
		],
		'Headset'      => [
			[ 'name' => 'Soundcore Space 1',          'price' => 130000 ],
			[ 'name' => 'Poga HP-50',                 'price' => 25000  ],
		],
		'Chords'       => [
			[ 'name' => 'Oraimo Type C',              'price' => 2500 ],
			[ 'name' => 'Poga Type C',                'price' => 2500 ],
		],
		'Pouch'        => [
			[ 'name' => 'iPhone Pouch 13 Pro',              'price' => 3500 ],
			[ 'name' => 'Tecno Spark 40 Pro Silicone',       'price' => 3500 ],
		],
		'Screen guard' => [
			[ 'name' => '5D',           'price' => 2000 ],
			[ 'name' => '21D',          'price' => 1000 ],
			[ 'name' => 'Privacy',      'price' => 1500 ],
			[ 'name' => 'Full Glue',    'price' => 2000 ],
		],
		'Power bank'   => [
			[ 'name' => 'Poga 65000mAh F60',            'price' => 55000 ],
			[ 'name' => 'Oraimo 40000mAh OPB-7400Q',    'price' => 35000 ],
		],
	];

	$repairs_data = [
		'Screen'       => [
			[ 'name' => 'iPhone 12 Screen GX', 'price' => 80000 ],
			[ 'name' => 'iPhone 12 Screen JK', 'price' => 45000 ],
		],
		'Batteries'    => [
			[ 'name' => 'iPhone 12 Battery', 'price' => 20000 ],
			[ 'name' => 'Samsung S22',        'price' => 15000 ],
		],
		'Charging port'=> [
			[ 'name' => 'Type A', 'price' => 1500 ],
			[ 'name' => 'Type C', 'price' => 3000 ],
		],
	];

	orion_seed_type( 'sales',   $sales_data );
	orion_seed_type( 'repairs', $repairs_data );
}

/**
 * Seeds categories and products for a single type.
 *
 * @param string $type       'sales' or 'repairs'
 * @param array  $categories Associative array of category name → products array.
 */
function orion_seed_type( $type, $categories ) {
	global $wpdb;

	$cat_table = $wpdb->prefix . 'orion_categories';

	foreach ( $categories as $cat_name => $products ) {
		// Check if category already exists to avoid duplicates.
		$existing_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$cat_table} WHERE name = %s AND type = %s LIMIT 1",
				$cat_name,
				$type
			)
		);

		if ( $existing_id ) {
			$category_id = (int) $existing_id;
		} else {
			$category_id = Orion_DB::insert_category( [ 'name' => $cat_name, 'type' => $type ] );
		}

		foreach ( $products as $product ) {
			Orion_DB::insert_product( [
				'category_id' => $category_id,
				'name'        => $product['name'],
				'price'       => $product['price'],
				'type'        => $type,
			] );
		}
	}
}

// -------------------------------------------------------------------------
// Rewrite rules
// -------------------------------------------------------------------------

add_action( 'init', 'orion_add_rewrite_rules' );

function orion_add_rewrite_rules() {
	$slugs = [
		'login',
		'home',
		'sales',
		'repairs',
		'credit-sales',
		'product-summary',
		'financial-summary',
		'import',
		'stock',
		'admin-panel',
		'analytics',
		'profile',
		'sales-history',
		'repairs-history',
		'credit-history',
		'stock-history',
		'import-history',
		'financial-history',
	];

	foreach ( $slugs as $slug ) {
		add_rewrite_rule(
			'^orion/' . preg_quote( $slug, '#' ) . '/?$',
			'index.php?orion_page=' . $slug,
			'top'
		);
	}
}

// -------------------------------------------------------------------------
// Query vars
// -------------------------------------------------------------------------

add_filter( 'query_vars', 'orion_register_query_vars' );

function orion_register_query_vars( $vars ) {
	$vars[] = 'orion_page';
	return $vars;
}

// -------------------------------------------------------------------------
// Template router
// -------------------------------------------------------------------------

add_action( 'template_redirect', 'orion_template_router' );

function orion_template_router() {
	$page = get_query_var( 'orion_page' );

	if ( ! $page ) {
		return;
	}

	// All pages except login require authentication.
	if ( 'login' !== $page ) {
		Orion_Auth::require_login();
	}

	// Admin-only pages.
	$admin_pages = [ 'admin-panel', 'analytics' ];
	if ( in_array( $page, $admin_pages, true ) ) {
		Orion_Auth::require_admin();
	}

	// Template filename matches the URL slug with hyphens preserved.
	$template_slug = $page;
	$template_file = ORION_PATH . 'templates/' . $template_slug . '.php';

	// If a specific template exists, load it; otherwise fall back to a generic shell.
	if ( file_exists( $template_file ) ) {
		require $template_file;
	} else {
		// Generic fallback so missing templates don't cause a 404.
		require ORION_PATH . 'templates/shell.php';
	}

	exit;
}

// -------------------------------------------------------------------------
// Asset enqueue
// -------------------------------------------------------------------------

add_action( 'wp_enqueue_scripts', 'orion_enqueue_assets' );

function orion_enqueue_assets() {
	if ( ! get_query_var( 'orion_page' ) ) {
		return;
	}

	// Google Fonts – Inter
	wp_enqueue_style(
		'orion-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
		[],
		null
	);

	// Tailwind CSS (CDN)
	wp_enqueue_script(
		'orion-tailwind',
		'https://cdn.tailwindcss.com',
		[],
		null,
		false
	);

	// Iconify (CDN)
	wp_enqueue_script(
		'orion-iconify',
		'https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js',
		[],
		'2.1.0',
		true
	);

	// Plugin stylesheet
	if ( file_exists( ORION_PATH . 'assets/main.css' ) ) {
		wp_enqueue_style(
			'orion-main',
			ORION_URL . 'assets/main.css',
			[ 'orion-google-fonts' ],
			ORION_VERSION
		);
	}

	// Plugin JS app
	if ( file_exists( ORION_PATH . 'assets/app.js' ) ) {
		wp_enqueue_script(
			'orion-app',
			ORION_URL . 'assets/app.js',
			[ 'orion-tailwind', 'orion-iconify' ],
			ORION_VERSION,
			true
		);

		// Pass data to JavaScript.
		wp_localize_script(
			'orion-app',
			'OrionData',
			[
				'apiBase'  => esc_url( rest_url( 'orion/v1' ) ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'siteUrl'  => esc_url( home_url() ),
				'version'  => ORION_VERSION,
				'page'     => get_query_var( 'orion_page' ),
			]
		);
	}
}
