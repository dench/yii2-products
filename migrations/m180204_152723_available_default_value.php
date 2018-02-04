<?php

use yii\db\Migration;

/**
 * Class m180204_152723_available_default_value
 */
class m180204_152723_available_default_value extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('variant', 'available', 'integer not null default 1');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->alterColumn('variant', 'available', 'integer');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180204_152723_available_default_value cannot be reverted.\n";

        return false;
    }
    */
}
