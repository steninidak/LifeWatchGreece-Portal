<div>
    <div style="float: left">
        Message List
    </div>
    <div style="float: right">
        {{ link_to('admin/ome_time_messages/add','New message',array('class'=>'btn btn-primary btn-sm','style'=>'margin-bottom:7px')) }}
    </div>
</div>
<table class="table table-bordered">
    <thead>
        <th>Author</th>
        <th>Type</th>
        <th>Body</th>
        <th>Created at</th>
        <th>Actions</th>
    </thead>
    <tbody>
        @foreach($messages as $item)
        <tr>
            <td>{{ $item->email }}</td>  
            <td>{{ $item->type }}</td> 
            <td>{{ $item->body }}</td> 
            <td>{{ dateToTimezone($item->created_at,$timezone) }}</td> 
            <td>
                {{ link_to('admin/ome_time_messages/edit/'.$item->id,'Edit') }}
                {{ Form::open(array('url'=>'admin/ome_time_messages/delete','name'=>'deleteForm'.$item->id,'style'=>'display:inline')) }}
                    <label onclick="javascript:document.deleteForm{{ $item->id }}.submit();" class="linkStyle">Delete</label>
                    <input type="hidden" name="message_id" value="{{ $item->id }}" >
                {{ Form::close() }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
