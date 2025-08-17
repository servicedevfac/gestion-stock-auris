<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light">
<head>
    <meta charset="utf-8" />
    <title>Gest_Stock-Auris</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Drezoc - Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="MyraStudio" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    {{-- bootstrap5 --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ url('assets/images/favicon.ico') }}">

    <!-- Vendor CSS -->
    <link href="{{ url('assets/libs/morris.js/morris.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ url('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ url('assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ url('assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css') }}" rel="stylesheet" />

    <!-- App CSS -->
    <link href="{{ url('assets/css/style.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('assets/css/style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
       <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>

        .filtres { margin-bottom: 20px; }
        .filtres input, .filtres button { margin-right: 10px; padding: 5px; }
        #ventesChart { max-width: 800px; margin-bottom: 20px; }
    /* petit style pour le canvas */

        .card-heade { background-color: #02228b; color: #ffffff !important; }
        .btn-header { background-color: #ffffff; color: #02228b; border: #02228b 1px solid; }
        .btn-header:hover { background-color: #e0e0e0; color: #02228b; border: 1px solid #02228b; font-weight: bold; }
        .btn-header1 { background-color: #02228b; color: #ffffff; border: #02228b 1px solid; }
        .btn-header1:hover { background-color: #e0e0e0; color: #02228b; border: 1px solid #02228b; font-weight: bold; }
        .btn-header2 { background-color: #E6BA23; color: #ffffff; border: #E6BA23 1px solid; }
        .colj { background-color: #e6ba23; color: #ffffff; }
        .btn-delete, .redoff { background-color: #dd1313; color: #ffffff; border: none; }
        .btn-delete:hover { background-color: #e60000; color: #ffffff; border: none; font-weight: bold; }
        .border1 { border: 1px solid #02228b; }
         .marquee {
            overflow: hidden;
            white-space: nowrap;
            box-sizing: border-box;
            animation: scroll 20s linear infinite;
            color: white;
            background-color: #dd1313;
            padding: 10px;
            font-weight: bold;
        }
        @keyframes scroll {
            0%   { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
    </style>

    <!-- App Config -->
    <script src="{{ url('assets/js/config.js') }}"></script>
</head>
<body>
    <div class="layout-wrapper">
        @include('layouts.sidebar')

        <div class="page-content">
            <div class="px-3">
                <div class="container-fluid">
                    @include('layouts.top-bar')

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div>
                            <script>document.write(new Date().getFullYear())</script> © Gest_Stock-Auris
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-none d-md-flex gap-4 align-item-center justify-content-md-end">
                            <p class="mb-0">Designé & développé par <a href="https://www.attouco.com/" target="_blank">Attouco</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>


    <!-- JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ url('assets/js/vendor.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ url('assets/js/vendor.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('assets/js/app.min.js') }}"></script>

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
    <script src="{{ url('assets/libs/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
    <script src="{{ url('assets/libs/jquery-knob/jquery.knob.min.js') }}"></script>
    <script src="{{ url('assets/libs/morris.js/morris.min.js') }}"></script>
    <script src="{{ url('assets/libs/raphael/raphael.min.js') }}"></script>
    <script src="{{ url('assets/js/pages/dashboard.js') }}"></script>
    <script src="{{ url('assets/js/app.js') }}"></script>
    <script src="{{ url('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ url('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ url('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ url('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ url('assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('assets/libs/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ url('assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ url('assets/libs/datatables.net-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ url('assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ url('assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ url('assets/js/pages/datatables.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <!-- Bootstrap JS (avec Popper inclus) -->

    <script>
        // Ensure dropdowns work
        document.addEventListener('DOMContentLoaded', function () {
            var dropdownElements = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownElements.map(function (dropdownToggleEl) {
                new bootstrap.Dropdown(dropdownToggleEl);
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
