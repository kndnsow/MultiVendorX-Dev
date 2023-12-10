<?php

use Elementor\Control_Repeater;

class MVX_Elementor_SortableList extends Control_Repeater {

	/**
	 * Control type
	 *
	 * @var string
	 */
	const CONTROL_TYPE = 'sortable_list';

	/**
	 * Get repeater control type.
	 *
	 * @return string
	 */
	public function get_type() {
		return self::CONTROL_TYPE;
	}

	/**
	 * Get repeater control default settings.
	 *
	 * @return array
	 */
	protected function get_default_settings() {
		return [
				'fields'        => [],
				'title_field'   => '',
				'prevent_empty' => true,
				'is_repeater'   => true,
				'item_actions'  => [
						'sort' => true,
				],
		];
	}

	/**
	 * Render repeater control output in the editor.
	 *
	 * @return void
	 */
	public function content_template() {
		?>
		<label>
				<span class="elementor-control-title">{{{ data.label }}}</span>
		</label>
		<div class="elementor-repeater-fields-wrapper"></div>
		<?php
	}

	/**
	 * Enqueue control scripts
	 *
	 * @return void
	 */
	public function enqueue() {
		global $MVX;
		
		wp_enqueue_style(
				'mvx-control-sortable-list',
				$MVX->plugin_url . 'packages/mvx-elementor/assets/css/mvx-elementor-control-sortable-list.css',
				[],
				$MVX->version
		);

		wp_enqueue_script(
				'mvx-control-sortable-list',
				$MVX->plugin_url . 'packages/mvx-elementor/assets/js/mvx-elementor-control-sortable-list.js',
				[ 'elementor-editor' ],
				$MVX->version,
				true
		);
	}
}
