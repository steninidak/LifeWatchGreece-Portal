@if(Session::has('send_reset_link_failed'))
    <div style='text-align: center'>
        <div class="alert alert-danger" role="alert" style='display:inline-block'>A problem occured when trying to send you a reset link! Please, try again later!</div>
    </div>
@endif


{{ Form::open(array('url'=>'password_reset_request','class'=>'form-horizontal')) }}

        <div class='row'>
            <div class='col-sm-4'>
                {{ Form::label('email','Your E-mail') }}
                {{ Form::text('email',null,array('class'=>'form-control','placeholder'=>'Your registration e-mail')) }}                    
                {{ $errors->first('email',"<span style='color:red'>:message</span>") }}
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
                    {{ $errors->first('captcha',"<span style='color:red'>:message</span>") }}
                </td>
            </tr>
        </table>    
        <div class='col-sm-4'>
            <button class='btn btn-primary' style='margin-left: 20px'>Request</button>
        </div>
    
{{ Form::close() }}

<script type="text/javascript">
    function refresh_captcha(){
        var formURL = "{{ url('new_captcha_link') }}";
        $.get(formURL).done(function( data ) {
                $('#captcha_table img').attr('src',data);
            }
        );
    }
</script>