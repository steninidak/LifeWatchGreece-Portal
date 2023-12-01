<div style='text-align: right; margin-bottom: 10px'>
    <div class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addAppDialog">Add new Application</div>
</div>
<table class='table table-bordered table-condensed'>
    <thead>
        <th>ID</th>
        <th>Title</th>    
        <th>Code name</th>        
        <th>Status</th>
        <th>Actions</th>
    </thead>
    <tbody>
        @foreach($apps as $app)
        <tr id="app{{ $app->id }}_row">
            <td>{{ $app->id }}</td>
            <td style='text-align: left'>{{ link_to('admin/app_management/profile/'.$app->codename,$app->title) }}</td>
            <td>{{ $app->codename }}</td>            
            <td>{{ $app->status }}</td>
            <td>
                <div class="linkStyle" onclick="deleteAppDialog({{ $app->id }})">Delete</div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Add application Modal -->
<div class="modal fade" id="addAppDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cancel</span></button>
        <h4 class="modal-title" id="myModalLabel">Add new application</h4>
      </div>
      <div class="modal-body" style="padding-bottom:0px">
        {{ Form::open(array('url'=>'admin/app_management/add','id'=>'addAppForm','files'=>'true','data-validation'=>url('admin/app_management/validate'))) }}

        <div class="row">
            <div class="col-md-8">
                {{ Form::label('title','Title') }}
                {{ Form::text('title',null,array('class'=>'form-control','maxlength'=>$maxlengths['title'])) }} 
            </div>
            <div class="col-md-4">
                {{ Form::label('codename','Code name') }}
                {{ Form::text('codename',null,array('class'=>'form-control','maxlength'=>$maxlengths['codename'])) }} 
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                {{ Form::label('description','Description') }}
                {{ Form::textarea('description',null,array('class'=>'form-control','rows'=>'2')) }} 
            </div>
            <div class="col-md-4">
                {{ Form::label('status','Status') }}
                {{ Form::select('status',array('open'=>'Open Access','free'=>'Freely Accessible','developing'=>'Under Development','controlled'=>'Controller Access'),'free',array('class'=>'form-control')) }} 
            </div>
        </div>       
        <div class="row">
            <div class="col-md-8">
                {{ Form::label('url','URL') }}
                {{ Form::text('url',null,array('class'=>'form-control','maxlength'=>$maxlengths['url'])) }} 
            </div>
            <div class="col-md-4">
                {{ Form::label('ip','IP') }}
                {{ Form::text('ip',null,array('class'=>'form-control','maxlength'=>$maxlengths['ip'])) }} 
            </div>
        </div>        
        <div class="row">
            <div class="col-md-8">
                {{ Form::label('api_username','API username') }}
                {{ Form::text('api_username',$app->api_username,array('class'=>'form-control','maxlength'=>$maxlengths['api_username'])) }} 
            </div>
            <div class="col-md-4">
                {{ Form::label('api_password','API password') }}
                {{ Form::text('api_password',null,array('class'=>'form-control','maxlength'=>$maxlengths['api_password'])) }} 
            </div>
        </div>
        <div class="row">            
            <div class="col-md-5" style="text-align: right">
                {{ Form::label('imageFile','Image') }}
                <span class="btn btn-default btn-file">
                    Select file... <input type="file" name="imageFile" id="imageFile">
                </span>                                         
            </div>
            <div class="col-md-7">
                {{ Form::text('selected_image',null,array('class'=>'form-control','disabled'=>'disabled')) }} 
            </div>
        </div>
        <div class="row">            
            <div class="col-md-5" style="text-align: right">
                {{ Form::label('toolbarImageFile','Toolbar Image') }}
                <span class="btn btn-default btn-file">
                    Select file... <input type="file" name="toolbarImageFile" id="toolbarImageFile">
                </span>                                         
            </div>
            <div class="col-md-7">
                {{ Form::text('selected_toolbar_image',null,array('class'=>'form-control','disabled'=>'disabled')) }} 
            </div>
        </div>
        <div class="row">
            <div class="checkbox" style="margin-left: 15px">
                <label>
                    <input type="checkbox" name="access_by_default"> Registered users can access it by default <span style="color:gray; margin-left: 5px">(* only for controlled access)</span>
                </label>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="checkbox" style="margin-left: 15px">
                        <label>
                            <input type="checkbox" name="mobile_app"> There is a mobile app for this application
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                {{ Form::label('mobile_version','Mobile App Version') }}
                {{ Form::text('mobile_version',null,array('class'=>'form-control','maxlength'=>$maxlengths['mobile_version'],'disabled'=>'disabled')) }} 
            </div>
        </div>                

        {{ Form::close() }}
        <div id="addAppErrors" style="text-align: center; color: red;"></div>
      </div>
      <div class="modal-footer" style="margin-top:0px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="createApplication()" id="addAppButton">Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete app Modal -->
<div class="modal fade" id="deleteAppDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background-color: salmon">        
        <h4 class="modal-title" id="myModalLabel">Application deletion</h4>
      </div>
      <div class="modal-body">
        {{ Form::open(array('url'=>'admin/app_management/delete','id'=>'deleteAppForm','name'=>'deleteAppForm')) }}

        <span class="label label-danger">Warning!</span> The application <span id="app_title" style="font-weight: bold"></span> with codename <span id="app_codename" style="font-weight: bold"></span> will be completely deleted and all the relating permissions
        will be revoked. 
        <input type="hidden" name="delete_app_id" value="">        

        {{ Form::close() }}        
      </div>
      <div class="modal-footer" style="margin-top:0px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="javascript:document.forms.deleteAppForm.submit();" id=deleteAppButton">Delete</button>
      </div>
    </div>
  </div>
</div>

<script type='text/javascript'>
    
    function deleteAppDialog(appId){

        var title = $('#app'+appId+"_row td:nth-child(2)").text();
        var codename = $('#app'+appId+"_row td:nth-child(3)").text();
        $('input[name="delete_app_id"]').val(""+appId);
        $('#app_title').text(title);
        $('#app_codename').text(codename);
        $('#deleteAppDialog').modal('show')
    }  
    
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
 
     function createApplication(){       

        /* get some values from elements on the page: */
        var form = $('#addAppForm');
        var formURL = form.attr( 'data-validation' );        
        
        $.ajax(
        {
            url : formURL,
            type: "POST",
            data : new FormData($('#addAppForm')[0]),
            processData: false,
            contentType: false,
            dataType : 'json',
            success:function(data, textStatus, jqXHR) 
            {
                $('#addAppForm').submit();
                //location.href = '{{ url("admin/app_management") }}';
            },
            error: function(jqXHR, textStatus, errorThrown) 
            {
                switch (jqXHR.status) {
                    case 400: // Form validation failed
                        $('#addAppErrors').empty();
                        response = JSON.parse(jqXHR.responseText);
                        var messages = "";
                        for(var key in response){
                            messages = messages+response[key]+"<br>";
                        }
                        $('#addAppErrors').append(messages);
                        break;
                     case 401: // Unauthorized access
                        $('#addAppErrors').empty();
                        $('#addAppErrors').append("Unauthorized access!");
                        break;
                     case 500: // Unexpected error
                        $('#addAppErrors').empty();                        
                        $('#addAppErrors').append("<strong>Unexpected error!</strong> Please refresh the page and try again!");
                        break;
                }
            }
        });

    }
         
</script>