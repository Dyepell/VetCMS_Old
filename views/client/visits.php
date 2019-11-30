<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
use yii\grid\GridView;
?>
<div class="row container-fluid " style="margin-top: 70px;">

    <span style="color: cornflowerblue;font-size: 200%;"><a href="index.php?r=client/anketa&clientId=<?=$pacient->ID_CL?>"><?=$pacient->KLICHKA?> </a></span>
    <span style="font-size: 130%;">(<?=$client->FAM.' '.$client->NAME.' '.$client->OTCH?>)</span>

    <?php echo GridView::widget([
            'dataProvider'=>$dataProvider,
            'id'=>'1',
            'columns'=>[
                ['class' => 'yii\grid\ActionColumn',
                    'template'=>'{view} ',
                    'buttons'=>['view'=>function($model, $key, $index){
                        $myurl='index.php?r=client/visit&ID_PAC='.$key['ID_PAC'].'&ID_VISIT='.$key['ID_VISIT'];
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $myurl,[
                            'title' => Yii::t('app', 'Просмотр'),
                        ]);
                    },

                    ],

                ],
                ['class' => 'yii\grid\SerialColumn'],
                ['label' => 'ID визита',
                    'attribute' => 'ID_VISIT',

                ],
                ['label' => 'Дата визита',
                    'attribute' => 'DATE',
                    'value'=>function($key){
                        if($key->DATE==NULL){
                            $date=$key->DATA;
                        }else{
                            $date=date('d.m.Y', strtotime($key->DATE));
                        }
                    return $date;
                    }

                ],
                ['label' => 'ID пациента',
                    'attribute' => 'ID_PAC',

                ],
                ['label' => 'Сумма визита',
                    'attribute' => 'SUMMAV',

                ],
                ['label' => 'Долг',
                    'attribute' => 'DOLG',

                ],
                ['label' => 'Дата погашения',
                    'attribute' => 'DATA_OPL',

                ],
                ['label' => 'Остаток долга',
                    'attribute' => 'PROZSKID',

                ],
                ['label' => 'Анаинез и лечение',
                    'attribute' => 'PRIMECH',

                ],
                ['label' => 'Диагноз',
                    'attribute' => 'ID_DIAG',
                    'value'=>function($key){
                        $diagnoz=\app\models\Diagnoz::findOne(['ID_D'=>$key->ID_DIAG]);
                        $diagnoz=$diagnoz->DIAGNOZ;
                    return $diagnoz;
                    }


                ]
            ],]);?>
</div>
<div class="row container-fluid p-0">
<div class="col-md-6">
    <span style="font-size: 200%;">Предстоящие услуги</span>

    <?php echo GridView::widget([
        'dataProvider'=>$vakcineProvider,
        'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
        'columns'=>[
            ['class' => 'yii\grid\SerialColumn'],
            ['label' => 'ID пациента',
                'attribute' => 'ID_PAC'

            ],
            ['label' => 'Дата оказания',
                'attribute' => 'DATA',
                'value'=>function($key){

                    return date('d.m.Y', strtotime($key->DATA));
                }

            ],
            ['label' => 'Дата следующего оказания',
                'attribute' => 'DATASL',
                'value'=>function($key){

                    return date('d.m.Y', strtotime($key->DATASL));
                }

            ],
            ['label' => 'Наименование',
                'attribute' => 'ID_PR',
                'value'=>function($key){
                    $procedure=\app\models\Price::findOne(['ID_PR'=>$key->ID_PR]);
                    return $procedure->NAME;
                }

            ],

        ],
        ]);?>
</div>
    <div class="col-md-5" style="margin-top:20px;">
        <a href="index.php?r=client/visit&ID_PAC=<?=$pacient->ID_PAC?>" class="btn btn-success">Новый визит</a>
    </div>
</div>

<?php
$js = <<<JS

$('#1 tbody tr').on('click', function()
{
    window.location = "index.php?r=client/visit&ID_VISIT="+$(this).data('key');
   });
JS;

$this->registerJs($js);?>
