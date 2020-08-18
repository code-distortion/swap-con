# SwapCon

[![Latest Version on Packagist](https://img.shields.io/packagist/v/code-distortion/swap-con.svg?style=flat-square)](https://packagist.org/packages/code-distortion/swap-con)
![PHP from Packagist](https://img.shields.io/packagist/php-v/code-distortion/swap-con?style=flat-square)
![Laravel](https://img.shields.io/badge/laravel-5%2C%206%20%26%207-blue?style=flat-square)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/code-distortion/swap-con/run-tests?label=tests&style=flat-square)](https://github.com/code-distortion/swap-con/actions)
[![Buy us a tree](https://img.shields.io/badge/treeware-%F0%9F%8C%B3-lightgreen?style=flat-square)](https://offset.earth/treeware?gift-trees)
[![Contributor Covenant](https://img.shields.io/badge/contributor%20covenant-v2.0%20adopted-ff69b4.svg?style=flat-square)](CODE_OF_CONDUCT.md)

***code-distortion/swap-con*** is a Laravel package that gives you control over which **database** to use and when to change.

Example:

``` php
// swap the database connection for the duration of the callback
SwapCon::swapDB('mysql2', $callback); // or ->swapDatabase(..)
```

In fact, SwapCon lets you change **broadcasting**, **cache**, **filesystem**, **logging** and **queue** connections as well.

---

Have you ever wanted to change database connections at runtime in Laravel but found it difficult?

You may have tried some of these methods but found them limiting:

``` php
// alter the connection for a particular query
DB::connection('mysql2')->table('users')->all();
```

``` php
// hard-code the connection a model uses
class SomeModel extends Eloquent {
    protected $connection = 'mysql2';
}
// or change the connection for a particular model instance
$someModel = new SomeModel;
$someModel->setConnection('mysql2');
$someModel->find(1);
```

``` php
// specify read/write connections in config/database.php
'mysql' => [
    'read' => [
        'host' => [
            '192.168.1.1',
            '196.168.1.2',
        ],
    ],
    'write' => [
        'host' => [
            '196.168.1.3',
         ],
    ],
    'sticky'    => true,
    'driver'    => 'mysql',
    'database'  => 'database',
    …
],
```

SwapCon is an alternative to these methods.

## Installation

Install the package via composer:

``` bash
composer require code-distortion/swap-con
```

### Service provider &amp; facade registration

SwapCon integrates with Laravel 5.5+ automatically thanks to Laravel's package auto-detection. For Laravel 5.0 - 5.4, add the following lines to config/app.php:

``` php
'providers' => [
    …
    CodeDistortion\SwapCon\SwapConServiceProvider::class,
    …
],
'aliases' => [
    …
    'SwapCon' => CodeDistortion\SwapCon\SwapConFacade::class,
    …
],
```

### Config file

Use the following command to publish the config/code-distortion.swapcon.php config file:

``` bash
php artisan vendor:publish --provider="CodeDistortion\SwapCon\SwapConServiceProvider" --tag="config"
```


## Usage

SwapCon makes the ***SwapCon*** facade available to use.

There are two main ways to change connections. You may run a callback while ***swapping*** the default database connection (which replaces the original connection back again afterwards), or you may ***use*** a new connection moving forward.

By changing the default connection, your code will start interacting with this new connection without having to micro-manage it.

``` php
// swap the current database connection for the duration of the callback
SwapCon::swapDB('mysql2', $callback); // or ->swapDatabase(..)
```

***Note:*** The ***swap*** methods will catch and re-throw exceptions, making sure to replace the original connection back again so you don't have to. This is the safest way to alternate between connections.

You can also simply change the default database to use:

``` php
// change the current database
SwapCon::useDB('mysql2'); // or ->useDatabase(..)
```

You can manage the other connection types too using these methods:

``` php
// swap the current connections for the duration of the callback
SwapCon::swapBroadcast('pusher2', $callback);
SwapCon::swapCache('redis2', $callback);
SwapCon::swapFilesystem('s3-2', $callback);
SwapCon::swapLog('syslog', $callback);
SwapCon::swapQueue('sqs2', $callback);

// change the current connection
SwapCon::useBroadcast('pusher2');
SwapCon::useCache('redis2');
SwapCon::useFilesystem('s3-2');
SwapCon::useLog('syslog');
SwapCon::useQueue('sqs2');
```

This might be all the functionality you need. Below are details on how to take SwapCon further.

### Custom connections

Imagine a situation where you want to connect to a read-only replicated database in specific parts of your code. Perhaps you'd like to offload some heavy queries to free up your read/write database.

You could start by adding a second connection to `configs/database.php`:

``` php
// config/database.php
'mysql' => [
    'driver' => 'mysql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', '192.168.1.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'mydb_rw'),
    …
],
'mysql-ro' => [
    'driver' => 'mysql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', '192.168.1.2'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'mydb_ro'),
    …
],
```

And swapping to this on the fly:

``` php
SwapCon::swapDB('mysql-ro', $callback);
```

However your local development environment might not have a replicated database like there is in production.

SwapCon lets you to define ***custom connections*** arbitrarily from your .env file, allowing you choose which connections to define in different environments, if at all.

Specify values for your new connections in your .env file with the format:

`SWAPCON_<CONNECTION-TYPE>_<CONNECTION-NAME>_<SETTING-NAME>=value`

("CONNECTION-TYPE" is one of: `BROADCASTING`, `CACHE`, `DATABASE`, `FILESYSTEMS`, `LOGGING` or`QUEUE`).

You can also choose to ***clone*** an existing connection's settings, and override specific values (like the database name or host).

eg. Below the new 'mysql-ro1' connection will take the existing 'mysql' connection settings, but override the database to connect to:

```
# .env
SWAPCON__DATABASE__MYSQL-RO1__CLONE=mysql
SWAPCON__DATABASE__MYSQL-RO1__DATABASE=mydb_ro
``` 

This doesn't give much extra functionality yet over adding the connection to the config file directly. However you can take it a step further with connection groups…

### Connection groups

You can group several connections together using ***connection groups***. Define groups in your .env file using the format:

`SWAPCON_GROUP_<CONNECTION-TYPE>_<GROUP-NAME>=<connections>`

(where "CONNECTION-TYPE" is one of: `BROADCASTING`, `CACHE`, `DATABASE`, `FILESYSTEMS`, `LOGGING` or `QUEUE`).

eg.

```
# .env
SWAPCON__GROUP__DATABASE__MYSQL-RO=mysql-ro1,mysql-ro2,mysql-ro3

SWAPCON__DATABASE__MYSQL-RO1__CLONE=mysql
SWAPCON__DATABASE__MYSQL-RO1__DATABASE=mydb_ro1

SWAPCON__DATABASE__MYSQL-RO2__CLONE=mysql
SWAPCON__DATABASE__MYSQL-RO2__DATABASE=mydb_ro2

SWAPCON__DATABASE__MYSQL-RO3__CLONE=mysql
SWAPCON__DATABASE__MYSQL-RO3__DATABASE=mydb_ro3
```

Now you suddenly have 3 database connections ready for read-only access, and can refer to them using the name 'mysql-ro'.

``` php
SwapCon::SwapDB('mysql-ro', $callback);
```

When SwapCon finds a group, it will pick a connection from it randomly. If this group is used again it will continue to use the same connection it chose before. In this example SwapCon may end up choosing the 'mysql-ro2' connection.

***Note:*** SwapCon won't enforce the read-only-ness of a database connection.

### Fallback connections

Now, if you have some of these read-only databases in production for example but not locally in your development environment, the above code will generate an exception for you because you haven't defined the 'mysql-ro' connection or group.

This is where ***fallback connections*** come in. You can specify in `config/code-distortion.swapcon.php` fallback connections to use when they can't be found.

``` php
// config/code-distortion.swapcon.php
'fallbacks' => [
    'reuse' => [
        'database' => [
            'mysql-ro' => 'mysql',
        ],
    ],
    'clone' => [
        'database' => [
//            'mysql-ro' => 'mysql',
        ],
    ],
],
```

Here you can specify a connection to simply ***reuse*** when needed. In this example the 'mysql' connection will continue being used unless a 'mysql-ro' connection or group exist in the current .env file.

Alternately you can choose a connection to ***clone*** when needed. If the line above is uncommented, the 'mysql' connection details will be cloned and a separate new connection will be made to that same database (unless a 'mysql-ro' connection or group exist in the current .env file).

***NOTE:*** As a rule of thumb you should add a fallback for each connection you refer to by name in your code. This will save exceptions from occurring when the connections can't be resolved.

### Altering the available connections on the fly

Sometimes you need to change connection settings from within your code. For example you may wish to do this if you have a website that connects to different databases, one for each client. Your code would need to first run to work out which client is needed, and then pick their database connection.
    
You can alter connections:

``` php
// some code to detect the tenant to use…
$tenantDB = 'client1';

// create a new connection called 'tenant' by cloning the 'mysql' connection, and override the 'database' value
SwapCon::cloneDB('mysql', 'tenant', ['database' => $tenantDB]);
// (allow for the 'tenant' connection to be overwritten if it already exists)
SwapCon::cloneDB('mysql', 'tenant', ['database' => $tenantDB], true);

// then you can access the new 'tenant' connection like any other. eg.
SwapCan::swapDB('tenant', $callback);
SwapCan::useDB('tenant');
DB::connection('tenant')->table('users')->all();
// etc…
```

Alternatively you can update a connection that already exists: 

``` php
SwapCon::updateDB('tenant', ['database' => $tenantDB]);
```

***Note:*** The purpose of this library is to help you manage connections, it doesn't help manage the tenancy choosing process. You will need to determine the tenant database to use yourself.

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

### SemVer

This library uses [SemVer 2.0.0](https://semver.org/) versioning. This means that changes to `X` indicate a breaking change: `0.0.X`, `0.X.y`, `X.y.z`. When this library changes to version 1.0.0, 2.0.0 and so forth it doesn't indicate that it's necessarily a notable release, it simply indicates that the changes were breaking.

## Treeware

You're free to use this package, but if it makes it to your production environment please plant or buy a tree for the world.

It's now common knowledge that one of the best tools to tackle the climate crisis and keep our temperatures from rising above 1.5C is to <a href="https://www.bbc.co.uk/news/science-environment-48870920">plant trees</a>. If you support this package and contribute to the Treeware forest you'll be creating employment for local families and restoring wildlife habitats.

You can buy trees here [offset.earth/treeware](https://offset.earth/treeware?gift-trees)

Read more about Treeware at [treeware.earth](http://treeware.earth)

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Code of conduct

Please see [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

### Security

If you discover any security related issues, please email tim@code-distortion.net instead of using the issue tracker.

## Credits

- [Tim Chandler](https://github.com/code-distortion)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## PHP Package Boilerplate

This package was generated using the [PHP Package Boilerplate](https://laravelpackageboilerplate.com).
