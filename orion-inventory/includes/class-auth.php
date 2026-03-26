<?php
/**
 * Authentication class for Orion Brothers Inventory System.
 * Uses a custom token-based session system stored in the database.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Orion_Auth {

	const COOKIE_NAME  = 'orion_session';
	const TOKEN_EXPIRY = 86400; // 24 hours in seconds

	/**
	 * Attempt to log a user in.
	 *
	 * @param string $username
	 * @param string $password Plain-text password.
	 * @return array { success: bool, token?: string, user?: object, message?: string }
	 */
	public static function login( $username, $password ) {
		$user = Orion_DB::get_user_by_username( $username );

		if ( ! $user ) {
			return [ 'success' => false, 'message' => 'Invalid credentials' ];
		}

		if ( ! wp_check_password( $password, $user->password_hash ) ) {
			return [ 'success' => false, 'message' => 'Invalid credentials' ];
		}

		$token      = self::generate_token();
		$expires_at = gmdate( 'Y-m-d H:i:s', time() + self::TOKEN_EXPIRY );

		Orion_DB::create_session( $user->id, $token, $expires_at );

		setcookie(
			self::COOKIE_NAME,
			$token,
			[
				'expires'  => time() + self::TOKEN_EXPIRY,
				'path'     => '/',
				'httponly' => true,
				'samesite' => 'Lax',
				'secure'   => is_ssl(),
			]
		);

		$user_data = [
			'id'          => $user->id,
			'username'    => $user->username,
			'role'        => $user->role,
			'full_name'   => $user->full_name,
			'email'       => $user->email,
			'phone'       => $user->phone,
			'profile_pic' => $user->profile_pic,
		];

		return [
			'success' => true,
			'token'   => $token,
			'user'    => $user_data,
		];
	}

	/**
	 * Log the current user out by deleting the session and clearing the cookie.
	 */
	public static function logout() {
		$token = self::get_token_from_request();

		if ( $token ) {
			Orion_DB::delete_session( $token );
		}

		// Clear cookie by setting an expired date.
		setcookie(
			self::COOKIE_NAME,
			'',
			[
				'expires'  => time() - YEAR_IN_SECONDS,
				'path'     => '/',
				'httponly' => true,
				'samesite' => 'Lax',
				'secure'   => is_ssl(),
			]
		);
	}

	/**
	 * Return the user data for the currently authenticated user, or null.
	 *
	 * @return object|null
	 */
	public static function get_current_user() {
		$token = self::get_token_from_request();

		if ( ! $token ) {
			return null;
		}

		$session = Orion_DB::get_session( $token );

		if ( ! $session ) {
			return null;
		}

		return Orion_DB::get_user_by_id( $session->user_id );
	}

	/**
	 * Returns true if a valid session token exists.
	 *
	 * @return bool
	 */
	public static function is_logged_in() {
		return self::get_current_user() !== null;
	}

	/**
	 * Redirect to login if the user is not authenticated.
	 */
	public static function require_login() {
		if ( ! self::is_logged_in() ) {
			wp_redirect( home_url( '/orion/login' ) );
			exit;
		}
	}

	/**
	 * Redirect to home if the user is not an admin or super_admin.
	 */
	public static function require_admin() {
		$user = self::get_current_user();

		if ( ! $user || ! in_array( $user->role, [ 'admin', 'super_admin' ], true ) ) {
			wp_redirect( home_url( '/orion/home' ) );
			exit;
		}
	}

	/**
	 * Generate a cryptographically secure random token.
	 *
	 * @return string 64-character hex string
	 */
	public static function generate_token() {
		return bin2hex( random_bytes( 32 ) );
	}

	// -------------------------------------------------------------------------
	// Private helpers
	// -------------------------------------------------------------------------

	/**
	 * Extract the session token from Authorization header or cookie.
	 *
	 * @return string|null
	 */
	private static function get_token_from_request() {
		// Authorization: Bearer <token>
		$auth_header = isset( $_SERVER['HTTP_AUTHORIZATION'] )
			? sanitize_text_field( wp_unslash( $_SERVER['HTTP_AUTHORIZATION'] ) )
			: '';

		if ( $auth_header && strpos( $auth_header, 'Bearer ' ) === 0 ) {
			return trim( substr( $auth_header, 7 ) );
		}

		// Fallback: cookie
		if ( isset( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			return sanitize_text_field( wp_unslash( $_COOKIE[ self::COOKIE_NAME ] ) );
		}

		return null;
	}
}
