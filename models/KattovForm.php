<?php


namespace app\models;


use yii\db\ActiveRecord;

class KattovForm extends ActiveRecord
{
    public  static function tableName()
    {
        return 'kattov';
    }
    public  function attributeLabels()
    {
        return [
            'ID_TOV'=>'ID ',
            'NAME'=>'Наименование',
            'KOL'=>'Количество',
            'PRICE'=>'Цена',



        ];
    }
    public function rules()
    {
        return [

            [["ID_TOV", "NAME", "KOL", "PRICE"], 'safe'],


        ];
    }

}