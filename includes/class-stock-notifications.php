<?php

defined( 'ABSPATH' ) || exit;

class Dokan_Stock_Notifications {
    // protected $vendors  = [];
    // protected $products = [];

    public function __construct() {
        // $this->set_vendors();
        $this->hooks();
    }

    /**
     * Init hooks
     *
     * @since DOKKAN_LITE_SINCE
     *
     * @return void
     */
    protected function hooks() {
        // error_log( var_export( 'load 3', true ) );
        // add_action( 'dokan_installed', [ $this, 'register_cron_jobs' ] );
        add_action( 'dokan_send_stock_notifications', [ $this, 'prepare_data_for_processing' ] );
    }

    // /**
    //  * Register jobs queues
    //  *
    //  * @since DOKAN_LITE_SINCE
    //  *
    //  * @return void
    //  */
    // public function register_cron_jobs() {
    //     error_log( var_export( 'load 4', true ) );
    //     if ( version_compare( WC_VERSION, '3.5', '<' ) && ! wp_next_scheduled( 'dokan_send_stock_notifications' ) ) {
    //         return wp_schedule_event( time(), 'daily', 'dokan_send_stock_notifications' );
    //     }

    //     error_log( var_export( 'load 5', true ) );

    //     WC()->queue()->schedule_recurring( time(), DAY_IN_SECONDS, 'dokan_send_stock_notifications' );
    // }

    // public function get_vendors() {
    //     return $this->vendors;
    // }

    // protected function set_vendors() {
    //     $this->vendors = dokan()->vendor->all();
    // }

    // public function prepare_data_for_processing() {
    //     $vendors = $this->get_vendors();
    // }

    /**
     * Prepare data for processing
     *
     * @since DOKAN_LITE_SINCE
     *
     * @return void
     */
    public function prepare_data_for_processing() {
        // error_log( var_export( 'prepare data for processing', true ) );
        $processor_file = DOKAN_INC_DIR . '/background-processes/class-dokan-stock-notifications-background-process.php';

        include_once $processor_file;

        $processor = new Dokan_Stock_Notifications_Background_Process;

        $args = [
            'numbers'    => 0,
            'processing' => 'send_stock_notifications'
        ];

        $processor->push_to_queue( $args )->dispatch_process( $processor_file );
    }
}