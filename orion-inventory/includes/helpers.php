<?php
/**
 * Standalone helper functions for Orion Brothers Inventory System.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Formats a number as a Nigerian Naira amount.
 * e.g. 1000 → ₦1,000 | 34000 → ₦34,000 | 300000 → ₦300,000
 *
 * @param  int|float $amount
 * @return string
 */
function orion_format_naira( $amount ) {
	return '₦' . number_format( (float) $amount, 0, '.', ',' );
}

/**
 * Returns the initials of a full name (up to 2 characters).
 * e.g. "John Doe" → "JD" | "Alice" → "A"
 *
 * @param  string $name
 * @return string
 */
function orion_get_initials( $name ) {
	$name  = trim( (string) $name );
	$parts = preg_split( '/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY );

	if ( empty( $parts ) ) {
		return '';
	}

	$initials = strtoupper( substr( $parts[0], 0, 1 ) );

	if ( isset( $parts[1] ) ) {
		$initials .= strtoupper( substr( $parts[1], 0, 1 ) );
	}

	return $initials;
}

/**
 * Returns a human-readable "time ago" string for the given datetime.
 * e.g. "2 hours ago", "3 days ago", "just now"
 *
 * @param  string $datetime A MySQL datetime string or any strtotime()-compatible format.
 * @return string
 */
function orion_time_ago( $datetime ) {
	$timestamp = is_numeric( $datetime ) ? (int) $datetime : strtotime( (string) $datetime );

	if ( ! $timestamp ) {
		return 'unknown';
	}

	$diff = time() - $timestamp;

	if ( $diff < 60 ) {
		return 'just now';
	}

	if ( $diff < 3600 ) {
		$mins = (int) floor( $diff / 60 );
		return $mins . ' minute' . ( $mins !== 1 ? 's' : '' ) . ' ago';
	}

	if ( $diff < 86400 ) {
		$hours = (int) floor( $diff / 3600 );
		return $hours . ' hour' . ( $hours !== 1 ? 's' : '' ) . ' ago';
	}

	if ( $diff < 604800 ) {
		$days = (int) floor( $diff / 86400 );
		return $days . ' day' . ( $days !== 1 ? 's' : '' ) . ' ago';
	}

	if ( $diff < 2592000 ) {
		$weeks = (int) floor( $diff / 604800 );
		return $weeks . ' week' . ( $weeks !== 1 ? 's' : '' ) . ' ago';
	}

	if ( $diff < 31536000 ) {
		$months = (int) floor( $diff / 2592000 );
		return $months . ' month' . ( $months !== 1 ? 's' : '' ) . ' ago';
	}

	$years = (int) floor( $diff / 31536000 );
	return $years . ' year' . ( $years !== 1 ? 's' : '' ) . ' ago';
}

/**
 * Generates a unique receipt number in the format ORION-YYYYMMDD-XXXXX.
 * e.g. ORION-20240115-04821
 *
 * @return string
 */
function orion_generate_receipt_number() {
	global $wpdb;
	// Use an auto-increment counter stored in the DB to guarantee uniqueness.
	$table = $wpdb->prefix . 'orion_sales';
	$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
	return 'ORION-' . gmdate( 'Ymd' ) . '-' . str_pad( $count + 1, 5, '0', STR_PAD_LEFT );
}

/**
 * Normalizes a Nigerian phone number to the international format (e.g. +2348012345678).
 * Accepts formats: 08012345678, 8012345678, +2348012345678, 2348012345678.
 *
 * @param  string $phone
 * @return string Normalized number, or the original value if it cannot be parsed.
 */
function orion_sanitize_whatsapp( $phone ) {
	$phone = preg_replace( '/\D/', '', (string) $phone );

	if ( strlen( $phone ) === 10 && substr( $phone, 0, 1 ) !== '0' ) {
		// e.g. 8012345678 → +2348012345678
		return '+234' . $phone;
	}

	if ( strlen( $phone ) === 11 && substr( $phone, 0, 1 ) === '0' ) {
		// e.g. 08012345678 → +2348012345678
		return '+234' . substr( $phone, 1 );
	}

	if ( strlen( $phone ) === 13 && substr( $phone, 0, 3 ) === '234' ) {
		// e.g. 2348012345678 → +2348012345678
		return '+' . $phone;
	}

	if ( strlen( $phone ) === 14 && substr( $phone, 0, 4 ) === '2340' ) {
		// Edge case: user typed 23408012345678 - strip the extra 0.
		return '+234' . substr( $phone, 4 );
	}

	// Return as-is (already in correct format or unrecognised).
	return $phone;
}

/**
 * Returns a human-readable payment method label.
 *
 * @param  string $method  Stored payment method key.
 * @return string
 */
function orion_get_payment_label( $method ) {
	$labels = [
		'transfer'      => 'Transfer/Card',
		'transfer_card' => 'Transfer/Card',
		'card'          => 'Transfer/Card',
		'cash'          => 'Cash',
		'both'          => 'Both',
		'split'         => 'Both',
	];

	$key = strtolower( trim( (string) $method ) );
	return $labels[ $key ] ?? ucfirst( $key );
}

/**
 * Truncates a string to the given length, appending an ellipsis if trimmed.
 *
 * @param  string $str
 * @param  int    $length Maximum number of characters (default 30).
 * @return string
 */
function orion_truncate( $str, $length = 30 ) {
	$str = (string) $str;

	if ( mb_strlen( $str ) <= $length ) {
		return $str;
	}

	return mb_substr( $str, 0, $length ) . '…';
}
