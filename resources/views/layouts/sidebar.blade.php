<div class="main-menu">
    <!-- Brand Logo -->
    <div class="logo-box">
        <!-- Brand Logo Light -->
        <a href="index.html" class="logo-light">
            <img src="{{url('assets/images/logo-light.png')}}" alt="logo" class="logo-lg" height="18">
            <img src="{{url('assets/images/logo-sm.png')}}" alt="small logo" class="logo-sm" height="24">
        </a>

        <!-- Brand Logo Dark -->
        <a href="index.html" class="logo-dark">
            <img src="{{url('assets/images/logo-dark.png')}}" alt="dark logo" class="logo-lg" height="18">
            <img src="{{url('assets/images/logo-sm.png')}}" alt="small logo" class="logo-sm" height="24">
        </a>
    </div>

    <!--- Menu -->
    <div data-simplebar>
        <ul class="app-menu">

            <li class="menu-title">Menu</li>

            <li class="menu-item">
                <a href="{{route('dashboard')}}" class="menu-link waves-effect">
                    <span class="menu-icon"><i data-lucide="airplay "></i></span>
                    <span class="menu-text"> Dashboard </span>
                    <span class="badge bg-info rounded-pill ms-auto">3</span>
                </a>
            </li>

            <li class="menu-title">Custom</li>
            
            <li class="menu-item">
                <a href="#Ventes" data-bs-toggle="collapse" class="menu-link waves-effect">
                    <span class="menu-icon"><i data-lucide="badge-dollar-sign"></i></span>
                    <span class="menu-text"> Ventes  </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="Ventes">
                    <ul class="sub-menu">
                        
                        <li class="menu-item">
                            <a href="{{ route('ventes.create') }}" class="menu-link">
                                <span class="menu-text">Creer vente</span>
                            </a>
                        </li>
                        
                        <li class="menu-item">
                            <a href="{{ route('ventes.index') }}" class="menu-link">
                                <span class="menu-text">Liste ventes</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="menu-item">
                <a href="#Produit" data-bs-toggle="collapse" class="menu-link waves-effect">
                    <span class="menu-icon"><i data-lucide="package-search"></i></span>
                    <span class="menu-text"> Produits  </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="Produit">
                    <ul class="sub-menu">
                        <li class="menu-item">
                            <a href="{{ route('produits.create') }}" class="menu-link">
                                <span class="menu-text">Creer produit</span>
                            </a>
                        </li>

                        <li class="menu-item">
                            <a href="{{ route('produits.index') }}" class="menu-link">
                                <span class="menu-text">Liste produits</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="menu-item">
                <a href="#Stocks" data-bs-toggle="collapse" class="menu-link waves-effect">
                    <span class="menu-icon"><i data-lucide="warehouse"></i></span>
                    <span class="menu-text">Stocks</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="Stocks">
                    <ul class="sub-menu">
                        <li class="menu-item">
                            <a href="{{ route('mouvementStocks.create') }}" class="menu-link">
                                <span class="menu-text">Nouveau stock</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('mouvementStocks.index') }}" class="menu-link">
                                <span class="menu-text">Liste stocks</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-item">
                <a href="#menuClients" data-bs-toggle="collapse" class="menu-link waves-effect">
                    <span class="menu-icon"><i data-lucide="users-round"></i></span>
                    <span class="menu-text"> Clients </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="menuClients">
                    <ul class="sub-menu">
                        <li class="menu-item">
                            <a href="{{ route('clients.create') }}" class="menu-link">
                                <span class="menu-text">Nouveau client</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('clients.index') }}" class="menu-link">
                                <span class="menu-text">Liste clients</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="menu-item">
                <a href="#menuUsers" data-bs-toggle="collapse" class="menu-link waves-effect">
                    <span class="menu-icon"><i data-lucide="users"></i></span>
                    <span class="menu-text"> Utilisateurs </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="menuUsers">
                    <ul class="sub-menu">
                        <li class="menu-item">
                            <a href="{{route('users.create')}}" class="menu-link">
                                <span class="menu-text">Nouveau utiisateur</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{route('users.index')}}" class="menu-link">
                                <span class="menu-text">Liste utilisateurs</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="menu-item">
                <a href="#role" data-bs-toggle="collapse" class="menu-link waves-effect">
                    <span class="menu-icon"><i data-lucide="scale"></i></span>
                    <span class="menu-text"> Rôles </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="role">
                    <ul class="sub-menu">
                        <li class="menu-item">
                            <a href="{{route('roles.create')}}" class="menu-link">
                                <span class="menu-text">Nouveau rôle</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{route('roles.index')}}" class="menu-link">
                                <span class="menu-text">Liste rôles</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="menu-item">
                <a href="#Permission" data-bs-toggle="collapse" class="menu-link waves-effect">
                    <span class="menu-icon"><i data-lucide="ruler"></i></span>
                    <span class="menu-text"> Permissions </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="Permission">
                    <ul class="sub-menu">
                        
                        <li class="menu-item">
                            <a href="{{route('permissions.create')}}" class="menu-link">
                                <span class="menu-text">Nouvelle permission</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{route('permissions.index')}}" class="menu-link">
                                <span class="menu-text">Liste permissions</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>
