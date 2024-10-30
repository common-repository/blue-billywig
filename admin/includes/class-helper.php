<?php

namespace BlueBillywigPlugin\Admin;

/**
 *  BlueBillywig Helper class
 */
class Helper {


	/**
	 *  Returns value in bytes
	 *
	 * @param int $val Initial value to transform to bytes.
	 *
	 * @return int
	 */
	public static function return_bytes_fn( $val ) {
		$val     = trim( $val );
		$last    = strtolower( $val[ strlen( $val ) - 1 ] );
		$new_val = mb_substr( $val, 0, -1 );
		switch ( $last ) {
			case 'g':
				$new_val *= 1024;
				break;
			case 'm':
				$new_val *= 1024;
				break;
			case 'k':
				$new_val *= 1024;
				break;
		}
		return $new_val;
	}
}
