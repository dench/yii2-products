<?php

use yii\db\Migration;

/**
 * Handles the creation of table `category`.
 */
class m170310_172422_create_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('category', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(),
            'slug' => $this->string()->notNull(),
            'image_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'main' => $this->boolean()->notNull()->defaultValue(0),
            'position' => $this->integer()->notNull()->defaultValue(0),
            'enabled' => $this->boolean()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->createTable('category_lang', [
            'category_id' => $this->integer()->notNull(),
            'lang_id' => $this->string(3)->notNull(),
            'name' => $this->string()->notNull(),
            'title' => $this->string()->notNull(),
            'h1' => $this->string()->notNull(),
            'keywords' => $this->string(),
            'description' => $this->text(),
            'text' => $this->text(),
            'seo' => $this->text(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-category_lang', 'category_lang', ['category_id', 'lang_id']);

        $this->addForeignKey('fk-category-image_id', 'category', 'image_id', 'image', 'id', 'SET NULL');

        $this->addForeignKey('fk-category-parent_id', 'category', 'parent_id', 'category', 'id', 'SET NULL');

        $this->addForeignKey('fk-category_lang-category_id', 'category_lang', 'category_id', 'category', 'id', 'CASCADE');

        $this->addForeignKey('fk-category_lang-lang_id', 'category_lang', 'lang_id', 'language', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-category_lang-lang_id', 'category_lang');

        $this->dropForeignKey('fk-category_lang-category_id', 'category_lang');

        $this->dropForeignKey('fk-category-parent_id', 'category');

        $this->dropForeignKey('fk-category-image_id', 'category');

        $this->dropPrimaryKey('pk-category_lang', 'category_lang');

        $this->dropTable('category_lang');

        $this->dropTable('category');
    }
}
