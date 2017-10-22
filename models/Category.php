<?php

namespace dench\products\models;

use dench\image\models\Image;
use dench\language\behaviors\LanguageBehavior;
use dench\sortable\behaviors\SortableBehavior;
use omgdef\multilingual\MultilingualQuery;
use voskobovich\linker\LinkerBehavior;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $slug
 * @property integer $image_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $position
 * @property boolean $enabled
 * @property boolean $main
 *
 *
 * @property string $name
 * @property string $h1
 * @property string $title
 * @property string $keywords
 * @property string $description
 * @property string $text
 * @property string $seo
 *
 * @property Image $image
 * @property Category $parent
 * @property Category[] $categories
 * @property Feature[] $features
 * @property Product[] $products
 * @property Image[] $images
 * @property Image[] $imagesAll
 * @property array $imageEnabled
 */
class Category extends ActiveRecord
{
    private $_imageEnabled = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            LanguageBehavior::className(),
            TimestampBehavior::className(),
            SortableBehavior::className(),
            'slug' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
                'ensureUnique' => true
            ],
            [
                'class' => LinkerBehavior::className(),
                'relations' => [
                    'feature_ids' => ['features'],
                    'product_ids' => ['products'],
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
            [['parent_id', 'image_id', 'position'], 'integer'],
            [['name', 'h1', 'title'], 'required'],
            [['slug', 'name', 'h1', 'title', 'keywords'], 'string', 'max' => 255],
            [['description', 'text', 'seo'], 'string'],
            [['slug', 'name', 'h1', 'title', 'keywords', 'description', 'text', 'seo'], 'trim'],
            [['enabled', 'main'], 'boolean'],
            [['enabled'], 'default', 'value' => true],
            [['feature_ids', 'product_ids', 'image_ids', 'imageEnabled'], 'each', 'rule' => ['integer']],
            [['image_id'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['image_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => Yii::t('app', 'Parent'),
            'slug' => Yii::t('app', 'Slug'),
            'image_id' => Yii::t('app', 'Image'),
            'created_at' => Yii::t('app', 'Created'),
            'updated_at' => Yii::t('app', 'Updated'),
            'position' => Yii::t('app', 'Position'),
            'enabled' => Yii::t('app', 'Enabled'),
            'name' => Yii::t('app', 'Name'),
            'h1' => Yii::t('app', 'H1'),
            'title' => Yii::t('app', 'Title'),
            'keywords' => Yii::t('app', 'Keywords'),
            'description' => Yii::t('app', 'Description'),
            'text' => Yii::t('app', 'Text'),
            'main' => Yii::t('app', 'Main'),
            'seo' => Yii::t('app', 'SEO text'),
        ];
    }

    public static function viewPage($id)
    {
        if (is_numeric($id)) {
            $page = self::findOne($id);
        } else {
            $page = self::findOne(['slug' => $id]);
        }
        if ($page === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        Yii::$app->view->params['page'] = $page;
        Yii::$app->view->title = $page->title;
        if ($page->description) {
            Yii::$app->view->registerMetaTag([
                'name' => 'description',
                'content' => $page->description
            ]);
        }
        if ($page->keywords) {
            Yii::$app->view->registerMetaTag([
                'name' => 'keywords',
                'content' => $page->keywords
            ]);
        }
        return $page;
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
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFeatures()
    {
        return $this->hasMany(Feature::className(), ['id' => 'feature_id'])->viaTable('feature_category', ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['id' => 'product_id'])->viaTable('product_category', ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        $name = $this->tableName();
        return $this->hasMany(Image::className(), ['id' => 'image_id'])
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
        return $this->hasMany(Image::className(), ['id' => 'image_id'])
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
        return $this->hasOne(Image::className(), ['id' => 'image_id']);
    }

    /**
     * @param boolean|null $enabled
     * @return array
     */
    public static function getList($enabled)
    {
        return ArrayHelper::map(self::find()->andFilterWhere(['enabled' => $enabled])->orderBy('position')->all(), 'id', 'name');
    }

    /**
     * @return \yii\db\ActiveQuery[]
     */
    public static function getMain()
    {
        return Category::find()->where(['enabled' => true, 'main' => true])->all();
    }

    /**
     * @return \yii\db\ActiveQuery[]
     */
    public static function getPodmenu()
    {
        return Yii::$app->cache->getOrSet('podmenu-' . Yii::$app->language, function () {
            $items = [];
            foreach (self::getMain() as $item) {
                $items[$item->id] = [
                    'id' => $item->id,
                    'slug' => $item->slug,
                    'name' => $item->name,
                ];
            }
            return $items;
        });
    }
}
