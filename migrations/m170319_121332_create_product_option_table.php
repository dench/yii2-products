<?php

use yii\db\Migration;

/**
 * Handles the creation of table `product_option`.
 */
class m170319_121332_create_product_option_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('product_option', [
            'product_id' => $this->integer()->notNull(),
            'option_id' => $this->integer()->notNull(),
            'position' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addPrimaryKey('pk-product_option', 'product_option', ['product_id', 'option_id']);

        $this->addForeignKey('fk-product_option-product_id', 'product_option', 'product_id', 'product', 'id', 'CASCADE');

        $this->addForeignKey('fk-product_option-option_id', 'product_option', 'option_id', 'product', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-product_option-option_id', 'product_option');

        $this->dropForeignKey('fk-product_option-product_id', 'product_option');

        $this->dropPrimaryKey('pk-product_option', 'product_option');

        $this->dropTable('product_option');
    }
}
