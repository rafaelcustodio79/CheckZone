<?php
session_start();

// inclui o arquivo de inicialização
require '../../functions/globals.php';

$idEmpresa = $_SESSION['empresa_id'];
$idUsuario = $_SESSION['user_id'];
$invoice_number = isset($_POST['invoice_number']) ? $_POST['invoice_number'] : null;
$id_im_sender = isset($_POST['id_im_sender']) ? $_POST['id_im_sender'] : null;

$idIM = isset($_POST['idIM']) ? $_POST['idIM'] : null; // ID do registro a ser atualizado

if ($idIM) {
    // Fazer o update no banco de dados
    try {
        $PDO = db_connect();
        $stmt = $PDO->prepare("UPDATE invoice_maker 
                               SET id_im_sender = :id_im_sender
                               WHERE id_im = :idIM AND id_empresa = :empresa_id AND id_usuario = :usuario_id AND invoice_number = :invoice_number");
        
        $stmt->bindParam(':empresa_id', $idEmpresa);
        $stmt->bindParam(':usuario_id', $idUsuario);
        $stmt->bindParam(':invoice_number', $invoice_number);
        $stmt->bindParam(':id_im_sender', $id_im_sender);
        $stmt->bindParam(':idIM', $idIM); // Parâmetro para o ID do registro a ser atualizado
        
        $stmt->execute();

        // Redirecionamento com mensagem de sucesso
        $_SESSION['active_step'] = 2;
        header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_editar&idIM='.$idIM.'&invoice='.$invoice_number.'&flag=success&tip=Exportador alterado com sucesso!&tabAtiva=2');
        exit();
    } catch (PDOException $e) {
        // Redirecionamento em caso de erro
        header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_editar&idIM='.$idIM.'&invoice='.$invoice_number.'&flag=erro&tip=Não foi possível realizar a operação. '.$e->getMessage());
    }
} else {
    // Lidar com o caso de não ter o ID do registro para atualizar
    header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_editar&flag=erro&tip=ID do registro não encontrado.');
    exit();
}