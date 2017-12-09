<?php

use yii\db\Migration;

/**
 * Handles adding text3 to table `product`.
 */
class m171209_103939_add_text3_column_to_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('product_lang', 'text_process', 'text');
        $this->addColumn('product_lang', 'text_use', 'text');
        $this->addColumn('product_lang', 'text_storage', 'text');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('product_lang', 'text_process');
        $this->dropColumn('product_lang', 'text_use');
        $this->dropColumn('product_lang', 'text_storage');
    }
}
