<?php
require_once 'includes/config.php';

// Registrar visualização
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    
    // Buscar currículo pelo slug (usando ID como slug por simplicidade)
    $stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ? AND active = 1");
    $stmt->execute([$slug]);
    $resume = $stmt->fetch();
    
    if ($resume) {
        // Registrar visualização
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        
        $stmt = $pdo->prepare("INSERT INTO views (resume_id, ip_address, user_agent, referer) VALUES (?, ?, ?, ?)");
        $stmt->execute([$resume['id'], $ip, $user_agent, $referer]);
        
        // Atualizar contador
        $pdo->prepare("UPDATE resumes SET total_views = total_views + 1 WHERE id = ?")->execute([$resume['id']]);
        
        // Incluir visualização do currículo
        include 'views/resume_view.php';
    } else {
        include 'views/404.php';
    }
} else {
    // Home - Listar currículos
    $stmt = $pdo->prepare("SELECT * FROM resumes WHERE active = 1 ORDER BY created_at DESC");
    $stmt->execute();
    $resumes = $stmt->fetchAll();
    
    include 'views/home.php';
}
?>
