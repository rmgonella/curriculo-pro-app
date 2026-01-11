<?php
// O config.php já foi incluído pelo admin/index.php
// Não é necessário incluir novamente

// Verificar autenticação
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

$route = $_GET['route'] ?? 'resume';
$route_parts = explode('/', $route);
$page = $route_parts[0];
$action = $route_parts[1] ?? 'list';
$resume_id = intval($route_parts[2] ?? 0);

if ($resume_id === 0 && isset($_GET['resume_id'])) {
    $resume_id = intval($_GET['resume_id']);
}
$message = '';
$error = '';

// ==================== PROCESSAR FORMULÁRIOS ====================

// Variáveis para edição
$edit_item = null;
if (isset($_GET['edit_item']) && isset($_GET['type']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'];
    $allowed_types = ['experiences', 'education', 'skills', 'projects', 'certifications', 'languages'];
    if (in_array($type, $allowed_types)) {
        $stmt = $pdo->prepare("SELECT * FROM $type WHERE id = ? AND resume_id = ?");
        $stmt->execute([$id, $resume_id]);
        $edit_item = $stmt->fetch();
    }
}

if (isset($_GET['delete_item']) && isset($_GET['type']) && isset($_GET['id'])) {
    try {
        $id = intval($_GET['id']);
        $type = $_GET['type'];
        $allowed_types = ['experiences', 'education', 'skills', 'projects', 'certifications', 'languages'];
        
        if (in_array($type, $allowed_types)) {
            $stmt = $pdo->prepare("DELETE FROM $type WHERE id = ? AND resume_id = ?");
            $stmt->execute([$id, $resume_id]);
            $message = '✅ Item excluído com sucesso!';
        }
    } catch (Exception $e) {
        $error = '❌ Erro ao excluir: ' . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // ===== SALVAR INFORMAÇÕES BÁSICAS (CRIAR OU EDITAR) =====
        if (isset($_POST['save_basic'])) {
            $title = trim($_POST['title'] ?? '');
            $full_name = trim($_POST['full_name'] ?? 'Rodrigo Marchi Gonella');
            $professional_title = trim($_POST['professional_title'] ?? '');
            $about = trim($_POST['about'] ?? '');
            $objective = trim($_POST['objective'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $location = trim($_POST['location'] ?? '');
            $linkedin = trim($_POST['linkedin'] ?? '');
            $github = trim($_POST['github'] ?? '');
            $portfolio_url = trim($_POST['portfolio_url'] ?? '');
            $personal_website = trim($_POST['personal_website'] ?? '');
            $theme = $_POST['theme'] ?? 'light';
            $language = $_POST['language'] ?? 'pt';
            $seo_description = trim($_POST['seo_description'] ?? '');
            
            // ==================== PROCESSAR FOTO ====================
$photo = null;

if (
    isset($_FILES['photo']) &&
    $_FILES['photo']['error'] === UPLOAD_ERR_OK &&
    $_FILES['photo']['size'] > 0
) {
    // Caminho absoluto correto
    $uploadDir = __DIR__ . '/../../assets/uploads/';

    // Criar pasta se não existir
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Extensões permitidas
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
        throw new Exception('Formato de imagem não permitido.');
    }

    $photo = 'photo_' . time() . '_' . uniqid() . '.' . $ext;
    $destination = $uploadDir . $photo;

    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
        throw new Exception('Falha ao salvar a imagem no servidor.');
    }
}

            
            if ($action === 'create') {
                // CRIAR NOVO CURRÍCULO
                $sql = "INSERT INTO resumes (user_id, title, full_name, professional_title, about, objective, email, phone, location, linkedin, github, portfolio_url, personal_website, theme, language, seo_description, photo) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
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
                    $seo_description,
                    $photo
                ]);
                
                $resume_id = $pdo->lastInsertId();
                $message = '✅ Currículo criado com sucesso! Redirecionando...';
                echo '<script>setTimeout(function() { window.location.href="index.php?route=resume&action=edit&resume_id=' . $resume_id . '"; }, 1500);</script>';
                exit;
            } else {
                // EDITAR CURRÍCULO EXISTENTE
                if ($photo) {
                    $sql = "UPDATE resumes SET title=?, full_name=?, professional_title=?, about=?, objective=?, email=?, phone=?, location=?, linkedin=?, github=?, portfolio_url=?, personal_website=?, theme=?, language=?, seo_description=?, photo=? WHERE id=? AND user_id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        $title, $full_name, $professional_title, $about, $objective, $email, $phone, $location, 
                        $linkedin, $github, $portfolio_url, $personal_website, $theme, $language, $seo_description, 
                        $photo, $resume_id, $_SESSION['admin_id']
                    ]);
                } else {
                    $sql = "UPDATE resumes SET title=?, full_name=?, professional_title=?, about=?, objective=?, email=?, phone=?, location=?, linkedin=?, github=?, portfolio_url=?, personal_website=?, theme=?, language=?, seo_description=? WHERE id=? AND user_id=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        $title, $full_name, $professional_title, $about, $objective, $email, $phone, $location, 
                        $linkedin, $github, $portfolio_url, $personal_website, $theme, $language, $seo_description, 
                        $resume_id, $_SESSION['admin_id']
                    ]);
                }
                $message = '✅ Currículo atualizado com sucesso!';
            }
        }
        
        // ===== ADICIONAR/EDITAR EXPERIÊNCIA =====
        if (isset($_POST['save_experience'])) {
            if (isset($_POST['item_id']) && !empty($_POST['item_id'])) {
                $stmt = $pdo->prepare("UPDATE experiences SET company=?, position=?, start_date=?, end_date=?, current_job=?, description=?, achievements=? WHERE id=? AND resume_id=?");
                $stmt->execute([
                    $_POST['company'], $_POST['position'], $_POST['start_date'], $_POST['end_date'] ?? null,
                    isset($_POST['current_job']) ? 1 : 0, $_POST['description'] ?? '', $_POST['achievements'] ?? '',
                    $_POST['item_id'], $resume_id
                ]);
                $message = '✅ Experiência atualizada!';
            } else {
                $stmt = $pdo->prepare("INSERT INTO experiences (resume_id, company, position, start_date, end_date, current_job, description, achievements) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $resume_id, $_POST['company'], $_POST['position'], $_POST['start_date'], $_POST['end_date'] ?? null,
                    isset($_POST['current_job']) ? 1 : 0, $_POST['description'] ?? '', $_POST['achievements'] ?? ''
                ]);
                $message = '✅ Experiência adicionada!';
            }
        }
        
        // ===== ADICIONAR/EDITAR EDUCAÇÃO =====
        if (isset($_POST['save_education'])) {
            if (isset($_POST['item_id']) && !empty($_POST['item_id'])) {
                $stmt = $pdo->prepare("UPDATE education SET institution=?, degree=?, field_of_study=?, start_date=?, end_date=?, description=? WHERE id=? AND resume_id=?");
                $stmt->execute([
                    $_POST['institution'], $_POST['degree'], $_POST['field_of_study'] ?? '', $_POST['start_date'], $_POST['end_date'], $_POST['description'] ?? '',
                    $_POST['item_id'], $resume_id
                ]);
                $message = '✅ Educação atualizada!';
            } else {
                $stmt = $pdo->prepare("INSERT INTO education (resume_id, institution, degree, field_of_study, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $resume_id, $_POST['institution'], $_POST['degree'], $_POST['field_of_study'] ?? '', $_POST['start_date'], $_POST['end_date'], $_POST['description'] ?? ''
                ]);
                $message = '✅ Educação adicionada!';
            }
        }
        
        // ===== ADICIONAR/EDITAR HABILIDADE =====
        if (isset($_POST['save_skill'])) {
            if (isset($_POST['item_id']) && !empty($_POST['item_id'])) {
                $stmt = $pdo->prepare("UPDATE skills SET name=?, level=?, category=? WHERE id=? AND resume_id=?");
                $stmt->execute([$_POST['skill_name'], $_POST['skill_level'] ?? 50, $_POST['skill_category'] ?? '', $_POST['item_id'], $resume_id]);
                $message = '✅ Habilidade atualizada!';
            } else {
                $stmt = $pdo->prepare("INSERT INTO skills (resume_id, name, level, category) VALUES (?, ?, ?, ?)");
                $stmt->execute([$resume_id, $_POST['skill_name'], $_POST['skill_level'] ?? 50, $_POST['skill_category'] ?? '']);
                $message = '✅ Habilidade adicionada!';
            }
        }
        
        // ===== ADICIONAR/EDITAR PROJETO =====
        if (isset($_POST['save_project'])) {
            if (isset($_POST['item_id']) && !empty($_POST['item_id'])) {
                $stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, technologies=?, link=? WHERE id=? AND resume_id=?");
                $stmt->execute([$_POST['project_title'], $_POST['project_description'], $_POST['project_technologies'] ?? '', $_POST['project_link'] ?? '', $_POST['item_id'], $resume_id]);
                $message = '✅ Projeto atualizado!';
            } else {
                $stmt = $pdo->prepare("INSERT INTO projects (resume_id, title, description, technologies, link) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$resume_id, $_POST['project_title'], $_POST['project_description'], $_POST['project_technologies'] ?? '', $_POST['project_link'] ?? '']);
                $message = '✅ Projeto adicionado!';
            }
        }
        
        // ===== ADICIONAR/EDITAR CERTIFICAÇÃO =====
        if (isset($_POST['save_certification'])) {
            if (isset($_POST['item_id']) && !empty($_POST['item_id'])) {
                $stmt = $pdo->prepare("UPDATE certifications SET name=?, issuing_organization=?, issue_date=?, expiration_date=?, credential_url=? WHERE id=? AND resume_id=?");
                $stmt->execute([$_POST['cert_title'], $_POST['cert_issuer'], $_POST['cert_issue_date'], $_POST['cert_expiration_date'] ?? null, $_POST['cert_url'] ?? '', $_POST['item_id'], $resume_id]);
                $message = '✅ Certificação atualizada!';
            } else {
                $stmt = $pdo->prepare("INSERT INTO certifications (resume_id, name, issuing_organization, issue_date, expiration_date, credential_url) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$resume_id, $_POST['cert_title'], $_POST['cert_issuer'], $_POST['cert_issue_date'], $_POST['cert_expiration_date'] ?? null, $_POST['cert_url'] ?? '']);
                $message = '✅ Certificação adicionada!';
            }
        }
        
        // ===== ADICIONAR/EDITAR IDIOMA =====
        if (isset($_POST['save_language'])) {
            if (isset($_POST['item_id']) && !empty($_POST['item_id'])) {
                $stmt = $pdo->prepare("UPDATE languages SET language_name=?, proficiency=? WHERE id=? AND resume_id=?");
                $stmt->execute([$_POST['language_name'], $_POST['language_proficiency'], $_POST['item_id'], $resume_id]);
                $message = '✅ Idioma atualizado!';
            } else {
                $stmt = $pdo->prepare("INSERT INTO languages (resume_id, language_name, proficiency) VALUES (?, ?, ?)");
                $stmt->execute([$resume_id, $_POST['language_name'], $_POST['language_proficiency']]);
                $message = '✅ Idioma adicionado!';
            }
        }
        
    } catch (Exception $e) {
        $error = '❌ Erro: ' . $e->getMessage();
    }
}

// ==================== BUSCAR DADOS ====================

if ($action === 'list') {
    // LISTAR CURRÍCULOS
    $stmt = $pdo->prepare("SELECT * FROM resumes WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['admin_id']]);
    $resumes = $stmt->fetchAll();
} else {
    // BUSCAR CURRÍCULO ESPECÍFICO
    $stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ? AND user_id = ?");
    $stmt->execute([$resume_id, $_SESSION['admin_id']]);
    $resume = $stmt->fetch();
    
    if (!$resume && $action !== 'create') {
        die('Currículo não encontrado.');
    }
    
    if ($resume) {
        // Buscar dados relacionados
        $stmt = $pdo->prepare("SELECT * FROM experiences WHERE resume_id = ? ORDER BY start_date DESC");
        $stmt->execute([$resume_id]);
        $experiences = $stmt->fetchAll();
        
        $stmt = $pdo->prepare("SELECT * FROM education WHERE resume_id = ? ORDER BY start_date DESC");
        $stmt->execute([$resume_id]);
        $education = $stmt->fetchAll();
        
        $stmt = $pdo->prepare("SELECT * FROM skills WHERE resume_id = ? ORDER BY name");
        $stmt->execute([$resume_id]);
        $skills = $stmt->fetchAll();
        
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE resume_id = ? ORDER BY id DESC");
        $stmt->execute([$resume_id]);
        $projects = $stmt->fetchAll();
        
        $stmt = $pdo->prepare("SELECT * FROM certifications WHERE resume_id = ? ORDER BY issue_date DESC");
        $stmt->execute([$resume_id]);
        $certifications = $stmt->fetchAll();
        
        $stmt = $pdo->prepare("SELECT * FROM languages WHERE resume_id = ? ORDER BY language_name");
        $stmt->execute([$resume_id]);
        $languages = $stmt->fetchAll();
    }
}

// ==================== EXIBIR INTERFACE ====================
?>

<div class="container-fluid py-4">
    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($action === 'list'): ?>
        <!-- LISTAR CURRÍCULOS -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2>Meus Currículos</h2>
            <a href="index.php?route=resume&action=create" class="btn btn-primary">+ Novo Currículo</a>
        </div>
        
        <?php if (empty($resumes)): ?>
            <div class="alert alert-info">Você ainda não criou nenhum currículo. <a href="index.php?route=resume&action=create">Clique aqui para começar</a></div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($resumes as $r): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($r['title']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($r['full_name']); ?></p>
                                <small class="text-muted">Criado em: <?php echo date('d/m/Y', strtotime($r['created_at'])); ?></small>
                                <div class="mt-3 d-flex gap-2 flex-wrap">
                                    <a href="index.php?route=resume/edit/<?php echo $r['id']; ?>" class="btn btn-sm btn-warning flex-grow-1">Editar</a>
                                    <a href="../../generate_pdf.php?resume_id=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger flex-grow-1" target="_blank">PDF</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    
    <?php elseif ($action === 'create'): ?>
        <!-- CRIAR NOVO CURRÍCULO -->
        <h2 class="mb-4">Criar Novo Currículo</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="save_basic" value="1">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Título do Currículo *</label>
                    <input type="text" name="title" class="form-control" required placeholder="Ex: Designer Gráfico - 2024">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nome Completo *</label>
                    <input type="text" name="full_name" class="form-control" required value="Rodrigo Marchi Gonella">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Profissão</label>
                    <input type="text" name="professional_title" class="form-control" placeholder="Ex: Designer Gráfico">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="seu@email.com">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Telefone</label>
                    <input type="tel" name="phone" class="form-control" placeholder="(17) 98160-0610">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Localização</label>
                    <input type="text" name="location" class="form-control" placeholder="Niterói, RJ">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">LinkedIn</label>
                    <input type="url" name="linkedin" class="form-control" placeholder="https://linkedin.com/in/...">
                </div>
                <div class="col-md-6">
                    <label class="form-label">GitHub</label>
                    <input type="url" name="github" class="form-control" placeholder="https://github.com/...">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Portfólio</label>
                    <input type="url" name="portfolio_url" class="form-control" placeholder="https://...">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Site Pessoal</label>
                    <input type="url" name="personal_website" class="form-control" placeholder="https://...">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Sobre Você</label>
                <textarea name="about" class="form-control" rows="3" placeholder="Descreva um pouco sobre você..."></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Objetivo Profissional</label>
                <textarea name="objective" class="form-control" rows="2" placeholder="Qual é seu objetivo profissional?"></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Foto de Perfil</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tema</label>
                    <select name="theme" class="form-select">
                        <option value="light">Claro</option>
                        <option value="dark">Escuro</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Idioma</label>
                    <select name="language" class="form-select">
                        <option value="pt">Português</option>
                        <option value="en">English</option>
                        <option value="es">Español</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Descrição SEO</label>
                <input type="text" name="seo_description" class="form-control" maxlength="160" placeholder="Descrição para mecanismos de busca">
            </div>
            
            <button type="submit" class="btn btn-success btn-lg w-100">Criar Currículo</button>
        </form>
    
    <?php elseif ($action === 'edit' && $resume): ?>
        <!-- EDITAR CURRÍCULO -->
        <h2 class="mb-4">Editar Currículo: <?php echo htmlspecialchars($resume['title']); ?></h2>
        
        <ul class="nav nav-tabs mb-3 flex-wrap" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#basic">Básico</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#experience">Experiência</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#education">Educação</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#skills">Habilidades</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#projects">Projetos</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#certifications">Certificações</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#languages">Idiomas</a></li>
        </ul>
        
        <div class="tab-content">
            <!-- TAB: BÁSICO -->
            <div id="basic" class="tab-pane fade show active">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="save_basic" value="1">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Título do Currículo *</label>
                            <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($resume['title']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nome Completo *</label>
                            <input type="text" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($resume['full_name']); ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Profissão</label>
                            <input type="text" name="professional_title" class="form-control" value="<?php echo htmlspecialchars($resume['professional_title'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($resume['email'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($resume['phone'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Localização</label>
                            <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($resume['location'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">LinkedIn</label>
                            <input type="url" name="linkedin" class="form-control" value="<?php echo htmlspecialchars($resume['linkedin'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">GitHub</label>
                            <input type="url" name="github" class="form-control" value="<?php echo htmlspecialchars($resume['github'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Portfólio</label>
                            <input type="url" name="portfolio_url" class="form-control" value="<?php echo htmlspecialchars($resume['portfolio_url'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Site Pessoal</label>
                            <input type="url" name="personal_website" class="form-control" value="<?php echo htmlspecialchars($resume['personal_website'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Sobre Você</label>
                        <textarea name="about" class="form-control" rows="3"><?php echo htmlspecialchars($resume['about'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Objetivo Profissional</label>
                        <textarea name="objective" class="form-control" rows="2"><?php echo htmlspecialchars($resume['objective'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Foto de Perfil</label>
                        <?php if ($resume['photo']): ?>
                            <div class="mb-2">
                                <img src="../../assets/uploads/<?php echo htmlspecialchars($resume['photo']); ?>" style="max-width: 150px; border-radius: 8px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tema</label>
                            <select name="theme" class="form-select">
                                <option value="light" <?php echo $resume['theme'] === 'light' ? 'selected' : ''; ?>>Claro</option>
                                <option value="dark" <?php echo $resume['theme'] === 'dark' ? 'selected' : ''; ?>>Escuro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Idioma</label>
                            <select name="language" class="form-select">
                                <option value="pt" <?php echo $resume['language'] === 'pt' ? 'selected' : ''; ?>>Português</option>
                                <option value="en" <?php echo $resume['language'] === 'en' ? 'selected' : ''; ?>>English</option>
                                <option value="es" <?php echo $resume['language'] === 'es' ? 'selected' : ''; ?>>Español</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descrição SEO</label>
                        <input type="text" name="seo_description" class="form-control" maxlength="160" value="<?php echo htmlspecialchars($resume['seo_description'] ?? ''); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-success btn-lg w-100">Salvar Alterações</button>
                </form>
            </div>
            
            <!-- TAB: EXPERIÊNCIA -->
            <div id="experience" class="tab-pane fade">
                <h4 class="mb-3">Experiências Profissionais</h4>
                
                <?php if (!empty($experiences)): ?>
                    <div class="list-group mb-3">
                        <?php foreach ($experiences as $exp): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6><?php echo htmlspecialchars($exp['position']); ?> - <?php echo htmlspecialchars($exp['company']); ?></h6>
                                    <small class="text-muted"><?php echo date('m/Y', strtotime($exp['start_date'])); ?> - <?php echo $exp['current_job'] ? 'Atual' : date('m/Y', strtotime($exp['end_date'])); ?></small>
                                </div>
                                <div>
                                    <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>&edit_item=1&type=experiences&id=<?php echo $exp['id']; ?>#experience" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>&delete_item=1&type=experiences&id=<?php echo $exp['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="save_experience" value="1">
                    <input type="hidden" name="resume_id" value="<?php echo $resume_id; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $edit_item && $_GET['type'] == 'experiences' ? $edit_item['id'] : ''; ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Empresa *</label>
                            <input type="text" name="company" class="form-control" required value="<?php echo $edit_item && $_GET['type'] == 'experiences' ? htmlspecialchars($edit_item['company']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cargo *</label>
                            <input type="text" name="position" class="form-control" required value="<?php echo $edit_item && $_GET['type'] == 'experiences' ? htmlspecialchars($edit_item['position']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Data Inicial *</label>
                            <input type="date" name="start_date" class="form-control" required value="<?php echo $edit_item && $_GET['type'] == 'experiences' ? $edit_item['start_date'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data Final</label>
                            <input type="date" name="end_date" class="form-control" value="<?php echo $edit_item && $_GET['type'] == 'experiences' ? $edit_item['end_date'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="current_job" class="form-check-input" id="current_job" <?php echo $edit_item && $_GET['type'] == 'experiences' && $edit_item['current_job'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="current_job">Trabalho Atual</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo $edit_item && $_GET['type'] == 'experiences' ? htmlspecialchars($edit_item['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Realizações</label>
                        <textarea name="achievements" class="form-control" rows="3"><?php echo $edit_item && $_GET['type'] == 'experiences' ? htmlspecialchars($edit_item['achievements']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100"><?php echo $edit_item && $_GET['type'] == 'experiences' ? 'Atualizar' : 'Adicionar'; ?> Experiência</button>
                    <?php if($edit_item && $_GET['type'] == 'experiences'): ?>
                        <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>#experience" class="btn btn-secondary w-100 mt-2">Cancelar Edição</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- TAB: EDUCAÇÃO -->
            <div id="education" class="tab-pane fade">
                <h4 class="mb-3">Formação Acadêmica</h4>
                
                <?php if (!empty($education)): ?>
                    <div class="list-group mb-3">
                        <?php foreach ($education as $edu): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6><?php echo htmlspecialchars($edu['degree']); ?> - <?php echo htmlspecialchars($edu['institution']); ?></h6>
                                    <small class="text-muted"><?php echo date('Y', strtotime($edu['start_date'])); ?> - <?php echo date('Y', strtotime($edu['end_date'])); ?></small>
                                </div>
                                <div>
                                    <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>&edit_item=1&type=education&id=<?php echo $edu['id']; ?>#education" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>&delete_item=1&type=education&id=<?php echo $edu['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="save_education" value="1">
                    <input type="hidden" name="resume_id" value="<?php echo $resume_id; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $edit_item && $_GET['type'] == 'education' ? $edit_item['id'] : ''; ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Instituição *</label>
                            <input type="text" name="institution" class="form-control" required value="<?php echo $edit_item && $_GET['type'] == 'education' ? htmlspecialchars($edit_item['institution']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Grau *</label>
                            <input type="text" name="degree" class="form-control" required placeholder="Ex: Bacharel, Mestrado" value="<?php echo $edit_item && $_GET['type'] == 'education' ? htmlspecialchars($edit_item['degree']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Área de Estudo</label>
                        <input type="text" name="field_of_study" class="form-control" placeholder="Ex: Administração" value="<?php echo $edit_item && $_GET['type'] == 'education' ? htmlspecialchars($edit_item['field_of_study']) : ''; ?>">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Data Inicial *</label>
                            <input type="date" name="start_date" class="form-control" required value="<?php echo $edit_item && $_GET['type'] == 'education' ? $edit_item['start_date'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data Final *</label>
                            <input type="date" name="end_date" class="form-control" required value="<?php echo $edit_item && $_GET['type'] == 'education' ? $edit_item['end_date'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo $edit_item && $_GET['type'] == 'education' ? htmlspecialchars($edit_item['description']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100"><?php echo $edit_item && $_GET['type'] == 'education' ? 'Atualizar' : 'Adicionar'; ?> Educação</button>
                    <?php if($edit_item && $_GET['type'] == 'education'): ?>
                        <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>#education" class="btn btn-secondary w-100 mt-2">Cancelar Edição</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- TAB: HABILIDADES -->
            <div id="skills" class="tab-pane fade">
                <h4 class="mb-3">Habilidades</h4>
                
                <?php if (!empty($skills)): ?>
                    <div class="row mb-3">
                        <?php foreach ($skills as $skill): ?>
                            <div class="col-md-6 mb-2">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($skill['name']); ?></h6>
                                            <div>
                                                <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>&edit_item=1&type=skills&id=<?php echo $skill['id']; ?>#skills" class="text-warning me-2"><i class="fas fa-edit"></i></a>
                                                <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>&delete_item=1&type=skills&id=<?php echo $skill['id']; ?>" class="text-danger" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: <?php echo $skill['level']; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="save_skill" value="1">
                    <input type="hidden" name="resume_id" value="<?php echo $resume_id; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $edit_item && $_GET['type'] == 'skills' ? $edit_item['id'] : ''; ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Habilidade *</label>
                            <input type="text" name="skill_name" class="form-control" required placeholder="Ex: Photoshop" value="<?php echo $edit_item && $_GET['type'] == 'skills' ? htmlspecialchars($edit_item['name']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nível (0-100)</label>
                            <input type="number" name="skill_level" class="form-control" min="0" max="100" value="<?php echo $edit_item && $_GET['type'] == 'skills' ? $edit_item['level'] : '50'; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <input type="text" name="skill_category" class="form-control" placeholder="Ex: Design" value="<?php echo $edit_item && $_GET['type'] == 'skills' ? htmlspecialchars($edit_item['category']) : ''; ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100"><?php echo $edit_item && $_GET['type'] == 'skills' ? 'Atualizar' : 'Adicionar'; ?> Habilidade</button>
                    <?php if($edit_item && $_GET['type'] == 'skills'): ?>
                        <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>#skills" class="btn btn-secondary w-100 mt-2">Cancelar Edição</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- TAB: PROJETOS -->
            <div id="projects" class="tab-pane fade">
                <h4 class="mb-3">Projetos</h4>
                
                <?php if (!empty($projects)): ?>
                    <div class="row mb-3">
                        <?php foreach ($projects as $proj): ?>
                            <div class="col-md-6 mb-2">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($proj['title']); ?></h6>
                                            <div>
                                                <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>&edit_item=1&type=projects&id=<?php echo $proj['id']; ?>#projects" class="text-warning me-2"><i class="fas fa-edit"></i></a>
                                                <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>&delete_item=1&type=projects&id=<?php echo $proj['id']; ?>" class="text-danger" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </div>
                                        <p class="card-text small"><?php echo htmlspecialchars(substr($proj['description'], 0, 100)); ?>...</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="save_project" value="1">
                    <input type="hidden" name="resume_id" value="<?php echo $resume_id; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $edit_item && $_GET['type'] == 'projects' ? $edit_item['id'] : ''; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Título do Projeto *</label>
                        <input type="text" name="project_title" class="form-control" required value="<?php echo $edit_item && $_GET['type'] == 'projects' ? htmlspecialchars($edit_item['title']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descrição *</label>
                        <textarea name="project_description" class="form-control" rows="3" required><?php echo $edit_item && $_GET['type'] == 'projects' ? htmlspecialchars($edit_item['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tecnologias</label>
                            <input type="text" name="project_technologies" class="form-control" placeholder="Ex: PHP, MySQL, HTML" value="<?php echo $edit_item && $_GET['type'] == 'projects' ? htmlspecialchars($edit_item['technologies']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Link</label>
                            <input type="url" name="project_link" class="form-control" placeholder="https://..." value="<?php echo $edit_item && $_GET['type'] == 'projects' ? htmlspecialchars($edit_item['link']) : ''; ?>">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100"><?php echo $edit_item && $_GET['type'] == 'projects' ? 'Atualizar' : 'Adicionar'; ?> Projeto</button>
                    <?php if($edit_item && $_GET['type'] == 'projects'): ?>
                        <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>#projects" class="btn btn-secondary w-100 mt-2">Cancelar Edição</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- TAB: CERTIFICAÇÕES -->
            <div id="certifications" class="tab-pane fade">
                <h4 class="mb-3">Certificações</h4>
                
                <?php if (!empty($certifications)): ?>
                    <div class="list-group mb-3">
                        <?php foreach ($certifications as $cert): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6><?php echo htmlspecialchars($cert['name']); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($cert['issuing_organization']); ?> - <?php echo date('m/Y', strtotime($cert['issue_date'])); ?></small>
                                </div>
                                <div>
                                    <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>&edit_item=1&type=certifications&id=<?php echo $cert['id']; ?>#certifications" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>&delete_item=1&type=certifications&id=<?php echo $cert['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="save_certification" value="1">
                    <input type="hidden" name="resume_id" value="<?php echo $resume_id; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $edit_item && $_GET['type'] == 'certifications' ? $edit_item['id'] : ''; ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Certificação *</label>
                            <input type="text" name="cert_title" class="form-control" required value="<?php echo $edit_item && $_GET['type'] == 'certifications' ? htmlspecialchars($edit_item['name']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Organização *</label>
                            <input type="text" name="cert_issuer" class="form-control" required value="<?php echo $edit_item && $_GET['type'] == 'certifications' ? htmlspecialchars($edit_item['issuing_organization']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Data de Emissão *</label>
                            <input type="date" name="cert_issue_date" class="form-control" required value="<?php echo $edit_item && $_GET['type'] == 'certifications' ? $edit_item['issue_date'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data de Expiração</label>
                            <input type="date" name="cert_expiration_date" class="form-control" value="<?php echo $edit_item && $_GET['type'] == 'certifications' ? $edit_item['expiration_date'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Link da Credencial</label>
                        <input type="url" name="cert_url" class="form-control" placeholder="https://..." value="<?php echo $edit_item && $_GET['type'] == 'certifications' ? htmlspecialchars($edit_item['credential_url']) : ''; ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100"><?php echo $edit_item && $_GET['type'] == 'certifications' ? 'Atualizar' : 'Adicionar'; ?> Certificação</button>
                    <?php if($edit_item && $_GET['type'] == 'certifications'): ?>
                        <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>#certifications" class="btn btn-secondary w-100 mt-2">Cancelar Edição</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- TAB: IDIOMAS -->
            <div id="languages" class="tab-pane fade">
                <h4 class="mb-3">Idiomas</h4>
                
                <?php if (!empty($languages)): ?>
                    <div class="list-group mb-3">
                        <?php foreach ($languages as $lang): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span><?php echo htmlspecialchars($lang['language_name']); ?></span>
                                    <span class="badge bg-primary ms-2"><?php echo htmlspecialchars($lang['proficiency']); ?></span>
                                </div>
                                <div>
                                    <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>&edit_item=1&type=languages&id=<?php echo $lang['id']; ?>#languages" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>&delete_item=1&type=languages&id=<?php echo $lang['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="save_language" value="1">
                    <input type="hidden" name="resume_id" value="<?php echo $resume_id; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $edit_item && $_GET['type'] == 'languages' ? $edit_item['id'] : ''; ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Idioma *</label>
                            <input type="text" name="language_name" class="form-control" required placeholder="Ex: Inglês" value="<?php echo $edit_item && $_GET['type'] == 'languages' ? htmlspecialchars($edit_item['language_name']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Proficiência *</label>
                            <select name="language_proficiency" class="form-select" required>
                                <option value="">Selecione...</option>
                                <option value="Básico" <?php echo $edit_item && $_GET['type'] == 'languages' && $edit_item['proficiency'] == 'Básico' ? 'selected' : ''; ?>>Básico</option>
                                <option value="Intermediário" <?php echo $edit_item && $_GET['type'] == 'languages' && $edit_item['proficiency'] == 'Intermediário' ? 'selected' : ''; ?>>Intermediário</option>
                                <option value="Avançado" <?php echo $edit_item && $_GET['type'] == 'languages' && $edit_item['proficiency'] == 'Avançado' ? 'selected' : ''; ?>>Avançado</option>
                                <option value="Fluente" <?php echo $edit_item && $_GET['type'] == 'languages' && $edit_item['proficiency'] == 'Fluente' ? 'selected' : ''; ?>>Fluente</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100"><?php echo $edit_item && $_GET['type'] == 'languages' ? 'Atualizar' : 'Adicionar'; ?> Idioma</button>
                    <?php if($edit_item && $_GET['type'] == 'languages'): ?>
                        <a href="index.php?route=resume/edit/<?php echo $resume_id; ?>#languages" class="btn btn-secondary w-100 mt-2">Cancelar Edição</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
