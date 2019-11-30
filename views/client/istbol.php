<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;use yii\widgets\ActiveForm; ?>

<div class="container-fluid row" style="margin-top: 70px;margin-bottom: 50px;">
    <span style="font-size: 150%">История болезни: <a href="index.php?r=client/anketa&clientId=<?=$pacient->ID_CL?>" class="clientLink"><?=$pacient->KLICHKA?></a></span>
    <?php $form = ActiveForm::begin(['options'=>['id'=>'priceForm']]) ?>
    <?= $form->field($istbol, 'ID_IST')->textInput(['readonly'=>'readonly'])->label('ID истории')?>
    <?= $form->field($istbol, 'ID_PAC')->textInput(['readonly'=>'readonly', 'value'=>$pacient->ID_PAC])->label('ID пациента')?>
    <?= $form->field($istbol, 'DIST')->textInput()->label('Дата')?>
    <?= $form->field($istbol, 'OBSL')->textarea(['rows'=>20])->label('Данные объективного исследования')?>


    <div class="row">
        <div class="col-md-2"><?= Html::submitButton('Отправить',['class'=>'btn btn-success'])?></div>

        <?php if ($_GET['ID_IST']!=NULL):?>
            <div class="col-md-10" style="text-align: right">
                <a href="index.php?r=client/istboldelete&ID_IST=<?=$istbol->ID_IST?>" class="btn btn-danger" >Удалить</a>
            </div>
        <?php endif;?>
    </div>

    <?php $form = ActiveForm::end();?>
</div>
