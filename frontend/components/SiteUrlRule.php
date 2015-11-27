<?php

namespace frontend\components;

use yii;
use yii\web\UrlRule;

class SiteUrlRule extends UrlRule {

    public function init() {
        if ($this->name === null) {
            $this->name = __CLASS__;
        }
    }

    public function createUrl($manager, $route, $params) {

        if (!isset($params['alias']) || $params['alias'] !== true)
            return false;
        else
            unset($params['alias']);

        ksort($params);
        $query = http_build_query($params);

        if ($query)
            $route .= "?" . $query;

        $data = Yii::$app->db->createCommand('SELECT * FROM {{%alias}} alias WHERE url =:url ', [
                    ':url' => $route,
                ])->queryOne();

        if ($data) {
            return $data['alias'];
        }
        return false;
    }

    public function parseRequest($manager, $request) {
        $pathInfo = $request->getPathInfo();


        if ($pathInfo) {
            $data = Yii::$app->db->createCommand('SELECT * FROM {{%alias}} alias WHERE alias =:alias ', [
                        ':alias' => $pathInfo,
                    ])->queryOne();

            if ($data) {
                $params = [];
                $url = parse_url($data['url']);
                if (isset($url['query']))
                    parse_str($url['query'], $params);

                return [$url['path'], $params];
            }
        }

        return false;
    }

}
