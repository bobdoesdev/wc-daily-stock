<?php
/*
Plugin Name:  WooCommerce Daily Stock
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


if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option('active_plugins') ) ) ){

    require_once plugin_dir_path(__FILE__) . 'includes/class-wc-daily-stock.php';

}

