<div style='text-align: right; margin-bottom: 10px'>
    <div class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addGroupDialog">Add new Group</div>
</div>
<table class='table table-bordered table-condensed'>
    <thead>
        <th>Name</th>
        <th>Description</th>
        <th>Count members</th>
        <th>Actions</th>
    </thead>
    <tbody>
        @foreach($groups as $group)
        <tr id="group{{ $group->id }}_row">
            <td>{{ link_to('admin/group_management/'.$group->name,$group->name) }}</td>
            <td>{{ $group->description }}</td>
            <td>{{ $group->count_members }}</td>
            <td>
                <div class="linkStyle" onclick="deleteGroupDialog({{ $group->id }})">Delete</div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Add group Modal -->
<div class="modal fade" id="addGroupDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cancel</span></button>
        <h4 class="modal-title" id="myModalLabel">Add new group</h4>
      </div>
      <div class="modal-body">
        {{ Form::open(array('url'=>'admin/group_management/add','id'=>'addGroupForm')) }}

        <table class="table borderless_td">
            <tr>
                <td style="width: 50%">
                    {{ Form::label('name','Group name') }}
                    {{ Form::text('name',null,array('class'=>'form-control','maxlength'=>$maxlengths['name'])) }}                    
                </td>
                <td>                        
                    {{ Form::label('description','Description') }}
                    {{ Form::text('description',null,array('class'=>'form-control','maxlength'=>$maxlengths['description'])) }}                    
                </td>
            </tr>
        </table>

        {{ Form::close() }}
        <div id="addGroupErrors" style="text-align: center; color: red;"></div>
      </div>
      <div class="modal-footer" style="margin-top:0px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="createGroup()" id="addGroupButton">Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete group Modal -->
<div class="modal fade" id="deleteGroupDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color: salmon">        
        <h4 class="modal-title" id="myModalLabel">Group deletion</h4>
      </div>
      <div class="modal-body">
        {{ Form::open(array('url'=>'admin/group_management/delete','id'=>'deleteGroupForm','name'=>'deleteGroupForm')) }}

        <span class="label label-danger">Warning!</span> The group <span id="group_fullname" style="font-weight: bold"></span> will be completely deleted and his permissions and 
        user memberships will be revoked. 
        <input type="hidden" name="delete_group_id" value="">        

        {{ Form::close() }}        
      </div>
      <div class="modal-footer" style="margin-top:0px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="javascript:document.forms.deleteGroupForm.submit();" id="deleteGroupButton">Delete</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    
function deleteGroupDialog(groupId){

    var group_name = $('#group'+groupId+"_row td:nth-child(1)").text();
    $('input[name="delete_group_id"]').val(""+groupId);
    $('#group_fullname').text(group_name);
    $('#deleteGroupDialog').modal('show')
}     
    
function createGroup(){

        /* get some values from elements on the page: */
        var form = $('#addGroupForm');
        var formURL = form.attr( 'action' );
        var postData = { 
                name: $('#addGroupForm input[name="name"]').val(), 
                description: $('#addGroupForm input[name="description"]').val(), 
                _token: $('#addGroupForm input[name="_token"]').val(), 
            };
        
        $.ajax(
        {
            url : formURL,
            type: "POST",
            data : postData,
            dataType : 'json',
            success:function(data, textStatus, jqXHR) 
            {
                location.href = '{{ url("admin/group_management") }}';
            },
            error: function(jqXHR, textStatus, errorThrown) 
            {
                switch (jqXHR.status) {
                    case 400: // Form validation failed
                        $('#addGroupErrors').empty();
                        response = JSON.parse(jqXHR.responseText);
                        var messages = "";
                        for(var key in response){
                            messages = messages+response[key]+"<br>";
                        }
                        $('#addGroupErrors').append(messages);
                        break;
                     case 401: // Unauthorized access
                        $('#addGroupErrors').empty();
                        $('#addGroupErrors').append("Unauthorized access!");
                        break;
                     case 500: // Unexpected error
                        $('#addGroupErrors').empty();                                                
                        $('#addGroupErrors').append("<strong>Unexpected error!</strong> Please refresh the page and try again!");
                        break;
                }
            }
        });

}

</script>