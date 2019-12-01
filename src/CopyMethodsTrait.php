<?php

namespace CodeDistortion\SwapCon;

use CodeDistortion\SwapCon\Exceptions\ConnectionResolutionException;
use CodeDistortion\SwapCon\Exceptions\InvalidConfigException;

/**
 * Trait containing SwapCon's public "swap" methods
 */
trait CopyMethodsTrait
{

    /**
     * Copy a BROADCASTING connection to another
     *
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function copyBroadcast(
        ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ): void {
        $this->copyConnection('broadcasting', $sourceCon, $newCon, $configData, $allowOverwrite);
    }

    /**
     * Copy a CACHE connection to another
     *
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function copyCache(
        ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ): void {
        $this->copyConnection('cache', $sourceCon, $newCon, $configData, $allowOverwrite);
    }

    /**
     * Copy a DATABASE connection to another
     *
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function copyDatabase(
        ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ): void {
        $this->copyConnection('database', $sourceCon, $newCon, $configData, $allowOverwrite);
    }

    /**
     * Copy a DATABASE connection to another
     *
     * alias for copyDatabaseConnection(…).
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function copyDB(
        ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ): void {
        $this->copyDatabase($sourceCon, $newCon, $configData, $allowOverwrite);
    }

    /**
     * Copy a FILESYSTEMS connection to another
     *
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function copyFilesystem(
        ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ): void {
        $this->copyConnection('filesystems', $sourceCon, $newCon, $configData, $allowOverwrite);
    }

    /**
     * Copy a LOGGING connection to another
     *
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function copyLog(
        ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ): void {
        $this->copyConnection('logging', $sourceCon, $newCon, $configData, $allowOverwrite);
    }

    /**
     * Copy a QUEUE connection to another
     *
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function copyQueue(
        ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ): void {
        $this->copyConnection('queue', $sourceCon, $newCon, $configData, $allowOverwrite);
    }





    /**
     * Build a new connection's details ready to be added to the config
     *
     * @param string      $config         The config file to look into.
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     */
    protected function copyConnection(
        string $config,
        ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ): void {

        // build the new config data to save
        $configData = $this->buildConData($configData, $config, $sourceCon);

        // store the new connection in the config
        $this->addToConfig($config, $newCon, $configData, $allowOverwrite);
    }
}
