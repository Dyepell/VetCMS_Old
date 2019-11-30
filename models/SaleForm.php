<?php


namespace app\models;


use yii\db\ActiveRecord;

class SaleForm extends ActiveRecord
{
    public  static function tableName()
    {
        return 'sale';
    }
    public  function attributeLabels()
    {
        return [
            'ID_SALE'=>'ID продажи',
            'ID_TOV'=>'Товар',
            'SOTRUDNIK'=>'Сотрудник',
            'NAME'=>'Наименование товара',
            'KOL'=>'Количество',
            'SKIDKA'=>'Скидка %',
            'VID_OPL'=>'Вид оплаты',

            'DATE'=>'Дата',
            'SUMM'=>'Сумма',


        ];
    }
    public function rules()
    {
        return [
            [["ID_SALE", "ID_TOV", "SOTRUDNIK", "NAME", "KOL", "SKIDKA", "VID_OPL", "DATE", 'SUMM'], 'safe'],



        ];
    }

}