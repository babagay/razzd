<?php

use yii\db\Schema;
use yii\db\Migration;

class m150715_073925_create_razz_table extends Migration {

    public function up() {
        $this->createTable('{{%razz}}', [
            'id' => Schema::TYPE_PK,
            'uid' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'type' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'ended' => Schema::TYPE_INTEGER . '(1) DEFAULT NULL',
            'title' => Schema::TYPE_STRING . '(255) NOT NULL',
            'description' => Schema::TYPE_TEXT . ' NOT NULL',
            'message' => Schema::TYPE_TEXT . ' DEFAULT NULL',
            'stream' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'stream_preview' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'responder_stream' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'responder_stream_preview' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'responder_uid' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'views' => Schema::TYPE_INTEGER . '(11) UNSIGNED NOT NULL DEFAULT "0"',
            'views_at' => Schema::TYPE_INTEGER . '(11) UNSIGNED NOT NULL',
            'email' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'hash' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'status' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'publish' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'created_at' => Schema::TYPE_INTEGER . '(11) UNSIGNED NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . '(11) UNSIGNED NOT NULL',
                ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');

        $this->db->createCommand('ALTER TABLE {{%razz}} ADD FULLTEXT s (`title`, `description`)')->execute();

        $this->createIndex('uid', '{{%razz}}', 'uid', false);
        $this->createIndex('responder_uid', '{{%razz}}', 'responder_uid', false);
        $this->createIndex('hash', '{{%razz}}', 'hash', true);
    }

    public function down() {
        $this->dropTable('{{%razz}}');

        return false;
    }

}
