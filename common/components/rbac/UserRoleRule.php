<?php

namespace common\components\rbac;

use Yii;
use yii\rbac\Rule;
use yii\helpers\ArrayHelper;
use common\models\User;

class UserRoleRule extends Rule {

    public $name = 'userRole';

    public function execute($user, $item, $params) {

        $user = ArrayHelper::getValue($params, 'user', User::findOne($user));
        if ($user) {
            $role = $user->role;
            //exit("AA");
            if ($item->name === 'admin') {
                return $role == User::ROLE_ADMIN;
            } elseif ($item->name === 'moderator') {
                return $role == User::ROLE_ADMIN || $role == User::ROLE_MODERATOR;
            } elseif ($item->name === 'editor') {
                return $role == User::ROLE_ADMIN || $role == User::ROLE_MODERATOR || $role == User::ROLE_EDITOR;
            } elseif ($item->name === 'copy') {
                return $role == User::ROLE_ADMIN || $role == User::ROLE_MODERATOR || $role == User::ROLE_EDITOR || $role == User::ROLE_COPY;
            } elseif ($item->name === 'agent') {
                return $role == User::ROLE_ADMIN || $role == User::ROLE_MODERATOR || $role == User::ROLE_EDITOR || $role == User::ROLE_AGENT;
            } elseif ($item->name === 'user') {
                return $role == User::ROLE_ADMIN || $role == User::ROLE_MODERATOR || $role == User::ROLE_USER || $role == User::ROLE_AGENT || $role == User::ROLE_EDITOR || $role == User::ROLE_COPY;
            }
        }
        return false;
    }

}
