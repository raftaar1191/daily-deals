var _doing_ajax = false;
jQuery( "div.harimay_widget_deals_products_for_relaod" ).addClass( 'hidden' );


function HARIMAY_DD_relaod_the_product_on_DD(){
  if( _doing_ajax ){
        return false;
    }
    jQuery( "div.harimay_widget_deals_products_for_relaod" ).removeClass( 'hidden' );
    _doing_ajax = true;
    jQuery.ajax({
       type: "POST",
       url: frountend_ajax.ajaxurl,
       data: {
        action : frountend_ajax.reload_product
       },
       success:function( response ){
          _doing_ajax = false;
          var product_html = jQuery.parseJSON( response );
          if( product_html.html !== "" && product_html.html !== undefined ){
            jQuery( "div.harimay_widget_deals_products_for_relaod" ).replaceWith( product_html.html );
          }else{
            window.location.reload();
            //alert("Your request failed to process.. please try again!!"); 
          }
       },
       error:function( response ){
          window.location.reload();
          _doing_ajax = false;
          //alert("Your request failed to process.. please try again!");
       }
    });
}

HARIMAY_DD_relaod_the_product_on_DD();
setInterval(HARIMAY_DD_relaod_the_product_on_DD , 60*1000*20);