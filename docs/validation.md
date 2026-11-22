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

# Clear configuration, check formatting, and run the complete suite
composer test

# Build frontend assets
pnpm run build
```

`composer lint` applies Pint and may modify files; `composer test:lint` only checks formatting. Feature tests use Pest and `RefreshDatabase` through `tests/Pest.php`. By default, `phpunit.xml` configures in-memory SQLite, array session and cache stores, array mail, and a synchronous queue. Do not assume an SQLite test validates every MySQL behavior.

Run the full Pint check in Docker with:

```powershell
docker compose --profile test run --build --rm test composer test:lint
```

The lint workflow runs the same non-mutating check on pushes and pull requests targeting `develop` or `main`; formatting violations fail the job.

### Test starting points

| Change | Starting point |
| --- | --- |
| Catalog, administration, inventory, or cart | `tests/Feature/StoreEndpointsTest.php`: validation, permissions, ownership, data, and queries. |
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
