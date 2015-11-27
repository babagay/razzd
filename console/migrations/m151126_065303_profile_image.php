<?php

use yii\db\Schema;
use yii\db\Migration;

class m151126_065303_profile_image extends Migration {

    public function up() {
	$this->createTable('{{%profile_image}}', [
	    'id' => Schema::TYPE_BIGPK,
	    'user_id' => Schema::TYPE_INTEGER,
	    'file_path' => 'VARCHAR(255)',
	    'file_name' => 'VARCHAR(255)',
	    'file_name' => 'VARCHAR(255)',
	    'date' => Schema::TYPE_DATETIME,
	]);
    }

    public function down() {
	$this->dropTable('{{%profile_image}}');
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
