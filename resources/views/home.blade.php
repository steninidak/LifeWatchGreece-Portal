<div class="row">
    <div class="col-md-9">              
        @foreach($one_time_messages as $message)
            <div class="alert alert-{{ $message->type }} alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                @if($message->type == 'info')
                <strong>News:</strong> 
                @endif
                @if($message->type == 'danger')
                <strong>Notification:</strong> 
                @endif                
                {{ $message->body }}
            </div>
        @endforeach
        <div style='text-align: center'>            
            @foreach($apps as $app)
                @if(!$app->hide_from_ui)
                    @if(!empty($app->url))
                        <!-- If the app has controlled access and the user has not this permission -->
                        @if(($app->status == 'controlled')&&(!in_array($app->codename,$user_permissions)))
                            <div class="inactive_speed_button2">
                                @if(empty($app->image))
                                    <img src="{{ asset('images/edit_app.png') }}" />
                                @else
                                    <img src="{{ asset('images/apps/'.$app->image) }}" />
                                @endif   
                                <div class="speed_text2">{{ $app->title }}</div>
                            </div> 
                        <!-- If the app is under development and the user hasn't got the 'access_unfinished_apps' permission -->
                        @elseif(($app->status == 'developing')&&(!in_array('access_unfinished_apps',$user_permissions)))
                            <div class="inactive_speed_button2">
                                @if(empty($app->image))
                                    <img src="{{ asset('images/edit_app.png') }}" />
                                @else
                                    <img src="{{ asset('images/apps/'.$app->image) }}" />
                                @endif   
                                <div class="speed_text2">{{ $app->title }}</div>
                            </div>
                        <!-- In any other case, including free and open access -->
                        @else
                            <a class="speed_button2" href="{{ $app->url }}">
                                @if(empty($app->image))
                                    <img src="{{ asset('images/edit_app.png') }}" />
                                @else
                                    <img src="{{ asset('images/apps/'.$app->image) }}" />
                                @endif                        
                                <div class="speed_text_blue">{{ $app->title }}</div>
                            </a>
                        @endif                    
                    @else
                        <div class="inactive_speed_button2">
                            @if(empty($app->image))
                                <img src="{{ asset('images/edit_app.png') }}" />
                            @else
                                <img src="{{ asset('images/apps/'.$app->image) }}" />
                            @endif   
                            <div class="speed_text2">{{ $app->title }}</div>
                        </div>
                    @endif 
                @endif
            @endforeach            
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-info">
            <div class="panel-heading">
                <span style="font-weight: bold">Announcements</span>
                <div style="float: right">
                    <span class="glyphicon glyphicon-volume-up" aria-hidden="true"></span>
                </div>
            </div>
            <div class="panel-body">
                @if(empty($announcements))
                    No announcement found!
                @else
                    @foreach($announcements as $item)
                    <div class="announcement_title">
                        {{ $item->title }}
                        <div class="announcement_date" style="float: right">{{ dateToTimezone($item->created_at,$timezone) }}</div>
                    </div>
                    <div class="announcement_body">{{ $item->body }}</div>
                    <hr>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
