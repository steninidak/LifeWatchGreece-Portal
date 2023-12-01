<a href="{{ url('admin') }}" style="margin:10px 0px 30px 20px">Back to Control Panel</a>

<div class="row" style="margin-top: 30px">
    <div class="col-sm-12">
        <table class="table table-bordered table-condensed log-table">                
                <thead>
                    <th>When</th>
                    <th>Message</th>
                </thead>
                <tbody>
                    @foreach($security_logs as $log)
                        <tr>
                            <td>{{ $log->when }}</td>
                            <td style="text-align: left">{{ $log->message }}</td>
                        </tr>
                    @endforeach                    
                </tbody>
            </table>
    </div>   
</div>
   