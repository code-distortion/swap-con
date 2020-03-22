<?php

namespace CodeDistortion\SwapCon;

use App;
use CodeDistortion\SwapCon\Exceptions\InvalidConfigException;
use CodeDistortion\SwapCon\Exceptions\ConnectionResolutionException;
use DB;

/**
 * SwapCon - Manage swapping between different connections (like databases).
 */
class SwapCon
{
    use BuildConfigTrait;
    use CopyMethodsTrait;
    use ResolveMethodsTrait;
    use SwapMethodsTrait;
    use UpdateMethodsTrait;
    use UseMethodsTrait;

    /**
     * The name of this package's config file.
     *
     * @var string
     */
    protected const CONFIG_NAME = 'code-distortion.swapcon';

    /**
     * The prefix given to swap-con .env settings.
     *
     * @var string
     */
    protected const ENV_PREFIX = 'SWAPCON';

    /**
     * The names of the "connections" array inside each config file that can be added to.
     *
     * @var array
     */
    protected const CONNECT_ARRAY_NAMES = [
        'broadcasting' => 'connections',
        'cache' => 'stores',
        'database' => 'connections',
        'filesystems' => 'disks',
        'logging' => 'channels',
        'queue' => 'connections',
    ];

    /**
     * The maximum number of times to recurse before throwing an exception.
     *
     * Used to avoid circular references.
     *
     * @var integer
     */
    protected const MAX_RECURSE_DEPTH = 32;

    /**
     * An internal cache of the replacement connections that have already been used.
     *
     * @var array
     */
    protected $usedReplacements = [];





    /**
     * Choose a connection to use - handles the recursion.
     *
     * This will add the new connection's values to the config if needed.
     *
     * @param string      $config      The config file to look into.
     * @param string|null $connection  The connection to resolve.
     * @param string|null $origConName The connection originally requested.
     * @param integer     $depth       The current recurse depth - to allow detection of circular references.
     * @return string
     * @throws ConnectionResolutionException Thrown when circular references end up looping indefinitely.
     */
    protected function resolveConnectionRecurse(
        string $config,
        ?string $connection,
        ?string $origConName,
        int $depth
    ): string {

        // check to see if error conditions have been met:
        // has reached the end without resolving
        if (is_null($connection)) {
            throw ConnectionResolutionException::unresolvable($config, (string) $origConName);
        }
        // has tried too many times and is probably looping indefinitely
        if ($depth >= static::MAX_RECURSE_DEPTH - 1) {
            throw ConnectionResolutionException::maxRecurseDepth($config, (string) $origConName);
        }



        // if the connection with this name already exists (in Laravel's config), return it as-is
        $conGroup = static::CONNECT_ARRAY_NAMES[$config];
        if (config("$config.$conGroup.$connection")) {
            return $connection;
        }



        // if the result for this connection has been recorded already then use that, even if it resolved to null
        $resolvedConName = null;
        if ((array_key_exists($config, $this->usedReplacements))
        && (array_key_exists($connection, $this->usedReplacements[$config]))) {

            $resolvedConName = $this->usedReplacements[$config][$connection];

        // otherwise try to resolve it now
        } else {

            // see if a SWAPCON CONNECTION with this name exists
            if ($conInfo = config(static::CONFIG_NAME.".connections.$config.$connection")) {

                // add it to the Laravel config ready for use
                $resolvedConName = $conInfo['name'];
                $this->copyConnection($config, $conInfo['clone'], $resolvedConName, $conInfo['values']);

            // or see if a SWAPCON GROUP with this name exists
            } elseif ($groupConnections = config(static::CONFIG_NAME.".groups.$config.$connection")) {

                // pick one - AT RANDOM
                $resolvedConName = $groupConnections[array_rand($groupConnections)];

            // or see if a REUSE FALLBACK with this name exists
            } elseif ($reuseConName = config(static::CONFIG_NAME.".fallbacks.reuse.$config.$connection")) {

                $resolvedConName = $this->resolveConnectionRecurse($config, $reuseConName, $origConName, $depth + 1);

            // or see if a CLONE FALLBACK with this name exists
            } elseif ($cloneFrom = config(static::CONFIG_NAME.".fallbacks.clone.$config.$connection")) {

                // resolve this "cloneFrom" connection
                if ($cloneFrom = $this->resolveConnectionRecurse($config, $cloneFrom, $origConName, $depth + 1)) {

                    // "clone" the connection from Laravel's config
                    // add it to the Laravel config under the new name ready for use
                    $resolvedConName = $connection;
                    $this->copyConnection($config, $cloneFrom, $resolvedConName, []);
                }
            }
        }

        // cache the outcome
        $this->usedReplacements[$config][$connection] = $resolvedConName;

        // further resolve the the connection that was found
        return $this->resolveConnectionRecurse($config, $resolvedConName, $origConName, $depth + 1);
    }

    /**
     * Build a new connection's details ready to be added to the config.
     *
     * @param array       $configData The config values to add.
     * @param string|null $config     The config file to look into.
     * @param string|null $cloneFrom  The name of the connection to clone.
     * @return array
     * @throws InvalidConfigException Thrown when the connection's clone template couldn't be found.
     */
    protected function buildConData(
        array $configData,
        ?string $config,
        ?string $cloneFrom
    ): array {

        // use an existing connection as a template to build the new connection's values
        if (($config) && ($cloneFrom)) {
            $conGroup = static::CONNECT_ARRAY_NAMES[$config];
            $values = config("$config.$conGroup.$cloneFrom");
            if (is_array($values)) {
                return array_merge($values, $configData);
            }
            throw InvalidConfigException::templateNotFound($config, $cloneFrom);
        }
        // or just return the connection's values as is
        return $configData;
    }

    /**
     * Store the given connection in Laravel's config.
     *
     * @param string  $config         The config file to look into.
     * @param string  $connection     The name of the connection to save.
     * @param array   $configData     The SwapCon connection details.
     * @param boolean $allowOverwrite Allow a connection to be overwritten.
     * @return void
     * @throws ConnectionResolutionException Thrown when trying to save over a connection that already exists in
     *                                       Laravel's config.
     */
    protected function addToConfig(
        string $config,
        string $connection,
        array $configData,
        bool $allowOverwrite = false
    ): void {

        $conGroup = static::CONNECT_ARRAY_NAMES[$config];
        $connectionExists = (bool) config("$config.$conGroup.$connection");

        if ((!$connectionExists) || ($allowOverwrite)) {

            // update the config
            config(["$config.$conGroup.$connection" => $configData]);

            // perform some clean-up where needed
            if ($config == 'database') {

                if (in_array($connection, array_keys(DB::getConnections()))) {
                    DB::purge($connection);
                }
            }
        } else {
            throw ConnectionResolutionException::connectionAlreadyExists($config, $connection);
        }
    }
}
