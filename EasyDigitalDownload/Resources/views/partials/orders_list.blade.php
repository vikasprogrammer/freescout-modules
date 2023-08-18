 @if($orders !='') 
<div class="panel-heading">
    <h4 class="panel-title">
        <a data-toggle="collapse" href=".wc-collapse-orders">
            {{ __("Recent Orders") }}
            <b class="caret"></b>
        </a>
    </h4>
</div>
<div class="wc-collapse-orders panel-collapse collapse in scrollbar" id="style-2" style="width: 100%;">
    <div class="panel-body force-overflow">
        <div class="sidebar-block-header2"><strong>{{ __("Recent Orders") }}</strong> (<a data-toggle="collapse" href=".wc-collapse-orders">{{ __('close') }}</a>)</div>
        
        <div id="wc-loader" >
            <img src="{{ asset('img/loader-tiny.gif') }}" />
        </div>
       
        @if (!$load)
            @if($orders !='') 
                <ul class="sidebar-block-list wc-orders-list" >
                    @foreach($orders as $order)

                        <li>
                            <div>
                                <a href="{{ $url }}wp-admin/edit.php?post_type=download&amp;page=edd-payment-history&amp;view=view-order-details&amp;id={{ $order['ID'] }}" target="_blank">{{ $order['products'][0]['name'] }}</a>
                                <span class="pull-right">${{ $order['total'] }}</span>
                            </div>
                            <div>
                             
                                <small class="text-help">{{ \Carbon\Carbon::parse($order['date'])->format('M j, Y')  }}</small>
                               
                            </div>
                           
                        </li>
                    @endforeach
                </ul>
                <div class="panel-heading" style="padding: 0px !important;">
                    <h5 class="panel-title">
                        <a data-toggle="collapse" href=".wc-collapse-orders1">
                            {{ 'Show License' }}
                            <b class="caret"></b>
                        </a>
                    </h5>
                </div>
                <div class="wc-collapse-orders1 panel-collapse collapse">
                <div class="panel-body">
                <div class="sidebar-block-header2"><strong>{{ __("Recent Orders") }}</strong> (<a data-toggle="collapse" href=".wc-collapse-orders1">{{ __('close') }}</a>)</div>
                <div id="wc-loader">
                    <img src="{{ asset('img/loader-tiny.gif') }}" />
                </div>
                <ul class="sidebar-block-list wc-orders-list1">
                    @foreach($orders as $order)
                        @if(!empty($order['licenses']))
                            @foreach($order['licenses'] as $license)
                                <li>
                                    <div>

                                        <a href="{{ $url }}wp-admin/edit.php?post_type=download&amp;page=edd-licenses&amp;view=overview&amp;license_id={{ $license['id'] }}" target="_blank">{{ $license['name'] }}</a>
                                        <span class="pull-right">{{ $license['status'] }}</span>
                                    </div>
                                    <div>
                                     
                                        <small class="text-help">{{ $license['key'] }}</small>
                                      
                                    </div>
                                   
                                </li>
                            @endforeach
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
            @else
                <div class="text-help margin-top-10 wc-no-orders">{{ __("No orders found") }}</div>
            @endif
        @endif
        
        <div class="margin-top-10 wc-refresh small">
            <a href="#" class="sidebar-block-link"><i class="glyphicon glyphicon-refresh"></i> {{ __("Refresh") }}</a>
        </div>
      
    </div>
</div>
@endif
<style type="text/css">
    #wc-loader img{
        display: none;
    }
    .scrollbar 
    {
        margin-left: 0px !important;
        margin-right: 0px !important;
        float: left;
        height: auto;
        overflow-y: scroll;
        margin-bottom: 25px;
    }

    .force-overflow
    {
        min-height: 450px;
    }

    #style-2::-webkit-scrollbar-track
    {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        border-radius: 10px;
        background-color: #f4f8fd;
    }

    #style-2::-webkit-scrollbar
    {
        width: 12px;
        background-color: #f4f8fd;
    }

    #style-2::-webkit-scrollbar-thumb
    {
        border-radius: 10px;
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
        background-color: #f4f8fd;
    }

</style>
