<?php

use Elementor\Core\DynamicTags\Data_Tag;

abstract class MVX_Elementor_DataTagBase extends Data_Tag {

    public function get_group() {
        return 'mvx';
    }

    public function get_categories() {
        return [ \Elementor\Modules\DynamicTags\Module::BASE_GROUP ];
    }
}
