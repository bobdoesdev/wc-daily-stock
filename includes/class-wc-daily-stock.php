<?php 

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}
 
if( ! class_exists( 'WC_Daily_Stock' ) ) {
    class WC_Daily_Stock {
 
        public function __construct() {
            add_action( 'woocommerce_product_options_inventory_product_data', array($this, 'add_daily_stock'));

            add_action( 'woocommerce_process_product_meta', array($this, 'save_fields') );
        }
 
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

        public function stock_update_error(){
          echo'
            <div class="notice notice-error">
                <p>This item is already booked on certain dates for an amount greater than the daily stock you are trying to set.</p>
            </div>';
        }

        public function save_fields( $id ){

            if( !empty( $_POST['has_daily_stock'] ) ) {
                update_post_meta( $id, 'has_daily_stock', 'yes' );
                if (!empty( $_POST['daily_stock_quantity'])) {
                    
                    global $wpdb;
                    $daily_stocks = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = $id AND meta_key LIKE 'stock_%' " );
                    $update = true;
                    foreach ($daily_stocks as $daily_stock) {
                        if (intval($_POST['daily_stock_quantity']) < intval($daily_stock->meta_value)) {
                            $update = false;
                        }
                    }

                    if ($update) {
                        update_post_meta( $id, 'daily_stock_quantity', $_POST['daily_stock_quantity'] );
                    }
                } 
            } else {
                update_post_meta( $id, 'has_daily_stock', 'no' );
            } 
        }
  
 
    }
}

$wc_daily_stock = new WC_Daily_Stock();
