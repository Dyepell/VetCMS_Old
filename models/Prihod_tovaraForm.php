<?php


namespace app\models;


use yii\db\ActiveRecord;

class Prihod_tovaraForm extends ActiveRecord
{
    public  static function tableName()
    {
        return 'prihod_tovara';
    }
    public  function attributeLabels()
    {
        return [
            'ID_PRIHOD'=>'ID ',
            'ID_TOV'=>'Товар',
            'PRICE'=>'Цена',
            'SUMM'=>'Суммы',
            'DATE'=>'Дата',
            'KOL'=>'Количество',
            'SELL_PRICE'=>'Цена продажи'


        ];
    }
    public function rules()
    {
        return [
            [["ID_PRIHOD"], 'safe'],
            [["ID_TOV", "KOL", "SUMM", "DATE", 'PRICE', 'SELL_PRICE'], 'safe'],


        ];
    }

}