<?php

use yii\db\Migration;

/**
 * Class m200805_071636_alter_variant_price_column
 */
class m200805_071636_alter_variant_price_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%variant}}', 'price', $this->decimal(9,2));

        $this->alterColumn('{{%variant}}', 'price_old', $this->decimal(9,2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%variant}}', 'price', $this->integer());

        $this->alterColumn('{{%variant}}', 'price_old', $this->integer());
    }
}
