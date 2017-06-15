<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function HARIMAY_DD_wc_stock_amount( $amount, $product=false ) {
  if( empty( $product ) ){
    global $product;
  }
  $amount = apply_filters( 'HARIMAY_DD_woocommerce_stock_amount', $amount, $product );
  return $amount;
}



add_filter( 'woocommerce_variation_prices', 'HARIMAY_DD_woocommerce_variation_prices_callback', 12, 3 );
function HARIMAY_DD_woocommerce_variation_prices_callback( $prices_array, $product, $display ){
  $product_id = $product->id;
  $new_prices_array = $prices_array;
  if( $product_id && HARIMAY_DD_is_deal_active_or_not() ){
    //$get_the_ID = HARIMAY_DD_get_DD_product_id( 2 );
    $get_the_ID = HARIMAY_DD_get_product_id_for_transient_id();

    if( $product_id == $get_the_ID && !empty( $get_the_ID ) ){
      $HARIMAY_DD_daily_deals_price = get_post_meta( $get_the_ID, 'HARIMAY_DD_daily_deals_price', true );
      if( empty( $HARIMAY_DD_daily_deals_price ) ){
        $MM_DD_setting = get_option( 'MM_DD_setting' );
        $percentage_value = $MM_DD_setting['percentage_value'];
        if( empty( $percentage_value ) ){
          $percentage_value = 50;
        }
        if( !empty( $new_prices_array['price'] ) && is_array( $new_prices_array['price'] ) ){
          foreach( $new_prices_array['regular_price'] as $key => $price) {
            $percentage_rupess = HARIMAY_DD_round_up( ($percentage_value / 100) * $price, 2 );
            $new_price = $price - $percentage_rupess;
            $new_prices_array['price'][$key] = $new_price;
          }
        }elseif ( !empty( $new_prices_array['sale_price'] ) && is_array( $new_prices_array['sale_price'] ) ) {
          foreach( $new_prices_array['regular_price'] as $key => $price) {
            $percentage_rupess = HARIMAY_DD_round_up( ($percentage_value / 100) * $price, 2 );
            $new_price = $price - $percentage_rupess;
            $new_prices_array['sale_price'][$key] = $new_price;
          }
        }elseif ( !empty( $new_prices_array['regular_price'] ) && is_array( $new_prices_array['regular_price'] ) ) {
          foreach( $new_prices_array['regular_price'] as $key => $price) {
            $percentage_rupess = HARIMAY_DD_round_up( ($percentage_value / 100) * $price, 2 );
            $new_price = $price - $percentage_rupess;
            $new_prices_array['regular_price'][$key] = $new_price;
          }
        }
      }
    }
  }
  return $new_prices_array;
}


add_filter( 'woocommerce_get_price', 'HARIMAY_DD_woocommerce_get_price_callback', 1, 2 );
function HARIMAY_DD_woocommerce_get_price_callback( $price, $product ){
  $new_price = $price;
  $product_id = $product->id;
  if( $product_id && HARIMAY_DD_is_deal_active_or_not() ){
    // $get_the_ID = HARIMAY_DD_get_DD_product_id( 3 );
    $get_the_ID = HARIMAY_DD_get_product_id_for_transient_id();
    if( $product_id == $get_the_ID && !empty( $get_the_ID ) ){
      $HARIMAY_DD_daily_deals_price = get_post_meta( $get_the_ID, 'HARIMAY_DD_daily_deals_price', true );
      if( !empty( $HARIMAY_DD_daily_deals_price ) ){
        $new_price = $HARIMAY_DD_daily_deals_price;
      }else{
        if( $product->is_type( 'simple' ) ){
          $regular_price = get_post_meta( $get_the_ID, '_regular_price', true );
        }elseif ( !empty( $product->variation_id ) ) {
          $regular_price = get_post_meta( $product->variation_id, '_regular_price', true );
        }

        if( $regular_price != $price && $regular_price > 0 && !empty( $regular_price ) ){
          $price = $regular_price;
        }
        
        $MM_DD_setting = get_option( 'MM_DD_setting' );
        $percentage_value = $MM_DD_setting['percentage_value'];
        if( empty( $percentage_value ) ){
          $percentage_value = 50;
        }
        $percentage_rupess = HARIMAY_DD_round_up( ($percentage_value / 100) * $price, 2 );
        $new_price = $price - $percentage_rupess;
      }
    }
  }
  return $new_price;
}

function HARIMAY_DD_round_up ($value, $places=0) {
  if ($places < 0) { $places = 0; }
  $mult = pow(10, $places);
  return ceil($value * $mult) / $mult;
}



/*
* this is for variaction of the product
*/
add_filter( 'HARIMAY_DD_woocommerce_stock_amount', 'HARIMAY_DD_woocommerce_stock_amount_callback', 12, 2 );
function HARIMAY_DD_woocommerce_stock_amount_callback( $amount, $product ){
  if( empty( $product ) ){
    global $product;
  }
  $new_amount = (int)$amount;

  if( $product->id && HARIMAY_DD_is_deal_active_or_not() ){

    $get_the_ID = HARIMAY_DD_get_product_id_for_transient_id();
    if( $product->id == $get_the_ID && !empty( $get_the_ID ) ){
        
      $is_type_variable = 0;
      if( !empty( $product ) ){
        $is_type_variable = $product->is_type( 'variable' );
      }else{
        $product_type = HARIMAY_DD_get_product_type( $get_the_ID );
        if( $product_type->name == 'variable' ){
          $is_type_variable = 1;
        }
      }

      if( $is_type_variable ){
        $HARIMAY_DD_daily_deals_quantity = get_post_meta( $product->id, 'HARIMAY_DD_daily_deals_quantity', true );
        $HARIMAY_DD_daily_deals_quantity_less_stock = get_post_meta( $product->id, 'HARIMAY_DD_daily_deals_quantity_less_stock', true );
        
        if( $amount > $HARIMAY_DD_daily_deals_quantity_less_stock ){
          $new_amount = $amount - $HARIMAY_DD_daily_deals_quantity_less_stock;
        }
      }
    }
  }
  return $new_amount;
}



add_filter( 'woocommerce_get_stock_quantity', 'HARIMAY_DD_woocommerce_get_stock_quantity_callback', 12, 2 );
function HARIMAY_DD_woocommerce_get_stock_quantity_callback( $amount, $product ){
  if( empty( $product ) ){
    global $product;
  }
  
  $new_amount = (int)$amount;

  if( $product->id && HARIMAY_DD_is_deal_active_or_not() ){
    //$get_the_ID = HARIMAY_DD_get_DD_product_id( 4 );
    $get_the_ID = HARIMAY_DD_get_product_id_for_transient_id();
    $is_type_simple = 0;
    if( !empty( $product ) ){
      $is_type_simple = $product->is_type( 'simple' );
    }else{
      $product_type = HARIMAY_DD_get_product_type( $get_the_ID );
      if( $product_type->name == 'simple' ){
        $is_type_simple = 1;
      }
    }

    if( $product->id == $get_the_ID && !empty( $get_the_ID ) ){
      $HARIMAY_DD_daily_deals_quantity = get_post_meta( $product->id, 'HARIMAY_DD_daily_deals_quantity', true );
      $HARIMAY_DD_daily_deals_quantity_less_stock = get_post_meta( $product->id, 'HARIMAY_DD_daily_deals_quantity_less_stock', true );
      if( $amount > $HARIMAY_DD_daily_deals_quantity_less_stock ){
        $new_amount = $amount - $HARIMAY_DD_daily_deals_quantity_less_stock;
      }else{
        if( $is_type_simple ){
          $dd_stoack_over = get_post_meta( $product->id, 'dd_stoack_over', true );
          if( !$dd_stoack_over ){
            update_post_meta( $product->id, 'dd_stoack_over', 1 );
          }  
        }
        $new_amount = 0;
      }
    }
  }
  return $new_amount;
}