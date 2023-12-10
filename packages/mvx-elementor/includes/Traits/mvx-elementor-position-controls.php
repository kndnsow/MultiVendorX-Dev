<?php

use Elementor\Controls_Manager;

trait PositionControls {

	/**
	 * Add css position controls
	 *
	 * @return void
	 */
	protected function add_position_controls() {
			$this->start_injection( [
					'type' => 'section',
					'at'   => 'start',
					'of'   => '_section_style',
			] );

			$this->start_controls_section(
					'section_position',
					[
							'label' => __( 'Position', 'multivendorx' ),
							'tab'   => Controls_Manager::TAB_ADVANCED,
					]
			);

			$this->add_responsive_control(
					'_mvx_position',
					[
							'label'   => __( 'Position', 'multivendorx' ),
							'type'    => Controls_Manager::SELECT,
							'options' => [
									'static'   => __( 'Static', 'multivendorx' ),
									'relative' => __( 'Relative', 'multivendorx' ),
									'absolute' => __( 'Absolute', 'multivendorx' ),
									'sticky'   => __( 'Sticky', 'multivendorx' ),
									'fixed'    => __( 'Fixed', 'multivendorx' ),
							],
							'desktop_default' => 'relative',
							'tablet_default'  => 'relative',
							'mobile_default'  => 'relative',
							'selectors' => [
									'{{WRAPPER}}' => 'position: relative; min-height: 1px',
									'{{WRAPPER}} > .elementor-widget-container' => 'position: {{VALUE}};',
							],
					]
			);

			$this->add_responsive_control(
					'_mvx_position_top',
					[
							'label'     => __( 'Top', 'multivendorx' ),
							'type'      => Controls_Manager::TEXT,
							'default'   => '',
							'selectors' => [
									'{{WRAPPER}} > .elementor-widget-container' => 'top: {{VALUE}};',
							],
					]
			);

			$this->add_responsive_control(
					'_mvx_position_right',
					[
							'label'     => __( 'Right', 'multivendorx' ),
							'type'      => Controls_Manager::TEXT,
							'default'   => '',
							'selectors' => [
									'{{WRAPPER}} > .elementor-widget-container' => 'right: {{VALUE}};',
							],
					]
			);

			$this->add_responsive_control(
					'_mvx_position_bottom',
					[
							'label'     => __( 'Bottom', 'multivendorx' ),
							'type'      => Controls_Manager::TEXT,
							'default'   => '',
							'selectors' => [
									'{{WRAPPER}} > .elementor-widget-container' => 'bottom: {{VALUE}};',
							],
					]
			);

			$this->add_responsive_control(
					'_mvx_position_left',
					[
							'label'     => __( 'Left', 'multivendorx' ),
							'type'      => Controls_Manager::TEXT,
							'default'   => '',
							'selectors' => [
									'{{WRAPPER}} > .elementor-widget-container' => 'left: {{VALUE}};',
							],
					]
			);

			$this->end_controls_section();

			$this->end_injection();
	}
}
