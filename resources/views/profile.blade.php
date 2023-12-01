<form class="form-horizontal">

    <div class='row'>
        
        <div class="col-md-6">
            <div style='text-align: center; margin-bottom: 30px'>
                <img src="{{ asset('images/user-info.png') }}" class="img-rounded">
            </div>
        </div>
        
        <div class="col-md-6">
            
        </div>
        
    </div>
    
    <div class='row'>
        
        <div class="col-md-6">            
            
            <div class="form-group">
                <label for="firstname" class="col-sm-4 control-label">First name</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="firstname" value="{{ $user->firstname }}" disabled>
                </div>
            </div>
            
            <div class="form-group">
                <label for="lastname" class="col-sm-4 control-label">Last name</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="firstname" value="{{ $user->lastname }}" disabled>
                </div>
            </div>
            
            <div class="form-group">
                <label for="registration_date" class="col-sm-4 control-label">Registration Date</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="registration_date" value="{{ $user->created_at }}" disabled>
                </div>
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
                  <input type="text" class="form-control" id="email" value="{{ $user->email }}" disabled>
                </div>
            </div>
 
            <div class="form-group">
                <label for="last_login" class="col-sm-4 control-label">Last Login</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="last_login" value="{{ $user->last_login }}" disabled>
                </div>
            </div>
            
        </div>

        
    </div>

</form>
    
    <div style="margin:20px 10px 0px 10px; font-weight: bold; color: gray">
        UPDATE PROFILE INFO        
    </div>
    <hr style="color: gray; margin: 6px 10px; border-width: 2px">
    <div id="profileInfo" style="margin:15px 0px; background-color: white">
        
        <div class='row'>
        
            {{ Form::open(array('url' => 'profile', 'method' => 'post','class'=>'form-horizontal')) }}
            <form action="{{ url('profile') }}" method="post" class="form-horizontal">
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="affiliation" class="col-sm-4 control-label">Affiliation</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="affiliation" name="affiliation" value="{{ $user->affiliation }}">
                      {{ $errors->first('affiliation',"<span style='color:red'>:message</span>") }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="position" class="col-sm-4 control-label">Position</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="position" name="position" value="{{ $user->position }}">
                      {{ $errors->first('position',"<span style='color:red'>:message</span>") }}
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="timezone" class="col-sm-4 control-label">Timezone</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="timezone" name="timezone">
                            @foreach($timezones as $zone)   
                                <option value="{{ $zone }}">{{ $zone }}</option>
                            @endforeach
                        </select>
                        {{ $errors->first('timezone',"<span style='color:red'>:message</span>") }}
                    </div>
                </div>    
            </div>
                
            <div style="text-align: right; margin-right: 15px">
                <button type="submit" class="btn btn-default">Save changes</button>
            </div>            
            {{ Form::close() }}                                
            
        </div>
        
    </div>

    <div style="margin:10px 10px 0px 10px; font-weight: bold; color: gray">
        CHANGE PASSWORD
        <span id="pwdToggleIcon" onclick="toggleNewPassword()" class="glyphicon glyphicon-chevron-down hoverStyle" style="position: relative; left: 7px; top: 3px"></span>
    </div>
   <hr style="color: gray; margin: 6px 10px; border-width: 2px">
   <div id="newPasswordWell" class="well" style="margin: 0px 10px; background-color: white; display:none">
       
       <div class="row">
       
        {{ Form::open(array('url'=>'change_password')) }}

            <div class="col-md-4">
                {{ Form::label('new_password','New Password') }}
                {{ Form::password('new_password',null,array('class'=>'form-control')) }}
                {{ $errors->first('new_password',"<span style='color:red'>:message</span>") }}
            </div>
            <div class="col-md-4">
                {{ Form::label('repeat_password','Repeat Password') }}
                {{ Form::password('repeat_password',null,array('class'=>'form-control')) }}   
                {{ $errors->first('repeat_password',"<span style='color:red'>:message</span>") }}
            </div>
            <div class="col-md-4">
                <br>
                <button type="submit" class="btn btn-default" style="margin-top: 5px">Submit</button>
            </div>
        
        {{ Form::close() }}
       
       </div>
   </div>

<script type="text/javascript">
    
     $("#timezone").val("{{ $user->timezone }}");
    
    function toggleNewPassword(){
        $('#newPasswordWell').toggle('slide');
        if($('#pwdToggleIcon').hasClass('glyphicon-chevron-down')){
            $('#pwdToggleIcon').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        } else {
            $('#pwdToggleIcon').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        }
    }        
</script>    

@if(Session::has('pwd_failed'))
    <script type="text/javascript">
        toggleNewPassword();
    </script>
@endif