<?php

use yii\db\Schema;
use yii\db\Migration;

class m151118_124658_create_views_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%views}}', [
            'id' => Schema::TYPE_PK,
            'uid' => Schema::TYPE_INTEGER . '(11) NOT NULL',
            'rid' => Schema::TYPE_INTEGER . '(11) NOT NULL',
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');

        $this->createIndex('uid', '{{%views}}', 'uid', false);
        $this->createIndex('rid', '{{%views}}', 'rid', false);

    }

    public function down()
    {
        echo "m151118_124658_create_views_table cannot be reverted.\n";
        $this->dropTable('{{%views}}');
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
