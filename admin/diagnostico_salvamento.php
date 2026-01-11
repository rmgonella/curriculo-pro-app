<?php
require_once '../includes/config.php';

// Simular POST request
$_POST = [
    'tab' => 'basic',
    'title' => 'Teste Currículo',
    'full_name' => 'Rodrigo Marchi Gonella',
    'professional_title' => 'Designer',
    'about' => 'Teste',
    'objective' => 'Teste',
    'email' => 'test@example.com',
    'phone' => '1798160061',
    'location' => 'Niterói',
    'linkedin' => '',
    'github' => '',
    'portfolio_url' => '',
    'personal_website' => '',
    'theme' => 'light',
    'language' => 'pt',
    'seo_description' => 'Teste'
];

$_SESSION['admin_id'] = 1;
$_SERVER['REQUEST_METHOD'] = 'POST';

echo "<h1>Diagnóstico de Salvamento de Currículo</h1>";

// Teste 1: Verificar conexão
echo "<h2>1. Verificando conexão PDO...</h2>";
try {
    $result = $pdo->query("SELECT 1");
    echo "✅ Conexão OK<br>";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
    exit;
}

// Teste 2: Montar dados
echo "<h2>2. Montando dados para INSERT...</h2>";
$data = [
    'title' => $_POST['title'] ?? '',
    'full_name' => $_POST['full_name'] ?? 'Rodrigo Marchi Gonella',
    'professional_title' => $_POST['professional_title'] ?? '',
    'about' => $_POST['about'] ?? '',
    'objective' => $_POST['objective'] ?? '',
    'email' => $_POST['email'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'location' => $_POST['location'] ?? '',
    'linkedin' => $_POST['linkedin'] ?? '',
    'github' => $_POST['github'] ?? '',
    'portfolio_url' => $_POST['portfolio_url'] ?? '',
    'personal_website' => $_POST['personal_website'] ?? '',
    'theme' => $_POST['theme'] ?? 'light',
    'language' => $_POST['language'] ?? 'pt',
    'seo_description' => $_POST['seo_description'] ?? ''
];

$data['user_id'] = $_SESSION['admin_id'];

echo "Dados a inserir:<br>";
echo "<pre>";
print_r($data);
echo "</pre>";

// Teste 3: Preparar statement
echo "<h2>3. Preparando statement SQL...</h2>";
$sql = "INSERT INTO resumes (user_id, title, full_name, professional_title, about, objective, email, phone, location, linkedin, github, portfolio_url, personal_website, theme, language, seo_description, photo, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
echo "SQL: " . htmlspecialchars($sql) . "<br>";

try {
    $stmt = $pdo->prepare($sql);
    echo "✅ Statement preparado com sucesso<br>";
} catch (Exception $e) {
    echo "❌ Erro ao preparar statement: " . $e->getMessage() . "<br>";
    exit;
}

// Teste 4: Executar INSERT
echo "<h2>4. Executando INSERT...</h2>";
try {
    $values = array_values($data);
    echo "Valores: " . json_encode($values) . "<br>";
    
    $result = $stmt->execute($values);
    
    if ($result) {
        echo "✅ INSERT executado com sucesso<br>";
        
        $resume_id = $pdo->lastInsertId();
        echo "ID gerado: " . $resume_id . "<br>";
        
        // Teste 5: Verificar se foi realmente salvo
        echo "<h2>5. Verificando se foi salvo no banco...</h2>";
        $check = $pdo->prepare("SELECT * FROM resumes WHERE id = ?");
        $check->execute([$resume_id]);
        $resume = $check->fetch(PDO::FETCH_ASSOC);
        
        if ($resume) {
            echo "✅ Currículo encontrado no banco!<br>";
            echo "<pre>";
            print_r($resume);
            echo "</pre>";
        } else {
            echo "❌ Currículo NÃO foi encontrado no banco!<br>";
        }
    } else {
        echo "❌ INSERT falhou<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro ao executar INSERT: " . $e->getMessage() . "<br>";
    echo "Código: " . $e->getCode() . "<br>";
}

// Teste 6: Listar todos os currículos
echo "<h2>6. Listando todos os currículos do usuário...</h2>";
try {
    $stmt = $pdo->prepare("SELECT id, title, full_name, created_at FROM resumes WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([1]);
    $resumes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total de currículos: " . count($resumes) . "<br>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Título</th><th>Nome</th><th>Criado em</th></tr>";
    foreach ($resumes as $r) {
        echo "<tr>";
        echo "<td>" . $r['id'] . "</td>";
        echo "<td>" . $r['title'] . "</td>";
        echo "<td>" . $r['full_name'] . "</td>";
        echo "<td>" . $r['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "❌ Erro ao listar: " . $e->getMessage() . "<br>";
}

?>
