<script type="text/javascript" src="{{ asset('js/highcharts.js') }}"></script>

<div class="panel panel-primary">
  <div class="panel-heading"><strong>Utilization Statistics</strong></div>
  <div class="panel-body">
    <div id="utilization_container" style="height: 500px"></div>
  </div>
</div>

<div class="panel panel-primary">
  <div class="panel-heading"><strong>Queue Length statistics<strong></div>
  <div class="panel-body">

	<div class='row' style='margin-bottom:20px'>
		<div class="col-md-9" style='text-align:right; padding-top:6px; color: red'>Select user:</div>
		<div class="col-md-3">
			 <form autocomplete="off">
		            <select class="form-control" id="userSelector">
		                <option value='total' selected="selected">Total (all users)</option>
		                @foreach($user_list as $user)
		                    <option>{{ $user }}</option>
		                @endforeach
		            </select>
		        </form>

		</div>
	</div>

    	<div id="fast_container" style="height: 500px"></div>
	<div id="batch_container" style="height: 500px"></div>
	<div id="bigmem_container" style="height: 500px"></div>
  </div>
</div>

<script type="text/javascript">

    $('#userSelector').on('change',function(){

        var user = $(this).val(); // Get the selected user

        if(user == 'total'){
            // Get the fast queue chart object
            var fastChart = $('#fast_container').highcharts();
            // Delete user line from fast queue chart, if exists
            var fastSeriesLength = fastChart.series.length; // get the number of user lines on graph          
            for(var i = fastSeriesLength - 1; i > -1; i--) {                 
                if(fastChart.series[i].name != 'total')
                    fastChart.series[i].remove();
            }
            
            // Get the batch queue chart object
            var batchChart = $('#batch_container').highcharts();
            // Delete user line from batch queue chart, if exists
            var batchSeriesLength = batchChart.series.length;
            for(var i = batchSeriesLength - 1; i > -1; i--) {                
                if(batchChart.series[i].name != 'total')
                    batchChart.series[i].remove();
            }
            
            // Get the bigmem queue chart object
            var bigmemChart = $('#bigmem_container').highcharts();
            // Delete user line from batch queue chart, if exists
            var bigmemSeriesLength = bigmemChart.series.length;
            for(var i = bigmemSeriesLength - 1; i > -1; i--) {                
                if(bigmemChart.series[i].name != 'total')
                    bigmemChart.series[i].remove();
            }
        } else {
            // Load user data        
            $.ajax({
                url : "{{ url('admin/biocluster/user') }}"+'/'+user,
                type: "GET",
                dataType : 'json',
                success:function(jdata, textStatus, jqXHR) {
                    // Get the fast queue chart object
                    var fastChart = $('#fast_container').highcharts();
                    // Delete user line from fast queue chart, if exists
                    var fastSeriesLength = fastChart.series.length;
                    for(var i = fastSeriesLength - 1; i > -1; i--) {
                        //chart.series[i].remove();
                        if(fastChart.series[i].name != 'total')
                            fastChart.series[i].remove();
                    }
                    // Load the user series on fast queue chart
                    fastChart.addSeries({
                        name: user,
                        data: jdata.fast,
                        pointStart: Date.UTC(jdata.fast_start_year,jdata.fast_start_month-1,jdata.fast_start_day),
                        pointInterval: 1800*1000 // 24 * 3600 * 1000 = 24 hours
                    });

                    // Get the batch queue chart object
                    var batchChart = $('#batch_container').highcharts();
                    // Delete user line from batch queue chart, if exists
                    var batchSeriesLength = batchChart.series.length;
                    for(var i = batchSeriesLength - 1; i > -1; i--) {
                        //chart.series[i].remove();
                        if(batchChart.series[i].name != 'total')
                            batchChart.series[i].remove();
                    }
                    // Load the user series on batch queue chart
                    batchChart.addSeries({
                        name: user,
                        data: jdata.batch,
                        pointStart: Date.UTC(jdata.batch_start_year,jdata.batch_start_month-1,jdata.batch_start_day),
                        pointInterval: 1800*1000 // 24 * 3600 * 1000 = 24 hours
                    });

                    // Get the batch queue chart object
                    var bigmemChart = $('#bigmem_container').highcharts();
                    // Delete user line from batch queue chart, if exists
                    var bigmemSeriesLength = bigmemChart.series.length;
                    for(var i = bigmemSeriesLength - 1; i > -1; i--) {
                        //chart.series[i].remove();
                        if(bigmemChart.series[i].name != 'total')
                            bigmemChart.series[i].remove();
                    }
                    // Load the user series on batch queue chart
                    bigmemChart.addSeries({
                        name: user,
                        data: jdata.bigmem,
                        // Note: Date.UTC expects month to be between 0 and 11
                        pointStart: Date.UTC(jdata.bigmem_start_year,jdata.bigmem_start_month-1,jdata.bigmem_start_day),
                        pointInterval: 1800*1000 // 24 * 3600 * 1000 = 24 hours
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Data series could not be loaded!');
                }
            });
        }                

    });

    $('#fast_container').highcharts({
    
        chart: {
            type: 'area'
        },
        title: {
            text: 'Fast Queue Length'
        },
        subtitle: {
            text: 'Number of jobs in the queue (running or waiting to run)'
        },
        tooltip: {
            pointFormat: "Value: {point.y:,.1f} mm"
        },

        xAxis: {
            type: 'datetime',
            labels: {
                format: '{value:%Y-%m-%d}',
                rotation: 45,
                align: 'left'
            }
        },
        yAxis: {
            min: 0
        },
        plotOptions: {
            line: {
                connectNulls: false // we cannot set missing points to zero, but we don't want to connect the points around missing values
            },
            series: {
                fillOpacity: 0.5
            }
        },

        series: [{
            name: 'total',
            data: [{{ $total_fast }}],
            pointStart: Date.UTC({{ $fast_start_year }}, {{ $fast_start_month-1 }} , {{ $fast_start_day }}),
            pointInterval: 1800*1000 // 24 * 3600 * 1000 = 24 hours
        }]

    });
    
    $('#batch_container').highcharts({
    
        chart: {
            type: 'area'
        },
        title: {
            text: 'Batch Queue Length'
        },
        subtitle: {
            text: 'Number of jobs in the queue (running or waiting to run)'
        },
        tooltip: {
            pointFormat: "Value: {point.y:,.1f} mm"
        },
        xAxis: {
            type: 'datetime',
            labels: {
                format: '{value:%Y-%m-%d}',
                rotation: 45,
                align: 'left'
            }
        },
        yAxis: {
            min: 0
        },        
        plotOptions: {
            line: {
                connectNulls: false // we cannot set missing points to zero, but we don't want to connect the points around missing values
            },
            series: {
                fillOpacity: 0.5
            }
        },

        series: [{
            name: 'total',
            data: [{{ $total_batch }}],
            pointStart: Date.UTC({{ $batch_start_year }}, {{ $batch_start_month-1 }} , {{ $batch_start_day }}),
            pointInterval: 1800*1000 // (in milliseconds) 24 * 3600 * 1000 = 24 hours
        }]

    });
    
    $('#bigmem_container').highcharts({
    
        chart: {
            type: 'area'
        },
        title: {
            text: 'Bigmem Queue Length'
        },
        subtitle: {
            text: 'Number of jobs in the queue (running or waiting to run)'
        },
        tooltip: {
            pointFormat: "Value: {point.y:,.1f} mm"
        },
        xAxis: {
            type: 'datetime',
            labels: {
                format: '{value:%Y-%m-%d}',
                rotation: 45,
                align: 'left'
            }
        },
        yAxis: {
            min: 0
        },        
        plotOptions: {
            line: {
                connectNulls: false // we cannot set missing points to zero, but we don't want to connect the points around missing values
            },
            series: {
                fillOpacity: 0.5
            }
        },

        series: [{
            name: 'total',
            data: [{{ $total_bigmem }}],
            pointStart: Date.UTC({{ $bigmem_start_year }}, {{ $bigmem_start_month-1 }} , {{ $bigmem_start_day }}),
            pointInterval: 1800*1000 // 24 * 3600 * 1000 = 24 hours
        }]

    });
    
    $('#utilization_container').highcharts({
    
        chart: {
            type: 'line'
        },
        title: {
            text: 'Queue Resources Utilization'
        },
        subtitle: {
            text: 'Percentage of reserved CPUs in each queue'
        },
        tooltip: {
            pointFormat: "Value: {point.y:,.1f} mm"
        },
        xAxis: {
            type: 'datetime',
            labels: {
                format: '{value:%Y-%m-%d}',
                rotation: 45,
                align: 'left'
            }
        },
        yAxis: {
            min: 0
        },        
        plotOptions: {
            line: {
                connectNulls: false // we cannot set missing points to zero, but we don't want to connect the points around missing values
            }
        },

        series: [{
            name: 'fast_utilization',
            data: [{{ $fast_utilization }}],
            pointStart: Date.UTC({{ $fast_util_year }}, {{ $fast_util_month-1 }} , {{ $fast_util_day }}),
            pointInterval: 1800*1000 // 24 * 3600 * 1000 = 24 hours
        },{
            name: 'batch_utilization',
            data: [{{ $batch_utilization }}],
            pointStart: Date.UTC({{ $batch_util_year }}, {{ $batch_util_month-1 }} , {{ $batch_util_day }}),
            pointInterval: 1800*1000 // 24 * 3600 * 1000 = 24 hours
        },{
            name: 'bigmem_utilization',
            data: [{{ $bigmem_utilization }}],
            pointStart: Date.UTC({{ $bigmem_util_year }}, {{ $bigmem_util_month-1 }} , {{ $bigmem_util_day }}),
            pointInterval: 1800*1000 // 24 * 3600 * 1000 = 24 hours
        }]

    });
    
</script>


