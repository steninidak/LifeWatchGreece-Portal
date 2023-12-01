<p style="font-size: 30px; font-weight: bold; margin-bottom: 15px; text-align: center">
    Contact Form
</p>
<p style="text-align: center; margin: 0px 30px 40px 30px">
     For feedback, problem, proposals and any other issue generally related
     to the portal <br>or regarding a certain vLab or web service provided by the portal,
     use the form of this page.
</p>

<div class='row'>
     <div class='col-md-2' style="text-align: center">

    </div>
     <div class='col-md-7'>
         {{ Form::open(array('url'=>'contact_us','class'=>'form-horizontal')) }}

         <div class="form-group">
            <label for="subject" class="col-sm-4 control-label">Subject *</label>
            <div class="col-sm-8">
              {{ Form::text('subject',Input::old('subject'),array('class'=>'form-control')) }}
              {!! $errors->first('subject',"<span style='color:red'>:message</span>") !!}
            </div>
          </div>

        <div class="form-group">
            <label for="related_to" class="col-sm-4 control-label">Related To *</label>
            <div class="col-sm-8">
              {{ Form::select('related_to',$options,'Generic',array('class'=>'form-control')) }}
              {!! $errors->first('related_to',"<span style='color:red'>:message</span>") !!}
            </div>
          </div>

         <div class="form-group">
            <label for="message" class="col-sm-4 control-label">Message *</label>
            <div class="col-sm-8">
              {{ Form::textarea('message',Input::old('message'),array('class'=>'form-control')) }}
              {!! $errors->first('message',"<span style='color:red'>:message</span>") !!}
            </div>
        </div>

         <div class="form-group">
            <label for="captcha" class="col-sm-4 control-label">Fill in the image text *</label>
            <div class="col-sm-8">
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
            </div>
         </div>

        <div style='text-align: center'>
            <button class='btn btn-primary'>Send Message</button>
        </div>

        {{ Form::close() }}

    </div>
    <div class='col-md-2'>
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