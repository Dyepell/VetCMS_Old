<?php

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm; ?>

<div class="row container-fluid " style="margin-top: 70px;">

<!--    --><?php //$form = ActiveForm::begin(['options'=>['id'=>'facilityForm']]) ?>
<!--    --><?//= $form->field($facility, 'ID_PAC')->textInput(['readonly'=>'readonly'])->label('ID')?>
<!--    --><?//= $form->field($facility, 'ID_DOC')->dropDownList(
//        ArrayHelper::map(\app\models\Doctor::find()->all(), 'ID_DOC', 'NAME'), [
//
//
//        'prompt' => 'Не выбрано...'
//    ])->label('Специалист');?>
<!--    --><?//= $form->field($facility, 'ID_PR')->dropDownList(
//        ArrayHelper::map(\app\models\Price::find()->orderBy("NAME ASC")->all(), 'ID_PR', 'NAME'), [
//
//
//        'prompt' => 'Не выбрано...'
//    ])->label('Услуга');?>
<!--    --><?//= $form->field($facility, 'KOL')->textInput(['autocomplete'=>'off'])->label('Количество')?>
<!---->
<!--    --><?//= $form->field($facility, 'DATA')->textInput(['autocomplete'=>'off'])->label('Дата оказания')?>
<!--    --><?//= $form->field($facility, 'DATASL')->textInput(['autocomplete'=>'off'])->label('Дата следующего оказания (не обязательно)')?>
<!--    <div class="row">-->
<!--        <div class="col-md-2">--><?//= Html::submitButton('Сохранить',['class'=>'btn btn-success'])?><!--</div>-->
<!---->
<!--        --><?php //if ($_GET['ID_PR']!=NULL):?>
<!--            <div class="col-md-10" style="text-align: right">-->
<!--                <a href="index.php?r=client/pricedelete&ID_PR=--><?//=$model->ID_PR?><!--" class="btn btn-danger" >Удалить</a>-->
<!--            </div>-->
<!--        --><?php //endif;?>
<!--    </div>-->
<!--    --><?php //$form = ActiveForm::end();?>
    <?= Html::beginForm(['client/new_facility', 'id' => 'otchet', 'name'=>'form1'], 'GET'); ?>
    <div class="row">
        <div class="col-md-2"><a href="index.php?r=client/visit&ID_VISIT=<?=$_GET['ID_VISIT']?>" class="btn btn-primary">Перейти к визиту</a></div>
        <div class="col-md-3"><?= Html::textInput('ID_VISIT',$_GET['ID_VISIT'],['class' => 'form-control','style'=>'width:200px' ]); ?> </div>
        <div class="col-md-4"> <?=Html::dropDownList('doctor', 'null', $doc, ['class'=>'form-control']);?></div>
        <div class="col-md-2"> <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success', 'id'=>'btn1', 'style'=>'width:100%;']) ?></div>
    </div>





    <?php
    echo GridView::widget([
                    'dataProvider'=>$dataProvider,
                    'filterModel'=>$searchModel,

                    'id'=>'price',

                    'columns'=>

                        [
                            ['label' => 'Количество',
                                'attribute' => 'ID_PR',
                                'filter'=>false,
                                'format'=>'raw',
                                'value'=>function($key){
                                    return  Html::textInput($key->ID_PR,'',['class' => 'form-control','style'=>'width:200px' ]);
                                }

                            ],
                            ['label' => 'Цена',
                                'attribute' => 'PRICE',

                            ],
                            ['label' => 'Вид',
                                'attribute' => 'ID_SPDOC',
                                'filter'=>[
                                        1=>'Терапия',
                                        2=>'Хирургия',
                                        3=>'УЗИ',
                                        4=>'Медикаменты',
                                        5=>'Вакцинация',
                                        6=>'Дегельминтизация',
                                        7=>'Анализы',
                                        8=>'Корм',
                                ],
                                'value'=>function($key){
                                    $spdoc=\app\models\Spdoc::findOne(['ID_SPDOC'=>$key->ID_SPDOC]);
                                    return $spdoc->SP_DOC;
                                }


                            ],
                            ['label' => 'Наименование',
                                'attribute' => 'NAME',

                            ],





                        ]




                ])

    ?>

    <?php Html::endForm(); ?>


</div>
