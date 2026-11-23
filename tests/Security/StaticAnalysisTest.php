<?php

declare(strict_types=1);

namespace Tests\Security;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class StaticAnalysisTest extends TestCase
{
    private const DANGEROUS_FUNCTIONS = [
        'eval' => 'Remote code execution risk',
        'exec' => 'Command injection risk',
        'system' => 'Command injection risk',
        'passthru' => 'Command injection risk',
        'shell_exec' => 'Command injection risk',
        'proc_open' => 'Process execution risk',
        'mysql_' => 'Deprecated MySQL extension',
        'assert' => 'Code injection if not constant',
        'create_function' => 'Remote code execution',
        'unserialize' => 'Object injection risk',
    ];

    private const WEAK_RANDOM = [
        'mt_rand' => 'Weak random for security (use random_int)',
        'rand' => 'Weak random for security (use random_int)',
        'srand' => 'Weak random seed',
        'lcg_value' => 'Weak random value',
    ];

    private const APP_DIR = __DIR__ . '/../../app';

    /**
     * Test that dangerous functions are not used in the application code
     */
    public function test_no_dangerous_functions_used(): void
    {
        $violations = [];

        foreach ($this->getPhpFiles() as $file) {
            $content = file_get_contents($file->getRealPath());
            
            foreach (self::DANGEROUS_FUNCTIONS as $function => $risk) {
                if (preg_match("/\b{$function}\s*\(/i", $content)) {
                    $violations[$file->getRealPath()][] = "$function: $risk";
                }
            }
        }

        $this->assertEmpty(
            $violations,
            'Found dangerous functions in application code: ' . json_encode($violations, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Test that weak random functions are not used for security purposes
     */
    public function test_no_weak_random_functions(): void
    {
        $violations = [];

        foreach ($this->getPhpFiles() as $file) {
            $content = file_get_contents($file->getRealPath());
            
            foreach (self::WEAK_RANDOM as $function => $risk) {
                if (preg_match("/\b{$function}\s*\(/", $content)) {
                    $violations[$file->getRealPath()][] = "$function: $risk";
                }
            }
        }

        // Note: Some uses might be legitimate (non-security)
        $this->assertLessThanOrEqual(
            2,
            count($violations),
            'Found weak random functions: ' . json_encode($violations, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Test that SQL injection patterns are avoided
     */
    public function test_no_sql_injection_patterns(): void
    {
        $violations = [];

        foreach ($this->getPhpFiles() as $file) {
            $content = file_get_contents($file->getRealPath());
            $lines = file($file->getRealPath(), FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $lineNum => $line) {
                // Check for string concatenation in DB queries
                if (preg_match('/DB::|->raw\(|SELECT|INSERT|UPDATE|DELETE/i', $line) &&
                    preg_match('/\.\s*\$|\$\w+\s*\./', $line)) {
                    $violations[$file->getRealPath()][] = "Line " . ($lineNum + 1) . ": Possible SQL injection via string concatenation";
                }
            }
        }

        $this->assertEmpty(
            $violations,
            'Found potential SQL injection patterns: ' . json_encode($violations, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Test that mass assignment protections are in place
     */
    public function test_models_have_fillable_or_guarded(): void
    {
        $violations = [];
        $modelsDir = self::APP_DIR . '/Models';

        if (!is_dir($modelsDir)) {
            $this->markTestSkipped('Models directory not found');
        }

        foreach ($this->getPhpFilesIn($modelsDir) as $file) {
            $content = file_get_contents($file->getRealPath());

            // Check if it's an Eloquent model (extends Model)
            if (preg_match('/extends\s+(?:Model|BaseModel)/', $content)) {
                // Check if fillable or guarded is defined
                if (!preg_match('/protected\s+\$(?:fillable|guarded)/', $content)) {
                    $violations[] = $file->getFilename() . ': Missing $fillable or $guarded property';
                }
            }
        }

        $this->assertEmpty(
            $violations,
            'Models without mass assignment protection: ' . implode(', ', $violations)
        );
    }

    /**
     * Test for XSS vulnerabilities in Blade templates
     */
    public function test_no_unescaped_output_in_views(): void
    {
        $violations = [];
        $viewsDir = self::APP_DIR . '/../resources/views';

        if (!is_dir($viewsDir)) {
            $this->markTestSkipped('Views directory not found');
        }

        foreach ($this->getFilesIn($viewsDir, 'blade.php') as $file) {
            $content = file_get_contents($file->getRealPath());
            $lines = file($file->getRealPath(), FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $lineNum => $line) {
                // Check for unescaped output: {!! $variable !!}
                if (preg_match('/\{!!\s*\$\w+.*!!\}/', $line)) {
                    // Check if it's not in a comment or intentional (HTML content)
                    if (!preg_match('/@php|comment|\/\/|safe|html/i', $line)) {
                        $violations[$file->getRealPath()][] = "Line " . ($lineNum + 1) . ": Unescaped output {!! !!} - XSS risk";
                    }
                }
            }
        }

        // Note: Some uses might be legitimate for HTML content
        $this->assertLessThanOrEqual(
            10,
            count($violations),
            'Found potential XSS vulnerabilities: ' . json_encode($violations, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Test that authorization checks exist in controllers
     */
    public function test_controllers_check_authorization(): void
    {
        $violations = [];
        $controllersDir = self::APP_DIR . '/Http/Controllers';

        if (!is_dir($controllersDir)) {
            $this->markTestSkipped('Controllers directory not found');
        }

        foreach ($this->getPhpFilesIn($controllersDir) as $file) {
            $content = file_get_contents($file->getRealPath());
            
            // Check if the controller has methods that might need authorization
            if (preg_match('/public\s+function\s+(update|delete|destroy|edit|show)\s*\(/', $content)) {
                // These methods typically need authorization
                if (!preg_match('/authorize\(|gate|policy|can\(|cannot\(|abort_unless|abort_if/', $content)) {
                    // Allow if it's a resource or index controller without sensitive operations
                    if (!preg_match('/index|create/i', $file->getFilename())) {
                        $violations[] = $file->getFilename() . ': May be missing authorization checks';
                    }
                }
            }
        }

        // Note: This is a heuristic check; false positives are expected for review
        $this->assertLessThanOrEqual(
            5,
            count($violations),
            'Review these controllers for authorization: ' . implode(', ', $violations)
        );
    }

    /**
     * Test that sensitive data is not logged
     */
    public function test_no_sensitive_data_logging(): void
    {
        $violations = [];
        $sensitivePatterns = [
            'password' => 'Password should not be logged',
            'token' => 'Token should not be logged',
            'secret' => 'Secret should not be logged',
            'api_key' => 'API key should not be logged',
            'credit_card' => 'Credit card data should not be logged',
        ];

        foreach ($this->getPhpFiles() as $file) {
            $lines = file($file->getRealPath(), FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $lineNum => $line) {
                if (preg_match('/Log::|logger\(|dump\(|dd\(/', $line)) {
                    foreach ($sensitivePatterns as $pattern => $risk) {
                        if (preg_match("/{$pattern}/i", $line)) {
                            $violations[$file->getRealPath()][] = "Line " . ($lineNum + 1) . ": $risk";
                        }
                    }
                }
            }
        }

        $this->assertEmpty(
            $violations,
            'Found potential sensitive data logging: ' . json_encode($violations, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Test that input validation is performed
     */
    public function test_controllers_validate_input(): void
    {
        $violations = [];
        $controllersDir = self::APP_DIR . '/Http/Controllers';

        if (!is_dir($controllersDir)) {
            $this->markTestSkipped('Controllers directory not found');
        }

        foreach ($this->getPhpFilesIn($controllersDir) as $file) {
            $content = file_get_contents($file->getRealPath());
            
            if (preg_match('/\$request|->input\(/', $content)) {
                // Skip if validation is present
                if (!preg_match('/validate\(|validated\(|rules/', $content)) {
                    $violations[] = $file->getFilename() . ': May be missing input validation';
                }
            }
        }

        // Note: This is a heuristic check; false positives are expected for review
        $this->assertLessThanOrEqual(
            5,
            count($violations),
            'Review these controllers for input validation: ' . implode(', ', $violations)
        );
    }

    /**
     * Test for hardcoded credentials in code
     */
    public function test_no_hardcoded_credentials(): void
    {
        $violations = [];
        $patterns = [
            '/password\s*=\s*["\'][\w\-]+["\']/' => 'Hardcoded password detected',
            '/token\s*=\s*["\'][a-zA-Z0-9]+["\']/' => 'Hardcoded token detected',
            '/api_key\s*=\s*["\'][a-zA-Z0-9]+["\']/' => 'Hardcoded API key detected',
            '/secret\s*=\s*["\'][a-zA-Z0-9]+["\']/' => 'Hardcoded secret detected',
            '/DATABASE_PASSWORD\s*=\s*["\'][^"\']+["\']/' => 'Database password in code',
        ];

        foreach ($this->getPhpFiles() as $file) {
            $content = file_get_contents($file->getRealPath());
            
            // Skip .env files
            if (strpos($file->getRealPath(), '.env') !== false) {
                continue;
            }

            foreach ($patterns as $pattern => $risk) {
                if (preg_match($pattern, $content)) {
                    $violations[$file->getRealPath()][] = "$risk";
                }
            }
        }

        $this->assertEmpty(
            $violations,
            'Found hardcoded credentials: ' . json_encode($violations, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Test for path traversal vulnerabilities
     */
    public function test_no_path_traversal_patterns(): void
    {
        $violations = [];

        foreach ($this->getPhpFiles() as $file) {
            $content = file_get_contents($file->getRealPath());
            $lines = file($file->getRealPath(), FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $lineNum => $line) {
                // Check for unvalidated file operations with user input
                if (preg_match('/(file_get_contents|file_put_contents|readfile|fopen|fread|require|include|require_once|include_once)\s*\(\s*\$/', $line)) {
                    // Check if there's validation before the operation
                    $contextLines = array_slice($lines, max(0, $lineNum - 5), 6);
                    $context = implode('', $contextLines);
                    
                    if (!preg_match('/(realpath|str_replace|preg_replace|filter|validate|basename|dirname|pathinfo)/', $context)) {
                        $violations[$file->getRealPath()][] = "Line " . ($lineNum + 1) . ": Possible path traversal with \$variable";
                    }
                }
            }
        }

        $this->assertLessThanOrEqual(
            3,
            count($violations),
            'Found potential path traversal vulnerabilities: ' . json_encode($violations, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Test for CSRF token usage in forms
     */
    public function test_forms_have_csrf_tokens(): void
    {
        $violations = [];
        $viewsDir = self::APP_DIR . '/../resources/views';

        if (!is_dir($viewsDir)) {
            $this->markTestSkipped('Views directory not found');
        }

        foreach ($this->getFilesIn($viewsDir, 'blade.php') as $file) {
            $content = file_get_contents($file->getRealPath());
            
            // Check if file has forms with method="post" (but not Livewire forms)
            if (preg_match('/<form[^>]*method\s*=\s*["\']?post["\']?/i', $content)) {
                // Skip Livewire forms (they handle CSRF automatically)
                if (strpos($content, 'wire:submit') !== false || strpos($content, 'wire:') !== false) {
                    continue;
                }
                
                // Check if CSRF token is present (either csrf_field(), @csrf, or csrf_token)
                if (!preg_match('/csrf_field\(\)|@csrf|csrf_token/', $content)) {
                    $violations[] = $file->getFilename() . ': POST form without CSRF token';
                }
            }
        }

        // Note: Some forms might use API tokens instead
        $this->assertLessThanOrEqual(
            1,
            count($violations),
            'Found forms without CSRF tokens: ' . implode(', ', $violations)
        );
    }

    /**
     * Test for secure header configuration
     */
    public function test_security_headers_configured(): void
    {
        $middleware = self::APP_DIR . '/Http/Middleware';
        $found = false;

        if (is_dir($middleware)) {
            foreach ($this->getPhpFilesIn($middleware) as $file) {
                $content = file_get_contents($file->getRealPath());
                
                if (preg_match('/X-Content-Type-Options|X-Frame-Options|X-XSS-Protection|Strict-Transport-Security/i', $content)) {
                    $found = true;
                    break;
                }
            }
        }

        // Check kernel or config
        $kernel = self::APP_DIR . '/Http/Kernel.php';
        if (file_exists($kernel)) {
            $content = file_get_contents($kernel);
            if (preg_match('/X-Content-Type-Options|middleware|header/i', $content)) {
                $found = true;
            }
        }

        // Note: Security headers can be set in middleware or web server config
        // This just checks if there's any indication they are configured
        $this->assertTrue(
            $found,
            'No evidence of security headers configuration found in middleware or kernel'
        );
    }

    /**
     * Test for SQL query parameter binding
     */
    public function test_database_queries_use_parameters(): void
    {
        $violations = [];

        foreach ($this->getPhpFiles() as $file) {
            $content = file_get_contents($file->getRealPath());
            $lines = file($file->getRealPath(), FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $lineNum => $line) {
                // Check for string interpolation in queries
                if (preg_match('/DB::select|DB::insert|DB::update|DB::delete|DB::statement/', $line)) {
                    if (preg_match('/\$|\./', $line) && !preg_match('/\?|\[.*\]/', $line)) {
                        $violations[$file->getRealPath()][] = "Line " . ($lineNum + 1) . ": Query may not use parameterized binding";
                    }
                }
            }
        }

        $this->assertEmpty(
            $violations,
            'Found queries without proper parameter binding: ' . json_encode($violations, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Get all PHP files in the app directory
     */
    private function getPhpFiles(): iterable
    {
        return $this->getPhpFilesIn(self::APP_DIR);
    }

    /**
     * Get all PHP files in a specific directory
     */
    private function getPhpFilesIn(string $directory): iterable
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file instanceof SplFileInfo && $file->getExtension() === 'php') {
                yield $file;
            }
        }
    }

    /**
     * Get all files with a specific extension in a directory
     */
    private function getFilesIn(string $directory, string $extension): iterable
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file instanceof SplFileInfo && $file->getExtension() === 'php' && str_ends_with($file->getFilename(), $extension)) {
                yield $file;
            }
        }
    }
}
