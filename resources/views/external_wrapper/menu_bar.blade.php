<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/lfw_logo.png') }}" style="width: 30px; display: inline-block" />
                <span style="margin-left: 10px; color: #16737B">Lifewatch Greece Portal</span>
            </a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <!-- Left navbar 
            <ul class="nav navbar-nav">
                <li><a href="#">Link</a></li>
            </ul>
            -->

            <!-- Right navbar -->
            <ul class="nav navbar-nav navbar-right">
                {{ Form::open(array('url'=>'orcid_login','class'=>'form-inline login-form')) }}
                    &nbsp;&nbsp;&nbsp;or 
                    <button type="submit" style="margin-left: 10px; padding: 1px 7px" title="Log in with ORCiD">
                        <img src="{{ asset('images/orcid.png') }}" style="height: 14px;">
                    </button>
                {{ Form::close() }}
                <a href="{{ url('/orcid/howto') }}" style="float: right; font-size: 11px; margin-right: 7px; margin-top: 2px">How To</a>
            </ul>
            
            <div class="nav navbar-nav navbar-right">
                {{ Form::open(array('url'=>'login','class'=>'form-inline login-form')) }}
                    <div class="form-group">
                      <label for="email">E-mail</label>
                      <input type="text" class="form-control" id="email" name="email">
                    </div>
                    <div class="form-group">
                      <label for="password">Password</label>
                      <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <button type="submit" class="btn btn-sm btn-default">Sign In</button>
                {{ Form::close() }}
                
                {{ link_to('password_reset_request','Forgot your password?',array('style'=>'font-size: 11px')) }}
                {{ link_to('register','Register',array('style'=>'font-size: 11px; margin-left: 20px')) }}
            </div>
            
            

        </div><!-- /.navbar-collapse -->              

    </div><!-- /.container-fluid -->
</nav>