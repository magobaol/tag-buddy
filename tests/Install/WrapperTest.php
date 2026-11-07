<?php

namespace Tests\Install;

use PHPUnit\Framework\TestCase;

/**
 * Runs install/tag-buddy against a throwaway HOME whose "installed app" is a stub console
 * that prints the TAGS_FILE_PATH it received, so the wrapper is tested without the real app.
 */
class WrapperTest extends TestCase
{
    private string $home;
    private string $configDir;

    protected function setUp(): void
    {
        $this->home = sys_get_temp_dir().'/tag-buddy-wrapper-'.uniqid();
        $this->configDir = $this->home.'/config';
        mkdir($this->home.'/.local/opt/tag-buddy/bin', 0777, true);
        mkdir($this->configDir.'/tag-buddy', 0777, true);
        file_put_contents(
            $this->home.'/.local/opt/tag-buddy/bin/console',
            "<?php echo getenv('TAGS_FILE_PATH');"
        );
    }

    protected function tearDown(): void
    {
        exec('rm -rf '.escapeshellarg($this->home));
    }

    public function test_it_stops_with_an_error_when_no_tags_path_is_configured(): void
    {
        [$exitCode, $stdout, $stderr] = $this->runWrapper();

        $this->assertSame(1, $exitCode);
        $this->assertSame('', $stdout);
        $this->assertStringContainsString($this->configDir.'/tag-buddy/config.yaml', $stderr);
        $this->assertStringContainsString('tags_file_path', $stderr);
    }

    public function test_it_stops_with_an_error_when_the_config_has_no_tags_path(): void
    {
        file_put_contents($this->configDir.'/tag-buddy/config.yaml', "other_key: value\n");

        [$exitCode, , $stderr] = $this->runWrapper();

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('tags_file_path', $stderr);
    }

    public function test_it_exports_the_path_read_from_the_config(): void
    {
        file_put_contents(
            $this->configDir.'/tag-buddy/config.yaml',
            "tags_file_path: '/data/tags.yaml'\n"
        );

        [$exitCode, $stdout] = $this->runWrapper();

        $this->assertSame(0, $exitCode);
        $this->assertSame('/data/tags.yaml', $stdout);
    }

    public function test_it_accepts_a_path_exported_by_hand_when_there_is_no_config(): void
    {
        [$exitCode, $stdout] = $this->runWrapper(['TAGS_FILE_PATH' => '/by-hand/tags.yaml']);

        $this->assertSame(0, $exitCode);
        $this->assertSame('/by-hand/tags.yaml', $stdout);
    }

    /**
     * @return array{int, string, string} exit code, stdout, stderr
     */
    private function runWrapper(array $extraEnv = []): array
    {
        $env = array_merge([
            'HOME' => $this->home,
            'XDG_CONFIG_HOME' => $this->configDir,
            'PATH' => dirname(PHP_BINARY).':/usr/bin:/bin',
        ], $extraEnv);

        $process = proc_open(
            ['bash', dirname(__DIR__, 2).'/install/tag-buddy'],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            null,
            $env
        );
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return [proc_close($process), $stdout, $stderr];
    }
}
