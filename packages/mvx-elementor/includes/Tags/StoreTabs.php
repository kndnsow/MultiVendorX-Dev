<?php

use Elementor\Controls_Manager;

class StoreTabs extends MVX_Elementor_TagBase {

    /**
     * Tag name
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_name() {
        return 'mvx-store-tabs';
    }

    /**
     * Tag title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Tabs', 'multivendorx' );
    }

    /**
     * Render Tag
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function get_value() {
        global $MVX;
        $store_id = 0;

        if ( mvx_is_store_page() ) {
            $store = mvx_find_shop_page_vendor();

            if ( $store ) {
                $store_id = $store;
            }

           $store_tab_items = $MVX->frontend->mvx_get_store_tabs($store_id);
        } else {
            $store_tab_items = $this->get_store_tab_items();
        }
        
        $tab_items = [];

        foreach( $store_tab_items as $item_key => $item ) {
            $url = $item['url'];

            if ( empty( $url ) && ! $store_id ) {
                $url = '#';
            }

            $tab_items[] = [
                'key'         => $item['id'],
                'title'       => $item['title'],
                'text'        => $item['title'],
                'url'         => $url,
                'icon'        => '',
                'show'        => true,
                '__dynamic__' => [
                    'text' => $item['title'],
                    'url'  => $url,
                ]
            ];
        }
        /**
         * Filter to modify tag values
         *
         * @since 1.0.0
         *
         * @param array $tab_items
         */
        return apply_filters( 'mvx_elementor_tags_store_tab_items_value', $tab_items );
    }

    /**
     * Store tab items for Elementor Builder
     *
     * @since 2.9.14
     *
     * @return array
     */
    protected function get_store_tab_items() {
        return [
            [
                'id'              => 'products',
                'title' => __( 'Products', 'multivendorx' ),
                'show'        => true,
                'url'   => '#',
            ],
            [
                'id'              => 'policies',
                'title' => __( 'Policies', 'multivendorx' ),
                'show'        => true,
                'url'   => '#',
            ],
            [
                'id'              => 'reviews',
                'title' => __( 'Reviews', 'multivendorx' ),
                'show'        => true,
                'url'   => '#'
            ],
        ];
    }

    protected function render() {
        echo json_encode( $this->get_value() );
    }
}
