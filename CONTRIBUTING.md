# Contributing

Thank you for contributing. Please keep changes focused and explain the problem and proposed behavior in the pull request. For larger changes, consider opening an issue first.

## Getting started

1. Follow the [README](README.md) for prerequisites and setup. Use a disposable database for development and tests.
2. Read [AGENTS.md](AGENTS.md) and the [architecture guide](docs/architecture.md) for project conventions and current behavior.
3. Create a branch for your change and make focused commits.
4. Add or update tests for behavior changes and update relevant documentation and translations.

Take care with `composer setup`: it generates an application key and runs migrations. Docker Compose also runs migrations and the demonstration seeder on application startup. Never use these commands against shared or production data without understanding their effects.

## Branch and pull request naming

- Name branches `<type>/<short-description>`, using lowercase English words separated by hyphens. Use `feature`, `fix`, `docs`, `refactor`, `test`, or `chore` as the type, for example `feature/store-search` or `fix/cart-quantity`. These conventions apply to new contributor branches; existing and automated branches need not be renamed.
- Title pull requests `<type>: <short description>`, using the same types and a concise English description in the imperative, for example `fix: validate cart quantities` or `docs: explain local setup`.

## Explaining code changes

For every code change, explain what it does in the pull request description or a review comment. Add or update concise English comments in the code where they clarify non-obvious behavior, constraints, or rationale; keep them accurate when the code changes and avoid repeating self-explanatory statements.

## Before opening a pull request

- Run the relevant focused tests, `composer test:lint`, `composer test:format`, and when appropriate `composer test` and `pnpm run build`. See [validation](docs/validation.md) for commands and test locations.
- Check authorization and order ownership when changing protected routes. Preserve the supported `ca`, `es`, and `en` translations when changing user-facing text.
- Run security tests if changing any authentication, authorization, database queries, or input handling code.
- Describe the change, list checks performed and any limitations, and include screenshots for visible interface changes.
- Mention migrations, configuration changes, and deployment or rollback precautions when applicable. Do not commit credentials, `.env`, or generated dependencies.

### Code Quality Checks

Before submitting a pull request, ensure:

1. **Formatting** - Run `composer test:format` (checks both Pint and Prettier)
   - PHP code passes Pint linting
   - JavaScript/CSS passes Prettier formatting

2. **Security** - Run `./vendor/bin/pest tests/Security/StaticAnalysisTest.php`
   - All 13 security checks pass
   - No dangerous functions or SQL injection patterns
   - Authorization and validation in place

3. **Tests** - Run `composer test`
   - All tests pass
   - New behavior has test coverage
   - Existing tests still pass

4. **Build** - Run `pnpm run build`
   - Frontend assets build without errors
   - No TypeScript or syntax errors

For suspected security vulnerabilities, follow [SECURITY.md](SECURITY.md) instead of opening a public issue.
