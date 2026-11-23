# Static Analysis Guide

This guide explains the static analysis tools configured for this Laravel project to detect security vulnerabilities and code quality issues.

## Tools Configured

### 1. **CodeQL** (GitHub Action)
Automated security analysis that runs on every push to `develop` and `main` branches.

**What it checks:**
- SQL injection vulnerabilities
- Cross-site scripting (XSS) vulnerabilities
- Command injection risks
- Authentication/authorization issues
- Insecure deserialization
- Use of deprecated functions

**Configuration:** `.github/workflows/codeql.yml`

### 2. **Semgrep** (GitHub Action)
Fast, customizable static analysis with security-focused rulesets.

**Included rulesets:**
- `p/security-audit` - General security best practices
- `p/laravel` - Laravel-specific vulnerabilities
- `p/owasp-top-ten` - OWASP Top 10 patterns
- `p/php` - PHP language security patterns

**Custom rules:** `.semgrep/custom-rules.yaml`

### 3. **PHPStan**
Static type checker that finds bugs at compile-time without running code.

**Configuration:** Default Laravel preset (level max)

### 4. **Pint** (Laravel's Code Style Fixer)
Ensures consistent code style across the project.

## Running Tests Locally

### Prerequisites
```bash
composer install
php artisan setup
```

### Run All Static Analysis
```bash
# Run all checks
./run-static-analysis.sh

# Or run individually:
php artisan security:analyze  # Pest tests
vendor/bin/phpstan analyse app --level=max
vendor/bin/pint --test
```

### Run Security Tests Only
```bash
php ./vendor/bin/pest --filter=Security
```

### Run Specific Security Checks

**Check for dangerous functions:**
```bash
php ./vendor/bin/pest tests/Security/StaticAnalysisTest.php::test_no_dangerous_functions_used
```

**Check for SQL injection patterns:**
```bash
php ./vendor/bin/pest tests/Security/StaticAnalysisTest.php::test_no_sql_injection_patterns
```

**Check mass assignment protection:**
```bash
php ./vendor/bin/pest tests/Security/StaticAnalysisTest.php::test_models_have_fillable_or_guarded
```

**Check authorization in controllers:**
```bash
php ./vendor/bin/pest tests/Security/StaticAnalysisTest.php::test_controllers_check_authorization
```

**Check for sensitive data logging:**
```bash
php ./vendor/bin/pest tests/Security/StaticAnalysisTest.php::test_no_sensitive_data_logging
```

**Check input validation:**
```bash
php ./vendor/bin/pest tests/Security/StaticAnalysisTest.php::test_controllers_validate_input
```

## Understanding the Test Results

### High Priority Issues
- SQL injection patterns
- Dangerous function usage (eval, exec, etc.)
- Missing CSRF tokens
- Hardcoded credentials

### Medium Priority Issues
- Missing mass assignment protection ($fillable/$guarded)
- Weak random number generation
- Sensitive data in logs
- Missing input validation

### Low Priority Issues
- Unsafe redirects (need validation)
- Authorization check suggestions

## GitHub Workflow Triggers

| Workflow | Trigger | Frequency |
|----------|---------|-----------|
| CodeQL | Push to main/develop, PRs, schedule | Weekly (Mondays 4:23 AM UTC) |
| Static Analysis | Push to main/develop, PRs | On every push |
| Tests | Push to main/develop, PRs | On every push |

## Fixing Issues Found

### SQL Injection
❌ **Bad:**
```php
DB::select("SELECT * FROM users WHERE id = " . $id);
```

✅ **Good:**
```php
DB::select("SELECT * FROM users WHERE id = ?", [$id]);
// Or using Eloquent:
User::find($id);
```

### Mass Assignment
❌ **Bad:**
```php
class Product extends Model
{
    // No mass assignment protection
}
```

✅ **Good:**
```php
class Product extends Model
{
    protected $fillable = ['name', 'description', 'price'];
    // Or use $guarded = []
}
```

### Authorization
❌ **Bad:**
```php
public function update(Request $request, Post $post)
{
    $post->update($request->validated());
    return redirect()->back();
}
```

✅ **Good:**
```php
public function update(Request $request, Post $post)
{
    $this->authorize('update', $post); // Add authorization check
    $post->update($request->validated());
    return redirect()->back();
}
```

### Sensitive Data Logging
❌ **Bad:**
```php
Log::info('User data', $user->toArray()); // May log password!
```

✅ **Good:**
```php
Log::info('User authenticated', ['user_id' => $user->id]);
```

## CI/CD Integration

Violations found by CodeQL and Semgrep will appear as:
- Checks on pull requests
- Security alerts in GitHub Security tab
- Workflow failure (depending on severity)

## False Positives

Some checks may report false positives. Mark them with:

**For Semgrep:**
```php
// nosemgrep: <rule-id>
$potentially_safe_code();
```

**For PHPStan:**
```php
/** @phpstan-ignore-next-line */
$potentially_safe_code();
```

## Extending the Analysis

To add custom security rules:

1. Edit `.semgrep/custom-rules.yaml`
2. Add new patterns following [Semgrep documentation](https://semgrep.dev/docs/writing-rules)
3. Add corresponding tests in `tests/Security/StaticAnalysisTest.php`
4. Run tests locally before committing

## Resources

- [GitHub CodeQL Documentation](https://codeql.github.com/)
- [Semgrep Documentation](https://semgrep.dev/docs/)
- [PHPStan Documentation](https://phpstan.org/)
- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
