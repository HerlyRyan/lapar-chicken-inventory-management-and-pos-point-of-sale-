# 🍗 LaparChicken Inventory & Sales System

[![Laravel](https://img.shields.io/badge/Laravel-9.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)

Sistem Manajemen Inventori dan Penjualan Multi-Cabang untuk LaparChicken Restaurant Chain.

## 🚀 Fitur Utama

### 📊 **Multi-Branch Management**
- ✅ Manajemen inventori terpisah per cabang
- ✅ Transfer stok antar cabang
- ✅ Dashboard admin dengan pemilihan cabang
- ✅ Role-based access control

### 🍽️ **Inventory Management**
- ✅ **Raw Materials**: Manajemen terpusat dari pusat produksi
- ✅ **Semi-Finished Products**: Stok per cabang dengan tracking
- ✅ **Finished Products**: Manajemen stok per cabang
- ✅ Stock movement tracking dan audit trail

### 🎯 **Sales & POS**
- ✅ Point of Sale (POS) system
- ✅ Sales reporting per cabang
- ✅ Invoice generation
- ✅ Customer management

### 👥 **User Management**
- ✅ Multi-role system (Super Admin, Manager, Kasir, Staff)
- ✅ Branch-specific user assignment
- ✅ Permission-based access control

## 🛠️ Installation

### Prerequisites
- PHP 8.1 atau lebih tinggi
- Composer
- MySQL 8.0+
- Node.js & NPM (untuk asset compilation)

### Quick Start

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd laparchicken_inventory_and_sales
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laparchicken_db
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed --class=BranchSeeder
   ```

6. **Start Development Server**
   ```bash
   # Windows
   scripts\setup\start-server.bat
   
   # Linux/Mac
   chmod +x scripts/setup/start-server.sh
   ./scripts/setup/start-server.sh
   ```

7. **Access Application**
   - URL: http://localhost:8000
   - Development Auto-Login: http://localhost:8000/dev/auto-login

## 📁 Project Structure

```
laparchicken_inventory_and_sales/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/           # Admin-specific controllers
│   │   ├── Api/            # API controllers
│   │   └── Dev/            # Development-only controllers
│   ├── Models/             # Eloquent models
│   └── Services/           # Business logic services
├── database/
│   ├── migrations/         # Database migrations
│   ├── seeders/           # Database seeders
│   └── schema/            # Database schema dumps
├── resources/
│   └── views/
│       ├── dashboard/      # Dashboard views
│       ├── layouts/        # Layout templates
│       └── testing/        # Testing views (dev only)
├── scripts/
│   ├── database/          # Database utility scripts
│   └── setup/             # Setup and startup scripts
├── docs/                  # Project documentation
└── storage/
```

## 🗄️ Database Architecture

### Core Tables
- `branches` - Branch master data
- `users` - User accounts with branch assignment
- `materials` - Raw materials (centralized)
- `semi_finished_products` - Semi-finished products master
- `finished_products` - Finished products master

### Multi-Branch Stock Tables
- `branch_stocks` - Finished product stock per branch
- `semi_finished_branch_stocks` - Semi-finished product stock per branch
- `stock_movements` - All stock movement history

## 🔐 User Roles & Permissions

| Role | Access Level | Capabilities |
|------|-------------|-------------|
| **Super Admin** | All branches | Full system access, branch selection |
| **Manager Cabang** | Single branch | Branch management, reports |
| **Kasir** | Single branch | POS, sales, basic inventory view |
| **Staff Gudang** | Single branch | Inventory management, stock movements |

## 🧪 Development & Testing

### Development Mode
```bash
# Auto-login as Super Admin (development only)
http://localhost:8000/dev/auto-login

# Access test dashboard
http://localhost:8000/testing/dashboard
```

### Database Scripts
```bash
# Run database utilities
php scripts/database/create_sample_data.php
php scripts/database/check_table_structure.php
```

## 🚦 API Endpoints

### Stock Management
- `GET /api/finished-products` - Get finished products list
- `GET /api/semi-finished-products` - Get semi-finished products list
- `GET /api/stock/{itemType}/{itemId}/branch/{branchId}` - Check stock
- `POST /api/stock-transfer` - Transfer stock between branches

### Branch Management
- `GET /api/branches/{branch}/inventory-summary` - Branch inventory summary
- `POST /api/branches/transfer-stock` - Inter-branch stock transfer

## 📊 Multi-Branch System Logic

### Inventory Strategies

1. **Raw Materials** 🥕
   - **Strategy**: Centralized management
   - **Stock Location**: Central production facility
   - **Distribution**: Via distribution system to branches

2. **Semi-Finished Products** 🍗
   - **Strategy**: Per-branch stock management
   - **Stock Location**: Individual branch storage
   - **Transfers**: Enabled between branches

3. **Finished Products** 🍽️
   - **Strategy**: Per-branch stock management
   - **Stock Location**: Individual branch inventory
   - **Sales**: Deducted from branch stock

### Branch Context System
The system automatically filters data based on user's branch assignment:
- **Branch Staff**: See only their branch data
- **Super Admin**: Can select and view any branch
- **Manager**: Access to their assigned branch

## 🛡️ Security Features

- ✅ Role-based access control (RBAC)
- ✅ Branch-level data isolation
- ✅ Audit trail for all stock movements
- ✅ Session-based branch selection for admins
- ✅ Input validation and sanitization

## 📈 Reporting & Analytics

- Sales reports per branch and period
- Inventory level monitoring
- Low stock alerts
- Stock movement history
- Inter-branch transfer tracking

## 🔧 Maintenance

### Regular Tasks
- Database backup (recommended daily)
- Log file rotation
- Cache clearing: `php artisan cache:clear`
- View cache refresh: `php artisan view:clear`

### Monitoring
- Check disk space for storage/logs
- Monitor database performance
- Review stock movement patterns

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 🧭 Global Rules (project guidance for AI / Copilot)
Please follow these rules whenever you open the project or use AI assistants (Copilot, Chat):

1. Database Safety (Always On, applies to `database/**` and `migrations/**`)
   - Do not perform any operation that deletes the entire database without explicit approval.
   - Do not use `DROP` or `TRUNCATE` in code or migrations without prior approval.
   - For intentional destructive changes, add an entry to `.githooks/allow-sql-approval.txt` with a short justification.

2. No Duplication (Always On, applies to `app/**` and `src/**`)
   - Do not create new logic that duplicates existing implementation. Reuse helpers in `app/Helpers` or services in `app/Services`.

Local enforcement: enable the local git hooks to scan for `DROP|TRUNCATE` and use the PR CI check to block unsafe changes. See `.githooks/` and `.github/workflows/db-safety.yml`.

## 📝 License

This project is proprietary software developed for LaparChicken Restaurant Chain.

## 📞 Support

For technical support or questions:
- Email: tech-support@laparchicken.com
- Internal Documentation: `docs/` folder

---

**© 2025 LaparChicken Restaurant Chain. All rights reserved.**
