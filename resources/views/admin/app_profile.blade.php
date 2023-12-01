<div class="row">
    <div class="col-md-3">
        <div style="text-align: center">
        @if(empty($app->image))
            <img src="{{ asset('images/edit_app.png') }}" class="img-responsive">
        @else
            <img src="{{ asset('images/apps/'.$app->image) }}" class="img-responsive" style="width: 250px" >
        @endif
        </div>
    </div>
    <div class="col-md-9">
        {{ Form::open(array('url'=>'admin/app_management/profile/'.$app->codename,'id'=>'addAppForm','data-validation'=>url('admin/app_management/validate'),'files'=>'true')) }}

        <div class="row">
            <div class="col-md-8">
                {{ Form::label('title','Title') }}
                {{ Form::text('title',$app->title,array('class'=>'form-control','maxlength'=>$maxlengths['title'])) }} 
                {{ $errors->first('title','<span class="error">:message</span>')}}
            </div>
            <div class="col-md-4">
                {{ Form::label('codename','Code name') }}
                {{ Form::text('codename',$app->codename,array('class'=>'form-control','disabled'=>'disabled')) }} 
                {{ $errors->first('codename','<span class="error">:message</span>')}}
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                {{ Form::label('description','Description') }}
                {{ Form::textarea('description',$app->description,array('class'=>'form-control','rows'=>'6')) }} 
                {{ $errors->first('description','<span class="error">:message</span>')}}
            </div>
            <div class="col-md-4">
                {{ Form::label('status','Status') }}
                {{ Form::select('status',array('free'=>'Freely accessible','developing'=>'Under Development','controlled'=>'Controller Access','open'=>'Open Access'),$app->status,array('class'=>'form-control')) }} 
                {{ $errors->first('status','<span class="error">:message</span>')}}
                <div id="status_description" style="margin-top: 10px; color: gray">
                    <strong>Status explanation:</strong> The application is accessible by all users that have been registered and logged in to the portal.
                </div>
            </div>
        </div>        
        <div class="row">
            <div class="col-md-8">
                {{ Form::label('url','URL') }}
                {{ Form::text('url',$app->url,array('class'=>'form-control','maxlength'=>$maxlengths['url'])) }} 
                {{ $errors->first('url','<span class="error">:message</span>')}}
            </div>
            <div class="col-md-4">
                {{ Form::label('ip','IP') }}
                @if(empty($app->mobile_app))
                    {{ Form::text('ip',$app->ip,array('class'=>'form-control','maxlength'=>$maxlengths['ip'])) }} 
                @else
                    {{ Form::text('ip',$app->ip,array('class'=>'form-control','maxlength'=>$maxlengths['ip'],'disabled'=>'disabled')) }}
                @endif
                {{ $errors->first('ip','<span class="error">:message</span>')}}
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                {{ Form::label('api_username','API username') }}
                {{ Form::text('api_username',$api_user->username,array('class'=>'form-control','maxlength'=>$maxlengths['api_username'])) }} 
                {{ $errors->first('api_username','<span class="error">:message</span>')}}
            </div>
            <div class="col-md-4">
                {{ Form::label('api_password','API password') }}
                {{ Form::text('api_password',null,array('class'=>'form-control','maxlength'=>$maxlengths['api_password'])) }} 
                {{ $errors->first('api_password','<span class="error">:message</span>')}}
            </div>
        </div>
        <div class="row">            
            <div class="col-md-4" style="text-align: right">
                {{ Form::label('imageFile','Image ') }}
                <span class="btn btn-default btn-file">
                    Select file... <input type="file" name="imageFile" id="imageFile">
                </span>            
                {{ $errors->first('imageFile','<span class="error">:message</span>')}}
            </div>
            <div class="col-md-8">
                {{ Form::text('selected_image',$app->image,array('class'=>'form-control','disabled'=>'disabled')) }} 
            </div>
        </div>        
        <div class="row">            
            <div class="col-md-4" style="text-align: right">
                {{ Form::label('toolbarImageFile','Toolbar Image ') }}
                <span class="btn btn-default btn-file">
                    Select file... <input type="file" name="toolbarImageFile" id="toolbarImageFile">
                </span>           
                {{ $errors->first('toolbarImageFile','<span class="error">:message</span>')}}
            </div>
            <div class="col-md-8">
                {{ Form::text('selected_toolbar_image',$app->toolbar_image,array('class'=>'form-control','disabled'=>'disabled')) }} 
            </div>
        </div>      
        
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="checkbox" style="margin-left: 15px">
                        <label>
                            @if(empty($app->reg_access))
                                <input type="checkbox" name="access_by_default"> Registered users can access it by default <span style="color:gray; margin-left: 5px">(* only for controlled access)</span>
                            @else
                                <input type="checkbox" name="access_by_default" checked> Registered users can access it by default <span style="color:gray; margin-left: 5px">(* only for controlled access)</span>
                            @endif
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="checkbox" style="margin-left: 15px">
                        <label>
                            @if(empty($app->mobile_app))
                                <input type="checkbox" name="mobile_app"> There is a mobile app for this application
                            @else
                                <input type="checkbox" name="mobile_app" checked> There is a mobile app for this application
                            @endif
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="checkbox" style="margin-left: 15px">
                        <label>
                            @if(empty($app->hide_from_ui))
                                <input type="checkbox" name="hide_from_ui"> Hide from UI
                            @else
                                <input type="checkbox" name="hide_from_ui" checked> Hide from UI
                            @endif
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                {{ Form::label('mobile_version','Current Mobile Version') }}
                @if(empty($app->mobile_app))
                    {{ Form::text('mobile_version',$app->mobile_version,array('class'=>'form-control','maxlength'=>$maxlengths['mobile_version'],'disabled'=>'disabled')) }} 
                @else
                    {{ Form::text('mobile_version',$app->mobile_version,array('class'=>'form-control','maxlength'=>$maxlengths['mobile_version'])) }} 
                @endif
                {{ $errors->first('mobile_version','<span class="error">:message</span>')}}
            </div>
        </div>
                
        <div style="text-align: center">
            <button type="button" class="btn btn-default" onclick="javascript:location='{{ url('admin/app_management') }}'">Back to App List</button>
            <button class="btn btn-primary">Save</button>
        </div>
        
        {{ Form::close() }}
        <div id="editAppErrors" style="text-align: center; color: red;"></div>
    </div>    
</div>
<script type="text/javascript">
    $("input[name='imageFile']").change(function(){        
        var filename = $(this).val();
        $("input[name='selected_image']").val(filename);        
     });
     
     $("input[name='toolbarImageFile']").change(function(){        
        var filename2 = $(this).val();
        $("input[name='selected_toolbar_image']").val(filename2);        
     });
     
     $("input[name='mobile_app']").change(function(){        
         if($("input[name='mobile_app']").prop('checked')){
             $("input[name='ip']").prop( "disabled", true );
             $("input[name='mobile_version']").prop( "disabled", false );
         } else {
             $("input[name='ip']").prop( "disabled", false );
             $("input[name='mobile_version']").prop( "disabled", true );
         }
     });
     
     $("select[name='status']").change(function(){        
        var statusDescr = "";
        var statusType = $(this).val();
        switch(statusType){
            case 'free':
                statusDescr = "<strong>Status explanation:</strong> The application is accessible by all users that have been registered and logged in to the portal.";
                break;
            case 'open':
                statusDescr = "<strong>Status explanation:</strong> The application is accessible by all portal visitors, without having to register or log in.";
                break;
            case 'developing':
                statusDescr = "<strong>Status explanation:</strong> Only registered and logged in users that have the 'access_unfinished_apps' permission can access this application.";
                break;
            case 'controlled':
                statusDescr = "<strong>Status explanation:</strong> The application is available only to registered and logged in users that have been given access by portal's admin. The permission that users have to be assigned has the same name as the codename of the application.";
                break;
        }
        $('#status_description').html(statusDescr);
     });
</script>