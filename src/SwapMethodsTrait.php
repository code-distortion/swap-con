<?php

namespace CodeDistortion\SwapCon;

use CodeDistortion\SwapCon\Exceptions\ConnectionResolutionException;
use CodeDistortion\SwapCon\Exceptions\InvalidConfigException;

/**
 * Trait containing SwapCon's public "swap" methods
 */
trait SwapMethodsTrait
{

    /**
     * Swap the active (default) BROADCASTING connection to another and run the given closure
     *
     * Restores the original connection afterwards.
     * @param string   $name    The connection to use.
     * @param callable $closure The closure to call once the connection has been swapped.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function swapBroadcast(string $name, callable $closure)
    {
        return $this->swapConnection('broadcasting', $name, $closure);
    }

    /**
     * Swap the active (default) CACHE connection to another and run the given closure
     *
     * Restores the original connection afterwards.
     * @param string   $name    The connection to use.
     * @param callable $closure The closure to call once the connection has been swapped.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function swapCache(string $name, callable $closure)
    {
        return $this->swapConnection('cache', $name, $closure);
    }

    /**
     * Swap the active (default) DATABASE connection to another and run the given closure
     *
     * Restores the original connection afterwards.
     * @param string   $name    The connection to use.
     * @param callable $closure The closure to call once the connection has been swapped.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function swapDatabase(string $name, callable $closure)
    {
        return $this->swapConnection('database', $name, $closure);
    }

    /**
     * Swap the active (default) DATABASE connection to another and run the given closure
     *
     * Restores the original connection afterwards.
     * alias for swapDatabase(…).
     * @param string   $name    The connection to use.
     * @param callable $closure The closure to call once the connection has been swapped.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function swapDB(string $name, callable $closure)
    {
        return $this->swapDatabase($name, $closure);
    }

    /**
     * Swap the active (default) FILESYSTEMS connection to another and run the given closure
     *
     * Restores the original connection afterwards.
     * @param string   $name    The connection to use.
     * @param callable $closure The closure to call once the connection has been swapped.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function swapFilesystem(string $name, callable $closure)
    {
        return $this->swapConnection('filesystems', $name, $closure);
    }

    /**
     * Swap the active (default) LOGGING connection to another and run the given closure
     *
     * Restores the original connection afterwards.
     * @param string   $name    The connection to use.
     * @param callable $closure The closure to call once the connection has been swapped.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function swapLog(string $name, callable $closure)
    {
        return $this->swapConnection('logging', $name, $closure);
    }

    /**
     * Swap the active (default) QUEUE connection to another and run the given closure
     *
     * Restores the original connection afterwards.
     * @param string   $name    The connection to use.
     * @param callable $closure The closure to call once the connection has been swapped.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function swapQueue(string $name, callable $closure)
    {
        return $this->swapConnection('queue', $name, $closure);
    }





    /**
     * Swap the active (default) connection to another and run the given closure
     *
     * Restores the original connection afterwards.
     * @param string   $config     The config file to look into.
     * @param string   $connection The connection to use.
     * @param callable $closure    The closure to call once the connection has been swapped.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    protected function swapConnection(string $config, string $connection, callable $closure)
    {
        $initialCon = config("$config.default");
        $newCon = $this->resolveConnection($config, $connection);

        // point to this new connection
        config(["$config.default" => $newCon]);
        try {
            return $closure();
        } finally {
            // return to the original connection
            config(["$config.default" => $initialCon]);
        }
    }
}
