<?php

abstract class MVX_Elementor_ModuleBase {

	/**
	 * Runs after first instance
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );
	}

	/**
	 * Module name
	 *
	 * @return void
	 */
	abstract public function get_name();

	/**
	 * Module widgets
	 *
	 * @return array
	 */
	public function get_widgets() {
		return [];
	}

	/**
	 * Register module widgets
	 *
	 * @return void
	 */
	public function init_widgets() {
		global $mvx_elementor;

		if ( version_compare( '3.5.0', ELEMENTOR_VERSION, '<' ) ) {
			$widget_manager = $mvx_elementor->mvx_elementor()->widgets_manager;
		}

		foreach ( $this->get_widgets() as $widget ) {
			$this->load_class( $widget );
			
			$class_name = "MVX_Elementor_{$widget}";

			if ( class_exists( $class_name ) ) {
				$widget_manager->register( new $class_name() );
			}
		}
	}
	
	public function load_class($class_name = '') {
		global $MVX;
		if ('' != $class_name && '' != $MVX->token) {
			require_once ( $MVX->plugin_path .  'packages/mvx-elementor/widgets/class-' . esc_attr($MVX->token) . '-widget-' . strtolower(esc_attr($class_name)) . '.php');
		} // End If Statement
	}
}
