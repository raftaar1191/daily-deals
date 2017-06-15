<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



add_shortcode( 'HARIMAY_DD_daily_deals', 'HARIMAY_DD_daily_deals_callback_shortcode' );
function HARIMAY_DD_daily_deals_callback_shortcode(){ 
	ob_start(); ?>
	<div class="harimay_widget_deals_products_for_relaod widget_deals_products widget">
		<ul class="harimay_product_dd_widget_class"><?php
			if( HARIMAY_DD_is_deal_active_or_not() ){

				$get_the_ID = HARIMAY_DD_get_product_id_for_transient_id();
	    		if( !empty( $get_the_ID ) ){

	                $get_post_custom      	= get_post_custom( $get_the_ID );
	                $stock =  $get_post_custom['_stock'][0];
	                $HARIMAY_DD_daily_deals_quantity = $get_post_custom['HARIMAY_DD_daily_deals_quantity'][0];
	                $HARIMAY_DD_daily_deals_quantity_less_stock = $get_post_custom['HARIMAY_DD_daily_deals_quantity_less_stock'][0];
	                $HARIMAY_DD_daily_deals_price = $get_post_custom['HARIMAY_DD_daily_deals_price'][0];
	                $new_stock = $stock - $HARIMAY_DD_daily_deals_quantity_less_stock;

	                $price = $get_post_custom['_regular_price'][0];
	                if( empty($price ) ){
	                	$price = $get_post_custom['_sale_price'][0];
	                }

	                if( $stock > $HARIMAY_DD_daily_deals_quantity_less_stock && $new_stock > 0 ){ 
            			$MM_DD_setting = get_option( 'MM_DD_setting' );
	                	$get_permalink      	= get_permalink( $get_the_ID );
	                	$size = 'shop_catalog';
		                $product 				= wc_get_product( $get_the_ID ); ?>
		                <li class="harimay_product_dd_widget_li HARIMAY_DD_starting">
		                	<h3 class="HARIMAY_dd_header widget-title">
		                		<span>
		                			<?php
		                			$deals_active_text_value =  $MM_DD_setting['deals_active_text_value'];
		                			if( empty( $deals_active_text_value ) ){
		                				$deals_active_text_value = "Daily Deal. Every Day. A New Deal.";
		                			}
		                			echo $deals_active_text_value;
		                			?>
		                		</span>
		                	</h3>

		                	<div class="harimay_product_sub">
		                		<?php
		                		if( empty( $HARIMAY_DD_daily_deals_price ) || $HARIMAY_DD_daily_deals_price <= 0 ){
		                			$percentage_value = $MM_DD_setting['percentage_value'];
		                			if( empty( $percentage_value ) ){
		                				$percentage_value = 50;
		                			}
		                			echo '<div class="sale-off-harimay pull-left">-'.$percentage_value.'%</div>';
		                		}else{
		                			
					                if( !empty( $price ) && $price > 0 && $HARIMAY_DD_daily_deals_price <= $price ){
					                	$percentage_value = $HARIMAY_DD_daily_deals_price / $price * 100;
					                }
		                			echo '<div class="sale-off-harimay pull-left">-'.round( $percentage_value ).'%</div>';
		                		}

		                		$option_table_post_deatils = HARIMAY_DD_get_value_from_option_table_post_deatils();
            					$HARIMAY_DD_get_product_stock_over_new = $option_table_post_deatils['end_time'];
			                	$HARIMAY_DD_get_product_stock_over_new = date('Y-m-d-H-i-s',$HARIMAY_DD_get_product_stock_over_new );
								HARIMAY_DD_to_print_the_date_and_time_with_js( $HARIMAY_DD_get_product_stock_over_new, 'New deal in moments' ); ?>
		                		<a href="<?php echo $get_permalink; ?>">
		                			<figure class="image">
	                                	<?php echo trim( $product->get_image('image-widgets') ); ?>
	                                </figure>

		                			<!-- <h3><?php 
		                			//echo substr( esc_html( $product->get_title() ), 0, 48 ); ?></h3> -->
		                			<h3><?php echo esc_html( $product->get_title() ); ?></h3>

		                			<?php
		                			if ( $price_html = $product->get_price_html() ) : ?>
										<span class="price"><?php echo $price_html; ?></span>
									<?php endif; ?>

		                		</a>

								<div class="rating clearfix">
	                                <?php if ( $rating_html = $product->get_rating_html() ) { ?>
	                                    <div><?php echo trim( $rating_html ); ?></div>
	                                <?php }else{ ?>
	                                    <div class="star-rating"></div>
	                                <?php } ?>
	                            </div>
                            </div>
		                </li>
	                <?php
	                }else{
	                	HARIMAY_DD_stock_over_of_product( $get_the_ID, $get_post_custom );
	                }
	            }else{
	            	HARIMAY_DD_no_deals_is_active();
	            }
			}else{
				HARIMAY_DD_is_going_to_start_at();
			} ?>
	    </ul>
    </div><?php
    $output = ob_get_contents();
	ob_end_clean();
	return $output;
}


/**
 * Adds HARIMAY_DD_product_Widget widget.
 */
class HARIMAY_DD_product_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'HARIMAY_DD_product_Widget', // Base ID
			__( 'Daily Deals Widget', 'HARIMAY-DD-textcore' ), // Name
			array( 'description' => __( 'Daily Deals widget to show product in deals deals', 'HARIMAY-DD-textcore' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		echo HARIMAY_DD_daily_deals_callback_shortcode();
		
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Daily Deal', 'HARIMAY-DD-textcore' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		HARIMAY_DD_delect_unreview_transent();

		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class HARIMAY_DD_product_Widget



function HARIMAY_DD_no_deals_is_active(){ 
	$MM_DD_setting = get_option( 'MM_DD_setting' ); ?>
	<li class="HARIMAY_DD_starting no_deals_is_running">
		<h3 class="HARIMAY_dd_header widget-title">
			<span>
				<?php
    			$current_no_deals_is_active =  $MM_DD_setting['current_no_deals_is_active'];
    			if( empty( $current_no_deals_is_active ) ){
    				$current_no_deals_is_active = "Current No deal is Running.";
    			}
    			echo $current_no_deals_is_active;
    			?>
			</span>
		</h3>
	</li><?php
}



function HARIMAY_DD_is_going_to_start_at(){

	HARIMAY_DD_to_delect_all_DD_transient( 6 ); 
	
	$MM_DD_setting = get_option( 'MM_DD_setting' );
    $time_period_start_date_text = $MM_DD_setting['time_period_start_date_text'];
    $time_period_start_time_text = $MM_DD_setting['time_period_start_time_text'];
    $time_period_interval_text = $MM_DD_setting['time_period_interval_text'];
    if( empty( $time_period_start_date_text ) || empty( $time_period_interval_text ) ){
    	HARIMAY_DD_no_deals_is_active();
    }else{
    	 $deals_Srat_time = $time_period_start_date_text ."-".$time_period_start_time_text."-00-00";  ?>
	    <li class="HARIMAY_DD_starting deal_start_from">
			<h3 class="HARIMAY_dd_header widget-title">
				<span>
					<?php
					$deal_will_going_to_start_on =  $MM_DD_setting['deal_will_going_to_start_on'];
	    			if( empty( $deal_will_going_to_start_on ) ){
	    				$deal_will_going_to_start_on = "Deals will start from";
	    			}
	    			echo $deal_will_going_to_start_on;
	    			?>
				</span>
			</h3>
			<div class="harimay_product_sub">
				<?php HARIMAY_DD_to_print_the_date_and_time_with_js( $deals_Srat_time, 'New deal in moments' ); ?>
			</div>
		</li><?php
    }
}


function HARIMAY_DD_stock_over_of_product( $get_the_ID, $get_post_custom ){
	if( !$get_post_custom['dd_stoack_over'][0] ){
		update_post_meta( $get_the_ID, 'dd_stoack_over', 1 );
	}
	$MM_DD_setting = get_option( 'MM_DD_setting' );
	$option_table_post_deatils = HARIMAY_DD_get_value_from_option_table_post_deatils();
    $HARIMAY_DD_get_product_stock_over_new = $option_table_post_deatils['end_time'];
	$HARIMAY_DD_get_product_stock_over_new = date('Y-m-d-H-i-s',$HARIMAY_DD_get_product_stock_over_new ); ?>
    <li class="HARIMAY_DD_starting next_deal_start_from">
		<h3 class="HARIMAY_dd_header widget-title">
			<span>
				<?php
				$stock_over_value =  $MM_DD_setting['stock_over_value'];
    			if( empty( $stock_over_value ) ){
    				$stock_over_value = "Stock over next deal will start with in";
    			}
    			echo $stock_over_value;
    			?>
			</span>
		</h3>
		<div class="harimay_product_sub">
			<?php HARIMAY_DD_to_print_the_date_and_time_with_js( $HARIMAY_DD_get_product_stock_over_new, 'New deal in moments' ); ?>
		</div>
	</li><?php
}




function HARIMAY_DD_to_print_the_date_and_time_with_js( $dealstime, $FinishMessage ){ 

	wp_enqueue_script( 'HARIMAY-DD-countdown-script');
	
	$div_class =  rand()."harimay_wid_class";
	$div_class_js =  ".".$div_class; ?>
	<div id="clock_main" class="<?php echo $div_class; ?>" data-harimay_countdown="countdown" data-date="<?php echo $dealstime; ?>"></div>
	<script type='text/javascript'>
		jQuery(document).ready(function() {
	        jQuery( '<?php echo $div_class_js; ?>' ).each(function(index, el) {
	            var $this = jQuery(this);
	            var $date = $this.data('date').split("-");
	            $this.lofCountDown({
	                TargetDate:$date[1]+"/"+$date[2]+"/"+$date[0]+" "+$date[3]+":"+$date[4]+":"+$date[5],
	                DisplayFormat:"<ul class=\"clock_main clock harimay_countdown-times\"><li class=\"day\">%%D%% <?php echo __('Days', TEXTDOMAIN); ?></li><li class=\"hours\">%%H%% <?php echo __('Hours', TEXTDOMAIN); ?> </li><li class=\"minutes\">%%M%% <?php echo __('Mins', TEXTDOMAIN); ?> </li><li class=\"seconds\">%%S%% <?php echo __('Secs', TEXTDOMAIN); ?> </div></ul>",
	                FinishMessage: "<?php echo __( $FinishMessage , HARIMAY-DD-textcore); ?>",
	                CurrentDate: "<?php echo current_time( 'm/d/Y H:i:s' ); ?>"
	            });
	        });
	    });
	</script><?php
}