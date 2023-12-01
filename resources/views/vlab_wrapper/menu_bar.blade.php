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
            <a class="navbar-brand" href="#"><img src="{{ asset('images/lfw_logo.png') }}" style="width: 30px" /></a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <!-- Left navbar -->
            <ul class="nav navbar-nav">
                <li><a href="#">Link</a></li>
            </ul>
            
            <!-- Right navbar -->            
            <ul class="nav navbar-nav navbar-right">                
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
                        @if(hasPermission('manage_configuration'))
                            <li>{{ link_to('admin/config_management','System Configuration') }}</li>
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
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Gougousis Alexandros <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                      <li>{{ link_to('profile','My Profile') }}</li>
                      <li class="divider"></li>
                      <li>{{ link_to('logout','Logout') }}</li>
                    </ul>
                </li>
            </ul>

        </div><!-- /.navbar-collapse -->              

    </div><!-- /.container-fluid -->
</nav>