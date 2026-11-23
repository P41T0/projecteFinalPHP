# Documentation Index

Complete reference for the Electronics Store project.

## Core Documentation

### Project Overview
- **[README.md](README.md)** - Project introduction, setup, and running with Docker
- **[CONTRIBUTING.md](CONTRIBUTING.md)** - Contribution guidelines and code quality requirements

### Architecture & Development
- **[docs/architecture.md](docs/architecture.md)** - Application architecture, data flows, and security implementation
- **[docs/validation.md](docs/validation.md)** - Testing and validation procedures
- **[docs/formatting.md](docs/formatting.md)** - Code formatting with Pint and Prettier

### Security & Quality
- **[SECURITY.md](SECURITY.md)** - Security policy and vulnerability reporting
- **[docs/static-analysis.md](docs/static-analysis.md)** - Security analysis tools and GitHub workflows
- **[SECURITY_FIX_REPORT.md](SECURITY_FIX_REPORT.md)** - Detailed security vulnerabilities fixed

## Security Testing

### Test Suites
- **[tests/Security/StaticAnalysisTest.php](tests/Security/StaticAnalysisTest.php)** - 13 comprehensive security tests
- **[tests/Feature/FormattingTest.php](tests/Feature/FormattingTest.php)** - Code formatting validation tests

### Security Reports
- **[SECURITY_TESTS_MAPPING.md](SECURITY_TESTS_MAPPING.md)** - Detailed test-to-vulnerability mapping
- **[VULNERABLE_EXAMPLES_TEST_RESULTS.md](VULNERABLE_EXAMPLES_TEST_RESULTS.md)** - Security test detection results

### Code Examples
- **[app/Examples/VulnerableCodeExamples.php](app/Examples/VulnerableCodeExamples.php)** - Intentionally vulnerable code examples for test validation

## Code Formatting

### Configuration
- **[.prettierrc.json](.prettierrc.json)** - Prettier configuration (JavaScript/CSS)
- **[.prettierignore](.prettierignore)** - Prettier exclusions
- **[pint.json](pint.json)** - Pint configuration (PHP)

### Setup Guides
- **[PRETTIER_SETUP_SUMMARY.md](PRETTIER_SETUP_SUMMARY.md)** - Prettier integration with Pint

## Repository Map

| Path | Responsibility |
|------|---|
| `app/Http/Controllers/` | HTTP logic for catalog, orders, stores, products, sections |
| `app/Http/Middleware/` | Session locale, administrator checks, security headers |
| `app/Models/` | Eloquent models, relationships, UUID identifiers |
| `app/Actions/Fortify/` | Authentication, password, passkey registration |
| `app/Concerns/` | Shared validation rules |
| `app/Examples/` | Vulnerable code examples (for security testing only) |
| `resources/views/` | Blade storefront and admin views |
| `resources/views/pages/` | Full-page Livewire components |
| `resources/views/components/` | Reusable Blade components |
| `resources/css/` | Tailwind CSS configuration |
| `resources/js/` | Frontend entry points and passkeys integration |
| `routes/` | Web routes, API routes, settings pages |
| `database/migrations/` | Schema and indexes |
| `database/seeders/` | Demonstration data |
| `tests/Feature/` | Pest feature tests |
| `tests/Security/` | Security analysis tests |
| `tests/Unit/` | Pest unit tests |
| `docs/` | Additional documentation |
| `.github/workflows/` | CI/CD automation (security, tests, formatting) |

## Quick Start Commands

### Setup
```bash
# Local setup (PHP + Node.js required)
composer setup

# Docker setup
docker compose up --build -d
```

### Development
```bash
# Run development server
composer dev

# Format code
composer lint:fix && pnpm run format

# Run all tests
composer test
```

### Testing
```bash
# Run all tests (including security and formatting)
docker compose --profile test run --build --rm test

# Security tests only
./vendor/bin/pest tests/Security/StaticAnalysisTest.php

# Formatting check only
composer test:format

# Individual commands
composer test:lint        # PHP formatting check
pnpm run format:check     # JavaScript/CSS formatting check
```

### Deployment
```bash
# Build frontend
pnpm run build

# Run migrations
php artisan migrate --force

# Clear cache
php artisan config:clear
```

## Testing & Quality

### Test Coverage

- **13 Security Tests** - Comprehensive vulnerability detection
- **Formatting Tests** - PHP (Pint) and JavaScript/CSS (Prettier)
- **Feature Tests** - User flows and business logic
- **Unit Tests** - Individual component testing

### CI/CD Workflows

Located in `.github/workflows/`:
- **static-analysis.yml** - PHPStan, Semgrep, Pint, Prettier
- **codeql.yml** - GitHub CodeQL security analysis
- **tests.yml** - Full test suite on PHP 8.5 and 8.6
- **lint.yml** - Code formatting checks

## Key Technologies

| Component | Technology | Version |
|-----------|-----------|---------|
| Backend | Laravel | 13.x |
| Frontend | Livewire | 4.x |
| Styling | Tailwind CSS | 4.x |
| Components | Flux UI | 2.x |
| Database | MySQL | 26.10 |
| PHP | PHP | 8.5+ |
| Node | Node.js | 22.18.0, 24.11.0, 26+ |
| Package Manager | pnpm | 12.21.0 |
| Authentication | Fortify | 1.43+ |
| Passkeys | Laravel Passkeys | 0.3.0+ |

## Security Features

✅ **Implemented:**
- HTTP security headers (HSTS, CSP, X-Frame-Options, etc.)
- Mass assignment protection via `$fillable`
- Authorization checks (admin middleware, ownership verification)
- Input validation on all controllers
- SQL injection prevention via parameterized queries
- CSRF protection via Livewire and @csrf tokens
- Passkey/WebAuthn authentication
- Two-factor authentication via Fortify
- Security tests (13 comprehensive checks)

📋 **In Development/Consideration:**
- Payment processing (not yet implemented)
- Inventory reservation on order confirmation
- Stock deduction logic
- File upload sanitization (original files retained as-is)

## Important Notes

⚠️ **Never**
- Run `composer setup` against production or shared databases
- Commit credentials or `.env` files
- Deploy the vulnerable code examples file to production
- Disable security headers or authorization checks

🔐 **Security**
- Keep `APP_KEY` stable (used for encryption and passkeys)
- Use HTTPS in production (required for WebAuthn)
- Run security tests before deploying changes
- Review all code changes for security implications

📱 **Deployment**
- Configure persistent storage for product image uploads
- Back up database before major version upgrades
- Test migrations against disposable databases first
- Keep dependencies updated

## Support & Reporting

- **Issues**: Use GitHub issues for bug reports
- **Security**: See [SECURITY.md](SECURITY.md) for vulnerability reporting
- **Contributions**: Follow [CONTRIBUTING.md](CONTRIBUTING.md)

## Related Documents

- [Architecture Guide](docs/architecture.md) - Detailed application flows
- [Validation Guide](docs/validation.md) - Testing procedures
- [Formatting Guide](docs/formatting.md) - Code quality tools
- [Static Analysis Guide](docs/static-analysis.md) - Security testing tools
- [Agent Guide](AGENTS.md) - AI assistant integration

---

**Last Updated:** November 2024
**Project Status:** Active Development
**Security Tests:** 13/13 Passing
