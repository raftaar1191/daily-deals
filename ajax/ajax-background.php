<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;




add_action( 'wp_ajax_HARIMAY_DD_on_click_save_deatils_in_db', 'HARIMAY_DD_on_click_save_deatils_in_db_callback' );
function HARIMAY_DD_on_click_save_deatils_in_db_callback(){

	$HARIMAY_DD_get_product_id_for_transient_id = $_REQUEST['HARIMAY_DD_get_product_id_for_transient_id'];
	$HARIMAY_DD_daily_deals_price = $_REQUEST['HARIMAY_DD_daily_deals_price'];
	$HARIMAY_DD_daily_deals_quantity = $_REQUEST['HARIMAY_DD_daily_deals_quantity'];
	$HARIMAY_DD_daily_deals_order = $_REQUEST['HARIMAY_DD_daily_deals_order'];
	$post_id = $_REQUEST['post_id'];
	if( isset( $HARIMAY_DD_daily_deals_price ) && isset( $HARIMAY_DD_daily_deals_quantity ) && !empty( $HARIMAY_DD_daily_deals_quantity ) && isset( $HARIMAY_DD_daily_deals_order ) && !empty( $HARIMAY_DD_daily_deals_order ) && isset( $post_id ) && !empty( $post_id ) ){
		
		if( $HARIMAY_DD_get_product_id_for_transient_id == "true" ){
			$HARIMAY_DD_get_product_id_for_transient_id = "yes";
		}else{
			$HARIMAY_DD_get_product_id_for_transient_id = "";
		}
		
		update_post_meta( $post_id, 'HARIMAY_DD_get_product_id_for_transient_id', ( isset( $HARIMAY_DD_get_product_id_for_transient_id ) && $HARIMAY_DD_get_product_id_for_transient_id ) ? 'yes' : 'no' );

        update_post_meta( $post_id, 'HARIMAY_DD_daily_deals_price', $HARIMAY_DD_daily_deals_price );

        $HARIMAY_DD_daily_deals_quantity = $HARIMAY_DD_daily_deals_quantity;
        if( empty( $HARIMAY_DD_daily_deals_quantity ) ){
            $HARIMAY_DD_daily_deals_quantity = 100;
        }
        update_post_meta( $post_id, 'HARIMAY_DD_daily_deals_quantity', $HARIMAY_DD_daily_deals_quantity );

        $HARIMAY_DD_daily_deals_order = $HARIMAY_DD_daily_deals_order;
        if( empty( $HARIMAY_DD_daily_deals_order ) ){
            $HARIMAY_DD_daily_deals_order = 100;
        }

        update_post_meta( $post_id, 'HARIMAY_DD_daily_deals_order', $HARIMAY_DD_daily_deals_order );

        $get_the_ID = HARIMAY_DD_get_product_id_for_transient_id();
        if( $post_id == $get_the_ID && $_REQUEST['HARIMAY_DD_get_product_id_for_transient_id'] == false ){
        	$HARIMAY_DD_product_args_active = get_option( 'HARIMAY_DD_product_args_active', 1 );
        	if( !empty( $HARIMAY_DD_product_args_active ) && $HARIMAY_DD_product_args_active > 1 ){
        		$HARIMAY_DD_product_args_active = $HARIMAY_DD_product_args_active - 1;
        		update_option( 'HARIMAY_DD_product_args_active', $HARIMAY_DD_product_args_active );
        	}
        	HARIMAY_DD_to_delect_all_DD_transient( 3 );
        }

        $stock = get_post_meta( $post_id, '_stock', true );

        HARIMAY_DD_update_stock_less_for_day_deals( $stock, $HARIMAY_DD_daily_deals_quantity, $post_id );

        HARIMAY_DD_upate_the_stock_of_product( $stock, $HARIMAY_DD_daily_deals_quantity, $post_id );

        if ( function_exists( 'HARIMAY_DD_delect_unreview_transent' ) ) {
            HARIMAY_DD_delect_unreview_transent();
        }

		$responces['success'] = true;
	}else{
		$responces['error'] = "error";
	}
	$responces = json_encode($responces);
    die( $responces );	
}


add_action( 'wp_ajax_HARIMAY_DD_on_changing_possion_of_deals_ajax', 'HARIMAY_DD_on_changing_possion_of_deals_ajax_callback' );
function HARIMAY_DD_on_changing_possion_of_deals_ajax_callback(){
	$post_ids = $_REQUEST['post_ids'];
	if( !empty( $post_ids ) ){
		$post_ids_array = explode( ',', $post_ids );
		$counter = 1;
		foreach ($post_ids_array as $post_id) {
			update_post_meta( $post_id, 'HARIMAY_DD_daily_deals_order', $counter );
			$counter++;
			$responces['success'] .= ",".$post_id;
		}
	}else{
		$responces['error'] = "error";
	}
	$responces = json_encode($responces);
    die( $responces );	
}