<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link" style="background: linear-gradient(to right, #4f7686, #a7bf13);;">
        <img src="img/logo-padrao.png" alt="Logo CheckZone" class="brand-image" id="large-logo">
        <img src="img/simbolo.png" alt="Logo Pequena CheckZone" class="brand-image" id="small-logo">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <?php
                if (isset($_SESSION['user_photo'])) {
                    $imgUser = $_SESSION['user_photo'];
                } else {
                    $imgUser = 'sem-foto.png';
                }
                ?>
                <img src="img/users/<?= $imgUser; ?>" class="img-circle elevation-2" alt="Foto do usuário">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= $_SESSION['user_name']; ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <?php
                if (isset($principal) == '') {
                    $navLink = 'active';
                } else {
                    $navLink = '';
                }
                ?>
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?= $navLink; ?>">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Página inicial
                        </p>
                    </a>
                </li>


                <li class="nav-item <?php if ($principal == 'aduaneiro') {
                    echo 'menu-open';
                } ?>">
                    <a href="#" class="nav-link <?php if ($principal == 'aduaneiro') {
                        echo 'active';
                    } ?>">
                        <i class="nav-icon fas fa-hand-holding-usd"></i>
                        <p>
                            Painel Financeiro
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="aduaneiro.php?a=aduaneiro&b=processos" class="nav-link <?php if ($principal == 'aduaneiro' && ($pagina == 'processos' || $pagina == 'processos_profile' || $pagina == 'processos_acoes')) {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-file-invoice-dollar nav-icon"></i>
                                <p>Dados de Cobrança</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="aduaneiro.php?a=aduaneiro&b=processos" class="nav-link <?php if ($principal == 'aduaneiro' && ($pagina == 'processos' || $pagina == 'processos_profile' || $pagina == 'processos_acoes')) {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-receipt nav-icon"></i>
                                <p>Extratos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="aduaneiro.php?a=aduaneiro&b=processos" class="nav-link <?php if ($principal == 'aduaneiro' && ($pagina == 'processos' || $pagina == 'processos_profile' || $pagina == 'processos_acoes')) {
                                echo 'active';
                            } ?>">
                                <i class="far fa-credit-card nav-icon"></i>
                                <p>Realizar Pagamento</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="aduaneiro.php?a=aduaneiro&b=processos" class="nav-link <?php if ($principal == 'aduaneiro' && ($pagina == 'processos' || $pagina == 'processos_profile' || $pagina == 'processos_acoes')) {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-file-invoice nav-icon"></i>
                                <p>Emitir Nota Fiscal</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item <?php if ($principal == 'divulgacao') {
                    echo 'menu-open';
                } ?>">
                    <a href="#" class="nav-link <?php if ($principal == 'divulgacao') {
                        echo 'active';
                    } ?>">
                        <i class="fas fa-user-check nav-icon"></i>
                        <p>
                            Vistorias
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="divulgacao.php?a=divulgacao&b=adicionar" class="nav-link <?php if ($principal == 'divulgacao' && $pagina == 'adicionar') {
                                echo 'active';
                            } ?>">
                                <i class="far fa-plus-square nav-icon"></i>
                                <p>Adicionar</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="divulgacao.php?a=divulgacao&b=listar" class="nav-link <?php if ($principal == 'divulgacao' && $pagina == 'listar' || $principal == 'divulgacao' && $pagina == 'editar') {
                                echo 'active';
                            } ?>">
                                <i class="far fa-list-alt nav-icon"></i>
                                <p>Listar/Editar</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item <?php if ($principal == 'config') {
                    echo 'menu-open';
                } ?>">
                    <a href="#" class="nav-link <?php if ($principal == 'config') {
                        echo 'active';
                    } ?>">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Configurações
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="config.php?a=config&b=empresas" class="nav-link <?php if ($principal == 'config' && $pagina == 'empresas' || $pagina == 'empresas_profile') {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-building nav-icon"></i>
                                <p>Clientes</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="config.php?a=config&b=usuarios" class="nav-link <?php if ($principal == 'config' && $pagina == 'usuarios') {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-users nav-icon"></i>
                                <p>Usuários</p>
                            </a>
                        </li>



                    </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<style>
/* Exibe a imagem grande por padrão */
#large-logo {
    display: block;
}

/* Esconde a imagem pequena por padrão */
#small-logo {
    display: none;
}

/* Quando a sidebar estiver recolhida, troca as imagens */
body.sidebar-collapse #large-logo {
    display: none;
}

body.sidebar-collapse #small-logo {
    display: block;
}
</style>