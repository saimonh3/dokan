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
