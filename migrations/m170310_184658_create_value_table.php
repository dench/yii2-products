<?php

use yii\db\Migration;

/**
 * Handles the creation of table `value`.
 */
class m170310_184658_create_value_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('value', [
            'id' => $this->primaryKey(),
            'feature_id' => $this->integer()->notNull(),
            'position' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createTable('value_lang', [
            'value_id' => $this->integer()->notNull(),
            'lang_id' => $this->string(3)->notNull(),
            'name' => $this->string()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-value_lang', 'value_lang', ['value_id', 'lang_id']);

        $this->addForeignKey('fk-value_lang-feature_id', 'value', 'feature_id', 'feature', 'id', 'CASCADE');

        $this->addForeignKey('fk-value_lang-value_id', 'value_lang', 'value_id', 'value', 'id', 'CASCADE');

        $this->addForeignKey('fk-value_lang-lang_id', 'value_lang', 'lang_id', 'language', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-value_lang-lang_id', 'value_lang');

        $this->dropForeignKey('fk-value_lang-value_id', 'value_lang');

        $this->dropForeignKey('fk-value_lang-feature_id', 'value');

        $this->dropPrimaryKey('pk-value_lang', 'value_lang');
        
        $this->dropTable('value_lang');
        
        $this->dropTable('value');
    }
}
