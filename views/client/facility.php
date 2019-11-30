<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm; ?>

<div class="row container-fluid " style="margin-top: 70px;">

    <?php $form = ActiveForm::begin(['options'=>['id'=>'facilityForm']]) ?>
    <?= $form->field($facility, 'ID_PAC')->textInput(['readonly'=>'readonly'])->label('ID')?>
    <?= $form->field($facility, 'ID_DOC')->dropDownList(
        ArrayHelper::map(\app\models\Doctor::find()->all(), 'ID_DOC', 'NAME'), [


        'prompt' => 'Не выбрано...'
    ])->label('Специалист');?>
    <?= $form->field($facility, 'ID_PR')->dropDownList(
        ArrayHelper::map(\app\models\Price::find()->orderBy("NAME ASC")->all(), 'ID_PR', 'NAME'), [


        'prompt' => 'Не выбрано...'
    ])->label('Услуга');?>
    <?= $form->field($facility, 'KOL')->textInput(['autocomplete'=>'off'])->label('Количество')?>

    <?= $form->field($facility, 'DATA')->textInput(['autocomplete'=>'off'])->label('Дата оказания')?>
    <?= $form->field($facility, 'DATASL')->textInput(['autocomplete'=>'off'])->label('Дата следующего оказания (не обязательно)')?>
    <div class="row">
        <div class="col-md-2"><?= Html::submitButton('Отправить',['class'=>'btn btn-success'])?></div>

        <?php if ($_GET['ID_PR']!=NULL):?>
            <div class="col-md-10" style="text-align: right">
                <a href="index.php?r=client/pricedelete&ID_PR=<?=$model->ID_PR?>" class="btn btn-danger" >Удалить</a>
            </div>
        <?php endif;?>
    </div>

    <?php $form = ActiveForm::end();?>
</div>
