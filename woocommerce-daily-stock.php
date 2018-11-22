<?php
/*
Plugin Name:  WooCommerce Daily Stock
Plugin URI:   http://digitaleel.com
Description:  Add daily stock option for products that have a stock available daily for rent.
Version:      1.0
Author:       Bob O'Brien
Author URI:   http://digitaleel.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  woocommerce-daily-stock
Domain Path:  /languages
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}
 
if( ! class_exists( 'WC_Daily_Stock' ) ) {
    class WC_Daily_Stock {
 
        public function __construct() {
            $this->init();
        }
 
        public function init() {
            add_action( 'woocommerce_product_options_inventory_product_data', array($this, 'add_daily_stock'));

            add_action( 'woocommerce_process_product_meta', array($this, 'save_fields', 10, 2 ) );
        }

        //create/turn on/off/save daily inventory for individual items in product bundles
        
        public function add_daily_stock(){

        echo '<div class="options_group">';
         
            woocommerce_wp_checkbox( array(
                'id'      => 'has_daily_stock',
                'value'   => get_post_meta( get_the_ID(), 'has_daily_stock', true ),
                'label'   => 'Track daily inventory',
                'desc_tip' => true,
                'description' => 'This is primarily for rentable products that need to have a certain inventory to rent on a daily basis. This must be checked in order for the stock below to be read.',
            ) );

            woocommerce_wp_text_input(
                array(
                    'id'        => 'daily_stock_quantity',
                    'value'     => get_post_meta( get_the_ID(), 'daily_stock_quantity', true ),
                    'label'     => 'Daily stock quantity',
                    'type'      => 'number',
                    'desc_tip' => true,
                    'description' => 'This is the maximum number of each of this product available to rent on a daily basis. If a number is entered that is lower than the maximum amount already booked on any given day, the new maximum will not save.',
                )
            );

            echo '</div>';
        }

        public function dei_stock_update_error(){
          echo'
            <div class="notice notice-error">
                <p>This item is already booked on certain dates for an amount greater than the daily stock you are trying to set.</p>
            </div>';
        }


        //create daily total inventory. can be changed in the product editor. that number will be checked against the booking orders, which create a tally of meta data based on order date. when those numbers subtraced equal 0 or less, an error is thrown. 
        
        public function save_fields( $id, $post ){

            if( !empty( $_POST['has_daily_stock'] ) ) {
                update_post_meta( $id, 'has_daily_stock', 'yes' );
                if (!empty( $_POST['daily_stock_quantity'])) {
                    $current_total_stock = get_post_meta($id, 'daily_stock_quantity', true);
                    global $wpdb;
                    $daily_stocks = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE 'stock_%' " );
                    foreach ($daily_stocks as $daily_stock) {
                        if ($_POST['daily_stock_quantity'] < $daily_stock->meta_value) {
                            //should return so it only adds the error once
                            //notice doesn't work but it does prevent saving
                            add_action( 'admin_notices', 'dei_stock_update_error' );
                            return;
                        } else{
                            update_post_meta( $id, 'daily_stock_quantity', $_POST['daily_stock_quantity'] );
                        }
                    }
                } 
            } else {
                update_post_meta( $id, 'has_daily_stock', 'no' );
            } 
        }
  
 
    }
}

$wc_daily_stock = new WC_Daily_Stock();