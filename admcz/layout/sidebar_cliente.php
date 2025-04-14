<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link" style="background-color: #046868;">
        <img src="img/logo-padrao.png" alt="Logo COMEX MANAGER" class="brand-image" id="large-logo">
        <img src="img/simbolo.png" alt="Logo Pequena COMEX MANAGER" class="brand-image" id="small-logo">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <?php
                if (isset($dadosUsuario['foto'])) {
                    $imgUser = $dadosUsuario['foto'];
                } else {
                    $imgUser = 'sem-foto.png';
                }
                ?>
                <img src="img/users/<?= $imgUser; ?>" class="img-circle elevation-2" alt="Foto do usuário">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= $dadosUsuario['nome']; ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <?php
                if (isset($pga) == '') {
                    $navLink = 'active';
                } else {
                    $navLink = '';
                }
                ?>
                <li class="nav-item">
                    <a href="cliente_inicial.php" class="nav-link <?= $navLink; ?>">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Página inicial
                        </p>
                    </a>
                </li>


                <li class="nav-item <?php if ($pga == 'aduaneiro') {
                    echo 'menu-open';
                } ?>">
                    <a href="#" class="nav-link <?php if ($pga == 'aduaneiro') {
                        echo 'active';
                    } ?>">
                        <i class="nav-icon fas fa-calculator"></i>
                        <p>
                            Aduaneiro
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="cliente.php?a=aduaneiro&b=processos" class="nav-link <?php if ($pga == 'aduaneiro' && ($pgb == 'processos' || $pgb == 'processos_profile' || $pgb == 'processos_acoes')) {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-clipboard-list nav-icon"></i>
                                <p>Processos</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item <?php if ($principal == 'frete') {
                    echo 'menu-open';
                } ?>">
                    <a href="#" class="nav-link <?php if ($principal == 'frete') {
                        echo 'active';
                    } ?>">
                        <i class="nav-icon fas fa-paper-plane"></i>
                        <p>
                            Frete Internacional
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="frete.php?a=frete&b=frete_cotacao" class="nav-link <?php if ($principal == 'frete' && $pagina == 'frete_cotacao') {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-luggage-cart nav-icon"></i>
                                <p>Realizar Cotação</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="frete.php?a=frete&b=frete_listar" class="nav-link <?php if ($principal == 'frete' && $pagina == 'frete_listar' || $principal == 'frete' && $pagina == 'frete_editar'|| $principal == 'frete' && $pagina == 'frete_abrir') {
                                echo 'active';
                            } ?>">
                                <i class="far fa-list-alt nav-icon"></i>
                                <p>Minhas cotações</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item <?php if (($pga == 'ferramentas' && $pgb == 'freight_index') || ($pga == 'ferramentas' && $pgb == 'taxas_listar')|| ($pga == 'ferramentas' && $pgb == 'invoice_maker_listar' || $pgb == 'invoice_maker_criar' || $pgb == 'invoice_maker_gerar' || $pgb == 'invoice_maker_editar')) {
                    echo 'menu-open';
                } ?>">
                    <a href="#" class="nav-link <?php if (($pga == 'ferramentas' && $pgb == 'freight_index') || ($pga == 'ferramentas' && $pgb == 'taxas_listar')|| ($pga == 'ferramentas' && $pgb == 'invoice_maker_listar' || $pgb == 'invoice_maker_criar' || $pgb == 'invoice_maker_gerar' || $pgb == 'invoice_maker_editar')) {
                    echo 'active';
                } ?>">
                        <i class="fas fa-wrench nav-icon"></i>
                        <p>
                            Ferramentas
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="cliente.php?a=ferramentas&b=freight_index" class="nav-link <?php if ($pga == 'ferramentas' && $pgb == 'freight_index') {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-chart-line nav-icon"></i>
                                <p>Freight Index</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="cliente.php?a=ferramentas&b=taxas_listar" class="nav-link <?php if ($pga == 'ferramentas' && $pgb == 'taxas_listar') {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-dollar-sign nav-icon"></i>
                                <p>Taxas Cambiais</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="cliente.php?a=ferramentas&b=invoice_maker_listar" class="nav-link <?php if ($pga == 'ferramentas' && $pgb == 'invoice_maker_listar' || $pgb == 'invoice_maker_criar' || $pgb == 'invoice_maker_gerar' || $pgb == 'invoice_maker_editar') {
                                echo 'active';
                            } ?>">
                                <i class="far fa-file-alt nav-icon"></i>
                                <p>Invoice Maker</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="cliente.php?a=google_drive&b=listar_gd" class="nav-link <?php if ($pga == 'google_drive' && ($pgb == 'listar_gd')) {
                                echo 'active';
                            } ?>">
                        <i class="nav-icon fab fa-google-drive"></i>
                        <p>
                            Meu Drive
                        </p>
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