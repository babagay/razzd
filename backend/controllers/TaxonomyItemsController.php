<?php

namespace backend\controllers;

use Yii;
use backend\models\TaxonomyItems;
use backend\models\TaxonomyItemsSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\web\Response;
use common\helpers\DataHelper;

/**
 * TaxonomyItemsController implements the CRUD actions for TaxonomyItems model.
 */
class TaxonomyItemsController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'term-find', 'parent-term', 'hierarchy', 'update', 'view', 'delete', 'create'],
                        'allow' => true,
                        'roles' => ['moderator'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all TaxonomyItems models.
     * @return mixed
     */
    public function actionIndex() {

        $searchModel = new TaxonomyItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all TaxonomyItems hierarchy models.
     * @return mixed
     */
    public function actionHierarchy() {

        $searchModel = new TaxonomyItemsSearch();

        if ($data = Yii::$app->request->post()) {

            $newOrder = $this->toFlat($data['data']);
            $models = $searchModel->findAll(['vid' => $data['vid']]);

            foreach ($models as $model) {
                if (isset($newOrder[$model->id]['pid']) && ($newOrder[$model->id]['pid'] != $model->pid || $newOrder[$model->id]['weight'] != $model->weight)) {
                    $model->pid = $newOrder[$model->id]['pid'];
                    $model->weight = $newOrder[$model->id]['weight'];
                    $model->save();
                }
            }
            exit('Ordered');
        } elseif (($searchModel->load(Yii::$app->request->get()) && $searchModel->validate())) {
            return $this->render('hierarchy', [
                        'model' => $searchModel,
                        'tree' => $this->tree($searchModel->vid, $searchModel->pid),
            ]);
        }

        return $this->render('hierarchy', [
                    'model' => $searchModel,
                    'tree' => [],
        ]);
    }

    public function actionParentTerm($id, $vid, $query) {

        if ($id)
            $items = \Yii::$app->db->createCommand('SELECT id,name FROM {{%taxonomy_items}} taxonomy_items WHERE id != :id AND pid != :id AND vid = :vid AND name LIKE :name  LIMIT 20')->bindValue(':id', $id)->bindValue(':vid', $vid)->bindValue(':name', rawurldecode($query) . '%')->queryAll();
        else
            $items = \Yii::$app->db->createCommand('SELECT id,name FROM {{%taxonomy_items}} taxonomy_items WHERE   vid = :vid AND name LIKE :name  LIMIT 20')->bindValue(':vid', $vid)->bindValue(':name', rawurldecode($query) . '%')->queryAll();

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $items;
    }

    public function actionTermFind($vid, $query) {

        $items = \Yii::$app->db->createCommand('SELECT name,CONCAT("[id:",id,"]")  as id FROM {{%taxonomy_items}} taxonomy_items WHERE  vid = :vid AND name LIKE :name  LIMIT 20')->bindValue(':vid', $vid)->bindValue(':name', rawurldecode($query) . '%')->queryAll();

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $items;
    }

    /**
     * Displays a single TaxonomyItems model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TaxonomyItems model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new TaxonomyItems();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['/taxonomy-items', 'TaxonomyItemsSearch' => ['vid' => $model->vid]]);
        } else {
            $model->load(Yii::$app->request->get());
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TaxonomyItems model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/taxonomy-items', 'TaxonomyItemsSearch' => ['vid' => $model->vid]]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TaxonomyItems model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $_model = $this->findModel($id);
        $vid = $_model->vid;
        $_model->delete();

        return $this->redirect(['/taxonomy-items', 'TaxonomyItemsSearch' => ['vid' => $vid]]);
    }

    /**
     * Finds the TaxonomyItems model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TaxonomyItems the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = TaxonomyItems::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function tree($vid, $parent = 0, $max_depth = NULL) {

        static $trees = [];

        if (empty($trees[$vid])) {
            $tree = array();
            $cleanParent = array();
            $elems = \Yii::$app->db->createCommand('SELECT * FROM {{%taxonomy_items}} taxonomy_items WHERE  vid = :vid ORDER BY weight')->bindValue(':vid', $vid)->queryAll();

            foreach ($elems as $itm) {
                $tree[$itm['id']] = $itm;
            }

            $tree = DataHelper::treeMap($tree);

            $trees[$vid] = $tree;
        } else
            $tree = $trees[$vid];


        if ($parent)
            $tree = DataHelper::treeParent($tree, $parent);
        if ($max_depth !== NULL)
            $tree = DataHelper::treeDepth($tree, $max_depth);


        return $tree;
    }

    private function toFlat($data, $parent = 0, $weight = 0) {
        $d = [];
        $t = [];

        if (!is_array($data))
            return [];

        foreach ($data as $key => $itm) {

            if (!isset($itm['children'])) {
                $weight++;
                $d[$itm['id']] = ['id' => $itm['id'], 'pid' => $parent, 'weight' => $weight];
            } else {
                $weight++;
                $d[$itm['id']] = ['id' => $itm['id'], 'pid' => $parent, 'weight' => $weight];
                $t = $this->toFlat($itm['children'], $itm['id'], $weight);
                $d += $t;
            }
            $weight++;
        }

        return $d;
    }

    public function ol($data) {

        $out = '<ol class="dd-list">';

        foreach ($data as $key => $itm) {
            $link = Html::a('&nbsp;&nbsp;&nbsp;<i class="glyphicon glyphicon-align-right pull-right"></i>', '/admin/taxonomy-items/hierarchy?TaxonomyItemsSearch[vid]=' . $itm['vid'] . '&TaxonomyItemsSearch[pid]=' . $itm['id'], [
                        'title' => Yii::t('yii', 'List'),
            ]);

            $out .= '<li class="dd-item" data-id="' . $itm['id'] . '"><div class="dd-handle dd3-handle"></div><div class="dd3-content">' . Html::encode($itm['name']) . $link . '</div>';

            if (isset($itm['children']))
                $out .= $this->ol($itm['children']);

            $out .= '</li>';
        }


        $out .= '</ol>';

        return $out;
    }

}
