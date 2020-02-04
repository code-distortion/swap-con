<?php

namespace CodeDistortion\SwapCon;

use CodeDistortion\SwapCon\Exceptions\ConnectionResolutionException;
use CodeDistortion\SwapCon\Exceptions\InvalidConfigException;

/**
 * Trait containing SwapCon's public "use" methods.
 */
trait UseMethodsTrait
{

    /**
     * Change the active (default) BROADCASTING connection to another.
     *
     * Does NOT restore the original connection afterwards.
     *
     * @param string $name The connection to use.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function useBroadcast(string $name)
    {
        return $this->useConnection('broadcasting', $name);
    }

    /**
     * Change the active (default) CACHE connection to another.
     *
     * Does NOT restore the original connection afterwards.
     *
     * @param string $name The connection to use.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function useCache(string $name)
    {
        return $this->useConnection('cache', $name);
    }

    /**
     * Change the active (default) DATABASE connection to another.
     *
     * Does NOT restore the original connection afterwards.
     *
     * @param string $name The connection to use.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function useDatabase(string $name)
    {
        return $this->useConnection('database', $name);
    }

    /**
     * Change the active (default) DATABASE connection to another.
     *
     * Does NOT restore the original connection afterwards.
     * alias for useDatabase(…).
     *
     * @param string $name The connection to use.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function useDB(string $name)
    {
        return $this->useDatabase($name);
    }

    /**
     * Change the active (default) FILESYSTEMS connection to another.
     *
     * Does NOT restore the original connection afterwards.
     *
     * @param string $name The connection to use.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function useFilesystem(string $name)
    {
        return $this->useConnection('filesystems', $name);
    }

    /**
     * Change the active (default) LOGGING connection to another.
     *
     * Does NOT restore the original connection afterwards.
     *
     * @param string $name The connection to use.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function useLog(string $name)
    {
        return $this->useConnection('logging', $name);
    }

    /**
     * Change the active (default) QUEUE connection to another.
     *
     * Does NOT restore the original connection afterwards.
     *
     * @param string $name The connection to use.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    public function useQueue(string $name)
    {
        return $this->useConnection('queue', $name);
    }





    /**
     * Change the active (default) connection to another.
     *
     * Does NOT restore the original connection afterwards.
     *
     * @param string $config     The config file to look into.
     * @param string $connection The connection to use.
     * @return mixed
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    protected function useConnection(string $config, string $connection)
    {
        $newCon = $this->resolveConnection($config, $connection);
        config(["$config.default" => $newCon]);
        return true;
    }
}
