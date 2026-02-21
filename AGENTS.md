# AGENTS.md - La Vitrina Web

## Project Overview

This is a PHP-based contract management system called "La Vitrina". It follows an MVC architecture with:
- **Controllers**: `src/Controllers/`
- **Models**: `src/Models/`
- **Views/Templates**: `templates/`
- **Public entry point**: `public/index.php`
- **Database**: MySQL with stored procedures

## Build/Lint/Test Commands

This project does **not** have a built-in test suite or linting tools configured.

### PHP Syntax Check
```bash
# Check PHP syntax for all PHP files
php -l src/Controllers/*.php
php -l src/Models/*.php
php -l public/index.php
```

### Composer
```bash
# Install dependencies
composer install

# Dump autoloader
composer dump-autoload
```

### Database
Reference the SQL files in `database/` folder for table schemas and stored procedures. Key files:
- `database/CREATE TABLE CONTRATO_TIPO_PUESTO_.txt` - Contains table definitions and SPs

## Code Style Guidelines

### PHP Standards
- Use **PSR-4** autoloading (namespace `Vitrina\`)
- PHP version: **>=7.4**
- Required extensions: `ext-pdo`

### Naming Conventions
- **Classes**: PascalCase (e.g., `MantenimientoController`, `PuestoComercial`)
- **Methods**: camelCase (e.g., `getPaginatedRecords`, `arrendatarioGuardar`)
- **Variables**: camelCase (e.g., `$filtroEstado`, `$records_per_page`)
- **Constants**: UPPER_CASE with underscores
- **Database tables**: Use exact names from SQL files (e.g., `CONTRATO_PUESTO_COMERCIAL`)
- **Foreign keys**: Follow the desktop app conventions (e.g., `id_tipo_puesto_comercial`, `sucursalid`)

### File Structure
```
src/
├── Controllers/   # HTTP request handlers
├── Models/        # Database operations
├── Lib/           # Utilities (Globales.php)
└── bootstrap.php  # Application bootstrap

public/
└── index.php      # Entry point with routing

templates/
├── partials/      # Reusable components (navbar)
├── mantenimiento/ # CRUD maintenance pages
└── contratos/     # Contract management pages
```

### Database Operations
- Use **PDO** with prepared statements to prevent SQL injection
- Always bind parameters explicitly (never interpolate strings into SQL)
- Use `Globales::$o_id_empresa` for company context in queries
- Reference stored procedures from the desktop app (C#) as the source of truth for business logic

### Error Handling
- Use `try/catch` blocks for database operations
- Log errors with `error_log()` including the method name and exception message
- Throw meaningful exceptions with user-friendly messages
- Return empty arrays `[]` or `null` on errors rather than crashing

### Controller Guidelines
- Check authentication with `$_SESSION['user_id']`
- Use `header('Location: ...')` for redirects
- Exit after redirects: `exit;`
- Set flash messages in `$_SESSION['success']` or `$_SESSION['error']`
- Accept form data via `$_POST` with proper casting: `(int)$_POST['id']`

### Model Guidelines
- Constructor connects to database: `$this->pdo = Database::connect()`
- Return types should be declared when possible
- Use `PDO::FETCH_ASSOC` for query results
- Keep methods focused on single responsibility

### Template/View Guidelines
- Use short PHP tags `<?php ?>` for embedding
- Always escape output with `htmlspecialchars()`
- Use Tailwind CSS (CDN) for styling
- Use Alpine.js for client-side interactivity
- Use Font Awesome for icons
- Avoid inline styles; use utility classes

### Frontend Development (from frontend-design skill)
When creating/modifying UI components:
- Choose distinctive, production-grade aesthetics
- Avoid generic "AI slop" aesthetics
- Use creative typography, color schemes, and animations
- Focus on usability and user experience
- Mobile-first responsive design

### Important Patterns

#### Routing (public/index.php)
```php
case '/route/path':
    $controller = new ControllerName();
    $controller->method();
    break;
```

#### Modal Pattern
- Use plain JavaScript (not Alpine.js) for modal open/close
- Set `display: flex` after removing `hidden` class
- Pass IDs as strings in onclick handlers: `String(value)`

#### API Endpoints
- Return JSON with `header('Content-Type: application/json')`
- Use `echo json_encode($data)`
- Check authentication before processing

#### Dropdown/Cascading Selectors
- Load initial data in controller
- Use JavaScript fetch for dependent options
- Convert values to strings when setting select values

## Key Database References

### Important Tables
- `CONTRATO_PUESTO_COMERCIAL` - Commercial premises
- `CONTRATO_ARRENDATARIO` - Tenants/lessees
- `CONTRATO_ARRENDADOR` - Landlords/lessors
- `CONTRATO_TIPO_PUESTO_COMERCIAL` - Types of commercial premises
- `CONTRATO_TIPO_CONTRATO_MOD` - Contract types
- `sucursal` - Branches/locations
- `TIPODOCIDENTIDAD` - Identity document types
- `ubdepartamento`, `ubprovincia`, `ubdistrito` - Geographic locations

### Key Stored Procedures
- `USP_PuestoComercial_Listar` - List commercial premises
- `USP_Listar_Contrato_Arrendatarios` - List tenants
- `USP_PuestoComercial_ListarParaEdicion_Hoy` - Active premises for contracts
- `USP_Listar_Arrendatarios_Busqueda` - Search tenants

## Common Tasks

### Adding a New Maintenance CRUD
1. Create model in `src/Models/`
2. Add controller methods in `MantenimientoController.php`
3. Create template in `templates/mantenimiento/`
4. Add routes in `public/index.php`
5. Add navigation link in `templates/partials/navbar.php`

### Adding a New API Endpoint
1. Add route in `public/index.php`
2. Add method in appropriate controller
3. Return JSON response

### Modifying a Modal Form
1. Update controller to pass necessary dropdown data
2. Modify form fields in template
3. Update JavaScript functions for open/edit
4. Update controller save method to accept new fields

## Notes for Agents

- This project is a web modernization of a desktop C# application
- Reference the C# code in `Sistema de Ventas la Vitrina/` for business logic
- The desktop app uses specific stored procedures - use these as the source of truth
- Always use prepared statements for SQL queries
- Test changes manually - there are no automated tests
