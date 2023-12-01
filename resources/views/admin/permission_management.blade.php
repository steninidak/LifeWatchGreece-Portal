<div style='text-align: right; margin-bottom: 10px'>
    <div class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addPermissionDialog">Add new Persmission</div>
</div>
<table class='table table-bordered table-condensed'>
    <thead>
        <th>Name</th>
        <th>Type</th>
        <th>Description</th>
        <th>Actions</th>
    </thead>
    <tbody>
        @foreach($permissions as $permission)
        <tr id="permission{{ $permission->id }}_row">
            <td>{{ $permission->name }}</td>
            <td>{{ $permission->type }}</td>
            <td>{{ $permission->description }}</td>
            <td>
                @if($permission->type != 'fixed')
                    {{ link_to('admin/permission_management/edit/'.$permission->id,'Edit') }}
                    <div class="linkStyle" onclick="deletePermissionDialog({{ $permission->id }})">Delete</div>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Add permission Modal -->
<div class="modal fade" id="addPermissionDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cancel</span></button>
        <h4 class="modal-title" id="myModalLabel">Add new permission</h4>
      </div>
      <div class="modal-body">
        {{ Form::open(array('url'=>'admin/permission_management/add','id'=>'addPermissionForm')) }}

        <table class="table borderless_td">
            <tr>
                <td style="width: 50%">
                    {{ Form::label('pname','Permission name') }}
                    {{ Form::text('pname',null,array('class'=>'form-control','maxlength'=>$maxlengths['pname'])) }}                    
                </td>
                <td>                        
                    {{ Form::label('used_by','Used by') }}
                    {{ Form::select('used_by', $app_list, 'core',array('class'=>'form-control')) }}                    
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    {{ Form::label('description','Description') }}
                    {{ Form::text('description',null,array('class'=>'form-control','maxlength'=>$maxlengths['description'])) }}                   
                </td>            
            </tr>
        </table>

        {{ Form::close() }}
        <div id="addPermissionErrors" style="text-align: center; color: red;"></div>
      </div>
      <div class="modal-footer" style="margin-top:0px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="createPermission()" id="addPermissionButton">Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete permission Modal -->
<div class="modal fade" id="deletePermissionDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color: salmon">        
        <h4 class="modal-title" id="myModalLabel">Permission deletion</h4>
      </div>
      <div class="modal-body">
        {{ Form::open(array('url'=>'admin/permission_management/delete','id'=>'deletePermissionForm','name'=>'deletePermissionForm')) }}

        <span class="label label-danger">Warning!</span> The permission <span id="permission_name" style="font-weight: bold"></span> will be completely deleted and be 
        revoked from all users and groups. 
        <input type="hidden" name="delete_permission_id" value="">        

        {{ Form::close() }}        
      </div>
      <div class="modal-footer" style="margin-top:0px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="javascript:document.forms.deletePermissionForm.submit();" id="deleteGroupButton">Delete</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    
function deletePermissionDialog(permissionId){

    var permission_name = $('#permission'+permissionId+"_row td:nth-child(1)").text();
    $('input[name="delete_permission_id"]').val(""+permissionId);
    $('#permission_name').text(permission_name);
    $('#deletePermissionDialog').modal('show')
}         
    
function createPermission(){

        /* get some values from elements on the page: */
        var form = $('#addPermissionForm');
        var formURL = form.attr( 'action' );
        var postData = { 
                pname: $('#addPermissionForm input[name="pname"]').val(), 
                description: $('#addPermissionForm input[name="description"]').val(), 
                used_by: $('#addPermissionForm select[name="used_by"]').val(),
                _token: $('#addPermissionForm input[name="_token"]').val(), 
            };
        
        $.ajax(
        {
            url : formURL,
            type: "POST",
            data : postData,
            dataType : 'json',
            success:function(data, textStatus, jqXHR) 
            {
                location.href = '{{ url("admin/permission_management") }}';
            },
            error: function(jqXHR, textStatus, errorThrown) 
            {
                switch (jqXHR.status) {
                    case 400: // Form validation failed
                        $('#addPermissionErrors').empty();
                        response = JSON.parse(jqXHR.responseText);
                        var messages = "";
                        for(var key in response){
                            messages = messages+response[key]+"<br>";
                        }
                        $('#addPermissionErrors').append(messages);
                        break;
                     case 401: // Unauthorized access
                        $('#addPermissionErrors').empty();
                        $('#addPermissionErrors').append("Unauthorized access!");
                        break;
                     case 500: // Unexpected error
                        $('#addPermissionErrors').empty();                        
                        $('#addPermissionErrors').append("<strong>Unexpected error:</strong> Please refresh the page and try again!");
                        break;
                }
            }
        });

}

</script>