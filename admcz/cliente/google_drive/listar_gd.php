<?php
$PDO = db_connect();

if (isset($_GET['ano'])) {
    $anoSelecionado = $_GET['ano'];
}else{
    $anoSelecionado = date("Y");
}

$idEmpresa = $dadosUsuario['id_empresa'];

$sqlDrive = "SELECT * FROM pasta_drive WHERE id_empresa = :id_empresa AND ano = :ano";
$stmtDrive = $PDO->prepare($sqlDrive);
$stmtDrive->bindParam(':id_empresa', $idEmpresa, PDO::PARAM_INT);
$stmtDrive->bindParam(':ano', $anoSelecionado, PDO::PARAM_INT);
$stmtDrive->execute();
$resultadosDrive = $stmtDrive->fetch(PDO::FETCH_ASSOC);
$idPastaDrive = $resultadosDrive['id_pasta_drive'];
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Meu Drive</h1>
                <small>Pastas e Arquivos dos Processos</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="../cliente_inicial.php">Home</a></li>
                    <li class="breadcrumb-item">Meu Drive</li>
                    <li class="breadcrumb-item active">Pastas e Arquivos dos Processos</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Listando Pastas e Arquivos: <strong><?=$anoSelecionado;?></strong></h3>
                        <div class="ml-auto">
                            <select class="form-control" id="selectAno" onchange="redirecionarComAno()">
                                <option value="">Selecione o Ano</option>
                                <?php
            // Gera os anos dinamicamente
            $anoAtual = date("Y");
            for ($ano = $anoAtual; $ano >= 2000; $ano--) {
                $selecionado = (isset($_GET['ano']) && $_GET['ano'] == $ano) ? 'selected' : '';
                echo "<option value=\"$ano\" $selecionado>$ano</option>";
            }
            ?>
                            </select>
                        </div>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">

                        <?php
$apiKey = $googleApiKey;
$folderId = isset($_GET['folderId']) ? $_GET['folderId'] : $idPastaDrive;
$url = "https://www.googleapis.com/drive/v3/files?q='" . $folderId . "'%20in%20parents&key=" . $apiKey . "&fields=files(id,%20name,%20mimeType,%20webViewLink)";
$response = file_get_contents($url);
$data = json_decode($response, true);
?>

                        <div class="container mt-4">
                            <div class="row">
                                <?php if (isset($data['files'])): ?>
                                <?php foreach ($data['files'] as $file): ?>
                                <?php
                $fileName = htmlspecialchars($file['name']);
                $fileId = $file['id'];
                $fileLink = htmlspecialchars($file['webViewLink']) . "?usp=sharing";
                $isFolder = $file['mimeType'] === 'application/vnd.google-apps.folder';
                $icon = $isFolder ? 'fas fa-folder' : 'fas fa-file';
                $target = 'target="_blank"';
                ?>

                                <div class="col-md-3 mb-4">
                                    <a href="<?php echo $fileLink; ?>" <?php echo $target; ?>
                                        class="file-block d-block text-decoration-none">
                                        <div class="file-icon text-center">
                                            <i class="<?php echo $icon; ?> fa-3x text-secondary"></i>
                                        </div>
                                        <div class="file-name text-center mt-2">
                                            <?php echo $fileName; ?>
                                        </div>
                                    </a>
                                </div>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <p>Nenhum arquivo ou pasta encontrado.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <style>
                        .file-block {
                            padding: 10px;
                            background-color: #f8f9fa;
                            border: 1px solid #e9ecef;
                            border-radius: 8px;
                            transition: background-color 0.3s;
                        }

                        .file-block:hover {
                            background-color: #e2e6ea;
                        }

                        .file-icon i {
                            color: #5f6368;
                        }

                        .file-name {
                            font-size: 14px;
                            color: #202124;
                            white-space: nowrap;
                            overflow: hidden;
                            text-overflow: ellipsis;
                        }
                        </style>


                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

<script>
$(document).ready(function() {
    // Verifique se o parâmetro 'flag' está presente na URL
    var flagParam =
        '<?php echo isset($_GET['flag']) ? $_GET['flag'] : ''; ?>';
    var tipParam =
        '<?php echo isset($_GET['tip']) ? $_GET['tip'] : ''; ?>';

    // Se 'flag' estiver presente e for igual a 'erro', exiba a notificação de erro
    if (flagParam == 'erro') {
        toastr.error(tipParam);
    }
    if (flagParam == 'warning') {
        toastr.warning(tipParam);
    }
    if (flagParam == 'success') {
        toastr.success(tipParam);
    }
});

function redirecionarComAno() {
    const ano = document.getElementById('selectAno').value;
    if (ano) {
        window.location.href = 'cliente.php?a=google_drive&b=listar_gd&ano=' + ano;
    }
}

function toggleGerenteConta() {
    var tipoEmpresa = document.getElementById("id_tipo_empresa").value;
    var gerenteConta = document.getElementById("gerenteConta");
    if (tipoEmpresa == "2") {
        gerenteConta.style.display = "block";
    } else {
        gerenteConta.style.display = "none";
    }
}

function openEditModal(userId) {
    // Aqui você pode adicionar lógica para buscar os dados do usuário se necessário.
    console.log("Abrindo modal para o usuário com ID: " + userId);

    // Exemplo de como abrir o modal (se estiver usando Bootstrap 4 ou 5):
    $('#editUserModal').modal('show');

    // Se precisar carregar dados via AJAX, pode fazer algo como:
    $.ajax({
        url: 'aduaneiro/empresas_buscar.php',
        method: 'GET',
        data: {
            id: userId
        },
        success: function(response) {
            // Assuming the response is a JSON object with user data
            var userData = JSON.parse(response);

            // Populate the modal fields with the user data
            $('#id_empresa').val(userData.id_empresa);
            $('#edit_id_tipo_empresa').val(userData.id_tipo_empresa);
            $('#edit_email_principal').val(userData.email_principal);
            $('#edit_nome_empresa').val(userData.nome_empresa);
            $('#edit_segmento_empresa').val(userData.segmento_empresa);
            $('#edit_cnpj').val(userData.cnpj);
            $('#edit_localizacao').val(userData.localizacao);
            $('#edit_idUsuario').val(userData.id_gerente_conta);
            // $('#edit_especializacoes').val(userData.especializacoes);
            // $('#edit_pontos_fortes').val(userData.pontos_fortes);
            $('#edit_website').val(userData.website);
            $('#edit_telefone_empresa').val(userData.telefone_empresa);

        },
        error: function() {
            alert('Error fetching user data.');
        }
    });
}
</script>