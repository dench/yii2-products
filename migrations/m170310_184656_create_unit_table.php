<?php

use yii\db\Migration;

/**
 * Handles the creation of table `unit`.
 */
class m170310_184656_create_unit_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('unit', [
            'id' => $this->primaryKey(),
            'position' => $this->integer()->notNull()->defaultValue(0),
            'enabled' => $this->boolean()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->createTable('unit_lang', [
            'unit_id' => $this->integer()->notNull(),
            'lang_id' => $this->string(3)->notNull(),
            'name' => $this->string()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-unit_lang', 'unit_lang', ['unit_id', 'lang_id']);

        $this->addForeignKey('fk-unit_lang-unit_id', 'unit_lang', 'unit_id', 'unit', 'id', 'CASCADE');

        $this->addForeignKey('fk-unit_lang-lang_id', 'unit_lang', 'lang_id', 'language', 'id', 'CASCADE', 'CASCADE');

        $this->insert('unit', []);

        $id = 1;

        $this->update('unit', ['position' => $id], ['id' => $id]);

        $this->batchInsert('unit_lang', ['unit_id', 'lang_id', 'name'], [
            [$id, 'ru', 'шт'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-unit_lang-lang_id', 'unit_lang');

        $this->dropForeignKey('fk-unit_lang-unit_id', 'unit_lang');

        $this->dropPrimaryKey('pk-unit_lang', 'unit_lang');

        $this->dropTable('unit_lang');
        
        $this->dropTable('unit');
    }
}
