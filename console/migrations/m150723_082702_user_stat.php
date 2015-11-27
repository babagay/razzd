<?php

use yii\db\Schema;
use yii\db\Migration;

class m150723_082702_user_stat extends Migration {

    public function up() {
        $this->createTable('{{%user_stat}}', [
            'id' => Schema::TYPE_PK,
            'uid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_STRING . '(40) NOT NULL',
            'total' => Schema::TYPE_FLOAT . ' DEFAULT NULL',
            'data' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'created_at' => Schema::TYPE_INTEGER . '(11) UNSIGNED NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->db->createCommand('ALTER TABLE {{%user_stat}} ADD CONSTRAINT `user_stat_ibfk_1` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`id`) ON DELETE CASCADE ON UPDATE NO ACTION')->execute();


        $this->createIndex('uid', '{{%user_stat}}', 'uid', false);
        $this->createIndex('type', '{{%user_stat}}', 'type', false);
    }

    public function down() {
        $this->dropTable('{{%user_stat}}');
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
