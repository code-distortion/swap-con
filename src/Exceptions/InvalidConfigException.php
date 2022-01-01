<?php

namespace CodeDistortion\SwapCon\Exceptions;

/**
 * Exception for when invalid config details are used.
 */
class InvalidConfigException extends SwapConException
{
    /**
     * Return a new instance when an unexpected config-type was specified.
     *
     * @param string $config    The name of the unexpected config-type used.
     * @param array  $available The valid config-types.
     * @return static
     */
    public static function invalidConfigTypeFound(string $config, array $available): self
    {
        return new static(
            'The config type "' . $config . '" is not valid (try one of: ' . implode(', ', $available) . ')'
        );
    }

    /**
     * Return a new instance when multiple templates are given for one connection.
     *
     * @param string $config The config-type used.
     * @param string $clone  The connection to clone.
     * @return static
     */
    public static function templateNotFound(string $config, string $clone): self
    {
        return new static('The ' . $config . ' connection to clone "' . $clone . '" was not found');
    }

    /**
     * Return a new instance when a group looks empty.
     *
     * @param string $config The config-type the group belongs to.
     * @param string $group  The group that's empty.
     * @return static
     */
    public static function noConnectionsInGroup(string $config, string $group): self
    {
        return new static('The ' . $config . ' group "' . $group . '" is empty');
    }
}
