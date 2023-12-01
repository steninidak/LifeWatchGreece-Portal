
<div class="row">
    <div class="col-md-2">                       
        <img src="{{ asset('images/announcement.png') }}" >
    </div>
    <div class="col-md-10">
        {{ Form::open(array('url'=>'admin/announcements/edit',)) }}

        <input type="hidden" name="announcement_id" value="{{ $announcement->id }}">
        
        <div class="row">
            <div class="col-md-8">
                {{ Form::label('title','Title') }}
                {{ Form::text('title',Input::old('title',$announcement->title),array('class'=>'form-control')) }} 
                {{ $errors->first('title',"<span style='color:red'>:message</span>") }}
            </div>
            <div class="col-md-4">
               {{ Form::label('valid_from','Active From') }}
                 <div class='input-group date' id='datetimepicker1'>
                    <input type='text' class="form-control" name="valid_from"/>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                {{ $errors->first('valid_from',"<span style='color:red'>:message</span>") }}
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                {{ Form::label('body','Announcement Text') }}
                {{ Form::textarea('body',Input::old('body',$announcement->body),array('class'=>'form-control')) }} 
                {{ $errors->first('body',"<span style='color:red'>:message</span>") }}
            </div>
            <div class="col-md-4">
                {{ Form::label('valid_to','Active To') }}
                 <div class='input-group date' id='datetimepicker2'>
                    <input type='text' class="form-control" name="valid_to" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                {{ $errors->first('valid_to',"<span style='color:red'>:message</span>") }}
            </div>
        </div>              
        <div style="text-align: center">
            <button type="submit" class="btn btn-primary">Save Announcement</button>
            <a href="{{ url('admin/announcements/all') }}" class="btn btn-default">Go Back to List</a>
        </div>
        {{ Form::close() }}
    </div>    
</div>

<script type="text/javascript">

    $(function () {
        $('#datetimepicker1').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
            defaultDate: new Date("{{ (new DateTime($announcement->valid_from))->format('Y/m/d H:i') }}")
        });
        $('#datetimepicker2').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
            defaultDate: new Date("{{ (new DateTime($announcement->valid_to))->format('Y/m/d H:i') }}")
        });
        $("#datetimepicker1").on("dp.change",function (e) {
            $('#datetimepicker1').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker2").on("dp.change",function (e) {
            $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
        });                     
    });
</script>