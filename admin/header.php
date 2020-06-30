<header class="main-header">
    <a href="index.php" class="logo">
        <span class="logo-mini">PA</span>
        <span class="logo-lg">Administration</span>
    </a>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Bouton Menu</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <li class="dropdown notifications-menu">
                    <a href="#" onclick="alert('Ici pour afficher le nombre de demande de création de compte ou des messages des Admins');" class="dropdown-toggle"
                       data-toggle="dropdown" aria-expanded="true" id="notification-indicator">
                        <i class="fa fa-bell-o"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <div id="notification-container" class="slimScrollDiv"
                                 style="position: relative; overflow: hidden; width: auto;"></div>
                        </li>
                    </ul>
                </li>


                <li class="user user-menu">
                    <a href="#">
                        <span class="hidden-xs"><?= $_SESSION['pseudo'] ?></span>
                    </a>
                </li>
                <li>
                    <a href="./../deco.php"><i class="fa fa-power-off"></i> Déconnexion</a>
                </li>
            </ul>
        </div>

    </nav>
</header>

