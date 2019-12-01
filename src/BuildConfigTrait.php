<?php

namespace CodeDistortion\SwapCon;

use CodeDistortion\SwapCon\Exceptions\InvalidConfigException;
use Dotenv\Dotenv;
use Dotenv\Environment\DotenvFactory;

/**
 * Trait containing SwapCon's method to build the config data from a .env file
 */
trait BuildConfigTrait
{
    /**
     * Find SwapCon values in the .env file and build the desired config values from them
     *
     * @param string $directory The directory the .env file is in.
     * @param string $filename  The name of an alternate file to use.
     * @return array
     * @throws InvalidConfigException Thrown when there is a problem with the .env config settings.
     */
    public static function buildConfig(string $directory = null, string $filename = null): array
    {
        if (is_null($directory)) {
            $directory = base_path();
        }

        // passing no factories stops Dotenv from populating to getenv(), $_ENV and $_SERVER
        $length = mb_strlen(static::ENV_PREFIX);
        $factory = new DotenvFactory([]);
        $dotenv = Dotenv::create($directory, $filename, $factory);
        $values = $dotenv->load();



        // pick out the relevant settings
        $groups = $connectionData = [];
        foreach ($values as $name => $value) {
            if (mb_substr($name, 0, $length) == static::ENV_PREFIX) {

                // look for GROUPS
                // eg. "SWAPCON__DATABASE__GROUP__MYSQLRO"
                if (preg_match('/^'.preg_quote(static::ENV_PREFIX).'_+GROUP_+([^_]+)_+([^_]+)$/', $name, $matches)) {

                    $config = mb_strtolower($matches[1]);
                    $group = mb_strtolower($matches[2]);
                    $groupContains = mb_strtolower((string) $value);

                    $groups[$config][$group] = array_filter((array) preg_split('/[\s,]+/', $groupContains));
                    if (!count($groups[$config][$group])) {
                        throw InvalidConfigException::noConnectionsInGroup($config, $group);
                    }

                    // look for CONNECTION settings
                    // eg. "SWAPCON__DATABASE__MYSQLRO__HOST"
                } elseif (preg_match(
                    '/^'.preg_quote(static::ENV_PREFIX).'_+([^_]+)_+([^_]+)_+([^_]+)$/',
                    $name,
                    $matches
                )) {

                    $config = mb_strtolower($matches[1]);
                    $connection = mb_strtolower($matches[2]);
                    $var = mb_strtolower($matches[3]);

                    // make sure the config-type is allowed
                    if (array_key_exists($config, static::CONNECT_ARRAY_NAMES)) {

                        // build the connection details
                        if (!isset($connectionData[$config][$connection])) {
                            $connectionData[$config][$connection] = [
                                'name' => $connection,
                                'clone' => null,
                                'values' => []
                            ];
                        }
                        // record the special "clone" value differently
                        if ($var == 'clone') {
                            $connectionData[$config][$connection]['clone'] = $value;
                            // store the rest of the values in an array for later
                        } else {
                            $connectionData[$config][$connection]['values'][$var] = $value;
                        }
                    } else {
                        throw InvalidConfigException::invalidConfigTypeFound(
                            $config,
                            array_keys(static::CONNECT_ARRAY_NAMES)
                        );
                    }
                }
            }
        }
        return [
            'groups' => $groups,
            'connections' => $connectionData,
        ];
    }
}
