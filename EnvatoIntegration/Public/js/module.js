/**
 * Module's JavaScript.
 */
var cf_save_period = 5; // seconds
var cf_save_fields = [];


var ajaxUrl="{{url('/')}}";
// backend
function initEnvato()
{
 	
	$(document).ready(function() {

		if (!$('#custom-fields-form').length) {
			return;
		}else{
			getOrder();
		}
		$('#custom-fields-form .custom-field :input').on('keyup keypress change', function(e) {
			//location.reload();
		});
	});
}

function getOrder(){
	var conversion_id = getGlobalAttr('conversation_id');
	$.ajaxSetup({
	    headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    }
	});
	$.ajax({
        url: "/envatointegration/ajax",
        type: "post",
     	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: { 
       	 action: 'orders',
       	 'conversion_id':conversion_id
         },
        success: function(response){
       	  $('#wc-orders img').hide();
           if (typeof(response.status) != "undefined" && response.status == 'success'
			&& typeof(response.html) != "undefined" && response.html
			) {
				$('#conv-layout-customer').append(response.html);
			}
        }
    }); 
}
