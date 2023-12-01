<div class='row'>

     <div class='col-md-2'>
         
    </div>
     <div class='col-md-4'>
         {{ Form::open(array('url'=>'register','class'=>'form-horizontal')) }}

         <div class="form-group">
            <label for="firstname" class="col-sm-4 control-label">First name *</label>
            <div class="col-sm-8">
              {{ Form::text('firstname',Input::old('firstname'),array('class'=>'form-control')) }}
              {!! $errors->first('firstname',"<span style='color:red'>:message</span>") !!}
            </div>            
          </div>        

        <div class="form-group">
            <label for="lastname" class="col-sm-4 control-label">Last name *</label>
            <div class="col-sm-8">
              {{ Form::text('lastname',Input::old('lastname'),array('class'=>'form-control')) }}
              {!! $errors->first('lastname',"<span style='color:red'>:message</span>") !!}
            </div>
          </div>                

        <div class="form-group">
            <label for="email" class="col-sm-4 control-label">E-mail *</label>
            <div class="col-sm-8">
              {{ Form::text('email',Input::old('email'),array('class'=>'form-control')) }}
              {!! $errors->first('email',"<span style='color:red'>:message</span>") !!}
            </div>
          </div>

        <div class="form-group">
            <label for="password" class="col-sm-4 control-label">Password: *</label>
            <div class="col-sm-8">
              {{ Form::password('password',array('class'=>'form-control')) }}
              {!! $errors->first('password',"<span style='color:red'>:message</span>") !!}
            </div>
          </div>                  
        
        <div class="form-group">
            <label for="verify_password" class="col-sm-4 control-label">Repeat password: *</label>
            <div class="col-sm-8">
              {{ Form::password('verify_password',array('class'=>'form-control')) }}
              {!! $errors->first('verify_password',"<span style='color:red'>:message</span>") !!}
            </div>
          </div>       
       
        <div class="form-group">
            <label for="affiliation" class="col-sm-4 control-label">Affiliation</label>
            <div class="col-sm-8">
              {{ Form::text('affiliation',Input::old('affiliation'),array('class'=>'form-control')) }}
              {!! $errors->first('affiliation',"<span style='color:red'>:message</span>") !!}
            </div>
          </div>                
        
        <div class="form-group">
            <label for="position" class="col-sm-4 control-label">Position</label>
            <div class="col-sm-8">
              {{ Form::text('position',Input::old('position'),array('class'=>'form-control')) }}
              {!! $errors->first('position',"<span style='color:red'>:message</span>") !!}
            </div>
          </div>                
        
        {{ Form::label('captcha','Fill in the image text:') }} 
        <table style="width: 100%; margin-bottom: 10px" id="captcha_table">
            <tr>
                <td style="width: 150px; padding-left: 0px">
                    {{ Form::text('captcha','',array('class'=>'form-control')) }}
                </td>
                <td>
                    {!! captcha_img() !!}
                    <div title="Refresh image" class="btn btn-sm btn-default" onclick="javascript:refresh_captcha()"><span class="glyphicon glyphicon-repeat"></span></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    {!! $errors->first('captcha',"<span style='color:red'>:message</span>") !!}
                </td>
            </tr>
        </table>   

        <div style='text-align: center'>
            <button class='btn btn-primary'>Sign Up</button>
        </div>
        
        {{ Form::close() }}

    </div>
     <div class='col-md-6' style="text-align: center;">
         <img src="{{ asset('images/registration.jpg') }}" style="max-width: 300px; margin-top: 60px">
    </div>
</div>

<script type="text/javascript">
    function refresh_captcha(){
        var formURL = "{{ url('new_captcha_link') }}";
        $.get(formURL).done(function( data ) {
                $('#captcha_table img').attr('src',data);
            }
        );
    }
</script>
