<?php

namespace dench\products\controllers;

use dench\products\models\Feature;
use dench\image\models\Image;
use dench\language\models\Language;
use dench\sortable\actions\SortingAction;
use Yii;
use dench\products\models\Variant;
use dench\products\models\VariantSearch;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VariantController implements the CRUD actions for Variant model.
 */
class VariantController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'sorting' => [
                'class' => SortingAction::class,
                'query' => Variant::find(),
            ],
        ];
    }

    /**
     * Lists all Variant models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VariantSearch(['all' => Yii::$app->request->get('all')]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Variant model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Variant model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Variant();

        $model->loadDefaultValues();

        $model->product_id = Yii::$app->request->get('product_id');

        $features = !empty($model->product->category_ids) ? Feature::getObjectList(true, $model->product->category_ids) : [];

        $images = [];

        if ($post = Yii::$app->request->post()) {
            /** @var Image[] $images */
            $images = [];
            $image_ids = isset($post['Image']) ? $post['Image'] : [];
            foreach ($image_ids as $key => $image) {
                $images[$key] = Image::findOne($key);
            }
            if ($images) {
                Model::loadMultiple($images, $post);
            } else {
                $model->image_ids = [];
            }

            $model->load($post);

            $error = [];
            if (!$model->validate()) $error['model'] = $model->errors;
            foreach ($images as $key => $image) {
                if (!$image->validate()) $error['image'][$key] = $image->errors;
            }
            if (empty($error)) {
                foreach ($images as $key => $image) {
                    $image->save(false);
                }
                if (!$model->image_id && $images) {
                    $image = current($images);
                    $model->image_id = $image->id;
                }
                $model->save(false);
                Yii::$app->session->setFlash('success', Yii::t('app', 'Information added successfully'));
                foreach (Language::find()->select('id')->column() as $lang) {
                    Yii::$app->cache->delete('_product_card-' . $model->product->id . '-' . $lang);
                }
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'features' => $features,
            'images' => $images,
        ]);
    }

    /**
     * Updates an existing Variant model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModelMulti($id);

        $features = Feature::getObjectList(true, $model->product->category_ids);

        $images = $model->imagesAll;

        if ($post = Yii::$app->request->post()) {
            $model->load($post);
            $old_ids = ArrayHelper::map($images, 'id', 'id');
            /** @var Image[] $images */
            $images = [];
            $image_ids = isset($post['Image']) ? $post['Image'] : [];
            $new_ids = [];
            foreach ($image_ids as $key => $image) {
                $images[$key] = Image::findOne($key);
                $new_ids[$key] = $key;
            }
            if ($images) {
                Model::loadMultiple($images, $post);
            } else {
                $model->image_ids = [];
            }
            $deleted_ids = array_diff($old_ids, $new_ids);

            $error = [];
            if (!$model->validate()) $error['model'] = $model->errors;
            foreach ($images as $key => $image) {
                if (!$image->validate()) $error['image'][$key] = $image->errors;
            }
            if (empty($error)) {
                foreach ($images as $key => $image) {
                    $image->save(false);
                }
                foreach ($deleted_ids as $d_id) {
                    if ($deleted_image = Image::findOne($d_id)) {
                        $deleted_image->delete();
                    }
                }
                if (!$model->image_id && $images) {
                    $image = current($images);
                    $model->image_id = $image->id;
                }
                $model->save(false);
                Yii::$app->session->setFlash('success', Yii::t('app', 'Information has been saved successfully'));
                foreach (Language::find()->select('id')->column() as $lang) {
                    Yii::$app->cache->delete('_product_card-' . $model->product->id . '-' . $lang);
                }
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'features' => $features,
            'images' => $images,
        ]);
    }

    /**
     * Deletes an existing Variant model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Variant model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Variant the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Variant::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Variant|\yii\db\ActiveRecord
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelMulti($id)
    {
        if (($model = Variant::find()->where(['id' => $id])->multilingual()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
