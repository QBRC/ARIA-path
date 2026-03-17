<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/iconfinder-494-atom-chemistry-molecule-laboratory-4212919_114941.ico') }}" />

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for data table -->
{{--    <link href="{{ asset('vendor/datatables/jquery.dataTables.min.css') }}" rel="stylesheet">--}}
{{--    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />--}}
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables/datatables.min.css') }}"/>


    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">



    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    @yield('style')


    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>


<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
{{--            <div class="sidebar-brand-icon rotate-n-15">--}}
        <div class="sidebar-brand-icon">
            <i class="fas fa-search"></i>
            </div>
            <div class="sidebar-brand-text mx-3">{{ config('app.name', 'Laravel') }}</div>
        </a>

        <!-- Divider -->
        {{--        <hr class="sidebar-divider my-0">--}}

        {{--        <!-- Nav Item - Dashboard -->--}}
        {{--        <li class="nav-item active">--}}
        {{--            <a class="nav-link" href="{{ url('/') }}">--}}
        {{--                <i class="fas fa-fw fa-tachometer-alt"></i>--}}
        {{--                <span>Dashboard</span></a>--}}
        {{--        </li>--}}

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Research
        </div>

        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item">
            <a class="nav-link" href="{{ url('slides') }}">
                <i class="fas fa-table"></i>
                <span>Images</span>
            </a>
        </li>

        {{--        <li class="nav-item">--}}
        {{--            <a class="nav-link" href="{{ url('methods') }}">--}}
        {{--                <i class="fas fa-tools"></i>--}}
        {{--                <span>Methods</span>--}}
        {{--            </a>--}}
        {{--        </li>--}}

        {{--        <li class="nav-item">--}}
        {{--            <a class="nav-link" href="{{ url('analysis') }}">--}}
        {{--                <i class="fas fa-chart-bar"></i>--}}
        {{--                <span>Analysis</span>--}}
        {{--            </a>--}}
        {{--        </li>--}}

        {{--        <li class="nav-item">--}}
        {{--            <a class="nav-link" href="{{ url('gallery') }}">--}}
        {{--                <i class="far fa-images"></i>--}}
        {{--                <span>Gallery</span>--}}
        {{--            </a>--}}
        {{--        </li>--}}

        <!-- Divider -->
        <hr class="sidebar-divider">
        {{--{{ dd(Auth::user()) }}--}}
        @auth
            @if( Route::has('login') && (Auth::user()->role == 1 || Auth::user()->role == 2) )
                <!-- Heading -->
                <div class="sidebar-heading">
                    System setting
                </div>

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('users') }}">
                        <i class="fas fa-users"></i>
                        <span>Users</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('groups') }}">
                        <i class="fas fa-house-user"></i>
                        <span>Teams</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('batches') }}">
                        <i class="fas fa-project-diagram"></i>
                        <span>Batches</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('models') }}">
                        <i class="fas fa-cogs"></i>
                        <span>Models</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('api-health-checks') }}">
                        <i class="fas fa-heartbeat"></i>
                        <span>Health</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('import') }}">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Import</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('backup') }}">
                        <i class="fas fa-database"></i>
                        <span>Backup</span></a>
                </li>
                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">

            @endif
        @endauth

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>


    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow" id="show_top">

                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <!-- Topbar Search -->
                {{--                <form--}}
                {{--                    class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">--}}
                {{--                    <div class="input-group">--}}
                {{--                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."--}}
                {{--                               aria-label="Search" aria-describedby="basic-addon2">--}}
                {{--                        <div class="input-group-append">--}}
                {{--                            <button class="btn btn-primary" type="button">--}}
                {{--                                <i class="fas fa-search fa-sm"></i>--}}
                {{--                            </button>--}}
                {{--                        </div>--}}
                {{--                    </div>--}}
                {{--                </form>--}}

                <small class="font-italic"> Release version: 1.0</small>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                    <li class="nav-item dropdown no-arrow d-sm-none">
                        {{--                        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"--}}
                        {{--                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
                        {{--                            <i class="fas fa-search fa-fw"></i>--}}
                        {{--                        </a>--}}
                        <!-- Dropdown - Messages -->
                        {{--                        <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"--}}
                        {{--                             aria-labelledby="searchDropdown">--}}
                        {{--                            <form class="form-inline mr-auto w-100 navbar-search">--}}
                        {{--                                <div class="input-group">--}}
                        {{--                                    <input type="text" class="form-control bg-light border-0 small"--}}
                        {{--                                           placeholder="Search for..." aria-label="Search"--}}
                        {{--                                           aria-describedby="basic-addon2">--}}
                        {{--                                    <div class="input-group-append">--}}
                        {{--                                        <button class="btn btn-primary" type="button">--}}
                        {{--                                            <i class="fas fa-search fa-sm"></i>--}}
                        {{--                                        </button>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </form>--}}
                        {{--                        </div>--}}
                    </li>

                    <li class="nav-item dropdown no-arrow">

                        <!-- Authentication Links -->
                        @if (Route::has('login'))
                            <div class="">
                                @auth
                                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{{ Auth::user()->name }}}
</span>
                                        {{--                                        <img class="img-profile rounded-circle" src="{{ asset('img/undraw_profile.svg') }}">--}}
                                        <i class="fas fa-laugh-beam fa-2x text-primary"></i>
                                    </a>
                                    <!-- Dropdown - User Information -->
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                         aria-labelledby="userDropdown">
                                        <a class="dropdown-item" href="{{ url('profile') }}">
                                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Profile
                                        </a>
                                        <a class="dropdown-item" href="{{ url('changepassword') }}">
                                            <i class="fas fa-unlock-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Change password
                                        </a>

                                        {{--                                        <a class="dropdown-item" href="#">--}}
                                        {{--                                            <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>--}}
                                        {{--                                            Activity Log--}}
                                        {{--                                        </a>--}}
                                        <div class="dropdown-divider"></div>

                                        {{--                                        <a class="dropdown-item" href="" data-toggle="modal" data-target="#logoutModal">--}}
                                        {{--                                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>--}}
                                        {{--                                            Logout--}}
                                        {{--                                        </a>--}}

                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                              class="d-none">
                                            @csrf
                                        </form>

                                    </div>
                                @else
                                    @if (Route::has('login'))
                                        <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Log
                                            in</a>
                                    @endif

                                    {{--                                    @if (Route::has('register'))--}}
                                    {{--                                        <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline pr-3">Register</a>--}}
                                    {{--                                    @endif--}}
                                @endauth
                            </div>
                        @endif


                    </li>

                </ul>

            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->


            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800" id="page_title">
                        @yield('page-title')
                    </h1>
                    {{--                            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i--}}
                    {{--                                    class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>--}}
                </div>

                @yield('content')


            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>2021 &copy; <a href="https://qbrc.swmed.edu/" target="_blank">Quantitative Biomedical Research Center</a> <span
                            class="mx-2">|</span> <a href="https://www.utsouthwestern.edu/" target="_blank">UT Southwestern Medical Center</a></span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
{{--<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"--}}
{{--     aria-hidden="true">--}}
{{--    <div class="modal-dialog" role="document">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>--}}
{{--                <button class="close" type="button" data-dismiss="modal" aria-label="Close">--}}
{{--                    <span aria-hidden="true">×</span>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>--}}
{{--            <div class="modal-footer">--}}
{{--                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>--}}
{{--                <a class="btn btn-primary" href="{{ route('logout') }}">Logout</a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

<!-- Bootstrap core JavaScript-->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Core plugin JavaScript-->
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

<!-- Custom scripts for all pages-->
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

<!-- Chart JS -->
{{--<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>--}}
{{--<script src="{{ asset('js/demo/chart-area-demo.js') }}"></script>--}}
{{--<script src="{{ asset('js/demo/chart-pie-demo.js') }}"></script>--}}


<!-- Data table -->
{{--<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>--}}

<!-- Added for sum

 -->
<script type="text/javascript" src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>

<script src="{{ asset('js/app.js') }}" defer></script>


<!-- Page level custom scripts -->
{{--<script src="{{ asset('js/demo/datatables-demo.js') }}"></script>--}}


@yield('scripts')

</body>

</html>

