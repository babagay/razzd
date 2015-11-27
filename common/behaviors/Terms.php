<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\behaviors;

use yii;
use yii\base\Behavior;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\TaxonomyIndex;

class Terms extends Behavior {

    public $fields, $types;
    private $model, $id;

    public function init() {

        if (!$this->fields || !is_array($this->fields)) {
            throw new InvalidParamException('Invalid param property: fields');
        }
    }

    private function _data() {
        $this->id = $this->owner->getAttribute('id');
        $this->model = get_class($this->owner);
        $this->model = substr($this->model, strrpos($this->model, '\\') + strlen('\\'));


        if (!$this->types) {
            foreach ($this->fields as $key => $field) {
                $f = explode(':', $field);
                $this->fields[$key] = $f[0];
                $this->types[$key] = isset($f[1]) ? $f[1] : 'tags';
            }
        }
    }

    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'validateTerms',
            ActiveRecord::EVENT_AFTER_INSERT => 'saveTerms',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveTerms',
            ActiveRecord::EVENT_BEFORE_DELETE => 'deleteTerms',
            ActiveRecord::EVENT_AFTER_FIND => 'findTerms'
        ];
    }

    public function validateTerms($event) {

    }

    private function putIndex($id, $vid, $field) {

        //проверяем существует ли термин
        $termExist = Yii::$app->db->createCommand('SELECT name FROM {{%taxonomy_items}} WHERE id=:id AND vid=:vid', [
                    ':id' => $id,
                    ':vid' => $vid
                ])->queryScalar();


        if ($termExist) {


            //Проверяем не добавлен ли уже данный термин в индекс
            $termInIndex = Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{%taxonomy_index}}WHERE nid=:nid AND tid=:tid AND model=:model AND field=:field', [
                        ':tid' => $id,
                        ':nid' => $this->id,
                        ':model' => $this->model,
                        ':field' => $field
                    ])->queryScalar();



            if (!$termInIndex) {
                //print_r($termInIndex); exit("A");
                $r = Yii::$app->db->createCommand()->insert('{{%taxonomy_index}}', [
                            'nid' => $this->id,
                            'model' => $this->model,
                            'field' => $field,
                            'tid' => $id,
                        ])->execute();
            }

            return $termExist;
        }

        return false;
    }

    public function saveTerms($event, $insert = false) {
        $this->_data();

        foreach ($this->fields as $vid => $fieldName) {

            $termsNoDelete = [];
            $newTerms = [];

            $items = $this->owner->{$fieldName};





            if (!empty($items)) {
                $data = [];
                switch ($this->types[$vid]) {
                    case 'dropdownlist':
                        if (is_array($items)) {
                            foreach ($items as $itm)
                                $data[] = '[id:' . $itm . ']';
                        } else
                            $data[0] = '[id:' . $items . ']';
                        break;
                    case 'checkboxlist':
                        foreach ($items as $itm)
                            $data[] = '[id:' . $itm . ']';
                        break;
                    default:
                        if (is_array($items))
                            foreach ($items as $itm)
                                $data[] = '[id:' . $itm . ']';
                        elseif (is_string($items) && $items)
                            $data = explode('[:|:]', $items);
                        break;
                }
                $items = $data;
            }


            if (!empty($items)) {

                foreach ($items as $itm) {

                    //Если параметр по шаблону значит это существующий термин на привязку к обьекту. Пример [id:117]
                    if (preg_match("/^\[id:(\d+)\]$/", $itm, $matches)) {

                        $termsNoDelete[] = $this->putIndex($matches[1], $vid, $fieldName);
                    } else { // Процесс создания нового термина
                        $termsExist = (new \yii\db\Query())->select(['name'])->from('{{%taxonomy_items}}')->where([
                                    'vid' => $vid,
                                    'name' => $itm
                                ])->createCommand()->queryColumn();

                        if (!$termsExist) {
                            //создаем новый термин
                            Yii::$app->db->createCommand()->insert('{{%taxonomy_items}}', [
                                'name' => $itm,
                                'vid' => $vid,
                            ])->execute();
                            //получаем его id
                            $newTermsId = Yii::$app->db->getLastInsertID();
                            //привязываем к обьекту
                            $this->putIndex($newTermsId, $vid, $fieldName);
                        }

                        $termsNoDelete[] = $itm;
                    }
                }


                $subQuery = (new \yii\db\Query())->select(['id'])->from('{{%taxonomy_items}}')->where([
                            'vid' => $vid,
                            'name' => $termsNoDelete
                        ])->createCommand()->rawsql;


                $commandDeleteExcess = Yii::$app->db->createCommand('DELETE FROM {{%taxonomy_index}}  WHERE nid=:nid AND model=:model AND field=:field AND tid NOT IN (' . $subQuery . ')', [
                            ':nid' => $this->id,
                            ':model' => $this->model,
                            ':field' => $fieldName,
                        ])->execute();
            } else {

                $commandDeleteAll = Yii::$app->db->createCommand('DELETE FROM {{%taxonomy_index}} WHERE nid=:nid AND model=:model AND field=:field', [
                            ':nid' => $this->id,
                            ':model' => $this->model,
                            ':field' => $fieldName
                        ])->execute();
            }
        }
    }

    public function deleteTerms() {
        $this->_data();
        $commandDeleteAll = Yii::$app->db->createCommand('DELETE FROM {{%taxonomy_index}} WHERE nid=:nid AND model=:model', [
                    ':nid' => $this->id,
                    ':model' => $this->model,
                ])->execute();
    }

    public function findTerms($event) {

        $data = [];
        if (Yii::$app->controller->action->id == 'index')
            return;

        $this->_data();
        $terms = Yii::$app->db->createCommand('SELECT t2.*,t1.field FROM {{%taxonomy_index}}  t1 LEFT JOIN {{%taxonomy_items}} t2 ON t1.tid =t2.id WHERE t1.nid=:nid AND t1.model=:model', [
                    ':nid' => $this->id,
                    ':model' => $this->model
                ])->queryAll();

        foreach ($terms as $term) {

            $id = $term['id'];
            $vid = $term['vid'];
            $field = $term['field'];

            if (!isset($data[$field]))
                $data[$field] = NULL;
            if (isset($this->types[$vid]))
                switch ($this->types[$vid]) {
                    case 'dropdownlist':
                    case 'checkboxlist':
                        $data[$field][$id] = $id;
                        break;
                    default:
                        $data[$field] .= $data[$field] ? '[:|:]' . $term['name'] : $term['name'];
                        break;
                }
        }

        foreach ($data as $field => $itm) {
            $this->owner->{$field} = $itm;
        }
    }

    public function getTermsList($vid) {


        $rows = (new \yii\db\Query())
                        ->select('id, name')
                        ->from('{{%taxonomy_items}}')
                        ->where([
                            'vid' => $vid,
                        ])
                        ->orderBy([
                            'weight' => SORT_ASC,
                        ])->all();

        return ArrayHelper::map($rows, 'id', 'name');
    }

}
