<?php
// Buscar currículo
$slug = $_GET['slug'] ?? null;
if (!$slug) {
    header('Location: ' . BASE_URL);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ? AND active = 1");
$stmt->execute([$slug]);
$resume = $stmt->fetch();

if (!$resume) {
    header('Location: ' . BASE_URL . '/views/404.php');
    exit;
}

// Registrar acesso
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$referer = $_SERVER['HTTP_REFERER'] ?? null;
$stmt = $pdo->prepare("INSERT INTO views (resume_id, ip_address, user_agent, referer) VALUES (?, ?, ?, ?)");
$stmt->execute([$resume['id'], $ip, $user_agent, $referer]);

// Atualizar contador de visualizações
$pdo->prepare("UPDATE resumes SET total_views = total_views + 1 WHERE id = ?")->execute([$resume['id']]);

// Buscar dados relacionados
$stmt = $pdo->prepare("SELECT * FROM experiences WHERE resume_id = ? ORDER BY start_date DESC");
$stmt->execute([$resume['id']]);
$experiences = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM education WHERE resume_id = ? ORDER BY start_date DESC");
$stmt->execute([$resume['id']]);
$education = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM skills WHERE resume_id = ? ORDER BY category");
$stmt->execute([$resume['id']]);
$skills = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM projects WHERE resume_id = ?");
$stmt->execute([$resume['id']]);
$projects = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM certifications WHERE resume_id = ? ORDER BY issue_date DESC");
$stmt->execute([$resume['id']]);
$certifications = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM languages WHERE resume_id = ?");
$stmt->execute([$resume['id']]);
$languages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $resume['language']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $resume['full_name']; ?> - <?php echo $resume['title']; ?></title>
    <meta name="description" content="<?php echo $resume['seo_description'] ?: $resume['professional_title']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-green: #10b981;
            --primary-purple: #8b5cf6;
            --dark-green: #047857;
            --dark-purple: #6d28d9;
            --light-green: #d1fae5;
            --light-purple: #ede9fe;
        }

        .resume-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-purple) 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
        }

        .resume-header h1 {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .resume-header .subtitle {
            font-size: 1.3rem;
            opacity: 0.95;
        }

        .section-title {
            color: var(--primary-green);
            font-weight: 700;
            font-size: 1.8rem;
            margin-top: 40px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--primary-green);
        }

        .profile-img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .contact-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .contact-item i {
            color: var(--primary-green);
            width: 30px;
            font-size: 1.3rem;
            margin-right: 15px;
        }

        .experience-item, .education-item, .skill-item, .project-item, .cert-item, .lang-item {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 12px;
            border-left: 5px solid var(--primary-green);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .experience-item:hover, .education-item:hover, .project-item:hover {
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.2);
            transform: translateY(-2px);
        }

        .experience-item h4, .education-item h4, .project-item h4 {
            color: var(--primary-purple);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .experience-item .company, .education-item .institution {
            color: var(--primary-green);
            font-weight: 600;
            margin-bottom: 5px;
        }

        .date-range {
            color: #999;
            font-size: 0.95rem;
            margin-bottom: 10px;
        }

        .skill-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-purple) 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            margin: 5px;
            font-weight: 600;
        }

        .skill-level {
            display: inline-block;
            width: 100%;
            margin-top: 10px;
        }

        .progress {
            height: 8px;
            background: var(--light-green);
            border-radius: 10px;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-purple) 100%);
        }

        .whatsapp-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-green) 0%, #34d399 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 30px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
            z-index: 999;
            text-decoration: none;
        }

        .whatsapp-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6);
        }

        .back-btn {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-purple) 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            min-height: 45px;
            cursor: pointer;
            border: none;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .empty-section {
            background: var(--light-green);
            padding: 20px;
            border-radius: 8px;
            color: var(--dark-green);
            text-align: center;
            font-style: italic;
        }
        @media (max-width: 768px) {
            .resume-header {
                padding: 40px 0;
                margin-bottom: 20px;
            }

            .resume-header h1 {
                font-size: 1.8rem;
                margin-bottom: 5px;
            }

            .resume-header .subtitle {
                font-size: 1rem;
            }

            .profile-img {
                width: 120px;
                height: 120px;
            }

            .section-title {
                font-size: 1.3rem;
                margin-top: 20px;
                margin-bottom: 15px;
            }

            .contact-card {
                padding: 15px;
                margin-bottom: 15px;
            }

            .contact-item {
                margin-bottom: 10px;
                font-size: 0.95rem;
            }

            .experience-item, .education-item, .skill-item, .project-item, .cert-item, .lang-item {
                padding: 15px;
                margin-bottom: 15px;
            }

            .back-btn {
                padding: 8px 15px;
                font-size: 0.9rem;
                margin-bottom: 10px;
            }

            .skill-badge {
                padding: 5px 10px;
                font-size: 0.85rem;
                margin: 3px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="resume-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <?php if ($resume['photo']): ?>
                        <img src="<?php echo BASE_URL; ?>/assets/uploads/<?php echo $resume['photo']; ?>" alt="<?php echo $resume['full_name']; ?>" class="profile-img">
                    <?php else: ?>
                        <div class="profile-img" style="background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="font-size: 60px;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-9">
                    <h1><?php echo htmlspecialchars($resume['full_name']); ?></h1>
                    <p class="subtitle"><?php echo htmlspecialchars($resume['professional_title']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container" style="max-width: 1000px; margin-bottom: 60px;">
        <div style="display: flex; gap: 10px; margin-bottom: 30px;">
            <a href="<?php echo BASE_URL; ?>" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>Voltar ao Início
            </a>
            <a href="<?php echo BASE_URL; ?>/generate_pdf.php?slug=<?php echo $resume['id']; ?>" class="back-btn" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
                <i class="fas fa-file-pdf me-2"></i>Gerar PDF
            </a>
            <button type="button" class="back-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#contactModal">
                <i class="fas fa-envelope me-2"></i>Enviar Mensagem
            </button>
        </div>

        <!-- Contato -->
        <div class="contact-card">
            <h5 style="color: var(--primary-green); margin-bottom: 15px;">
                <i class="fas fa-address-card me-2"></i>Informações de Contato
            </h5>
            <?php if ($resume['email']): ?>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:<?php echo $resume['email']; ?>"><?php echo $resume['email']; ?></a>
                </div>
            <?php endif; ?>
            <?php if ($resume['phone']): ?>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <?php echo $resume['phone']; ?>
                </div>
            <?php endif; ?>
            <?php if ($resume['location']): ?>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo $resume['location']; ?>
                </div>
            <?php endif; ?>
            <?php if ($resume['linkedin']): ?>
                <div class="contact-item">
                    <i class="fab fa-linkedin"></i>
                    <a href="<?php echo $resume['linkedin']; ?>" target="_blank">LinkedIn</a>
                </div>
            <?php endif; ?>
            <?php if ($resume['github']): ?>
                <div class="contact-item">
                    <i class="fab fa-github"></i>
                    <a href="<?php echo $resume['github']; ?>" target="_blank">GitHub</a>
                </div>
            <?php endif; ?>
            <?php if ($resume['portfolio_url']): ?>
                <div class="contact-item">
                    <i class="fas fa-briefcase"></i>
                    <a href="<?php echo $resume['portfolio_url']; ?>" target="_blank">Portfólio</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Objetivo Profissional -->
        <?php if ($resume['objective']): ?>
            <h2 class="section-title"><i class="fas fa-bullseye me-2"></i>Objetivo Profissional</h2>
            <div class="experience-item">
                <p><?php echo nl2br(htmlspecialchars($resume['objective'])); ?></p>
            </div>
        <?php endif; ?>

        <!-- Sobre Mim -->
        <?php if ($resume['about']): ?>
            <h2 class="section-title"><i class="fas fa-user-circle me-2"></i>Sobre Mim</h2>
            <div class="experience-item">
                <p><?php echo nl2br(htmlspecialchars($resume['about'])); ?></p>
            </div>
        <?php endif; ?>

        <!-- Experiência Profissional -->
        <?php if (!empty($experiences)): ?>
            <h2 class="section-title"><i class="fas fa-briefcase me-2"></i>Experiência Profissional</h2>
            <?php foreach ($experiences as $exp): ?>
                <div class="experience-item">
                    <h4><?php echo htmlspecialchars($exp['position']); ?></h4>
                    <div class="company"><i class="fas fa-building me-2"></i><?php echo htmlspecialchars($exp['company']); ?></div>
                    <div class="date-range">
                        <i class="fas fa-calendar me-2"></i>
                        <?php echo date('M/Y', strtotime($exp['start_date'])); ?> - 
                        <?php echo $exp['current_job'] ? 'Presente' : date('M/Y', strtotime($exp['end_date'])); ?>
                    </div>
                    <?php if ($exp['description']): ?>
                        <p><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                    <?php endif; ?>
                    <?php if ($exp['achievements']): ?>
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                            <strong style="color: var(--primary-purple);">Conquistas:</strong>
                            <p><?php echo nl2br(htmlspecialchars($exp['achievements'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <h2 class="section-title"><i class="fas fa-briefcase me-2"></i>Experiência Profissional</h2>
            <div class="empty-section">Nenhuma experiência cadastrada</div>
        <?php endif; ?>

        <!-- Educação -->
        <?php if (!empty($education)): ?>
            <h2 class="section-title"><i class="fas fa-graduation-cap me-2"></i>Educação</h2>
            <?php foreach ($education as $edu): ?>
                <div class="education-item">
                    <h4><?php echo htmlspecialchars($edu['degree']); ?></h4>
                    <div class="institution"><i class="fas fa-school me-2"></i><?php echo htmlspecialchars($edu['institution']); ?></div>
                    <div class="date-range">
                        <i class="fas fa-calendar me-2"></i>
                        <?php echo date('M/Y', strtotime($edu['start_date'])); ?> - 
                        <?php echo date('M/Y', strtotime($edu['end_date'])); ?>
                    </div>
                    <?php if ($edu['description']): ?>
                        <p><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <h2 class="section-title"><i class="fas fa-graduation-cap me-2"></i>Educação</h2>
            <div class="empty-section">Nenhuma formação cadastrada</div>
        <?php endif; ?>

        <!-- Habilidades -->
        <?php if (!empty($skills)): ?>
            <h2 class="section-title"><i class="fas fa-star me-2"></i>Habilidades</h2>
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);">
                <?php foreach ($skills as $skill): ?>
                    <div class="skill-item" style="border-left: none; padding: 0; margin-bottom: 20px; box-shadow: none;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <strong style="color: var(--primary-purple);"><?php echo htmlspecialchars($skill['name']); ?></strong>
                            <span style="color: var(--primary-green);"><?php echo $skill['level']; ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $skill['level']; ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <h2 class="section-title"><i class="fas fa-star me-2"></i>Habilidades</h2>
            <div class="empty-section">Nenhuma habilidade cadastrada</div>
        <?php endif; ?>

        <!-- Projetos -->
        <?php if (!empty($projects)): ?>
            <h2 class="section-title"><i class="fas fa-project-diagram me-2"></i>Projetos</h2>
            <?php foreach ($projects as $proj): ?>
                <div class="project-item">
                    <h4><?php echo htmlspecialchars($proj['title']); ?></h4>
                    <p><?php echo nl2br(htmlspecialchars($proj['description'])); ?></p>
                    <?php if ($proj['technologies']): ?>
                        <div style="margin-top: 10px;">
                            <strong style="color: var(--primary-green);">Tecnologias:</strong><br>
                            <?php foreach (explode(',', $proj['technologies']) as $tech): ?>
                                <span class="skill-badge"><?php echo trim($tech); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($proj['link']): ?>
                        <div style="margin-top: 10px;">
                            <a href="<?php echo $proj['link']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt me-2"></i>Ver Projeto
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Certificações -->
        <?php if (!empty($certifications)): ?>
            <h2 class="section-title"><i class="fas fa-certificate me-2"></i>Certificações</h2>
            <?php foreach ($certifications as $cert): ?>
                <div class="cert-item" style="border-left-color: var(--primary-purple);">
                    <h4><?php echo htmlspecialchars($cert['name']); ?></h4>
                    <div style="color: var(--primary-purple); font-weight: 600; margin-bottom: 5px;">
                        <i class="fas fa-building me-2"></i><?php echo htmlspecialchars($cert['issuing_organization']); ?>
                    </div>
                    <div class="date-range">
                        <i class="fas fa-calendar me-2"></i>
                        <?php echo date('M/Y', strtotime($cert['issue_date'])); ?>
                        <?php if ($cert['expiration_date']): ?>
                            - <?php echo date('M/Y', strtotime($cert['expiration_date'])); ?>
                        <?php endif; ?>
                    </div>
                    <?php if ($cert['credential_url']): ?>
                        <a href="<?php echo htmlspecialchars($cert['credential_url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-link me-2"></i>Ver Credencial
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Idiomas -->
        <?php if (!empty($languages)): ?>
            <h2 class="section-title"><i class="fas fa-language me-2"></i>Idiomas</h2>
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);">
                <div class="row">
                    <?php foreach ($languages as $lang): ?>
                        <div class="col-md-6 mb-3">
                            <div style="padding: 15px; background: var(--light-green); border-radius: 8px; border-left: 4px solid var(--primary-green);">
                                <strong style="color: var(--primary-green);"><?php echo htmlspecialchars($lang['language_name']); ?></strong>
                                <div style="color: var(--dark-green); font-size: 0.9rem;">
                                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($lang['proficiency']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- WhatsApp Button -->
    <a href="https://wa.me/5517981600610?text=Olá%20Rodrigo!%20Visitei%20seu%20currículo%20e%20gostaria%20de%20conversar." target="_blank" class="whatsapp-btn" title="Enviar mensagem via WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

    <!-- Modal de Contato -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-purple) 100%); color: white;">
                    <h5 class="modal-title" id="contactModalLabel"><i class="fas fa-envelope me-2"></i>Enviar Mensagem</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="contactForm">
                        <div class="mb-3">
                            <label for="contactName" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="contactName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="contactEmail" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="contactEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="contactPhone" class="form-label">Telefone (Opcional)</label>
                            <input type="tel" class="form-control" id="contactPhone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="contactSubject" class="form-label">Assunto</label>
                            <input type="text" class="form-control" id="contactSubject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="contactMessage" class="form-label">Mensagem</label>
                            <textarea class="form-control" id="contactMessage" name="message" rows="4" required></textarea>
                        </div>
                        <div id="contactStatus"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn" style="background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-purple) 100%); color: white; border: none;" id="sendMessageBtn">Enviar Mensagem</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sendMessageBtn').addEventListener('click', function() {
            const form = document.getElementById('contactForm');
            const statusDiv = document.getElementById('contactStatus');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const formData = new FormData(form);
            const button = this;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
            
            fetch('<?php echo BASE_URL; ?>/api/send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Mensagem enviada com sucesso!</div>';
                    form.reset();
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('contactModal'));
                        modal.hide();
                        statusDiv.innerHTML = '';
                    }, 2000);
                } else {
                    statusDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + (data.message || 'Erro ao enviar mensagem') + '</div>';
                }
            })
            .catch(error => {
                statusDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Erro ao enviar mensagem</div>';
                console.error('Error:', error);
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-envelope me-2"></i>Enviar Mensagem';
            });
        });
    </script>
</body>
</html>
