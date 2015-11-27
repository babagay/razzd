<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201442_init extends Migration {

    public function up() {

        $this->createTable('{{%alias}}', [
            'id' => Schema::TYPE_PK,
            'eid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'model' => Schema::TYPE_STRING . '(40) NOT NULL',
            'url' => Schema::TYPE_STRING . ' NOT NULL',
            'alias' => Schema::TYPE_STRING . ' NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');
        $this->createIndex('eid', '{{%alias}}', 'eid', false);

        $this->createTable('{{%file}}', [
            'id' => Schema::TYPE_PK,
            'nid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'field' => Schema::TYPE_STRING . '(40) NOT NULL',
            'model' => Schema::TYPE_STRING . '(40) NOT NULL',
            'filename' => Schema::TYPE_STRING . ' NOT NULL',
            'path' => Schema::TYPE_STRING . ' NOT NULL',
            'size' => Schema::TYPE_INTEGER . ' NOT NULL',
            'mimetype' => Schema::TYPE_STRING . '(30) NOT NULL',
            'delta' => Schema::TYPE_INTEGER . ' NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');
        $this->createIndex('nid', '{{%file}}', 'nid', false);

        $this->createTable('{{%taxonomy_vocabulary}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');

        $this->createTable('{{%taxonomy_items}}', [
            'id' => Schema::TYPE_PK,
            'vid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'pid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'weight' => Schema::TYPE_INTEGER . ' NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');

        $this->createIndex('name', '{{%taxonomy_items}}', 'name', false);

        $this->createTable('{{%taxonomy_index}}', [
            'id' => Schema::TYPE_PK,
            'nid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'field' => Schema::TYPE_STRING . '(50) NOT NULL',
            'model' => Schema::TYPE_STRING . '(50) NOT NULL',
            'tid' => Schema::TYPE_INTEGER . ' NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');
        $this->createIndex('tid', '{{%taxonomy_index}}', 'tid', false);
        $this->createIndex('nid', '{{%taxonomy_index}}', 'nid', false);
    }

    public function down() {
        $this->dropTable('{{%alias}}');
        $this->dropTable('{{%file}}');
        $this->dropTable('{{%taxonomy_vocabulary}}');
        $this->dropTable('{{%taxonomy_items}}');
        $this->dropTable('{{%taxonomy_index}}');
    }

}
