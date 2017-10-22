<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brand`.
 */
class m170310_172711_create_brand_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('brand', [
            'id' => $this->primaryKey(),
            'image_id' => $this->integer(),
            'position' => $this->integer()->notNull()->defaultValue(0),
            'enabled' => $this->boolean()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->createTable('brand_lang', [
            'brand_id' => $this->integer()->notNull(),
            'lang_id' => $this->string(3)->notNull(),
            'name' => $this->string()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-brand_lang', 'brand_lang', ['brand_id', 'lang_id']);

        $this->addForeignKey('fk-brand-image_id', 'brand', 'image_id', 'image', 'id', 'SET NULL');

        $this->addForeignKey('fk-brand_lang-brand_id', 'brand_lang', 'brand_id', 'brand', 'id', 'CASCADE');

        $this->addForeignKey('fk-brand_lang-lang_id', 'brand_lang', 'lang_id', 'language', 'id', 'CASCADE', 'CASCADE');

        $this->insert('brand', []);

        $id = 1;

        $this->update('brand', ['position' => $id], ['id' => $id]);

        $this->batchInsert('brand_lang', ['brand_id', 'lang_id', 'name'], [
            [$id, 'ru', 'Бренд'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-brand_lang-lang_id', 'brand_lang');

        $this->dropForeignKey('fk-brand_lang-brand_id', 'brand_lang');

        $this->dropForeignKey('fk-brand-image_id', 'brand');

        $this->dropPrimaryKey('pk-brand_lang', 'brand_lang');

        $this->dropTable('brand_lang');

        $this->dropTable('brand');
    }
}
