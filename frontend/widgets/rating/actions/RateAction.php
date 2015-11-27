<?php

namespace frontend\widgets\rating\actions;

use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;

class RateAction extends Action {

    /**
     * @inheritdoc
     */
    public function run() {

        if (Yii::$app->request->isPost) {

            $model = new \frontend\widgets\rating\models\Rating;
            $post = Yii::$app->request->post();
            if ($model->load($post) && $model->save()) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return $model->return;
                }
                return Yii::$app->response->redirect(Yii::$app->request->referrer);
            }
        } else
            exit(); //$this->redirect('/', 302);
    }

}
