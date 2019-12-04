<?php

use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm; ?>




<div class="modal fade" id="istbol" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Истории болезни</h4>
            </div>
            <div class="modal-body">
                <?php
                if ($_GET['ID_VISIT']!=null){echo GridView::widget([
                    'dataProvider'=>$istbolProvider,
                    'id'=>'istbol',

                    'columns'=>

                        [
                            ['label' => 'ID',
                                'attribute' => 'ID_IST',

                            ],
                            ['label' => 'ID пациента',
                                'attribute' => 'ID_PAC',

                            ],
                            ['label' => 'Дата',
                                'attribute' => 'DIST',

                            ],




                        ],

                    'rowOptions' => function ($model, $key, $index, $grid) {

                        return ['id' => $model['ID_IST'], 'onclick' => 'window.location = "index.php?r=client/istbol&ID_IST="+this.id'];

                    },


                ]);}?>
                <a href="index.php?r=client/istbol&ID_PAC=<?=$visit->ID_PAC?>" class="btn btn-success" style="width: 100%;">Добавить историю</a>
            </div>
            <div class="modal-footer">
                <!--                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="docs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Приложения</h4>
            </div>
            <div class="modal-body" style="text-align: center">

                <a href="index.php?r=client/docagree&ID_VISIT=<?=$visit->ID_VISIT?>" class="btn btn-success" style="margin-bottom: 10px;">Соглашение</a>
                <a href="index.php?r=client/docdolg&ID_VISIT=<?=$visit->ID_VISIT?>" class="btn btn-success">Договор возмездного оказания</a>
            </div>
            <div class="modal-footer">
                <!--                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->

            </div>
        </div>
    </div>
</div>


<div class="row container-fluid " style="margin-top: 70px;">
    <h1>Визит <a href="index.php?r=client/anketa&clientId=<?=$pacient->ID_CL?>"><?=$pacient->KLICHKA?></a></h1>
    <div class="col-md-6 p-0">
    <?php $form = ActiveForm::begin(['options'=>['id'=>'visitForm']]) ?>
        <div style="display: flex">
    <?= $form->field($visit, 'ID_VISIT')->textInput(['readonly'=>'readonly'])->label('ID визита')?>
    <?= $form->field($visit, 'ID_PAC')->textInput(['readonly'=>'readonly', 'style'=>'margin-left:5px;'])->label('ID пациента')?>
    <?php

    if ($visit->DATE!=NULL){
       echo  $form->field($visit, 'DATE')->textInput(['style'=>'margin-left:10px;'])->label('Дата визита');
    }else{
        echo $form->field($visit, 'DATA')->textInput(['style'=>'margin-left:10px;'])->label('Дата визита');
    }
    ?>


        </div>

        <?= $form->field($visit, 'ID_DIAG')->dropDownList(
            ArrayHelper::map(\app\models\Diagnoz::find()->all(), 'ID_D', 'DIAGNOZ'), [


            'prompt' => 'Не выбрано...'
        ])->label('Диагноз основного заболевания');?>
    <div class="row">
        <div class="col" style="margin-left: 20px;">
           <?php  if ($_GET['ID_VISIT']!=null)
           {echo "<span style=\"font-size: 150%;\">Список услуг</span>";}?>
            <?php
            if ($_GET['ID_VISIT']!=null){echo GridView::widget([
                'dataProvider'=>$FacilityProvider,

                'columns'=>

                    [
                        ['class' => 'yii\grid\ActionColumn',
                            'template'=>' {delete}',
                            'buttons'=>[
                                'delete'=>function($model, $key, $index){

                                    $myurl='index.php?r=client/facilitydelete&ID_FAC='.$key['ID_FAC'].'&ID_VISIT='.$key['ID_VISIT'];

                                    return Html::a('<span class="glyphicon glyphicon-trash" style="margin-left: 5px;"  onclick=\'return confirm("Вы уверены?")\'></span>', $myurl,[
                                        'title' => Yii::t('app', 'Удалить'),
                                    ]);
                                },],

                        ],

                        ['label' => 'Специалист',
                            'attribute' => 'ID_DOC',
                            'value'=>function($key){
                                $doc=\app\models\Doctor::findOne(['ID_DOC'=>$key->ID_DOC]);
                                $doc=$doc->NAME;
                                return $doc;
                            }

                        ],
                        ['label' => 'Услуга',
                            'attribute' => 'ID_PR',
                            'value'=>function($key){
                                $pr=\app\models\Price::findOne(['ID_PR'=>$key->ID_PR]);
                                $pr=$pr->NAME;
                                return $pr;
                            }
                        ],
                        ['label' => 'Цена',
                            'attribute' => 'PRICE',

                        ],
                        ['label' => 'Количество',
                            'attribute' => 'KOL',

                        ],
                        ['label' => 'Сумма',
                            'attribute' => 'SUMMA',


                        ],



                    ],


                ]);}?>
            <?php
            if ($_GET['ID_VISIT']!=NULL):
            ?>
            <div class="row">
                <div class="col">
                <span style="font-size: 200%; color: darkred;">Итого: <?=$visit->SUMMAV?> руб.</span>
                </div>
                <div class="col-md-5">
                    <?php if($visit->DOLG!=0):?>

                        <span style="font-size: 200%; color: darkred;">Долг: <?=$visit->DOLG?> руб.</span>
                    <?php endif;?>
            <?php endif;?>
                </div>
            </div>
        </div>
        <?php if($_GET['ID_VISIT']!=NULL):?>
        <div class="row" style="margin-left:10px;">

            <div class="col">
        <a href="index.php?r=client/facility&ID_VISIT=<?=$visit->ID_VISIT?>" class="btn btn-warning">Добавить услуги</a>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#docs">
            Приложения
        </button>


            </div>
        </div>
        <?php endif;?>
    </div>



    </div>

    <div class="col-md-5" style="margin-left: 20px;">
        <?php if($_GET['ID_VISIT']!=NULL):?>
        <?= $form->field($visit, 'PRIMECH')->textarea(['rows'=>5])->label('Анаинез и лечение')?>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#istbol">
            История болезни
        </button>
        <a href="index.php?r=client/analysis&ID_PAC=<?=$visit->ID_PAC?>" class="btn btn-primary">Исследования</a>

        <div style="display: flex">

        <?= $form->field($visit, 'DATE_OPL')->textInput(['readonly'=>'readonly','autocomplete'=>'0'])->label('Дата оплаты')?>
        <?= $form->field($visit, 'SUMMAO')->textInput(['style'=>'margin-left:10px;width:70px;'])->label('Оплата', ['style'=>'margin-left:10px; '])?>
        <?=$form->field($visit, 'VIDOPL')->dropDownList([
            '0' => 'Наличные',
            '1' => 'Б/нал',

        ]);?>
        </div>
        <?php endif;?>
        <div class="row">
            <div class="col-md-2">

        <?= Html::submitButton('Сохранить',['class'=>'btn btn-success'])?>
            </div>
        <?php if ($_GET['ID_VISIT']!=NULL):?>
                    <div class="col-md-10" style="text-align: right">
                     <a href="index.php?r=client/visitdelete&ID_VISIT=<?=$visit->ID_VISIT?>" class="btn btn-danger"
                        onclick='return confirm("Вы уверены?")' >Удалить</a>
                    </div>
        <?php endif;?>
        </div>
        <?php $form = ActiveForm::end();?>
    </div>



</div>
<?php
$js = <<<JS

$('.grid-view tbody tr').on('click', function()
{
    console.log($(this).data());
    // window.location = "index.php?r=client/istbol&ID_IST="+$(this).data('ID_IST');
   });

JS;

$this->registerJs($js);?>
