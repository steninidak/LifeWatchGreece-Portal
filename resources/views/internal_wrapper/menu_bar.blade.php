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

            
            <!-- Right navbar -->
            <ul class="nav navbar-nav navbar-right">    
                <li><a href="{{ url('/') }}">Home</a></li>
                @if(hasPermission('backend_access'))
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Administration<span class="caret"></span></a>                    
                        <ul class="dropdown-menu" role="menu">                           
                            @if(hasPermission('manage_users'))
                                <li>{{ link_to('admin/user_management','Users') }}</li>
                            @endif
                            @if(hasPermission('manage_groups'))
                                <li>{{ link_to('admin/group_management','Groups') }}</li>
                            @endif
                            @if(hasPermission('manage_permissions'))
                                <li>{{ link_to('admin/permission_management','Permissions') }}</li>
                            @endif
                            @if(hasPermission('manage_apps'))
                                <li>{{ link_to('admin/app_management','Applications') }}</li>
                            @endif
                            @if(hasPermission('manage_settings'))
                                <li>{{ link_to('admin/system_settings','System Configuration') }}</li>
                            @endif
                            @if(hasPermission('manage_announcements'))
                                <li>{{ link_to('admin/announcements','Announcements') }}</li>
                            @endif
                            @if(hasPermission('manage_announcements'))
                                <li>{{ link_to('admin/ome_time_messages','One-time messages') }}</li>
                            @endif                        
                        </ul>
                    </li>
                @endif
                @if(hasPermission('view_system_info'))
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">System<span class="caret"></span></a>                    
                        <ul class="dropdown-menu" role="menu">                         
                                <li>{{ link_to('admin','System Info') }}</li>                            
                                <li>{{ link_to('admin/biocluster','Biocluster Logs') }}</li>                        
                        </ul>
                    </li>
                @endif                
                 
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::guard('web')->user()->firstname }} {{ Auth::guard('web')->user()->lastname }}<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                      <li>{{ link_to('profile','My Profile') }}</li>
                      <li class="divider"></li>
                      <li>{{ link_to('logout','Logout') }}</li>
                    </ul>
                </li>
                <li><a href="{{ url('/contact_us') }}">Contact Us</a></li>

            </ul>

        </div><!-- /.navbar-collapse -->              

    </div><!-- /.container-fluid -->
</nav>