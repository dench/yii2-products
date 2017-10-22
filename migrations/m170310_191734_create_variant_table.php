<?php

use yii\db\Migration;

/**
 * Handles the creation of table `variant`.
 */
class m170310_191734_create_variant_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('variant', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'code' => $this->string(),
            'price' => $this->integer(),
            'price_old' => $this->integer(),
            'currency_id' => $this->integer()->notNull(),
            'unit_id' => $this->integer()->notNull(),
            'available' => $this->integer(),
            'image_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'position' => $this->integer()->notNull()->defaultValue(0),
            'enabled' => $this->boolean()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->createTable('variant_lang', [
            'variant_id' => $this->integer()->notNull(),
            'lang_id' => $this->string(3)->notNull(),
            'name' => $this->string(),
        ], $tableOptions);

        $this->createTable('variant_value', [
            'variant_id' => $this->integer()->notNull(),
            'value_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-variant-image_id','variant', 'image_id');

        $this->addPrimaryKey('pk-variant_lang', 'variant_lang', ['variant_id', 'lang_id']);

        $this->addPrimaryKey('pk-variant_value', 'variant_value', ['variant_id', 'value_id']);

        $this->addForeignKey('fk-variant_value-variant_id', 'variant_value', 'variant_id', 'variant', 'id', 'CASCADE');

        $this->addForeignKey('fk-variant_value-value_id', 'variant_value', 'value_id', 'value', 'id', 'CASCADE');

        $this->addForeignKey('fk-variant-product_id', 'variant', 'product_id', 'product', 'id', 'CASCADE');

        $this->addForeignKey('fk-variant-currency_id', 'variant', 'currency_id', 'currency', 'id');

        $this->addForeignKey('fk-variant-unit_id', 'variant', 'unit_id', 'unit', 'id');

        $this->addForeignKey('fk-variant_lang-variant_id', 'variant_lang', 'variant_id', 'variant', 'id', 'CASCADE');

        $this->addForeignKey('fk-variant_lang-lang_id', 'variant_lang', 'lang_id', 'language', 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('fk-variant-image_id', 'variant', 'image_id', 'image', 'id', 'SET NULL');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-variant-image_id', 'variant');

        $this->dropForeignKey('fk-variant_lang-lang_id', 'variant_lang');

        $this->dropForeignKey('fk-variant_lang-variant_id', 'variant_lang');

        $this->dropForeignKey('fk-variant-unit_id', 'variant');

        $this->dropForeignKey('fk-variant-currency_id', 'variant');

        $this->dropForeignKey('fk-variant-product_id', 'variant');

        $this->dropForeignKey('fk-variant_value-value_id', 'variant_value');

        $this->dropForeignKey('fk-variant_value-variant_id', 'variant_value');

        $this->dropPrimaryKey('pk-variant_value', 'variant_value');

        $this->dropPrimaryKey('pk-variant_lang', 'variant_lang');

        $this->dropIndex('idx-variant-image_id', 'variant');

        $this->dropTable('variant_value');

        $this->dropTable('variant_lang');

        $this->dropTable('variant');
    }
}
