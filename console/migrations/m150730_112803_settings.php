<?php

use yii\db\Schema;
use yii\db\Migration;

class m150730_112803_settings extends Migration {

    public function up() {
        $this->createTable('{{%settings}}', [
            'key' => Schema::TYPE_STRING . '(100) NOT NULL',
            'data' => Schema::TYPE_TEXT . ' DEFAULT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');
        $this->createIndex('key', '{{%settings}}', 'key', true);
    }

    public function down() {
        $this->dropTable('{{%settings}}');
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
