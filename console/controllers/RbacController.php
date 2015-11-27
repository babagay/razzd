<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\components\rbac\UserRoleRule;

class RbacController extends Controller {

	public function actionInit() {
		$auth = Yii::$app->authManager;
		$auth->removeAll(); //удаляем старые данные
		//Создадим для примера права для доступа к админке
		$dashboard = $auth->createPermission('dashboard');
		$dashboard->description = 'Админ панель';
		$auth->add($dashboard);

		$deleteObjects = $auth->createPermission('deleteObjects');
		$deleteObjects->description = 'Удаление обьекта';
		$auth->add($deleteObjects);

		$updateObjects = $auth->createPermission('updateObjects');
		$updateObjects->description = 'Редактирование обьекта';
		$auth->add($updateObjects);

		$createObjects = $auth->createPermission('createObjects');
		$createObjects->description = 'Редактирование обьекта';
		$auth->add($createObjects);

		//Включаем наш обработчик
		$rule = new UserRoleRule();
		$auth->add($rule);

		//Добавляем роли
		$user = $auth->createRole('user');
		$user->description = 'Пользователь';
		$user->ruleName = $rule->name;
		$auth->add($user);


		$agent = $auth->createRole('agent');
		$agent->description = 'Представитель сервиса';
		$agent->ruleName = $rule->name;
		$auth->add($agent);
		$auth->addChild($agent, $user);


		$copy = $auth->createRole('copy');
		$copy->description = 'Копирайтер';
		$copy->ruleName = $rule->name;
		$auth->add($copy);
		$auth->addChild($copy, $user);


		$editor = $auth->createRole('editor');
		$editor->description = 'Редактор';
		$editor->ruleName = $rule->name;
		$auth->add($editor);
		$auth->addChild($editor, $user);

		$moder = $auth->createRole('moderator');
		$moder->description = 'Модератор';
		$moder->ruleName = $rule->name;
		$auth->add($moder);
		$auth->addChild($moder, $user);

		$admin = $auth->createRole('admin');
		$admin->description = 'Администратор';
		$admin->ruleName = $rule->name;
		$auth->add($admin);
		$auth->addChild($admin, $moder);
	}

}
