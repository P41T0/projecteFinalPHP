# Static Analysis Suite for Windows
# Run all static analysis tools

Write-Host "================================" -ForegroundColor Cyan
Write-Host "Static Analysis Suite" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Check if composer dependencies are installed
if (-not (Test-Path "vendor")) {
    Write-Host "Installing composer dependencies..." -ForegroundColor Yellow
    composer install --no-interaction
    Write-Host ""
}

# Run PHPStan
Write-Host "🔍 Running PHPStan..." -ForegroundColor Cyan
& "vendor/bin/phpstan" analyse app --level=max --no-progress; $phpstan_result = $LASTEXITCODE
Write-Host ""

# Run Pest security tests
Write-Host "🛡️  Running security tests..." -ForegroundColor Cyan
& "php" "./vendor/bin/pest" "tests/Security/" --no-interaction; $pest_result = $LASTEXITCODE
Write-Host ""

# Run Pint (code style)
Write-Host "🎨 Checking code style with Pint..." -ForegroundColor Cyan
& "vendor/bin/pint" --test; $pint_result = $LASTEXITCODE
Write-Host ""

# Check for dependencies vulnerabilities
Write-Host "🔐 Checking for vulnerable dependencies..." -ForegroundColor Cyan
composer audit; $composer_result = $LASTEXITCODE
Write-Host ""

Write-Host "================================" -ForegroundColor Cyan
Write-Host "✅ Static analysis complete!" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Review the output above for any issues." -ForegroundColor Yellow
Write-Host "For more information, see: docs/static-analysis.md" -ForegroundColor Yellow
Write-Host ""

# Summary
Write-Host "Results Summary:" -ForegroundColor Cyan
Write-Host "  PHPStan: $(if($phpstan_result -eq 0) { 'PASSED ✓' } else { 'ISSUES FOUND' })"
Write-Host "  Security Tests: $(if($pest_result -eq 0) { 'PASSED ✓' } else { 'ISSUES FOUND' })"
Write-Host "  Code Style: $(if($pint_result -eq 0) { 'PASSED ✓' } else { 'ISSUES FOUND' })"
Write-Host "  Dependencies: $(if($composer_result -eq 0) { 'SAFE ✓' } else { 'VULNERABILITIES FOUND' })"
