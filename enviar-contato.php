<?php
header('Content-Type: application/json');

// Sua chave secreta do reCAPTCHA
$secret_key = "6LfFiWUrAAAAAOpiWvlEYDb4pDWyP1RX_0Izfeq5";
$response = $_POST['g-recaptcha-response'] ?? '';

// Verificação do reCAPTCHA
$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$response}");
$captcha_success = json_decode($verify);

if (!$captcha_success->success) {
    echo json_encode([
        'success' => false, 
        'message' => 'Por favor, complete a verificação "Não sou um robô"'
    ]);
    exit;
}

// Sanitização dos dados
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
$mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING);

// Validação básica
if (empty($nome) || empty($email) || empty($mensagem)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Preencha todos os campos obrigatórios'
    ]);
    exit;
}

// 1. CONFIGURAÇÃO DE E-MAIL (RECOMENDADO)
$to = "app.codeone@gmail.com"; // Substitua pelo seu e-mail
$subject = "Novo contato CODEONE: $nome";
$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";

$email_body = "Nome: $nome\n";
$email_body .= "Email: $email\n";
$email_body .= "Telefone: $telefone\n\n";
$email_body .= "Mensagem:\n$mensagem";

// Tentativa de enviar e-mail
if (mail($to, $subject, $email_body, $headers)) {
    echo json_encode([
        'success' => true, 
        'message' => 'Mensagem enviada com sucesso!'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao enviar mensagem. Tente novamente mais tarde.'
    ]);
}

// 2. ALTERNATIVA: Salvar em arquivo (para testes sem servidor de e-mail)
/*
$dados = date('d/m/Y H:i:s') . "\n" . $email_body . "\n\n";
file_put_contents('contatos.txt', $dados, FILE_APPEND);
echo json_encode(['success' => true, 'message' => 'Mensagem recebida!']);
*/
?>