YaproDebug
===============

YaproDebug is a [Monolog Cascade](https://github.com/theorchard/monolog-cascade) extension that which gives you the opportunity to handle and log errors of different levels.

------------


Installation
------------

Add YaproDebug as a requirement in your `composer.json` file or run
```sh
$ composer require yapro/debug
```

Note: YaproDebug requires PHP 5.3.9 or higher.

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
        'console' => [
            'class' => 'Monolog\Handler\StreamHandler',
            'level' => 'DEBUG',
            'formatter' => 'dashed',
            'stream' => 'php://stdout'
        ],
        'info_file_handler' => [
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
                0 => 'console',
                1 => 'info_file_handler'
            ],
            'processors' => [
                0 => 'web_processor'
            ]
        ]
    ],
    'disable_existing_loggers' => true
];
```

More informations about configurations - https://github.com/theorchard/monolog-cascade
