<?php

use yii\db\Schema;
use yii\db\Migration;

class m150727_093803_meta extends Migration {

    public function up() {
        $this->createTable('{{%meta}}', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'keywords' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'description' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'route' => Schema::TYPE_STRING . '(255) NOT NULL',
            'params' => Schema::TYPE_STRING . '(255) NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');
        $this->createIndex('route', '{{%meta}}', 'route', false);
        $this->createIndex('params', '{{%meta}}', 'params', false);
    }

    public function down() {
        $this->dropTable('{{%meta}}');
    }

}
