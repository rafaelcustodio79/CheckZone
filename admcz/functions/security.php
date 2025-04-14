<?php
/**
 * Funções de segurança para o sistema ChecZone
 * 
 * Este arquivo contém funções que auxiliam na segurança do sistema,
 * como verificação de CSRF, limite de tentativas de login, etc.
 */

// Definição dos tipos de usuário baseados na tabela users_type
define('TIPO_SUPER', 1);      // Super
define('TIPO_MASTER', 2);     // Master
define('TIPO_GERENTE', 3);    // Gerente
define('TIPO_INSPETOR', 4);   // Inspetor
define('TIPO_TECNICO', 5);    // Técnico
define('TIPO_AUDITOR', 6);    // Auditor
define('TIPO_FINANCEIRO', 7); // Financeiro

/**
 * Gera um token CSRF para o formulário
 * 
 * @return string Token gerado
 */
function gerarCSRFToken() {
    if (!isset($_SESSION['csrf_token']) || time() > $_SESSION['csrf_token_time'] + 3600) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica se o token CSRF enviado é válido
 * 
 * @param string $token Token a ser verificado
 * @return bool True se o token for válido, false caso contrário
 */
function verificaCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    
    // Regenera token após uso para proteger contra ataques de replay
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
    
    return true;
}

/**
 * Verifica o captcha do Google reCAPTCHA
 * 
 * @param string $response Resposta do captcha
 * @return bool True se o captcha for válido, false caso contrário
 */
function verificaCaptcha($response) {
    $config = require '../config/config.php';
    $secretKey = $config['recaptcha_secret_key'] ?? '6LcrmuoqAAAAAEmYiQMenguDSYKrgg5yIAm5lTxb';
    
    // Dados para requisição
    $data = [
        'secret' => $secretKey,
        'response' => $response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    
    // Cria o contexto para a requisição
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $verificacao = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
    
    if ($verificacao === false) {
        return false;
    }
    
    $resposta = json_decode($verificacao);
    return isset($resposta->success) && $resposta->success === true;
}

/**
 * Verifica se há um limite de tentativas de login excedido para o IP
 * 
 * @param string $ip Endereço IP
 * @return bool True se o limite for excedido, false caso contrário
 */
function verificaLimiteTentativas($ip) {
    try {
        $PDO = db_connect();
        
        // Limpa registros antigos (mais de 15 minutos)
        $sql = "DELETE FROM login_attempts WHERE attempt_time < NOW() - INTERVAL 15 MINUTE";
        $PDO->exec($sql);
        
        // Verifica tentativas nos últimos 15 minutos
        $sql = "SELECT COUNT(*) as total FROM login_attempts WHERE ip_address = :ip AND attempt_time > NOW() - INTERVAL 15 MINUTE";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':ip', $ip);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Se houver mais de 5 tentativas em 15 minutos, bloqueia temporariamente
        return ($resultado['total'] >= 5);
    } catch (PDOException $e) {
        logErro('Erro ao verificar limite de tentativas: ' . $e->getMessage());
        return false; // Em caso de erro, permitir login para não bloquear acesso legítimo
    }
}

/**
 * Incrementa o contador de tentativas de login para um IP
 * 
 * @param string $ip Endereço IP
 * @param string $email Email utilizado (opcional)
 */
function incrementarTentativaLogin($ip, $email = null) {
    try {
        $PDO = db_connect();
        
        $sql = "INSERT INTO login_attempts (ip_address, email, attempt_time) VALUES (:ip, :email, NOW())";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        // Se for um email específico, verifica se precisamos bloquear a conta
        if ($email) {
            $sql = "SELECT COUNT(*) as total FROM login_attempts WHERE email = :email AND attempt_time > NOW() - INTERVAL 15 MINUTE";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Se houver mais de 3 tentativas para o mesmo email, bloqueia a conta por 30 minutos
            if ($resultado['total'] >= 3) {
                $sql = "UPDATE users SET bloqueado_ate = DATE_ADD(NOW(), INTERVAL 30 MINUTE), tentativas_login = tentativas_login + 1 WHERE email = :email";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
            }
        }
    } catch (PDOException $e) {
        logErro('Erro ao incrementar tentativa de login: ' . $e->getMessage());
    }
}

/**
 * Reseta o contador de tentativas de login após sucesso
 * 
 * @param string $ip Endereço IP
 * @param string $email Email do usuário
 */
function resetarTentativasLogin($ip, $email) {
    try {
        $PDO = db_connect();
        
        // Remove tentativas para este IP e email
        $sql = "DELETE FROM login_attempts WHERE ip_address = :ip OR email = :email";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    } catch (PDOException $e) {
        logErro('Erro ao resetar tentativas de login: ' . $e->getMessage());
    }
}

/**
 * Registra uma tentativa de acesso no log
 * 
 * @param string $mensagem Descrição da tentativa
 * @param string $ip Endereço IP
 * @param string $email Email utilizado (opcional)
 */
function registrarTentativaAcesso($mensagem, $ip, $email = null) {
    try {
        $PDO = db_connect();
        
        $sql = "INSERT INTO security_log (evento, descricao, email, ip_address, data_hora) 
                VALUES ('tentativa_acesso', :descricao, :email, :ip, NOW())";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':descricao', $mensagem);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':ip', $ip);
        $stmt->execute();
    } catch (PDOException $e) {
        logErro('Erro ao registrar tentativa de acesso: ' . $e->getMessage());
    }
}

/**
 * Registra um acesso bem-sucedido no log
 * 
 * @param int $userId ID do usuário
 * @param string $mensagem Descrição do acesso
 * @param string $ip Endereço IP
 */
function registrarAcesso($userId, $mensagem, $ip) {
    try {
        $PDO = db_connect();
        
        $sql = "INSERT INTO access_log (user_id, evento, descricao, ip_address, data_hora) 
                VALUES (:user_id, 'login', :descricao, :ip, NOW())";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':descricao', $mensagem);
        $stmt->bindParam(':ip', $ip);
        $stmt->execute();
    } catch (PDOException $e) {
        logErro('Erro ao registrar acesso: ' . $e->getMessage());
    }
}

/**
 * Registra um erro no log do sistema
 * 
 * @param string $mensagem Mensagem de erro
 */
function logErro($mensagem) {
    $log_file = '../logs/errors.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
    $log_message = "[$timestamp] [$ip] $mensagem" . PHP_EOL;
    
    // Certifica-se de que o diretório de logs existe
    if (!is_dir('../logs')) {
        mkdir('../logs', 0755, true);
    }
    
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

/**
 * Verifica se o usuário tem permissão para acessar um recurso específico
 * 
 * @param string $permissao Nome da permissão (ex: 'admin_access', 'manage_clients', etc)
 * @return bool True se o usuário tem permissão, false caso contrário
 */
function verificaPermissao($permissao) {
    // Verifica se o usuário está logado
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        return false;
    }
    
    // Super têm acesso a tudo
    if (isset($_SESSION['users_type_id']) && $_SESSION['users_type_id'] == TIPO_SUPER) {
        return true;
    }
    
    // Master têm acesso a quase tudo, exceto funcionalidades específicas de Super
    if (isset($_SESSION['users_type_id']) && $_SESSION['users_type_id'] == TIPO_MASTER) {
        // Lista de permissões exclusivas de Super
        $super_only = ['super_admin_functions'];
        
        if (!in_array($permissao, $super_only)) {
            return true;
        }
    }
    
    // Gerentes têm acesso a permissões específicas
    if (isset($_SESSION['users_type_id']) && $_SESSION['users_type_id'] == TIPO_GERENTE) {
        // Permissões específicas de gerente conforme a tabela
        $gerente_permissions = ['manage_users', 'manage_clients', 'view_reports', 'financial_access'];
        
        if (in_array($permissao, $gerente_permissions)) {
            return true;
        }
    }
    
    // Inspetor tem permissões específicas
    if (isset($_SESSION['users_type_id']) && $_SESSION['users_type_id'] == TIPO_INSPETOR) {
        // Permissões específicas de inspetor conforme a tabela
        $inspetor_permissions = ['perform_inspections', 'create_inspections'];
        
        if (in_array($permissao, $inspetor_permissions)) {
            return true;
        }
    }
    
    // Verifica se a permissão específica existe nas permissões do usuário
    if (isset($_SESSION['permissoes']) && is_array($_SESSION['permissoes'])) {
        return in_array($permissao, $_SESSION['permissoes']);
    }
    
    return false;
}

/**
 * Verifica se a sessão expirou por tempo
 * 
 * @return bool True se a sessão expirou, false caso contrário
 */
function verificaSessaoExpirada() {
    $tempo_maximo = 3600; // 1 hora em segundos
    
    if (!isset($_SESSION['ultimo_acesso'])) {
        return true;
    }
    
    if (time() - $_SESSION['ultimo_acesso'] > $tempo_maximo) {
        return true;
    }
    
    // Atualiza o tempo de último acesso
    $_SESSION['ultimo_acesso'] = time();
    
    return false;
}

/**
 * Purifica entrada de texto para prevenir XSS
 * 
 * @param string $data Dados a serem purificados
 * @return string Dados purificados
 */
function purificarTexto($data) {
    // Remove espaços em branco no início e no fim
    $data = trim($data);
    
    // Remove tags HTML e PHP
    $data = strip_tags($data);
    
    // Converte caracteres especiais em entidades HTML
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    return $data;
}

/**
 * Função para sanitizar arrays de entrada recursivamente
 * 
 * @param array $array Array a ser sanitizado
 * @return array Array sanitizado
 */
function sanitizarArray($array) {
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = sanitizarArray($value);
        } else {
            $array[$key] = purificarTexto($value);
        }
    }
    
    return $array;
}

/**
 * Adiciona uma função auxiliar para carregar permissões do banco de dados
 * 
 * @param int $userTypeId ID do tipo de usuário
 * @param PDO $pdo Conexão PDO com banco de dados
 * @return array Lista de permissões
 */
function carregarPermissoes($userTypeId, $pdo) {
    $permissoes = [];
    
    try {
        // Consulta permissões associadas ao tipo de usuário
        $sql = "SELECT p.nome FROM permissoes p
                INNER JOIN user_type_permissoes utp ON p.id = utp.permissao_id
                WHERE utp.user_type_id = :user_type_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_type_id', $userTypeId);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $permissoes[] = $row['nome'];
        }
    } catch (PDOException $e) {
        logErro('Erro ao carregar permissões: ' . $e->getMessage());
    }
    
    return $permissoes;
}
?>