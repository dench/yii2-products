<?php

use yii\db\Migration;

class m170402_155313_create_many_to_many_product_complect extends Migration
{
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->dropForeignKey('fk-complect-product_id', 'complect');

        $this->dropColumn('complect', 'product_id');

        $this->createTable('product_complect', [
            'product_id' => $this->integer()->notNull(),
            'complect_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-product_complect', 'product_complect', ['product_id', 'complect_id']);

        $this->addForeignKey('fk-product_complect-product_id', 'product_complect', 'product_id', 'product', 'id', 'CASCADE');

        $this->addForeignKey('fk-product_complect-complect_id', 'product_complect', 'complect_id', 'complect', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk-product_complect-complect_id', 'product_complect');

        $this->dropForeignKey('fk-product_complect-product_id', 'product_complect');

        $this->dropPrimaryKey('pk-product_complect', 'product_complect');

        $this->dropTable('product_complect');

        $this->addColumn('complect', 'product_id', 'integer');

        $this->addForeignKey('fk-complect-product_id', 'complect', 'product_id', 'product', 'id', 'CASCADE');
    }
}
