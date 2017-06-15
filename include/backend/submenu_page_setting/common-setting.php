<?php

new HARIMAY_DD_deals_deals_SettingsPage;

class HARIMAY_DD_deals_deals_SettingsPage{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page_message' ) );
        add_action( 'admin_init', array( $this, 'page_init_message' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page_message()
    {
        add_submenu_page('edit.php?post_type=product', 'Daily Deals', 'Daily Deals', 'manage_options', 'daily_deals_product_view',  array( $this, 'MM_daily_deals_product_view_callback' ) );
        add_submenu_page('edit.php?post_type=product', 'Daily Deals Setting', 'Daily Deals Setting', 'manage_options', 'daily_deals_product_setting',  array( $this, 'MM_daily_deals_product_setting_callback' ) );
    }


    public function MM_daily_deals_product_view_callback(){
        echo '<div class="harimay_product_main">';
            $MM_DD_setting = get_option( 'MM_DD_setting' ); ?>
            <h2 class="start_from_dd">    
                Started From
                <span class="date"><?php echo $MM_DD_setting['time_period_start_date_text']; ?></span>
                at 
                <span class="time"><?php echo $MM_DD_setting['time_period_start_time_text']; ?></span>
            </h2>
            <?php
            $args = $this->MM_daily_deals_product();
            $the_query = new WP_Query( $args );
            // The Loop

            if( HARIMAY_DD_is_deal_active_or_not() ){
                $active_post_id = HARIMAY_DD_get_product_id_for_transient_id();
            }
            
            $get_admin_url = get_admin_url();

            if ( $the_query->have_posts() ) { ?>
                <ul class="harimay_product_sub"><?php
                    while ( $the_query->have_posts() ) {
                        $the_query->the_post(); 
                        $get_the_ID         = get_the_ID();
                        $get_permalink      = get_permalink( $get_the_ID );
                        $get_edit_post_link = get_edit_post_link( $get_the_ID );
                        $get_the_title      = get_the_title( $get_the_ID );

                        $get_post_custom      = get_post_custom( $get_the_ID );

                        $HARIMAY_DD_get_product_id_for_transient_id = $get_post_custom['HARIMAY_DD_get_product_id_for_transient_id'][0];
                        $HARIMAY_DD_daily_deals_price = $get_post_custom['HARIMAY_DD_daily_deals_price'][0];
                        $HARIMAY_DD_daily_deals_quantity = $get_post_custom['HARIMAY_DD_daily_deals_quantity'][0];
                        $HARIMAY_DD_daily_deals_order = $get_post_custom['HARIMAY_DD_daily_deals_order'][0];
                        $stock = $get_post_custom['_stock'][0];
                        $HARIMAY_DD_daily_deals_quantity_less_stock = $get_post_custom['HARIMAY_DD_daily_deals_quantity_less_stock'][0];
                        $dd_stoack_over = $get_post_custom['dd_stoack_over'][0];
                        $new_stock = $stock - $HARIMAY_DD_daily_deals_quantity_less_stock;
                        //var_dump( $HARIMAY_DD_daily_deals_quantity_less_stock );
                        ?>
                        <li class="harimay_product_li">
                            <h2 class="product_title">
                                <a href="<?php echo $get_permalink; ?>" target="_blank">
                                    <?php 
                                    echo $get_the_title;  
                                    if( $dd_stoack_over != 0 ){
                                        echo '( Stock Over )';
                                    }?>
                                </a>
                            </h2>
                            <p class="link_to_edit"> 
                                <a href="<?php echo $get_permalink; ?>" target="_blank">View</a>&nbsp&nbsp&nbsp&nbsp
                                <a href="#" onclick="HARIMAY_edit_product_deatils( this ); return false;" target="_blank">Edit Here</a>&nbsp&nbsp&nbsp&nbsp
                                <a href="<?php echo $get_admin_url.'post.php?post='.$get_the_ID.'&action=edit'; ?>" target="_blank">Edit</a>&nbsp&nbsp&nbsp&nbsp
                            </p>
                            <div class="view_DD_value_display">
                                <ul class="DD_product_value">
                                    <li class="DD_product_value_li">
                                        <p class="form-field">
                                            <label class="alignleft">
                                                DD Active?:
                                            </label>
                                            <span>
                                                <?php 
                                                if( $HARIMAY_DD_get_product_id_for_transient_id == 'yes' && $active_post_id == $get_the_ID ){
                                                    echo '&nbsp&nbsp&nbspActive';
                                                }else{
                                                    echo '&nbsp&nbsp&nbspNot Active';
                                                }  ?>
                                            </span>
                                        </p>
                                    </li>
                                    <li class="DD_product_value_li">
                                        <p class="form-field">
                                            <label class="alignleft">
                                                DD Price
                                            </label>
                                            <span>
                                                <?php 
                                                if( $HARIMAY_DD_daily_deals_price ){
                                                    echo '&nbsp&nbsp&nbsp'.$HARIMAY_DD_daily_deals_price;
                                                }else{
                                                    echo '&nbsp&nbsp&nbspNo Price is Set';
                                                } ?>
                                            </span>
                                        </p>
                                    </li>
                                    <li class="DD_product_value_li">
                                        <p class="form-field">
                                            <label class="alignleft">
                                                DD Quantity
                                            </label>
                                            <span>
                                                <?php 
                                                if( $HARIMAY_DD_daily_deals_quantity ){
                                                    echo '&nbsp&nbsp&nbsp'.$HARIMAY_DD_daily_deals_quantity."&nbsp(".$stock.")";
                                                }else{
                                                    echo '&nbsp&nbsp&nbspNo Quantity is Set';
                                                } ?>
                                            </span>
                                        </p>
                                    </li>
                                    <li class="DD_product_value_li">
                                        <p class="form-field">
                                            <label class="alignleft">
                                                Product Order
                                            </label>
                                            <span>
                                                <?php 
                                                if( $HARIMAY_DD_daily_deals_order ){
                                                    echo '&nbsp&nbsp&nbsp'.$HARIMAY_DD_daily_deals_order;
                                                }else{
                                                    echo '&nbsp&nbsp&nbspNo Order is Set';
                                                } ?>
                                            </span>
                                        </p>
                                    </li>
                                    <li class="DD_product_value_li">
                                        <p class="form-field">
                                            <label class="alignleft">
                                                Product Status
                                            </label>
                                            <span>
                                                <?php 
                                                if( $new_stock > 0 ){
                                                  echo '&nbsp&nbsp&nbspIn stock'.$new_stock;  
                                                }else{
                                                    echo '&nbsp&nbsp&nbspOut of stock';  
                                                } ?>
                                            </span>
                                        </p>
                                    </li>
                                    <li class="DD_product_value_li">
                                        <p class="form-field">
                                            <label class="alignleft">
                                                Product Type
                                            </label>
                                            <span>
                                                <?php 
                                                $product_type =  HARIMAY_DD_get_product_type( $get_the_ID );
                                                if( !empty( $product_type->name ) ){
                                                    echo $product_type->name;
                                                } ?>
                                            </span>
                                        </p>
                                    </li>
                                </ul>
                            </div>
                            <div class="view_DD_value_edit" id="woocommerce-fields" style="display:none;">
                                <form action="" method="post">
                                    <input type ="hidden"  name="post_id" class="post_id" value="<?php echo $get_the_ID; ?>" />
                                    <ul class="view_DD_value_edit_sub">
                                        <li class="view_DD_value_li">
                                            <p class="form-field datepicker_dates_fields">
                                                <label class="alignleft HARIMAY_DD_get_product_id_for_transient_id">
                                                    <input type="checkbox" name="HARIMAY_DD_get_product_id_for_transient_id"  class="checkbox HARIMAY_DD_get_product_id_for_transient_id" <?php if( $HARIMAY_DD_get_product_id_for_transient_id == 'yes' ){ echo 'checked'; } ?> value="<?php echo  $HARIMAY_DD_get_product_id_for_transient_id;?>">
                                                    <span class="checkbox-title"><?php _e( 'Daily Deals Active?', 'woocommerce' ); ?></span>
                                                </label>
                                            </p>
                                        </li>
                                        <li class="view_DD_value_li">
                                             <p class="form-field">
                                                <label>
                                                    <span class="title"><?php _e( 'Daily Deals Price', 'woocommerce' ); ?></span>
                                                    <span class="input-text-wrap">
                                                        <input type="number" name="HARIMAY_DD_daily_deals_price" class="text HARIMAY_DD_daily_deals_price" placeholder="<?php esc_attr_e( 'Daily Deals Price', 'woocommerce' ); ?>" value="<?php echo $HARIMAY_DD_daily_deals_price; ?>">
                                                    </span>
                                                </label>
                                            </p>
                                        </li>
                                        <li class="view_DD_value_li">
                                             <p class="form-field">
                                                <label>
                                                    <span class="title"><?php _e( 'Daily Deals Quantity', 'woocommerce' ); ?></span>
                                                    <span class="input-text-wrap">
                                                        <input type="number" name="HARIMAY_DD_daily_deals_quantity" class="text HARIMAY_DD_daily_deals_quantity" placeholder="<?php esc_attr_e( 'Daily Deals Quantity', 'woocommerce' ); ?>" value="<?php echo $HARIMAY_DD_daily_deals_quantity;  ?>">
                                                    </span>
                                                </label>
                                            </p>
                                        </li>
                                        <li class="view_DD_value_li">
                                            <p class="form-field">
                                                <label>
                                                    <span class="title"><?php _e( 'Daily Deals Order', 'woocommerce' ); ?></span>
                                                    <span class="input-text-wrap">
                                                        <input type="number" name="HARIMAY_DD_daily_deals_order" class="text HARIMAY_DD_daily_deals_order" placeholder="<?php esc_attr_e( 'Daily Deals Order', 'woocommerce' ); ?>" value="<?php echo $HARIMAY_DD_daily_deals_order; ?>">
                                                    </span>
                                                </label>
                                            </p>
                                        </li>
                                        <li class="view_DD_submit">
                                            <a href="#" class="update_curent_post" onclick="HARIMAY_DD_on_click_save_deatils_in_db( this ); return false;" postid="<?php $get_the_ID; ?>">Update</a>
                                            &nbsp&nbsp&nbsp&nbsp
                                            <a href="#" class="cancel_curent_post update_curent_post" onclick="HARIMAY_on_click_cancel_deatils_in_db( this ); return false;" postid="<?php $get_the_ID; ?>">Cancel</a>
                                        </li>
                                    </ul>
                                </form>
                            </div>
                        </li><?php
                    } ?>
                </ul><?php
            }
        echo '</div>';
    }


    public function MM_daily_deals_product(){
        return $args = array(
                'post_type'             => 'product',
                'meta_query'            => array(
                                            // array(
                                            //     'key'     => 'dd_stoack_over',
                                            //     'value'   => 0,
                                            //     'type'    => 'numeric',
                                            //     'compare' => '=',
                                            // ),
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
                'posts_per_page'        => -1,
                'orderby'               => 'meta_value_num title',
                'order'                 => 'ASC',
                'meta_key'              => 'HARIMAY_DD_daily_deals_order'
            );
    }



    /**
     * Options page callback
     */
    public function MM_daily_deals_product_setting_callback()
    {
        // Set class property
        $this->options = get_option( 'MM_DD_setting' ); ?>
        
        <div class="wrap wrapHARIMAY">
            <h2>Daily Deals</h2>           
            <?php 
            if( $_GET['settings-updated'] && $_GET['page'] == "daily_deals_product_setting" ){
                if( empty( $this->options['time_period_start_date_text'] ) || empty( $this->options['time_period_interval_text'] ) ){

                    if( HARIMAY_DD_PLUGIN_DEBUG )
                        wp_mail( HARIMAY_DD_PLUGIN_EMMAIL, 'reset TWO', current_time( "Y-m-d-H-i-s" )."value".$this->options['time_period_interval_text'] );

                    HARIMAY_DD_to_delect_all_DD_transient( 15 );
                    
                    update_option( 'HARIMAY_DD_product_args_active', 1 );
                    
                }elseif ( HARIMAY_DD_is_deal_active_or_not() ) {

                    if( HARIMAY_DD_PLUGIN_DEBUG )
                        wp_mail( HARIMAY_DD_PLUGIN_EMMAIL, 'reset ONE', current_time( "Y-m-d-H-i-s" )."value".$this->options['time_period_interval_text'] );

                    HARIMAY_DD_get_DD_product_id( 6, 9 );
                }

                if ( function_exists( 'HARIMAY_DD_delect_unreview_transent' ) ) {
                    HARIMAY_DD_delect_unreview_transent();
                }
            }
            ?>
            <form method="post" action="options.php">
            <?php
                // echo 'test1';
                // echo get_option( 'time_period_start_date_text' );
                // echo 'test2';

                // This prints out all hidden setting fields
                settings_fields( 'my_option_group_setting' );

                do_settings_sections( 'daily_deals_product_setting' );

                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init_message()
    {        
        register_setting(
            'my_option_group_setting', // Option group
            'MM_DD_setting', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_deal_start_deals', // ID
            'Deals Start Time', // Title
            array( $this, 'print_section_info_deals_start_time_period' ), // Callback
            'daily_deals_product_setting' // Page
        );  

        add_settings_field( 'reset_daily_deals', 'Daily Deals Reset', array( $this, 'reset_daily_deals_callback' ), 'daily_deals_product_setting', 'setting_deal_start_deals' );

        add_settings_field( 'time_period_start_date_text', 'Deals Start On which DATE', array( $this, 'time_period_start_date_text_callback' ), 'daily_deals_product_setting', 'setting_deal_start_deals' );

        add_settings_field( 'time_period_start_time_text', 'Deals Start at what TIME', array( $this, 'time_period_start_time_text_callback' ), 'daily_deals_product_setting', 'setting_deal_start_deals' );

        add_settings_section(
            'setting_deal_change_period', // ID
            'Deals Change Time Periods', // Title
            array( $this, 'print_section_info_deals_change_period' ), // Callback
            'daily_deals_product_setting' // Page
        );

        add_settings_field( 'time_period_interval_text', 'Deals changing Time period', array( $this, 'time_period_interval_text_callback' ), 'daily_deals_product_setting', 'setting_deal_change_period' );


        add_settings_section(
            'setting_deal_change_percentage', // ID
            'Deals Changing Price(%)', // Title
            array( $this, 'print_section_info_deals_change_percentage' ), // Callback
            'daily_deals_product_setting' // Page
        );

        add_settings_field( 'percentage_period_interval_text', 'Deals changing Price( % )', array( $this, 'percentage_value_callback' ), 'daily_deals_product_setting', 'setting_deal_change_percentage' );

        add_settings_section(
            'setting_deal_change_text', // ID
            'Deals Change Text', // Title
            array( $this, 'print_section_info_deals_change_text' ), // Callback
            'daily_deals_product_setting' // Page
        );

        add_settings_field( 'widget_text_active_deals', 'Deals Active Text', array( $this, 'deals_active_text_value_callback' ), 'daily_deals_product_setting', 'setting_deal_change_text' );

        add_settings_field( 'current_no_deals_is_active', 'Deals No Deals is Active', array( $this, 'current_no_deals_is_active_callback' ), 'daily_deals_product_setting', 'setting_deal_change_text' );

        add_settings_field( 'deal_will_going_to_start_on', 'Deals Will start on', array( $this, 'deal_will_going_to_start_on_callback' ), 'daily_deals_product_setting', 'setting_deal_change_text' );

        add_settings_field( 'stock_over_value', 'Stock Over', array( $this, 'stock_over_value_callback' ), 'daily_deals_product_setting', 'setting_deal_change_text' );

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ){
        $new_input = array();


        if( isset( $input['time_period_interval_text'] ) )
            $new_input['time_period_interval_text'] = $input['time_period_interval_text'];

        if( isset( $input['time_period_start_date_text'] ) )
            $new_input['time_period_start_date_text'] = $input['time_period_start_date_text'];

        if( isset( $input['time_period_start_time_text'] ) )
            $new_input['time_period_start_time_text'] = $input['time_period_start_time_text'];

        if( isset( $input['percentage_value'] ) )
            $new_input['percentage_value'] = $input['percentage_value'];

        if( isset( $input['deals_active_text_value'] ) )
            $new_input['deals_active_text_value'] = $input['deals_active_text_value'];

        if( isset( $input['current_no_deals_is_active'] ) )
            $new_input['current_no_deals_is_active'] = $input['current_no_deals_is_active'];

        if( isset( $input['deal_will_going_to_start_on'] ) )
            $new_input['deal_will_going_to_start_on'] = $input['deal_will_going_to_start_on'];

        if( isset( $input['stock_over_value'] ) )
            $new_input['stock_over_value'] = $input['stock_over_value'];

        return $new_input;
    }

    public function print_section_info_deals_change_text(){
        echo 'Change the text of the Widget<br/>';
    }


    public function deals_active_text_value_callback(){
        if( empty( $this->options['deals_active_text_value'] ) ){
            $this->options['deals_active_text_value'] = "Daily Deal. Every Day. A New Deal.";
        }
        echo '<input type="text" style="width:100%;" value="'.$this->options['deals_active_text_value'].'" name="MM_DD_setting[deals_active_text_value]" />';
    }

    public function current_no_deals_is_active_callback(){
        if( empty( $this->options['current_no_deals_is_active'] ) ){
            $this->options['current_no_deals_is_active'] = "Current No deal is Running";
        }
        echo '<input type="text" style="width:100%;" value="'.$this->options['current_no_deals_is_active'].'" name="MM_DD_setting[current_no_deals_is_active]" />';
    }

    public function deal_will_going_to_start_on_callback(){
        if( empty( $this->options['deal_will_going_to_start_on'] ) ){
            $this->options['deal_will_going_to_start_on'] = "Deals will start from";
        }
        echo '<input type="text" style="width:100%;" value="'.$this->options['deal_will_going_to_start_on'].'" name="MM_DD_setting[deal_will_going_to_start_on]" />';
    }

    public function stock_over_value_callback(){
        if( empty( $this->options['stock_over_value'] ) ){
            $this->options['stock_over_value'] = "Stock over next deal will start with in";
        }
        echo '<input type="text" style="width:100%;" value="'.$this->options['stock_over_value'].'" name="MM_DD_setting[stock_over_value]" />';
    }
    

    public function print_section_info_deals_change_period(){
        echo 'Change the sale price of a Woocommerce product for a given time period<br/>';
        echo 'If Deals should get chnage every after 1 day user must enter 24<br/>';
        echo 'IF deals should get change every after 1 week user must enter 168<br/>';
        echo 'Formula for calulating the Number is 1 day= 24<br/>';
    }

    public function print_section_info_deals_start_time_period(){
        echo 'Change the sale price of a Woocommerce product for a given time period<br/>';
        echo 'Need to mention when the deals is going to get start<br/>';
    }


    public function print_section_info_deals_change_percentage(){
        echo 'Change the sale price of a Woocommerce product if price is not given then<br/>';
        echo 'Need to mention the % of discount on the product given<br/>';
        echo 'Defalut is 50%<br/>';
    }


    public function time_period_interval_text_callback(){ 
        echo '<input type="number" value="'.$this->options['time_period_interval_text'].'" class="time_period_interval_text" name="MM_DD_setting[time_period_interval_text]" />';
    }

    public function reset_daily_deals_callback(){ ?>
        <a href="#" onclick="reser_all_the_value(); return false;">Reset Deals</a>
        <?php 
        if( HARIMAY_DD_is_deal_active_or_not() && HARIMAY_DD_get_product_id_for_transient_id() ){
            $option_table_post_deatils = HARIMAY_DD_get_value_from_option_table_post_deatils();
            $HARIMAY_DD_get_product_stock_over_new = $option_table_post_deatils['end_time'];
            if( !empty( $HARIMAY_DD_get_product_stock_over_new ) ){
                $HARIMAY_DD_get_product_stock_over_new = date('Y-m-d H:i:s',$HARIMAY_DD_get_product_stock_over_new );
                echo 'Current Deals will Expired on '.$HARIMAY_DD_get_product_stock_over_new;
            }
        } ?>
        <script type="text/javascript">
            function reser_all_the_value(){
                jQuery( '.wrapHARIMAY .time_period_start_date_text' ).val( '' );
                jQuery( '.wrapHARIMAY .time_period_start_time_text' ).val( '' );
                jQuery( '.wrapHARIMAY .time_period_interval_text' ).val( '' );
                jQuery( '.wrapHARIMAY #submit' ).trigger(  'click');
            }
        </script>

        <?php
    }

    
    public function time_period_start_date_text_callback(){ 
        echo 'Now: '.current_time( "Y-m-d" );
        echo '<input type="text" class="add_datepicker_harimay time_period_start_date_text" value="'.$this->options['time_period_start_date_text'].'" name="MM_DD_setting[time_period_start_date_text]" />';
    }


    public function time_period_start_time_text_callback(){
        $time_period_start_time_text = $this->options['time_period_start_time_text'];
        echo 'Now: '.current_time( "H:i:s" );  ?>
        <select name="MM_DD_setting[time_period_start_time_text]" class='time_period_start_time_text'>
            <?php
            for ($i=0; $i < 24 ; $i++) { ?>
                <option value="<?php echo $i; ?>" <?php if( $time_period_start_time_text == $i ){ echo 'selected'; } ?>><?php echo $i; ?></option>
                <?php
            } ?>
        </select><?php
    }

    public function percentage_value_callback(){ 
        if( empty( $this->options['percentage_value'] ) ){
            $this->options['percentage_value'] = 50;
        }
        echo '<input type="number" value="'.$this->options['percentage_value'].'" name="MM_DD_setting[percentage_value]" />';
    }
}