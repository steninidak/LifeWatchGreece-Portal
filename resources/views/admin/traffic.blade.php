
<script type="text/javascript" src="{{ asset('js/jsapi.js') }}"></script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["geochart"]});
  google.setOnLoadCallback(drawRegionsMap);

  function drawRegionsMap() {

    var data = google.visualization.arrayToDataTable([
      ['Country', 'Page requests'],
      @foreach($countries as $country)
        ["{{ $country->country }}", {{ $country->requests }}],
      @endforeach
    ]);

    var options = {
        colorAxis: {colors: ['#9CD1B3',  '#003300']}    // green 
        //colorAxis: {colors: ['#FFFF99','#FF6600','#FF0000']}  // yellow to red
    };

    var chart = new google.visualization.GeoChart(document.getElementById('traffic_map'));

    chart.draw(data, options);
  }
</script>
<div style="text-align: right">
    <select class="form-control" id="appSelector" style="margin-bottom: 10px; width: 200px">
        <option value="">Portal</option>
        @foreach($apps_with_traffic as $appName)
        <option value="{{ $appName }}">{{ $appName }}</option>
        @endforeach
    </select>
</div>
<div style='font-weight: bold; margin-bottom: 4px; color: #3182bd; text-align: center'>Traffic origination for {{ (empty($app_name)) ? "Portal" : $app_name }}</div>
<div id="traffic_map" style="width: 100%; height: 800px;"></div>
 
<script type="text/javascript">
    $('#appSelector').on('change',function(){
        // Get selected app name
        var newApp = $(this).val();  
        
        window.location = "{{ url('admin/traffic/') }}"+newApp;
    });
</script>