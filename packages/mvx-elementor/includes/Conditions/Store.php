<?php

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class StoreCondition extends Condition_Base {

	/**
	 * Type of condition
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'mvx-store';
	}

	/**
	 * Condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'mvx-store';
	}

	/**
	 * Condition label
	 *
	 * @return string
	 */
	public function get_label() {
		return __( 'MVX Store Page', 'multivendorx' );
	}

	/**
	 * Condition label for all items
	 *
	 * @return string
	 */
	public function get_all_label() {
		return __( 'All Stores', 'multivendorx' );
	}

	/**
	 * Check if proper conditions are met
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public function check( $args ) {
		global $MVX;
		return mvx_is_store_page();
	}
}
