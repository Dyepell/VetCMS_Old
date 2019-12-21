<?php


namespace app\models;


use yii\db\ActiveRecord;

class Expense_catalog extends ActiveRecord
{
    public static function tableName()
    {
        return 'expense_catalog';
    }
}