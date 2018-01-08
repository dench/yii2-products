<?php

use yii\db\Migration;

/**
 * Handles the creation of table `product_file`.
 */
class m180107_121423_create_product_file_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('product_file', [
            'product_id' => $this->integer()->notNull(),
            'file_id' => $this->integer()->notNull(),
            'position' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addPrimaryKey('pk-product_file', 'product_file', ['product_id', 'file_id']);

        $this->addForeignKey('fk-product_file-product_id', 'product_file', 'product_id', 'product', 'id', 'CASCADE');

        $this->addForeignKey('fk-product_file-file_id', 'product_file', 'file_id', 'file', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-product_file-file_id', 'product_file');

        $this->dropForeignKey('fk-product_file-product_id', 'product_file');

        $this->dropPrimaryKey('pk-product_file', 'product_file');

        $this->dropTable('product_file');
    }
}
