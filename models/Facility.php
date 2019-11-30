<?php


namespace app\models;
use yii\db\ActiveRecord;



class Facility extends ActiveRecord
{
    public static function tableName()
    {
        return 'Facility';
    }


}