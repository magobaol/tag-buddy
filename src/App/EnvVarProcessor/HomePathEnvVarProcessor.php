<?php

namespace App\EnvVarProcessor;

use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

/**
 * Expands a leading "~" or "~/" to the home directory: "%env(home_path:NAME)%".
 * "~user" is left verbatim, resolving it would need the user database.
 */
class HomePathEnvVarProcessor implements EnvVarProcessorInterface
{
    public function getEnv(string $prefix, string $name, \Closure $getEnv): string
    {
        return self::expand($getEnv($name), (string) getenv('HOME'));
    }

    public static function getProvidedTypes(): array
    {
        return ['home_path' => 'string'];
    }

    public static function expand(string $path, string $home): string
    {
        if ($path !== '~' && !str_starts_with($path, '~/')) {
            return $path;
        }
        if ($home === '') {
            throw new \RuntimeException(sprintf('Impossibile espandere «%s»: HOME non impostato', $path));
        }

        return $home.substr($path, 1);
    }
}
