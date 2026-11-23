# Development and validation

See the [README](../README.md) for requirements, installation, and Docker. The commands below assume dependencies are installed and a suitable test environment is available.

## Development

```powershell
composer dev
```

This starts the Laravel server, queue listener, and Vite. For frontend development alone, use `pnpm run dev`; to build assets, use `pnpm run build`. `composer setup` also generates `APP_KEY` and runs migrations: read the precautions in [AGENTS.md](../AGENTS.md) before using it on an existing installation.

## Checks

```powershell
# One test file
php artisan test tests/Feature/StoreEndpointsTest.php

# Check PHP formatting without changing files
composer test:lint

# Check all formatting (PHP + JavaScript/CSS)
composer test:format

# Security analysis tests
./vendor/bin/pest tests/Security/StaticAnalysisTest.php

# Clear configuration, check formatting, and run the complete suite
composer test

# Build frontend assets
pnpm run build
```

`composer lint` applies Pint and may modify files; `composer test:lint` only checks formatting. Feature tests use Pest and `RefreshDatabase` through `tests/Pest.php`. By default, `phpunit.xml` configures in-memory SQLite, array session and cache stores, array mail, and a synchronous queue. Do not assume an SQLite test validates every MySQL behavior.

### Code Quality Tests

#### Formatting Tests

Run all formatting checks (Pint for PHP, Prettier for JavaScript/CSS):

```powershell
composer test:format
```

Or individually:
```powershell
# PHP formatting check
composer test:lint

# JavaScript/CSS formatting check
pnpm run format:check

# Fix formatting
composer lint:fix && pnpm run format
```

See [Formatting Documentation](../docs/formatting.md) for detailed configuration and usage.

#### Security Analysis Tests

The application includes 13 comprehensive security tests covering common PHP/Laravel vulnerabilities:

```powershell
./vendor/bin/pest tests/Security/StaticAnalysisTest.php
```

Security tests verify:
1. No dangerous functions (eval, exec, system, etc.)
2. No weak random functions used for security
3. No SQL injection patterns
4. Models have proper mass assignment protection
5. No unescaped output in views
6. Controllers check authorization
7. No sensitive data logging
8. Input validation on all controllers
9. No hardcoded credentials
10. No path traversal vulnerabilities
11. CSRF protection on forms
12. Security headers configured
13. Database queries use parameterized binding

See [Security Analysis Guide](../docs/static-analysis.md) and [Security Test Mapping](../SECURITY_TESTS_MAPPING.md) for detailed information.

#### Formatting Test File

Location: `tests/Feature/FormattingTest.php`

Tests:
- `test_php_code_passes_pint()` - Verifies PHP code passes Pint linting
- `test_js_css_code_passes_prettier()` - Verifies JavaScript/CSS passes Prettier
- `test_pint_and_prettier_do_not_conflict()` - Validates configuration consistency

Run the full Pint check in Docker with:

```powershell
docker compose --profile test run --build --rm test composer test:lint
```

The lint workflow runs the same non-mutating check on pushes and pull requests targeting `develop` or `main`; formatting violations fail the job.

### Test starting points

| Change | Starting point |
| --- | --- |
| Catalog, administration, inventory, or cart | `tests/Feature/StoreEndpointsTest.php`: validation, permissions, ownership, data, and queries. |
| Product image uploads | `tests/Feature/StoreEndpointsTest.php`: rejected disguised and malformed files, size and dimension limits, supported formats, and generated storage names. |
| Locale or error pages | `tests/Feature/LocalizationTest.php`, `tests/Feature/ErrorPagesTest.php`. |
| Login, registration, recovery, or two-factor authentication | `tests/Feature/Auth/` and `tests/Feature/Settings/TwoFactorAuthenticationTest.php`. |
| Profile, appearance, password, or passkeys | `tests/Feature/Settings/`. |
| UUIDs or passkey migration | `tests/Feature/UuidIdsTest.php`, `tests/Feature/PasskeyIdMigrationTest.php`; run these on both SQLite and a disposable MySQL database. |

For tests in a disposable container, run `docker compose --profile test run --build --rm test`. CI in `.github/workflows/tests.yml` runs Pest on PHP 8.5 and 8.6 and checks migrations, seeding, rollback, and tests with a disposable MySQL service. The browser workflow only runs when its token is available and uses an external suite; do not assume browser tests are available locally.

For focused SQLite validation of identifier migration and passkey authentication in Docker, run:

```powershell
docker compose --profile test run --build --rm test php artisan test tests/Feature/UuidIdsTest.php tests/Feature/PasskeyIdMigrationTest.php
```

These tests cover relationship updates, unchanged UUIDv7 IDs, database-session invalidation, retry after a simulated failure, numeric passkey conversion, and authentication with a preserved WebAuthn handle. The Compose test service uses in-memory SQLite; also run these tests against a disposable MySQL database when changing the migrations, since foreign-key handling differs.

Do not run `migrate:fresh`, rollback, seeders, or other potentially destructive operations against shared or production databases. Compose runs migrations and the seeder when the application starts; the seeder skips all demonstration data if any table it checks already contains data.
