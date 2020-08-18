<?php

use CodeDistortion\SwapCon\SwapCon;

$return = [

    /*
    |--------------------------------------------------------------------------
    | SwapCon - available connections
    |--------------------------------------------------------------------------
    |
    | For full instructions please visit:
    |   https://github.com/code-distortion/swap-con
    |
    | A list of connections will be built from your .env file.
    |
    | Specify CUSTOM CONNECTION details with values in the format:
    |   SWAPCON__<con-type>__<con-name>__<setting-name>=
    |
    | eg.
    |   SWAPCON__DATABASE__MYSQL2__HOST=xxx
    |   SWAPCON__DATABASE__MYSQL2__PORT=xxx
    |   SWAPCON__DATABASE__MYSQL2__DATABASE=xxx
    |   SWAPCON__DATABASE__MYSQL2__USERNAME=xxx
    |   SWAPCON__DATABASE__MYSQL2__PASSWORD=xxx
    |   SWAPCON__DATABASE__MYSQL2__SOCKET=xxx
    |
    | This connection will then be available for SwapCon to use:
    |   $swapCon->swapDB('mysql2', $callback);
    |
    | You can also specify an existing connection to clone. SwapCon will copy this
    | connection's details and override it with new values:
    |   SWAPCON__DATABASE__MYSQL2__CLONE=mysql
    |   SWAPCON__DATABASE__MYSQL2__HOST=xxx
    |
    |
    |
    | SwapCon supports CONNECTION GROUPS which allows you to specify alternate
    | connections that can be chosen from. Specify them with values in the format:
    |   SWAPCON__GROUP__<con-type>__<con-name>=xxx,yyy,zzz
    |
    | eg.
    |   SWAPCON__GROUP__DATABASE__MYSQLRO=mysqlro1,mysqlro2,mysqlro3
    |
    |   SWAPCON__DATABASE__MYSQLRO__MYSQLRO1__CLONE=mysql
    |   SWAPCON__DATABASE__MYSQLRO__MYSQLRO1__HOST=xxx
    |
    |   SWAPCON__DATABASE__MYSQLRO__MYSQLRO2__CLONE=mysql
    |   SWAPCON__DATABASE__MYSQLRO__MYSQLRO2__HOST=yyy
    |
    |   SWAPCON__DATABASE__MYSQLRO__MYSQLRO3__CLONE=mysql
    |   SWAPCON__DATABASE__MYSQLRO__MYSQLRO3__HOST=zzz
    |
    | SwapCon will then pick one of the alternatives randomly when called like
    | this:
    |
    |   $swapCon->swapDB('mysqlro', $callback);
    |
    |
    |
    | Specify fallback connections for each connection referred to in your code.
    | This will save exceptions from occurring if they aren't present in a .env file.
    |
    | Fallbacks can either 'reuse' an existing connection (where the connection will be
    | re-used), or 'cloned' (where the connection will be copied and used as a new
    | connection).
    |
    |
    |
    | The possible <con-types> are:
    |
    |   BROADCASTING -> $swapCon->swapBroadcast('pusher', $callback);
    |   CACHE        -> $swapCon->swapCache('redis', $callback);
    |   DATABASE     -> $swapCon->swapDB('mysql', $callback); // or ->swapDatabase(..)
    |   FILESYSTEMS  -> $swapCon->swapFilesystem('s3', $callback);
    |   LOGGING      -> $swapCon->swapLog('slack', $callback);
    |   QUEUE        -> $swapCon->swapQueue('sqs', $callback);
    |
    */

    'fallbacks' => [
        'reuse' => [
//            'database' => [
//                'mysql-ro' => 'mysql',
//            ],
        ],
        'clone' => [
//            'database' => [
//                'mysql-ro' => 'mysql',
//            ],
        ],
     ],

];

// add connection details based on the current .env file
return array_merge($return, app(SwapCon::class)->buildConfig());
