<?php

namespace CodeDistortion\SwapCon;

use CodeDistortion\SwapCon\Exceptions\ConnectionResolutionException;
use CodeDistortion\SwapCon\Exceptions\InvalidConfigException;

/**
 * Trait containing SwapCon's public "resolve" methods.
 */
trait ResolveMethodsTrait
{
    /**
     * Pick a BROADCASTING connection for the given connection name.
     *
     * This will add the new connection's values to the config if needed.
     *
     * @param string $name The connection to use.
     * @return string
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function resolveBroadcast(string $name): string
    {
        return $this->resolveConnection('broadcasting', $name);
    }

    /**
     * Pick a CACHE connection for the given connection name.
     *
     * This will add the new connection's values to the config if needed.
     *
     * @param string $name The connection to use.
     * @return string
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function resolveCache(string $name): string
    {
        return $this->resolveConnection('cache', $name);
    }

    /**
     * Pick a DATABASE connection for the given connection name.
     *
     * This will add the new connection's values to the config if needed.
     *
     * @param string $name The connection to use.
     * @return string
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function resolveDatabase(string $name): string
    {
        return $this->resolveConnection('database', $name);
    }

    /**
     * Pick a DATABASE connection for the given connection name.
     *
     * This will add the new connection's values to the config if needed.
     * alias for resolveDatabase(…).
     *
     * @param string $name The connection to use.
     * @return string
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function resolveDB(string $name): string
    {
        return $this->resolveDatabase($name);
    }

    /**
     * Pick a FILESYSTEMS connection for the given connection name.
     *
     * This will add the new connection's values to the config if needed.
     *
     * @param string $name The connection to use.
     * @return string
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function resolveFilesystem(string $name): string
    {
        return $this->resolveConnection('filesystems', $name);
    }

    /**
     * Pick a LOGGING connection for the given connection name.
     *
     * This will add the new connection's values to the config if needed.
     *
     * @param string $name The connection to use.
     * @return string
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function resolveLog(string $name): string
    {
        return $this->resolveConnection('logging', $name);
    }

    /**
     * Pick a QUEUE connection for the given connection name.
     *
     * This will add the new connection's values to the config if needed.
     *
     * @param string $name The connection to use.
     * @return string
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function resolveQueue(string $name): string
    {
        return $this->resolveConnection('queue', $name);
    }





    /**
     * Choose a connection to use.
     *
     * @param string $config     The config file to look into.
     * @param string $connection The connection to resolve.
     * @return string
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    protected function resolveConnection(
        string $config,
        string $connection
    ): string {
        return $this->resolveConnectionRecurse($config, $connection, $connection, 0);
    }





    /**
     * Choose a connection to use.
     *
     * @param string $config The config to resolve-all for (optional).
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function resolveAll(string $config = null) // PHP7.1 ): void
    {
        $configs = config(static::CONFIG_NAME . ".connections");
        if ($configs) {
            foreach ($configs as $curConfig => $connections) {
                if ((is_null($config)) || ($curConfig == $config)) {
                    foreach (array_keys($connections) as $name) {
                        $this->resolveConnection($curConfig, (string) $name);
                    }
                }
            }
        }
    }
}
