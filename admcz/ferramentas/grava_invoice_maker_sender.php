<?php
session_start();

// inclui o arquivo de inicialização
require '../functions/globals.php';

$PDO = db_connect();

$idEmpresa = isset($_POST['id_empresa']) ? $_POST['id_empresa'] : null;
$company = isset($_POST['company']) ? $_POST['company'] : null;
$vat_number = isset($_POST['vat_number']) ? $_POST['vat_number'] : null;
$address = isset($_POST['address']) ? $_POST['address'] : null;
$post_code = isset($_POST['post_code']) ? $_POST['post_code'] : null;
$city = isset($_POST['city']) ? $_POST['city'] : null;
$country = isset($_POST['country']) ? $_POST['country'] : null;
$city_country = $city . ', ' . $country;
$name_im_sender = isset($_POST['name']) ? $_POST['name'] : null;
$email_im_sender = isset($_POST['email']) ? $_POST['email'] : null;


$stmt = $PDO->prepare("INSERT INTO invoice_maker_sender (id_empresa, company, vat_number, address, post_code, city_country, name_im_sender, email_im_sender) 
                            VALUES (:id_empresa, :company, :vat_number, :address, :post_code, :city_country, :name_im_sender, :email_im_sender)");
$stmt->bindParam(':id_empresa', $idEmpresa);
$stmt->bindParam(':company', $company);
$stmt->bindParam(':vat_number', $vat_number);
$stmt->bindParam(':address', $address);
$stmt->bindParam(':post_code', $post_code);
$stmt->bindParam(':city_country', $city_country);
$stmt->bindParam(':name_im_sender', $name_im_sender);
$stmt->bindParam(':email_im_sender', $email_im_sender);

if ($stmt->execute()) {
    header('Location: ../ferramentas.php?a=ferramentas&b=invoice_maker_criar&flag=success&tip=Exportador cadastrado com sucesso!');
} else {
    header('Location: ../ferramentas.php?a=ferramentas&b=invoice_maker_criar&flag=erro&tip=Não foi possível realizar a operação.');
}