<?php
/**
 * Script de Diagnóstico - CurrículosPro
 * Use este script para identificar problemas de conexão e permissões
 */

echo "<h1>🔍 Diagnóstico do Sistema CurrículosPro</h1>";
echo "<hr>";

// 1. Testar conexão com banco de dados
echo "<h2>1️⃣ Teste de Conexão com Banco de Dados</h2>";
try {
    require_once '../includes/config.php';
    echo "<p style='color: green;'>✅ Conexão com banco de dados: <strong>OK</strong></p>";
    
    // Testar query simples
    $test = $pdo->query("SELECT 1");
    echo "<p style='color: green;'>✅ Query de teste: <strong>OK</strong></p>";
    
    // Listar tabelas
    $tables = $pdo->query("SHOW TABLES")->fetchAll();
    echo "<p>📋 Tabelas encontradas: <strong>" . count($tables) . "</strong></p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . $table[0] . "</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro na conexão: <strong>" . $e->getMessage() . "</strong></p>";
}

// 2. Testar permissões de pasta
echo "<h2>2️⃣ Teste de Permissões de Pasta</h2>";
$upload_dir = '../assets/uploads/';
if (is_dir($upload_dir)) {
    echo "<p style='color: green;'>✅ Pasta de upload existe: <strong>OK</strong></p>";
    if (is_writable($upload_dir)) {
        echo "<p style='color: green;'>✅ Pasta é gravável: <strong>OK</strong></p>";
    } else {
        echo "<p style='color: red;'>❌ Pasta NÃO é gravável: <strong>ERRO</strong></p>";
        echo "<p>Solução: Execute no terminal: <code>chmod 755 " . realpath($upload_dir) . "</code></p>";
    }
} else {
    echo "<p style='color: red;'>❌ Pasta de upload não existe: <strong>ERRO</strong></p>";
}

// 3. Testar sessão
echo "<h2>3️⃣ Teste de Sessão</h2>";
session_start();
if (isset($_SESSION['admin_id'])) {
    echo "<p style='color: green;'>✅ Sessão ativa: <strong>OK</strong></p>";
    echo "<p>Admin ID: <strong>" . $_SESSION['admin_id'] . "</strong></p>";
} else {
    echo "<p style='color: orange;'>⚠️ Nenhuma sessão ativa (normal se não está logado)</p>";
}

// 4. Testar recepção de POST
echo "<h2>4️⃣ Teste de Recepção POST</h2>";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<p style='color: green;'>✅ POST recebido com sucesso</p>";
    echo "<p>Dados recebidos:</p>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
} else {
    echo "<p>ℹ️ Nenhum POST recebido. Envie o formulário abaixo para testar:</p>";
    echo "<form method='POST'>";
    echo "<input type='text' name='test_field' placeholder='Digite algo' required>";
    echo "<button type='submit'>Testar POST</button>";
    echo "</form>";
}

// 5. Informações do Servidor
echo "<h2>5️⃣ Informações do Servidor</h2>";
echo "<ul>";
echo "<li>PHP Version: <strong>" . phpversion() . "</strong></li>";
echo "<li>Server Software: <strong>" . $_SERVER['SERVER_SOFTWARE'] . "</strong></li>";
echo "<li>Document Root: <strong>" . $_SERVER['DOCUMENT_ROOT'] . "</strong></li>";
echo "<li>Script Filename: <strong>" . $_SERVER['SCRIPT_FILENAME'] . "</strong></li>";
echo "</ul>";

// 6. Testar INSERT simples
echo "<h2>6️⃣ Teste de INSERT no Banco</h2>";
if (isset($_SESSION['admin_id'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['test_insert'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO resumes (user_id, title, full_name, professional_title, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([
                $_SESSION['admin_id'],
                'Teste ' . date('Y-m-d H:i:s'),
                'Rodrigo Marchi Gonella',
                'Designer',
            ]);
            echo "<p style='color: green;'>✅ INSERT funcionando: <strong>OK</strong></p>";
            echo "<p>Novo currículo criado com ID: <strong>" . $pdo->lastInsertId() . "</strong></p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erro no INSERT: <strong>" . $e->getMessage() . "</strong></p>";
        }
    } else {
        echo "<form method='POST'>";
        echo "<button type='submit' name='test_insert' value='1'>Testar INSERT</button>";
        echo "</form>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Faça login primeiro para testar INSERT</p>";
}

echo "<hr>";
echo "<p style='text-align: center; color: #999;'>Diagnóstico gerado em: " . date('Y-m-d H:i:s') . "</p>";
?>
