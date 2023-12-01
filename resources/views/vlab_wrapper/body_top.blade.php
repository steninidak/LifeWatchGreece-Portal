<div class="container">
   
    @include('internal_wrapper.menu_bar')
    
    @if((!empty($application_name))&&($application_name == 'medobis'))
        <div class="panel panel-default" id='main_area_div'>        
    @else
        <div class="panel panel-default">
    @endif        
        <div class="panel-heading">
            <a href="{{ $app->url }}">
                <img src='{{ asset('images/apps_toolbar/'.$app->toolbar_image) }}' class='vlab_logo'><div class="textlink">{{ $app->title }}</div>
            </a>
        </div>
        <div class="panel-body">
 
        