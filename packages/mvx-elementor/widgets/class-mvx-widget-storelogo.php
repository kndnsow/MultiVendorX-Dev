<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Image;

class MVX_Elementor_StoreLogo extends Widget_Image {

    use PositionControls;

    /**
     * Widget name
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_name() {
        return 'mvx-store-logo';
    }

    /**
     * Widget title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Logo', 'multivendorx' );
    }

    /**
     * Widget icon class
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-image';
    }

    /**
     * Widget categories
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_categories() {
        return [ 'mvx-store-elements-single' ];
    }

    /**
     * Widget keywords
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_keywords() {
        return [ 'mvx', 'store', 'vendor', 'profile', 'picture', 'image', 'avatar', 'logo' ];
    }

    /**
     * Register widget controls
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function _register_controls() {
    	global $mvx_elementor;
        parent::_register_controls();

        $this->update_control(
            'section_image',
            [
                'label' => __( 'Store Logo', 'multivendorx' ),
            ]
        );

        $this->update_control(
            'image',
            [
                'dynamic' => [
                    'default' => $mvx_elementor->mvx_elementor()->dynamic_tags->tag_data_to_tag_text( null, 'mvx-store-logo' ),
                ],
            ],
            [
                'recursive' => true,
            ]
        );
        
        $this->remove_control( 'caption_source' );
        $this->remove_control( 'caption' );

        $this->add_position_controls();
    }

    protected function get_html_wrapper_class() {
        return parent::get_html_wrapper_class() . ' elementor-widget-' . parent::get_name();
    }
}
