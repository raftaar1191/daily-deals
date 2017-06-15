<?php
/**
 * Process meta
 *
 * Processes the custom tab options when a post is saved
 */
function HARIMAY_DD_process_product_meta_custom_tab_spec( $post_id ) {

        if ( function_exists( 'HARIMAY_DD_delect_unreview_transent' ) ) {
            HARIMAY_DD_delect_unreview_transent();
        }

        update_post_meta( $post_id, 'HARIMAY_DD_get_product_id_for_transient_id', ( isset($_REQUEST['HARIMAY_DD_get_product_id_for_transient_id']) && $_REQUEST['HARIMAY_DD_get_product_id_for_transient_id'] ) ? 'yes' : 'no' );

        update_post_meta( $post_id, 'HARIMAY_DD_daily_deals_price', $_REQUEST['HARIMAY_DD_daily_deals_price'] );
        
        $HARIMAY_DD_daily_deals_quantity = $_REQUEST['HARIMAY_DD_daily_deals_quantity'];
        $stock = $_REQUEST['_stock'];
        if( !isset( $_REQUEST['HARIMAY_DD_daily_deals_quantity'] ) || empty( $HARIMAY_DD_daily_deals_quantity ) ){
            if( !empty( $stock ) && isset( $_REQUEST['_stock'] ) ){
                $HARIMAY_DD_daily_deals_quantity = $stock;
            }else{
                $HARIMAY_DD_daily_deals_quantity = 100;
            }
        }
        update_post_meta( $post_id, 'HARIMAY_DD_daily_deals_quantity', $HARIMAY_DD_daily_deals_quantity );

        $HARIMAY_DD_daily_deals_order = $_REQUEST['HARIMAY_DD_daily_deals_order'];
        if( empty( $HARIMAY_DD_daily_deals_order ) ){
            $HARIMAY_DD_daily_deals_order = 100;
        }

        update_post_meta( $post_id, 'HARIMAY_DD_daily_deals_order', $HARIMAY_DD_daily_deals_order );

        HARIMAY_DD_update_stock_less_for_day_deals( $stock, $HARIMAY_DD_daily_deals_quantity, $post_id );

        HARIMAY_DD_upate_the_stock_of_product( $stock, $HARIMAY_DD_daily_deals_quantity, $post_id );

        $get_the_ID = HARIMAY_DD_get_product_id_for_transient_id();
        if( $post_id == $get_the_ID &&  isset( $_REQUEST['HARIMAY_DD_get_product_id_for_transient_id'] ) && $_REQUEST['HARIMAY_DD_get_product_id_for_transient_id'] != "yes" ){
            $HARIMAY_DD_product_args_active = get_option( 'HARIMAY_DD_product_args_active', 1 );
            if( !empty( $HARIMAY_DD_product_args_active ) && $HARIMAY_DD_product_args_active > 1 ){
                $HARIMAY_DD_product_args_active = $HARIMAY_DD_product_args_active - 1;
                update_option( 'HARIMAY_DD_product_args_active', $HARIMAY_DD_product_args_active );
            }
            
            HARIMAY_DD_to_delect_all_DD_transient( 4 );
        }

}



function HARIMAY_DD_woocommerce_product_write_panel_tabs_callback() { ?>
    <li class="product_daily_deals_class"><a href="#product_daily_deals"><?php _e('Daily Deals', 'HARIMAY-DD-textcore'); ?></a></li>
<?php
}




/**
 * Custom Tab Options
 *
 * Provides the input fields and add/remove buttons for custom tabs on the single product page.
 */
function HARIMAY_DD_woocommerce_product_write_panels_callback() {
        global $post;
        ?>
        <div id="product_daily_deals" class="panel woocommerce_options_panel">
            <div class="options_group">
                    <p class="form-field">
                        <?php woocommerce_wp_checkbox( array( 'id' => 'HARIMAY_DD_get_product_id_for_transient_id', 'label' => __('Daily Deals Active?', 'HARIMAY-DD-textcore'), 'description' => __('Enable This will add the product in Daily Deals.', 'HARIMAY-DD-textcore') ) ); ?>
                    </p>
            </div>

            <div class="options_group custom_tab_options">                                                                        
                <p class="form-field">
                        <label><?php _e('Custom Product Setting for Daily Deals:', 'HARIMAY-DD-textcore'); ?></label>
                        <?php 
                        woocommerce_wp_text_input( array( 
                            'id' => 'HARIMAY_DD_daily_deals_price', 
                            'label' => __( 'Daily Deals Price', 'HARIMAY-DD-textcore' ) . ' (' . get_woocommerce_currency_symbol() . ')',
                             'data_type' => 'price'
                        ) );

                        woocommerce_wp_text_input( array(
                            'id'                => 'HARIMAY_DD_daily_deals_quantity',
                            'label'             => __( 'Daily Deals Quantity', 'woocommerce' ),
                            'desc_tip'          => true,
                            'description'       => __( 'Default Quantity is set to 100.', 'woocommerce' ),
                            'type'              => 'number',
                            'data_type'         => 'stock'
                        ) ); 

                        woocommerce_wp_text_input( array(
                            'id'                => 'HARIMAY_DD_daily_deals_order',
                            'label'             => __( 'Daily Deals Order', 'woocommerce' ),
                            'desc_tip'          => true,
                            'description'       => __( 'Default Order is set to 100 and if two product Deals are at 100 then title order is being used.', 'woocommerce' ),
                            'type'              => 'number',
                            'data_type'         => 'stock'
                        ) ); 

                        ?>
                </p>
            </div>
        </div>
<?php
}





function HARIMAY_DD_woocommerce_product_quick_edit_start_callback(){
    ?>
    <div class="custom_product_dealis">
        <label class="alignleft HARIMAY_DD_get_product_id_for_transient_id">
            <input type="checkbox" name="HARIMAY_DD_get_product_id_for_transient_id" id="HARIMAY_DD_get_product_id_for_transient_id" class="checkbox HARIMAY_DD_get_product_id_for_transient_id" value="1">
            <span class="checkbox-title"><?php _e( 'Daily Deals Active?', 'woocommerce' ); ?></span>
        </label>
        <br class="clear" />
        <label>
            <span class="title"><?php _e( 'DD Price', 'woocommerce' ); ?></span>
            <span class="input-text-wrap">
                <input type="number" name="HARIMAY_DD_daily_deals_price" class="text HARIMAY_DD_daily_deals_price" placeholder="<?php esc_attr_e( 'Daily Deals Price', 'woocommerce' ); ?>" value="">
            </span>
        </label>
        <br class="clear" />
        <label>
            <span class="title"><?php _e( 'DD Quantity', 'woocommerce' ); ?></span>
            <span class="input-text-wrap">
                <input type="number" name="HARIMAY_DD_daily_deals_quantity" class="text HARIMAY_DD_daily_deals_quantity" placeholder="<?php esc_attr_e( 'Daily Deals Quantity', 'woocommerce' ); ?>" value="">
            </span>
        </label>
        <br class="clear" />
        <label>
            <span class="title"><?php _e( 'DD Order', 'woocommerce' ); ?></span>
            <span class="input-text-wrap">
                <input type="number" name="HARIMAY_DD_daily_deals_order" class="text HARIMAY_DD_daily_deals_order" placeholder="<?php esc_attr_e( 'Daily Deals Order', 'woocommerce' ); ?>" value="">
            </span>
        </label>
        <br class="clear" />
    </div>
    <?php 
}


function HARIMAY_DD_woocommerce_product_quick_edit_save_callback( $product ){
    HARIMAY_DD_process_product_meta_custom_tab_spec( $product->id );
}







/**
 * Output a datepicker input box.
 *
 * @param array $field
 */
function woocommerce_wp_datapicker_input( $field ){
    global $thepostid, $post;

    $thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
    $field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
    $field['class'] = isset( $field['class'] ) ? $field['class'] : '';
    $field['label'] = isset( $field['label'] ) ? $field['label'] : '';
    echo    '<p class="form-field datepicker_dates_fields">';
            if( !empty( $field['label'] ) ){
                echo '<label for="'. $field['class'] .'">' . __( $field['label'], 'woocommerce' ) . '</label>';
            }
            echo '<input type="text" class="short '.esc_attr( $field['class'] ).'" name="'.esc_attr( $field['id'] ).'" id="'.esc_attr( $field['id'] ).'" value="' . esc_attr( $field['value'] ) . '" placeholder="' . _x( 'From&hellip;', 'placeholder', 'woocommerce' ) . ' YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />';
    echo '</p>';
}


/**
 * Output a datepicker input box.
 *
 * @param array $field
 */
function woocommerce_wp_timepicker_input( $field ){
    global $thepostid, $post;

    $thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
    $field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
    $field['class'] = isset( $field['class'] ) ? $field['class'] : '';
    $field['label'] = isset( $field['label'] ) ? $field['label'] : '';
    echo    '<p class="form-field timepicker_dates_fields">';
                if( !empty( $field['label'] ) ){
                    echo '<label for="'. $field['class'] .'">' . __( $field['label'], 'woocommerce' ) . '</label>';
                }
                echo '<input type="text" class="short '.esc_attr( $field['class'] ).'" name="'.esc_attr( $field['id'] ).'" id="'.esc_attr( $field['id'] ).'" value="' . esc_attr( $field['value'] ) . '"';
    echo '</p>';
}



add_action('woocommerce_process_product_meta', 'HARIMAY_DD_process_product_meta_custom_tab_spec', 10, 2);
add_action('woocommerce_product_write_panel_tabs', 'HARIMAY_DD_woocommerce_product_write_panel_tabs_callback');
add_action('woocommerce_product_write_panels', 'HARIMAY_DD_woocommerce_product_write_panels_callback');
add_action( 'woocommerce_product_quick_edit_start', 'HARIMAY_DD_woocommerce_product_quick_edit_start_callback' );
add_action( 'woocommerce_product_quick_edit_save', 'HARIMAY_DD_woocommerce_product_quick_edit_save_callback', 10, 1 );