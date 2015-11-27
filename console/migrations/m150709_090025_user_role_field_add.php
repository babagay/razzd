<?php

use yii\db\Schema;
use yii\db\Migration;

class m150709_090025_user_role_field_add extends Migration {

    public function up() {
        $this->addColumn('{{%user}}', 'role', Schema::TYPE_STRING . '(255) NOT NULL DEFAULT 0');
    }

    public function down() {
        $this->dropColumn('{{%user}}', 'role');

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
