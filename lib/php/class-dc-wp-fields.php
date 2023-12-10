<?php

if (!class_exists('MVX_WP_Fields')) {

    class MVX_WP_Fields {

        /**
         * Start up
         */
        public function __construct() {
            
        }

        /**
         * Output a hidden input box.
         *
         * @access public
         * @param array $field
         * @return void
         */
        function hidden_input($field) {

            $field['value'] = isset($field['value']) ? $field['value'] : '';
            $field['class'] = isset($field['class']) ? $field['class'] : 'hidden';
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];

            // Custom attribute handling
            $custom_attributes = array();

            if (!empty($field['custom_attributes']) && is_array($field['custom_attributes']))
                foreach ($field['custom_attributes'] as $attribute => $value)
                    $custom_attributes[] = 'data-' . esc_attr($attribute) . '="' . esc_attr($value) . '"';

            printf(
                    '<input type="hidden" id="%s" name="%s" class="%s" value="%s" %s />', esc_attr($field['id']), esc_attr($field['name']), esc_attr($field['class']), esc_attr($field['value']), implode(' ', $custom_attributes)
            );
        }

        /**
         * Output a Title
         *
         * @access public
         * @param array $field
         * @return void
         */
        function title_input($field) {
            $label = isset($field['label']) ? $field['label'] : '';
            $tag = isset($field['tag']) ? $field['tag'] : 'h3';
            echo "<".$tag.">" . $label . "</".$tag.">";
        }
        
        /**
         * Output a text input box.
         *
         * @access public
         * @param array $field
         * @return void
         */
        public function text_input($field) {
            $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
            $field['class'] = isset($field['class']) ? $field['class'] : 'regular-text';
            $field['dfvalue'] = isset($field['dfvalue']) ? $field['dfvalue'] : '';
            $field['value'] = isset($field['value']) ? $field['value'] : $field['dfvalue'];
            if (empty($field['value'])) {
                $field['value'] = $field['dfvalue'];
            }
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
            $field['type'] = isset($field['type']) ? $field['type'] : 'text';

            // Custom attribute handling
            $custom_attributes = array();

            if (!empty($field['custom_attributes']) && is_array($field['custom_attributes']))
                foreach ($field['custom_attributes'] as $attribute => $value)
                    $custom_attributes[] = 'data-' . esc_attr($attribute) . '="' . esc_attr($value) . '"';

            // attribute handling
            $attributes = array();

            if (!empty($field['attributes']) && is_array($field['attributes']))
                foreach ($field['attributes'] as $attribute => $value)
                    $attributes[] = esc_attr($attribute) . '="' . esc_attr($value) . '"';

            $field = $this->field_wrapper_start($field);

            printf(
                    '<input type="%s" id="%s" name="%s" class="%s" value="%s" placeholder="%s" %s %s />', $field['type'], esc_attr($field['id']), esc_attr($field['name']), esc_attr($field['class']), esc_attr($this->string_wpml('' . $field['value'] . '')), esc_attr($this->string_wpml('' . $field['placeholder'] . '')), implode(' ', $custom_attributes), implode(' ', $attributes)
            );
            $this->field_wrapper_end($field);
        }
        
        /**
         * Output a Label input box.
         *
         * @access public
         * @param array $field
         * @return void
         */
        public function label_input($field) {
            $field['class'] = isset($field['class']) ? $field['class'] : 'regular-text';
            $field['dfvalue'] = isset($field['dfvalue']) ? $field['dfvalue'] : '';
            $field['value'] = isset($field['value']) ? $field['value'] : $field['dfvalue'];
            if (empty($field['value'])) {
                $field['value'] = $field['dfvalue'];
            }
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
            $field['type'] = isset($field['type']) ? $field['type'] : 'label';

            $field = $this->field_wrapper_start($field);

            printf(
                    '<label id="%s" name="%s" class="%s" for="%s"> %s </label>', esc_attr($field['id']), esc_attr($field['name']), esc_attr($field['class']), esc_attr($field['name']), esc_attr($this->string_wpml('' . $field['value'] . ''))
            );
            $this->field_wrapper_end($field);
        }

        /**
         * Output a textarea input box.
         *
         * @access public
         * @param array $field
         * @return void
         */
        function textarea_input($field) {

            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
            $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
            $field['class'] = isset($field['class']) ? $field['class'] : 'textarea';
            $field['rows'] = isset($field['rows']) ? $field['rows'] : 2;
            $field['cols'] = isset($field['cols']) ? $field['cols'] : 20;
            $field['value'] = isset($field['value']) ? $field['value'] : '';
            $field['raw_value'] = isset($field['raw_value']) ? true : false;

            // Custom attribute handling
            $custom_attributes = array();

            if (!empty($field['custom_attributes']) && is_array($field['custom_attributes']))
                foreach ($field['custom_attributes'] as $attribute => $value)
                    $custom_attributes[] = 'data-' . esc_attr($attribute) . '="' . esc_attr($value) . '"';

            $field = $this->field_wrapper_start($field);
            /* For viewing raw data, specially for css & js */
            $value = ($field['raw_value']) ? $field['value'] : esc_textarea($this->string_wpml($field['value']));
                    
            printf(
                    '<textarea id="%s" name="%s" class="%s" placeholder="%s" rows="%s" cols="%s" %s>%s</textarea>', esc_attr($field['id']), esc_attr($field['name']), esc_attr($field['class']), esc_attr($field['placeholder']), absint($field['rows']), absint($field['cols']), implode(' ', $custom_attributes), $value
            );

            $this->field_wrapper_end($field);
        }

        /**
         * Output a wp editor box.
         *
         * @access public
         * @param array $field
         * @return void
         */
        function wpeditor_input($field) {

            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
            $field['rows'] = isset($field['rows']) ? $field['rows'] : 5;
            $field['cols'] = isset($field['cols']) ? $field['cols'] : 10;
            $field['value'] = isset($field['value']) ? $field['value'] : '';
            $field['settings'] = isset($field['settings']) ? $field['settings'] : array();
            $settings = array_merge(array('textarea_name' => $field['name'], 'textarea_rows' => $field['rows']), $field['settings']);

            $field = $this->field_wrapper_start($field);

            wp_editor(stripslashes($field['value']), $field['id'], $settings);

            $this->field_wrapper_end($field);
        }

        /**
         * Output a checkbox.
         *
         * @access public
         * @param array $field
         * @return void
         */
        public function checkbox_input($field) {
            $field['class'] = isset($field['class']) ? $field['class'] : 'checkbox';
            $field['value'] = isset($field['value']) ? $field['value'] : '';
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
            $field['dfvalue'] = isset($field['dfvalue']) ? $field['dfvalue'] : '';
            $field['text'] = isset($field['text']) ? $field['text'] : '';

            // Custom attribute handling
            $custom_attributes = array();
            $custom_tags = array();

            if (!empty($field['custom_attributes']) && is_array($field['custom_attributes']))
                foreach ($field['custom_attributes'] as $attribute => $value)
                    $custom_attributes[] = 'data-' . esc_attr($attribute) . '="' . esc_attr($value) . '"';

            if (!empty($field['custom_tags']) && is_array($field['custom_tags'])) {
                foreach ($field['custom_tags'] as $tag => $value) {
                    $custom_tags[] = esc_attr($tag) . '="' . esc_attr($value) . '"';
                }
            }
            $field = $this->field_wrapper_start($field);
            echo '<label>';
            printf(
                    '<input type="checkbox" id="%s" name="%s" class="%s" value="%s" %s %s %s />', esc_attr($field['id']), esc_attr($field['name']), esc_attr($field['class']), esc_attr($field['value']), checked($field['value'], $field['dfvalue'], false), implode(' ', $custom_attributes), implode(' ', $custom_tags)
            );
            echo $field['text'];
            echo '</label>';
            $this->field_wrapper_end($field);
        }

        /**
         * Output a radio gruop field.
         *
         * @access public
         * @param array $field
         * @return void
         */
        public function radio_input($field) {
            $field['class'] = isset($field['class']) ? $field['class'] : 'select short';
            $field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
            $field['value'] = isset($field['value']) ? $field['value'] : '';
            $field['dfvalue'] = isset($field['dfvalue']) ? $field['dfvalue'] : '';
            $field['value'] = ( $field['value'] ) ? $field['value'] : $field['dfvalue'];

            $options = '';
            foreach ($field['options'] as $key => $value) {
                $options .= '<label title="' . esc_attr($key) . '"><input class="' . esc_attr($field['class']) . '" type="radio" ' . checked(esc_attr($field['value']), esc_attr($key), false) . ' value="' . esc_attr($key) . '" name="' . esc_attr($field['name']) . '"> <span>' . esc_html($value) . '</span></label><br />';
            }

            $field = $this->field_wrapper_start($field);

            printf(
                    '
        <label id="%s" class="%s_field %s">
          <legend class="screen-reader-text"><span>%s</span></legend>
            %s
        </label>
        ', esc_attr($field['id']), esc_attr($field['id']), esc_attr($field['wrapper_class']), esc_attr($field['title']), $options
            );

            $this->field_wrapper_end($field);
        }

        /**
         * Output a radio gruop field.
         *
         * @access public
         * @param array $field
         * @return void
         */
        public function radio_select_input($field) {
            $field['class'] = isset($field['class']) ? $field['class'] : 'select short';
            $field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
            $field['value'] = isset($field['value']) ? $field['value'] : '';
            $field['dfvalue'] = isset($field['dfvalue']) ? $field['dfvalue'] : '';
            $field['value'] = ( $field['value'] ) ? $field['value'] : $field['dfvalue'];

            $options = '';
            if (isset($field['options']) && !empty($field['options'])) {
                foreach ($field['options'] as $key => $value) {
                    $options .= '<label title="' . esc_attr($key) . '" class="mvx_template_list"><input class="' . esc_attr($field['class']) . '" style="display:none;" type="radio" ' . checked(esc_attr($field['value']), esc_attr($key), false) . ' value="' . esc_attr($key) . '" name="' . esc_attr($field['name']) . '"><span class="dashicons dashicons-unlock"></span><img src="' . $value . '" /></label><br />';
                }
            }

            $field = $this->field_wrapper_start($field);

            printf('<label id="%s" class="%s_field %s"><legend class="screen-reader-text"><span>%s</span></legend>%s</label>', esc_attr($field['id']), esc_attr($field['id']), esc_attr($field['wrapper_class']), esc_attr($field['title']), $options);

            $this->field_wrapper_end($field);
        }

        public function color_scheme_picker_input($field) {
            $field['class'] = isset($field['class']) ? $field['class'] : 'select short';
            $field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
            $field['value'] = isset($field['value']) ? $field['value'] : '';
            $field['dfvalue'] = isset($field['dfvalue']) ? $field['dfvalue'] : '';
            $field['value'] = ( $field['value'] ) ? $field['value'] : $field['dfvalue'];

            $options = '';
            foreach ($field['options'] as $key => $value) {
                $selected = ( $field['value'] == $key ) ? 'selected' : '';
                $options .= '<div class="color-option ' . $selected . '">'
                        . '<label>'
                        . '<input id="admin_color_' . esc_attr($key) . '" class="' . esc_attr($field['class']) . '" type="radio" ' . checked(esc_attr($field['value']), esc_attr($key), false) . ' value="' . esc_attr($key) . '" name="' . esc_attr($field['name']) . '"> '
                        . '<label for="admin_color_' . esc_attr($key) . '">' . esc_html($value['label']) . '</label>'
                        . '<table class="color-palette">'
                        . '<tbody>'
                        . '<tr>';
                foreach ($value['color'] as $color) {
                    $options .= '<td style="background-color: ' . $color . '">&nbsp;</td>';
                }
                $options .= '</tr>'
                        . '</tbody>'
                        . '</table>'
                        . '</label>'
                        . '</div>';
            }

            $this->field_wrapper_start($field);

            printf(
                    '
        <div id="%s" class="%s_field %s">
          <legend class="screen-reader-text"><span>%s</span></legend>
            %s
        </div>
        ', esc_attr($field['id']), esc_attr($field['id']), esc_attr($field['wrapper_class']), esc_attr($field['title']), $options
            );

            $this->field_wrapper_end($field);
        }

        /**
         * Output a select input box.
         *
         * @access public
         * @param array $field
         * @return void
         */
        public function select_input($field) {
            $field['class'] = isset($field['class']) ? $field['class'] : 'select short';
            $field['value'] = isset($field['value']) ? $field['value'] : '';
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];

            // Custom attribute handling
            $custom_attributes = array();

            if (!empty($field['custom_attributes']) && is_array($field['custom_attributes']))
                foreach ($field['custom_attributes'] as $attribute => $value)
                    $custom_attributes[] = 'data-' . esc_attr($attribute) . '="' . esc_attr($value) . '"';

            // attribute handling
            $attributes = array();

            if (!empty($field['attributes']) && is_array($field['attributes']))
                foreach ($field['attributes'] as $attribute => $value)
                    $attributes[] = esc_attr($attribute) . '="' . esc_attr($value) . '"';

            $options = '';
            foreach ($field['options'] as $key => $value) {
                if (is_array($value)) {
                    $options .= '<optgroup label="' . $value['label'] . '">';
                    foreach ($value['options'] as $key1 => $value1) {
                        $options .= '<option value="' . esc_attr($key1) . '" ' . selected(esc_attr($field['value']), esc_attr($key1), false) . '>' . esc_html($this->string_wpml($value1)) . '</option>';
                    }
                    $options .= '</optgroup>';
                } else {
                    $options .= '<option value="' . esc_attr($key) . '" ' . selected(esc_attr($field['value']), esc_attr($key), false) . '>' . esc_html($this->string_wpml($value)) . '</option>';
                }
            }

            $field = $this->field_wrapper_start($field);

            printf(
                    '<select id="%s" name="%s" class="%s" %s %s>%s</select>', esc_attr($field['id']), esc_attr($field['name']), esc_attr($field['class']), implode(' ', $custom_attributes), implode(' ', $attributes), $options
            );

            $this->field_wrapper_end($field);
        }

        /**
         * Output a select input box.
         *
         * @access public
         * @param array $field
         * @return void
         */
        public function timezone_input($field) {
            $field['class'] = isset($field['class']) ? $field['class'] : 'select short';
            $field['value'] = isset($field['value']) ? $field['value'] : '';
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];

            // Custom attribute handling
            $custom_attributes = array();

            if (!empty($field['custom_attributes']) && is_array($field['custom_attributes']))
                foreach ($field['custom_attributes'] as $attribute => $value)
                    $custom_attributes[] = 'data-' . esc_attr($attribute) . '="' . esc_attr($value) . '"';

            $options = wp_timezone_choice($field['value']);

            $field = $this->field_wrapper_start($field);

            printf(
                    '<select id="%s" name="%s" class="%s" %s />%s</select>', esc_attr($field['id']), esc_attr($field['name']), esc_attr($field['class']), implode(' ', $custom_attributes), $options
            );

            $this->field_wrapper_end($field);
        }

        /**
         * Output a upload input box.
         *
         * @access public
         * @param array $field
         * @return void
         */
        public function upload_input($field) {
            $field['class'] = isset($field['class']) ? $field['class'] : 'upload_input';
            $field['mime'] = isset($field['mime']) ? $field['mime'] : 'image';
            $field['value'] = isset($field['value']) ? $field['value'] : '';
            $field['url'] = isset($field['url']) ? $field['url'] : get_url_from_upload_field_value($field['value']);
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
            $field['prwidth'] = isset($field['prwidth']) ? $field['prwidth'] : 75;
            $customStyle = isset($field['value']) ? 'display: none;' : '';
            $placeHolder = ( $field['value'] ) ? '' : 'placeHolder';
            if ($field['mime'] == 'image') {
                $mimeProp = '<img id="' . esc_attr($field['id']) . '_display" src="' . esc_attr($field['url']) . '" width="' . absint($field['prwidth']) . '" class="' . $placeHolder . '" alt="" />';
            } else {
                if ($field['value'])
                    $field['mime'] = pathinfo($field['value'], PATHINFO_EXTENSION);
                $placeHolder = 'placeHolder' . $field['mime'];
                $mimeProp = '<a target="_blank" style="width: ' . absint($field['prwidth']) . 'px; height: ' . absint($field['prwidth']) . 'px;" id="' . esc_attr($field['id']) . '_display" href="' . esc_attr($field['value']) . '"><span style="width: ' . absint($field['prwidth']) . 'px; height: ' . absint($field['prwidth']) . 'px; display: inline-block;" class="' . $placeHolder . '"></span></a>';
            }

            // Custom attribute handling
            $custom_attributes = array();

            if (!empty($field['custom_attributes']) && is_array($field['custom_attributes']))
                foreach ($field['custom_attributes'] as $attribute => $value)
                    $custom_attributes[] = 'data-' . esc_attr($attribute) . '="' . esc_attr($value) . '"';

            $field = $this->field_wrapper_start($field);

            printf(
                    '
        <span class="dc-wp-fields-uploader">
          %s
          <input type="text" name="%s" id="%s" style="%s" class="%s" readonly value="%s" %s data-mime="%s" />
          <input type="button" class="upload_button button button-secondary" name="%s_button" id="%s_button" data-mime="%s" value="Upload" />
          <input type="button" class="remove_button button button-secondary" name="%s_remove_button" id="%s_remove_button" data-mime="%s" value="Remove" />
        </span>
        ', $mimeProp, esc_attr($field['name']), esc_attr($field['id']), $customStyle, esc_attr($field['class']), esc_attr($field['value']), implode(' ', $custom_attributes), $field['mime'], esc_attr($field['id']), esc_attr($field['id']), $field['mime'], esc_attr($field['id']), esc_attr($field['id']), $field['mime']
            );

            $this->field_wrapper_end($field);
        }

        /**
         * Output a colorpicker box.
         *
         * @access public
         * @param array $field
         * @return void
         */
        public function colorpicker_input($field) {
            $field['class'] = isset($field['class']) ? $field['class'] : 'colorpicker';
            $field['default'] = isset($field['default']) ? $field['default'] : 'fbfbfb';
            $field['value'] = isset($field['value']) ? $field['value'] : $field['default'];
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];

            $field = $this->field_wrapper_start($field);

            printf(
                    '<input type="%s" id="%s" name="%s" class="%s" data-default="%s" value="%s" />', $field['type'], esc_attr($field['id']), esc_attr($field['name']), esc_attr($field['class']), esc_attr($field['default']), esc_attr($field['value'])
            );

            $this->field_wrapper_end($field);
        }

        /**
         * Output a date input box.
         *
         * @access public
         * @param array $field
         * @return void
         */
        public function datepicker_input($field) {
            $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
            $field['class'] = isset($field['class']) ? $field['class'] : 'regular-text';
            $field['value'] = isset($field['value']) ? $field['value'] : '';
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
            $field['type'] = isset($field['type']) ? $field['type'] : 'text';
            $field['class'] .= ' dc_datepicker';

            // Custom attribute handling
            $custom_attributes = array();

            if (!empty($field['custom_attributes']) && is_array($field['custom_attributes'])) {
                foreach ($field['custom_attributes'] as $attribute => $value)
                    $custom_attributes[] = 'data-' . esc_attr($attribute) . '="' . esc_attr($value) . '"';
            } else {
                $custom_attributes[] = 'data-date_format="dd/mm/yy"';
            }

            $field = $this->field_wrapper_start($field);

            printf(
                    '<input type="%s" id="%s" name="%s" class="%s" value="%s" placeholder="%s" %s />', $field['type'], esc_attr($field['id']), esc_attr($field['name']), esc_attr($field['class']), esc_attr($field['value']), esc_attr($field['placeholder']), implode(' ', $custom_attributes)
            );

            $this->field_wrapper_end($field);
        }

        /**
         * Output a multiinput box.
         *
         * @access public
         * @param array $field
         * @return void
         */
        public function multi_input($field) {
            $field['class']         = isset( $field['class'] ) ? $field['class'] : '';
            $field['value']         = isset( $field['value'] ) ? $field['value'] : array();
            $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
            $field['options']   = isset( $field['options'] ) ? $field['options'] : array();   
            $field['value']     = array_values($field['value']);    
            $field              = $this->multi_input_field_wrapper_start($field);
            $has_dummy          = isset( $field['has_dummy'] ) ? 1 : 0;

            // Custom attribute handling
            $custom_attributes = array();

            if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
                foreach ( $field['custom_attributes'] as $attribute => $value ) {
                    $custom_attributes[] = 'data-' . esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';

                    // Required Option
                    if( $attribute == 'required' ) { 
                        if( !isset( $field['custom_attributes']['required_message'] ) ) {
                            if( !isset( $field['label'] ) ) $field['label'] = str_replace( '_', ' ', ucfirst( $field['id'] ) );
                            $custom_attributes[] = 'data-required_message="' . esc_attr( $field['label'] ) . ': ' . __( 'This field is required.', 'multivendorx' ) . '"';
                        }
                        $field['label'] .= '<span class="required">*</span>';
                    }
                }
            }

            $eleCount = count($field['value']);
            if( !$eleCount ) $eleCount = 1;

            printf(
                '<div id="%s" class="%s multi_input_holder" data-name="%s" data-length="%s" data-has-dummy="%s" %s>',
                $field['id'],
                $field['class'],
                $field['name'],
                count($field['value']),
                $has_dummy,
                implode(' ', $custom_attributes)
            );

            $has_dummy_class = 'multi_input_block_dummy';
            if( !$has_dummy || count($field['value']) ) $has_dummy_class = '';

            if(!empty($field['options'])) {
                for($blockCount = 0; $blockCount < $eleCount; $blockCount++) {
                    $wrapper_class = '';
                    $wrapper_class = isset($field['value'][$blockCount]['wrapper_class']) ? $field['value'][$blockCount]['wrapper_class'] : '';
                    printf('<div class="multi_input_block ' . $wrapper_class . ' ' . $has_dummy_class . '">');
                    foreach($field['options'] as $optionKey => $optionField) {
                        $optionField = $this->check_field_id_name($optionKey, $optionField);
                        if($optionField['type'] == 'checkbox') {
                            if(isset($field['value']) && isset($field['value'][$blockCount]) && isset($field['value'][$blockCount][$optionField['name']])) $optionField['dfvalue'] = $field['value'][$blockCount][$optionField['name']];
                        } elseif($optionField['type'] == 'html') {

                        } else {
                            if(isset($field['value']) && isset($field['value'][$blockCount]) && isset($field['value'][$blockCount][$optionField['name']])) $optionField['value'] = $field['value'][$blockCount][$optionField['name']];
                        }
                        $option_values = array();
                        if($optionField['type'] == 'select') {
                            if(isset($field['value']) && isset($field['value'][$blockCount]) && isset($field['value'][$blockCount]['option_values'])) $optionField['options'] = $field['value'][$blockCount]['option_values'];
                        }
                        $optionField['custom_attributes']['name'] = $optionField['name'];
                        if(!isset($optionField['class'])) $optionField['class'] = '';
                        $optionField['class'] .= ' multi_input_block_element';
                        $optionField['id'] = $field['id'] . '_' . $optionField['name'] . '_' . $blockCount;
                        $optionField['name'] = $field['name'].'['.$blockCount.']['.$optionField['name'].']';
                        if(!empty($optionField['type'])) {
                            switch($optionField['type']) {
                                case 'input':
                                case 'text':
                                case 'email':
                                case 'number':
                                case 'numeric':
                                case 'time':
                                case 'file':
                                case 'url':
                                case 'phone':
                                case 'password':
                                case 'textfield':
                                    $this->multi_input_text_input($optionField);
                                break;

                                case 'hidden':
                                    $this->hidden_input($optionField);
                                break;

                                case 'textarea':
                                case 'wysiwyg':
                                    $this->textarea_input($optionField);
                                break;

                                case 'wpeditor':
                                    $this->wpeditor_input($optionField);
                                break;

                                case 'checkbox':
                                    $this->checkbox_input($optionField);
                                break;

                                case 'checklist':
                                    $this->checklist_input($field);
                                break;

                                case 'checkboxoffon':
                                    $this->checkbox_offon_input($optionField);
                                break;

                                case 'radio':
                                    $this->radio_input($optionField);
                                break;

                                case 'radiooffon':
                                    $this->radio_offon_input($optionField);
                                break;

                                case 'select':
                                    $this->multi_input_select_input($optionField);
                                break;

                                case 'country':
                                    $this->country_input($optionField);
                                break;

                                case 'timezone':
                                    $this->timezone_input($optionField);
                                break;

                                case 'upload':
                                    $this->upload_input($optionField);
                                break;

                                case 'file':
                                    $this->file_input($optionField);
                                break;

                                case 'colorpicker':
                                    $this->colorpicker_input($optionField);
                                break;

                                case 'datepicker':
                                case 'date':
                                    $this->datepicker_input($optionField);
                                break;

                                case 'multiinput':
                                    $this->multi_input($optionField);
                                break;

                                case 'title':
                                    $this->title_input($optionField);
                                break;

                                case 'html':
                                    $this->html_input($optionField);
                                break;

                                default:

                                break;

                            }
                        }
                    }
                    printf('<span class="multi_input_block_manupulate remove_multi_input_block button-secondary">-</span>
                        <span class="add_multi_input_block multi_input_block_manupulate button-primary">+</span></div>');
                }
            }

            printf('</div>');

            $this->multi_input_field_wrapper_end($field);
        }

        /*         * ************************************** Help Functions *********************************************** */

        public function multi_input_field_wrapper_start($field) {
            $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? ($field['wrapper_class'] . ' ' . $field['id'] . '_wrapper') : ($field['id'] . '_wrapper');
            $field['label_holder_class'] = isset( $field['label_holder_class'] ) ? ($field['label_holder_class']. ' ' . $field['id'] . '_label_holder') : ($field['id'] . '_label_holder');
            $field['label_for'] = isset( $field['label_for'] ) ? ($field['label_for']. ' ' . $field['id']) : $field['id'];
            $field['label_class'] = isset( $field['label_class'] ) ? ($field['label_for']. ' ' . $field['label_class']) : $field['label_for'];

            do_action('before_field_wrapper', $field);
            do_action('before_field_wrapper_' . $field['id']);

            if(isset($field['in_table'])) {
                printf(
                    '<tr class="%s">',
                    $field['wrapper_class']
                );
            }

            do_action('field_wrapper_start', $field);
            do_action('field_wrapper_start_' . $field['id'], $field);

            if(isset($field['label'])) {
                do_action('before_field_label');
                do_action('before_field_label_' . $field['id'], $field);

                if(isset($field['in_table'])) {
                    printf(
                        '<th class="%s">',
                        $field['label_holder_class']
                    );
                }
                do_action('field_label_start', $field);
                do_action('field_label_start_' . $field['id'], $field);
                printf(
                    '<p class="%s"><strong>%s</strong>',
                    $field['label_class'],
                    $this->mvx_removeslashes( $field['label'] )
                );
                if( isset( $field['hints'] ) && !empty( $field['hints'] ) ) {
                    printf(
                        '<span class="img_tip" data-desc="%s"></span>', wp_kses_post($field['hints'])
                    );
                }
                printf(
                    '</p><label class="screen-reader-text" for="%s">%s</label>',
                    $field['label_for'],
                    $this->mvx_removeslashes( $field['label'] )
                );

                // Description
                if( in_array( $field['type'], array( 'checklist', 'radio' ) ) ) {
                    if( isset( $field['desc'] ) && !empty( $field['desc'] ) ) {
                        do_action('before_desc', $field);
                        do_action('before_desc_' . $field['id'], $field);
                        if( !isset($field['desc_class']) ) $field['desc_class'] = '';

                        printf(
                            '<p class="description instructions %s">%s</p>',
                            wp_kses_post ( $field['desc_class'] ),
                            wp_kses_post ( $field['desc'] )
                        );

                        do_action('after_desc_' . $field['id'], $field);
                        do_action('after_desc', $field);
                    }
                }

                do_action('field_label_end_' . $field['id'], $field);
                do_action('field_label_end', $field);
                if(isset($field['in_table'])) printf('</th>');

                do_action('after_field_label_' . $field['id'], $field);
                do_action('after_field_label', $field);
            }

            do_action('before_field', $field);
            do_action('before_field_' . $field['id'], $field);

            if(isset($field['in_table']) && isset($field['label'])) printf('<td>');
            else if(isset($field['in_table']) && !isset($field['label'])) printf('<td colspan="2">');

            do_action('field_start', $field);
            do_action('field_start_' . $field['id'], $field);

            if(!isset($field['custom_attributes'])) $field['custom_attributes'] = array();
            $field['custom_attributes'] = apply_filters('manupulate_custom_attributes', $field['custom_attributes']);
            $field['custom_attributes'] = apply_filters('manupulate_custom_attributes_' . $field['id'], $field['custom_attributes']);

            return $field;
        }

        public function multi_input_field_wrapper_end($field) {
            // Help message
            if( !isset( $field['label'] ) && isset( $field['hints'] ) && !empty( $field['hints'] ) ) {
                do_action('before_hints', $field);
                do_action('before_hints_' . $field['id'], $field);

                printf(
                    '<span class="img_tip" data-desc="%s"></span>', wp_kses_post($field['hints'])
                );

                do_action('after_hints_' . $field['id'], $field);
                do_action('after_hints', $field);
            }

            // Description
            if( !in_array( $field['type'], array( 'checklist', 'radio' ) ) ) {
                if( isset( $field['desc'] ) && !empty( $field['desc'] ) ) {
                    do_action('before_desc', $field);
                    do_action('before_desc_' . $field['id'], $field);
                    if( !isset($field['desc_class']) ) $field['desc_class'] = '';

                    printf(
                        '<p class="description %s">%s</p>',
                        wp_kses_post ( $field['desc_class'] ),
                        wp_kses_post ( $field['desc'] )
                    );

                    do_action('after_desc_' . $field['id'], $field);
                    do_action('after_desc', $field);
                }
            }

            do_action('field_end_' . $field['id'], $field);
            do_action('field_end', $field);

            if(isset($field['in_table'])) printf('</td>');

            do_action('after_field_' . $field['id'], $field);
            do_action('after_field', $field);

            do_action('field_wrapper_end_' . $field['id'], $field);
            do_action('field_wrapper_end', $field);

            if(isset($field['in_table'])) printf('</tr>');

            do_action('afet_field_wrapper_' . $field['id'], $field);
            do_action('after_field_wrapper', $field);
        }

        public function field_wrapper_start($field) {
            $field['wrapper_class'] = isset($field['wrapper_class']) ? ($field['wrapper_class'] . ' ' . $field['id'] . '_wrapper') : ($field['id'] . '_wrapper');
            $field['label_holder_class'] = isset($field['label_holder_class']) ? ($field['label_holder_class'] . ' ' . $field['id'] . '_label_holder') : ($field['id'] . '_label_holder');
            $field['label_for'] = isset($field['label_for']) ? ($field['label_for'] . ' ' . $field['id']) : $field['id'];
            $field['label_class'] = isset($field['label_class']) ? ($field['label_for'] . ' ' . $field['label_class']) : $field['label_for'];
            if (!isset($field['in_table']))
            printf(
                '<fieldset class="%s">', $field['wrapper_class']
            );
            do_action('before_field_wrapper');
            do_action('before_field_wrapper_' . $field['id']);

            if (isset($field['in_table'])) {
                printf(
                        '<tr class="%s">', $field['wrapper_class']
                );
            }

            do_action('field_wrapper_start');
            do_action('field_wrapper_start_' . $field['id']);

            if (isset($field['label'])) {
                do_action('before_field_label');
                do_action('before_field_label_' . $field['id']);

                if (isset($field['in_table'])) {
                    printf(
                            '<th class="%s">', $field['label_holder_class']
                    );
                }
                do_action('field_label_start');
                do_action('field_label_start_' . $field['id']);
                printf(
                        '<p class="%s"><strong>%s</strong>', $field['label_class'], $field['label']
                );
                if (isset($field['hints'])) {
                    printf(
                            '<span class="img_tip" data-desc="%s"></span>', wp_kses_post($field['hints'])
                    );
                }
                printf(
                        '</p><label class="screen-reader-text" for="%s">%s</label>', $field['label_for'], $field['label']
                );
                do_action('field_label_end_' . $field['id']);
                do_action('field_label_end');
                if (isset($field['in_table']))
                    printf('</th>');

                do_action('after_field_label_' . $field['id']);
                do_action('after_field_label');
            }

            do_action('before_field');
            do_action('before_field_' . $field['id']);

            if (isset($field['in_table']) && isset($field['label']))
                printf('<td>');
            else if (isset($field['in_table']) && !isset($field['label']))
                printf('<td colspan="2">');

            do_action('field_start');
            do_action('field_start_' . $field['id']);

            if (!isset($field['custom_attributes']))
                $field['custom_attributes'] = array();
            $field['custom_attributes'] = apply_filters('manupulate_custom_attributes', $field['custom_attributes']);
            $field['custom_attributes'] = apply_filters('manupulate_custom_attributes_' . $field['id'], $field['custom_attributes']);
            // Help message
            echo '<label class="hint-container">';
            if (isset($field['hints'])) {
                do_action('before_hints');
                do_action('before_hints_' . $field['id']);
                if (apply_filters('mvx_img_tip_display_on_fileds_section', false)) {
                    printf(
                        '<span class="img_tip" data-desc="%s"></span>', wp_kses_post($field['hints'])
                    );
                }
                do_action('after_hints_' . $field['id']);
                do_action('after_hints');
            }
            echo '</label>';
            return $field;
        }

        public function field_wrapper_end($field) {

//            // Help message
//            if (!isset($field['label']) && isset($field['hints'])) {
//                do_action('before_hints');
//                do_action('before_hints_' . $field['id']);
//
//                printf(
//                        '<span class="img_tip" data-desc="%s"></span>', wp_kses_post($field['hints'])
//                );
//
//                do_action('after_hints_' . $field['id']);
//                do_action('after_hints');
//            }
            // Description
            if (isset($field['desc'])) {
                do_action('before_desc');
                do_action('before_desc_' . $field['id']);

                printf(
                        '<p class="description">%s</p>', wp_kses_post($field['desc'])
                );

                do_action('after_desc_' . $field['id']);
                do_action('after_desc');
            }

            do_action('field_end_' . $field['id']);
            do_action('field_end');

            if (isset($field['in_table']))
                printf('</td>');

            do_action('after_field_' . $field['id']);
            do_action('after_field');

            do_action('field_wrapper_end_' . $field['id']);
            do_action('field_wrapper_end');

            if (isset($field['in_table']))
                printf('</tr>');

            do_action('afet_field_wrapper_' . $field['id']);
            do_action('after_field_wrapper');
            if (!isset($field['in_table']))
                echo '</fieldset>';
        }

        public function mvx_removeslashes( $string ) {
            $string = implode("",explode("\\",$string));
            return stripslashes(trim($string));
        }

        public function multi_input_select_input($field) {
            $field['class'] = isset($field['class']) ? $field['class'] : 'select short';
            $field['value'] = isset($field['value']) ? $field['value'] : '';
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];

            // Custom attribute handling
            $custom_attributes = array();

            if (!empty($field['custom_attributes']) && is_array($field['custom_attributes']))
                foreach ($field['custom_attributes'] as $attribute => $value)
                    $custom_attributes[] = 'data-' . esc_attr($attribute) . '="' . esc_attr($value) . '"';

            // attribute handling
            $attributes = array();

            if (!empty($field['attributes']) && is_array($field['attributes']))
                foreach ($field['attributes'] as $attribute => $value)
                    $attributes[] = esc_attr($attribute) . '="' . esc_attr($value) . '"';

            $options = '';
            foreach ($field['options'] as $key => $value) {
                if (is_array($value)) {
                    $options .= '<optgroup label="' . $value['label'] . '">';
                    foreach ($value['options'] as $key1 => $value1) {
                        $options .= '<option value="' . esc_attr($key1) . '" ' . selected(esc_attr($field['value']), esc_attr($key1), false) . '>' . esc_html($this->string_wpml($value1)) . '</option>';
                    }
                    $options .= '</optgroup>';
                } else {
                    $options .= '<option value="' . esc_attr($key) . '" ' . selected(esc_attr($field['value']), esc_attr($key), false) . '>' . esc_html($this->string_wpml($value)) . '</option>';
                }
            }

            $field = $this->multi_input_field_wrapper_start($field);

            printf(
                    '<select id="%s" name="%s" class="%s" %s %s>%s</select>', esc_attr($field['id']), esc_attr($field['name']), esc_attr($field['class']), implode(' ', $custom_attributes), implode(' ', $attributes), $options
            );

            $this->multi_input_field_wrapper_end($field);
        }

        public function multi_input_text_input($field) {
            $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
            $field['class'] = isset($field['class']) ? $field['class'] : 'regular-text';
            $field['dfvalue'] = isset($field['dfvalue']) ? $field['dfvalue'] : '';
            $field['value'] = isset($field['value']) ? $field['value'] : $field['dfvalue'];
            if (empty($field['value'])) {
                $field['value'] = $field['dfvalue'];
            }
            $field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
            $field['type'] = isset($field['type']) ? $field['type'] : 'text';

            // Custom attribute handling
            $custom_attributes = array();

            if (!empty($field['custom_attributes']) && is_array($field['custom_attributes']))
                foreach ($field['custom_attributes'] as $attribute => $value)
                    $custom_attributes[] = 'data-' . esc_attr($attribute) . '="' . esc_attr($value) . '"';

            // attribute handling
            $attributes = array();

            if (!empty($field['attributes']) && is_array($field['attributes']))
                foreach ($field['attributes'] as $attribute => $value)
                    $attributes[] = esc_attr($attribute) . '="' . esc_attr($value) . '"';

            $field = $this->multi_input_field_wrapper_start($field);

            printf(
                    '<input type="%s" id="%s" name="%s" class="%s" value="%s" placeholder="%s" %s %s />', $field['type'], esc_attr($field['id']), esc_attr($field['name']), esc_attr($field['class']), esc_attr($this->string_wpml('' . $field['value'] . '')), esc_attr($this->string_wpml('' . $field['placeholder'] . '')), implode(' ', $custom_attributes), implode(' ', $attributes)
            );
            $this->multi_input_field_wrapper_end($field);
        }

        public function dc_generate_form_field($fields, $common_attrs = array()) {
            if (!empty($fields)) {
                foreach ($fields as $fieldID => $field) {
                    $field = $this->check_field_id_name($fieldID, $field);
                    if (!empty($common_attrs))
                        foreach ($common_attrs as $common_attr_key => $common_attr_val)
                            $field[$common_attr_key] = $common_attr_val;
                    if (!empty($field['type'])) {
                        switch ($field['type']) {
                            case 'input':
                            case 'text':
                            case 'email':
                            case 'number':
                            case 'file':
                            case 'password':
                            case 'button':
                            case 'url':
                                $this->text_input($field);
                                break;

                            case 'hidden':
                                $this->hidden_input($field);
                                break;

                            case 'textarea':
                                $this->textarea_input($field);
                                break;

                            case 'wpeditor':
                                $this->wpeditor_input($field);
                                break;

                            case 'checkbox':
                                $this->checkbox_input($field);
                                break;

                            case 'radio':
                                $this->radio_input($field);
                                break;

                            case 'select':
                                $this->select_input($field);
                                break;

                            case 'timezone':
                                $this->timezone_input($field);
                                break;

                            case 'upload':
                                $this->upload_input($field);
                                break;

                            case 'colorpicker':
                                $this->colorpicker_input($field);
                                break;

                            case 'datepicker':
                                $this->datepicker_input($field);
                                break;

                            case 'multiinput':
                                $this->multi_input($field);
                                break;

                            case 'title':
                                $this->title_input($field);
                                break;
                            
                            case 'label':
                                $this->label_input($field);
                                break;

                            default:

                                break;
                        }
                    }
                }
            }
        }

        public function check_field_id_name($fieldID, $field) {
            if (empty($fieldID))
                return $field;

            if (!isset($field['id']) || empty($field['id']))
                $field['id'] = $fieldID;
            if (!isset($field['name']) || empty($field['name']))
                $field['name'] = $fieldID;

            return $field;
        }

        public function string_wpml($input) {
            do_action( 'wpml_register_single_string', 'MVX', $input, $input );
            if (function_exists('icl_t')) {
                return icl_t('MVX', '' . $input . '', '' . $input . '');
            } else {
                return $input;
            }
        }

    }

}