<?php

use yii\db\Migration;

/**
 * Handles the creation of table `category_image`.
 */
class m170325_195942_create_category_image_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        $this->createTable('category_image', [
            'category_id' => $this->integer()->notNull(),
            'image_id' => $this->integer()->notNull(),
            'position' => $this->integer()->notNull()->defaultValue(0),
            'enabled' => $this->integer()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->addPrimaryKey('pk-category_image', 'category_image', ['category_id', 'image_id']);

        $this->addForeignKey('fk-category_image-category_id', 'category_image', 'category_id', 'category', 'id', 'CASCADE');

        $this->addForeignKey('fk-category_image-image_id', 'category_image', 'image_id', 'image', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-category_image-image_id', 'category_image');

        $this->dropForeignKey('fk-category_image-category_id', 'category_image');

        $this->dropPrimaryKey('pk-category_image', 'category_image');

        $this->dropTable('category_image');
    }
}
