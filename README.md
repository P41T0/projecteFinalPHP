# Botiga electrònica

## Introduction

This application uses Laravel 13, Livewire 4, Tailwind CSS 4, Fortify, and the [Flux UI](https://fluxui.dev) component library.

## Project structure

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

The frontend uses Vite Plus. Run `npm run dev` or `npm run build` with Node `^22.18.0`, `^24.11.0`, or `>=26.0.0`; Docker builds use Node 24.

## Running with Docker

Docker Compose runs the Laravel application with Apache and a persistent MySQL database.

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

Run the test suite in its own PHP container with:

```powershell
docker compose --profile test run --build --rm test
```

## Contributing

Thank you for considering contributing to our starter kit! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## License

The Laravel + Livewire starter kit is open-sourced software licensed under the MIT license.
