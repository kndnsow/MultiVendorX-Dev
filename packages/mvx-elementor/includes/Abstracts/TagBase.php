<?php

use Elementor\Core\DynamicTags\Tag;

abstract class MVX_Elementor_TagBase extends Tag {
    
	public function get_group() {
			return 'mvx';
	}

	public function get_categories() {
			return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
	}
}
