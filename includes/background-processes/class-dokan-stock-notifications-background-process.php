<?php

defined( 'ABSPATH' ) || exit;

/**
 * Send email in the background
 *
 * @since DOKAN_LITE_SINCE
 */
class Dokan_Stock_Notifications_Background_Process extends Abstract_Dokan_Background_Processes {

    /**
     * Process action id
     *
     * @since DOKAN_LITE_SINCE
     *
     * @var string
     */
    protected $action = 'Dokan_Stock_Notifications_Background_Process';

    /**
     * Perform each task
     *
     * @since DOKAN_LITE_SINCE
     *
     * @param array $item
     *
     * @return boolean
     */
    public function task( $item ) {
        if ( ! $item ) {
            return false;
        }

        if ( 'send_stock_notifications' === $item['processing'] ) {
            return $this->send_stock_notifications( $item['numbers'] );
        }

        return false;
    }

    /**
     * Send stock notifications
     *
     * @since DOKAN_LITE_SINCE
     *
     * @param $number
     *
     * @return array
     */
    public function send_stock_notifications( $number ) {
        // error_log( var_export( 'send stock notifications', true ) );
        $limit = 5;
        $offset = $limit * $number;

        $vendor_args = [
            'number' => $limit,
            'offset' => $offset
        ];

        $vendors = dokan()->vendor->all( $vendor_args );
        // error_log( var_export( ['total vendors', count( $vendors ) ], true ) );

        if ( empty( $vendors ) ) {
            return;
        }

        foreach ( $vendors as $vendor ) {

            if ( ! $vendor->is_out_of_stock_notifications_enabled() && ! $vendor->is_low_stock_notifications_enabled() ) {
                continue;
            }

            if ( $vendor->is_out_of_stock_notifications_enabled() ) {
                $out_of_stock_products = $vendor->get_out_of_stock_products();
            }

            if ( $vendor->is_low_stock_notifications_enabled() ) {
                $low_stock_products = $vendor->get_low_stock_products();
            }

            // call wc mail once
            wc()->mailer();

            do_action( 'send_out_of_notifications_email', $vendor, $out_of_stock_products, $low_stock_products );
        }

        return [
            'processing' => 'send_stock_notifications',
            'numbers'    => ++$number
        ];
    }
}