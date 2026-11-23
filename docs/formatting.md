# Code Formatting & Linting Configuration

## Overview

This project uses two complementary code formatters to ensure code quality and consistency:

- **Pint**: PHP code formatting (Laravel standard preset)
- **Prettier**: JavaScript and CSS code formatting

Both tools are integrated into the testing pipeline and work without conflicts.

## Tools

### Pint (PHP)

- **Purpose**: Format and lint PHP code
- **Configuration**: `pint.json`
- **Preset**: `laravel` (follows Laravel's coding standards)
- **Runs on**: All PHP files in `app/`, `config/`, `routes/`, etc.

### Prettier (JavaScript/CSS)

- **Purpose**: Format JavaScript and CSS code
- **Configuration**: `.prettierrc.json`
- **Excludes**: `.prettierignore`
- **Runs on**: Files in `resources/js/` and `resources/css/`

### Configuration Files

#### `pint.json`
```json
{
  "preset": "laravel"
}
```

#### `.prettierrc.json`
```json
{
  "semi": true,
  "singleQuote": false,
  "tabWidth": 2,
  "trailingComma": "es5",
  "useTabs": false,
  "printWidth": 80,
  "arrowParens": "always"
}
```

#### `.prettierignore`
Excludes the following from Prettier formatting:
- `node_modules/`
- `vendor/`
- `public/build/`
- `bootstrap/cache/`
- `storage/`
- Configuration files (`.env`, `pint.json`, `composer.json`, etc.)

## Running Formatters

### Format All Code

#### Lint PHP Code (Pint)
```bash
composer lint
```

#### Format PHP Code (Pint)
```bash
composer lint:fix
```

#### Check JS/CSS Formatting (Prettier)
```bash
pnpm run format:check
```

#### Format JS/CSS Code (Prettier)
```bash
pnpm run format
```

### Format and Lint Tests

#### Check Both Pint and Prettier
```bash
composer test:format
```

This runs:
1. `pint --parallel --test` - PHP code style check
2. `pnpm run format:check` - JavaScript/CSS formatting check

#### Run All Tests
```bash
composer test
```

This runs:
1. `config:clear`
2. `test:lint` (Pint check)
3. `php artisan test` (Pest tests, including FormattingTest)

## Test File

**Location**: `tests/Feature/FormattingTest.php`

### Test Cases

1. **test_php_code_passes_pint**
   - Verifies PHP code passes Pint linting
   - Fails if `pint --test` returns non-zero exit code

2. **test_js_css_code_passes_prettier**
   - Verifies JavaScript/CSS code passes Prettier formatting
   - Skips if pnpm is not available
   - Fails if `pnpm run format:check` returns non-zero exit code

3. **test_pint_and_prettier_do_not_conflict**
   - Verifies both configuration files exist and are valid JSON
   - Verifies configuration files have required settings
   - Verifies `.prettierignore` is configured properly

## CI/CD Integration

Both formatters can be integrated into GitHub Actions workflows:

### In `.github/workflows/static-analysis.yml`

```yaml
- name: Check PHP formatting (Pint)
  run: pint --test

- name: Check JS/CSS formatting (Prettier)
  run: pnpm run format:check
```

### Or use the combined test command

```yaml
- name: Check all formatting
  run: composer test:format
```

## Conflict Avoidance

### How They Work Together

1. **Pint** handles PHP files only
2. **Prettier** handles JS and CSS files only
3. **No overlap** in file types or scope
4. **Separate configurations** prevent conflicting rules
5. **`.prettierignore`** excludes PHP configuration files from Prettier

### File Scope

| Tool | Files | Extensions |
|------|-------|-----------|
| Pint | PHP source code | `.php` |
| Prettier | Frontend assets | `.js`, `.css`, `.vue` |

### Configuration Scope

| Tool | Config File | Scope |
|------|---|---|
| Pint | `pint.json` | PHP files only |
| Prettier | `.prettierrc.json` | JS/CSS files only |

## Development Workflow

### Before Committing

```bash
# Format all code
composer lint:fix      # Fix PHP formatting
pnpm run format        # Fix JS/CSS formatting

# Run all tests
composer test
```

### In CI/CD

```bash
# Check formatting (don't modify)
composer test:format
```

## Troubleshooting

### Pint Issues

```bash
# View formatting errors without fixing
composer test:lint

# Fix formatting errors
composer lint:fix
```

### Prettier Issues

```bash
# View formatting errors without fixing
pnpm run format:check

# Fix formatting errors
pnpm run format
```

### Both Tools

```bash
# Run comprehensive test suite
composer test
```

## Notes

- **pnpm** must be installed globally for Prettier tests to run successfully
- **Prettier** test will be skipped if pnpm is not available
- Both tools respect `.prettierignore` and PHP namespace conventions
- Configuration files should be version-controlled in the repository
- Never override formatting requirements unless necessary (use `// prettier-ignore` or `/** pint: ignore */`)

## Related Documentation

- [Pint Documentation](https://laravel.com/docs/pint)
- [Prettier Documentation](https://prettier.io/docs/en/index.html)
- [Laravel Coding Standards](https://laravel.com/docs/contributions#code-style)
