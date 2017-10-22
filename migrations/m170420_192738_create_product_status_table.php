<?php

use yii\db\Migration;

/**
 * Handles the creation of table `product_status`.
 */
class m170420_192738_create_product_status_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('product_status', [
            'product_id' => $this->integer()->notNull(),
            'status_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-product_status', 'product_status', ['product_id', 'status_id']);

        $this->addForeignKey('fk-product_status-product_id', 'product_status', 'product_id', 'product', 'id', 'CASCADE');

        $this->addForeignKey('fk-product_status-status_id', 'product_status', 'status_id', 'status', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-product_status-status_id', 'product_status');

        $this->dropForeignKey('fk-product_status-product_id', 'product_status');

        $this->dropPrimaryKey('pk-product_status', 'product_status');

        $this->dropTable('product_status');
    }
}
