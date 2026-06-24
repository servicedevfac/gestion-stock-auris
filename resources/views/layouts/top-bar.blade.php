<div class="navbar-custom">
                <div class="topbar">
                    <div class="topbar-menu d-flex align-items-center gap-lg-2 gap-1">

                        <!-- Brand Logo -->
                        <div class="logo-box">
                            <!-- Brand Logo Light -->
                            <a href="{{url(path: 'dashboard')}}" class="logo-light">
                                <img src="{{url('assets/images/logo-darkc.png')}}" alt="logo" class="logo-lg" style="height: 60px; object-fit: contain;">
                                <img src="{{url('assets/images/logo-smF.png')}}" alt="small logo" class="logo-sm" style="height: 40px; object-fit: contain;">
                            </a>

                            <!-- Brand Logo Dark -->
                            <a href="{{url(path: 'dashboard')}}" class="logo-dark">
                                <img src="{{url('assets/images/logo-darkc.png')}}" alt="dark logo" class="logo-lg" style="height: 60px !important; object-fit: contain;">
                                <img src="{{url('assets/images/logo-smF.png')}}" alt="small logo" class="logo-sm" style="height: 40px; object-fit: contain;">
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

   <li class="nav-item dropdown d-flex align-items-center">
    <a class="dropdown-toggle btn btn-header1 d-flex align-items-center px-3 py-2"
       href="#"
       id="userDropdown"
       role="button"
       data-bs-toggle="dropdown"
       aria-expanded="false"
       style="margin-top: 10px;">
        <span class="d-flex align-items-center justify-content-center rounded-circle bg-white fw-bold me-2" style="width:32px;height:32px;font-size:14px;color:#1a237e;">
            {{ strtoupper(substr(Auth::user()->nom, 0, 1)) }}
        </span>
        <span class="d-none d-md-inline-block text-white fw-medium">
            {{ Auth::user()->nom }} <i class="mdi mdi-chevron-down ms-1"></i>
        </span>
    </a>

    <ul class="dropdown-menu dropdown-menu-end profile-dropdown" aria-labelledby="userDropdown">
        <li class="dropdown-header noti-title">
            <h6 class="text-overflow m-0">Bienvenue !</h6>
        </li>

        <li>
            <a href="{{ route('user-logins.index') }}" class="dropdown-item notify-item">
                <i data-lucide="history" class="font-size-16 me-2"></i>
                <span>Historique des connexions</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.horaires.index') }}" class="dropdown-item notify-item">
                <i data-lucide="settings" class="font-size-16 me-2"></i>
                <span>Paramètres horaires</span>
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <li>
            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                @csrf
                <button type="submit" class="dropdown-item notify-item text-danger">
                    <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                </button>
            </form>
        </li>
    </ul>

</li>


                    </ul>
                </div>
            </div>
