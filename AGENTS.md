# Agent guide

## Context

This repository contains an electronics store with a catalog, pickup orders, and inventory administration. It uses PHP 8.5+, Laravel 13, Livewire 4, Fortify, Flux UI, Tailwind CSS 4, and Vite Plus. The frontend package manager is pnpm 12.21.0; supported Node versions are listed in `package.json`.

Before changing the project, read the [README](README.md) for setup and deployment and the [architecture guide](docs/architecture.md) for existing flows and limitations. The [validation guide](docs/validation.md) maps changes to relevant tests.

## Repository map

| Path | Responsibility |
| --- | --- |
| `routes/web.php` | Catalog, cart, language, and administration routes. |
| `routes/settings.php` | Livewire account settings pages. Fortify registers authentication routes. |
| `app/Http/Controllers/` | HTTP logic for the catalog, orders, stores, products, and sections. |
| `app/Http/Middleware/` | Session locale and administrator checks; registered in `bootstrap/app.php`. |
| `app/Models/` | Eloquent models, relationships, and UUID pivot models. |
| `app/Actions/Fortify/`, `app/Concerns/`, `app/Providers/` | Registration, passwords, shared validation, and Fortify and passkey configuration. |
| `resources/views/` | Blade storefront and administration views; reusable components and Livewire pages in `components/` and `pages/`. |
| `resources/css/`, `resources/js/`, `vite.config.js` | Frontend assets, including passkey integration. |
| `lang/` | Interface and Laravel translations for `ca`, `es`, and `en`. |
| `database/migrations/`, `database/seeders/` | Schema, indexes, and demonstration data. |
| `tests/Feature/`, `tests/Unit/` | Pest tests; feature tests use `RefreshDatabase`. |
| `Dockerfile`, `compose.yaml`, `.github/workflows/` | Containers and CI automation. |

## Change guidelines

- Follow existing Laravel, Blade, and Livewire conventions. Check actual route names and methods before adding routes; some existing routes use `PUT` to create and `GET` to change state.
- New application entity and pivot identifiers are UUIDv7. Do not assume numeric identifiers; Laravel's internal tables may use other formats.
- Preserve `admin` authorization, order ownership checks, and validation of associated items. Add authorization and input tests when changing these flows.
- Maintain all three supported languages: interface strings use `__()` and catalog names and descriptions have separate language columns.
- Do not mistake existing behavior for an ideal business rule: consult the [architecture guide](docs/architecture.md), especially before changing the cart, inventory, or prices.
- Do not edit `vendor/`, `node_modules/`, generated files, or `.env` as part of an ordinary change. Do not publish keys or credentials.
- Add focused tests for changed behavior, run the relevant [validation](docs/validation.md), and update related documentation.
- Explain what each code change does in the pull request description or a review comment. Add or update concise English code comments where the behavior or its rationale needs clarification; avoid comments that merely repeat obvious code.
- Follow the branch and pull request naming conventions in [CONTRIBUTING.md](CONTRIBUTING.md).

## Execution precautions

- `composer setup` generates an application key and runs migrations. Do not run it against an existing environment without checking the consequences. Changing `APP_KEY` affects encrypted data and passkeys.
- Docker Compose runs migrations **and the seeder** when the application starts. The seeder creates demonstration data only when all tables it checks are empty; it is not production initialization.
- Run tests and migration or rollback experiments only against disposable test databases, never shared or production data. Consult the README warnings before upgrading MySQL or migrating passkeys.
