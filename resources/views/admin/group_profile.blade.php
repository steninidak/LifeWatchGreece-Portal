<div class='row'>
    <div class='col-md-6'>
        {{ Form::label('group_name','Group name') }}
        {{ Form::text('group_name',$group->name,array('class'=>'form-control','disabled'=>'disabled')) }}    
    </div>
    <div class='col-md-2'>
        {{ Form::label('count_members','Total Members') }}
        {{ Form::text('count_members',$group->count_members,array('class'=>'form-control','disabled'=>'disabled')) }}   
    </div>
</div>      
<div class='row'>
    <div class='col-md-12'>
        {{ Form::label('description','Description') }}
        {{ Form::textarea('description',$group->description,array('class'=>'form-control','disabled'=>'disabled','rows'=>'3')) }}                 
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-6">
        <div>
            {{ Form::label('member_list','Member List') }}
            <div class="btn btn-primary btn-xs" style="float: right" data-toggle="modal" data-target="#addMemberDialog">Add member</div>
        </div>
        <table class='table table-bordered table-condensed'>
            <thead>
                <th>E-mail</th>
                <th>First name</th>
                <th>Last name</th>
                <th>Actions</th>
            </thead>
            <tbody>
                @foreach($members as $member)
                <tr>
                    <td>{{ link_to('user_management/profile/'.$member->id,$member->email) }}</td>
                    <td>{{ $member->firstname }}</td>
                    <td>{{ $member->lastname }}</td>
                    <td>
                        {{ Form::open(array('url'=>'admin/group_management/remove_member','name'=>'removeMemberForm'.$group->id,'id'=>'removeMemberForm'.$group->id)) }}
                            <input type="hidden" name="group_id" value="{{ $group->id }}">
                            <input type="hidden" name="user_id" value="{{ $member->id }}">
                            <label onclick="javascript:document.forms['removeMemberForm{{ $group->id }}'].submit();" class="linkStyle">Remove</label>
                        {{ Form::close() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        {{ Form::open(array('url'=>'admin/group_management/update_group_permissions','name'=>'updatePermissionsForm')) }}
        <input type="hidden" name="group_id" value="{{ $group->id }}" >
        <div>
            {{ Form::label('group_permissions','Group Permissions') }}     
            {{ Form::submit('Save Permissions',array('class'=>'btn btn-primary btn-xs','style'=>'float:right')) }}
        </div>
        <table class='table table-bordered table-condensed'>
            <thead>
                <th>Permission Name</th>
                <th>Granted to Group</th>
            </thead>
            <tbody>
                @foreach($permission_list as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td>
                        <input type="checkbox" name="{{ $permission->name.'_enabled' }}" value="{{ $permission->id }}"
                            @if(in_array($permission->id,$group_permission_ids)) 
                                checked='checked'
                            @endif
                        >
                    </td>                     
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ Form::close() }}
    </div>
</div>

<!-- Add member Modal -->
<div class="modal fade" id="addMemberDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cancel</span></button>
        <h4 class="modal-title" id="myModalLabel">Add member</h4>
      </div>
      <div class="modal-body">
        {{ Form::open(array('url'=>'admin/group_management/add_member','id'=>'addMemberForm')) }}
        <input type="hidden" name="group_id" value="{{ $group->id }}" >
        <table class="table borderless_td">
            <tr>
                <td style="width: 50%">
                    {{ Form::label('name','Group name') }}
                    {{ Form::text('name',$group->name,array('class'=>'form-control','disabled'=>'disabled')) }}                    
                </td>
                <td>                        
                    {{ Form::label('user_id','User') }}
                    <select name="user_id" style="max-width:350px" class="form-control">
                    @foreach($user_list as $user){
                            <option value='{{ $user->id }}'>{{ $user->fullname_email }}</option>
                    @endforeach                    
                </select>                    
                </td>
            </tr>
        </table>

        {{ Form::close() }}
        <div id="addMemberErrors" style="text-align: center; color: red;"></div>
      </div>
      <div class="modal-footer" style="margin-top:0px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="addMember()" id="addMemberButton">Submit</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    
function addMember(){

        /* get some values from elements on the page: */
        var form = $('#addMemberForm');
        var formURL = form.attr( 'action' );
        var postData = { 
                group_id: $('#addMemberForm input[name="group_id"]').val(), 
                user_id: $('#addMemberForm select[name="user_id"]').val(), 
                _token: $('#addMemberForm input[name="_token"]').val(), 
            };
        
        $.ajax(
        {
            url : formURL,
            type: "POST",
            data : postData,
            dataType : 'json',
            success:function(data, textStatus, jqXHR) 
            {
                location.href = '{{ url("admin/group_management/".$group->name) }}';
            },
            error: function(jqXHR, textStatus, errorThrown) 
            {
                switch (jqXHR.status) {
                    case 400: // Form validation failed
                        $('#addMemberErrors').empty();
                        response = JSON.parse(jqXHR.responseText);
                        var messages = "";
                        for(var key in response){
                            messages = messages+response[key]+"<br>";
                        }
                        $('#addMemberErrors').append(messages);
                        break;
                     case 401: // Unauthorized access
                        $('#addMemberErrors').empty();
                        $('#addMemberErrors').append("Unauthorized access!");
                        break;
                     case 500: // Unexpected error
                        $('#addMemberErrors').empty();                        
                        response = JSON.parse(jqXHR.responseText);
                        $('#addMemberErrors').append("<strong>Unexpected error:</strong> "+response.error);
                        break;
                }
            }
        });

}

</script>