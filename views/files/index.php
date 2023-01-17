<?php

use app\models\Files;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\BaseInflector;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;
use yii\widgets\DetailView;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var app\models\FilesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Картинки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="files-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="float-end">
        <?= Html::a('Добавить файлы', ['set'], ['class' => 'btn btn-info']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table',
        ],
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions' => ['class' => 'w-25'],
                'headerOptions' => ['class' => 'w-25 col'],
            ],
            [
                'attribute' => 'uploaded_at',
                'contentOptions' => ['class' => 'w-25'],
                'headerOptions' => ['class' => 'w-25 col'],
            ],
            [
                'attribute' => 'filename',
                'format' => 'raw',
                'contentOptions' => ['class' => 'h-25'],
                'headerOptions' => ['class' => 'col'],
                'value' => function ($model) {
                    $path = './uploads/';

                    if (!empty($model->filename) && file_exists($path . $model->filename)) {

                        return '<figure class="figure h-25">
                            <a data-toggle="lightbox" href="/web/uploads/' . $model->filename . '" target="_blank">
                           <img src="/web/uploads/'  . $model->filename . '" class="figure-img img-fluid rounded img-thumbnail" alt="' . $model->filename . '">
                            </a>
                           <figcaption class="figure-caption">' . $model->filename . '</figcaption>
                       </figure>';
                    } else {
                        return '';
                    }
                }
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Files $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>


</div>