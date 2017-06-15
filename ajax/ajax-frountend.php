<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_action( 'wp_ajax_HARIMAY_DD_relaod_the_product_on_DD', 'HARIMAY_DD_relaod_the_product_on_DD_callback' );
add_action( 'wp_ajax_nopriv_HARIMAY_DD_relaod_the_product_on_DD', 'HARIMAY_DD_relaod_the_product_on_DD_callback' );
function HARIMAY_DD_relaod_the_product_on_DD_callback(){
	
	$responces['html'] = HARIMAY_DD_daily_deals_callback_shortcode();

	$responces = json_encode($responces);
    die( $responces );
}