<?php

use yii\db\Migration;

/**
 * Handles the creation of table `product`.
 */
class m170310_185835_create_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('product', [
            'id' => $this->primaryKey(),
            'slug' => $this->string()->notNull(),
            'brand_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'price_from' => $this->integer()->notNull()->defaultValue(0),
            'view' => $this->string(),
            'position' => $this->integer()->notNull()->defaultValue(0),
            'enabled' => $this->boolean()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->createTable('product_lang', [
            'product_id' => $this->integer()->notNull(),
            'lang_id' => $this->string(3)->notNull(),
            'name' => $this->string()->notNull(),
            'title' => $this->string()->notNull(),
            'h1' => $this->string()->notNull(),
            'keywords' => $this->string()->notNull()->defaultValue(''),
            'description' => $this->text(),
            'text' => $this->text(),
        ], $tableOptions);

        $this->createTable('product_category', [
            'product_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-product_lang', 'product_lang', ['product_id', 'lang_id']);

        $this->addPrimaryKey('pk-product_category', 'product_category', ['product_id', 'category_id']);

        $this->addForeignKey('fk-product_category-product_id', 'product_category', 'product_id', 'product', 'id', 'CASCADE');

        $this->addForeignKey('fk-product_category-category_id', 'product_category', 'category_id', 'category', 'id', 'CASCADE');

        $this->addForeignKey('fk-product-brand_id', 'product', 'brand_id', 'brand', 'id', 'SET NULL');

        $this->addForeignKey('fk-product_lang-product_id', 'product_lang', 'product_id', 'product', 'id', 'CASCADE');

        $this->addForeignKey('fk-product_lang-lang_id', 'product_lang', 'lang_id', 'language', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-product_lang-lang_id', 'product_lang');

        $this->dropForeignKey('fk-product_lang-product_id', 'product_lang');

        $this->dropForeignKey('fk-product-brand_id', 'product');

        $this->dropForeignKey('fk-product_category-category_id', 'product_category');

        $this->dropForeignKey('fk-product_category-product_id', 'product_category');

        $this->dropPrimaryKey('pk-product_category', 'product_category');

        $this->dropPrimaryKey('pk-product_lang', 'product_lang');

        $this->dropTable('product_category');

        $this->dropTable('product_lang');

        $this->dropTable('product');
    }
}
