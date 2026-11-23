<?php

/**
 * VULNERABLE CODE EXAMPLES - For Security Test Verification
 *
 * This file contains intentionally vulnerable code patterns that demonstrate
 * security issues. It's used to verify that security tests can detect these problems.
 *
 * ⚠️ WARNING: This file contains examples of BAD practices. Never use these patterns
 * in production code. This is for testing purposes only.
 */

namespace App\Examples\VulnerableCode;

use App\Models\Producte;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

// ============================================================================
// 1. DANGEROUS FUNCTIONS - Remote Code Execution
// ============================================================================

class DangerousFunctionsExample
{
    /**
     * ❌ VULNERABLE: Using eval() - Remote Code Execution
     */
    public function vulnerableEval($userInput)
    {
        // Never do this!
        eval($userInput);
    }

    /**
     * ❌ VULNERABLE: Using exec() - Command Injection
     */
    public function vulnerableExec($filename)
    {
        // Never do this!
        exec('rm '.$filename);
    }

    /**
     * ❌ VULNERABLE: Using system() - Command Injection
     */
    public function vulnerableSystem($command)
    {
        // Never do this!
        system($command);
    }

    /**
     * ❌ VULNERABLE: Using shell_exec() - Command Injection
     */
    public function vulnerableShellExec($userInput)
    {
        // Never do this!
        $result = shell_exec('echo '.$userInput);

        return $result;
    }

    /**
     * ❌ VULNERABLE: Using passthru() - Command Injection
     */
    public function vulnerablePassthru($file)
    {
        // Never do this!
        passthru('cat '.$file);
    }

    /**
     * ❌ VULNERABLE: Using assert() - Code Injection
     */
    public function vulnerableAssert($condition)
    {
        // Never do this!
        assert($condition);
    }

    /**
     * ❌ VULNERABLE: Using create_function() - Deprecated & Dangerous
     */
    public function vulnerableCreateFunction()
    {
        // Never do this!
        $func = create_function('$a,$b', 'return "ln($a) + ln($b) = " . log($a * $b);');
    }

    /**
     * ❌ VULNERABLE: Using unserialize() - Object Injection
     */
    public function vulnerableUnserialize($userData)
    {
        // Never do this!
        $object = unserialize($userData);

        return $object;
    }
}

// ============================================================================
// 2. WEAK RANDOM FUNCTIONS - Cryptographic Weakness
// ============================================================================

class WeakRandomExample
{
    /**
     * ❌ VULNERABLE: Using mt_rand() for security
     */
    public function vulnerableMtRand()
    {
        // Never use for security!
        $token = mt_rand(1, 1000000);

        return $token;
    }

    /**
     * ❌ VULNERABLE: Using rand() for security
     */
    public function vulnerableRand()
    {
        // Never use for security!
        $secret = rand(100000, 999999);

        return $secret;
    }

    /**
     * ❌ VULNERABLE: Using srand() - Weak seed
     */
    public function vulnerableSrand()
    {
        // Never do this!
        srand(time());
        $value = rand();

        return $value;
    }

    /**
     * ❌ VULNERABLE: Using lcg_value() for security
     */
    public function vulnerableLcgValue()
    {
        // Never use for security!
        $token = (int) (lcg_value() * 1000000);

        return $token;
    }
}

// ============================================================================
// 3. SQL INJECTION - Database Vulnerabilities
// ============================================================================

class SQLInjectionExample
{
    /**
     * ❌ VULNERABLE: String concatenation in DB::raw()
     */
    public function vulnerableSQLRaw($userId)
    {
        // Never do this!
        $user = \DB::select('SELECT * FROM users WHERE id = '.$userId);

        return $user;
    }

    /**
     * ❌ VULNERABLE: String interpolation in DB::select()
     */
    public function vulnerableSQLSelect($username)
    {
        // Never do this!
        $user = \DB::select("SELECT * FROM users WHERE username = '$username'");

        return $user;
    }

    /**
     * ❌ VULNERABLE: String concatenation in DB::update()
     */
    public function vulnerableSQLUpdate($id, $email)
    {
        // Never do this!
        \DB::update("UPDATE users SET email = '".$email."' WHERE id = ".$id);
    }

    /**
     * ❌ VULNERABLE: String concatenation in DB::delete()
     */
    public function vulnerableSQLDelete($id)
    {
        // Never do this!
        \DB::delete('DELETE FROM users WHERE id = '.$id);
    }
}

// ============================================================================
// 4. MASS ASSIGNMENT - Unauthorized Property Assignment
// ============================================================================

class VulnerableModel extends Model
{
    protected $table = 'vulnerable_models';

    // ❌ VULNERABLE: No $fillable or $guarded protection
    // Any property can be mass assigned!
}

// ============================================================================
// 5. MISSING AUTHORIZATION - Unauthorized Access
// ============================================================================

class VulnerableAuthorizationController
{
    /**
     * ❌ VULNERABLE: No authorization check
     * Any user can delete any post!
     */
    public function deletePost($postId)
    {
        $post = Producte::find($postId);
        $post->delete(); // No check if user owns this post!

        return redirect()->back();
    }

    /**
     * ❌ VULNERABLE: No authorization in update
     * Any user can edit any post!
     */
    public function updatePost(Request $request, $postId)
    {
        $post = Producte::find($postId);
        $post->update($request->all()); // No check if user is admin!

        return redirect()->back();
    }

    /**
     * ❌ VULNERABLE: No admin check
     * Any user can create products!
     */
    public function createProduct(Request $request)
    {
        // No: abort_unless(Auth::user()->isAdmin(), 403);
        $product = Producte::create($request->all());

        return redirect()->back();
    }
}

// ============================================================================
// 6. HARDCODED CREDENTIALS - Secret Exposure
// ============================================================================

class HardcodedCredentialsExample
{
    /**
     * ❌ VULNERABLE: Hardcoded password
     */
    protected $password = 'superSecretPassword123';

    /**
     * ❌ VULNERABLE: Hardcoded API token
     */
    protected $token = 'sk_live_abcd1234efgh5678';

    /**
     * ❌ VULNERABLE: Hardcoded API key
     */
    protected $api_key = 'AIzaSyDxDi1Hpyf3h4K5lm6noPqRsTuVwXyZ1234';

    /**
     * ❌ VULNERABLE: Hardcoded secret
     */
    protected $secret = 'my_app_secret_key_12345';

    /**
     * ❌ VULNERABLE: Database password in code
     */
    public function connect()
    {
        $password = 'db_password_root_123';
        // connect to database
    }
}

// ============================================================================
// 7. PATH TRAVERSAL - File Access Vulnerabilities
// ============================================================================

class PathTraversalExample
{
    /**
     * ❌ VULNERABLE: Unvalidated file read
     * User could request "../../sensitive.txt"
     */
    public function readFile(Request $request)
    {
        $filename = $request->input('filename');
        // Never do this!
        $content = file_get_contents($filename);

        return $content;
    }

    /**
     * ❌ VULNERABLE: Unvalidated file write
     */
    public function writeFile(Request $request)
    {
        $filename = $request->input('filename');
        $content = $request->input('content');
        // Never do this!
        file_put_contents($filename, $content);
    }

    /**
     * ❌ VULNERABLE: Unvalidated file inclusion
     */
    public function includeFile(Request $request)
    {
        $file = $request->input('file');
        // Never do this!
        include $file;
    }

    /**
     * ❌ VULNERABLE: Unvalidated require
     */
    public function requireFile(Request $request)
    {
        $file = $request->input('file');
        // Never do this!
        require_once $file;
    }
}

// ============================================================================
// 8. MISSING INPUT VALIDATION - Injection Attacks
// ============================================================================

class MissingValidationController
{
    /**
     * ❌ VULNERABLE: No input validation
     */
    public function storeWithoutValidation(Request $request)
    {
        // No: $request->validate([...]);
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'age' => $request->input('age'),
        ]);

        return $user;
    }

    /**
     * ❌ VULNERABLE: Validation skipped
     */
    public function updateWithoutValidation(Request $request, $id)
    {
        // No: $validated = $request->validate([...]);
        $post = Producte::find($id);
        $post->update($request->all());

        return $post;
    }
}

// ============================================================================
// 9. SENSITIVE DATA LOGGING - Information Disclosure
// ============================================================================

class SensitiveDataLoggingExample
{
    /**
     * ❌ VULNERABLE: Logging user password
     */
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        // Never do this!
        \Log::info('User login attempt', $user->toArray());
        // Password is now in logs!
    }

    /**
     * ❌ VULNERABLE: Logging API token
     */
    public function authenticate($token)
    {
        // Never do this!
        \Log::debug('Authentication token', ['token' => $token]);
    }

    /**
     * ❌ VULNERABLE: Using dd() with sensitive data
     */
    public function debugUser($user)
    {
        // Never do this!
        dd($user); // Password visible in browser
    }

    /**
     * ❌ VULNERABLE: Dumping secret
     */
    public function debugApiKey($apiKey)
    {
        // Never do this!
        dump(['secret' => $apiKey]);
    }

    /**
     * ❌ VULNERABLE: Logging credit card
     */
    public function processPayment($creditCard)
    {
        // Never do this!
        logger()->info('Processing payment', ['credit_card' => $creditCard]);
    }
}

// ============================================================================
// 10. INSECURE RANDOMIZATION - Cryptographic Failure
// ============================================================================

class InsecureRandomizationExample
{
    /**
     * ❌ VULNERABLE: Password reset token with weak randomization
     */
    public function requestPasswordReset($email)
    {
        $user = User::where('email', $email)->first();
        // Never do this!
        $token = mt_rand(100000, 999999); // Easily guessable!
        // Send password reset email with this weak token
    }

    /**
     * ❌ VULNERABLE: Session ID with weak randomization
     */
    public function createSession()
    {
        // Never do this!
        $sessionId = rand(1, 1000); // Can be predicted!
        // Use this session ID
    }
}
