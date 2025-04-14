<?php
$idEmpresa = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($idEmpresa === null) {
    // ID inválido, redirecionar ou exibir mensagem de erro
    echo "ID inválido.";
    exit;
}

$PDO = db_connect();

$sql = "SELECT emp.*, usu.nome AS gerenteConta FROM empresa emp
        INNER JOIN usuario usu ON emp.id_gerente_conta = usu.id
        WHERE emp.id_empresa = :id_empresa";
$stmt = $PDO->prepare($sql);
$stmt->bindParam(':id_empresa', $idEmpresa);
$stmt->execute();
$resultados = $stmt->fetch(PDO::FETCH_ASSOC);

$sqlUsuarios = "SELECT * FROM usuario
        WHERE id_empresa = :id_empresa";
$stmtUsuarios = $PDO->prepare($sqlUsuarios);
$stmtUsuarios->bindParam(':id_empresa', $idEmpresa);
$stmtUsuarios->execute();
$resultadosUsuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
?>
<style type="text/css">
.card-primary:not(.card-outline)>.card-header {
    background-color: #046868;
    color: #fff;
}

.card-primary.card-outline {
    border-top: 3px solid #046868;
}

.nav-pills .nav-link.active,
.nav-pills .show>.nav-link {
    color: #fff;
    background-color: #046868;
}

.profile-user-img {
    border: none !important;
}
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Configurações</h1>
                <small>Profile da Empresa</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../index.php"> Home</a></li>
                    <li class="breadcrumb-item">Configurações</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">

                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <?php
                            if(empty($resultados['logo'])) {
                                $fotoAssociado = 'img/logos/sem-foto.jpg';
                            } else {
                                $fotoAssociado = 'img/logos/'.$resultados['logo'];
                            }
?>
                            <img class="profile-user-img img-fluid" src="<?=$fotoAssociado;?>" alt="Foto Associado">
                        </div>

                        <h3 class="profile-username text-center">
                            <?=$resultados['nome_empresa'];?>
                        </h3>

                        <p class="text-muted text-center">
                            na plataforma desde<br>
                            <b><?=dateConvert($resultados['data_add']);?></b>
                        </p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Tipo de Empresa</b>
                                <a class="float-right">
                                    <?php if($resultados['id_tipo_empresa']==1) : ?>
                                    Administrador
                                    <?php else : ?>
                                    Cliente
                                    <?php endif;?>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b>Segmento</b> <a class="float-right"><?=$resultados['segmento_empresa'];?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Gerente de conta</b> <a class="float-right"><?=$resultados['gerenteConta'];?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Usuários</b> <a class="float-right"><?=count($resultadosUsuarios);?></a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <!-- About Me Box -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Dados Gerais</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <strong><i class="far fa-address-card mr-1"></i> CNPJ</strong>

                        <p class="text-muted">
                            <?=$resultados['cnpj'];?>
                        </p>

                        <hr>

                        <strong><i class="fas fa-map-marker-alt mr-1"></i> Localização</strong>

                        <p class="text-muted">
                            <?=$resultados['localizacao'];?><br>
                        </p>

                        <hr>

                        <strong><i class="fas fa-user-tie mr-1"></i> Website</strong>

                        <p class="text-muted">
                            <?=$resultados['website'];?>
                        </p>

                        <hr>

                        <strong><i class="fas fa-address-book mr-"></i> Contatos</strong>

                        <p class="text-muted">
                            <i class="fas fa-phone"></i>
                            <?=$resultados['telefone_empresa'];?><br>
                            <i class="fas fa-envelope"></i>
                            <?=$resultados['email_principal'];?>
                        </p>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#usuarios" data-toggle="tab">Usuários</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#googledrive" data-toggle="tab">Google Drive</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#link3" data-toggle="tab">Link3</a>
                            </li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">

                            <div class="active tab-pane" id="usuarios">
                                <p>Usuários vinculados</p>

                                <table id="listaUsuarios" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Foto</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th class="text-center">Telefone</th>
                                            <th class="text-center">Tipo</th>
                                            <th class="text-center" style="width: 50px;">Ativo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultadosUsuarios as $resultado) : ?>
                                        <tr>
                                            <td class="text-center">
                                                <img src="img/users/<?= $resultado['foto']; ?>"
                                                    class="img-circle elevation-2 img-zoom" alt="Foto do usuário"
                                                    style="max-height: 30px;">
                                            </td>
                                            <td>
                                                <?= $resultado['nome']; ?>
                                            </td>
                                            <td>
                                                <?= $resultado['email']; ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $resultado['telefone']; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                        $tipoUsuario = '';
                                            switch ($resultado['id_tipo_usuario']) {
                                                case 1:
                                                    $tipoUsuario = 'Administrador';
                                                    break;
                                                case 2:
                                                    $tipoUsuario = 'Cliente';
                                                    break;
                                                case 3:
                                                    $tipoUsuario = 'Operacional';
                                                    break;
                                                default:
                                                    $tipoUsuario = 'Desconhecido';
                                                    break;
                                            }
                                            ?>

                                                <?= $tipoUsuario; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                                                    if ($resultado['ativo'] == 1) {
                                                                                        echo "<a href='config.php?a=config&b=usuarios_ativar&id={$resultado['id']}&acao=0'><i class='fas fa-check-circle text-green' title='Sim'></i></a>";
                                                                                    } else {
                                                                                        echo "<a href='config.php?a=config&b=usuarios_ativar&id={$resultado['id']}&acao=1'><i class='fas fa-times-circle text-danger' title='Não'></i></a>";
                                                                                    }
                                            ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                            </div>

                            <div class="tab-pane" id="googledrive">

                                <?php
                                $sqlDrive = "SELECT * FROM pasta_drive WHERE id_empresa = :id_empresa ORDER BY ano DESC";
                                $stmtDrive = $PDO->prepare($sqlDrive);
                                $stmtDrive->bindParam(':id_empresa', $idEmpresa, PDO::PARAM_INT);
                                $stmtDrive->execute();
                                $resultadosDrive = $stmtDrive->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <table class="table table-striped table-hover" id="listaIds">
                                    <tr>
                                        <th>Ano</th>
                                        <th>ID Pasta</th>
                                    </tr>
                                    <?php foreach ($resultadosDrive as $resultadoDrive) : ?>
                                    <tr>
                                        <td><?=$resultadoDrive['ano'];?></td>
                                        <td><?=$resultadoDrive['id_pasta_drive'];?></td>
                                    </tr>
                                    <?php endforeach;?>
                                </table>

                            </div>

                            <div class="tab-pane" id="link3">

                                Info do Link

                            </div>


                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- PDF html2pdf -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<script>
var printButtons = document.querySelectorAll('.btnImprimir');

printButtons.forEach(function(button) {
    button.addEventListener('click', function(event) {
        // Impede que o link navegue para outra página
        event.preventDefault();

        // Seleciona a carteirinha mais próxima do botão clicado
        var card = button.parentElement.previousElementSibling.querySelector('.card-virtual');

        // Configurações para o PDF
        var opt = {
            margin: 1,
            filename: 'carteirinha_virtual.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2
            },
            jsPDF: {
                unit: 'in',
                format: 'A4',
                orientation: 'portrait'
            }
        };

        // Gera o PDF para a carteirinha correspondente
        html2pdf().from(card).set(opt).save();
    });
});
</script>