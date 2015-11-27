<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\actions;

use Yii;
use yii\base\Action;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;

class DeleteFileAction extends Action {

    public function run($id, $hex) {
        // return "deleted";
        if (Yii::$app->request->isGet) {


            $file = \common\models\File::findOne($id);

            if ($file->id && $hex == md5(Yii::$app->request->cookieValidationKey . $_SERVER['REMOTE_ADDR'] . $file->filename . $file->id)) {

                $dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $file->path;

                if (is_file($dir . '/' . $file->filename))
                    unlink($dir . '/' . $file->filename);
                $file->delete();


                $styles = scandir($dir);
                foreach ($styles as $style) {
                    if (is_dir($dir . '/' . $style) && is_file($dir . '/' . $style . '/' . $file->filename))
                        unlink($dir . '/' . $style . '/' . $file->filename);
                }


                Yii::$app->db->createCommand('UPDATE {{file}} SET delta = delta -1 WHERE nid=:nid AND model=:model  AND field=:field AND delta > :delta', [
                    ':nid' => $file->nid,
                    ':model' => $file->model,
                    ':field' => $file->field,
                    ':delta' => $file->delta,
                ])->execute();



                return "deleted";
            }

            return false;
        }
    }

}
