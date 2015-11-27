<?php

namespace frontend\widgets\rating;

use yii;
use yii\base\Widget;
use yii\base\InvalidParamException;

class Rating extends Widget {

    public $nid, $model, $return_id;
    public $data = [];

    public function init() {
        parent::init();

        if ($this->nid === null) {
            throw new InvalidParamException('Invalid param property: nid');
        }

        if ($this->model === null) {
            throw new InvalidParamException('Invalid param property: model');
        }
    }

    public function rate($name, $options) {

        $this->data[$this->nid][$name] = $options;
    }

    public function run() {

        $model = new \frontend\widgets\rating\models\Rating;
        $model->nid = $this->nid;
        $model->model = $this->model;
        $model->return_id = $this->return_id;
        if (!$model->isRated())
            return '';

        $model->loadData($this->data);

        return $this->render('rate', ['model' => $model, 'data' => $this->data]);
    }

    public static function amIVoted($id,$type){
        return \frontend\widgets\rating\models\Rating::amIVoted($id,$type);
    }

}
