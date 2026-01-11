<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .error-container {
            text-align: center;
            color: white;
        }

        .error-code {
            font-size: 6rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .error-message {
            font-size: 1.5rem;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .error-description {
            font-size: 1rem;
            margin-bottom: 40px;
            opacity: 0.8;
        }

        .btn-home {
            background: white;
            color: #667eea;
            padding: 12px 40px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-home:hover {
            background: #f0f0f0;
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-message">Página não encontrada</div>
        <div class="error-description">O currículo que você está procurando não existe ou foi removido.</div>
        <a href="./" class="btn-home">
            <i class="fas fa-arrow-left me-2"></i>Voltar para Home
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
