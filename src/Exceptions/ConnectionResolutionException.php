<?php

namespace CodeDistortion\SwapCon\Exceptions;

/**
 * Exception for when connections cannot be resolved.
 */
class ConnectionResolutionException extends SwapConException
{
    /**
     * Return a new instance when circular references end up looping indefinitely.
     *
     * @param string $config     The config type used.
     * @param string $connection The connection that was specified.
     * @return static
     */
    public static function maxRecurseDepth(string $config, string $connection): self
    {
        return new static(
            'The connection "'.$config.'.'.$connection.'" could not be resolved'
            .' - it seems to have circular references that loop indefinitely'
        );
    }

    /**
     * Return a new instance when the given connection could not be found.
     *
     * @param string $config     The config type used.
     * @param string $connection The connection that was specified.
     * @return static
     */
    public static function unresolvable(string $config, string $connection): self
    {
        return new static('The connection "'.$config.'.'.$connection.'" could not be resolved');
    }

    /**
     * Return a new instance when a connection already exists.
     *
     * @param string $config     The config type used.
     * @param string $connection The connection that was specified.
     * @return static
     */
    public static function connectionAlreadyExists(string $config, string $connection): self
    {
        return new static('The connection "'.$config.'.'.$connection.'" cannot be stored, it already exists');
    }
}
