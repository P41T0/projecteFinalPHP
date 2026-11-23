<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class FormattingTest extends TestCase
{
    /**
     * Test that PHP code passes Pint linting
     */
    public function test_php_code_passes_pint(): void
    {
        $output = [];
        $returnCode = 0;

        exec('pint --test 2>&1', $output, $returnCode);

        $this->assertEquals(
            0,
            $returnCode,
            'PHP code formatting check with Pint failed. '.
            'Run: pint --fix'."\n".
            implode("\n", $output)
        );
    }

    /**
     * Test that JavaScript and CSS code passes Prettier formatting
     */
    public function test_js_css_code_passes_prettier(): void
    {
        $output = [];
        $returnCode = 0;

        // Check if pnpm is available, otherwise skip
        exec('which pnpm 2>/dev/null || where pnpm 2>nul', $pnpmCheck, $pnpmCode);

        if ($pnpmCode !== 0) {
            $this->markTestSkipped('pnpm is not installed in the test environment');
        }

        // Run prettier check
        exec('pnpm run format:check 2>&1', $output, $returnCode);

        $this->assertEquals(
            0,
            $returnCode,
            'JavaScript/CSS formatting check with Prettier failed. '.
            'Run: pnpm run format'."\n".
            implode("\n", $output)
        );
    }

    /**
     * Test that both Pint and Prettier configurations don't conflict
     */
    public function test_pint_and_prettier_do_not_conflict(): void
    {
        $this->assertFileExists(
            base_path('pint.json'),
            'pint.json configuration file is missing'
        );

        $this->assertFileExists(
            base_path('.prettierrc.json'),
            '.prettierrc.json configuration file is missing'
        );

        $this->assertFileExists(
            base_path('.prettierignore'),
            '.prettierignore file is missing'
        );

        // Verify Pint configuration
        $pintConfig = json_decode(file_get_contents(base_path('pint.json')), true);
        $this->assertNotNull($pintConfig, 'pint.json is not valid JSON');
        $this->assertArrayHasKey('preset', $pintConfig, 'pint.json must have a preset');

        // Verify Prettier configuration
        $prettierConfig = json_decode(file_get_contents(base_path('.prettierrc.json')), true);
        $this->assertNotNull($prettierConfig, '.prettierrc.json is not valid JSON');
        $this->assertArrayHasKey('semi', $prettierConfig, '.prettierrc.json must be configured');

        // Verify .prettierignore is not empty
        $prettierIgnore = file_get_contents(base_path('.prettierignore'));
        $this->assertNotEmpty($prettierIgnore, '.prettierignore should not be empty');
        $this->assertStringContainsString('vendor/', $prettierIgnore, '.prettierignore should exclude vendor/');
    }
}
