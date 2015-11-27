<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use console\models\Objects;
use backend\models\Setting;
use yii\BaseYii;

class ParserController extends Controller {

	public function actionCreate($hash) {

		$num = 2;

		if ($hash != 'K91hV3PxaD7LLc')
			exit(":)");
		exit();
		//echo dirname(__FILE__) . "   ";
		include dirname(__FILE__) . '/../parser/sto.php';

		$p = new \console\parser\Parser();

		for ($i = 0; $i < $num; $i++) {

			$link = Yii::$app->db->createCommand('SELECT * FROM objects_links WHERE  status IS NULL ORDER BY RAND() LIMIT 1', [
					])
					->queryOne();

			$object = $p->getObject($link['link']);

			$model = new Objects;
			$model->created_at = time();
			$model->updated_at = time();
			$model->publish = 0;
			$model->type = $object['type'];
			$model->phone = $object['phone'];
			$model->site = $object['site'];
			$model->title = $object['title'];
			$model->body = $object['body'];
			$model->h24 = $object['h24'];
			$model->moika_type = $object['moika_type'];

			Yii::info("OkKKKKKKK");

			if ($model->validate()) {
				$model->save();
				$model->setServices($model, $object);
				$model->setAvto($model, $object);
				$object['cid'] = $model->setAddress($model, $object);
				$model->setAlias($model, $object);
				$model->setLink($model, $link);
				$model->updateRating($model->id);
				$model->setImages($model, $object);
			} else {



				print_r($model->getErrors());
				exit();
			}
		}



		exit();
	}

	public function actionIndex($hash) {
		if ($hash != 'gPnde6Y881MAblE')
			exit(":)");

		include '../../console/parser/sto.php';

		$p = new \console\parser\Parser();

		$data = [];
		$objects = [];


		foreach ($p->getLinks() as $type => $itm) {

			$d = $p->getObjectLinks($itm, $type);
			$data = array_merge($data, $d);
		}



		foreach ($data as $link) {

			Yii::$app->db->createCommand()->insert('objects_links', [
				'oid' => 0,
				'domain' => $p->domain,
				'link' => $link,
				'updated_at' => time()
			])->execute();

			$id = Yii::$app->db->getLastInsertID();
		}

		exit('Ok');
	}

}
