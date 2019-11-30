<?php

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm; ?>


<div class="container-fluid row" style="margin-top: 70px;margin-bottom: 50px;">
    <h1>Справочник товаров</h1>
    <div class="col">



        <?php $form = ActiveForm::begin(['options'=>['id'=>'tovarForm']]) ?>
        <div style="display: flex">
        <?= $form->field($model, 'ID_TOV')->textInput(['readonly'=>'readonly', 'style'=>'width:70px; margin-right:10px;'])->label('ID')?>
        <?= $form->field($model, 'NAME')->textInput(['style'=>'width:300px;'])->label('Наименование товара')?>
        <?= $form->field($model, 'PRICE')->textInput(['style'=>'margin-left:10px;width:70px;'])->label('Цена')?>
        <?= $form->field($model, 'KOL')->textInput(['style'=>'margin-left:10px;width:70px;'])->label('Изначальное количество')?>


        </div>

        <div class="row">
            <div class="col-md-2"><?= Html::submitButton('Добавить товар',['class'=>'btn btn-success'])?></div>
            <a href="index.php?r=client/report_ostatki_form" class="btn btn-primary">Сформировать отчет по остаткам</a>

        </div>

        <?php $form = ActiveForm::end();?>
        <div class="col">

            <div   style="">
                <?php

                echo GridView::widget([
                    'dataProvider'=>$KattovProvider,

                    'columns'=>[
                        ['class' => 'yii\grid\ActionColumn',
                            'template'=>'{delete}',
                            'buttons'=>[
                                'delete'=>function($model, $key, $index){

                                    $myurl='index.php?r=client/tovardelete&ID_TOV='.$key['ID_TOV'];

                                    return Html::a('<span class="glyphicon glyphicon-trash" style="margin-left: 5px;"></span>', $myurl,[
                                        'title' => Yii::t('app', 'Удалить'),
                                    ]);
                                },],

                        ],



                            ['label' => 'ID товара',
                                'attribute' => 'ID_TOV',

                            ],
                            ['label' => 'Наименование товара ',
                                'attribute' => 'NAME'

                            ],
                        ['label' => 'Цена продажи ',
                            'attribute' => 'PRICE'

                        ],
                        ['label' => 'Количество',
                            'attribute' => 'KOL'

                        ],


                        ],]);?>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS

$('.grid-view tbody tr').on('click', function()
{
    window.location = "index.php?r=client/tovar&ID_TOV="+$(this).data('key');
   });
JS;

$this->registerJs($js);?>