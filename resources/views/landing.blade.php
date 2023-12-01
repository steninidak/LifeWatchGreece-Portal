@foreach($apps as $app)
    @if(!$app->hide_from_ui)
    <?php
        switch($app->status){
            case 'open':                   
                    echo "<div class='tool_info' onclick=\"javascript:location='$app->url'\">";
                break;
            case 'free':                   
                    echo "<div class='tool_info' onclick=\"javascript:location='$app->url'\">";
                break;
            case 'developing':                    
                    echo "<div class='tool_info'>";
                break;
            case 'controlled':                    
                    echo "<div class='tool_info' onclick=\"javascript:location='$app->url'\">";
                break;
        }
    ?>              
        <div class="tool_image_div">
            <img class="tool_image" src="{{ asset('images/apps/'.$app->image) }}" >
        </div>
        <div class="tool_text">
            <div class="tool_title">{{ $app->title }}</div>
            <div class="tool_description">{{ $app->description }}</div>
        </div>
            <?php
                switch($app->status){
                    case 'open':
                            echo "<div class='tool_status tool_status_open'>";
                            echo "Available without Sign In";
                            echo "</div>";
                        break;
                    case 'free':
                            echo "<div class='tool_status tool_status_free'>";
                            echo "Available after Sign In";
                            echo "</div>";
                        break;
                    case 'developing':
                            echo "<div class='tool_status tool_status_develop'>";
                            echo "Under development";
                            echo "</div>";
                        break;
                    case 'controlled':
                            echo "<div class='tool_status tool_status_signin'>";
                            echo "Special Permission required after Sign In";
                            echo "</div>";
                        break;
                }
            ?>           
        <div style="clear: both"></div>
    </div>
    @endif
@endforeach
