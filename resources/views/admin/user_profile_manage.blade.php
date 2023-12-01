<form class="form-horizontal">

    <div class='row'>
        
        <div class="col-md-6">
            <div style='text-align: center; margin-bottom: 30px'>
                <img src="{{ asset('images/user-info.png') }}" class="img-rounded">
            </div>
        </div>
    
        <div class="col-md-6">
            <div class="form-group">
                <label for="status" class="col-sm-4 control-label">Status</label>
                <div class="col-sm-8">
                  @if($user->status == 'enabled')
                    <span class='btn btn-sm btn-success'>Enabled</span>
                  @else
                    <span class='btn btn-sm btn-danger'>Disabled</span>
                  @endif
                </div>
            </div>
            
            <div class="form-group">
                <label for="email" class="col-sm-4 control-label">E-mail</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="email" value="{{ $user->email }}">
                </div>
            </div>
        </div>
        
    </div>
        
    <div class='row'>
        
        <div class="col-md-6">            
            
            <div class="form-group">
                <label for="firstname" class="col-sm-4 control-label">First name</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="firstname" value="{{ $user->firstname }}">
                </div>
            </div>
            
            <div class="form-group">
                <label for="lastname" class="col-sm-4 control-label">Last name</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="firstname" value="{{ $user->lastname }}">
                </div>
            </div>
                        
        </div>
        
        <div class="col-md-6">                        
            
            <div class="form-group">
                <label for="registration_date" class="col-sm-4 control-label">Registration Date</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="registration_date" value="{{ $user->created_at }}">
                </div>
            </div>

            <div class="form-group">
                <label for="last_login" class="col-sm-4 control-label">Last Login</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="last_login" value="{{ $user->last_login }}">
                </div>
            </div>
            
        </div>       
        
    </div>

</form>

<br>
<div class="row">
    <div class="col-md-6">
        <div>
            {{ Form::label('group_list','Group List') }}
            <div class="btn btn-primary btn-xs" style="float: right" data-toggle="modal" data-target="#addGroupDialog">Add group</div>
        </div>
        <table class='table table-bordered table-condensed'>
            <thead>
                <th>Name</th>
                <th>Actions</th>
            </thead>
            <tbody>
                @foreach($groups as $group)
                <tr>
                    <td>{{ link_to('admin/group_management/'.$group->name,$group->name) }}</td>
                    <td>
                        {{ Form::open(array('url'=>'admin/user_management/remove_group','name'=>'removeGroupForm'.$group->id,'id'=>'removeGroupForm'.$group->id)) }}
                            <input type="hidden" name="group_id" value="{{ $group->id }}">
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <label onclick="javascript:document.forms.removeGroupForm{{ $group->id }}.submit();" class="linkStyle">Remove</label>
                        {{ Form::close() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-md-6">
        {{ Form::open(array('url'=>'admin/user_management/update_user_permissions','name'=>'updatePermissionsForm')) }}
        <input type="hidden" name="user_id" value="{{ $user->id }}" >
        <div>
            {{ Form::label('user_permissions','User Permissions') }}     
            {{ Form::submit('Save Permissions',array('class'=>'btn btn-primary btn-xs','style'=>'float:right')) }}
        </div>
        <table class='table table-bordered table-condensed'>
            <thead>
                <th>Permission Name</th>
                <th>Granted to User</th>
            </thead>
            <tbody>
                @foreach($permission_list as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td>
                        <input type="checkbox" name="{{ $permission->name.'_enabled' }}" value="{{ $permission->id }}"
                            @if(in_array($permission->id,$user_permission_ids)) 
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

<!-- Add group Modal -->
<div class="modal fade" id="addGroupDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cancel</span></button>
        <h4 class="modal-title" id="myModalLabel">Add new group</h4>
      </div>
      <div class="modal-body">
        {{ Form::open(array('url'=>'admin/user_management/add_group','id'=>'addGroupForm')) }}

        <input type='hidden' name='user_id' value='{{ $user->id }}'>
        
        <table class="table borderless_td">
            <tr>
                <td>                        
                    {{ Form::label('group_name','Group name') }}
                    {{ Form::select('group_name', $rest_of_groups) }}                    
                </td>
            </tr>
        </table>

        {{ Form::close() }}
        <div id="addGroupErrors" style="text-align: center; color: red;"></div>
      </div>
      <div class="modal-footer" style="margin-top:0px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="javascript:document.forms.addGroupForm.submit()" id="addGroupButton">Add Group</button>
      </div>
    </div>
  </div>
</div>