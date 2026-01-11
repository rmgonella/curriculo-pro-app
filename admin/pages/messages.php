<?php
$action = $_GET['action'] ?? 'list';
$message_id = $_GET['message_id'] ?? null;

// Marcar como lido
if ($action == 'read' && $message_id) {
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    $stmt->execute([$message_id]);
    header('Location: index.php?route=messages&action=view&message_id=' . $message_id);
    exit;
}

// Deletar mensagem
if ($action == 'delete' && $message_id) {
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$message_id]);
    header('Location: index.php?route=messages');
    exit;
}

// Listar mensagens - Corrigido para incluir mensagens sem resume_id
if ($action == 'list'):
    $stmt = $pdo->prepare("SELECT m.*, r.title as resume_title FROM messages m LEFT JOIN resumes r ON m.resume_id = r.id WHERE m.resume_id IS NULL OR r.user_id = ? ORDER BY m.created_at DESC");
    $stmt->execute([$_SESSION['admin_id']]);
    $messages = $stmt->fetchAll();
    
    $unread_count = $pdo->prepare("SELECT COUNT(*) as total FROM messages m LEFT JOIN resumes r ON m.resume_id = r.id WHERE (m.resume_id IS NULL OR r.user_id = ?) AND m.is_read = 0");
    $unread_count->execute([$_SESSION['admin_id']]);
    $unread_count = $unread_count->fetch()['total'];
?>
    <script>updatePageTitle('Mensagens');</script>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="fas fa-inbox me-2"></i>Mensagens
            <?php if ($unread_count > 0): ?>
            <span class="badge bg-danger"><?php echo $unread_count; ?> nova(s)</span>
            <?php endif; ?>
        </h2>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 30px;"></th>
                        <th>De</th>
                        <th>Assunto</th>
                        <th>Currículo</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Nenhuma mensagem recebida</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                        <tr class="<?php echo !$msg['is_read'] ? 'table-light' : ''; ?>">
                            <td>
                                <?php if (!$msg['is_read']): ?>
                                <span class="badge bg-primary">Novo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo $msg['name']; ?></strong><br>
                                <small class="text-muted"><?php echo $msg['email']; ?></small>
                            </td>
                            <td><?php echo $msg['subject'] ?: '(Sem assunto)'; ?></td>
                            <td><?php echo $msg['resume_title'] ?: '(Não especificado)'; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?></td>
                            <td>
                                <a href="index.php?route=messages&action=view&message_id=<?php echo $msg['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="index.php?route=messages&action=delete&message_id=<?php echo $msg['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($action == 'view' && $message_id):
    $stmt = $pdo->prepare("SELECT m.*, r.title as resume_title FROM messages m LEFT JOIN resumes r ON m.resume_id = r.id WHERE m.id = ?");
    $stmt->execute([$message_id]);
    $msg = $stmt->fetch();
    
    if (!$msg) {
        header('Location: index.php?route=messages');
        exit;
    }
    
    // Marcar como lido
    if (!$msg['is_read']) {
        $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
        $stmt->execute([$message_id]);
        $msg['is_read'] = 1;
    }
?>
    <script>updatePageTitle('Visualizar Mensagem');</script>

    <div class="mb-4">
        <a href="index.php?route=messages" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="mb-0"><?php echo $msg['subject'] ?: '(Sem assunto)'; ?></h5>
                    <small class="text-muted">De: <strong><?php echo $msg['name']; ?></strong> (<?php echo $msg['email']; ?>)</small>
                    <?php if ($msg['phone']): ?>
                    <br><small class="text-muted">Telefone: <?php echo $msg['phone']; ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-md-end">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?>
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <p><?php echo nl2br($msg['message']); ?></p>
            
            <?php if ($msg['resume_title']): ?>
            <hr>
            <p class="text-muted">
                <i class="fas fa-file-alt me-2"></i><strong>Currículo:</strong> <?php echo $msg['resume_title']; ?>
            </p>
            <?php endif; ?>
        </div>
        <div class="card-footer bg-light">
            <a href="mailto:<?php echo $msg['email']; ?>" class="btn btn-primary"><i class="fas fa-reply me-2"></i>Responder por Email</a>
            <a href="index.php?route=messages&action=delete&message_id=<?php echo $msg['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza?')"><i class="fas fa-trash me-2"></i>Deletar</a>
        </div>
    </div>
<?php endif; ?>
