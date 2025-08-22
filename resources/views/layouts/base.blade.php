<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light">

<head>
    <meta charset="utf-8" />
    <title>Gest_Stock-Auris</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{url('assets/images/favicon.ico')}} ">

    <link href="{{url('assets/libs/morris.js/morris.css')}}" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="{{url('assets/css/style.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{url('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css">
    <script src="{{url('assets/js/config.js')}}"></script>
    <link rel="shortcut icon" href="{{url('assets/images/favicon.ico')}} ">
    {{-- HEAD --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- third party css -->
    <link href="{{url('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- third party css end -->

    <style>
        .card-heade {
            background-color: #02228b;
            color: #ffffff !important;
        }
        .btn-header {
            background-color: #ffffff;
            color: #02228b;
            border: #02228b 1px solid;
        }
        .btn-header:hover {
            background-color: #e0e0e0;
            color: #02228b;
            border: 1px solid #02228b;
            font-weight: bold;
        }
        .btn-header1 {
            background-color: #02228b;
            color: #ffffff;
            border: #02228b 1px solid;
        }
        .btn-header1:hover {
            background-color: #e0e0e0;
            color: #02228b;
            border: 1px solid #02228b;
            font-weight: bold;
        }
        .btn-header2 {
            background-color: #E6BA23;
            color: #ffffff;
            border: #E6BA23 1px solid;
        }
        .btn-delete {
            background-color: #dd1313;
            color: #ffffff;
            border: none;
        }
        .btn-delete:hover {
            background-color: #e60000;
            color: #ffffff;
            border: none;
            font-weight: bold;
        }
        .border1{
            border: 1px solid #02228b;
        }


    </style>

</head>

<body>

    <div class="layout-wrapper">

        @include('layouts.sidebar')

        <div class="page-content">

            @include('layouts.top-bar')

            <div class="px-3">
                <!-- Start Content-->
                <div class="container-fluid">
                    
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')

                    {{-- @stack('scripts') --}}
                    
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div>
                                <script>document.write(new Date().getFullYear())</script> © Drezoc
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-none d-md-flex gap-4 align-item-center justify-content-md-end">
                                <p class="mb-0">Design & Develop by <a href="https://myrathemes.com/"
                                        target="_blank">MyraStudio</a> </p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            
        </div>
    </div>


    <!-- ============================================================== -->
    <!-- End Page content -->
    <!-- ============================================================== -->
    <!-- END wrapper -->

    <!-- App js -->
    <script src="{{url('assets/js/vendor.min.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.btn-delete').on('click', function () {
                const formId = $(this).data('form-id');

                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: "Cette action est irréversible !",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer !',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#' + formId).submit();
                    }
                });
            });
        });
    </script>


    @yield('scripts')


    <!-- Jquery Sparkline Chart  -->
    <script src="{{url('assets/libs/jquery-sparkline/jquery.sparkline.min.js')}}"></script>

    <!-- Jquery-knob Chart Js-->
    <script src="{{url('assets/libs/jquery-knob/jquery.knob.min.js')}}"></script>


    <!-- Morris Chart Js-->
    <script src="{{url('assets/libs/morris.js/morris.min.js')}}"></script>

    <script src="{{url('assets/libs/raphael/raphael.min.js')}}"></script>

    <!-- Dashboard init-->
    <script src="{{url('assets/js/pages/dashboard.js')}}"></script>

    <script src="{{url('assets/js/vendor.min.js')}}"></script>
    <script src="{{url('assets/js/app.js')}}"></script>

    <!-- third party js -->
    <script src="{{url('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{url('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{url('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{url('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
    <script src="{{url('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{url('assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
    <script src="{{url('assets/libs/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{url('assets/libs/datatables.net-buttons/js/buttons.flash.min.js')}}"></script>
    <script src="{{url('assets/libs/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
    <script src="{{url('assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js')}}"></script>
    <script src="{{url('assets/libs/datatables.net-select/js/dataTables.select.min.js')}}"></script>
    <script src="{{url('assets/libs/pdfmake/build/pdfmake.min.js')}}"></script>
    <script src="{{url('assets/libs/pdfmake/build/vfs_fonts.js')}}"></script>
    <!-- third party js ends -->
    {{-- SCRIPTS (dans @section('scripts')) --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Datatables js -->
    <script src="{{url('assets/js/pages/datatables.js')}}"></script>
    <!-- jQuery (si non déjà inclus) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
