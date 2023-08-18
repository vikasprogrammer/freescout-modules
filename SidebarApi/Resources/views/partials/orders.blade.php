<div class="conv-sidebar-block">
    <div class="panel-group accordion accordion-empty">
        <div class="panel panel-default @if ($load) wc-loading @endif" id="wc-orders">
            @include('sidebarapi::partials/orders_list')
        </div>
    </div>
</div>
@section('javascript')
    @parent
    var ajaxUrl="{{url('/')}}";
    var wc_customer_email = '';
    initSidebarapi('{{ $customer_email }}', {{ (int)$load }});
	function initSidebarapi(customer_email, load)
	{
		wc_customer_email = customer_email;

		$(document).ready(function(){

			if (load) {
				wcLoadOrders();
			}
			$('.wc-refresh').click(function(e) {
				wcLoadOrders();
				e.preventDefault();
			});
		});
	}

	function wcLoadOrders()
	{	
			$('#wc-orders img').show();
			$.ajax({
		        url: ajaxUrl+"/sidebarapi/ajax",
		        type: "post",
		        data: { 
		         "_token": "{{ csrf_token() }}",
		       	 action: 'orders',
				 customer_email: wc_customer_email
		         },
		        success: function(response){
		       	  $('#wc-orders img').hide();
		           if (typeof(response.status) != "undefined" && response.status == 'success'
					&& typeof(response.html) != "undefined" && response.html
					) {
						$('#wc-orders').html(response.html);
						$('.wc-refresh').click(function(e) {
							wcLoadOrders();
							e.preventDefault();
						});
					} else {
						//showAjaxError(response);
					}
		        }
		    }); 
	}

@endsection