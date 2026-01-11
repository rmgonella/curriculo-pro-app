<?php
require_once '../includes/config.php';

// Simular sessão de admin
$_SESSION['admin_id'] = 1;

echo "<h1>Teste de Criação de Currículo</h1>";

// Teste 1: Verificar conexão com banco
echo "<h2>1. Verificando conexão com banco de dados...</h2>";
try {
    $result = $pdo->query("SELECT 1");
    echo "✅ Conexão OK<br>";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
    exit;
}

// Teste 2: Verificar estrutura da tabela resumes
echo "<h2>2. Verificando estrutura da tabela resumes...</h2>";
try {
    $result = $pdo->query("DESCRIBE resumes");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    echo "Colunas encontradas: " . count($columns) . "<br>";
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

// Teste 3: Tentar criar um currículo de teste
echo "<h2>3. Tentando criar um currículo de teste...</h2>";
try {
    $data = [
        'user_id' => 1,
        'title' => 'Currículo Teste',
        'full_name' => 'Rodrigo Marchi Gonella',
        'professional_title' => 'Designer Gráfico',
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
    
    $stmt = $pdo->prepare("INSERT INTO resumes (user_id, title, full_name, professional_title, about, objective, email, phone, location, linkedin, github, portfolio_url, personal_website, theme, language, seo_description, photo, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $result = $stmt->execute(array_values($data));
    
    if ($result) {
        $resume_id = $pdo->lastInsertId();
        echo "✅ Currículo criado com sucesso! ID: " . $resume_id . "<br>";
        
        // Verificar se foi realmente criado
        $check = $pdo->prepare("SELECT * FROM resumes WHERE id = ?");
        $check->execute([$resume_id]);
        $resume = $check->fetch();
        
        if ($resume) {
            echo "✅ Currículo verificado no banco de dados<br>";
            echo "Dados: " . json_encode($resume) . "<br>";
        } else {
            echo "❌ Currículo não foi encontrado no banco de dados<br>";
        }
    } else {
        echo "❌ Erro ao inserir currículo<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
    echo "Código: " . $e->getCode() . "<br>";
}

// Teste 4: Listar currículos do usuário
echo "<h2>4. Listando currículos do usuário...</h2>";
try {
    $stmt = $pdo->prepare("SELECT * FROM resumes WHERE user_id = ?");
    $stmt->execute([1]);
    $resumes = $stmt->fetchAll();
    echo "Currículos encontrados: " . count($resumes) . "<br>";
    foreach ($resumes as $resume) {
        echo "- ID: " . $resume['id'] . " | Título: " . $resume['title'] . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

?>
