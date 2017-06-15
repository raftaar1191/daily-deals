<?php
/*
Plugin Name: Deal Deals for Woocommerces by Harimay
Plugin URI: http://harimay.com/
Description: This plugin provided a new tab in product single page admin section where user can set the prices an quantity of the product and a tab to set the product deals timing
Version: 0.0.1
Author: Harimay
Author URI: http://harimay.com/
Text Domain: HARIMAY-DD-textcore
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/* ++++++++++++++++++++++++++++++
 * CONSTANTS
 +++++++++++++++++++++++++++++ */
// Directory
if ( ! defined( 'HARIMAY_DD_PLUGIN_DIR' ) ) {
	define( 'HARIMAY_DD_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}


define( 'HARIMAY_DD_PLUGIN_DEBUG', false );
define( 'HARIMAY_DD_PLUGIN_EMMAIL', 'test@test.com' );


/*
 * Include all the function that are being over write the other plugin function or being needed to run this plugin
*/
include_once( HARIMAY_DD_PLUGIN_DIR . 'include/common-functions.php' );


/*
 * Run when plugin is loaded action is fire
 * Include the file that need to run this plugin
*/
function HARIMAY_DD_on_pluigin_loaded_callback() {
    if ( is_admin() ) {
        /* admin menu frountend ajax */
        include_once( HARIMAY_DD_PLUGIN_DIR . 'include/backend/custom-feilds-product/common-setting.php' );

        include_once( HARIMAY_DD_PLUGIN_DIR . 'include/backend/submenu_page_setting/common-setting.php' );

        /* admin menu frountend ajax */
        include_once( HARIMAY_DD_PLUGIN_DIR . 'ajax/ajax-background.php' );
    }

    /* */
    wp_deregister_script( 'woocommerce_quick-edit' );
    
    include_once( HARIMAY_DD_PLUGIN_DIR . 'ajax/ajax-frountend.php' );
    include_once( HARIMAY_DD_PLUGIN_DIR . 'include/frountend/common-setting.php' );
    include_once( HARIMAY_DD_PLUGIN_DIR . 'include/frountend/widget.php' );
}





function HARIMAY_DD_admin_enqueue_scripts_callback( $hook ) {

    if ( 'edit.php' === $hook && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] ) {
        wp_enqueue_script( 'quit_edit_Script', plugins_url('assest/js/admin_quit_edit_script.js', __FILE__), array( 'jquery' ), '1.0.0' , true );
    }

    if( $_GET['page'] == 'daily_deals_product_setting' && 'product' === $_GET['post_type'] ){
        global $wp_scripts;

        wp_enqueue_script( 'jquery-ui-datepicker' );
        $screen = get_current_screen();
        $jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
        // Admin styles for WC pages only
        wp_enqueue_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );
    }

    if( $_GET['page'] == 'daily_deals_product_view' && 'product' === $_GET['post_type'] ){
        wp_enqueue_script( 'jquery-ui-sortable' );

        wp_enqueue_script( 'daily_deals_product_view', plugins_url('assest/js/daily_deals_product_view.js', __FILE__), array( 'jquery-ui-sortable' ), '1.0.0' , true );
        $data = array( 
            'ajaxurl'=>  admin_url('/admin-ajax.php')
        );
        wp_localize_script( 'daily_deals_product_view', 'backend_ajax', $data); 

        wp_enqueue_style( 'HARIMAY-DD-backend-style', plugins_url('assest/css/backend.css', __FILE__) );
    }else{
        wp_enqueue_script( 'HARIMAY-DD-woocommerce_quick-edit', plugins_url('assest/js/quick-edit.js', __FILE__),  array( 'jquery' ), WC_VERSION );
    }
}



function HARIMAY_DD_wp_enqueue_scripts_callback(){
    wp_register_script( 'HARIMAY-DD-countdown-script', plugins_url('assest/js/countdown.js', __FILE__),  array( 'jquery' ), WC_VERSION, false );
    wp_enqueue_script( 'HARIMAY-DD-frountend-script', plugins_url('assest/js/frountend.js', __FILE__),  array( 'jquery' ), WC_VERSION, true );
    $data = array( 
        'ajaxurl'=>  admin_url('/admin-ajax.php'),
        'reload_product'=>  'HARIMAY_DD_relaod_the_product_on_DD'
    );
    wp_localize_script( 'HARIMAY-DD-frountend-script', 'frountend_ajax', $data);
    wp_enqueue_style( 'HARIMAY-DD-frountend-style', plugins_url('assest/css/frountend.css', __FILE__) );
}


// register Foo_Widget widget
function HARIMAY_DD_register_product_widget() {
    if( class_exists( 'HARIMAY_DD_product_Widget' ) ){
        register_widget( 'HARIMAY_DD_product_Widget' );
    }
}







function HARIMAY_DD_new_cron_job_schedules($interval) {
    $interval['min_1'] = array(
        'interval' => 60,
        'display' => __('Once 60 Minute')
    );
    return $interval;
}

function HARIMAY_DD_reset_transent_query_min_1_callback() {
	HARIMAY_DD_check_the_transient_value_fron_cron_of_server( 1, 9 );
}

function HARIMAY_DD_custom_plugin_activation_callback() {
    wp_schedule_event( time() , 'min_1', 'HARIMAY_DD_reset_transent_query_min_1');
}
register_activation_hook(__FILE__, 'HARIMAY_DD_custom_plugin_activation_callback');


function HARIMAY_DD_custom_plugin_deactivation_callback() {
    wp_clear_scheduled_hook('HARIMAY_DD_reset_transent_query_min_1');
}
register_deactivation_hook(__FILE__, 'HARIMAY_DD_custom_plugin_deactivation_callback');



add_filter('cron_schedules', 'HARIMAY_DD_new_cron_job_schedules');
add_action( 'plugins_loaded', 'HARIMAY_DD_on_pluigin_loaded_callback' );
add_action( 'admin_enqueue_scripts', 'HARIMAY_DD_admin_enqueue_scripts_callback' );
add_action( 'wp_enqueue_scripts', 'HARIMAY_DD_wp_enqueue_scripts_callback' );
add_action( 'widgets_init', 'HARIMAY_DD_register_product_widget' );
add_action('HARIMAY_DD_reset_transent_query_min_1', 'HARIMAY_DD_reset_transent_query_min_1_callback');