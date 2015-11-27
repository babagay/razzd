<?php

return [
    'name' => 'RAZZD',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'modules' => [
        'datecontrol' => [
            'class' => 'kartik\datecontrol\Module',
        ]
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => ['user', 'agent', 'copy', 'editor', 'moderator', 'admin'],
            'itemFile' => '@common/components/rbac/items.php',
            'assignmentFile' => '@common/components/rbac/assignments.php',
            'ruleFile' => '@common/components/rbac/rules.php'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<action:(login|logout)>' => 'user/security/<action>',
                '<action:(register)>' => 'user/registration/<action>',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'php:M jS, Y h:iA',
            'decimalSeparator' => '.',
            'thousandSeparator' => ' ',
            'currencyCode' => 'USD',
        ],
    ],
];
