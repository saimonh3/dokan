<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package Dokan/Templates
 * @version DOKAN_LITE_SINCE
 */

defined( 'ABSPATH' ) || exit; ?>

<?php do_action( 'dokan_before_store_lists_filter', $stores ); ?>

<div id="dokan-store-listing-filter-wrap">
    <div class="left">
        <p class="item store-count">
            <?php printf( '%1$s: %2$d', __( 'Total store showing', 'dokan-lite' ), $number_of_store ); ?>
        </p>
    </div>

    <div class="right">
        <div class="item">
            <button class="dokan-store-list-filter-button dokan-btn dokan-btn-theme">
                <span class="dashicons dashicons-menu"></span>
                <?php esc_html_e( 'Filter', 'dokan-lite' ); ?>
            </button>
        </div>

        <div class="sort-by item">
            <label><?php esc_html_e( 'Sort by', 'dokan-lite' ); ?>:</label>

            <select name="store_sort_filter">
                <?php
                    foreach ( $sort_filters as $key => $filter ) {
                        $optoins = "<option value='${key}'>${filter}</option>";
                        printf( $optoins );
                    }
                ?>
            </select>
        </div>

        <div class="toggle-view item">
            <span class="dashicons dashicons-screenoptions" data-view="grid-view"></span>
            <span class="dashicons dashicons-menu-alt" data-view="list-view"></span>
        </div>
    </div>
</div>

<?php do_action( 'dokan_before_store_lists_filter_form', $stores ); ?>

<form id="dokan-store-listing-filter-form-wrap" style="display: none">
    <?php do_action( 'dokan_before_store_lists_filter_search', $stores ); ?>

    <div class="store-search grid-item">
        <input type="search" class="store-search-input" name="store-search" placeholder="<?php esc_html_e( 'Search Stores', 'dokan-lite' ); ?>">
    </div>

    <?php do_action( 'dokan_before_store_lists_filter_category', $stores ); ?>

    <div class="store-lists-other-filter-wrap">
        <div class="store-lists-category item">
            <div class="category-input">
                <span class="category-label">
                    <?php esc_html_e( 'Category:', 'dokan-lite' ); ?>
                </span>
                <span class="category-items">
                </span>
            </div>

            <div class="category-box" style="display: none">
                <ul>
                    <li>Electronics</li>
                    <li>House Hold</li>
                    <li>Computer</li>
                    <li>Digital</li>
                    <li>TV</li>
                    <li>Fridge</li>
                    <li>Others</li>
                </ul>
            </div>
        </div>

        <?php do_action( 'dokan_after_store_lists_filter_category', $stores ); ?>
    </div>

    <?php do_action( 'dokan_before_store_lists_filter_apply_button', $stores ); ?>

    <div class="apply-filter">
        <button id="apply-filter-btn" class="dokan-btn dokan-btn-theme" type="submit"><?php esc_html_e( 'Apply', 'dokan-lite' ); ?></button>
    </div>

</form>