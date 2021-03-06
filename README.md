PHPDebug
===============

PHPDebug is a [Monolog Cascade](https://github.com/theorchard/monolog-cascade) extension that which gives you the opportunity to handle and log errors of different levels.

------------


Installation
------------

Add PHPDebug as a requirement in your `composer.json` file or run
```sh
$ composer require datravel/php-debug
```

Usage
-----
Then just use your logger as shown below
```php
Cascade::fileConfig($config);
Log::info('Well, that works!');
Log::error('Maybe not...', ['some'=>'extra data']);
```

Configuring your loggers
------------------------
Monolog Cascade supports the following config formats:
 - Yaml
 - JSON
 - Php array

### Configuration structure

Here is a sample Php array config file:

```php
<?php

$config = [
    'formatters' => [
        'dashed' => [
            //'class' => 'Monolog\Formatter\LineFormatter',
            'class' => \Monolog\Formatter\JsonFormatter::class
            //'format' => '%datetime%-%channel%.%level_name% - %message%'
        ]
    ],
    'handlers' => [
        'output' => [
            'class' => \Monolog\Handler\StreamHandler::class,
            'level' => 'ERROR',
            'formatter' => 'dashed',
            'stream' => 'php://output'
        ],
        'console' => [
            'class' => \Monolog\Handler\StreamHandler::class,
            'level' => 'DEBUG',
            'formatter' => 'dashed',
            'stream' => 'php://stdout'
        ],
        'console' => [
            'class' => 'Monolog\Handler\StreamHandler',
            'level' => 'DEBUG',
            'formatter' => 'dashed',
            'stream' => 'php://stdout'
        ],
        'file' => [
            'class' => 'Monolog\Handler\StreamHandler',
            'level' => 'INFO',
            'formatter' => 'dashed',
            'stream' => './example_info.log'
        ]
    ],
    'processors' => [
        'web_processor' => [
            'class' => 'Monolog\Processor\WebProcessor'
        ]
    ],
    'loggers' => [
        'mainLogger' => [
            'handlers' => [
                0 => 'output',
                1 => 'console',
                2 => 'file'
            ],
            'processors' => [
                0 => 'web_processor'
            ]
        ]
    ],
    'disable_existing_loggers' => true,
    'errorReporting' => E_ALL & ~E_DEPRECATED & ~E_STRICT,
];
```

More informations about configurations - https://github.com/theorchard/monolog-cascade


What is DebugException
------------------------

DebugException is exception which you can to create as object, to add the extra data and throw away. After throwing the Debugger will catches this exception and saves extra data to logs. Examples:

```
throw (new DebugException())->setExtra('mixed data');
```
or:
```
try {
    ...
} catch (\Exception $e) {
    throw (new DebugException())->setCustomTrace($e->getTraceAsString());
```
