<?php

use yii\db\Migration;

/**
 * Handles the creation of table `status`.
 */
class m170310_185023_create_product_status_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('status', [
            'id' => $this->primaryKey(),
            'color' => $this->string(),
            'position' => $this->integer()->notNull()->defaultValue(0),
            'enabled' => $this->boolean()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->createTable('status_lang', [
            'status_id' => $this->integer()->notNull(),
            'lang_id' => $this->string(3)->notNull(),
            'name' => $this->string()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-status_lang', 'status_lang', ['status_id', 'lang_id']);

        $this->addForeignKey('fk-status_lang-status_id', 'status_lang', 'status_id', 'status', 'id', 'CASCADE');

        $this->addForeignKey('fk-status_lang-lang_id', 'status_lang', 'lang_id', 'language', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-status_lang-lang_id', 'status_lang');

        $this->dropForeignKey('fk-status_lang-status_id', 'status_lang');

        $this->dropPrimaryKey('pk-status_lang', 'status_lang');

        $this->dropTable('status_lang');
        
        $this->dropTable('status');
    }
}
