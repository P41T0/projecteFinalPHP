# Architecture and application flows

This document describes the current implementation, not a specification of future features. Routes, controllers, models, and migrations are the source of truth.

## Requests and interface

`public/index.php` starts Laravel. `bootstrap/app.php` loads `routes/web.php` and `routes/console.php`, registers `/up`, and adds `SetLocale` to the web middleware group. Store routes call controllers that return Blade views. Account pages are Livewire components registered in `routes/settings.php`; Fortify configures authentication views in `app/Providers/FortifyServiceProvider.php`.

The interface uses Blade, Flux components, and Tailwind. Vite compiles the entry points declared in `vite.config.js`; `resources/js/passkeys.js` integrates the WebAuthn client. Product and section views select text columns according to the active locale.

## Catalog and administration

The home page (`inici`) loads sections and eagerly loads visible products for visible sections; the view filters hidden items. Product detail is public and displays inventory by store. Direct access to a product detail does not apply the visibility filter, so visibility is not an access restriction.

Administration routes in `routes/web.php` use the `admin` middleware. `EnsureUserIsAdmin` calls `User::isAdmin()` and returns 404 for both guests and non-administrators. Administrators can create and edit products and sections and edit inventory quantities for existing stores. Empty controller methods do not imply that store creation or deletion is implemented.

New products and sections are hidden by default. When a product is created, it is associated with each existing store with zero inventory. Product image uploads are limited to JPEG, PNG, GIF, or WebP, with a maximum size of 2 MiB and dimensions of 4096 × 4096 pixels. The server checks the detected type and dimensions and attempts to decode the image with PHP GD, then stores the original file on Laravel's public disk under a generated name and content-derived extension. GIF animation is preserved, but these checks do not re-encode or sanitize image contents. Existing uploads are not revalidated and replacing an image does not delete the previous file.

A local installation may need `php artisan storage:link` to expose the public disk. However, the product detail view currently constructs image URLs using a hard-coded external host rather than the configured disk URL. Docker Compose does not mount persistent storage for uploads; see the [README](../README.md#running-with-docker) before replacing an app container.

## Orders

Purchase routes require authentication. `ComandaController::afegir` finds the user's first open order, or creates one using the first store ordered by population name. It adds a product with quantity 1 if it is not already present; adding it again does not increase its quantity. Creating an order requires at least one store.

The cart form submits quantities and a store to `actualiza.quantitat`. The controller checks order ownership, whether the order is open, integer quantities from 0 to 10, the existence of the store, and whether submitted products belong to the order. The code intends to remove zero-quantity lines but compares strictly with integer `0`: HTTP form values may arrive as strings. Verify this case before relying on or changing it.

`confirma.compres` also checks ownership: an open order with products is closed, an empty order is not closed, and a closed order is displayed again. The total uses the product's current price and pivot quantity, not a historical line price. The current flow does not implement payment or reserve or deduct stock on confirmation. Adding and confirming use state-changing `GET` routes; consider this when changing HTTP behavior or security.

## Data and relationships

| Table / model | Relationships and relevant data |
| --- | --- |
| `users` / `User` | A user has many `comanda` records via `usuari_id`; `admin` controls administrative access. |
| `seccio` / `Seccio` | A section has many products; it has names and descriptions in three languages and `mostra_sec`. |
| `producte` / `Producte` | Belongs to a section; has a price, optional image, `mostra_prod`, and relationships to stores and orders. |
| `botiga` / `Botiga` | Pickup store with population name and contact details. |
| `comanda` / `Comanda` | Belongs to a user and has a `botiga_id`; `oberta` indicates cart state. |
| `estoc_botiga` / `EstocBotiga` | Product-store pivot with `quantitat`. |
| `linia_comanda` / `LiniaComanda` | Product-order pivot with `quantitat`. |
| `passkeys` / `Passkey` | WebAuthn credentials associated with users; the custom model is registered in `AppServiceProvider`. |

Application models and pivots use `HasUuids`; new keys are UUIDv7. Composite indexes cover `producte(seccio_id, mostra_prod)` and `comanda(usuari_id, oberta)`. Laravel's internal tables do not necessarily use the same key format.

The identifier migrations convert legacy numeric passkey IDs and non-UUIDv7 IDs in the application tables above. The latter migration updates user references in orders and passkeys, section references in products, product and store references in inventory and orders, and order references in order lines. IDs already using UUIDv7 are left unchanged. Database sessions for users whose IDs change are deleted; links, queued payloads, and references outside these tables are not rewritten.

For a migrated user with passkeys, the migration reads the WebAuthn user handle from a stored credential and saves it as base64 in `users.passkey_user_handle`. The `User` model hides this column during serialization and uses the decoded handle for passkey authentication; users without a preserved handle use the package default. A missing or invalid credential handle stops the migration. Identifier updates run in a transaction; SQLite defers foreign-key checks and MySQL temporarily disables them. Read the [README](../README.md#upgrading-application-identifiers) before deploying this irreversible migration.

## Authentication and locale

Fortify enables registration, password reset, email verification, two-factor authentication, and passkeys in `config/fortify.php`. Account routes use `auth`, some use `verified`, and certain security settings use `password.confirm`. `User` does not currently implement `MustVerifyEmail`; do not assume that `verified` requires all users to verify their email without reviewing that contract.

Passkeys are optional and require HTTPS or localhost. `APP_URL` must match the origin and `APP_KEY` must remain stable. Fortify actions and shared validation are in `app/Actions/Fortify/` and `app/Concerns/`.

`/lang/{idioma}` accepts `ca`, `en`, and `es`, stores `idioma` in the session, and redirects back. `SetLocale` applies that value or the configured locale to each web request. Interface strings are translated through `lang/*.json` and `lang/{locale}/`; catalog data uses `nom`, `nom_es`, `nom_en`, and corresponding description fields.

## Security

The application implements comprehensive security measures:

### Security Headers

`app/Http/Middleware/SetSecurityHeaders.php` applies critical HTTP security headers globally:
- `X-Content-Type-Options: nosniff` - Prevents MIME sniffing attacks
- `X-Frame-Options: DENY` - Prevents clickjacking
- `X-XSS-Protection: 1; mode=block` - Mitigates XSS attacks
- `Strict-Transport-Security: max-age=31536000` - Enforces HTTPS
- `Content-Security-Policy` - Prevents injection attacks
- `Referrer-Policy` - Controls referrer information

### Mass Assignment Protection

All Eloquent models define `$fillable` to prevent unauthorized property assignment:
- `Producte`, `Comanda`, `Botiga`, `Seccio`, `LiniaComanda`, `EstocBotiga`

### Authorization

Administrative routes enforce authorization via `EnsureUserIsAdmin` middleware and explicit checks:
- Controllers use `abort_unless(Auth::user()->isAdmin(), 403);`
- Order operations verify ownership before modification
- All data-modifying operations check permissions

### Input Validation

All user inputs are validated:
- Controllers use `$request->validate()`
- Product and section creation requires validation
- Cart updates validate quantities (0-10 range) and store existence
- Order confirmation validates order state and ownership

### Database Security

- All database queries use parameterized binding to prevent SQL injection
- Models use Eloquent's ORM which handles parameter escaping
- Relationships are properly defined and type-hinted
- UUIDs prevent ID enumeration attacks

### Code Security Tests

The application includes 13 comprehensive security analysis tests in `tests/Security/StaticAnalysisTest.php`:

1. **No dangerous functions** - Detects eval, exec, system, unserialize, etc.
2. **No weak random functions** - Prevents use of mt_rand/rand for security
3. **No SQL injection patterns** - Finds string concatenation in queries
4. **Mass assignment protection** - Verifies $fillable/$guarded defined
5. **No unescaped output** - Checks for unescaped Blade output
6. **Authorization checks** - Validates admin/owner verification
7. **No sensitive data logging** - Prevents credentials in logs
8. **Input validation** - Ensures request validation used
9. **No hardcoded credentials** - Finds hardcoded secrets
10. **No path traversal** - Validates file operations
11. **CSRF protection** - Verifies form tokens
12. **Security headers** - Confirms headers configured
13. **Parameterized queries** - Checks for safe query binding

See [Static Analysis Guide](static-analysis.md) for detailed test information and [Security Test Mapping](../SECURITY_TESTS_MAPPING.md) for coverage details.

### Code Formatting & Linting

The application uses two complementary formatters:

- **Pint** (PHP): Enforces Laravel coding standards
- **Prettier** (JavaScript/CSS): Maintains consistent frontend code style

Both tools are integrated into the test suite and can be run via:
```bash
composer test:format
```

See [Formatting Documentation](formatting.md) for configuration and usage details.
