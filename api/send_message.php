<?php
require_once '../includes/config.php';

// Apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Validar dados
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validação básica
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios']);
    exit;
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'E-mail inválido']);
    exit;
}

// Sanitizar dados
$name = htmlspecialchars($name);
$email = htmlspecialchars($email);
$phone = htmlspecialchars($phone);
$subject = htmlspecialchars($subject);
$message = htmlspecialchars($message);
$resume_id = intval($_POST['resume_id'] ?? 0);

try {
    // Inserir mensagem no banco de dados
    $stmt = $pdo->prepare("INSERT INTO messages (resume_id, name, email, phone, subject, message, created_at, is_read) VALUES (?, ?, ?, ?, ?, ?, NOW(), 0)");
    $result = $stmt->execute([$resume_id > 0 ? $resume_id : null, $name, $email, $phone, $subject, $message]);
    
    if ($result) {
        // Enviar email de confirmação para o visitante
        $to = $email;
        $subject_confirm = "Recebemos sua mensagem - " . htmlspecialchars($subject);
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: noreply@cv.techinnovationbr.com.br\r\n";
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #10b981 0%, #8b5cf6 100%); color: white; padding: 20px; border-radius: 5px; }
                .content { padding: 20px; background: #f8f9fa; border-radius: 5px; margin-top: 20px; }
                .footer { text-align: center; margin-top: 20px; color: #999; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class=\"container\">
                <div class=\"header\">
                    <h2>Obrigado por sua mensagem!</h2>
                </div>
                <div class=\"content\">
                    <p>Olá " . $name . ",</p>
                    <p>Recebemos sua mensagem com sucesso. Rodrigo entrará em contato em breve.</p>
                    <p><strong>Detalhes da sua mensagem:</strong></p>
                    <ul>
                        <li><strong>Assunto:</strong> " . $subject . "</li>
                        <li><strong>Seu e-mail:</strong> " . $email . "</li>
                        <li><strong>Seu telefone:</strong> " . ($phone ?: 'Não informado') . "</li>
                    </ul>
                </div>
                <div class=\"footer\">
                    <p>&copy; 2024 - Rodrigo Marchi Gonella. Todos os direitos reservados.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        @mail($to, $subject_confirm, $body, $headers);
        
        echo json_encode(['success' => true, 'message' => '✅ Mensagem enviada com sucesso! Você receberá uma confirmação no seu email.']);
    } else {
        echo json_encode(['success' => false, 'message' => '❌ Erro ao salvar mensagem']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '❌ Erro ao processar mensagem: ' . $e->getMessage()]);
}
?>
