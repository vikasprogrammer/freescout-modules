<div class="conv-sidebar-block">
    <div class="panel-group accordion accordion-empty">
        <div class="panel panel-default @if ($load) wc-loading @endif" id="wc-orders">
            @include('envatointegration::partials/orders_list')
        </div>
    </div>
</div>
@section('javascript')
    @parent
    var ajaxUrl="{{ url('/') }}";
    initCustomFieldEnvato({{ (int)$load }});
	function initCustomFieldEnvato(load)
	{
		$(document).ready(function(){

			if (load) {
				customFieldLoadOrders();
			}

			$('.wc-refresh').click(function(e) {
				customFieldLoadOrders();
				e.preventDefault();
			});
		});
	}

	function customFieldLoadOrders()
	{	
			$('#wc-orders img').show();
			$.ajax({
		        url: ajaxUrl+"/envatointegration/ajax",
		        type: "post",
		        data: { 
		         "_token": "{{ csrf_token() }}",
		       	 action: 'orders'
		         },
		        success: function(response){
		       	  $('#wc-orders img').hide();
		           if (typeof(response.status) != "undefined" && response.status == 'success'
					&& typeof(response.html) != "undefined" && response.html
					) {
						$('#wc-orders').html(response.html);
						$('.wc-refresh').click(function(e) {
							customFieldLoadOrders();
							e.preventDefault();
						});
					} else {
						//showAjaxError(response);
					}
		        }
		    }); 
	}

@endsection