<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rodrigo Marchi Gonella - Currículos Profissionais</title>

<link rel="icon" href="/favicon.ico">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
:root {
    --primary:#10b981;
    --secondary:#8b5cf6;
}

body {
    background:#f8f9fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* ================= HERO ================= */
.hero {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color:#fff;
    padding:80px 0;
    position:relative;
    overflow:hidden;
}

.hero::before {
    content:'';
    position:absolute;
    top:-50%;
    right:-10%;
    width:500px;
    height:500px;
    background:rgba(255,255,255,.1);
    border-radius:50%;
}

.hero h1, .hero p {
    position:relative;
    z-index:2;
}

/* ================= STATS ================= */
.stats {
    display:flex;
    justify-content:space-around;
    padding:30px;
    background:#fff;
    border-radius:12px;
    margin-bottom:40px;
}

.stat-item { text-align:center; }
.stat-number { font-size:2rem;font-weight:700;color:var(--primary); }
.stat-label { color:#888;font-size:.9rem; }

/* ================= CARDS ================= */
.cv-card {
    border:none;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,.08);
    transition:.3s;
    height:100%;
}

.cv-card:hover {
    transform:translateY(-8px);
    box-shadow:0 15px 40px rgba(0,0,0,.15);
}

.cv-card-img {
    height:180px;
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:3rem;
    color:#fff;
}

.cv-card-body { padding:20px; }

.btn-view {
    display:block;
    background:var(--primary);
    color:#fff;
    padding:10px;
    border-radius:8px;
    text-align:center;
    text-decoration:none;
    font-weight:600;
}

.btn-view:hover {
    background:var(--secondary);
    color:#fff;
}

/* ================= FOOTER ================= */
footer {
    background:#2c3e50;
    color:#fff;
    padding:40px 0;
    margin-top:60px;
}

footer a {
    color:#bdc3c7;
    text-decoration:none;
}

footer a:hover { color:#fff; }

/* ================= WHATSAPP ================= */
.whatsapp-button {
    position:fixed;
    bottom:25px;
    right:25px;
    width:60px;
    height:60px;
    background:#25d366;
    color:#fff;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:30px;
    z-index:1000;
}

/* ================= MOBILE FIX ================= */
@media (max-width:768px) {

    /* HERO */
    .hero {
        text-align:center;
        padding:50px 0;
    }

    .hero .d-flex {
        align-items:center !important;
    }

    .hero .action-buttons {
        width:100%;
        max-width:320px;
        margin:0 auto;
    }

    /* STATS */
    .stats {
        flex-direction:column;
        gap:20px;
    }

    /* FOOTER (ISOLADO) */
    footer {
        text-align:center;
    }

    footer .row > div {
        text-align:center;
    }

    footer ul {
        padding-left:0;
    }

    footer ul li {
        list-style:none;
    }

    footer .btn-outline-light {
        margin:5px;
    }
}
</style>
</head>

<body>

<!-- HERO -->
<section class="hero">
<div class="container">
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">

<div>
<h1><i class="fas fa-briefcase me-2"></i>Rodrigo Marchi Gonella</h1>
<p>Currículos profissionais com as minhas especialidades e profissões!</p>
</div>

<div class="action-buttons d-flex flex-column gap-2">
<button class="btn btn-light w-100" data-bs-toggle="modal" data-bs-target="#contactModalHome">
<i class="fas fa-envelope me-2"></i>Enviar Mensagem
</button>

<a href="admin/login.php" class="btn btn-light w-100">
<i class="fas fa-lock me-2"></i>Área Administrativa
</a>
</div>

</div>
</div>
</section>

<!-- MAIN -->
<main class="container my-5">

<div class="stats">
<div class="stat-item">
<div class="stat-number"><?= count($resumes) ?></div>
<div class="stat-label">Currículos</div>
</div>
<div class="stat-item">
<div class="stat-number"><?= array_sum(array_column($resumes,'total_views')) ?></div>
<div class="stat-label">Visualizações</div>
</div>
<div class="stat-item">
<div class="stat-number">+15</div>
<div class="stat-label">Anos de Experiência</div>
</div>
</div>

<div class="row g-4">
<?php foreach ($resumes as $res): ?>
<div class="col-md-6 col-lg-4">
<div class="cv-card">
<div class="cv-card-img"><i class="fas fa-file-alt"></i></div>
<div class="cv-card-body">
<h5><?= $res['title'] ?></h5>
<p><?= $res['professional_title'] ?></p>
<a href="index.php?slug=<?= $res['id'] ?>" class="btn-view">Ver Currículo</a>
</div>
</div>
</div>
<?php endforeach; ?>
</div>

</main>

<!-- FOOTER -->
<footer>
<div class="container">
<div class="row mb-4">

<div class="col-md-4 mb-4">
<h5><i class="fas fa-briefcase me-2"></i>Rodrigo Marchi</h5>
<p>Sistema desenvolvido por mim para apresentar meus Currículos.</p>
</div>

<div class="col-md-4 mb-4">
<h5>Contato</h5>
<ul class="list-unstyled">
<li><a href="#" data-bs-toggle="modal" data-bs-target="#contactModalHome"><i class="fas fa-envelope me-2"></i>freelaforever@gmail.com</a></li>
<li><a href="https://wa.me/5517981600610" target="_blank"><i class="fas fa-phone me-2"></i>+55 (17) 98160-0610</a></li>
<li><i class="fas fa-map-marker-alt me-2"></i>Niterói, RJ</li>
</ul>
</div>

<div class="col-md-4 mb-4">
<h5>Redes Sociais</h5>
<a href="#" class="btn btn-sm btn-outline-light"><i class="fab fa-linkedin-in"></i></a>
<a href="#" class="btn btn-sm btn-outline-light"><i class="fab fa-github"></i></a>
<a href="#" class="btn btn-sm btn-outline-light"><i class="fab fa-instagram"></i></a>
<a href="#" class="btn btn-sm btn-outline-light"><i class="fas fa-globe"></i></a>
</div>

</div>

<hr style="border-color:rgba(255,255,255,.1)">
<div class="text-center">
<p>&copy; <?= date('Y') ?> - Rodrigo Marchi Gonella | <a href="admin/login.php"><i class="fas fa-lock me-1"></i>Área Restrita</a></p>
</div>
</div>
</footer>

<!-- MODAL -->
<div class="modal fade" id="contactModalHome" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header bg-success text-white">
<h5 class="modal-title"><i class="fas fa-envelope me-2"></i>Enviar Mensagem</h5>
<button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<form action="api/send_message.php" method="POST">
<div class="modal-body">
<input type="hidden" name="resume_id" value="<?= $resumes[0]['id'] ?? '' ?>">
<div class="mb-3">
<label class="form-label">Nome</label>
<input type="text" name="name" class="form-control" required placeholder="Seu nome">
</div>
<div class="mb-3">
<label class="form-label">E-mail</label>
<input type="email" name="email" class="form-control" required placeholder="Seu e-mail">
</div>
<div class="mb-3">
<label class="form-label">Assunto</label>
<input type="text" name="subject" class="form-control" required placeholder="Assunto da mensagem">
</div>
<div class="mb-3">
<label class="form-label">Mensagem</label>
<textarea name="message" class="form-control" rows="4" required placeholder="Como posso ajudar?"></textarea>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
<button type="submit" class="btn btn-success">Enviar Mensagem</button>
</div>
</form>
</div>
</div>
</div>

<a href="https://wa.me/5517981600610" class="whatsapp-button" target="_blank">
<i class="fab fa-whatsapp"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
