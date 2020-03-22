<?php

namespace CodeDistortion\SwapCon\Tests\Laravel\Unit;

use App;
use CodeDistortion\SwapCon\Exceptions\ConnectionResolutionException;
use CodeDistortion\SwapCon\Exceptions\InvalidConfigException;
use CodeDistortion\SwapCon\Tests\Laravel\TestCase;
use CodeDistortion\SwapCon\SwapConFacade as SwapCon;
use Exception;
use PHPUnit\Framework\Constraint\Exception as ConstraintException;

/**
 * Test the SwapCon library class.
 *
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class SwapConUnitTest extends TestCase
{

    /**
     * Provide data for the test_that_exceptions_are_thrown_when_clone_sources_arent_present test
     * Provide data for the test_that_circular_references_generates_an_exception test
     * Provide data for the test_that_exceptions_are_thrown_when_connections_are_unresolvable test
     * Provide data for the test_that_connections_resolve_properly test
     * Provide data for the test_that_connections_can_be_copied test
     * Provide data for the test_that_connections_can_be_updated test
     * Provide data for the test_that_connections_can_be_altered test
     *
     * @return array
     */
    public function staticMethodDataProvider(): array
    {
        return [
            'broadcast' => [
                'copyBroadcast',
                'updateBroadcast',
                'resolveBroadcast',
                'useBroadcast',
                'swapBroadcast',
                'broadcasting',
                'connections',
            ],
            'cache' => [
                'copyCache',
                'updateCache',
                'resolveCache',
                'useCache',
                'swapCache',
                'cache',
                'stores',
            ],
            'database' => [
                'copyDatabase',
                'updateDatabase',
                'resolveDatabase',
                'useDatabase',
                'swapDatabase',
                'database',
                'connections',
            ],
            'DB' => [
                'copyDB',
                'updateDB',
                'resolveDB',
                'useDB',
                'swapDB',
                'database',
                'connections',
            ],
            'filesystem' => [
                'copyFilesystem',
                'updateFilesystem',
                'resolveFilesystem',
                'useFilesystem',
                'swapFilesystem',
                'filesystems',
                'disks',
            ],
            'log' => [
                'copyLog',
                'updateLog',
                'resolveLog',
                'useLog',
                'swapLog',
                'logging',
                'channels',
            ],
            'queue' => [
                'copyQueue',
                'updateQueue',
                'resolveQueue',
                'useQueue',
                'swapQueue',
                'queue',
                'connections',
            ],
        ];
    }

    /**
     * Build and set the swapcon config.
     *
     * @param array  $reuse       The fallback 'reuse' section of the config.
     * @param array  $clone       The fallback 'clone' section of the config.
     * @param string $envFilename The .env filename to load.
     * @return array
     */
    protected function buildConfig(array $reuse, array $clone, string $envFilename): array
    {
        $configData = [
            'fallbacks' => [
                'reuse' => $reuse,
                'clone' => $clone,
            ],
        ];
        $envPath = realpath(__DIR__.'/../../');
        $configData = array_merge(
            $configData,
            SwapCon::buildConfig($envPath, $envFilename)
        );
        config(['code-distortion.swapcon' => $configData]); // store the values in Laravel's config

        return $configData;
    }





    /**
     * Test that the settings in the .env file are interpreted correctly.
     *
     * @test
     * @return void
     */
    public function test_that_the_env_file_is_interpreted_properly() // PHP7.1 ): void
    {

        $expectedConfig = [
            'fallbacks' => [
                'reuse' => [],
                'clone' => [],
            ],
            'groups' => [],
            'connections' => [],
        ];
        foreach (['broadcasting', 'cache', 'database', 'filesystems', 'logging', 'queue'] as $configName) {
            $expectedConfig['groups'][$configName] = [
                'con' => [
                    'con1',
                    'con2',
                ],
            ];
            $expectedConfig['connections'][$configName] = [
                'con1' => [
                    'name' => 'con1',
                    'clone' => 'test1',
                    'values' => [
                        'myval' => 'con1',
                    ],
                ],
                'con2' => [
                    'name' => 'con2',
                    'clone' => null,
                    'values' => [
                        'myval' => 'con2',
                    ],
                ],
            ];
        }

        $reuse = $clone = [];
        $config = $this->buildConfig($reuse, $clone, '.env.test1');

        $this->assertSame($expectedConfig, $config);
    }

    /**
     * Test that exceptions are thrown properly when invalid .env values are found.
     *
     * @test
     * @return void
     */
    public function test_that_env_file_errors_throw_exceptions() // PHP7.1 ): void
    {
        // PHPUnit\Framework\Constraint\Exception is required by jchook/phpunit-assert-throws
        if (class_exists(ConstraintException::class)) {

            // .env contains a setting with an invalid config type
            $this->assertThrows(InvalidConfigException::class, function () {
                $reuse = $clone = [];
                $this->buildConfig($reuse, $clone, '.env.test-invalid1');
            });

            // .env contains a group setting containing no connections
            $this->assertThrows(InvalidConfigException::class, function () {
                $reuse = $clone = [];
                $this->buildConfig($reuse, $clone, '.env.test-invalid2');
            });
        }
    }

    /**
     * Test that exceptions are thrown properly when invalid .env values are found.
     *
     * @test
     * @dataProvider staticMethodDataProvider
     * @param string $copyMethod           The 'copy' method to run.
     * @param string $updateMethod         The 'update' method to run.
     * @param string $resolveMethod        The 'resolve' method to run.
     * @param string $useMethod            The 'use' method to run.
     * @param string $swapMethod           The 'swap' method to run.
     * @param string $configName           The name of the config used.
     * @param string $configConnectionName The name of the connection in the config.
     * @return void
     */
    public function test_that_exceptions_are_thrown_when_clone_sources_arent_present(
        string $copyMethod,
        string $updateMethod,
        string $resolveMethod,
        string $useMethod,
        string $swapMethod,
        string $configName,
        string $configConnectionName
    ) { // PHP7.1 ): void {

        // PHPUnit\Framework\Constraint\Exception is required by jchook/phpunit-assert-throws
        if (class_exists(ConstraintException::class)) {

            $reuse = $clone = [];
            $this->buildConfig($reuse, $clone, '.env.test1');

            $this->assertThrows(InvalidConfigException::class, function () use ($resolveMethod) {
                $callable = ['SwapCon', $resolveMethod];
                if (is_callable($callable)) { // to please phpstan
                    forward_static_call_array($callable, ['con1']);
                }
            });
        }
    }

    /**
     * Test that exceptions are thrown when circular references are found.
     *
     * @test
     * @dataProvider staticMethodDataProvider
     * @param string $copyMethod           The 'copy' method to run.
     * @param string $updateMethod         The 'update' method to run.
     * @param string $resolveMethod        The 'resolve' method to run.
     * @param string $useMethod            The 'use' method to run.
     * @param string $swapMethod           The 'swap' method to run.
     * @param string $configName           The name of the config used.
     * @param string $configConnectionName The name of the connection in the config.
     * @return void
     */
    public function test_that_circular_references_generates_an_exception(
        string $copyMethod,
        string $updateMethod,
        string $resolveMethod,
        string $useMethod,
        string $swapMethod,
        string $configName,
        string $configConnectionName
    ) { // PHP7.1 ): void {

        // PHPUnit\Framework\Constraint\Exception is required by jchook/phpunit-assert-throws
        if (class_exists(ConstraintException::class)) {

            $reuse = $clone = [];
            $this->buildConfig($reuse, $clone, '.env.test-invalid3');

            $this->assertThrows(ConnectionResolutionException::class, function () use ($resolveMethod) {
                $callable = ['SwapCon', $resolveMethod];
                if (is_callable($callable)) { // to please phpstan
                    forward_static_call_array($callable, ['group1']);
                }
            });
        }
    }

    /**
     * Test that exceptions are thrown properly when invalid .env values are found.
     *
     * @test
     * @dataProvider staticMethodDataProvider
     * @param string $copyMethod           The 'copy' method to run.
     * @param string $updateMethod         The 'update' method to run.
     * @param string $resolveMethod        The 'resolve' method to run.
     * @param string $useMethod            The 'use' method to run.
     * @param string $swapMethod           The 'swap' method to run.
     * @param string $configName           The name of the config used.
     * @param string $configConnectionName The name of the connection in the config.
     * @return void
     */
    public function test_that_exceptions_are_thrown_when_connections_are_unresolvable(
        string $copyMethod,
        string $updateMethod,
        string $resolveMethod,
        string $useMethod,
        string $swapMethod,
        string $configName,
        string $configConnectionName
    ) { // PHP7.1 ): void {

        // PHPUnit\Framework\Constraint\Exception is required by jchook/phpunit-assert-throws
        if (class_exists(ConstraintException::class)) {

            $reuse = $clone = [];
            $this->buildConfig($reuse, $clone, '.env.test1');

            $this->assertThrows(ConnectionResolutionException::class, function () use ($resolveMethod) {
                $callable = ['SwapCon', $resolveMethod];
                if (is_callable($callable)) { // to please phpstan
                    forward_static_call_array($callable, ['group1']);
                }
            });
        }
    }

    /**
     * Test that connection groups resolve to connections properly.
     *
     * @test
     * @dataProvider staticMethodDataProvider
     * @param string $copyMethod           The 'copy' method to run.
     * @param string $updateMethod         The 'update' method to run.
     * @param string $resolveMethod        The 'resolve' method to run.
     * @param string $useMethod            The 'use' method to run.
     * @param string $swapMethod           The 'swap' method to run.
     * @param string $configName           The name of the config used.
     * @param string $configConnectionName The name of the connection in the config.
     * @return void
     */
    public function test_that_connections_resolve_properly(
        string $copyMethod,
        string $updateMethod,
        string $resolveMethod,
        string $useMethod,
        string $swapMethod,
        string $configName,
        string $configConnectionName
    ) { // PHP7.1 ): void {

        $resolveCallable = ['SwapCon', $resolveMethod];
        $copyCallable = ['SwapCon', $copyMethod];
        if ((is_callable($resolveCallable)) && (is_callable($copyCallable))) { // to please phpstan

            // set up the swap-con config, as if it was built from the .env file
            $swapConConfig = [
                'fallbacks'   => [
                    'reuse' => [
                        $configName => [
                            'con1' => 'test1',
                            'con3' => 'test1',
                        ],
                    ],
                    'clone' => [
                        $configName => [
                            'con2' => 'test1',
                            'con4' => 'test1',
                        ],
                    ],
                ],
                'groups'      => [
                    $configName => [
                        'con' => [
                            'con1',
                            'con2',
                        ],
                    ],
                ],
                'connections' => [
                    $configName => [
                        'con1' => [
                            'name'   => 'con1',
                            'clone'  => 'test1',
                            'values' => [
                                'myval' => 'con1',
                            ],
                        ],
                        'con2' => [
                            'name'   => 'con2',
                            'clone'  => null,
                            'values' => [
                                'myval' => 'con2',
                            ],
                        ],
                    ],
                ],
            ];
            config(['code-distortion.swapcon' => $swapConConfig]);


            // set up the original connection to copy from
            $expected = ['a' => 'b'];
            forward_static_call_array($copyCallable, [null, 'test1', $expected]);


            // resolve a connection directly - with a 'clone'
            $resolved = forward_static_call_array($resolveCallable, ['con1']);
            $this->assertSame('con1', $resolved);
            $this->assertSame(['a' => 'b', 'myval' => 'con1'], config("$configName.$configConnectionName.$resolved"));

            // resolve a connection directly - without a 'clone'
            $resolved = forward_static_call_array($resolveCallable, ['con2']);
            $this->assertSame('con2', $resolved);
            $this->assertSame(['myval' => 'con2'], config("$configName.$configConnectionName.$resolved"));

            // resolve a connection from a group
            $resolved = forward_static_call_array($resolveCallable, ['con']);
            $this->assertTrue(in_array($resolved, ['con1', 'con2']));

            // test that a group resolves to the same connection each time
            for ($count = 0; $count < 20; $count++) {
                $resolved2 = forward_static_call_array($resolveCallable, ['con']);
                $this->assertSame($resolved, $resolved2);
            }

            // test that the 'reuse' fallbacks get picked up when the connection doesn't exist
            $resolved = forward_static_call_array($resolveCallable, ['con3']);
            $this->assertSame('test1', $resolved);
            $this->assertSame($expected, config("$configName.$configConnectionName.$resolved"));

            // test that the 'reuse' fallbacks get picked up when the connection doesn't exist
            $resolved = forward_static_call_array($resolveCallable, ['con4']);
            $this->assertSame('con4', $resolved);
            $this->assertSame($expected, config("$configName.$configConnectionName.$resolved"));



            // PHPUnit\Framework\Constraint\Exception is required by jchook/phpunit-assert-throws
            if (class_exists(ConstraintException::class)) {

                // test that an exception is thrown when a connection isn't found
                $this->assertThrows(
                    ConnectionResolutionException::class,
                    function () use ($resolveCallable) {
                        forward_static_call_array($resolveCallable, ['con5']);
                    }
                );
            }
        }
    }

    /**
     * Test that connection copying works properly.
     *
     * @test
     * @dataProvider staticMethodDataProvider
     * @param string $copyMethod           The 'copy' method to run.
     * @param string $updateMethod         The 'update' method to run.
     * @param string $resolveMethod        The 'resolve' method to run.
     * @param string $useMethod            The 'use' method to run.
     * @param string $swapMethod           The 'swap' method to run.
     * @param string $configName           The name of the config used.
     * @param string $configConnectionName The name of the connection in the config.
     * @return void
     */
    public function test_that_connections_can_be_copied(
        string $copyMethod,
        string $updateMethod,
        string $resolveMethod,
        string $useMethod,
        string $swapMethod,
        string $configName,
        string $configConnectionName
    ) { // PHP7.1 ): void {

        $copyCallable = ['SwapCon', $copyMethod];
        if (is_callable($copyCallable)) { // to please phpstan

            // set up the original connection to copy from
            $expected = ['a' => 'b'];
            forward_static_call_array($copyCallable, [null, 'test1', $expected]);
            $this->assertSame($expected, config("$configName.$configConnectionName.test1"));

            // copy this connection - with no values
            forward_static_call_array($copyCallable, ['test1', 'test2']);
            $this->assertSame($expected, config("$configName.$configConnectionName.test2"));

            // PHPUnit\Framework\Constraint\Exception is required by jchook/phpunit-assert-throws
            if (class_exists(ConstraintException::class)) {

                // an exception will be thrown because the connection already exists
                $this->assertThrows(
                    ConnectionResolutionException::class,
                    function () use ($copyCallable) {
                        forward_static_call_array($copyCallable, ['test1', 'test2', []]);
                    }
                );
            }

            // won't throw an exception because the flag is set
            forward_static_call_array($copyCallable, ['test1', 'test2', [], true]);
            $this->assertSame($expected, config("$configName.$configConnectionName.test2"));

            // copy this connection - with extra values
            $expected = ['a' => 'c', 'd' => 'e'];
            forward_static_call_array($copyCallable, ['test1', 'test3', $expected]);
            $this->assertSame($expected, config("$configName.$configConnectionName.test3"));
        }
    }

    /**
     * Test that connection updating works properly.
     *
     * @test
     * @dataProvider staticMethodDataProvider
     * @param string $copyMethod           The 'copy' method to run.
     * @param string $updateMethod         The 'update' method to run.
     * @param string $resolveMethod        The 'resolve' method to run.
     * @param string $useMethod            The 'use' method to run.
     * @param string $swapMethod           The 'swap' method to run.
     * @param string $configName           The name of the config used.
     * @param string $configConnectionName The name of the connection in the config.
     * @return void
     */
    public function test_that_connections_can_be_updated(
        string $copyMethod,
        string $updateMethod,
        string $resolveMethod,
        string $useMethod,
        string $swapMethod,
        string $configName,
        string $configConnectionName
    ) { // PHP7.1 ): void {

        $updateCallable = ['SwapCon', $updateMethod];
        $copyCallable = ['SwapCon', $copyMethod];
        if ((is_callable($updateCallable)) && (is_callable($copyCallable))) { // to please phpstan

            // set up the original connection to copy from
            $expected = ['a' => 'b'];
            forward_static_call_array($copyCallable, [null, 'test1', $expected]);

            // update this connection - with no values
            forward_static_call_array($updateCallable, ['test1', []]);
            $this->assertSame($expected, config("$configName.$configConnectionName.test1"));

            // update this connection - with new values
            $expected = ['a' => 'c', 'd' => 'e'];
            forward_static_call_array($updateCallable, ['test1', $expected]);
            $this->assertSame($expected, config("$configName.$configConnectionName.test1"));
        }
    }

    /**
     * Test that the 'swap' methods work properly.
     *
     * @test
     * @dataProvider staticMethodDataProvider
     * @param string $copyMethod           The 'copy' method to run.
     * @param string $updateMethod         The 'update' method to run.
     * @param string $resolveMethod        The 'resolve' method to run.
     * @param string $useMethod            The 'use' method to run.
     * @param string $swapMethod           The 'swap' method to run.
     * @param string $configName           The name of the config used.
     * @param string $configConnectionName The name of the connection in the config.
     * @return void
     */
    public function test_that_connections_can_be_altered(
        string $copyMethod,
        string $updateMethod,
        string $resolveMethod,
        string $useMethod,
        string $swapMethod,
        string $configName,
        string $configConnectionName
    ) { // PHP7.1 ): void {

        $useCallable = ['SwapCon', $useMethod];
        $swapCallable = ['SwapCon', $swapMethod];
        $copyCallable = ['SwapCon', $copyMethod];
        if ((is_callable($useCallable))
        && (is_callable($swapCallable))
        && (is_callable($copyCallable))) { // to please phpstan

            // set up the available connections
            forward_static_call_array($copyCallable, [null, 'test1', ['a' => 'b']]);
            forward_static_call_array($copyCallable, [null, 'test2', ['a' => 'b']]);
            forward_static_call_array($copyCallable, [null, 'test3', ['a' => 'b']]);

            // 'use' the initial connection
            forward_static_call_array($useCallable, ['test1']);
            $this->assertSame('test1', config("$configName.default"));


            // call the 'swap' method - two levels deep
            $callbackConnection1a = $callbackConnection1b = $callbackConnection2 = null;
            $callback = function () use (
                &$callbackConnection1a,
                &$callbackConnection1b,
                &$callbackConnection2,
                $configName,
                $swapCallable
            ) {
                $callbackConnection1a = config("$configName.default");

                // call the 2nd level
                $callback = function () use (&$callbackConnection2, $configName) {
                    $callbackConnection2 = config("$configName.default");
                };
                forward_static_call_array($swapCallable, ['test3', $callback]);

                $callbackConnection1b = config("$configName.default");
            };
            forward_static_call_array($swapCallable, ['test2', $callback]);
            $this->assertSame('test2', $callbackConnection1a);
            $this->assertSame('test2', $callbackConnection1b);
            $this->assertSame('test3', $callbackConnection2);

            // check the connection was returned to normal
            $this->assertSame('test1', config("$configName.default"));



            // PHPUnit\Framework\Constraint\Exception is required by jchook/phpunit-assert-throws
            if (class_exists(ConstraintException::class)) {

                // call the 'swap' method - and throw an exception
                $callbackConnection = null;
                $this->assertThrows(
                    Exception::class,
                    function () use (&$callbackConnection, $configName, $swapCallable) {
                        $callback = function () use (&$callbackConnection, $configName) {
                            $callbackConnection = config("$configName.default");
                            throw new Exception('test');
                        };
                        forward_static_call_array($swapCallable, ['test2', $callback]);
                    }
                );
                $this->assertSame('test2', $callbackConnection);
            }

            // check the connection was returned to normal
            $this->assertSame('test1', config("$configName.default"));
        }
    }





    /**
     * A version of PHP's var_export that renders arrays as [] instead of array().
     *
     * @param mixed   $value The value to var-export.
     * @param integer $depth The current depth.
     * @return string
     */
    public static function varExport($value, int $depth = 0)
    {
        $return = '';
        if (is_array($value)) {
            if (count($value)) {
                $return .= '['.PHP_EOL;
                foreach ($value as $index2 => $value2) {
                    $return .=
                        str_repeat('    ', $depth + 1)
                        .var_export($index2, true)
                        .' => '
                        .static::varExport($value2, $depth + 1)
                        .','.PHP_EOL;
                }
                $return .= str_repeat('    ', $depth).']';
            } else {
                $return .= '[]';
            }
        } elseif (is_scalar($value)) {
            $return .= var_export($value, true);
        } elseif (is_null($value)) {
            $return .= 'null';
        }

        return $return;
    }
}
