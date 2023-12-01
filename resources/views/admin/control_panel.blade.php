<?php
    $firstTrafficApp = NULL;
    foreach ($per_app_traffic as $key => $value) {
        $firstTrafficApp = $key;
        break;
    }
?>

<script type="text/javascript" src="{{ asset('js/Chart.min.js') }}"></script>

<div>

    <style type="text/css">           
        .log-table tr td {
            border: 1px solid #ddd;
            border-collapse: separate !important;
        }
        
        .log-table caption {
            font-weight: bold;
            color: blue;
        }
    </style>
            
</div>

<div>

    <div style="position: relative; padding: 0px 3px; top:9px; left:20px; display: inline; background-color: #f7f7f7; color: #8C8C8C; z-index: 100">Statistics: </div>
    <div style="border: 1px solid #D2D2D2; padding: 25px 25px 10px 25px">
        <div class="row">
            <div class="col-sm-6" style='text-align: center'>
                <div style='font-weight: bold; margin-bottom: 4px; color: #3182bd'>Number of registered users</div>
                <canvas id="registrationsChart" width="450" height="300"></canvas>
            </div>
            <div class="col-sm-6" style='text-align: center'>
                <div style='font-weight: bold; margin-bottom: 4px; color: #3182bd'>Total portal traffic (number of pages requests)</div>
                <canvas id="trafficChart" width="450" height="300"></canvas>
                <div style='text-align: right'><a href="{{ url('admin/traffic') }}" class='btn btn-default btn-sm' style="margin-top: 10px; margin-right: 15px">Check traffic origination</a></div>
            </div>
        </div>   
        <div class="row">
            <div class="col-sm-8" style='text-align: left'>
                <div id="perAppChartTitle" style='font-weight: bold; margin-bottom: 4px; color: #3182bd; text-align: center'>{{ $firstTrafficApp }} Traffic</div>
                <canvas id="appTrafficChart" width="600" height="300"></canvas>
            </div>
            <div class="col-sm-4" style='text-align: left'>
                <table class="table" style="text-align: left; margin-top: 100px">
                    <tr>
                        <td>Display monthly traffic for the selected app:</td>
                        <td>
                             <select class="form-control" id="appTrafficSelector">
                                @foreach(array_keys($per_app_traffic) as $appName)
                                <option>{{ $appName }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Total pages requests in last {{ $count_months }} months:</td>
                        <td>
                            <span style="background-color: #FCF8E3; color: #A66D3B; padding: 5px 10px" id="total_app_requests">
                                {{ array_sum($per_app_traffic[$firstTrafficApp]) }}
                            </span>
                        </td>
                    </tr>
                </table>                                                               
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12" style='text-align: left'>
                <div style='font-weight: bold; margin-bottom: 4px; color: #3182bd; text-align: center'>Number of unique visitors per application in the last {{ $count_months }} months</div>
                <canvas id="uniqueVisitorsChart" width="1000" height="300" style="margin-left: 15px"></canvas>
            </div>           
        </div>  
    </div>
    
    <div style="position: relative; padding: 0px 3px; top:9px; left:20px; display: inline; background-color: #f7f7f7; color: #8C8C8C; z-index: 100">Last week's logs: </div>
    <div style="border: 1px solid #D2D2D2; padding: 25px">
        <div class="row"style="margin-top: 30px">
            <div class="col-sm-4" style="text-align: center">                
                <div class="logCircleCaption">Error Logs</div>
                <a href="{{ url('admin/logs/error') }}">
                    @if($logs['error']['count'] == 0)
                        <div class="greenLogCircle">{{ $logs['error']['count'] }}</div>
                    @else
                        <div class="redLogCircle">{{ $logs['error']['count'] }}</div>
                    @endif                    
                </a>
                <div class="lastLogOn">Last on: <span style="color: #0088cc">{{ $logs['error']['last_on'] }}</span></div>
            </div>    
            <div class="col-sm-4" style="text-align: center">
                <div class="logCircleCaption">Security Logs</div>
                <a href="{{ url('admin/logs/security') }}">
                    @if($logs['security']['count'] == 0)
                        <div class="greenLogCircle">{{ $logs['security']['count'] }}</div>
                    @else
                        <div class="redLogCircle">{{ $logs['security']['count'] }}</div>
                    @endif  
                </a>
                <div class="lastLogOn">Last on: <span style="color: #0088cc">{{ $logs['security']['last_on'] }}</span></div>
            </div> 
            <div class="col-sm-4" style="text-align: center">
                <div class="logCircleCaption">Registrations</div>
                <a href="{{ url('admin/logs/registration') }}"> 
                    @if($logs['registration']['count'] == 0)
                        <div class="greenLogCircle">{{ $logs['registration']['count'] }}</div>
                    @else
                        <div class="redLogCircle">{{ $logs['registration']['count'] }}</div>
                    @endif  
                </a>
                <div class="lastLogOn">Last on: <span style="color: #0088cc">{{ $logs['registration']['last_on'] }}</span></div>
            </div> 
        </div>
    </div>        
    
    <div style="padding:2px 5px; margin: 40px 10px 10px 10px">
        <span style="font-weight: bold">Who is online:</span>
        @foreach($online_users as $user)
            <div style="color: #535657; display: inline-block; border: 1px solid #DCDCDC; padding: 3px 4px; border-radius: 3px; margin-left: 5px; margin-bottom: 1px;">{{ $user->email }}</div>
        @endforeach
    </div>
    
</div>

<script type="text/javascript">
        
        var appTrafficObject = {
            @foreach($per_app_traffic as $appName => $appTraffic)
                {{ $appName }} : [{{ implode(',',$appTraffic) }}],
            @endforeach
        };
        
        var registrationData = {
            labels: ["{!! implode('","',$registered_month) !!}"],
            datasets: [
                {
                    label: "My First dataset",
                    fillColor: "rgba(220,220,220,0.2)",
                    strokeColor: "rgba(220,220,220,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: [{{ implode(',',$registered_value) }}]
                }               
            ]
        };
        
        var trafficData = {
            labels: ["{!! implode('","',$registered_month) !!}"],
            datasets: [
                {
                    label: "My First dataset",
                    fillColor: "rgba(220,220,220,0.2)",
                    strokeColor: "rgba(220,220,220,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: [{{ implode(',',$total_traffic) }}]
                }               
            ]
        };                
        
        var appTrafficData = {
            labels: ["{!! implode('","',$registered_month) !!}"],
            datasets: [
                {
                    label: "My First dataset",
                    fillColor: "rgba(220,220,220,0.2)",
                    strokeColor: "rgba(220,220,220,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: appTrafficObject.{{ $firstTrafficApp }}
                }               
            ]
        };
        
        var uniqueVisitorsData = {
            labels: ["{!! implode('","',$unique_visitors['apps']) !!}"],
            datasets: [
                {
                    label: "My First dataset",
                    fillColor: "rgba(220,220,220,0.2)",
                    strokeColor: "rgba(220,220,220,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: [{{ implode(',',$unique_visitors['counts']) }}]
                }               
            ]
        };            
        
        var options = {};
        
        var registration_ctx = document.getElementById("registrationsChart").getContext("2d");
        var registrationChart = new Chart(registration_ctx).Line(registrationData, options);
        
        var traffic_ctx = document.getElementById("trafficChart").getContext("2d");
        var trafficChart = new Chart(traffic_ctx).Line(trafficData, options);
        
        var app_traffic_ctx = document.getElementById("appTrafficChart").getContext("2d");
        var appTrafficChart = new Chart(app_traffic_ctx).Line(appTrafficData, options);
        
        var unique_visitors_ctx = document.getElementById("uniqueVisitorsChart").getContext("2d");
        var uniqueVisitorsChart = new Chart(unique_visitors_ctx).Bar(uniqueVisitorsData, options);

        //
        $('#appTrafficSelector').on('change',function(){
            // Get selected app name
            var newApp = $(this).val();  
            // Update chart values
            for (index = 0, len = appTrafficObject[newApp].length; index < len; ++index) {
                appTrafficChart.datasets[0].points[index].value = appTrafficObject[newApp][index];
            }
            // Update the chart
            appTrafficChart.update();      
            
            // Update the total requests for this app
            var totalAppRequests = appTrafficObject[newApp].reduce(function(a, b){return a+b;});
            $('#total_app_requests').html(totalAppRequests);  
            // Update the chart title
            $('#perAppChartTitle').html(newApp+" traffic");
        });
</script>