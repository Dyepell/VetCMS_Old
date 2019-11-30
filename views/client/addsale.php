<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Kattov;



?>

<div class="container-fluid row" style="margin-top: 70px;margin-bottom: 50px;">
    <?php $form = ActiveForm::begin(['options'=>['id'=>'priceForm']]) ?>
    <?= $form->field($model, 'ID_SALE')->textInput(['readonly'=>'readonly'])->label('ID')?>
    <?= $form->field($model, 'SOTRUDNIK')->dropDownList(
        ArrayHelper::map(\app\models\Doctor::find()->all(), 'ID_DOC', 'NAME'), [


        'prompt' => 'Не выбрано...'
    ])->label('Сотрудник');?>
    <?= $form->field($model, 'ID_TOV')->dropDownList(
        ArrayHelper::map(Kattov::find()->all(), 'ID_TOV', 'NAME'), [


        'prompt' => 'Не выбрано...'
    ])->label('Товар');?>
    <?= $form->field($model, 'KOL')->textInput()->label('Количество')?>
    <?= $form->field($model, 'SKIDKA')->textInput()->label('Скидка, %')?>
    <?=$form->field($model, 'VID_OPL')->dropDownList([
        '0' => 'Наличные',
        '1' => 'Б/нал',

    ]);?>
    <?= $form->field($model, 'DATE')->textInput()->label('Дата')?>
    <?= $form->field($model, 'SUMM')->textInput(['readonly'=>'readonly'])->label('Сумма')?>


    <div class="row">
        <div class="col-md-2"><?= Html::submitButton('Отправить',['class'=>'btn btn-success'])?></div>


    </div>

    <?php $form = ActiveForm::end();?>
</div>

