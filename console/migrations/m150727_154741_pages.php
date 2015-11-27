<?php

use yii\db\Schema;
use yii\db\Migration;

class m150727_154741_pages extends Migration {

    public function up() {
        $this->createTable('{{%pages}}', [
            'id' => Schema::TYPE_PK,
            'uid' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'title' => Schema::TYPE_STRING . '(255) NOT NULL',
            'body' => Schema::TYPE_TEXT . ' NOT NULL',
            'publish' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'promote' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'created_at' => Schema::TYPE_INTEGER . '(11) UNSIGNED NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . '(11) UNSIGNED NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');

        $this->createIndex('uid', '{{%pages}}', 'uid', false);
    }

    public function down() {
        $this->dropTable('{{%pages}}');
    }

    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
