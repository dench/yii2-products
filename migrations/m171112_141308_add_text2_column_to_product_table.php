<?php

use yii\db\Migration;

/**
 * Handles adding text2 to table `product`.
 */
class m171112_141308_add_text2_column_to_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('product_lang', 'text_tips', 'text');
        $this->addColumn('product_lang', 'text_features', 'text');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('product_lang', 'text_tips');
        $this->dropColumn('product_lang', 'text_features');
    }
}
