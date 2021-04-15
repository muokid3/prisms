
<!DOCTYPE html>
<html lang="en">
{{--<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">--}}
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PRISMS') }}</title>

    <link href="{{url('css/styles.css')}}" rel="stylesheet" />

    {{--datepicker css--}}
    <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.css" rel="stylesheet"/>

    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand" href="{{url('/')}}">PRISMS</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
        <div class="input-group">
            <input class="form-control" type="text" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" />
            <div class="input-group-append">
                <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav ml-auto ml-md-0">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                {{--                <a class="dropdown-item" href="#">Settings</a>--}}
                {{--                <a class="dropdown-item" href="#">Activity Log</a>--}}
                {{--                <div class="dropdown-divider"></div>--}}
                <a class="dropdown-item" href="{{url('logout')}}">Logout</a>
            </div>
        </li>
    </ul>
</nav>


























<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Core</div>
                    <a class="nav-link" href="{{url('/')}}">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>

                </div>
            </div>

        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <div id="layoutError">
                    <div id="layoutError_content">
                        <main>
                            <div class="container">
                                <div class="row justify-content-center">
                                    <div class="col-lg-6">
                                        <div class="text-center mt-4">
{{--                                            <img class="mb-4 img-error" src="{{url('assets/img/error-404-monochrome.svg')}}" />--}}
                                            <h1>403 - Access Denied!</h1>
                                            <h5>{{ $exception->getMessage() }}</h5>

                                            <a href="{{url('/')}}">
                                                <i class="fas fa-arrow-left mr-1"></i>
                                                Return to Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </main>
                    </div>
                    <div id="layoutError_footer">
                        <footer class="py-4 bg-light mt-auto">
                            <div class="container-fluid">
                                <div class="d-flex align-items-center justify-content-between small">
                                    <div class="text-muted">Copyright &copy; KEMRI
                                        <script>
                                            document.write(new Date().getFullYear())
                                        </script>
                                    </div>
                                    <div>
                                        <a href="#">Privacy Policy</a>
                                        &middot;
                                        <a href="#">Terms &amp; Conditions</a>
                                    </div>
                                </div>
                            </div>
                        </footer>
                    </div>
                </div>



            </div>
        </main>
    </div>
</div>
{{--<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>--}}
<script src="{{ url('js/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ url('js/popper.min.js') }}" type="text/javascript"></script>
<script src="{{ url('js/bootstrap-material-design.min.js') }}" type="text/javascript"></script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="{{url('js/scripts.js')}}"></script>

<script src="{{ url('js/jquery.dataTables.min.js') }}"></script>

<script src="{{ url('js/moment.min.js') }}"></script>

{{--datepicker js--}}
<script src="{{ url('js/bootstrap-datetimepicker.min.js') }}"></script>


<script type="text/javascript">




    $(document).ready(function() {

        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    });

</script>

@yield('scripts')
@stack('js')

</body>
</html>


