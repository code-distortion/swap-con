<?php

namespace CodeDistortion\SwapCon;

use CodeDistortion\SwapCon\Exceptions\ConnectionResolutionException;
use CodeDistortion\SwapCon\Exceptions\InvalidConfigException;

/**
 * Trait containing SwapCon's public "swap" methods.
 */
trait UpdateMethodsTrait
{

    /**
     * Update value/s in an existing BROADCASTING connection.
     *
     * @param string $connection The connection to update.
     * @param array  $configData The config values to add.
     * @return void
     * @throws InvalidConfigException Thrown when the connection's clone template couldn't be found.
     */
    public function updateBroadcast(
        string $connection,
        array $configData
    ): void {
        $this->updateConnection('broadcasting', $connection, $configData);
    }

    /**
     * Update value/s in an existing CACHE connection.
     *
     * @param string $connection The connection to update.
     * @param array  $configData The config values to add.
     * @return void
     * @throws InvalidConfigException Thrown when the connection's clone template couldn't be found.
     */
    public function updateCache(
        string $connection,
        array $configData
    ): void {
        $this->updateConnection('cache', $connection, $configData);
    }

    /**
     * Update value/s in an existing DATABASE connection.
     *
     * @param string $connection The connection to update.
     * @param array  $configData The config values to add.
     * @return void
     * @throws InvalidConfigException Thrown when the connection's clone template couldn't be found.
     */
    public function updateDatabase(
        string $connection,
        array $configData
    ): void {
        $this->updateConnection('database', $connection, $configData);
    }

    /**
     * Update value/s in an existing DATABASE connection.
     *
     * alias for updateDatabase(…).
     *
     * @param string $connection The connection to update.
     * @param array  $configData The config values to add.
     * @return void
     * @throws InvalidConfigException Thrown when the connection's clone template couldn't be found.
     */
    public function updateDB(
        string $connection,
        array $configData
    ): void {
        $this->updateDatabase($connection, $configData);
    }

    /**
     * Update value/s in an existing FILESYSTEMS connection.
     *
     * @param string $connection The connection to update.
     * @param array  $configData The config values to add.
     * @return void
     * @throws InvalidConfigException Thrown when the connection's clone template couldn't be found.
     */
    public function updateFilesystem(
        string $connection,
        array $configData
    ): void {
        $this->updateConnection('filesystems', $connection, $configData);
    }

    /**
     * Update value/s in an existing LOGGING connection.
     *
     * @param string $connection The connection to update.
     * @param array  $configData The config values to add.
     * @return void
     * @throws InvalidConfigException Thrown when the connection's clone template couldn't be found.
     */
    public function updateLog(
        string $connection,
        array $configData
    ): void {
        $this->updateConnection('logging', $connection, $configData);
    }

    /**
     * Update value/s in an existing QUEUE connection.
     *
     * @param string $connection The connection to update.
     * @param array  $configData The config values to add.
     * @return void
     * @throws InvalidConfigException Thrown when the connection's clone template couldn't be found.
     */
    public function updateQueue(
        string $connection,
        array $configData
    ): void {
        $this->updateConnection('queue', $connection, $configData);
    }







    /**
     * Update value/s in an existing connection.
     *
     * @param string $config     The config file to look into.
     * @param string $connection The connection to update.
     * @param array  $configData The config values to add.
     * @return void
     * @throws InvalidConfigException Thrown when the connection's clone template couldn't be found.
     */
    protected function updateConnection(
        string $config,
        string $connection,
        array $configData
    ): void {
        $this->copyConnection($config, $connection, $connection, $configData, true);
    }
}
