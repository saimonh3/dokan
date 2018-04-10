<?php
/**
* Shipping Tracking Class
* @since 2.8
*/

class Dokan_Shipping_Tracking {
    public static $instance = null;

    /**
    * Constructor
    */
    public function __construct() {
        $this->init_hooks();
    }

    public function init_hooks() {
        add_action( 'wp_ajax_handle_shipping_tracking', array( $this, 'handle_shipping_tracking' ) );
        add_action( 'woocommerce_order_details_after_order_table', array( $this, 'load_customer_shipping_tracking' ) );
    }

    public function handle_shipping_tracking () {
        if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'handle_shipping_tracking' ) {
            return;
        }

        if ( ! isset( $_POST['shipping_info']['security'] ) ||
            ! wp_verify_nonce( $_POST['shipping_info']['security'], 'add-shipping-tracking-info' ) ) {
            return;
        }

        $order_id           = wc_clean( $_POST['shipping_info']['post_id'] );
        $tracking_no        = wc_clean( $_POST['shipping_info']['tracking_no'] );
        $shipping_date      = wc_clean( $_POST['shipping_info']['shipping_date'] );
        $shipping_carrier   = wc_clean( $_POST['shipping_info']['shipping_carrier'] );
        $shipping_status    = wc_clean( $_POST['shipping_info']['shipping_status'] );

        update_post_meta( $order_id, 'tracking_no', $tracking_no );
        update_post_meta( $order_id, 'shipping_date', $shipping_date );
        update_post_meta( $order_id, 'shipping_carrier', $shipping_carrier );
        update_post_meta( $order_id, 'shipping_status', $shipping_status );

        do_action( 'dokan_handle_shipping_tracking', $order_id );
        // error_log( print_r( $_POST['action'], true ) );
    }

    /**
     * Load customer shipping tracking
     * @param  object order;
     *
     * @since 2.8.0
     *
     * @return [type]        [description]
     */
    public function load_customer_shipping_tracking( $order ) {

        if ( is_admin() ) {
            return;
        }

        // var_dump( $order->get_id() );
        $order_id           = $order->get_id();
        $tracking_no        = get_post_meta( $order_id, 'tracking_no', true );
        $shipping_carrier   = get_post_meta( $order_id, 'shipping_carrier', true );
        $shipping_time      = get_post_meta( $order_id, 'shipping_date', true );

        // return early if no shipping tracking info is available
        if ( empty( $tracking_no ) || empty( $shipping_carrier ) || empty( $shipping_time ) ) {
            // return;
        }

        // var_dump( $tracking_no, $shipping_carrier, $shipping_date );

        ?>
        <div class="customer-shipping-tracking">
            <h3> <?php _e( 'Shipping Tracking', 'dokan-lite' ) ?> </h3>
            <table>
                <tbody>
                    <tr>
                        <th><?php printf( '%s: %s', __( 'Tracking No', 'dokan-lite' ), $tracking_no ); ?></th>
                    </tr>
                    <tr>
                        <th><?php printf( '%s: %s', __( 'Shipping Carrier', 'dokan-lite' ), $shipping_carrier ); ?></th>
                    </tr>
                    <tr>
                        <th><?php printf( '%s: %s', __( 'Shipping Time', 'dokan-lite' ), $shipping_time ); ?></th>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php

    }

    public static function init() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new Self;
        }

        return self::$instance;
    }

    /**
    * Get bootstrap label class based on shipping status
    * @param string status
    * @since 2.8
    * @return string
    */
    function get_shipping_status_class( $status ) {
        switch ( $status ) {
            case 'delivered':
                return 'success';
            case 'on-hold':
                return 'warning';
            case 'processing':
                return 'wanring';
        }
        // $statuses = $this->get_shipping_statuses();
        //
        // return applay_filters( 'get_shipping_status_class', $statuses );

    }

    function get_shipping_statuses() {
        $statuses = array(
            'delivered'  => __( 'Delivered', 'dokan-lite' ),
            'on-hold'    => __( 'On-Hold', 'dokan-lite' ),
            'processing' => __( 'Processing', 'dokan-lite' ),
        );

        return apply_filters( 'dokan_get_shipping_statuses', $statuses );
    }

    function get_shipping_carriers() {
        $carriers = array(
            'dhl'   => __( 'DHL', 'dokan-lite' ),
            'ups'   => __( 'UPS', 'dokan-lite' ),
            'fedex' => __( 'FedEx', 'dokan-lite' ),
        );

        return apply_filters( 'dokan_get_shipping_carriers', $carriers );
    }
}
