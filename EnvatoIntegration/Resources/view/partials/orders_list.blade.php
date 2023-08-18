@if($orders !='') 
<div class="conv-sidebar-block">
    <div class="panel-group accordion accordion-empty">
        <div class="panel panel-default @if ($load) wc-loading @endif" id="wc-orders">
            
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" href=".wc-collapse-order-envato">
                        {{ __("Recent Orders") }}
                        <b class="caret recentOrder"></b>
                    </a>
                </h4>
            </div>
            <div class="wc-collapse-order-envato panel-collapse collapse in scrollbar" id="style-2" style="width: 100%;">
                <div class="panel-body force-overflow" style="background: #f4f8fd;">
                    <div class="sidebar-block-header2"><strong>{{ __("Recent Orders") }}</strong> (<a data-toggle="collapse" href=".wc-collapse-order-envato">{{ __('close') }}</a>)</div>
                    
                    <div id="wc-loader" >
                        <img src="{{ asset('img/loader-tiny.gif') }}" />
                    </div>
                    
                        @if($orders !='') 

                            <ul class="sidebar-block-list wc-orders-list" >
                               
                                <li>
                                    <div>
                                        <a href="#" target="_blank"> {{ $orders['item']['name'] }}</a>
                                    </div>
                                    <div class="pt-2">
                                        <span href="#" target="_blank"> {{ $orders['buyer'] }}</span>
                                        <span class="pull-right">
                                         <small>{{ \Carbon\Carbon::parse($orders['sold_at'])->format('M j, Y')  }}</small>
                                        </span>
                                    </div>
                                    <div class="pt-2">
                                        <span> {{ $orders['license'] }} <small>({{ $orders['purchase_count'] }})</small></span>
                                        <span class="pull-right">${{ $orders['amount'] }}</span>
                                    </div>
                                    <div class="pt-2">
                                        <span> Support Amount</span>
                                        <span class="pull-right">${{ $orders['support_amount'] }}</span>
                                    </div>
                                    <div class="pt-2">
                                        <span>Support Until</span>
                                        <span class="pull-right"><small>{{ ($orders['supported_until']=='') ?'Null':\Carbon\Carbon::parse($orders['supported_until'])->format('M j, Y') }}</small></span>
                                       
                                    </div>
                                </li>
                            </ul>
                           
                        @else
                            <div class="text-help margin-top-10 wc-no-orders">{{ __("No orders found") }}</div>
                        @endif
                  
                </div>
            </div>
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
    .pt-2{
        padding-top: 5px;
    }
    .recentOrder{
        float: right;
    }
</style>
