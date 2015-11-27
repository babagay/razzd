<?php

namespace console\models;

use Yii;
use yii\imagine\Image;
use Imagine\Image\ManipulatorInterface;
use common\helpers\TransliteratorHelper;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $title
 * @property string $body
 * @property integer $publish
 * @property integer $promote
 */
class Objects extends \yii\db\ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'objects';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['uid', 'publish', 'promote', 'created_at', 'photos', 'type', 'moika_type', 'h24'], 'integer'],
			[['title', 'type'], 'required'],
			[['body'], 'string'],
			[['title', 'phone', 'site'], 'string', 'max' => 255]
		];
	}

	public function countRating($id) {
		$rate = 0;

		$bales = (new \yii\db\Query())
				->select('*')
				->from('objects_rating_bales')
				->all();

		$object = (new \yii\db\Query())
				->select('*')
				->from('objects')
				->where([
					'id' => $id,
				])
				->one();


		foreach ($bales as $b) {

			if ($b['type'] == 'photo')
				$rate += $b['bales'] * $object['photos'];

			if ($b['type'] == 'phone' && strlen($object['phone']) > 5)
				$rate += $b['bales'] * 1;

			if ($b['type'] == 'site' && strlen($object['site']) > 5)
				$rate += $b['bales'] * 1;

			if ($b['type'] == 'mail' && strlen($object['mail']) > 5)
				$rate += $b['bales'] * 1;

			if ($b['type'] == 'body')
				$rate += $b['bales'] * strlen(strip_tags($object['body']));
		}

		return $rate;
	}

	public function updateRating($id) {

		$rating = $this->countRating($id);
		Yii::$app->db->createCommand()
				->update('objects', [
					'rating' => $rating
						], 'id = :id', [
					':id' => $id
				])->execute();
	}

	public function setAddress($model, $object) {

		$cid = 0;
		$rid = 0;
		$raid = 0;
		$city_name = '';
		$region_name = '';
		$raion_name = '';
		$street_coordinates = '';
		$cid = Yii::$app->db->createCommand('SELECT oid FROM {{objects_cities_index}} WHERE oid=:oid LIMIT 1', [
					':oid' => $model->id,
				])->queryScalar();

		if ($cid)
			return $cid;


		$data = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . str_replace(' ', '+', $object['address']) . '&language=ru&sensor=false'));


		if (!isset($data->results[0]->address_components))
			return false;

		foreach ($data->results[0]->address_components as $itm) {

			if (in_array('locality', $itm->types) && in_array('political', $itm->types))
				$city_name = $itm->long_name;

			if (in_array('administrative_area_level_1', $itm->types) && in_array('political', $itm->types))
				$region_name = $itm->long_name;

			if (in_array('sublocality_level_1', $itm->types) && in_array('sublocality', $itm->types))
				$raion_name = $itm->long_name;
		}

		$street_coordinates = $data->results[0]->geometry->location->lat . ',' . $data->results[0]->geometry->location->lng;


		if ($region_name) {
			$rid = Yii::$app->db->createCommand("SELECT id FROM {{objects_region}} WHERE name=:name OR names LIKE :name2 LIMIT 1", [
						':name' => $region_name,
						':name2' => '' . $region_name . '%,',
					])->queryScalar();

			if (!$rid) {

				Yii::$app->db->createCommand()->insert('objects_region', [
					'name' => $region_name,
					'names' => $region_name . ',',
				])->execute();

				$rid = Yii::$app->db->getLastInsertID();
			}
		}



		if ($city_name) {
			$cid = Yii::$app->db->createCommand('SELECT id FROM {{objects_cities}} WHERE name=:city_name OR names LIKE :names LIMIT 1', [
						':city_name' => $city_name,
						':names' => '%' . $city_name . '%',
					])->queryScalar();

			if (!$cid) {

				$data = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . str_replace(' ', '+', $city_name) . '&language=ru&sensor=false'));
				$coordinates = $data->results[0]->geometry->location->lat . ',' . $data->results[0]->geometry->location->lng;

				Yii::$app->db->createCommand()->insert('objects_cities', [
					'rid' => $rid,
					'transliterate' => $this->prepareAlias($city_name),
					'name' => $city_name,
					'name_ru' => $city_name,
					'coordinates' => $coordinates,
				])->execute();

				$cid = Yii::$app->db->getLastInsertID();
			}
		}


		if ($raion_name) {

			$raid = Yii::$app->db->createCommand('SELECT id FROM {{objects_raion}} WHERE name=:name AND cid=:cid LIMIT 1', [
						':cid' => $cid,
						':name' => $raion_name,
					])->queryScalar();

			if (!$raid) {

				Yii::$app->db->createCommand()->insert('objects_raion', [
					'cid' => $cid,
					'name' => $raion_name,
					'coordinates' => '',
				])->execute();

				$raid = Yii::$app->db->getLastInsertID();
			}
		}



		if ($cid) {

			/*
			 * привязуем місто,район,область до обьекту
			 */
			Yii::$app->db->createCommand()->insert('objects_cities_index', [
				'oid' => $model->id,
				'cid' => $cid,
				'rid' => $rid,
				'raid' => $raid
			])->execute();
			Yii::$app->db->createCommand()->insert('objects_address', [
				'oid' => $model->id,
				'address' => $object['address'],
				'coordinates' => $street_coordinates,
			])->execute();
		}

		return $cid;
	}

	public function setServices($model, $object) {

		foreach ($object['services'] as $name) {
			$id = Yii::$app->db->createCommand('SELECT id FROM {{objects_services}} WHERE name=:name', [
						':name' => $name
					])->queryScalar();
			if (!$id) {
				Yii::$app->db->createCommand()->insert('objects_services', [
					'name' => $name,
					'transliterate' => $this->prepareAlias($name)
				])->execute();

				$id = Yii::$app->db->getLastInsertID();
			}

			Yii::$app->db->createCommand()->insert('objects_services_index', [
				'oid' => $model->id,
				'sid' => $id
			])->execute();
		}
	}

	public function setAvto($model, $object) {

		foreach ($object['avto'] as $name) {
			$id = Yii::$app->db->createCommand('SELECT id FROM {{objects_auto}} WHERE name=:name', [
						':name' => $name
					])->queryScalar();
			if (!$id) {
				Yii::$app->db->createCommand()->insert('objects_auto', [
					'name' => $name,
					'transliterate' => $this->prepareAlias($name)
				])->execute();

				$id = Yii::$app->db->getLastInsertID();
			}

			Yii::$app->db->createCommand()->insert('objects_auto_index', [
				'oid' => $model->id,
				'aid' => $id
			])->execute();
		}
	}

	public function setLink($model, $link) {

		Yii::$app->db->createCommand()
				->update('objects_links', [
					'oid' => $model->id,
					'status' => 1,
					'updated_at' => time()
						], 'id = :id', [':id' => $link['id']])
				->execute();
	}

	public function setAlias($model, $object) {

		$alias = '';

		if (isset($object['cid'])) {
			$city = Yii::$app->db->createCommand('SELECT transliterate  FROM {{objects_cities}} WHERE id=:cid', [
						':cid' => $object['cid']
					])->queryScalar();

			$alias .= $city . '/';
		}

		switch ($object['type']) {
			case 1: $alias .= 'sto/';
				break;
			case 2: $alias .= 'schinomontazh/';
				break;
			case 3: $alias .= 'avtomoyka/';
				break;
		}

		$alias .= $this->prepareAlias($object['title']);

		$id = Yii::$app->db->createCommand('SELECT id FROM {{url_rule}} WHERE slug=:slug', [
					':slug' => $alias
				])->queryScalar();

		if ($id) {
			$alias .= '-' . $model->id;
		}

		Yii::$app->db->createCommand()->insert('url_rule', [
			'slug' => $alias,
			'model' => 'Objects',
			'oid' => $model->id,
			'route' => 'object',
			'params' => serialize(['id' => $model->id]),
		])->execute();
	}

	public function setImages($model, $object) {

		$path = 'files/objects/' . $model->id;
		$dir = dirname(__FILE__) . '/../../' . $path;
		if (!is_dir($dir)) {
			mkdir($dir, 0777);
			//mkdir($dir . '/styles', 0777);
		}

		$photos = 0;
		foreach ($object['images'] as $i => $img) {
			$file = @file_get_contents($img);
			if ($file) {
				$name = pathinfo($img);
				$name['basename'] = $this->prepareAlias($name['basename']);
				file_put_contents($dir . '/' . $name['basename'], $file);

				if (!is_dir($dir . '/160x100'))
					mkdir($dir . '/160x100', 0777);

				Image::thumbnail($dir . '/' . $name['basename'], 160, 100, ManipulatorInterface::THUMBNAIL_OUTBOUND)
						->save($dir . '/160x100/' . $name['basename'], ['quality' => 90]);


				Yii::$app->db->createCommand()->insert('file', [
					'nid' => $model->id,
					'field' => 'images',
					'model' => 'Objects',
					'filename' => $name['basename'],
					'path' => $path,
					'delta' => $i,
				])->execute();

				$id = Yii::$app->db->getLastInsertID();
				$photos++;
			}
		}

		Yii::$app->db->createCommand()->update('objects', ['photos' => $photos], 'id = ' . $model->id)->execute();
	}

	private function prepareAlias($alias) {

		$alias = TransliteratorHelper::process($alias, '', 'en');

		$s = [' ', ',', '"', '\'', '`', 'ʹ', '(', ')'];
		$r = ['-', '', '', '', '', '', '', ''];

		return strtolower(str_replace($s, $r, $alias));
	}

}
