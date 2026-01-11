# CurrículosPro ULTIMATE - Online Resume System

A professional and modern online resume management system for **Rodrigo Marchi Gonella**, developed with PHP, MySQL, and Bootstrap.

## 🚀 Features

### ✨ Frontend

* **Modern homepage** with resume listing
* **Premium resume view** with responsive design
* **Integrated contact form** on each resume
* **Real-time view counter**
* **Ultra-modern and professional Bootstrap 5 design**

### 📊 Administrative Dashboard

* **Complete dashboard** with general statistics
* **Management of multiple resumes** per user
* **Inbox system** for received messages
* **Advanced analytics** with view charts
* **Ultra-complete tabs** for resume creation:

  * Basic Information
  * Professional Experience
  * Academic Education
  * Skills
  * Projects
  * Certifications
  * Languages

### 💬 Messaging System

* Receive contact messages through the frontend
* Inbox management in the admin panel
* Mark messages as read
* Reply via email

### 📈 Analytics

* View counter per resume
* Views by date (last 7 days)
* Interactive charts with Chart.js
* Engagement rate (messages/views)

## 📋 Requirements

* PHP 7.4+
* MySQL 5.7+
* Apache with mod_rewrite enabled
* Composer (optional)

## 🔧 Installation

### 1. Import the Database

```bash
mysql -u your_user -p your_database < database.sql
```

### 2. Configure Credentials

Edit the file `includes/config.php` with your credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database');
define('DB_USER', 'your_user');
define('DB_PASS', 'your_password');
```

### 3. Create Uploads Directory

```bash
mkdir -p assets/uploads
chmod 755 assets/uploads
```

### 4. Access the System

* **Frontend**: `http://your-domain.com`
* **Admin**: `http://your-domain.com/admin/login.php`

## 🔐 Default Credentials

```
Username: admin
Password: admin123
```

⚠️ **IMPORTANT**: Change the default password after the first login!

## 📁 Directory Structure

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

## 🎨 Customization

### Colors and Theme

Edit the CSS variables in the view files:

```css
:root {
    --primary: #667eea;
    --secondary: #764ba2;
}
```

### Personal Information

Edit the file `views/home.php` to update:

* Name
* Profession
* Description
* Contacts
* Social media links

## 🔄 Usage Flow

### For the User (Rodrigo)

1. **Log in** to the admin panel
2. **Create a new resume** with title and basic information
3. **Fill in the tabs** with experience, education, skills, etc.
4. **Activate the resume** to appear on the homepage
5. **Monitor** views and messages on the dashboard

### For Recruiters

1. **Access the homepage** and view available resumes
2. **View the full resume** with professional design
3. **Send a message** through the contact form
4. **Follow** portfolio and social media links

## 📊 Database

### Main Tables

* **users**: System users
* **resumes**: Resumes
* **experiences**: Professional experiences
* **education**: Academic education
* **skills**: Skills
* **projects**: Projects
* **certifications**: Certifications
* **languages**: Languages
* **messages**: Received messages
* **views**: View records

## 🚀 Deploy

### Server Requirements

* PHP 7.4+ support
* MySQL 5.7+
* Disk space: minimum 100MB
* Bandwidth: as needed

### Steps

1. Upload files via FTP/SFTP
2. Import `database.sql` into MySQL
3. Configure `includes/config.php`
4. Set folder permissions: `chmod 755 assets/uploads`
5. Test admin and frontend access

## 🐛 Troubleshooting

### Database Connection Error

Check the credentials in `includes/config.php`

### Uploads Not Working

```bash
chmod 755 assets/uploads
chmod 755 assets
```

### Blank Page

Enable error display in `includes/config.php`:

```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

## 📞 Support

For questions or issues, contact us through the site’s message form.

## 📄 License

This project is the property of Rodrigo Marchi Gonella.

---

**Developed by Rodrigo Marchi Gonella using PHP, MySQL, and Bootstrap**
