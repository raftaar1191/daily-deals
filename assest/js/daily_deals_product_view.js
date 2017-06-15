var is_ajax = false;
var ajaxurl = backend_ajax.ajaxurl;
jQuery( document ).ready( function( $ ){
	
	$( "ul.harimay_product_sub" ).sortable({
		stop: function( event, ui ) {
			HARIMAY_on_changing_possion_of_deals();
		}
    });
});


function HARIMAY_on_changing_possion_of_deals(){
	var post_ids = "";
	jQuery( '.harimay_product_main ul li.harimay_product_li' ).each(function() {
		if( post_ids == "" ){
			post_ids = jQuery( this ).find( 'input[name="post_id"]' ).val();
		}else{
	  		post_ids = post_ids + "," +jQuery( this ).find( 'input[name="post_id"]' ).val();
		}
	});
	if( post_ids != "" ){
		HARIMAY_DD_on_changing_possion_of_deals_ajax( post_ids );
	}
}


function HARIMAY_DD_on_changing_possion_of_deals_ajax( post_ids ){
	jQuery.ajax({ 
        type: 'POST',  
        url: ajaxurl,  
        data: {
            post_ids:post_ids,
            action: 'HARIMAY_DD_on_changing_possion_of_deals_ajax'
        },
        success:function( response ){
            is_ajax = false;
            var response_json = jQuery.parseJSON( response );
            //console.log( response_json );
            if(response_json.success !=='' && response_json.success !== undefined){
            	// alert( "Update successfully..!!" );
             //    window.location.reload();
            }else{
            	alert( "Something get working please try after sometime..!!!" );
            }
        },
        error:function( response ){	
            is_ajax = false;
            alert("Something get working please try after sometime..!!");
        }
    });
}


function HARIMAY_edit_product_deatils( handle ){
	jQuery( '.view_DD_value_edit' ).hide();
	jQuery( handle ).closest( 'li' ).find( '.view_DD_value_edit' ).show();
	return false;
}

function HARIMAY_on_click_cancel_deatils_in_db( handle ){
    jQuery( handle ).closest( 'div.view_DD_value_edit' ).hide();
    return false;
}



function HARIMAY_DD_on_click_save_deatils_in_db( handle ){
	if( is_ajax ){
        return false;
    }
    is_ajax = true;
    var view_DD_value_edit = jQuery( handle ).closest( '.view_DD_value_edit' );
    var HARIMAY_DD_get_product_id_for_transient_id = jQuery( view_DD_value_edit ).find( 'input[name="HARIMAY_DD_get_product_id_for_transient_id"]').is(':checked');
    var HARIMAY_DD_daily_deals_price = jQuery( view_DD_value_edit ).find( 'input[name="HARIMAY_DD_daily_deals_price"]' ).val();
    var HARIMAY_DD_daily_deals_quantity = jQuery( view_DD_value_edit ).find( 'input[name="HARIMAY_DD_daily_deals_quantity"]' ).val();
    var HARIMAY_DD_daily_deals_order = jQuery( view_DD_value_edit ).find( 'input[name="HARIMAY_DD_daily_deals_order"]' ).val();
    var post_id = jQuery( view_DD_value_edit ).find( 'input[name="post_id"]' ).val();

 	// console.log( HARIMAY_DD_get_product_id_for_transient_id );
	// console.log( HARIMAY_DD_daily_deals_price );
	// console.log( HARIMAY_DD_daily_deals_quantity );
	// console.log( HARIMAY_DD_daily_deals_order );
	// console.log( post_id );


    jQuery.ajax({ 
        type: 'POST',  
        url: ajaxurl,  
        data: {
            HARIMAY_DD_get_product_id_for_transient_id:HARIMAY_DD_get_product_id_for_transient_id,
            HARIMAY_DD_daily_deals_price:HARIMAY_DD_daily_deals_price,
            HARIMAY_DD_daily_deals_quantity:HARIMAY_DD_daily_deals_quantity,
            HARIMAY_DD_daily_deals_order:HARIMAY_DD_daily_deals_order,
            post_id:post_id,
            action: 'HARIMAY_DD_on_click_save_deatils_in_db'
        },
        success:function( response ){
            is_ajax = false;
            var response_json = jQuery.parseJSON( response );
            //console.log( response_json );
            if(response_json.success !=='' && response_json.success !== undefined){
            	alert( "Update successfully..!!" );
                window.location.reload();
            }else{
            	alert( "Something get working please try after sometime..!!!" );
            }
        },
        error:function( response ){	
            is_ajax = false;
            alert("Something get working please try after sometime..!!");
        }
    });
	jQuery( '.view_DD_value_edit' ).hide();
}