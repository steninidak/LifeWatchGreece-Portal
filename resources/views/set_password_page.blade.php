<div class='row'>
        {{ Form::open(array('url'=>'password_reset/'.$code,'class'=>'form-horizontal')) }}
        
        <div class="col-md-3"></div>
        <div class="col-md-6">   
            <div class="form-group">
                <label for="new_passowrd" class="col-sm-4 control-label">New password</label>
                <div class="col-sm-8">
                  <input type="password" class="form-control" id="new_passowrd" name='new_password'>
                  {{ $errors->first('new_password',"<span style='color:red'>:message</span>") }}
                </div>                
            </div>
            
            <div class="form-group">
                <label for="repeat_password" class="col-sm-4 control-label">Repeat password</label>
                <div class="col-sm-8">
                  <input type="password" class="form-control" id="repeat_password" name='repeat_password'>
                  {{ $errors->first('repeat_password',"<span style='color:red'>:message</span>") }}
                </div>                
            </div>
            
            <div style='text-align: center'>
                <button type='submit' class='btn btn-default'>Reset password</button>
            </div>
        </div>
        <div class="col-md-3"></div>
        
        {{ Form::close() }}
    </div>    
</div>



