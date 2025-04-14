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
                if (isset($principal) == '') {
                    $navLink = 'active';
                } else {
                    $navLink = '';
                }
                ?>
                <li class="nav-item">
                    <a href="operacional_inicial.php" class="nav-link <?= $navLink; ?>">
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
                        <i class="nav-icon fas fa-calculator"></i>
                        <p>
                            Aduaneiro
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="aduaneiro.php?a=aduaneiro&b=processos" class="nav-link <?php if ($principal == 'aduaneiro' && ($pagina == 'processos' || $pagina == 'processos_profile' || $pagina == 'processos_acoes')) {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-clipboard-list nav-icon"></i>
                                <p>Processos</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item <?php if ($principal == 'cotacao') {
                    echo 'menu-open';
                } ?>">
                    <a href="#" class="nav-link <?php if ($principal == 'cotacao') {
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
                            <a href="cotacao.php?a=cotacao&b=cotacao_listar" class="nav-link <?php if ($principal == 'cotacao' && $pagina == 'cotacao_listar') {
                                echo 'active';
                            } ?>">
                                <i class="far fa-list-alt nav-icon"></i>
                                <p>Cotações</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="cotacao.php?a=cotacao&b=cotacao_propostas" class="nav-link <?php if ($principal == 'cotacao' && $pagina == 'cotacao_propostas' || $principal == 'cotacao' && $pagina == 'cotacao_propostas_adicionar') {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-clipboard-list nav-icon"></i>
                                <p>Propostas</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item <?php if (($principal == 'ferramentas' && $pagina == 'freight_index') || ($principal == 'ferramentas' && $pagina == 'taxas_listar') || ($principal == 'ferramentas' && $pagina == 'ncm_listar') || ($principal == 'ferramentas' && $pagina == 'formulario_armazenagem_m') || ($principal == 'ferramentas' && $pagina == 'formulario_armazenagem_a') || ($principal == 'ferramentas' && $pagina == 'invoice_maker_listar') || ($principal == 'ferramentas' && $pagina == 'invoice_maker_criar' || $pagina == 'invoice_maker_gerar'|| $pagina == 'invoice_maker_editar')|| ($principal == 'ferramentas' && $pagina == 'viabilidade_criar' || $pagina == 'viabilidade_listar' || $pagina == 'viabilidade_editar'|| $pagina == 'viabilidade_calculo' || $pagina == 'viabilidade_calculo_produto' || $pagina == 'viabilidade_calculo_fxs')) {
                    echo 'menu-open';
                } ?>">
                    <a href="#" class="nav-link <?php if (($principal == 'ferramentas' && $pagina == 'freight_index') || ($principal == 'ferramentas' && $pagina == 'taxas_listar') || ($principal == 'ferramentas' && $pagina == 'ncm_listar') || ($principal == 'ferramentas' && $pagina == 'formulario_armazenagem_m') || ($principal == 'ferramentas' && $pagina == 'formulario_armazenagem_a')|| ($principal == 'ferramentas' && $pagina == 'invoice_maker_listar') || ($principal == 'ferramentas' && $pagina == 'invoice_maker_criar' || $pagina == 'invoice_maker_gerar' || $pagina == 'invoice_maker_editar') || ($principal == 'ferramentas' && $pagina == 'viabilidade_criar' || $pagina == 'viabilidade_listar' || $pagina == 'viabilidade_editar' || $pagina == 'viabilidade_calculo' || $pagina == 'viabilidade_calculo_produto' || $pagina == 'viabilidade_calculo_fxs')) {
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
                            <a href="ferramentas.php?a=ferramentas&b=taxas_listar" class="nav-link <?php if ($principal == 'ferramentas' && $pagina == 'taxas_listar') {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-dollar-sign nav-icon"></i>
                                <p>Taxas Cambiais</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="ferramentas.php?a=ferramentas&b=ncm_listar" class="nav-link <?php if ($principal == 'ferramentas' && $pagina == 'ncm_listar') {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-list-alt nav-icon"></i>
                                <p>NCM's</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="ferramentas.php?a=ferramentas&b=viabilidade_listar" class="nav-link <?php if (($principal == 'ferramentas' && $pagina == 'viabilidade_listar') || ($principal == 'ferramentas' && $pagina == 'viabilidade_criar') || ($principal == 'ferramentas' && $pagina == 'viabilidade_calculo'|| $pagina == 'viabilidade_calculo_produto' || $pagina == 'viabilidade_calculo_fxs')) {
                                echo 'active';
                            } ?>">
                                <i class="fas fa-paste nav-icon"></i>
                                <p>Viabilidade</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="ferramentas.php?a=ferramentas&b=invoice_maker_listar" class="nav-link <?php if (($principal == 'ferramentas' && $pagina == 'invoice_maker_listar') || ($principal == 'ferramentas' && $pagina == 'invoice_maker_criar' || $pagina == 'invoice_maker_gerar' || $pagina == 'invoice_maker_editar')) {
                                echo 'active';
                            } ?>">
                                <i class="far fa-file-alt nav-icon"></i>
                                <p>Invoice Maker</p>
                            </a>
                        </li>
                        <li class="nav-item <?php if (($principal == 'ferramentas' && $pagina == 'formulario_armazenagem_m') || ($principal == 'ferramentas' && $pagina == 'formulario_armazenagem_a')) {
                    echo 'menu-open';
                } ?>">
                            <a href="#" class="nav-link <?php if (($principal == 'ferramentas' && $pagina == 'formulario_armazenagem_m') || ($principal == 'ferramentas' && $pagina == 'formulario_armazenagem_a')) {
                    echo 'active';
                } ?>">
                                <i class="fas fa-cubes nav-icon"></i>
                                <p>
                                    Armazenagem
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="ferramentas.php?a=ferramentas&b=formulario_armazenagem_m" class="nav-link <?php if ($principal == 'ferramentas' && $pagina == 'formulario_armazenagem_m') {
                                echo 'active';
                            } ?>">
                                        <i class="fas fa-ship nav-icon"></i>
                                        <p>Portuária</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="ferramentas.php?a=ferramentas&b=formulario_armazenagem_a" class="nav-link <?php if ($principal == 'ferramentas' && $pagina == 'formulario_armazenagem_a') {
                                echo 'active';
                            } ?>">
                                        <i class="fas fa-plane nav-icon"></i>
                                        <p>Aeroportuária</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
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