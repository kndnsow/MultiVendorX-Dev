<?php

class MVX_Elementor_StoreData {

	protected $store_data = [];

	public function get_data( $prop = null ) {
		global $mvx_elementor;
		if ( $mvx_elementor->is_edit_or_preview_mode() ) {
			$data = $this->get_store_data_for_editing();
		} else {
			$data = $this->get_store_data();
		}

		return ( $prop && isset( $data[ $prop ] ) ) ? $data[ $prop ] : $data;
	}

	protected function get_store_data() {
		global $MVX;
		if ( ! empty( $this->store_data ) ) {
			return $this->store_data;
		}

		$this->store_data = apply_filters( 'mvx_elementor_store_data_defaults', [
				'id'              => 0,
				'banner'          => [
						'id'  => 0,
						'url' => $MVX->plugin_url . 'packages/mvx-elementor/assets/images/default-banner.jpg',
				],
				'name'            => '',
				'logo' => [
						'id'  => 0,
						'url' => $MVX->plugin_url . 'packages/mvx-elementor/assets/images/default-logo.png',
				],
				'address'         => '',
				'phone'           => '',
				'email'           => '',
				'rating'          => '',
				'store_description' => ''
		] );

		$store_id = mvx_find_shop_page_vendor();
		$store = get_mvx_vendor( $store_id );
		$vendor_hide_description = apply_filters('mvx_vendor_store_header_hide_description', get_user_meta($store_id, '_vendor_hide_description', true), $store_id);
		$vendor_hide_address = apply_filters('mvx_vendor_store_header_hide_store_address', get_user_meta($store_id, '_vendor_hide_address', true), $store_id);
		$vendor_hide_phone = apply_filters('mvx_vendor_store_header_hide_store_phone', get_user_meta($store_id, '_vendor_hide_phone', true), $store_id);
		$vendor_hide_email = apply_filters('mvx_vendor_store_header_hide_store_email', get_user_meta($store_id, '_vendor_hide_email', true), $store_id);
		if ( $store_id ) {
			$this->store_data['id'] = $store_id;

			$banner_id = get_user_meta( $store_id, '_vendor_banner', true );

			if ( $banner_id ) {
				$this->store_data['banner'] = [
                    'id'  => $banner_id,
                    'url' => $store->get_image('banner'),
                ];
			}

			$this->store_data['name'] = $store->page_title;
			if (!$vendor_hide_description && !is_null($store->description)) {
				$this->store_data['store_description'] = $store->description;
			}
			$image = $store->get_image() ? $store->get_image() : $MVX->plugin_url . 'assets/images/WP-stdavatar.png';
			$logo_id = get_user_meta( $store_id, '_vendor_image', true );
			if ( $logo_id ) {
				$this->store_data['logo'] = [
						'id'  => $logo_id,
						'url' => $image,
				];
			}

			$address = $store->get_formatted_address();
			if( $address ) {
				if ( !$vendor_hide_address && !empty( $address ) ) {
					$this->store_data['address'] = $address;
				}
			}

			$phone = $store->phone;

			if( $phone ) {
				if ( !$vendor_hide_phone && !empty( $phone ) ) {
					$this->store_data['phone'] = $phone;
				}
			}

			$email = $store->user_data->user_email;
			if( $email ) {
				if ( !$vendor_hide_email && !empty( $email ) ) {
					$this->store_data['email'] = $email;
				}
			}

            $vendor_term_id = get_user_meta( $store_id, '_vendor_term_id', true );

            $rating_val_array = mvx_get_vendor_review_info($vendor_term_id);

			$rating = $rating_val_array['avg_rating'];

			if ( ! empty( $rating ) ) {
				$this->store_data['rating'] = $rating;
			}

			$this->store_data = apply_filters( 'mvx_elementor_store_data', $this->store_data );
		}

		return $this->store_data;
	}

	/**
	 * Data for editing/previewing purpose
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_store_data_for_editing() {
		global $MVX;
		return apply_filters( 'mvx_elementor_store_data_defaults_for_editing', [
				'id'              => 0,
				'banner'          => [
						'id'  => 0,
						'url' => $MVX->plugin_url . 'packages/mvx-elementor/assets/images/default-banner.jpg',
				],
				'name'            => 'Store Name',
				'logo' => [
						'id'  => 0,
						'url' => $MVX->plugin_url . 'packages/mvx-elementor/assets/images/default-logo.png',
				],
				'address'         => 'Kolkata, India (IN)',
				'phone'           => '888-888-8888',
				'email'           => 'wcmarketplace@dualcube.com',
				'rating'          => '5 rating from 50 reviews',
				'store_description'          => 'Vendor store description',
		] );
	}
}
