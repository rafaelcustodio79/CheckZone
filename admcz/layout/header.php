<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>



    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <?php
                if (isset($_SESSION['company_logo'])) {
                    $logoEmpresa = $_SESSION['company_logo'];
                } else {
                    $logoEmpresa = 'sem-foto.png';
                }
            ?>
            <img src="img/logos/<?= $logoEmpresa; ?>" style="max-height: 30px;" alt="Logo da empresa">
            <?= $_SESSION['company_name']; ?>
            <?php 
                switch ($_SESSION['users_type_id']) {
                    case 1:
                        echo '<span class="badge badge-danger">SUPER USUÁRUIO</span>';
                        break;
                    case 2:
                        echo '<span class="badge badge-success">MASTER</span>';
                        break;
                    case 3:
                        echo '<span class="badge badge-info">GERENTE</span>';
                        break;
                    case 4:
                        echo '<span class="badge badge-primary">INSPETOR</span>';
                        break;
                    case 5:
                        echo '<span class="badge badge-secondary">TÉCNICO</span>';
                        break;
                    case 6:
                        echo '<span class="badge badge-warning">AUDITOR</span>';
                        break;
                    case 7:
                    echo '<span class="badge badge-dark">FINANCEIRO</span>';
                    break;
                }
            ?>
        </li>
        &nbsp;&nbsp;&nbsp;
        <li class="nav-item"><a href="login/logout.php" class="btn btn-xs bg-ccomex"><i class="fas fa-sign-out-alt"></i>
                Logout</a></li>
    </ul>
</nav>