# Electronics store

## Introduction

This application uses Laravel 13, Livewire 4, Tailwind CSS 4, Fortify, and the [Flux UI](https://fluxui.dev) component library.

## Project structure

- See the [agent guide](AGENTS.md), [architecture and application flows](docs/architecture.md), and [development and validation](docs/validation.md).
- `app/Http/Controllers`, `app/Models`, and `app/Actions` contain the application's PHP code.
- `resources/views/pages` contains full-page Livewire components and authentication views.
- `resources/views/components` contains reusable Blade components and layouts.
- `resources/views` also contains the storefront's Blade pages.
- `resources/css` and `resources/js` contain the Vite entry points.

The Livewire page routes are declared with `Route::livewire` in `routes/settings.php`. Authentication views are selected in `App\Providers\FortifyServiceProvider`.

The obsolete Volt provider was removed because the application uses Livewire 4 page components and does not depend on `livewire/volt`.

## Official Documentation

- [Laravel 13](https://laravel.com/docs/13.x)
- [Livewire 4](https://livewire.laravel.com/docs/4.x)
- [Tailwind CSS 4](https://tailwindcss.com/docs)

## Deprecation warnings

Laravel follows its standard default and discards deprecation logs when `LOG_DEPRECATIONS_CHANNEL=null`. To see them locally, set `LOG_DEPRECATIONS_CHANNEL=single` in `.env`; in container deployments, `stderr` is also suitable. The test environment sends Laravel deprecation logs to `stderr`.

## Passkeys and frontend tooling

Passkey sign-in is optional; password sign-in and two-factor authentication remain available. Users can register and remove passkeys from **Settings → Security**. WebAuthn requires HTTPS, except on `localhost`; set `APP_URL` to the exact origin users visit, and keep `APP_KEY` stable because it is used to derive passkey user handles.

### Upgrading application identifiers

Application entities, pivots, and passkeys use UUIDv7 identifiers. The migrations replace older identifiers, including numeric passkey IDs, and update application relationships; existing UUIDv7 IDs remain unchanged. Laravel's internal queue, session, and migration IDs retain their framework formats.

Before deploying the migrations:

1. Back up the database and verify that it can be restored. Keep `APP_KEY` and any `PASSKEYS_USER_HANDLE_SECRET` unchanged.
2. Process or replace queued jobs containing old IDs, and plan to update existing links and external references containing changed IDs.
3. Stop all application instances and queue workers, and run migrations in maintenance mode. Conversion of existing IDs supports MySQL and SQLite; other database drivers fail if conversion is needed. MySQL temporarily disables foreign-key checks, so concurrent writes are unsafe.

For migrated users with passkeys, the migration preserves the existing WebAuthn user handle from a stored credential. Invalid or missing credential handles cause the migration to fail rather than silently replacing them. Database sessions belonging to users whose IDs change are deleted, so those users must sign in again when using database sessions.

The identifier conversion is irreversible: restore the backup to recover old IDs. Rollback refuses to remove preserved passkey user handles while any remain. If a migration fails, investigate and correct the cause before retrying; the identifier updates are transactional, although schema changes may already have been applied.

The frontend uses Vite Plus and pnpm 12.21.0. Install dependencies with `pnpm install --frozen-lockfile`, then run `pnpm run dev` or `pnpm run build` with Node `^22.18.0`, `^24.11.0`, or `>=26.0.0`; Docker builds use Node 26. New dependencies must have been published at least 24 hours earlier.

## Running with Docker

Docker Compose runs the Laravel application with Apache and a persistent MySQL database.

**Existing MySQL 8.4 installations:** back up the database before upgrading. MySQL 26.10 cannot be assumed to upgrade an existing 8.4 data directory directly. Follow the [MySQL upgrade documentation](https://dev.mysql.com/doc/refman/en/upgrade-paths.html), or restore a logical backup into a new MySQL 26.10 volume before starting this configuration. Do not remove the existing `db_data` volume without verifying the backup.

1. Copy `.env.example` to `.env` if you do not already have a local `.env`.
2. Generate an application key:

   ```powershell
   docker compose run --rm app php artisan key:generate --show
   ```

   Put the displayed value in `.env` as `APP_KEY=...`.
3. Build and start the containers:

   ```powershell
   docker compose up --build -d
   ```

The application is available at <http://localhost:8080>. Database migrations and seeders run when the application container starts. Use `docker compose down` to stop the services; the MySQL data remains in the `db_data` volume.

The storefront and open-order lookup use composite database indexes. Existing installations must run the new migrations (`php artisan migrate --force`); Docker Compose runs them automatically on startup.

Run the test suite in its own PHP container with:

```powershell
docker compose --profile test run --build --rm test
```

The CI workflow also runs migrations, seeding, rollback, and the test suite against MySQL 26.10. These checks use a disposable database, not the existing `db_data` volume.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for project-specific contribution and validation guidance.

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## License

The Laravel + Livewire starter kit is open-sourced software licensed under the MIT license.
