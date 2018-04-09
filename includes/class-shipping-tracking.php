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
