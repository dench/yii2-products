<?php

use yii\db\Migration;

/**
 * Handles the creation of table `feature_filter`.
 */
class m170504_183134_create_feature_filter_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('feature_filter', [
            'feature_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-feature_filter', 'feature_filter', ['feature_id', 'category_id']);

        $this->addForeignKey('fk-feature_filter-feature_id', 'feature_filter', 'feature_id', 'feature', 'id', 'CASCADE');

        $this->addForeignKey('fk-feature_filter-category_id', 'feature_filter', 'category_id', 'category', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-feature_filter-category_id', 'feature_filter');

        $this->dropForeignKey('fk-feature_filter-feature_id', 'feature_filter');

        $this->dropPrimaryKey('pk-feature_filter', 'feature_filter');

        $this->dropTable('feature_filter');
    }
}
