<?php


namespace app\models;
use yii\db\ActiveRecord;


class FacilityForm extends ActiveRecord
{
    public  static function tableName()
    {
        return 'Facility';
    }
    public  function attributeLabels()
    {
        return [
            'ID_PAC'=>'ID пациента',
            'ID_DOC' => 'Специалист',
            'ID_PR' => 'Услуга',
            'KOL' => 'Количество',
            'DATA' => 'Дата оказания',
            'DATASL'=> 'Дата следующего оказания (не обязательно)'

        ];
    }
    public function rules()
    {
        return [
            [['ID_PAC', "ID_DOC", "ID_PR", "KOL", "DATA"], 'required'],
            ["DATASL", 'safe'],


        ];
    }


}