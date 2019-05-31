<?php

defined( 'ABSPATH' ) || exit;

class Dokan_Cron_Jobs {
    public function __construct() {
        $this->register_cron_jobs();
    }

    /**
     * Register jobs queues
     *
     * @since DOKAN_LITE_SINCE
     *
     * @return void
     */
    public function register_cron_jobs() {
        if ( version_compare( WC_VERSION, '3.5', '<' ) && ! wp_next_scheduled( 'dokan_send_stock_notifications' ) ) {
            return wp_schedule_event( time(), 'daily', 'dokan_send_stock_notifications' );
        }

        // error_log( var_export( 'register jobs', true ) );
        WC()->queue()->schedule_recurring( time(), DAY_IN_SECONDS, 'dokan_send_stock_notifications' );

        do_action( 'dokan_register_cron_jobs', $this );
    }
}