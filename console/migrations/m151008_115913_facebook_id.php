<?php

use yii\db\Schema;
use yii\db\Migration;

class m151008_115913_facebook_id extends Migration {

    public function up() {
        $this->addColumn('{{%razz}}', 'facebook_id', Schema::TYPE_STRING . '(255) DEFAULT NULL AFTER `email`');
    }

    public function down() {
        $this->dropColumn('{{%razz}}', 'facebook_id');
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
