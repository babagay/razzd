<?php

namespace frontend\components;

use yii;
use yii\base\Object;

class Meta extends Object {

    private $_title, $_keywords, $_description;

    public function setMeta() {

        $params = [];
        $route = Yii::$app->requestedRoute;
        $params = Yii::$app->request->get();

        foreach ($params as $k => $r) {
            $params[$k] = (string) $r;
        }

        ksort($params);

        $meta = (new \yii\db\Query())
                ->select('*')
                ->from('{{%meta}} meta')
                ->where([
                    'route' => $route,
                    'params' => serialize($params),
                ])
                ->one();

        if (isset($meta['title']) && $meta['title'])
            $this->setTitle($meta['title']);
        if (isset($meta['keywords']) && $meta['keywords'])
            $this->setKeywords($meta['keywords']);
        if (isset($meta['description']) && $meta['description'])
            $this->setDescription($meta['description']);
    }

    public function setTitle($value) {
        // echo $value; exit();
        Yii::$app->view->title = $value;
    }

    public function setKeywords($value) {
        Yii::$app->view->registerMetaTag(['name' => 'keywords', 'content' => $value], 'keywords');
    }

    public function setDescription($value) {
        Yii::$app->view->registerMetaTag(['name' => 'description', 'content' => $value], 'description');
    }

}
