<?php

namespace dench\products\models;

use dench\image\models\Image;
use dench\language\behaviors\LanguageBehavior;
use dench\sortable\behaviors\SortableBehavior;
use omgdef\multilingual\MultilingualQuery;
use voskobovich\linker\LinkerBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "variant".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $code
 * @property integer $price
 * @property integer $price_old
 * @property integer $currency_id
 * @property integer $unit_id
 * @property integer $available
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $position
 * @property boolean $enabled
 * @property integer $image_id
 *
 * @property string $name
 * @property integer $priceDef
 * @property Currency $currencyDef
 *
 * @property Currency $currency
 * @property Product $product
 * @property Unit $unit
 * @property Value[] $values
 * @property Image[] $images
 * @property Image[] $imagesAll
 * @property Image $image
 * @property array $imageEnabled
 */
class Variant extends ActiveRecord
{
    private $_imageEnabled = null;
    private $_price;
    private $_currency;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'variant';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            LanguageBehavior::class,
            TimestampBehavior::class,
            SortableBehavior::class,
            [
                'class' => LinkerBehavior::class,
                'relations' => [
                    'value_ids' => ['values'],
                    'image_ids' => [
                        'images',
                        'updater' => [
                            'viaTableAttributesValue' => [
                                'position' => function($updater, $relatedPk, $rowCondition) {
                                    $primaryModel = $updater->getBehavior()->owner;
                                    $image_ids = array_values($primaryModel->image_ids);
                                    return array_search($relatedPk, $image_ids);
                                },
                                'enabled' => function($updater, $relatedPk, $rowCondition) {
                                    $primaryModel = $updater->getBehavior()->owner;
                                    return !empty($primaryModel->imageEnabled[$relatedPk]) ? 1 : 0;
                                },
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['currency_id', 'unit_id'], 'required'],
            [['product_id', 'currency_id', 'unit_id', 'available', 'position', 'image_id'], 'integer'],
            [['price', 'price_old'], 'number'],
            [['code', 'name'], 'string', 'max' => 255],
            [['enabled'], 'boolean'],
            [['enabled'], 'default', 'value' => true],
            [['value_ids', 'image_ids', 'imageEnabled'], 'each', 'rule' => ['integer']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
            [['unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::class, 'targetAttribute' => ['unit_id' => 'id']],
            [['image_id'], 'exist', 'skipOnError' => true, 'targetClass' => Image::class, 'targetAttribute' => ['image_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => Yii::t('app', 'Product'),
            'code' => Yii::t('app', 'Vendor code'),
            'name' => Yii::t('app', 'Name'),
            'price' => Yii::t('app', 'Price'),
            'price_old' => Yii::t('app', 'Price old'),
            'currency_id' => Yii::t('app', 'Currency'),
            'unit_id' => Yii::t('app', 'Unit'),
            'available' => Yii::t('app', 'Available'),
            'created_at' => Yii::t('app', 'Created'),
            'updated_at' => Yii::t('app', 'Updated'),
            'position' => Yii::t('app', 'Position'),
            'enabled' => Yii::t('app', 'Enabled'),
            'image_id' => Yii::t('app', 'Image'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::class, ['id' => 'currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::class, ['id' => 'unit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValues()
    {
        return $this->hasMany(Value::class, ['id' => 'value_id'])->viaTable('variant_value', ['variant_id' => 'id'])->orderBy(['position' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        $name = $this->tableName();
        return $this->hasMany(Image::class, ['id' => 'image_id'])
            ->viaTable($name . '_image', [$name . '_id' => 'id'])
            ->leftJoin($name . '_image', 'id=image_id')
            ->where([$name . '_image.' . $name . '_id' => $this->id])
            ->andFilterWhere([$name . '_image.enabled' => true])
            ->orderBy([$name . '_image.position' => SORT_ASC])
            ->indexBy('id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImagesAll()
    {
        $name = $this->tableName();
        return $this->hasMany(Image::class, ['id' => 'image_id'])
            ->viaTable($name . '_image', [$name . '_id' => 'id'])
            ->leftJoin($name . '_image', 'id=image_id')
            ->where([$name . '_image.' . $name . '_id' => $this->id])
            ->orderBy([$name . '_image.position' => SORT_ASC])
            ->indexBy('id');
    }

    public function getImageEnabled()
    {
        if ($this->_imageEnabled != null) {
            return $this->_imageEnabled;
        }
        $name = $this->tableName();
        return $this->_imageEnabled = (new \yii\db\Query())
            ->select(['enabled'])
            ->from($name . '_image')
            ->where([$name . '_id' => $this->id])
            ->indexBy('image_id')
            ->column();
    }

    public function setImageEnabled($value)
    {
        $this->_imageEnabled = $value;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        return $this->hasOne(Image::class, ['id' => 'image_id']);
    }

    public function getPriceDef()
    {
        if (empty($this->_currency)) {
            $this->_currency = Currency::findOne(Yii::$app->params['currency_id']);
        }

        if (empty($this->_currency)) {
            return $this->price;
        } else {
            return round($this->price * $this->_currency->rate);
        }
    }

    public function getCurrencyDef()
    {
        if (empty($this->_currency)) {
            $this->_currency = Currency::findOne(Yii::$app->params['currency_id']);
        }

        if (empty($this->_currency)) {
            return $this->currency;
        } else {
            return $this->_currency;
        }
    }
}
