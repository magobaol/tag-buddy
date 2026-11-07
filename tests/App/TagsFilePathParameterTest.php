<?php

namespace Tests\App;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TagsFilePathParameterTest extends KernelTestCase
{
    private array $previous;

    protected function setUp(): void
    {
        $this->previous = [
            'HOME' => getenv('HOME'),
            'TAGS_FILE_PATH' => getenv('TAGS_FILE_PATH'),
            'env' => $_ENV['TAGS_FILE_PATH'] ?? null,
            'server' => $_SERVER['TAGS_FILE_PATH'] ?? null,
        ];
        putenv('HOME=/home/francesco');
    }

    protected function tearDown(): void
    {
        putenv('HOME='.$this->previous['HOME']);
        putenv($this->previous['TAGS_FILE_PATH'] === false ? 'TAGS_FILE_PATH' : 'TAGS_FILE_PATH='.$this->previous['TAGS_FILE_PATH']);
        $this->restore($_ENV, $this->previous['env']);
        $this->restore($_SERVER, $this->previous['server']);
        parent::tearDown();
    }

    public function test_a_tilde_in_the_tags_file_path_is_expanded_for_the_whole_app(): void
    {
        // Dotenv fills $_ENV and $_SERVER, which win over putenv: set all three.
        putenv('TAGS_FILE_PATH=~/vault/tags.yaml');
        $_ENV['TAGS_FILE_PATH'] = $_SERVER['TAGS_FILE_PATH'] = '~/vault/tags.yaml';

        self::bootKernel();

        $this->assertSame(
            '/home/francesco/vault/tags.yaml',
            self::getContainer()->getParameter('app.tags_file_path')
        );
    }

    private function restore(array &$superglobal, ?string $value): void
    {
        if ($value === null) {
            unset($superglobal['TAGS_FILE_PATH']);
        } else {
            $superglobal['TAGS_FILE_PATH'] = $value;
        }
    }
}
