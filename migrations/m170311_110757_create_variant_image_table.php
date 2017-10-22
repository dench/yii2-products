<?php

use yii\db\Migration;

/**
 * Handles the creation of table `variant_image`.
 */
class m170311_110757_create_variant_image_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('variant_image', [
            'variant_id' => $this->integer()->notNull(),
            'image_id' => $this->integer()->notNull(),
            'enabled' => $this->integer()->notNull()->defaultValue(1),
            'position' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addPrimaryKey('pk-variant_image', 'variant_image', ['variant_id', 'image_id']);
        
        $this->addForeignKey('fk-variant_image-variant_id', 'variant_image', 'variant_id', 'variant', 'id', 'CASCADE');
        
        $this->addForeignKey('fk-variant_image-image_id', 'variant_image', 'image_id', 'image', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-variant_image-image_id', 'variant_image');
        
        $this->dropForeignKey('fk-variant_image-variant_id', 'variant_image');
        
        $this->dropPrimaryKey('pk-variant_image', 'variant_image');
        
        $this->dropTable('variant_image');
    }
}
