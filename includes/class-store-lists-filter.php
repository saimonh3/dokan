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

    private $query;
    private $orderby;

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
        add_filter( 'dokan_seller_listing_args', [ $this, 'filter_pre_user_query' ], 10, 2 );

        // remove these codes
        add_action( 'shutdown', function() {
            global $wpdb;

            // error_log( print_r( $wpdb->queries, true ) );

        } );
    }

    public function filter_area( $stores ) {
        dokan_get_template_part( 'store-lists-filter', '', [
            'stores'          => $stores,
            'number_of_store' => $stores['count'],
            'sort_filters'    => $this->sort_by_options()
        ] );
    }

    /**
     * Get sort by options
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @return array
     */
    public function sort_by_options() {
        return apply_filters( 'dokan_store_lists_sort_by_options', [
            'most_recent'   => __( 'Most Recent', 'dokan-lite' ),
            'total_orders'  => __( 'Total Orders', 'dokan-lite' ),
        ] );
    }

    public function filter_pre_user_query( $args, $request ) {
        if ( ! empty( $request['stores_orderby'] ) ) {
            $orderby = wc_clean( $request['stores_orderby'] );
            $args['orderby'] = $orderby;

            add_action( 'pre_user_query', array( $this, 'filter_user_query' ) );
        }

        return $args;
    }

    public function filter_user_query( $query ) {
        $this->query   = $query;
        $this->orderby = ! empty( $query->query_vars['orderby'] ) ? $query->query_vars['orderby'] : null;

        do_action( 'dokan_before_filter_user_query', $this->query, $this->orderby );

        $this->filter_query_from();
        $this->filter_query_orderby();
    }

    private function filter_query_from() {
        global $wpdb;

        if ( 'total_orders' === $this->orderby ) {
            $this->query->query_from .= " LEFT JOIN (
                                SELECT seller_id,
                                COUNT(*) AS orders_count
                                FROM {$wpdb->dokan_orders}
                                GROUP BY seller_id
                                ) as dokan_orders
                                ON ( {$wpdb->users}.ID = dokan_orders.seller_id )";
        }
    }

    private function filter_query_orderby() {
        if ( 'total_orders' === $this->orderby ) {
            $this->query->query_orderby = 'ORDER BY orders_count DESC';
        }

        if ( 'most_recent' === $this->orderby ) {
            $this->query->query_orderby = 'ORDER BY user_registered DESC';
        }
    }
}