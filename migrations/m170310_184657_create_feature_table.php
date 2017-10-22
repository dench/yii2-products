<?php

use yii\db\Migration;

/**
 * Handles the creation of table `feature`.
 */
class m170310_184657_create_feature_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('feature', [
            'id' => $this->primaryKey(),
            'position' => $this->integer()->notNull()->defaultValue(0),
            'enabled' => $this->boolean()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->createTable('feature_lang', [
            'feature_id' => $this->integer()->notNull(),
            'lang_id' => $this->string(3)->notNull(),
            'name' => $this->string()->notNull(),
            'after' => $this->string(32),
        ], $tableOptions);

        $this->createTable('feature_category', [
            'feature_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-feature_lang', 'feature_lang', ['feature_id', 'lang_id']);

        $this->addPrimaryKey('pk-feature_category', 'feature_category', ['feature_id', 'category_id']);

        $this->addForeignKey('fk-feature_category-feature_id', 'feature_category', 'feature_id', 'feature', 'id', 'CASCADE');

        $this->addForeignKey('fk-feature_category-category_id', 'feature_category', 'category_id', 'category', 'id', 'CASCADE');

        $this->addForeignKey('fk-feature_lang-feature_id', 'feature_lang', 'feature_id', 'feature', 'id', 'CASCADE');

        $this->addForeignKey('fk-feature_lang-lang_id', 'feature_lang', 'lang_id', 'language', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-feature_lang-lang_id', 'feature_lang');

        $this->dropForeignKey('fk-feature_lang-feature_id', 'feature_lang');

        $this->dropForeignKey('fk-feature_category-category_id', 'feature_category');

        $this->dropForeignKey('fk-feature_category-feature_id', 'feature_category');

        $this->dropPrimaryKey('pk-feature_category', 'feature_category');

        $this->dropPrimaryKey('pk-feature_lang', 'feature_lang');

        $this->dropTable('feature_category');

        $this->dropTable('feature_lang');

        $this->dropTable('feature');
    }
}
