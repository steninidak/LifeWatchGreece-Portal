<div class="container">
    
    @include('external_wrapper.menu_bar')
    
    <div style='position: relative'>
        <div style="color: #16737b; font-size: 12px; margin-bottom: 2px; position: absolute; top: -16px;">Institute of Marine Biology, Biotechnology and Aquaculture - HCMR</div>
    </div>    
    
   <div class="panel panel-default" style='position: relative; top: 4px;'>
        <div class="panel-heading">
            @if(isset($app))                
                <span style='font-size: 16px; font-weight: bold'>
                    @if(isset($app->toolbar_image))
                        <img src='{{ asset('images/apps_toolbar/'.$app->toolbar_image) }}' class='vlab_logo'>
                    @endif
                    @if(isset($app->title))
                        {{ $app->title }}
                    @endif
                </span>                                            
            @else
                <span style='font-size: 16px; font-weight: bold'></span>
            @endif
        </div>
        <div class="panel-body">
    