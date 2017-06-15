<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


function HARIMAY_DD_return_the_args_of_product( $HARIMAY_DD_product_args_active= false ){
    if( ! $HARIMAY_DD_product_args_active ){
      $HARIMAY_DD_product_args_active = get_option( 'HARIMAY_DD_product_args_active', 1 );
    }
    
    $product_args_per_page = get_option( 'product_args_per_page', 1 );

    $dd_product_args = array(
          'post_type'             => 'product',
          'meta_query'            => array(
                                      array(
                                          'key'     => 'dd_stoack_over',
                                          'value'   => 0,
                                          'type'    => 'numeric',
                                          'compare' => '=',
                                      ),
                                      array(
                                          'key'     => 'HARIMAY_DD_get_product_id_for_transient_id',
                                          'value'   => 'yes',
                                          'compare' => 'LIKE',
                                      ),
                                      array(
                                          'key'     => 'HARIMAY_DD_daily_deals_quantity',
                                          'value'   => 0,
                                          'type'    => 'numeric',
                                          'compare' => '>',
                                      ),
                                  ),
          'posts_per_page'        => $product_args_per_page,
          'paged'                 => $HARIMAY_DD_product_args_active,
          'orderby'               => 'meta_value_num title',
          'order'                 => 'ASC',
          'meta_key'              => 'HARIMAY_DD_daily_deals_order',
      );
    return $dd_product_args;
}



function HARIMAY_DD_to_delect_all_DD_transient( $from_where=false ){

    if( HARIMAY_DD_PLUGIN_DEBUG )
      wp_mail( HARIMAY_DD_PLUGIN_EMMAIL, 'Delete Transient', "from where the transient is delected: ".$from_where." time: ".current_time( 'Y-m-d H:i:s' ) );
    
    delete_option( 'HARIMAY_DD_get_product_id_and_time' );
}







function HARIMAY_DD_get_DD_product_id( $where=false, $from_where=false, $for_transent=false ){

  $get_the_ID = HARIMAY_DD_get_product_id_for_transient_id( $for_transent );

  if ( empty( $get_the_ID ) ) {
    /* put here the function that will return the post if the active deals */
    $product_args = HARIMAY_DD_get_product_args_for_dd( $where, $from_where );
    
    $get_the_ID = $product_args['product_id'];

  }
  return $get_the_ID;
}



function HARIMAY_DD_get_product_id_for_transient_id( $for_transent=false ){
    return HARIMAY_DD_to_check_in_cron_if_value_exit_or_not( $for_transent );
}




function HARIMAY_DD_upate_the_stock_of_product( $stock, $HARIMAY_DD_daily_deals_quantity, $post_id ){
    if( $stock >= $HARIMAY_DD_daily_deals_quantity ){
        update_post_meta( $post_id, 'dd_stoack_over', 0 );
    }else{
        update_post_meta( $post_id, 'dd_stoack_over', 1 );
    }
}


function HARIMAY_DD_get_product_type( $post_id ){
    $post_terms =  wp_get_post_terms( $post_id, 'product_type' );
    return $post_terms[0];
}



function HARIMAY_DD_update_stock_less_for_day_deals( $stock, $HARIMAY_DD_daily_deals_quantity, $post_id ){
    if( $stock >= $HARIMAY_DD_daily_deals_quantity ){
      $less_form_stock = $stock - $HARIMAY_DD_daily_deals_quantity;
      update_post_meta( $post_id, 'HARIMAY_DD_daily_deals_quantity_less_stock', (int)$less_form_stock );
    }
}



function HARIMAY_DD_is_deal_active_or_not(){
    $MM_DD_setting = get_option( 'MM_DD_setting' );
    
    $time_period_start_date_text = $MM_DD_setting['time_period_start_date_text'];
    $time_period_start_time_text = $MM_DD_setting['time_period_start_time_text'];
    $time_period_interval_text = $MM_DD_setting['time_period_interval_text'];

    $current_time = current_time( 'Y-m-d H:i:s' );
    $db_current_time = $time_period_start_date_text." ".$time_period_start_time_text.":00:00";

    $current_time_strtotime = strtotime( $current_time );
    $db_current_time_strtotime = strtotime( $db_current_time );

    if( $current_time_strtotime > $db_current_time_strtotime && !empty( $time_period_start_date_text ) && !empty( $time_period_interval_text ) ){
      return 1;  
    }else{
      return 0;
    }
}





function HARIMAY_DD_get_product_args_for_dd( $where=false, $from_where=false ){

    $dd_product_args = HARIMAY_DD_return_the_args_of_product();  

    $the_query =  new WP_Query( $dd_product_args );

    /* this part is related how much time is to save in transinet */
    $MM_DD_setting = get_option( 'MM_DD_setting' );
    $time_period_interval_text = $MM_DD_setting['time_period_interval_text'];
    if( empty( $time_period_interval_text ) ){
      $time_period_interval_text = 24;
    }

    $hours_time = $time_period_interval_text * HOUR_IN_SECONDS;

    $current_time = current_time( 'Y-m-d H:i:s' );

    $current_time_strtotime = strtotime( $current_time );

    $next_time_main_strtotime = $current_time_strtotime + $hours_time;


    $next_time_f = date('Y-m-d H',$next_time_main_strtotime);
    
    $next_time_f = $next_time_f.":00:00"; 

    $next_time_f_strtotime = strtotime( $next_time_f );

    if($next_time_f_strtotime > $next_time_main_strtotime){
      $diff = $next_time_f_strtotime - $next_time_main_strtotime;
      $new_time = $hours_time + $diff;
      $from_which_condition = 1;
    }else{
      $diff = $next_time_main_strtotime - $next_time_f_strtotime;
      $new_time = $hours_time - $diff;
      $from_which_condition = 2;
    }

    $HARIMAY_DD_product_args_active = get_option( 'HARIMAY_DD_product_args_active', 1 );

    if( empty( $from_where ) )
      $from_where = 1;


    HARIMAY_DD_to_delect_all_DD_transient( $from_where );


    $HARIMAY_DD_get_product_id_and_time = array();
    $HARIMAY_DD_get_product_id_and_time['post_id'] = $the_query->post->ID;
    $HARIMAY_DD_get_product_id_and_time['end_time'] = $next_time_f_strtotime;
    $HARIMAY_DD_get_product_id_and_time['query'] = $the_query;

    update_option( 'HARIMAY_DD_get_product_id_and_time', $HARIMAY_DD_get_product_id_and_time );


    $product_transient_timeout = get_option( '_transient_timeout_HARIMAY_get_product_id_new', 1 );

    set_transient( 
      'HARIMAY_DD_get_product_stock_over_new_extra', 
      "where->".$where."HARIMAY_DD_product_args_active->".$HARIMAY_DD_product_args_active."post_name->".$the_query->post->ID."new_time->".$new_time."curretn_time->".$current_time."curretn_time in strtotime->". $current_time_strtotime ."next time->".$next_time_f."next time in strtotime->".$next_time_f_strtotime."from which condition->".$from_which_condition." product_transient_timeout->".$product_transient_timeout."Hours Time->".$hours_time
      , $new_time 
    );

    if( HARIMAY_DD_PLUGIN_DEBUG ){
      wp_mail( 
        HARIMAY_DD_PLUGIN_EMMAIL, 
        'Main function Runnning', 
        "where->".$where."HARIMAY_DD_product_args_active->".$HARIMAY_DD_product_args_active."post_name->".$the_query->post->ID."new_time->".$new_time."curretn_time->".$current_time."curretn_time in strtotime->". $current_time_strtotime ."next time->".$next_time_f."next time in strtotime->".$next_time_f_strtotime."from which condition->".$from_which_condition." product_transient_timeout->".$product_transient_timeout."Hours Time->".$hours_time
       );
    }

    $found_posts = $the_query->found_posts;
    if( $found_posts > $HARIMAY_DD_product_args_active ){
      $HARIMAY_DD_product_args_active++;

    }else{
      $HARIMAY_DD_product_args_active = 1;
    }
    update_option( 'HARIMAY_DD_product_args_active', $HARIMAY_DD_product_args_active );

    return array(
      'product_query' => $the_query,
      'product_id' => $the_query->post->ID,
    );
}



function HARIMAY_DD_to_check_in_cron_if_value_exit_or_not( $for_transent=false ){

  $HARIMAY_DD_get_product_id_and_time = HARIMAY_DD_get_value_from_option_table_post_deatils();
  $return_value = 0;
  if( !empty( $HARIMAY_DD_get_product_id_and_time ) ){
    $post_id = $HARIMAY_DD_get_product_id_and_time['post_id'];
    $end_time = $HARIMAY_DD_get_product_id_and_time['end_time'];
    if( !empty( $post_id ) && !empty( $end_time ) && HARIMAY_DD_is_deal_active_or_not() ){
      
      $current_time = current_time( 'Y-m-d H:i:s' );
      $current_time_strtotime = strtotime( $current_time );
      
      if( $end_time > $current_time_strtotime ){
        $return_value = $post_id;
      }
    }
  }
  return $return_value;
}



function HARIMAY_DD_get_value_from_option_table_post_deatils(){
  return get_option( 'HARIMAY_DD_get_product_id_and_time', false );
}



function HARIMAY_DD_check_the_transient_value_fron_cron_of_server( $where=false, $for_transent=false ){
  if( HARIMAY_DD_is_deal_active_or_not() ){

        //if( HARIMAY_DD_PLUGIN_DEBUG )
          //mail( HARIMAY_DD_PLUGIN_EMMAIL, 'Wordpress cron runnning', current_time( 'Y-m-d H:i:s' ) );

        HARIMAY_DD_get_DD_product_id( $where, 2, $for_transent );
    }
    else{

        //if( HARIMAY_DD_PLUGIN_DEBUG )
          //mail( HARIMAY_DD_PLUGIN_EMMAIL, 'Wordpress cron not runnning', current_time( 'Y-m-d H:i:s' ) );
    } 
}





function HARIMAY_DD_delect_unreview_transent(){
  delete_transient( 'harimay_limit_unreview_product' );
  delete_transient( 'harimay_limit_unreview_product' );
}