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

    /**
     * Init all the hooks
     *
     * @since 2.8.2
     *
     * @return void
     */
    public function init_hooks() {
        add_action( 'wp_ajax_handle_shipping_tracking', array( $this, 'handle_shipping_tracking' ) );

        // show shipping tracking data into customer order page
        add_action( 'woocommerce_order_details_after_order_table', array( $this, 'load_customer_shipping_tracking' ) );

        add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_shipping_tracking_column' ), 12 );
        add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_shipping_tracking_column_data' ), 12, 2 );

        // add metabox to admin order details page
        add_action( 'add_meta_boxes', array( $this, 'render_shipping_tracking_matabox' ), 31 );
        add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_shipping_tracking_metabox' ) );

        // add shipping tracking box in the seller dashboard
        add_action( 'dokan_order_detail_after_order_items', array( $this, 'render_shipping_tracking_form' ) );
    }

    /**
     * Save shipping tracking data
     *
     * @since 2.8.2
     *
     * @return void
     */
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
    }

    /**
     * Add shipping status column
     *
     * @param array columns;
     *
     * @since 2.8.2
     *
     * @return array column;
     */
    function add_shipping_tracking_column( $columns ) {
        $column = array();

        foreach( $columns as $key => $val ) {
            if ( 'order_status' == $key ) {
                $column[$key] = $val;
                $column['shipping_status'] = __( 'Shipping', 'dokan-lite' );
            } else {
                $column[$key] = $val;
            }
        }

        return $column;
    }

    /**
     * Render shipping tracking column
     *
     * @param $column
     *
     * @param $order_id
     *
     * @since 2.8.2
     *
     * @return mixed
     */
    function render_shipping_tracking_column_data( $column, $order_id ) {
        if ( $column !== 'shipping_status' ) {
            return $column;
        }

        if ( ! empty( get_post_meta( $order_id, 'shipping_status', true ) ) ) {
            $status = get_post_meta( $order_id, 'shipping_status', true );
            printf( '<mark class="order-status status-%s"> <span> %s </span> </mark>', $this->get_admin_shipping_status_class( $status ), $this->get_shipping_statuses()[$status] );
        } else {
            printf( '<mark class="order-status status-processing"> <span> Default Status </span> </mark>' );
        }
    }

    /**
     * Render shipping tracking metabox
     *
     * @since 2.8.2
     *
     * @return string
     */
    public function render_shipping_tracking_matabox() {
        add_meta_box(
            'woocommerce-order-shipping-tracking',
            __( 'Shipping Tracking', 'dokan-lite' ),
            array( $this, 'shipping_tracking_metabox' ),
            'shop_order',
            'side',
            'high'
        );
    }

    /**
     * Shipping Tracking Metabox
     *
     * @since 2.8.2
     *
     * @return string
     */
    public function shipping_tracking_metabox() {
        $shipping_carriers = $this->get_shipping_carriers();
        $shipping_statuses = $this->get_shipping_statuses();

        global $post;
        $order_id = $post->ID;

        $tracking_no        = ! empty( get_post_meta( $order_id, 'tracking_no', true ) ) ? get_post_meta( $order_id, 'tracking_no', true ) : '';
        $shipping_date      = ! empty( get_post_meta( $order_id, 'shipping_date', true ) ) ? get_post_meta( $order_id, 'shipping_date', true ) : '';
        $shipping_status    = ! empty( get_post_meta( $order_id, 'shipping_status', true ) ) ? get_post_meta( $order_id, 'shipping_status', true ) : '';
        $shipping_carrier   = ! empty( get_post_meta( $order_id, 'shipping_carrier', true ) ) ? get_post_meta( $order_id, 'shipping_carrier', true ) : '';

        ?>
        <div class="" id="actions">
            <p class="post-attributes-label-wrapper">
                <label class="post-attributes-label"><?php _e( 'Carrier', 'dokan-lite' ); ?></label>
            </p>
            <select class="regular-text" name="shipping_carriers" id="shipping-carrier">
                <?php foreach ( $shipping_carriers as $key  => $value ) : ?>
                    <option value="<?php echo $value ?>" <?php selected( $value, $shipping_carrier ); ?> > <?php echo $value ?> </option>
                <?php endforeach; ?>
            </select>
        </div>

        <p class="post-attributes-label-wrapper">
            <label class="post-attributes-label"><?php _e( 'Tracking No', 'dokan-lite' ); ?></label>
        </p>
        <input type="text" name="tracking_number" id="tracking-no" class="text" value="<?php echo esc_attr( $tracking_no ); ?>">

        <p class="post-attributes-label-wrapper">
            <label class="post-attributes-label"><?php _e( 'Date', 'dokan-lite' ); ?></label>
        </p>
        <input type="text" name="shipped_date" id="shipped-date" class="text" value="<?php echo esc_attr( $shipping_date ); ?>" placeholder="<?php _e( 'YYYY-MM-DD', 'dokan-lite' ); ?>">

        <div class="" id="actions">
            <p class="post-attributes-label-wrapper">
                <label class="post-attributes-label"><?php _e( 'Status', 'dokan-lite' ); ?></label>
            </p>
            <select id="shipping-status" class="regular-text" name="shipping_status">
                <?php foreach( $shipping_statuses as $key => $value ) : ?>
                    <option value="<?php echo $key ?>" <?php selected( $key, $shipping_status ) ?> "> <?php echo $value; ?> </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="security" id="security" value="<?php echo wp_create_nonce( 'add-shipping-tracking-info' ); ?>">
        </div>

        <p><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Update', 'dokan-lite' ) ?>"></p>

        <script type="text/javascript">
            $(function() {
                $('#shipped-date').datepicker({
                    dateFormat : 'yy-mm-dd'
                });
            });
        </script>

        <?php
    }

    /**
     * Save shipping tracking metabox
     *
     * @since 2.8.2
     *
     * @return void
     */
    public function save_shipping_tracking_metabox() {
        if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'add-shipping-tracking-info' ) ) {
            return;
        }

        $order_id           = wc_clean( $_POST['post_ID'] );
        $tracking_no        = wc_clean( $_POST['tracking_number'] );
        $shipping_date      = wc_clean( $_POST['shipped_date'] );
        $shipping_carrier   = wc_clean( $_POST['shipping_carriers'] );
        $shipping_status    = wc_clean( $_POST['shipping_status'] );

        update_post_meta( $order_id, 'tracking_no', $tracking_no );
        update_post_meta( $order_id, 'shipping_date', $shipping_date );
        update_post_meta( $order_id, 'shipping_carrier', $shipping_carrier );
        update_post_meta( $order_id, 'shipping_status', $shipping_status );

        do_action( 'dokan_handle_shipping_tracking', $order_id );
    }

    /**
     * Render shipping tracking form
     *
     * @param $order
     *
     * @since 2.8.2
     *
     * @return string
     */
    public function render_shipping_tracking_form( $order ) {
        $dst = new Dokan_Shipping_Tracking;
        $shipping_carriers = $dst->get_shipping_carriers();
        $shipping_statuses = $dst->get_shipping_statuses();

        $order_id           = $order->get_id();
        $tracking_no        = ! empty( get_post_meta( $order_id, 'tracking_no', true ) ) ? get_post_meta( $order_id, 'tracking_no', true ) : '';
        $shipping_date      = ! empty( get_post_meta( $order_id, 'shipping_date', true ) ) ? get_post_meta( $order_id, 'shipping_date', true ) : '';
        $shipping_status    = ! empty( get_post_meta( $order_id, 'shipping_status', true ) ) ? get_post_meta( $order_id, 'shipping_status', true ) : '';
        $shipping_carrier   = ! empty( get_post_meta( $order_id, 'shipping_carrier', true ) ) ? get_post_meta( $order_id, 'shipping_carrier', true ) : '';
        ?>
        <div class="dokan-shpping-tracking" style="width: 100%">
            <div class="dokan-panel dokan-panel-default">
                <div class="dokan-panel-heading"><strong><?php _e( 'Shipping Tracking', 'dokan-lite' ); ?></strong></div>
                <div class="dokan-panel-body">
                    <form id="add-shipping-tracking-form" method="post" class="" style="margin-top: 10px;">
                        <div class="dokan-form-group">
                            <label class="dokan-control-label"><?php _e( 'Carrier', 'dokan-lite' ); ?></label>
                            <select class="form-control" name="shipping_carriers" id="shipping-carrier">
                                <?php foreach ( $shipping_carriers as $key  => $value ) : ?>
                                    <option value="<?php echo $value ?>" <?php selected( $value, $shipping_carrier ); ?> > <?php echo $value ?> </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="dokan-form-group">
                            <label class="dokan-control-label"><?php _e( 'Tracking No', 'dokan-lite' ); ?></label>
                            <input type="text" name="tracking_number" id="tracking-no" class="dokan-form-control" value="<?php echo esc_attr( $tracking_no ); ?>">
                        </div>

                        <div class="dokan-form-group">
                            <label class="dokan-control-label"><?php _e( 'Date', 'dokan-lite' ); ?></label>
                            <input type="text" name="shipped_date" id="shipped-date" class="dokan-form-control" value="<?php echo esc_attr( $shipping_date ); ?>" placeholder="<?php _e( 'YYYY-MM-DD', 'dokan-lite' ); ?>">
                        </div>

                        <div class="dokan-form-group">
                            <label class="dokan-control-label"><?php _e( 'Status', 'dokan-lite' ); ?></label>
                            <select id="shipping-status" class="form-control" name="shipping_status">
                                <?php foreach( $shipping_statuses as $key => $value ) : ?>
                                    <option value="<?php echo $key ?>" <?php selected( $key, $shipping_status ) ?> "> <?php echo $value ?> </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <input type="hidden" name="security" id="security" value="<?php echo wp_create_nonce('add-shipping-tracking-info'); ?>">
                        <input type="hidden" name="post_id" id="post-id" value="<?php echo dokan_get_prop( $order, 'id' ); ?>">

                        <div class="dokan-form-group">
                            <button type="submit" id="add-shipping-tracking" class="btn btn-primary" name=""> <?php _e( 'Add Tracking Details', 'dokan-lite' ); ?> </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Load customer shipping tracking
     *
     * @param  object order;
     *
     * @since 2.8.2
     *
     * @return string
     */
    public function load_customer_shipping_tracking( $order ) {
        if ( is_admin() ) {
            return;
        }

        $order_id           = $order->get_id();
        $tracking_no        = get_post_meta( $order_id, 'tracking_no', true );
        $shipping_carrier   = get_post_meta( $order_id, 'shipping_carrier', true );
        $shipping_time      = get_post_meta( $order_id, 'shipping_date', true );
        $shipping_status    = get_post_meta( $order_id, 'shipping_status', true );

        // return early if no shipping tracking info is available
        if ( empty( $tracking_no ) || empty( $shipping_carrier ) || empty( $shipping_time ) ) {
            return;
        }

        ?>
        <div class="customer-shipping-tracking">
            <h2> <?php _e( 'Shipping Tracking', 'dokan-lite' ) ?> </h2>
            <table>
                <tbody>
                    <tr>
                        <th><?php printf( '%s: %s', __( 'Tracking No', 'dokan-lite' ), esc_attr( $tracking_no ) ); ?></th>
                    </tr>
                    <tr>
                        <th><?php printf( '%s: %s', __( 'Shipping Carrier', 'dokan-lite' ), esc_attr( $shipping_carrier ) ); ?></th>
                    </tr>
                    <tr>
                        <th><?php printf( '%s: %s', __( 'Shipping Time', 'dokan-lite' ), esc_attr( $shipping_time ) ); ?></th>
                    </tr>
                    <tr>
                        <th><?php printf( '%s: %s', __( 'Shipping Status', 'dokan-lite' ), esc_attr( $this->get_shipping_statuses()[$shipping_status] ) ); ?></th>
                    </tr>
                </tbody>
            </table>
        </div>

        <style type="text/css">
            .customer-shipping-tracking table th {
                padding: 8px;
                border: 2px dashed #80808085;
            }
        </style>
        <?php
    }

    /**
     * Get the class instance
     *
     * @since 2.8.2
     *
     * @return Dokan_Shipping_Tracking|null
     */
    public static function init() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new Dokan_Shipping_Tracking();
        }

        return self::$instance;
    }

    /**
    * Get bootstrap label class based on shipping status
     *
    * @param string status
     *
    * @since 2.8.2
     *
    * @return string
    */
    function get_shipping_status_class( $status ) {
        switch ( $status ) {
            case 'delivered':
                return 'success';
            case 'on-hold':
                return 'warning';
            case 'processing':
                return 'warning';
        }
    }

    /**
     * Get shipping status classes
     *
     * @param $status
     *
     * @since 2.8.2
     *
     * @return string
     */
    public function get_admin_shipping_status_class( $status ) {
        switch ( $status ) {
            case 'delivered':
                return 'completed';
            case 'on-hold':
                return 'on-hold';
            case 'processing':
                return 'processing';
        }
    }

    /**
     * Get shipping status
     *
     * @since 2.8.2
     *
     * @return string
     */
    function get_shipping_statuses() {
        $statuses = array(
            'delivered'  => __( 'Delivered', 'dokan-lite' ),
            'on-hold'    => __( 'On-Hold', 'dokan-lite' ),
            'processing' => __( 'Processing', 'dokan-lite' ),
        );

        return apply_filters( 'dokan_get_shipping_statuses', $statuses );
    }

    /**
     * Get shipping carriers
     *
     * @since 2.8.2
     *
     * @return mixed|void
     */
    function get_shipping_carriers() {
        $carriers = array(
            'dhl'   => __( 'DHL', 'dokan-lite' ),
            'ups'   => __( 'UPS', 'dokan-lite' ),
            'fedex' => __( 'FedEx', 'dokan-lite' ),
        );

        return apply_filters( 'dokan_get_shipping_carriers', $carriers );
    }
}
