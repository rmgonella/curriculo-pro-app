<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/config.php';

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Aceitar resume_id ou slug
 */
if (isset($_GET['resume_id'])) {
    $resume_id = (int) $_GET['resume_id'];
} elseif (isset($_GET['slug'])) {
    $resume_id = (int) $_GET['slug'];
} else {
    die('Currículo não encontrado.');
}

/**
 * Buscar currículo
 */
$stmt = $pdo->prepare("SELECT * FROM resumes WHERE id = ?");
$stmt->execute([$resume_id]);
$resume = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resume) {
    die('Currículo não encontrado.');
}

/**
 * Função utilitária
 */
function fetchAllByResume(PDO $pdo, string $table, int $resume_id, string $order = null): array {
    $sql = "SELECT * FROM {$table} WHERE resume_id = ?";
    if ($order) {
        $sql .= " ORDER BY {$order}";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$resume_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Dados relacionados
 */
$experiences    = fetchAllByResume($pdo, 'experiences', $resume_id, 'start_date DESC');
$education      = fetchAllByResume($pdo, 'education', $resume_id, 'start_date DESC');
$skills         = fetchAllByResume($pdo, 'skills', $resume_id, 'category');
$certifications = fetchAllByResume($pdo, 'certifications', $resume_id, 'issue_date DESC');
$languages      = fetchAllByResume($pdo, 'languages', $resume_id);

/**
 * HTML do currículo
 */
$html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #333; }
.container { padding: 20px; }

.header {
    border-bottom: 3px solid #10b981;
    margin-bottom: 15px;
    padding-bottom: 8px;
}

.name { font-size: 24px; font-weight: bold; color: #10b981; }
.title { font-size: 14px; color: #6366f1; margin-top: 2px; }

.contact {
    font-size: 10px;
    margin-top: 6px;
    line-height: 1.6;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.contact-icon {
    width: 12px;
    height: 12px;
}

.section { margin-top: 15px; }

.section-title {
    background: #10b981;
    color: #fff;
    padding: 5px;
    font-weight: bold;
    font-size: 11px;
    text-transform: uppercase;
}

.item { margin-top: 8px; }
.item-title { font-weight: bold; }
.item-subtitle { color: #555; }
.item-date { font-size: 9px; color: #777; }
.item-description { font-size: 10px; margin-top: 3px; }

.skills span {
    display: inline-block;
    background: #10b981;
    color: #fff;
    padding: 3px 6px;
    margin: 2px;
    font-size: 9px;
}

.box {
    background: #f0fdf4;
    border-left: 4px solid #10b981;
    padding: 8px;
    font-size: 10px;
}
</style>
</head>
<body>
<div class="container">

<div class="header">
    <div class="name">' . htmlspecialchars($resume['full_name']) . '</div>
    <div class="title">' . htmlspecialchars($resume['professional_title']) . '</div>
    <div class="contact">';

/**
 * Contato com ícones SVG
 */
if (!empty($resume['email'])) {
    $html .= '
    <div class="contact-item">
        <img class="contact-icon" src="data:image/svg+xml;base64,' . base64_encode('
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10b981">
                <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/>
            </svg>
        ') . '" />
        <span>' . htmlspecialchars($resume['email']) . '</span>
    </div>';
}

if (!empty($resume['phone'])) {
    $html .= '
    <div class="contact-item">
        <img class="contact-icon" src="data:image/svg+xml;base64,' . base64_encode('
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10b981">
                <path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.07 21 3 13.93 3 5a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.59a1 1 0 0 1-.25 1.01l-2.2 2.19Z"/>
            </svg>
        ') . '" />
        <span>' . htmlspecialchars($resume['phone']) . '</span>
    </div>';
}

if (!empty($resume['location'])) {
    $html .= '
    <div class="contact-item">
        <img class="contact-icon" src="data:image/svg+xml;base64,' . base64_encode('
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10b981">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/>
            </svg>
        ') . '" />
        <span>' . htmlspecialchars($resume['location']) . '</span>
    </div>';
}

$html .= '</div></div>';

/**
 * Objetivo
 */
if (!empty($resume['objective'])) {
    $html .= '<div class="section">
        <div class="section-title">Objetivo Profissional</div>
        <div class="box">' . nl2br(htmlspecialchars($resume['objective'])) . '</div>
    </div>';
}

/**
 * Sobre
 */
if (!empty($resume['about'])) {
    $html .= '<div class="section">
        <div class="section-title">Sobre Mim</div>
        <div class="box">' . nl2br(htmlspecialchars($resume['about'])) . '</div>
    </div>';
}

/**
 * Experiência
 */
if ($experiences) {
    $html .= '<div class="section"><div class="section-title">Experiência Profissional</div>';
    foreach ($experiences as $exp) {
        $html .= '<div class="item">
            <div class="item-title">' . htmlspecialchars($exp['position']) . '</div>
            <div class="item-subtitle">' . htmlspecialchars($exp['company']) . '</div>
            <div class="item-date">'
            . date('m/Y', strtotime($exp['start_date'])) . ' - '
            . (!empty($exp['current_job']) ? 'Atual' : date('m/Y', strtotime($exp['end_date'])))
            . '</div>';

        if (!empty($exp['description'])) {
            $html .= '<div class="item-description">' . nl2br(htmlspecialchars($exp['description'])) . '</div>';
        }
        $html .= '</div>';
    }
    $html .= '</div>';
}

/**
 * Educação
 */
if ($education) {
    $html .= '<div class="section"><div class="section-title">Educação</div>';
    foreach ($education as $edu) {
        $html .= '<div class="item">
            <div class="item-title">'
            . htmlspecialchars($edu['degree'])
            . (!empty($edu['field_of_study']) ? ' em ' . htmlspecialchars($edu['field_of_study']) : '')
            . '</div>
            <div class="item-subtitle">' . htmlspecialchars($edu['institution']) . '</div>';

        if (!empty($edu['end_date'])) {
            $html .= '<div class="item-date">Conclusão: ' . date('m/Y', strtotime($edu['end_date'])) . '</div>';
        }
        $html .= '</div>';
    }
    $html .= '</div>';
}

/**
 * Habilidades
 */
if ($skills) {
    $html .= '<div class="section"><div class="section-title">Habilidades</div><div class="skills">';
    foreach ($skills as $skill) {
        $html .= '<span>' . htmlspecialchars($skill['name']) . '</span>';
    }
    $html .= '</div></div>';
}

/**
 * Idiomas
 */
if ($languages) {
    $html .= '<div class="section"><div class="section-title">Idiomas</div>';
    foreach ($languages as $lang) {
        $html .= '<div class="item">' . htmlspecialchars($lang['language_name']) . ' — ' . htmlspecialchars($lang['proficiency']) . '</div>';
    }
    $html .= '</div>';
}

$html .= '</div></body></html>';

/**
 * Gerar PDF
 */
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $resume['full_name']) . '_' . date('Y-m-d') . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo $dompdf->output();
exit;
