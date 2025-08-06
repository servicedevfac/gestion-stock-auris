<div class="navbar-custom">
                <div class="topbar">
                    <div class="topbar-menu d-flex align-items-center gap-lg-2 gap-1">

                        <!-- Brand Logo -->
                        <div class="logo-box">
                            <!-- Brand Logo Light -->
                            <a href="index.html" class="logo-light">
                                <img src="{{url('assets/images/logo-light.png')}}" alt="logo" class="logo-lg" height="20">
                                <img src="{{url('assets/images/logo-sm.png')}}" alt="small logo" class="logo-sm" height="20">
                            </a>

                            <!-- Brand Logo Dark -->
                            <a href="index.html" class="logo-dark">
                                <img src="{{url('assets/images/logo-dark.png')}}" alt="dark logo" class="logo-lg" height="20">
                                <img src="{{url('assets/images/logo-sm.png')}}" alt="small logo" class="logo-sm" height="20">
                            </a>
                        </div>

                        <!-- Sidebar Menu Toggle Button -->
                        <button class="button-toggle-menu waves-effect waves-dark rounded-circle">
                            <i class="mdi mdi-menu"></i>
                        </button>
                    </div>

                    <ul class="topbar-menu d-flex align-items-center gap-2">


                        <li class="nav-link waves-effect waves-dark" id="theme-mode">
                            <i class="bx bx-moon font-size-24"></i>
                        </li>

                        <li class="dropdown">

                                <a class=" dropdown-toggle btn btn-header1 nav-user  waves-effect waves-dark  p-3" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <img src="{{url('assets/images/users/avatar-1.jpg')}}" alt="user-image" class="rounded-circle">
                                <span class="ms-1 d-none d-md-inline-block">
                                    {{Auth::user()->nom}} <i class="mdi mdi-chevron-down"></i>
                                </span>
                            </a>


                            <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                                <!-- item-->
                                <div class="dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Welcome !</h6>
                                </div>

                                <!-- item-->
                                <a href="{{ route('profile.edit') }}" class="dropdown-item notify-item">
                                    <i data-lucide="user" class="font-size-16 me-2"></i>
                                    <span>Mon profile</span>
                                </a>

                                <!-- item-->
                                <a href="{{route('admin.horaires.index')}}" class="dropdown-item notify-item">
                                    <i data-lucide="settings" class="font-size-16 me-2"></i>
                                    <span>parametre des horaire de ventes</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                <a href="{{route('logout')}}" class="dropdown-item notify-item">
                                    <form action="{{route('logout')}}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-lg btn-header1"><i class="fas fa-sign-out-alt me-1"></i> Deconnexion</button>
                                    </form>

                                </a>

                            </div>
                        </li>

                    </ul>
                </div>
            </div>
