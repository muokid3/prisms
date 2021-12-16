<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Core</div>
                <a class="nav-link" href="{{url('/')}}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>



                @if(auth()->user()->role->has_perm([2,3]))
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAllocation" aria-expanded="false" aria-controls="collapseAllocation">
                        <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                        Allocation
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>

                    <div class="{{ \Request::is('strata') || \Request::is('allocation/upload') ? 'navbar-expanded' : 'collapse' }}" id="collapseAllocation" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            @if(auth()->user()->role->has_perm([2]))
                                <a class="nav-link" href="{{url('strata')}}">Strata</a>
                            @endif
                            @if(auth()->user()->role->has_perm([3]))
                               <a class="nav-link" href="{{url('allocation/upload')}}">Upload List</a>
                            @endif

                        </nav>
                    </div>
                @endif

                @if(auth()->user()->role->has_perm([4,5]))
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSms" aria-expanded="false" aria-controls="collapseSms">
                        <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                        Randomization
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>

                    <div class="{{ \Request::is('randomization') || \Request::is('sms') ? 'navbar-expanded' : 'collapse' }}" id="collapseSms" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            @if(auth()->user()->role->has_perm([4]))
                                <a class="nav-link" href="{{url('randomization')}}">Randomization Log</a>
                            @endif

                            @if(auth()->user()->role->has_perm([5]))
                               <a class="nav-link" href="{{url('sms')}}">SMS Log</a>
                            @endif
                        </nav>
                    </div>
                @endif

                @if(auth()->user()->role->has_perm([6,7,8]))
                    <div class="sb-sidenav-menu-heading">Sites</div>
                    @if(auth()->user()->role->has_perm([7]))
                        <a class="nav-link" href="{{url('sites')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-university"></i></div>
                            Sites
                        </a>
                    @endif

                    @if(auth()->user()->role->has_perm([6]))
                        <a class="nav-link" href="{{url('studies')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-infinity"></i></div>
                            Studies
                        </a>
                    @endif

                    @if(auth()->user()->role->has_perm([8]))
                        <a class="nav-link" href="{{url('site_studies')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            Site Studies
                        </a>
                    @endif

                @endif

                @if(auth()->user()->role->has_perm([9,12]))
                    <div class="sb-sidenav-menu-heading">Bulk Messaging</div>

                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMesaging" aria-expanded="false" aria-controls="collapseMesaging">
                        <div class="sb-nav-link-icon"><i class="fas fa-envelope-open-text"></i></div>
                        Messaging
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>

                    <div class="{{ \Request::is('bulk*') ? 'navbar-expanded' : 'collapse' }}" id="collapseMesaging" aria-labelledby="headingMesaging" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{url('bulk/mails')}}">Bulk E-Mail</a>
                            <a class="nav-link" href="{{url('bulk/messaging')}}"> Bulk Messaging</a>
                        </nav>
                    </div>
                @endif


                @if(auth()->user()->role->has_perm([13]))

                    <div class="sb-sidenav-menu-heading">RedCap</div>
                    <a class="nav-link" href="{{url('redcap_hospitals')}}">
                        <div class="sb-nav-link-icon"><i class="fas fa-university"></i></div>
                        RedCap Hospitals
                    </a>
                @endif

                <a class="nav-link" href="{{url('questions')}}">
                    <div class="sb-nav-link-icon"><i class="fas fa-poll"></i></div>
                    Questionnaire
                </a>

                @if(auth()->user()->role->has_perm([1]))

                    <div class="sb-sidenav-menu-heading">Users</div>
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUsers" aria-expanded="false" aria-controls="collapseUsers">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        User Management
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>

                    <div class="{{ \Request::is('user*') ? 'navbar-expanded' : 'collapse' }}" id="collapseUsers" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="{{url('users')}}">Users</a>
                            <a class="nav-link" href="{{url('user_groups')}}">User Roles</a>
                        </nav>
                    </div>

                @endif

                @if(auth()->user()->role->has_perm([11]))
                    <a class="nav-link" href="{{url('audit_logs')}}">
                        <div class="sb-nav-link-icon"><i class="fab fa-stumbleupon"></i></div>
                        Audit Logs
                    </a>
                @endif


            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            {{auth()->user()->first_name.' '.auth()->user()->last_name}}
        </div>
    </nav>
</div>
