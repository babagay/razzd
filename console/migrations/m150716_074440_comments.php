<?php

use yii\db\Schema;
use yii\db\Migration;

class m150716_074440_comments extends Migration {

    public function up() {
        $this->createTable('{{%comments}}', [
            'id' => Schema::TYPE_PK,
            'uid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'eid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'comment' => Schema::TYPE_STRING . '(255) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . '(11) UNSIGNED NOT NULL',
            'status' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'ip' => Schema::TYPE_STRING . '(30) NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');
        $this->createIndex('uid', '{{%comments}}', 'uid', false);
        $this->createIndex('eid', '{{%comments}}', 'eid', false);
    }

    public function down() {
        $this->dropTable('{{%comments}}');
        return false;
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
