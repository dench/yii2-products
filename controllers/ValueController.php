<?php

namespace dench\products\controllers;

use dench\sortable\actions\SortingAction;
use Yii;
use dench\products\models\Value;
use dench\products\models\ValueSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ValueController implements the CRUD actions for Value model.
 */
class ValueController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
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
                'class' => SortingAction::className(),
                'query' => Value::find(),
            ],
        ];
    }

    /**
     * Lists all Value models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ValueSearch(['all' => Yii::$app->request->get('all')]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $feature_id = Yii::$app->request->get('ValueSearch')['feature_id'];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'feature_id' => $feature_id,
        ]);
    }

    /**
     * Displays a single Value model.
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
     * Creates a new Value model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Value();

        $model->loadDefaultValues();

        $model->feature_id = Yii::$app->request->get('feature_id');

        $success = false;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $success = true;
        }

        if (Yii::$app->request->isAjax) {
            if ($success) {
                return 'success';
            } else {
                return $this->renderAjax('_form', [
                    'model' => $model,
                    'modal' => true,
                ]);
            }
        }

        if ($success) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Information has been saved successfully'));
            return $this->redirect(['index', 'ValueSearch[feature_id]' => $model->feature_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'modal' => false,
            ]);
        }
    }

    /**
     * Updates an existing Value model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModelMulti($id);

        $success = false;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $success = true;
        }

        if (Yii::$app->request->isAjax) {
            if ($success) {
                return 'success';
            } else {;
                return $this->renderAjax('_form', [
                    'model' => $model,
                    'modal' => true,
                ]);
            }
        }

        if ($success) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Information has been saved successfully'));
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'modal' => false,
            ]);
        }
    }

    /**
     * Deletes an existing Value model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->delete();

        return $this->redirect(['index', 'ValueSearch[feature_id]' => $model->feature_id]);
    }

    /**
     * Finds the Value model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Value the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Value::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Value|\yii\db\ActiveRecord
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelMulti($id)
    {
        if (($model = Value::find()->where(['id' => $id])->multilingual()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
