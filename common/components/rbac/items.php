<?php
return [
    'dashboard' => [
        'type' => 2,
        'description' => 'Админ панель',
    ],
    'deleteObjects' => [
        'type' => 2,
        'description' => 'Удаление обьекта',
    ],
    'updateObjects' => [
        'type' => 2,
        'description' => 'Редактирование обьекта',
    ],
    'createObjects' => [
        'type' => 2,
        'description' => 'Редактирование обьекта',
    ],
    'user' => [
        'type' => 1,
        'description' => 'Пользователь',
        'ruleName' => 'userRole',
    ],
    'agent' => [
        'type' => 1,
        'description' => 'Представитель сервиса',
        'ruleName' => 'userRole',
        'children' => [
            'user',
        ],
    ],
    'copy' => [
        'type' => 1,
        'description' => 'Копирайтер',
        'ruleName' => 'userRole',
        'children' => [
            'user',
        ],
    ],
    'editor' => [
        'type' => 1,
        'description' => 'Редактор',
        'ruleName' => 'userRole',
        'children' => [
            'user',
        ],
    ],
    'moderator' => [
        'type' => 1,
        'description' => 'Модератор',
        'ruleName' => 'userRole',
        'children' => [
            'user',
        ],
    ],
    'admin' => [
        'type' => 1,
        'description' => 'Администратор',
        'ruleName' => 'userRole',
        'children' => [
            'moderator',
        ],
    ],
];
