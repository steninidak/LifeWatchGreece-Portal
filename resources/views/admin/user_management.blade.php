<div style='text-align: right; margin-bottom: 10px'>
    <div class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUserDialog">Add new User</div>
</div>
<table class='table table-bordered table-condensed' id="usersTable">
    <thead>
        <th>First name</th>
        <th>Last name</th>
        <th>E-mail</th>
        <th>Status</th>
        <th>Registration Date</th>
        <th>Last login</th>
        <th>Actions</th>
    </thead>
    <tbody>
        @foreach($user_list as $user)
        <tr id="user{{ $user->id }}_row">
            <td>{{ $user->firstname }}</td>
            <td>{{ $user->lastname }}</td>
            <td>{{ $user->email }}</td>
            <td>
                @if($user->status == 'enabled')
                    <span class='glyphicon glyphicon-ok-sign' style='color:green'></span>
                @else
                    <span class='glyphicon glyphicon-minus-sign' style='color:red'></span>
                @endif
            </td>
            <td>{{ $user->created_at }}</td>
            <td>{{ $user->last_login }}</td>
            <td>
                {{ link_to('admin/user_management/edit/'.$user->id,'Edit') }}
                @if($user->status == 'enabled')
                    {{ link_to('admin/user_management/disable/'.$user->id,'Disable') }}
                @else
                    {{ link_to('admin/user_management/enable/'.$user->id,'Enable') }}
                @endif
                <div class="linkStyle" onclick="deleteUserDialog({{ $user->id }})">Delete</div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Add user Modal -->
<div class="modal fade" id="addUserDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cancel</span></button>
        <h4 class="modal-title" id="myModalLabel">Add new user</h4>
      </div>
      <div class="modal-body">
        {{ Form::open(array('url'=>'admin/user_management/add','id'=>'addUserForm')) }}

        <table class="table borderless_td">
            <tr>
                <td style="width: 50%">
                    {{ Form::label('email','E-mail') }}
                    {{ Form::text('email',null,array('class'=>'form-control','maxlength'=>$maxlengths['email'])) }}                    
                </td>
                <td>                        
                    {{ Form::label('password','Password') }}
                    {{ Form::password('password',array('class'=>'form-control','maxlength'=>$maxlengths['password'])) }}                    
                </td>
            </tr>
            <tr>
                <td>
                    {{ Form::label('verify_password','Verify Password') }}
                    {{ Form::password('verify_password',array('class'=>'form-control','maxlength'=>$maxlengths['verify_password'])) }}                   
                </td>
                <td>
                    {{ Form::label('firstname','First name') }}
                    {{ Form::text('firstname',null,array('class'=>'form-control','maxlength'=>$maxlengths['firstname'])) }}                   
                </td>
            </tr>
            <tr>
                <td>
                    {{ Form::label('lastname','Last name') }}
                    {{ Form::text('lastname',null,array('class'=>'form-control','maxlength'=>$maxlengths['lastname'])) }}                    
                </td>
                <td>
                    
                </td>
            </tr>
        </table>

        {{ Form::close() }}
        <div id="addUserErrors" style="text-align: center; color: red;"></div>
      </div>
      <div class="modal-footer" style="margin-top:0px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="createUser()" id="addUserButton">Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete user Modal -->
<div class="modal fade" id="deleteUserDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color: salmon">        
        <h4 class="modal-title" id="myModalLabel">User deletion</h4>
      </div>
      <div class="modal-body">
        {{ Form::open(array('url'=>'admin/user_management/delete','id'=>'deleteUserForm','name'=>'deleteUserForm')) }}

        <span class="label label-danger">Warning!</span> The user <span id="user_fullname" style="font-weight: bold"></span> with e-mail address <span id="user_email" style="font-weight: bold"></span> will be completely deleted and his permissions and 
        group participations will be revoked. 
        <input type="hidden" name="delete_user_id" value="">        

        {{ Form::close() }}        
      </div>
      <div class="modal-footer" style="margin-top:0px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="javascript:document.forms.deleteUserForm.submit();" id=deleteUserButton">Delete</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">    
    
function deleteUserDialog(userId){

    var firstname = $('#user'+userId+"_row td:nth-child(1)").text();
    var lastname = $('#user'+userId+"_row td:nth-child(2)").text();
    var email = $('#user'+userId+"_row td:nth-child(3)").text();
    $('input[name="delete_user_id"]').val(""+userId);
    $('#user_email').text(email);
    $('#user_fullname').text(firstname+" "+lastname);
    $('#deleteUserDialog').modal('show')
}    
    
function createUser(){

        /* get some values from elements on the page: */
        var form = $('#addUserForm');
        var formURL = form.attr( 'action' );
        var postData = { 
                firstname: $('#addUserForm input[name="firstname"]').val(), 
                lastname: $('#addUserForm input[name="lastname"]').val(), 
                email: $('#addUserForm input[name="email"]').val(),
                password: $('#addUserForm input[name="password"]').val(),
                verify_password: $('#addUserForm input[name="verify_password"]').val()
            };
        
        $.ajax(
        {
            url : formURL,
            type: "POST",
            data : postData,
            dataType : 'json',
            success:function(data, textStatus, jqXHR) 
            {
                location.href = '{{ url("admin/user_management") }}';
            },
            error: function(jqXHR, textStatus, errorThrown) 
            {
                switch (jqXHR.status) {
                    case 400: // Form validation failed
                        $('#addUserErrors').empty();
                        response = JSON.parse(jqXHR.responseText);
                        var messages = "";
                        for(var key in response){
                            messages = messages+response[key]+"<br>";
                        }
                        $('#addUserErrors').append(messages);
                        break;
                     case 401: // Unauthorized access
                        $('#addUserErrors').empty();
                        $('#addUserErrors').append("Unauthorized access!");
                        break;
                     case 500: // Unexpected error
                        $('#addUserErrors').empty();                        
                        $('#addUserErrors').append("<strong>Unexpected error!</strong> Please refresh the page and try again!");
                        break;
                }
            }
        });

}

</script>