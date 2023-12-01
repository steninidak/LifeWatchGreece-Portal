<div>
    <div style="float: left">
        All Announcements
    </div>
    <div style="float: right">
        {{ link_to('admin/announcements/add','New Announcement',array('class'=>'btn btn-primary btn-sm','style'=>'margin-bottom:7px')) }}
    </div>
</div>
<table class="table table-bordered">
    <thead>
        <th>Author</th>
        <th>Title</th>
        <th>Valid From</th>
        <th>Valid To</th>
        <th>Actions</th>
    </thead>
    <tbody>
        @foreach($announcements as $item)
        <tr>
            <td>{{ $item->author }}</td>  
            <td>{{ $item->title }}</td> 
            <td>{{ dateToTimezone($item->valid_from,$timezone) }}</td> 
            <td>{{ dateToTimezone($item->valid_to,$timezone) }}</td> 
            <td>
                {{ link_to('admin/announcements/edit/'.$item->id,'Edit') }}
                {{ Form::open(array('url'=>'admin/announcements/delete','name'=>'deleteForm'.$item->id,'style'=>'display:inline')) }}
                    <label onclick="javascript:document.deleteForm{{ $item->id }}.submit();" class="linkStyle">Delete</label>
                    <input type="hidden" name="announcement_id" value="{{ $item->id }}" >
                {{ Form::close() }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>