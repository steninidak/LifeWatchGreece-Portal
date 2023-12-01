
<div class="row">
    <div class="col-md-2">                       
        <img src="{{ asset('images/one_time_message.png') }}" >
    </div>
    <div class="col-md-10">
        {{ Form::open(array('url'=>'admin/ome_time_messages/add',)) }}

        <div class="row">
            <div class="col-md-3">
                {{ Form::label('type','Message type') }}
                <select name="type" class="form-control">
                    <option value="info">Informational (blue)</option>
                    <option value="danger">Important/Urgent (red)</option>
                </select>              
                {{ $errors->first('type',"<span style='color:red'>:message</span>") }}
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                {{ Form::label('body','Message Text') }}
                {{ Form::textarea('body',Input::old('body'),array('class'=>'form-control')) }} 
                {{ $errors->first('body',"<span style='color:red'>:message</span>") }}
            </div>
        </div>              
        <div style="text-align: center">
            <button type="submit" class="btn btn-primary">Save Message</button>
            <a href="{{ url('admin/ome_time_messages') }}" class="btn btn-default">Go Back to List</a>
        </div>
        {{ Form::close() }}
    </div>    
</div>