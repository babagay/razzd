<?php

    namespace frontend\models;

    use yii\base\Exception;
    use yii\db\ActiveRecord;
    use Yii;

    class Settings extends \yii\base\Model {

        /**
         * Возвращает значенеи конфигурационного параметра
         *
         * @param $key
         * @return string
         * @throws Exception
         */
        public function getConfigurationParamByKey($key){

            if( (int)(new \yii\db\Query())
                ->select('data')
                ->from('{{%settings}} comments')
                ->where([
                    'key' => $key
                ])->count() === 0 )

                throw new Exception("No such key ($key) stored in db");


            return (string)(new \yii\db\Query())
                ->select('data')
                ->from('{{%settings}} comments')
                ->where([
                    'key' => $key
                ])
                ->one()['data'];
        }

    }