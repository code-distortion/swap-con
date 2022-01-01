<?php

namespace CodeDistortion\SwapCon;

use CodeDistortion\SwapCon\Exceptions\ConnectionResolutionException;
use CodeDistortion\SwapCon\Exceptions\InvalidConfigException;

/**
 * Trait containing SwapCon's public "swap" methods.
 */
trait CloneMethodsTrait
{
    /**
     * Copy a BROADCASTING connection to another.
     *
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function cloneBroadcast(
        string $sourceCon = null, // PHP7.1 ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ) { // PHP7.1 ): void {
        $this->cloneConnection('broadcasting', $sourceCon, $newCon, $configData, $allowOverwrite);
    }

    /**
     * Copy a CACHE connection to another.
     *
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function cloneCache(
        string $sourceCon = null, // PHP7.1 ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ) { // PHP7.1 ): void {
        $this->cloneConnection('cache', $sourceCon, $newCon, $configData, $allowOverwrite);
    }

    /**
     * Copy a DATABASE connection to another.
     *
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function cloneDatabase(
        string $sourceCon = null, // PHP7.1 ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ) { // PHP7.1 ): void {
        $this->cloneConnection('database', $sourceCon, $newCon, $configData, $allowOverwrite);
    }

    /**
     * Copy a DATABASE connection to another.
     *
     * alias for copyDatabaseConnection(…).
     *
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function cloneDB(
        string $sourceCon = null, // PHP7.1 ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ) { // PHP7.1 ): void {
        $this->cloneDatabase($sourceCon, $newCon, $configData, $allowOverwrite);
    }

    /**
     * Copy a FILESYSTEMS connection to another.
     *
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function cloneFilesystem(
        string $sourceCon = null, // PHP7.1 ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ) { // PHP7.1 ): void {
        $this->cloneConnection('filesystems', $sourceCon, $newCon, $configData, $allowOverwrite);
    }

    /**
     * Copy a LOGGING connection to another.
     *
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function cloneLog(
        string $sourceCon = null, // PHP7.1 ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ) { // PHP7.1 ): void {
        $this->cloneConnection('logging', $sourceCon, $newCon, $configData, $allowOverwrite);
    }

    /**
     * Copy a QUEUE connection to another.
     *
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws InvalidConfigException        Thrown when the connection's template couldn't be found.
     * @throws ConnectionResolutionException Thrown when the connection already exists.
     */
    public function cloneQueue(
        string $sourceCon = null, // PHP7.1 ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ) { // PHP7.1 ): void {
        $this->cloneConnection('queue', $sourceCon, $newCon, $configData, $allowOverwrite);
    }





    /**
     * Build a new connection's details ready to be added to the config.
     *
     * @param string      $config         The config file to look into.
     * @param string|null $sourceCon      The connection to clone.
     * @param string      $newCon         The new connection to save.
     * @param array       $configData     The config values to add.
     * @param boolean     $allowOverwrite Allow a connection to be overwritten.
     * @return void
     */
    protected function cloneConnection(
        string $config,
        string $sourceCon = null, // PHP7.1 ?string $sourceCon,
        string $newCon,
        array $configData = [],
        bool $allowOverwrite = false
    ) { // PHP7.1 ): void {

        // build the new config data to save
        $configData = $this->buildConData($configData, $config, $sourceCon);

        // store the new connection in the config
        $this->addToConfig($config, $newCon, $configData, $allowOverwrite);
    }
}
