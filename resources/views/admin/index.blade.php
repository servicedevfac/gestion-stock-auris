@extends('layouts.base')
@section('content')

<div class="px-3">

    <!-- Start Content-->
    <div class="container-fluid">

        <!-- start page title -->
        <div class="py-3 py-lg-4">
            <div class="row">
                <div class="col-lg-6">
                    <h4 class="page-title mb-0">Dashboard</h4>
                </div>
                <div class="col-lg-6">
                    <div class="d-none d-lg-block">
                        <ol class="breadcrumb m-0 float-end">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Drezoc</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase font-size-12 text-muted mb-3">Cost per Unit</h6>
                                <span class="h3 mb-0"> $85.50 </span>
                            </div>
                            <div class="col-auto">
                                <span class="badge badge-soft-success">+7.5%</span>
                            </div>
                        </div> <!-- end row -->

                        <div id="sparkline1" class="mt-3"></div>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-lg-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase font-size-12 text-muted mb-3">Market Revenue</h6>
                                <span class="h3 mb-0"> $12,548.25 </span>
                            </div>
                            <div class="col-auto">
                                <span class="badge badge-soft-danger">-24.5%</span>
                            </div>
                        </div> <!-- end row -->

                        <div id="sparkline2" class="mt-3"></div>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-lg-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase font-size-12 text-muted mb-3">Expenses</h6>
                                <span class="h3 mb-0"> $8,451.28 </span>
                            </div>
                            <div class="col-auto">
                                <span class="badge badge-soft-success">+3.5%</span>
                            </div>
                        </div> <!-- end row -->

                        <div id="sparkline3" class="mt-3"></div>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-lg-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase font-size-12 text-muted mb-3">Daily Visits</h6>
                                <span class="h3 mb-0"> 1,12,584 </span>
                            </div>
                            <div class="col-auto">
                                <span class="badge badge-soft-success">+53.5%</span>
                            </div>
                        </div> <!-- end row -->

                        <div id="sparkline4" class="mt-3"></div>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
        <!-- end row-->

        <div class="row">
            <div class="col-xl-4 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Time Best Products</h4>
                        <p class="card-subtitle">From date of 1st Jan 2019 to continue</p>
                    </div>

                    <div class="card-body">
                        <div id="morris-donut-example" class="morris-chart"></div>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-xl-8 col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Sales Analytics</h4>
                        <p class="card-subtitle">From date of 1st Jan 2019 to continue</p>
                    </div>
                    <div class="card-body">
                        <div id="morris-bar-example" class="morris-chart"></div>
                    </div>
                </div>
            </div> <!-- end col-->
        </div>
        <!-- end row-->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Customers</h4>
                        <p class="card-subtitle">Transaction period from 21 July to 25 Aug</p>
                    </div>
                    <div class="card-ody">
                        <div class="table-responsive">
                            <table class="table table-centered table-striped table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Location</th>
                                        <th>Create Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="table-user">
                                            <img src="assets/images/users/avatar-4.jpg" alt="table-user" class="me-2 avatar-sm rounded-circle">
                                            <a href="javascript:void(0);" class="text-body font-weight-semibold">Paul J. Friend</a>
                                        </td>
                                        <td>
                                            937-330-1634
                                        </td>
                                        <td>
                                            pauljfrnd@jourrapide.com
                                        </td>
                                        <td>
                                            New York
                                        </td>
                                        <td>
                                            07/07/2024
                                        </td>
                                    </tr>



                                </tbody>
                            </table>
                        </div>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col -->

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Account Transactions</h4>
                        <p class="card-subtitle">Transaction period from 21 July to 25 Aug</p>
                    </div>

                    <div class="card-bod">
                        <div class="table-responsive">
                            <table class="table table-hover table-centered table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th>Card Number</th>
                                        <th>Amount</th>
                                        <th>Card Type</th>
                                        <th>User Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <h5 class="font-size-15 mb-1 font-weight-normal">4257 **** ****
                                                7852</h5>
                                            <span class="text-muted font-size-12">11 April 2019</span>
                                        </td>
                                        <td>
                                            <h5 class="font-size-15 mb-1 font-weight-normal">$79.49</h5>
                                            <span class="text-muted font-size-12">Amount</span>
                                        </td>
                                        <td>
                                            <h5 class="font-size-17 mb-1 font-weight-normal"><i class="fab fa-cc-visa"></i></h5>
                                            <span class="text-muted font-size-12">Card</span>
                                        </td>
                                        <td>
                                            <h5 class="font-size-15 mb-1 font-weight-normal">Helen Warren
                                            </h5>
                                            <span class="text-muted font-size-12">Pay</span>
                                        </td>
                                    </tr>



                                    <tr>
                                        <td>
                                            <h5 class="font-size-15 mb-1 font-weight-normal">4257 **** ****
                                                7852</h5>
                                            <span class="text-muted font-size-12">12 Nov 2024</span>
                                        </td>
                                        <td>
                                            <h5 class="font-size-15 mb-1 font-weight-normal">$8964.04</h5>
                                            <span class="text-muted font-size-12">Amount</span>
                                        </td>
                                        <td>
                                            <h5 class="font-size-17 mb-1 font-weight-normal"><i class="fab fa-cc-visa"></i></h5>
                                            <span class="text-muted font-size-12">Card</span>
                                        </td>
                                        <td>
                                            <h5 class="font-size-15 mb-1 font-weight-normal">Caitlyn Gibney
                                            </h5>
                                            <span class="text-muted font-size-12">Pay</span>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div>
        <!-- end row-->

    </div> <!-- container -->

</div> <!-- content -->
@endsection
