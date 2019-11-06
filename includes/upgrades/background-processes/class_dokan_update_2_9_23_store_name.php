<?php

defined( 'ABSPATH' ) || exit;

/**
 * Dokan store name updater class
 *
 * @since DOKAN_LITE_SINCE
 */
class Dokan_Update_2_9_23_Store_Name extends Abstract_Dokan_Background_Processes {

    /**
     * Action
     *
     * @since DOKAN_LITE_SINCE
     *
     * @var string
     */
    protected $action = 'dokan_update_2_9_23_store_name';

    /**
     * Perform updates
     *
     * @since DOKAN_LITE_SINCE
     *
     * @param mixed $item
     *
     * @return mixed
     */
    public function task( $item ) {
        if ( empty( $item ) ) {
            return false;
        }

        if ( 'store_name' === $item['updating'] ) {
            return $this->update_store_name( $item['paged'] );
        }

        return false;
    }

    /**
     * Update store settings
     *
     * @since DOKAN_LITE_SINCE
     *
     * @return void
     */
    private function update_store_name( $paged ) {
        $limit = 50;
        $count = $limit * $paged;

        $query_args = [
            'status' => '',
            'number' => $limit,
            'offset' => $count
        ];

        $vendors = dokan()->vendor->all( $query_args );

        if ( ! $vendors ) {
            return;
        }

        foreach ( $vendors as $vendor ) {
            if ( $vendor->get_meta( 'dokan_store_name' ) ) {
                continue;
            }

            $vendor->set_shop_name( $vendor->get_shop_name() );
            $vendor->save();
        }

        return array(
            'updating' => 'store_name',
            'paged'    => ++$paged
        );
    }
}