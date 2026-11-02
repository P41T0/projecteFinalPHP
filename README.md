# Laravel + Livewire Starter Kit

## Introduction

Our Laravel + [Livewire](https://livewire.laravel.com) starter kit provides a robust, modern starting point for building Laravel applications with a Livewire frontend.

Livewire is a powerful way of building dynamic, reactive, frontend UIs using just PHP. It's a great fit for teams that primarily use Blade templates and are looking for a simpler alternative to JavaScript-driven SPA frameworks like React and Vue.

This Livewire starter kit utilizes Livewire 3, Laravel Volt (optionally), TypeScript, Tailwind, and the [Flux UI](https://fluxui.dev) component library.

If you are looking for the alternate configurations of this starter kit, they can be found in the following branches:

- [components](https://github.com/laravel/livewire-starter-kit/tree/components) - if Volt is not selected
- [workos](https://github.com/laravel/livewire-starter-kit/tree/workos) - if WorkOS is selected for authentication

## Official Documentation

Documentation for all Laravel starter kits can be found on the [Laravel website](https://laravel.com/docs/starter-kits).

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

The application is available at <http://localhost:8080>. Database migrations run when the application container starts. Use `docker compose down` to stop the services; the MySQL data remains in the `db_data` volume.

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
