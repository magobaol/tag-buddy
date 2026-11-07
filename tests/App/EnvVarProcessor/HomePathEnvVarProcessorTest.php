<?php

namespace Tests\App\EnvVarProcessor;

use App\EnvVarProcessor\HomePathEnvVarProcessor;
use PHPUnit\Framework\TestCase;

class HomePathEnvVarProcessorTest extends TestCase
{
    /**
     * @dataProvider expansions
     */
    public function test_expand(string $path, string $expected): void
    {
        $this->assertSame($expected, HomePathEnvVarProcessor::expand($path, '/home/francesco'));
    }

    public static function expansions(): array
    {
        return [
            'tilde alone' => ['~', '/home/francesco'],
            'tilde slash' => ['~/vault/tags.yaml', '/home/francesco/vault/tags.yaml'],
            'absolute path' => ['/data/tags.yaml', '/data/tags.yaml'],
            'relative path' => ['tags.yaml', 'tags.yaml'],
            'tilde not at the start' => ['/data/~/tags.yaml', '/data/~/tags.yaml'],
            'other user is left verbatim' => ['~someone/tags.yaml', '~someone/tags.yaml'],
        ];
    }

    public function test_it_fails_when_home_is_unknown(): void
    {
        $this->expectException(\RuntimeException::class);

        HomePathEnvVarProcessor::expand('~/tags.yaml', '');
    }

    public function test_it_expands_the_value_of_the_variable_it_is_given(): void
    {
        $processor = new HomePathEnvVarProcessor();
        $previousHome = getenv('HOME');
        putenv('HOME=/home/francesco');

        try {
            $result = $processor->getEnv('home_path', 'TAGS_FILE_PATH', fn (string $name) => '~/tags.yaml');
        } finally {
            putenv('HOME='.$previousHome);
        }

        $this->assertSame('/home/francesco/tags.yaml', $result);
    }
}
