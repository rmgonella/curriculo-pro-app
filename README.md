# CurrículosPro ULTIMATE - Sistema de Currículos Online

Um sistema profissional e moderno de gerenciamento de currículos online para **Rodrigo Marchi Gonella**, desenvolvido com PHP, MySQL e Bootstrap.

## 🚀 Características

### ✨ Frontend
- **Home moderna** com listagem de currículos
- **Visualização premium** de currículos com design responsivo
- **Formulário de contato** integrado em cada currículo
- **Contador de acessos** em tempo real
- **Design Bootstrap 5** ultra moderno e profissional

### 📊 Dashboard Administrativo
- **Dashboard completo** com estatísticas gerais
- **Gerenciamento de múltiplos currículos** por usuário
- **Sistema de Inbox** para mensagens recebidas
- **Analytics avançado** com gráficos de visualizações
- **Abas/Tabs ultra completas** para cadastro de currículo:
  - Informações Básicas
  - Experiência Profissional
  - Formação Acadêmica
  - Habilidades
  - Projetos
  - Certificações
  - Idiomas

### 💬 Sistema de Mensagens
- Receba mensagens de contatos através do frontend
- Gerenciamento de Inbox no painel administrativo
- Marcar mensagens como lidas
- Responder por email

### 📈 Analytics
- Contador de acessos por currículo
- Visualizações por data (últimos 7 dias)
- Gráficos interativos com Chart.js
- Taxa de engajamento (mensagens/visualizações)

## 📋 Requisitos

- PHP 7.4+
- MySQL 5.7+
- Apache com mod_rewrite ativado
- Composer (opcional)

## 🔧 Instalação

### 1. Importar o Banco de Dados

```bash
mysql -u seu_usuario -p seu_banco < database.sql
```

### 2. Configurar Credenciais

Edite o arquivo `includes/config.php` com suas credenciais:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'seu_banco');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

### 3. Criar Diretório de Uploads

```bash
mkdir -p assets/uploads
chmod 755 assets/uploads
```

### 4. Acessar o Sistema

- **Frontend**: `http://seu-dominio.com`
- **Admin**: `http://seu-dominio.com/admin/login.php`

## 🔐 Credenciais Padrão

```
Usuário: admin
Senha: admin123
```

⚠️ **IMPORTANTE**: Altere a senha padrão após o primeiro acesso!

## 📁 Estrutura de Diretórios

```
curriculo_online/
├── admin/
│   ├── login.php
│   ├── index.php
│   ├── header.php
│   ├── footer.php
│   └── pages/
│       ├── dashboard.php
│       ├── resume.php
│       ├── messages.php
│       └── analytics.php
├── views/
│   ├── home.php
│   ├── resume_view.php
│   └── 404.php
├── includes/
│   └── config.php
├── assets/
│   ├── uploads/
│   ├── css/
│   └── js/
├── index.php
├── database.sql
└── README.md
```

## 🎨 Customização

### Cores e Tema

Edite as variáveis CSS nos arquivos de visualização:

```css
:root {
    --primary: #667eea;
    --secondary: #764ba2;
}
```

### Informações Pessoais

Edite o arquivo `views/home.php` para atualizar:
- Nome
- Profissão
- Descrição
- Contatos
- Links de redes sociais

## 🔄 Fluxo de Uso

### Para o Usuário (Rodrigo)

1. **Login** no painel administrativo
2. **Criar novo currículo** com título e informações básicas
3. **Preencher abas** com experiência, educação, habilidades, etc.
4. **Ativar currículo** para aparecer na home
5. **Monitorar** visualizações e mensagens no dashboard

### Para Recrutadores

1. **Acessar home** e ver currículos disponíveis
2. **Visualizar currículo** completo com design profissional
3. **Enviar mensagem** através do formulário de contato
4. **Acompanhar** com link de portfólio e redes sociais

## 📊 Banco de Dados

### Tabelas Principais

- **users**: Usuários do sistema
- **resumes**: Currículos
- **experiences**: Experiências profissionais
- **education**: Formação acadêmica
- **skills**: Habilidades
- **projects**: Projetos
- **certifications**: Certificações
- **languages**: Idiomas
- **messages**: Mensagens recebidas
- **views**: Registro de acessos

## 🚀 Deploy

### Requisitos de Servidor

- Suporte a PHP 7.4+
- MySQL 5.7+
- Espaço em disco: 100MB mínimo
- Banda: Conforme necessário

### Passos

1. Upload dos arquivos via FTP/SFTP
2. Importar `database.sql` no MySQL
3. Configurar `includes/config.php`
4. Definir permissões de pasta: `chmod 755 assets/uploads`
5. Testar acesso ao admin e frontend

## 🐛 Troubleshooting

### Erro de Conexão com Banco

Verifique as credenciais em `includes/config.php`

### Uploads não funcionam

```bash
chmod 755 assets/uploads
chmod 755 assets
```

### Página em branco

Ative exibição de erros em `includes/config.php`:

```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

## 📞 Suporte

Para dúvidas ou problemas, entre em contato através do formulário de mensagens no site.

## 📄 Licença

Este projeto é de propriedade de Rodrigo Marchi Gonella.

---

**Desenvolvido com ❤️ usando PHP, MySQL e Bootstrap**
