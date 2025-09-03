<div class="navbar-custom">
                <div class="topbar">
                    <div class="topbar-menu d-flex align-items-center gap-lg-2 gap-1">

                        <!-- Brand Logo -->
                        <div class="logo-box">
                            <!-- Brand Logo Light -->
                            <a href="{{url(path: 'dashboard')}}" class="logo-light">
                                <img src="{{url('assets/images/logo-light.png')}}" alt="logo" class="logo-lg" style="height: 100px;">
                                <img src="{{url('assets/images/logo-sm.png')}}" alt="small logo" class="logo-sm" style="height: 70px;">
                            </a>

                            <!-- Brand Logo Dark -->
                            <a href="{{url(path: 'dashboard')}}" class="logo-dark">
                                <img src="{{url('assets/images/logo-dark.png')}}" alt="dark logo" class="logo-lg" style="height: 100px !important;">
                                <img src="{{url('assets/images/logo-sm.png')}}" alt="small logo" class="logo-sm" style="height: 70px;">
                            </a>
                        </div>

                        <!-- Sidebar Menu Toggle Button -->
                        <button class="button-toggle-menu waves-effect waves-dark rounded-circle text-gray">
                            <i class="mdi mdi-menu"></i>
                        </button>
                    </div>

                    <ul class="topbar-menu d-flex align-items-center gap-2">


                        <li class="nav-link waves-effect waves-dark" id="theme-mode">
                            <i class="bx bx-moon font-size-24"></i>
                        </li>

   <li class="nav-item dropdown">
    <a class="dropdown-toggle btn btn-header1 nav-user waves-effect waves-dark p-3"
       href="#"
       id="userDropdown"
       role="button"
       data-bs-toggle="dropdown"
       aria-expanded="false">
        {{-- <img src="{{ url('assets/images/users/avatar-1.jpg') }}"
             alt="user-image"
             class="rounded-circle"> --}}
        <span class="ms-1 d-none d-md-inline-block">
            {{ Auth::user()->nom }} <i class="mdi mdi-chevron-down"></i>
        </span>
    </a>

    <ul class="dropdown-menu dropdown-menu-end profile-dropdown" aria-labelledby="userDropdown">
        <li class="dropdown-header noti-title">
            <h6 class="text-overflow m-0">Welcome !</h6>
        </li>

        {{-- <li>
            <a href="{{ route('profile.edit') }}" class="dropdown-item notify-item">
                <i data-lucide="user" class="font-size-16 me-2"></i>
                <span>Mon profil</span>
            </a>
        </li> --}}
        <li>
            <a href="{{ route('user-logins.index') }}" class="dropdown-item notify-item">
                <i data-lucide="user" class="font-size-16 me-2"></i>
                <span>historique des connexions</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.horaires.index') }}" class="dropdown-item notify-item">
                <i data-lucide="settings" class="font-size-16 me-2"></i>
                <span>Paramètre des horaires de ventes</span>
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <li>
            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                @csrf
                <button type="submit" class="dropdown-item notify-item">
                    <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
                </button>
            </form>
        </li>
    </ul>
</li>


                    </ul>
                </div>
            </div>
