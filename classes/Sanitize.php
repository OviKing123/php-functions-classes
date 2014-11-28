<?php

class Sanitize {

	/**
	 * [force_array description]
	 * @param  [type] $array [description]
	 * @return [type]        [description]
	 */
	public static function force_array( $array ) {
		return self::_force_array( $array );
	}

	/**
	 * [force_string description]
	 * @param  [type] $string [description]
	 * @return [type]         [description]
	 */
	public static function force_string( $string ) {
		return self::_force_string( $string );
	}

	/**
	 * [post_isset description]
	 * @param  [type] $keys [description]
	 * @return [type]       [description]
	 */
	public static function post_isset( $keys ) {
		return self::_post_isset( $array );
	}

	/**
	 * [get_isset description]
	 * @param  [type] $keys [description]
	 * @return [type]       [description]
	 */
	public static function get_isset( $keys ) {
		return self::_get_isset( $keys );
	}

	/**
	 * [request_isset description]
	 * @param  [type] $keys [description]
	 * @return [type]       [description]
	 */
	public static function request_isset( $keys ) {
		return self::_request_isset( $keys );
	}

	/**
	 * [_force_array description]
	 * @param  [type] $array [description]
	 * @return [type]        [description]
	 */
	protected static function _force_array( $array ) {
		return is_array( $array ) ? ( $array ? $array : self::___array_values( $array ) ) : self::___force_array( $array );
	}

	/**
	 * [_force_string description]
	 * @param  [type] $string [description]
	 * @return [type]         [description]
	 */
	protected static function _force_string( $string ) {

		if ( is_string( $string ) ) {
			return $string;
		}

		if ( is_array( $string ) && ( $tmp = current( $string ) ) !== false ) {
			return (string) $tmp;
		}

		if ( ( $tmp = (string) $object ) && is_string( $tmp ) ) {
			return $tmp;
		}

		return '';

	}

	/**
	 * [_post_isset description]
	 * @param  [type] $keys [description]
	 * @return [type]       [description]
	 */
	protected static function _post_isset( $keys ) {

		$keys = self::force_array( $keys );

		foreach ( $keys as $key ) {
			if ( !isset( $_POST[$key] ) ) {
				return false;
			}
		}

		return true;

	}

	/**
	 * [get_isset description]
	 * @param  [type] $keys [description]
	 * @return [type]       [description]
	 */
	protected static function _get_isset( $keys ) {

		$keys = self::force_array( $keys );

		foreach ( $keys as $key ) {
			if ( !isset( $_GET[$key] ) ) {
				return false;
			}
		}

		return true;

	}

	/**
	 * [request_isset description]
	 * @param  [type] $keys [description]
	 * @return [type]       [description]
	 */
	protected static function _request_isset( $keys ) {

		$keys = self::force_array( $keys );

		foreach ( $keys as $key ) {
			if ( !isset( $_REQUEST[$key] ) ) {
				return false;
			}
		}

		return true;

	}

	/**
	 * [___array_values description]
	 * @param  [type] $array [description]
	 * @return [type]        [description]
	 */
	protected static function ___array_values( $array ) {

		if ( !is_array( $array ) ) {
			return array();
		}

		$values = array();

		foreach ( $array as $key => $value ) {
			if ( !is_string( $value ) ) {
				continue;
			}
			$values[] = $value;
		}

		return $values;

	}

	/**
	 * [___force_array description]
	 * @param  [type] $string [description]
	 * @return [type]         [description]
	 */
	protected static function ___force_array( $string ) {

		if ( !is_string( $string ) ) {
			return array();
		}

		$length = strlen( $string );

		if ( strpos( $string, "\x2c" ) !== false ) {
			return array_map( 'trim', explode( "\x2c", $string ) );
		}

		return array( $string );

	}

}

?>
