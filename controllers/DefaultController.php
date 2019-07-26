<?php

namespace dench\products\controllers;

use dench\image\models\File;
use dench\language\models\Language;
use dench\products\models\Feature;
use dench\products\models\Model;
use dench\products\models\Variant;
use dench\image\models\Image;
use dench\sortable\actions\SortingAction;
use Exception;
use Yii;
use dench\products\models\Product;
use dench\products\models\ProductSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class DefaultController extends Controller
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
                'query' => Product::find(),
            ],
        ];
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch(['all' => Yii::$app->request->get('all')]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
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
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = 0)
    {
        if ($id) {
            $model = $this->findModelMulti($id);

            $model->slug = '';

            /** @var $modelsVariant Variant[] */
            $modelsVariant = Variant::find()->where(['product_id' => $id])->multilingual()->all();

            $variantImages = [];
            foreach ($modelsVariant as $key => $modelVariant) {
                $variantImages[$key] = $modelVariant->imagesAll;
            }

            $model->isNewRecord = true;
        } else {
            $model = new Product();

            $model->loadDefaultValues();

            $modelsVariant = [new Variant()];

            $modelsVariant[0]->loadDefaultValues();

            $variantImages[0] = $modelsVariant[0]->imagesAll;
        }

        $features = Feature::getObjectList(true, $model->category_ids);

        $files = [];

        if ($post = Yii::$app->request->post()) {
            if (!Yii::$app->request->isPjax) {

                if ($id) {
                    $model->id = 0;
                }

                /** @var File[] $files */
                $files = [];
                $file_ids = isset($post['File']) ? $post['File'] : [];
                foreach ($file_ids as $key => $file) {
                    $files[$key] = File::findOne($key);
                }
                if ($files) {
                    Model::loadMultiple($files, $post);
                } else {
                    $model->file_ids = [];
                }

                $model->load($post);

                $modelsVariant = Model::createMultiple(Variant::class, $modelsVariant);
                Model::loadMultiple($modelsVariant, Yii::$app->request->post());

                // ajax validation
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ArrayHelper::merge(
                        ActiveForm::validateMultiple($modelsVariant),
                        ActiveForm::validate($model)
                    );
                }

                /** @var Image[] $images */
                $images = [];
                $image_ids = isset($post['Image']) ? $post['Image'] : [];
                foreach ($image_ids as $key => $image) {
                    $images[$key] = Image::findOne($key);
                }
                if ($images) {
                    Model::loadMultiple($images, $post);
                }

                $valid = $model->validate();
                $valid = Model::validateMultiple($modelsVariant) && $valid;
                $valid = Model::validateMultiple($images) && $valid;
                $valid = Model::validateMultiple($files) && $valid;

                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save(false)) {
                            foreach ($modelsVariant as $k => $modelVariant) {
                                if (!isset($post['Variant'][$k]['image_ids'])) {
                                    $modelVariant->image_ids = [];
                                    $modelVariant->image_id = null;
                                    $images = [];
                                }
                                foreach ($files as $key => $file) {
                                    if (!($flag = $file->save(false))) {
                                        $transaction->rollBack();
                                        break;
                                    }
                                }
                                /** @var Variant $modelVariant */
                                if (!$modelVariant->image_id && !empty($modelVariant->image_ids)) {
                                    $modelVariant->image_id = current($modelVariant->image_ids);
                                }
                                if ($id) {
                                    $modelVariant->id = 0;
                                    $modelVariant->isNewRecord = true;
                                }
                                $modelVariant->product_id = $model->id;
                                if (!isset($post['Variant'][$k]['value_ids'])) {
                                    $modelVariant->value_ids = [];
                                }
                                if (!($flag = $modelVariant->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                            foreach ($images as $key => $image) {
                                if (!($flag = $image->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            Yii::$app->session->setFlash('success',
                                Yii::t('app', 'Information has been saved successfully'));
                            return $this->redirect(['index']);
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
            } else {
                if (!empty($model->category_ids)) {
                    $features = Feature::getObjectList(true, $model->category_ids);
                } else {
                    $features = [];
                }
                $modelsVariant = Model::createMultiple(Variant::class, $modelsVariant);
                Model::loadMultiple($modelsVariant, $post);
                $variantImages = [];
                foreach ($modelsVariant as $key => $modelVariant) {
                    foreach ($modelVariant->image_ids as $image_id) {
                        $image = Image::findOne($image_id);
                        $variantImages[$key][$image_id] = $image;
                    }
                    if (isset($variantImages[$key])) {
                        Model::loadMultiple($variantImages[$key], $post);
                    } else {
                        $variantImages[$key] = $modelVariant->imagesAll;
                    }
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'modelsVariant' => $modelsVariant,
            'variantImages' => $variantImages,
            'features' => $features,
            'files' => $files,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        /** @var $model Product */
        $model = $this->findModelMulti($id);

        /** @var $modelsVariant Variant[] */
        $modelsVariant = Variant::find()->where(['product_id' => $id])->multilingual()->all();

        $variantImages = [];
        foreach ($modelsVariant as $key => $modelVariant) {
            $variantImages[$key] = $modelVariant->imagesAll;
        }

        $features = Feature::getObjectList(true, $model->category_ids);

        $files = $model->filesAll;

        if ($post = Yii::$app->request->post()) {

            $model->load($post);

            if (!Yii::$app->request->isPjax) {

                $oldIDs = ArrayHelper::map($modelsVariant, 'id', 'id');
                $modelsVariant = Model::createMultiple(Variant::class, $modelsVariant);
                Model::loadMultiple($modelsVariant, $post);
                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsVariant, 'id', 'id')));

                // ajax validation
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ArrayHelper::merge(
                        ActiveForm::validateMultiple($modelsVariant),
                        ActiveForm::validate($model)
                    );
                }

                $images = [];
                foreach ($variantImages as $variantImage) {
                    array_push($images, $variantImage);
                }
                $old_ids = ArrayHelper::map($images, 'id', 'id');
                /** @var Image[] $images */
                $images = [];
                $new_ids = [];
                $image_ids = isset($post['Image']) ? $post['Image'] : [];
                foreach ($image_ids as $key => $image) {
                    $images[$key] = Image::findOne($key);
                    $new_ids[$key] = $key;
                }
                if ($images) {
                    Model::loadMultiple($images, $post);
                }
                $deleted_ids = array_diff($old_ids, $new_ids);

                //$f_old_ids = ArrayHelper::map($files, 'id', 'id');
                /** @var File[] $files */
                $files = [];
                $file_ids = isset($post['File']) ? $post['File'] : [];
                $f_new_ids = [];
                foreach ($file_ids as $key => $file) {
                    $files[$key] = File::findOne($key);
                    $f_new_ids[$key] = $key;
                }
                if ($files) {
                    Model::loadMultiple($files, $post);
                } else {
                    $model->file_ids = [];
                }
                //$f_deleted_ids = array_diff($f_old_ids, $f_new_ids);

                $valid = $model->validate();
                $valid = Model::validateMultiple($modelsVariant) && $valid;
                $valid = Model::validateMultiple($images) && $valid;
                $valid = Model::validateMultiple($files) && $valid;

                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save(false)) {
                            if (!empty($deletedIDs)) {
                                Variant::deleteAll(['id' => $deletedIDs]);
                            }
                            foreach ($deleted_ids as $d_id) {
                                if ($deleted_image = Image::findOne($d_id)) {
                                    $deleted_image->delete();
                                }
                            }
                            foreach ($files as $key => $file) {
                                if (!($flag = $file->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                            /* Deletes all files, including files tied to other objects.
                             * foreach ($f_deleted_ids as $d_id) {
                                if ($deleted_file = File::findOne($d_id)) {
                                    $deleted_file->delete();
                                }
                            }*/
                            foreach ($modelsVariant as $k => $modelVariant) {
                                if (!isset($post['Variant'][$k]['image_ids'])) {
                                    $modelVariant->image_ids = [];
                                    $modelVariant->image_id = null;
                                    $images = [];
                                }
                                /** @var Variant $modelVariant */
                                if (!$modelVariant->image_id && !empty($modelVariant->image_ids)) {
                                    $modelVariant->image_id = current($modelVariant->image_ids);
                                }
                                $modelVariant->product_id = $model->id;
                                if (!isset($post['Variant'][$k]['value_ids'])) {
                                    $modelVariant->value_ids = [];
                                }
                                if (!($flag = $modelVariant->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                            foreach ($images as $key => $image) {
                                if (!($flag = $image->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            Yii::$app->session->setFlash('success',
                                Yii::t('app', 'Information has been saved successfully'));
                            foreach (Language::find()->select('id')->column() as $lang) {
                                Yii::$app->cache->delete('_product_card-' . $id . '-' . $lang);
                            }
                            return $this->redirect(['index']);
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
            } else {
                if (!empty($model->category_ids)) {
                    $features = Feature::getObjectList(true, $model->category_ids);
                } else {
                    $features = [];
                }
                $modelsVariant = Model::createMultiple(Variant::class, $modelsVariant);
                Model::loadMultiple($modelsVariant, $post);
                $variantImages = [];
                foreach ($modelsVariant as $key => $modelVariant) {
                    foreach ($modelVariant->image_ids as $image_id) {
                        $image = Image::findOne($image_id);
                        $variantImages[$key][$image_id] = $image;
                    }
                    if (isset($variantImages[$key])) {
                        Model::loadMultiple($variantImages[$key], $post);
                    } else {
                        $variantImages[$key] = $modelVariant->imagesAll;
                    }
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelsVariant' => (empty($modelsVariant)) ? [new Variant()] : $modelsVariant,
            'variantImages' => $variantImages,
            'features' => $features,
            'files' => $files,
        ]);
    }

    /**
     * Deletes an existing Product model.
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
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product|\yii\db\ActiveRecord
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelMulti($id)
    {
        if (($model = Product::find()->where(['id' => $id])->multilingual()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
