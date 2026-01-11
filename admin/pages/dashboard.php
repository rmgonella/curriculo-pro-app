<?php
$stmt = $pdo->prepare("SELECT * FROM resumes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['admin_id']]);
$resumes = $stmt->fetchAll();

if (empty($resumes)) {
    echo '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Você ainda não criou nenhum currículo. <a href="index.php?route=resume&action=create">Clique aqui para começar</a></div>';
    // Footer será incluído pelo index.php
    exit;
}

// Estatísticas gerais
$total_views = 0;
// Contar TODAS as mensagens (incluindo as sem resume_id)
$stmt_all_messages = $pdo->prepare("SELECT COUNT(*) as total FROM messages m LEFT JOIN resumes r ON m.resume_id = r.id WHERE m.resume_id IS NULL OR r.user_id = ?");
$stmt_all_messages->execute([$_SESSION['admin_id']]);
$total_messages = $stmt_all_messages->fetch()['total'];

$stmt_unread = $pdo->prepare("SELECT COUNT(*) as total FROM messages m LEFT JOIN resumes r ON m.resume_id = r.id WHERE (m.resume_id IS NULL OR r.user_id = ?) AND m.is_read = 0");
$stmt_unread->execute([$_SESSION['admin_id']]);
$unread_messages = $stmt_unread->fetch()['total'];
$total_experiences = 0;
$total_skills = 0;
$total_education = 0;
$total_projects = 0;
$total_certifications = 0;

foreach ($resumes as $resume) {
    $total_views += $resume['total_views'];
    $total_experiences += $pdo->query("SELECT COUNT(*) as total FROM experiences WHERE resume_id = " . $resume['id'])->fetch()['total'];
    $total_skills += $pdo->query("SELECT COUNT(*) as total FROM skills WHERE resume_id = " . $resume['id'])->fetch()['total'];
    $total_education += $pdo->query("SELECT COUNT(*) as total FROM education WHERE resume_id = " . $resume['id'])->fetch()['total'];
    $total_projects += $pdo->query("SELECT COUNT(*) as total FROM projects WHERE resume_id = " . $resume['id'])->fetch()['total'];
    $total_certifications += $pdo->query("SELECT COUNT(*) as total FROM certifications WHERE resume_id = " . $resume['id'])->fetch()['total'];
}

// Últimas visualizações
$stmt = $pdo->prepare("
    SELECT v.*, r.title as resume_title 
    FROM views v 
    LEFT JOIN resumes r ON v.resume_id = r.id 
    WHERE r.user_id = ? 
    ORDER BY v.viewed_at DESC 
    LIMIT 10
");
$stmt->execute([$_SESSION['admin_id']]);
$recent_views = $stmt->fetchAll();

// Últimas mensagens - Corrigido para incluir mensagens sem resume_id
$stmt = $pdo->prepare("
    SELECT m.*, r.title as resume_title 
    FROM messages m 
    LEFT JOIN resumes r ON m.resume_id = r.id 
    WHERE m.resume_id IS NULL OR r.user_id = ? 
    ORDER BY m.created_at DESC 
    LIMIT 5
");
$stmt->execute([$_SESSION['admin_id']]);
$recent_messages = $stmt->fetchAll();
?>

<script>
    updatePageTitle('Dashboard');
</script>

<!-- Estatísticas Principais -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-eye" style="font-size: 2rem; color: #667eea;"></i>
                <h3 class="mt-3 mb-0"><?php echo $total_views; ?></h3>
                <small class="text-muted">Visualizações</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-envelope" style="font-size: 2rem; color: #764ba2;"></i>
                <h3 class="mt-3 mb-0"><?php echo $total_messages; ?></h3>
                <small class="text-muted">Mensagens</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-bell" style="font-size: 2rem; color: #e74c3c;"></i>
                <h3 class="mt-3 mb-0"><?php echo $unread_messages; ?></h3>
                <small class="text-muted">Não Lidas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-file-alt" style="font-size: 2rem; color: #27ae60;"></i>
                <h3 class="mt-3 mb-0"><?php echo count($resumes); ?></h3>
                <small class="text-muted">Currículos</small>
            </div>
        </div>
    </div>
</div>

<!-- Conteúdo do Currículo -->
<div class="row mb-4">
    <div class="col-md-2 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-briefcase" style="font-size: 1.5rem; color: #3498db;"></i>
                <h4 class="mt-2 mb-0"><?php echo $total_experiences; ?></h4>
                <small>Experiências</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-graduation-cap" style="font-size: 1.5rem; color: #3498db;"></i>
                <h4 class="mt-2 mb-0"><?php echo $total_education; ?></h4>
                <small>Educação</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-star" style="font-size: 1.5rem; color: #f39c12;"></i>
                <h4 class="mt-2 mb-0"><?php echo $total_skills; ?></h4>
                <small>Habilidades</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-project-diagram" style="font-size: 1.5rem; color: #9b59b6;"></i>
                <h4 class="mt-2 mb-0"><?php echo $total_projects; ?></h4>
                <small>Projetos</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-certificate" style="font-size: 1.5rem; color: #e67e22;"></i>
                <h4 class="mt-2 mb-0"><?php echo $total_certifications; ?></h4>
                <small>Certificações</small>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <a href="index.php?route=resume&action=create" class="btn btn-sm btn-primary w-100" style="margin-top: 10px;">
                    <i class="fas fa-plus me-1"></i>Novo CV
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Últimas Visualizações e Mensagens -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Últimas Visualizações</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Currículo</th>
                            <th>IP</th>
                            <th>Data/Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_views)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">Nenhuma visualização ainda</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($recent_views as $view): ?>
                            <tr>
                                <td><small><?php echo $view['resume_title']; ?></small></td>
                                <td><code><?php echo $view['ip_address'] ?: 'N/A'; ?></code></td>
                                <td><?php echo date('d/m H:i', strtotime($view['viewed_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Últimas Mensagens -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Últimas Mensagens</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>De</th>
                            <th>Assunto</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_messages)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">Nenhuma mensagem</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($recent_messages as $msg): ?>
                            <tr>
                                <td><strong><?php echo $msg['name']; ?></strong></td>
                                <td><?php echo $msg['subject'] ?: '(Sem assunto)'; ?></td>
                                <td>
                                    <a href="index.php?route=messages&action=view&message_id=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
