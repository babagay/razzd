<?php

use yii\db\Schema;
use yii\db\Migration;

class m150724_071256_notification extends Migration {

    public function up() {
        $this->createTable('{{%notification}}', [
            'id' => Schema::TYPE_PK,
            'uid' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'message' => Schema::TYPE_TEXT . ' NOT NULL',
            'link' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'hide' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'created_at' => Schema::TYPE_INTEGER . '(11) UNSIGNED NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');

        $this->createIndex('uid', '{{%notification}}', 'uid', false);
        $this->createIndex('hide', '{{%notification}}', 'hide', false);
    }

    public function down() {
        $this->dropTable('{{%notification}}');
    }

}
