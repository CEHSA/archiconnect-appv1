# ArchiConnect AI Platform

<p align="center">
<img src="public/images/logo.png" width="400" alt="ArchiConnect Logo">
</p>

<p align="center">
<strong>Intelligent Architecture Project Management Platform</strong><br>
🏗️ Project Management • ⏱️ Time Tracking • 🤖 AI Integration • 👥 Collaboration
</p>

## 🚀 About ArchiConnect AI

ArchiConnect AI is an advanced web application designed for architectural firms to manage projects, track time, and collaborate with clients and freelancers. Enhanced with AI capabilities, the platform provides:

### Core Features
- **🏗️ Project Management**: Comprehensive project tracking and task management
- **⏱️ Smart Time Tracking**: Automated time logging with AI-powered insights
- **👥 Team Collaboration**: Real-time communication and file sharing
- **💰 Financial Management**: Budget tracking, payment processing, and invoicing
- **🤖 AI Integration**: Intelligent project recommendations and automation
- **📊 Analytics Dashboard**: Real-time reporting and performance metrics

## 🛠️ Technology Stack

- **Backend**: Laravel 12 (PHP 8.4+)
- **Frontend**: TailwindCSS, Alpine.js, Vite
- **Database**: MySQL 8.0+ / SQLite (development)
- **AI Services**: OpenAI GPT, Custom ML Models
- **Build Tool**: Vite 6.0
- **Authentication**: Laravel Breeze with Multi-Factor Auth
- **Testing**: Pest PHP / PHPUnit
- **Deployment**: cPanel Automation, Git Hooks

## 📋 Requirements

### Development Environment
- **PHP**: 8.2+ (Recommended: 8.4+)
- **Database**: MySQL 8.0+ or SQLite 3.35+
- **Node.js**: 18+ (Recommended: 20 LTS)
- **Composer**: 2.5+
- **Git**: 2.40+

### Production Environment
- **Web Server**: Apache 2.4+ with mod_rewrite
- **PHP**: 8.2+ with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **SSL Certificate**: Required for production deployment
- **Memory**: 512MB+ recommended

## 🔧 Quick Start

### Local Development Setup

```bash
# Clone the repository
git clone https://github.com/architex-lava/archiconnect-app.git
cd archiconnect-app

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Create database and run migrations
php artisan migrate
php artisan db:seed

# Build assets and start development server
npm run dev
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## 🌐 Production Deployment

### Automated Deployment to ai.architex.co.za

We provide comprehensive deployment automation for both Windows and Linux environments:

#### Windows (PowerShell)
```powershell
# Run deployment preparation
.\prepare-deployment.ps1

# With options
.\prepare-deployment.ps1 -SkipTests -Force
```

#### Linux/macOS (Bash)
```bash
# Make script executable
chmod +x prepare-deployment.sh

# Run deployment preparation
./prepare-deployment.sh

# With options
./prepare-deployment.sh --skip-tests --force
```

### Deployment Features
- ✅ **100% Automated**: Complete cPanel deployment via `.cpanel.yml`
- ✅ **Environment Isolation**: Local SQLite for preparation, MySQL for production
- ✅ **Security Optimized**: Comprehensive `.htaccess` with security headers
- ✅ **Performance Tuned**: GZIP compression, browser caching, asset optimization
- ✅ **Error Handling**: Comprehensive backup and rollback mechanisms
- ✅ **Cross-Platform**: Support for Windows PowerShell and Linux/macOS Bash

### Deployment Process
1. **Preparation**: Run `prepare-deployment.ps1` or `prepare-deployment.sh`
2. **Validation**: Automated checks for dependencies, tests, and configuration
3. **Git Push**: Push changes to your cPanel Git repository
4. **Auto-Deploy**: cPanel automatically deploys via `.cpanel.yml` configuration
5. **Verification**: Visit `https://ai.architex.co.za/verify-deployment.php`

## 🧪 Testing

### Running Tests
```bash
# Run all tests
vendor/bin/pest

# Run with parallel execution
vendor/bin/pest --parallel

# Run specific test suite
vendor/bin/pest tests/Feature
vendor/bin/pest tests/Unit

# Generate coverage report
vendor/bin/pest --coverage
```

### Test Configuration
- **Framework**: Pest PHP (with PHPUnit fallback)
- **Database**: SQLite in-memory for fast testing
- **Environment**: Isolated testing environment
- **Coverage**: Comprehensive test coverage reporting

## 🔐 Environment Configuration

### Local Development (.env)
```env
APP_NAME="ArchiConnect AI"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### Production (.env.production)
```env
APP_NAME="ArchiConnect AI"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ai.architex.co.za

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=architex_ai
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 📁 Project Structure

```
archiconnect-app/
├── 🚀 Deployment
│   ├── .cpanel.yml                 # cPanel deployment automation
│   ├── prepare-deployment.ps1      # Windows deployment preparation
│   ├── prepare-deployment.sh       # Linux/macOS deployment preparation
│   ├── .htaccess.production       # Root web server config
│   └── public/.htaccess.production # Laravel routing config
├── 🏗️ Application
│   ├── app/                       # Laravel application logic
│   ├── config/                    # Configuration files
│   ├── database/                  # Migrations, seeders, SQLite DBs
│   ├── public/                    # Web accessible files
│   ├── resources/                 # Views, assets, language files
│   ├── routes/                    # Application routes
│   └── storage/                   # Logs, cache, uploads
├── 🧪 Testing
│   ├── tests/Feature/             # Feature tests
│   ├── tests/Unit/                # Unit tests
│   └── phpunit.xml                # Test configuration
└── 🛠️ Build Tools
    ├── package.json               # Node.js dependencies
    ├── vite.config.js             # Vite build configuration
    ├── tailwind.config.js         # TailwindCSS configuration
    └── composer.json              # PHP dependencies
```

## 🤖 AI Features

### Intelligent Project Management
- **Smart Scheduling**: AI-powered project timeline optimization
- **Resource Allocation**: Automatic freelancer assignment based on skills and availability
- **Risk Prediction**: Early warning system for project delays and budget overruns
- **Quality Assurance**: Automated review of deliverables and compliance checking

### Advanced Analytics
- **Performance Insights**: Real-time dashboard with predictive analytics
- **Cost Optimization**: AI-driven budget recommendations
- **Time Tracking Intelligence**: Automatic categorization and efficiency scoring
- **Client Satisfaction Prediction**: Proactive relationship management

## 🔒 Security Features

- **🛡️ Security Headers**: CSP, HSTS, X-Frame-Options, XSS Protection
- **🔐 Authentication**: Multi-factor authentication with Laravel Breeze
- **🚫 Input Validation**: Comprehensive form validation and sanitization
- **📝 Audit Logging**: Complete activity tracking and compliance reporting
- **🔒 Data Encryption**: End-to-end encryption for sensitive data
- **🚨 Intrusion Detection**: Real-time monitoring and threat detection

## 📊 Performance Optimization

- **⚡ Asset Optimization**: Minification, compression, and caching
- **🗂️ Database Optimization**: Query optimization and intelligent indexing
- **🚀 CDN Integration**: Global content delivery for static assets
- **📱 Progressive Web App**: Offline capabilities and mobile optimization
- **🔄 Caching Strategy**: Multi-layer caching (Redis, File, Database)

## 🐛 Troubleshooting

### Common Issues

**Database Connection Errors**
```bash
# Check database status
php artisan migrate:status

# Reset database (development only)
php artisan migrate:fresh --seed
```

**Asset Build Failures**
```bash
# Clear build cache
npm run clean

# Rebuild assets
npm run build
```

**Windows Permission Issues (EPERM errors)**
```powershell
# Stop Node.js processes
Get-Process | Where-Object {$_.ProcessName -match "node|npm|vite|esbuild"} | Stop-Process -Force

# Clean install
npm cache clean --force
Remove-Item node_modules -Recurse -Force
Remove-Item package-lock.json -Force
npm install --no-audit --no-fund --legacy-peer-deps

# Build with npx
npx vite build
```

**Permission Issues (Linux/macOS)**
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Antivirus Blocking npm Files**
- Temporarily disable Windows Defender real-time protection
- Add your project directory to antivirus exclusions
- Run terminal as Administrator

## 📚 Documentation

- **[Deployment Guide](CPANEL-DEPLOYMENT.md)**: Comprehensive deployment instructions
- **[AI Integration](AI-DEPLOYMENT-README.md)**: AI features and configuration
- **[User Testing Guide](USER_TESTING_GUIDE.md)**: Testing procedures and scenarios
- **[Vite CSP Configuration](VITE-CSP-FIX.md)**: Content Security Policy setup

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

### Development Workflow
- Follow PSR-12 coding standards
- Write comprehensive tests for new features
- Update documentation for API changes
- Use conventional commit messages

## 📄 License

**Proprietary Software** - All rights reserved.

© 2025 Architex Technologies. The ArchiConnect AI Platform is proprietary software developed for architectural project management. Unauthorized copying, modification, or distribution is strictly prohibited.

## 🆘 Support

- **Documentation**: Check the `/docs` directory
- **Issues**: Report bugs via GitHub Issues
- **Email**: support@architex.co.za
- **Emergency**: +27 (0)11 123-4567

## 🔄 Version Information

- **Current Version**: 2.1.0
- **Laravel**: 12.x
- **PHP**: 8.4+
- **Node.js**: 20 LTS
- **Last Updated**: May 26, 2025

---

<p align="center">
Built with ❤️ by the <strong>Architex Technologies</strong> team<br>
<a href="https://ai.architex.co.za">🌐 Visit ArchiConnect AI</a>
</p>
