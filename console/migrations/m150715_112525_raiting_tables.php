<?php

use yii\db\Schema;
use yii\db\Migration;

class m150715_112525_raiting_tables extends Migration {

    public function up() {
        $this->createTable('{{%raiting}}', [
            'id' => Schema::TYPE_PK,
            'nid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'model' => Schema::TYPE_STRING . '(50) NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');
        $this->createIndex('nid', '{{%raiting}}', 'nid', false);

        $this->createTable('{{%rating_votes}}', [
            'id' => Schema::TYPE_PK,
            'rid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'uid' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'vote' => Schema::TYPE_FLOAT . ' NOT NULL',
            'name' => Schema::TYPE_STRING . '(50) NOT NULL',
            'ip' => Schema::TYPE_STRING . '(20) NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');

        $this->createIndex('uid', '{{%rating_votes}}', 'uid', false);
        $this->createIndex('rid', '{{%rating_votes}}', 'rid', false);


        $this->createTable('{{%rating_total}}', [
            'id' => Schema::TYPE_PK,
            'rid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'nid' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'name' => Schema::TYPE_STRING . '(50) NOT NULL',
            'votes' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT "0"',
            'rating' => Schema::TYPE_FLOAT . ' NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');

        $this->createIndex('nid', '{{%rating_total}}', 'nid', false);
        $this->createIndex('rid', '{{%rating_total}}', 'rid', false);
    }

    public function down() {
        $this->dropTable('{{%raiting}}');
        $this->dropTable('{{%rating_votes}}');
        $this->dropTable('{{%rating_total}}');
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
