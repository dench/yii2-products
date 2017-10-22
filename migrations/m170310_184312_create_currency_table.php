<?php

use yii\db\Migration;

/**
 * Handles the creation of table `currency`.
 */
class m170310_184312_create_currency_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('currency', [
            'id' => $this->primaryKey(),
            'code' => $this->string(3)->notNull(),
            'rate' => $this->decimal(8, 4)->notNull(),
            'position' => $this->integer()->notNull()->defaultValue(0),
            'enabled' => $this->boolean()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->createTable('currency_lang', [
            'currency_id' => $this->integer()->notNull(),
            'lang_id' => $this->string(3)->notNull(),
            'name' => $this->string()->notNull(),
            'before' => $this->string(),
            'after' => $this->string(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-currency_lang', 'currency_lang', ['currency_id', 'lang_id']);

        $this->addForeignKey('fk-currency_lang-currency_id', 'currency_lang', 'currency_id', 'currency', 'id', 'CASCADE');

        $this->addForeignKey('fk-currency_lang-lang_id', 'currency_lang', 'lang_id', 'language', 'id', 'CASCADE', 'CASCADE');

        $this->insert('currency', [
            'code' => 'UAH',
            'rate' => 1,
        ]);

        $id = 1;

        $this->update('currency', ['position' => $id], ['id' => $id]);

        $this->batchInsert('currency_lang', ['currency_id', 'lang_id', 'name', 'after'], [
            [$id, 'ru', 'Гривна', 'грн'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-currency_lang-lang_id', 'currency_lang');

        $this->dropForeignKey('fk-currency_lang-currency_id', 'currency_lang');

        $this->dropPrimaryKey('pk-currency_lang', 'currency_lang');

        $this->dropTable('currency_lang');
        
        $this->dropTable('currency');
    }
}
