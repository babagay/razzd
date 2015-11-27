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
use yii\web\Response;

class AuthorAction extends Action
{

    
   public function run($term)
   {
       
        $items = \Yii::$app->db->createCommand('SELECT id,username label,CONCAT(username," [id:",id,"]") value FROM {{user}} WHERE  blocked_at IS NULL AND username LIKE :username  LIMIT 20')->bindValue(':username', $term . '%')->queryAll();
        
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $items;
       
   }
    
    
}

