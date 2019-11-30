<?php


namespace app\models;
use yii\db\ActiveRecord;


class IstbolForm extends ActiveRecord
{
    public  static function tableName()
    {
        return 'istbol';
    }
    public  function attributeLabels()
    {
        return [
            'ID_IST'=>'ID истории',
            'ID_PAC' => 'ID пациента',
            'OBSL' => 'Данные объективного обследования',

        ];
    }
    public function rules()
    {
        return [
            [['ID_IST', "ID_PAC", "OBSL"], 'safe'],


        ];
    }


}