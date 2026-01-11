<?php
/**
 * Página Simplificada de Cadastro de Currículo
 * Versão sem abas para diagnóstico e teste de salvamento
 */

$action = $_GET['action'] ?? 'list';
$resume_id = $_GET['resume_id'] ?? null;

// LISTAR CURRÍCULOS
if ($action == 'list'):
    $stmt = $pdo->prepare("SELECT * FROM resumes WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['admin_id']]);
    $resumes = $stmt->fetchAll();
?>
    <script>updatePageTitle('Meus Currículos');</script>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-alt me-2"></i>Meus Currículos</h2>
        <a href="index.php?route=resume&action=create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Novo Currículo
        </a>
    </div>

    <?php if (empty($resumes)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Você ainda não criou nenhum currículo.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Profissão</th>
                        <th>Visualizações</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resumes as $res): ?>
                    <tr>
                        <td><?php echo $res['title']; ?></td>
                        <td><?php echo $res['professional_title']; ?></td>
                        <td><span class="badge bg-info"><?php echo $res['total_views']; ?></span></td>
                        <td>
                            <a href="index.php?route=resume&action=edit&resume_id=<?php echo $res['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="index.php?route=resume&action=delete&resume_id=<?php echo $res['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">
                                <i class="fas fa-trash"></i> Deletar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

<?php elseif ($action == 'create' || ($action == 'edit' && $resume_id)):
    
    // Carregar dados se for edição
    if ($action == 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ? AND user_id = ?");
        $stmt->execute([$resume_id, $_SESSION['admin_id']]);
        $resume = $stmt->fetch();
        if (!$resume) {
            echo '<div class="alert alert-danger">Currículo não encontrado!</div>';
            exit;
        }
    } else {
        $resume = [
            'id' => null,
            'title' => '',
            'full_name' => 'Rodrigo Marchi Gonella',
            'professional_title' => '',
            'about' => '',
            'objective' => '',
            'email' => '',
            'phone' => '',
            'location' => '',
            'linkedin' => '',
            'github' => '',
            'portfolio_url' => '',
            'personal_website' => '',
            'theme' => 'light',
            'language' => 'pt',
            'seo_description' => ''
        ];
    }

    // PROCESSAR FORMULÁRIO
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'] ?? '';
        $full_name = $_POST['full_name'] ?? 'Rodrigo Marchi Gonella';
        $professional_title = $_POST['professional_title'] ?? '';
        $about = $_POST['about'] ?? '';
        $objective = $_POST['objective'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $location = $_POST['location'] ?? '';
        $linkedin = $_POST['linkedin'] ?? '';
        $github = $_POST['github'] ?? '';
        $portfolio_url = $_POST['portfolio_url'] ?? '';
        $personal_website = $_POST['personal_website'] ?? '';
        $theme = $_POST['theme'] ?? 'light';
        $language = $_POST['language'] ?? 'pt';
        $seo_description = $_POST['seo_description'] ?? '';

        // Validação básica
        if (empty($title)) {
            echo '<div class="alert alert-danger">❌ Título do currículo é obrigatório!</div>';
        } else {
            try {
                if ($action == 'create') {
                    // INSERT
                    $stmt = $pdo->prepare("
                        INSERT INTO resumes 
                        (user_id, title, full_name, professional_title, about, objective, email, phone, location, linkedin, github, portfolio_url, personal_website, theme, language, seo_description, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $result = $stmt->execute([
                        $_SESSION['admin_id'],
                        $title,
                        $full_name,
                        $professional_title,
                        $about,
                        $objective,
                        $email,
                        $phone,
                        $location,
                        $linkedin,
                        $github,
                        $portfolio_url,
                        $personal_website,
                        $theme,
                        $language,
                        $seo_description
                    ]);
                    
                    if ($result) {
                        $new_id = $pdo->lastInsertId();
                        echo '<div class="alert alert-success">✅ Currículo criado com sucesso! ID: ' . $new_id . '</div>';
                        $resume['id'] = $new_id;
                    } else {
                        echo '<div class="alert alert-danger">❌ Erro ao criar currículo!</div>';
                    }
                } else {
                    // UPDATE
                    $stmt = $pdo->prepare("
                        UPDATE resumes 
                        SET title = ?, full_name = ?, professional_title = ?, about = ?, objective = ?, email = ?, phone = ?, location = ?, linkedin = ?, github = ?, portfolio_url = ?, personal_website = ?, theme = ?, language = ?, seo_description = ?
                        WHERE id = ? AND user_id = ?
                    ");
                    $result = $stmt->execute([
                        $title,
                        $full_name,
                        $professional_title,
                        $about,
                        $objective,
                        $email,
                        $phone,
                        $location,
                        $linkedin,
                        $github,
                        $portfolio_url,
                        $personal_website,
                        $theme,
                        $language,
                        $seo_description,
                        $resume_id,
                        $_SESSION['admin_id']
                    ]);
                    
                    if ($result) {
                        echo '<div class="alert alert-success">✅ Currículo atualizado com sucesso!</div>';
                    } else {
                        echo '<div class="alert alert-danger">❌ Erro ao atualizar currículo!</div>';
                    }
                }
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">❌ Erro no banco de dados: ' . $e->getMessage() . '</div>';
            }
        }
    }
?>

    <script>updatePageTitle('<?php echo $action == 'create' ? 'Novo Currículo' : 'Editar Currículo'; ?>');</script>

    <div class="mb-4">
        <a href="index.php?route=resume" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="mb-0"><?php echo $action == 'create' ? 'Criar Novo Currículo' : 'Editar Currículo'; ?></h4>
        </div>
        <div class="card-body">
            <form method="POST" class="needs-validation">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Título do Currículo *</label>
                        <input type="text" name="title" class="form-control" value="<?php echo $resume['title']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo $resume['full_name']; ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Título Profissional</label>
                        <input type="text" name="professional_title" class="form-control" value="<?php echo $resume['professional_title']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Localização</label>
                        <input type="text" name="location" class="form-control" value="<?php echo $resume['location']; ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $resume['email']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefone</label>
                        <input type="tel" name="phone" class="form-control" value="<?php echo $resume['phone']; ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sobre Mim</label>
                    <textarea name="about" class="form-control" rows="3"><?php echo $resume['about']; ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Objetivo Profissional</label>
                    <textarea name="objective" class="form-control" rows="3"><?php echo $resume['objective']; ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição SEO</label>
                    <textarea name="seo_description" class="form-control" rows="2" maxlength="160"><?php echo $resume['seo_description']; ?></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">LinkedIn</label>
                        <input type="url" name="linkedin" class="form-control" value="<?php echo $resume['linkedin']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">GitHub</label>
                        <input type="url" name="github" class="form-control" value="<?php echo $resume['github']; ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Portfólio</label>
                        <input type="url" name="portfolio_url" class="form-control" value="<?php echo $resume['portfolio_url']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Website Pessoal</label>
                        <input type="url" name="personal_website" class="form-control" value="<?php echo $resume['personal_website']; ?>">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Tema</label>
                        <select name="theme" class="form-select">
                            <option value="light" <?php echo $resume['theme'] == 'light' ? 'selected' : ''; ?>>Claro</option>
                            <option value="dark" <?php echo $resume['theme'] == 'dark' ? 'selected' : ''; ?>>Escuro</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Idioma</label>
                        <select name="language" class="form-select">
                            <option value="pt" <?php echo $resume['language'] == 'pt' ? 'selected' : ''; ?>>Português</option>
                            <option value="en" <?php echo $resume['language'] == 'en' ? 'selected' : ''; ?>>English</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i><?php echo $action == 'create' ? 'Criar Currículo' : 'Atualizar Currículo'; ?>
                </button>
            </form>
        </div>
    </div>

<?php endif; ?>
