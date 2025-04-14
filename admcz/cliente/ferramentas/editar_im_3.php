<?php
session_start();

// inclui o arquivo de inicialização
require '../../functions/globals.php';

$idEmpresa = $_SESSION['empresa_id'];
$idUsuario = $_SESSION['user_id'];
$invoice_number = isset($_POST['invoice_number']) ? $_POST['invoice_number'] : null;
$id_im_receiver = isset($_POST['id_im_receiver']) ? $_POST['id_im_receiver'] : null;

$idIM = isset($_POST['idIM']) ? $_POST['idIM'] : null; // ID do registro a ser atualizado

if ($idIM) {
    // Fazer o update no banco de dados
    try {
        $PDO = db_connect();
        $stmt = $PDO->prepare("UPDATE invoice_maker 
                               SET id_im_receiver = :id_im_receiver
                               WHERE id_im = :idIM AND id_empresa = :empresa_id AND id_usuario = :usuario_id AND invoice_number = :invoice_number");
        
        $stmt->bindParam(':empresa_id', $idEmpresa);
        $stmt->bindParam(':usuario_id', $idUsuario);
        $stmt->bindParam(':invoice_number', $invoice_number);
        $stmt->bindParam(':id_im_receiver', $id_im_receiver);
        $stmt->bindParam(':idIM', $idIM); // Parâmetro para o ID do registro a ser atualizado
        
        $stmt->execute();

        // Redirecionamento com mensagem de sucesso
        $_SESSION['active_step'] = 3;
        header('Location: ../../cliente.php?a=ferramentas&b=invoice_maker_editar&idIM='.$idIM.'&invoice='.$invoice_number.'&flag=success&tip=Importador alterado com sucesso!&tabAtiva=2');
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