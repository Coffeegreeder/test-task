<?php

namespace app\controllers;

use Yii;
use yii\helpers\VarDumper;
use app\models\Patient;
use app\models\FilesSearch;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\web\Request;

class ApiController extends ActiveController
{
    public $modelClass = 'app\models\Patient';

    /**
     * @param $action
     * @return bool
     * @throws \yii\base\ExitException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = true;

        return parent::beforeAction($action);
    }

    protected function verbs()
    {
        return [
            'get-total-count' => ['GET'],
            'get-by-page' => ['GET'],
            'get-by-id' => ['GET'],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => \yii\filters\ContentNegotiator::className(),
                'formats' => [
                    'application/json' => \yii\web\Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Lists all Files models.
     * @return mixed
     */

    // запрос c параметром id который вернет данные картинки по id
    public function actionGetData()
    {
        $params = Yii::$app->request->get();
        $searchModel = new FilesSearch();
        $dataProvider = $searchModel->search($params, true);

        switch ($params) {

            case (!empty($params['id'])):

                $data = $dataProvider->getModels()[0];

                $json = [
                    'id' => $data->id,
                    'path' => $data->filename
                ];

                break;

            case (!empty($params['page'])):

                $json = [
                    'page' => $params['page'],
                    'list' => $dataProvider
                ];

                break;
            default:

                $json = ['total' => $dataProvider->getTotalCount()];

                break;
        }

        return $json;
    }
}
