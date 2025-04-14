<?php
// inclui o arquivo do bd
require 'db.php';

// Conecta com o MySQL usando PDO
function db_connect()
{
    $PDO = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
    return $PDO;
}

// Verifica se o usuário está logado
function isLoggedIn()
{
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        return false;
    } else {
        return true;
    }
}

const RECAPTCHA_SECRET_KEY = '6LcrmuoqAAAAAAg5ULeki7gLrLBSAXIEN3YVpx50';
const RECAPTCHA_SITE_KEY = '6LcrmuoqAAAAAEmYiQMenguDSYKrgg5yIAm5lTxb';

/**
 * Converte datas entre os padrões ISO e brasileiro
 * Fonte: http://rberaldo.com.br/php-conversao-de-datas-formato-brasileiro-e-formato-iso/
 */
function dateConvert($date)
{
    // Verificar se há horas no formato
    if (strpos($date, ' ') !== false) {
        // A data contém tempo
        [$datePart, $timePart] = explode(' ', $date);
    } else {
        // A data não contém tempo
        $datePart = $date;
        $timePart = '';
    }

    if (!strstr($datePart, '/')) {
        // $date está no formato ISO (yyyy-mm-dd) e deve ser convertida para dd/mm/yyyy
        sscanf($datePart, '%d-%d-%d', $y, $m, $d);
        $convertedDate = sprintf('%02d/%02d/%04d', $d, $m, $y);
    } else {
        // $date está no formato brasileiro (dd/mm/yyyy) e deve ser convertida para ISO
        sscanf($datePart, '%d/%d/%d', $d, $m, $y);
        $convertedDate = sprintf('%04d-%02d-%02d', $y, $m, $d);
    }

    // Verifica se há parte de tempo e a adiciona no final
    if ($timePart) {
        $convertedDate .= ' ' . $timePart;
    }

    return $convertedDate;
}


function clean_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Função para converter valores no formato brasileiro para formato numérico
function format_to_number($value)
{
    // Remove pontos (separadores de milhar) e substitui vírgula por ponto (separador decimal)
    $value = str_replace('.', '', $value);
    $value = str_replace(',', '.', $value);
    return $value;
}

$googleApiKey = 'AIzaSyDZT8X3HETmXGPUhXhDpyz977ElQU922wo';

header('Content-Type: text/html; charset=utf-8');