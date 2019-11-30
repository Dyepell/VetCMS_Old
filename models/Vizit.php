<?php


namespace app\models;
use yii\db\ActiveRecord;



class Vizit extends ActiveRecord
{
    public static function tableName()
    {
                return 'vizit';
    }

    public function getDiagnoz(){
        return $this->hasOne(Diagnoz::className(), ['ID_D' => 'ID_DIAG']);
    }

}