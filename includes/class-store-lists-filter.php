<?php

use Dokan\Traits\Singleton;

defined( 'ABSPATH' ) || exit;

/**
 * Store Lists Class
 *
 * @since  DOKAN_LITE_SINCE
 */
class Dokan_Store_Lists_Filter {
    use Singleton;

    /**
     * Boot method
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @return void
     */
    public function boot() {
        $this->hooks();
    }

    /**
     * Init hooks
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @return void
     */
    public function hooks() {
        add_action( 'dokan_store_lists_filter_form', [ $this, 'filter_area' ] );
    }

    public function filter_area( $stores ) {
        dokan_get_template_part( 'store-lists-filter', '', [
            'stores'          => $stores,
            'number_of_store' => $stores['count'],
            'sort_filters'    => $this->sort_by_options()
        ] );
    }

    public function sort_by_options() {
        return apply_filters( 'dokan_store_lists_sort_by_options', [
            'rating' => 'Store Rating',
            'price' => 'Product Price'
        ] );
    }
}