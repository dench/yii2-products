<?php

namespace dench\products\models;

use dench\language\behaviors\LanguageBehavior;
use dench\sortable\behaviors\SortableBehavior;
use omgdef\multilingual\MultilingualQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "currency".
 *
 * @property integer $id
 * @property string $code
 * @property string $rate
 * @property integer $position
 * @property boolean $enabled
 *
 * @property string $name
 * @property string $before
 * @property string $after
 */
class Currency extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'currency';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            LanguageBehavior::class,
            SortableBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['before', 'after'], 'string', 'max' => 32],
            [['code', 'rate', 'name'], 'required'],
            [['rate'], 'number'],
            [['position'], 'integer'],
            [['code'], 'string', 'max' => 3],
            [['enabled'], 'boolean'],
            [['enabled'], 'default', 'value' => true],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' =>'ID',
            'code' => Yii::t('app', 'Code'),
            'rate' => Yii::t('app', 'Rate'),
            'name' => Yii::t('app', 'Name'),
            'before' => Yii::t('app', 'Before'),
            'after' => Yii::t('app', 'After'),
            'position' => Yii::t('app', 'Position'),
            'enabled' => Yii::t('app', 'Enabled'),
        ];
    }

    /**
     * @return MultilingualQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        return new MultilingualQuery(get_called_class());
    }

    /**
     * @param boolean|null $enabled
     * @return array
     */
    public static function getList($enabled)
    {
        return ArrayHelper::map(self::find()->andFilterWhere(['enabled' => $enabled])->orderBy(['position' => SORT_ASC])->all(), 'id', 'name');
    }
}
