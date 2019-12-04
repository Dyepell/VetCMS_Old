<?php


namespace app\controllers;
use app\models\Client;
use app\models\Doctor;
use app\models\Facility;
use app\models\Kattov;
use app\models\Oplata;
use app\models\OplataForm;
use app\models\Pacient;
use app\models\Price;
use app\models\Sale;
use app\models\Vid;
use app\models\Vizit;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii;
use yii\helpers\StringHelper;
class Doc {
    public $name;
    public $pacients=[];
    public $summ;
    public $chek;
}
class Dolg{
    public $dolgDate;
    public $pacient;
    public $fio;
    public $summ;

}
class NewPacient{
    public  $fio;
    public $name;
    public $vid;
    public $date;
}
class Predusl{
    public $firstdate;
    public $seconddate;
    public $usluga;
    public $fio;
    public $pacient;
    public $numbers;
}
class DoctorPay{
    public $id;
    public $name;
    public $thProcent;
    public $suProcent;
    public $uzProcent;
    public $vakProcent;
    public $medProcent;
    public $degProcent;
    public $anProcent;
    public $kormProcent;

    public $thSumm;
    public $suSumm;
    public $uzSumm;
    public $vakSumm;
    public $medSumm;
    public $degSumm;
    public $anSumm;
    public $kormSumm;
}


class ReportsController extends AppController
{
    public $layout='basic';

    public function beforeAction($action)
    {
        if ($action->id=='index'){
            $this->enableCsrfValidation=false;
        }
        return parent::beforeAction($action);
    }

    public function actionOtchet_uslugi(){



        $date=date("d.m.Y");
        return $this->render('otchet_uslugi', compact('date'));
    }

    public function actionOtchet_uslugi_form(){

        Yii::setAlias('@reports', Yii::$app->basePath.'/отчеты');
        $firstdate= ($_GET['FIRST_DATE_S']);
        $secondtdate= ($_GET['SECOND_DATE_S']);

        $firstdate =date("Y-m-d", strtotime($firstdate));
        $secondtdate =date("Y-m-d", strtotime($secondtdate));

        if($_GET["vid"]==0){
            //-------------------------Отчет по пациентам--------------------------------------
            $facility=Facility::find()->where(['between', 'DATA', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->orderBy([
                'ID_CL' => SORT_ASC,
                'DATA' => SORT_ASC
            ])->all();

            $spreadsheet= new Spreadsheet();
            $sheet=$spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'ЗооДоктор');
            $sheet->setCellValue('D1', date("d.m.Y"));


            $sheet->setCellValue('A2', 'Отчет об услугах по пациентам');


            $sheet->setCellValue('A3', ' с ' . date("d.m.Y", strtotime($firstdate)) . ' по ' . date("d.m.Y", strtotime($secondtdate)));
            $sheet->mergeCells('A2:F2');
            $titles=[
                [
                    'name'=>'Дата',
                    'cell'=>'A',
                ],
                [
                    'name'=>'Клиент',
                    'cell'=>'B',
                ],
                [
                    'name'=>'Пациент',
                    'cell'=>'C',
                ],
                [
                    'name'=>'Процедура',
                    'cell'=>'D',
                ],
                [
                    'name'=>'Цена',
                    'cell'=>'E',
                ],
                [
                    'name'=>'Кол.',
                    'cell'=>'F',
                ],
                [
                    'name'=>'Нал.',
                    'cell'=>'G',
                ],
                [
                    'name'=>'Б/Нал.',
                    'cell'=>'H',
                ],
                [
                    'name'=>'Выручка',
                    'cell'=>'I',
                ],
                [
                    'name'=>'Долг',
                    'cell'=>'J',
                ],
                [
                    'name'=>'Специалист',
                    'cell'=>'K',
                ],

            ];
            for ($j = 0; $j < count($titles); $j++) {
                $string = $titles[$j]['name'];
                $cellLatter = $titles[$j]['cell'] . 4;
                $sheet->setCellValue($cellLatter, $string);
            }
            $activeRow=4;
            $clientSumm=0;
            foreach ($facility as $fac){
                $activeRow=$activeRow+1;
                $client=Client::findOne(['ID_CL'=>$fac->ID_CL]);
                $n = StringHelper::truncate($client->NAME, 1, '');
                $o = StringHelper::truncate($client->OTCH, 1, '');
                 if($lastClient->ID_CL!=$client->ID_CL&&$lastClient!=NULL){
                     $n = StringHelper::truncate($lastClient->NAME, 1, '');
                     $o = StringHelper::truncate($lastClient->OTCH, 1, '');
                     $cellA = 'A' . $activeRow;
                     $cellH = 'H' . $activeRow;
                     $cellI = 'I' . $activeRow;
                     $cellJ = 'J' . $activeRow;
                     $cellF = 'F' . $activeRow;
                     $cellG = 'G' . $activeRow;
                     $cellH = 'H' . $activeRow;


                     $stringA='Итого: '.$lastClient->FAM . ' ' . $n .'.'. $o;
                     $stringI=$clientSumm;

                     $dolg=Vizit::find()->where(['>', 'DOLG', 0])->andWhere(['ID_CL'=>$lastClient->ID_CL])->all();
                     $nal=Oplata::find()->where(['between', 'DATE', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->where(['ID_CL'=>$lastClient->ID_CL])->andWhere(['VID_OPL'=>0])->all();
                     $bnal=Oplata::find()->where(['between', 'DATE', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->where(['ID_CL'=>$lastClient->ID_CL])->andWhere(['VID_OPL'=>1])->all();

                     $summDolg=0;
                     $summNal=0;
                     $summBnal=0;

                     foreach ($dolg as $d){
                         $summDolg=$summDolg+$d->DOLG;
                     }
                     foreach ($nal as $nalich){
                         $summNal=$summNal+$nalich->SUMM;
                     }
                     foreach ($bnal as $bnalich){
                         $summBnal=$summBnal+$bnalich->SUMM;
                     }





                     $sheet->setCellValue($cellA, $stringA);

                     $sheet->getStyle($cellA, $stringA)->getFont()->applyFromArray(['bold' => TRUE]);
                     $sheet->getStyle($cellG, $stringG)->getFont()->applyFromArray(['bold' => TRUE]);
                     $sheet->getStyle($cellH, $stringH)->getFont()->applyFromArray(['bold' => TRUE]);
                     $sheet->getStyle($cellI, $stringI)->getFont()->applyFromArray(['bold' => TRUE]);
                     $sheet->getStyle($cellJ, $stringJ)->getFont()->applyFromArray(['bold' => TRUE]);


                     $sheet->setCellValue($cellI, $stringI);
                     $sheet->setCellValue($cellJ, $summDolg);
                     $sheet->setCellValue($cellG, $summNal);
                     $sheet->setCellValue($cellH, $summBnal);
                     $sheet->mergeCells($cellA.':'.$cellF);
                     $lastSumm=$activeRow;

                     $activeRow=$activeRow+1;
                 }

                     $cellA = 'A' . $activeRow;
                     $cellB = 'B' . $activeRow;
                     $cellC = 'C' . $activeRow;
                     $cellD = 'D' . $activeRow;
                     $cellE = 'E' . $activeRow;
                     $cellF = 'F' . $activeRow;
                     $cellG = 'G' . $activeRow;
                     $cellI = 'I' . $activeRow;
                     $cellK = 'K' . $activeRow;


                     $procedure = Price::findOne(['ID_PR' => $fac->ID_PR]);
                     $pacient = Pacient::findOne(['ID_PAC' => $fac->ID_PAC]);
                     $doctor = Doctor::findOne(['ID_DOC' => $fac->ID_DOC]);


                     $stringA = date( 'd.m.Y',strtotime($fac->DATA));
                     $stringB = $client->FAM . ' ' . $n . '.' . $o;
                     $stringC = $pacient->KLICHKA;
                     $stringD = $procedure->NAME;
                     $stringE = $procedure->PRICE;
                     $stringF = $fac->KOL;
                     $stringI = $stringE * $stringF;
                     $clientSumm=$clientSumm+$stringI;
                     $stringK = $doctor->NAME;


                     $sheet->setCellValue($cellA, $stringA);
                     $sheet->setCellValue($cellB, $stringB);
                     $sheet->setCellValue($cellC, $stringC);
                     $sheet->setCellValue($cellD, $stringD);
                     $sheet->setCellValue($cellE, $stringE);
                     $sheet->setCellValue($cellF, $stringF);
                     $sheet->setCellValue($cellI, $stringI);
                     $sheet->setCellValue($cellK, $stringK);
                     $lastClient = $client;



            }


            $summDolg=0;
            $summNal=0;
            $summBnal=0;


            $dolg=Vizit::find()->where(['between', 'DATE', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->andWhere(['>', 'DOLG', 0])->andWhere(['ID_CL'=>$lastClient->ID_CL])->all();
            $nal=Oplata::find()->where(['between', 'DATE', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->andWhere(['ID_CL'=>$lastClient->ID_CL])->andWhere(['VID_OPL'=>0])->all();
            $bnal=Oplata::find()->where(['between', 'DATE', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->andWhere(['ID_CL'=>$lastClient->ID_CL])->andWhere(['VID_OPL'=>1])->all();



            foreach ($dolg as $d){
                $summDolg=$summDolg+$d->DOLG;
            }
            foreach ($nal as $nalich){
                $summNal=$summNal+$nalich->SUMM;
            }
            foreach ($bnal as $bnalich){
                $summBnal=$summBnal+$bnalich->SUMM;
            }
            $n = StringHelper::truncate($lastClient->NAME, 1, '');
            $o = StringHelper::truncate($lastClient->OTCH, 1, '');
            $activeRow=$activeRow+1;
            $cellA='A'.$activeRow;
            $cellJ='J'.$activeRow;
            $cellI='I'.$activeRow;
            $cellF='F'.$activeRow;
            $cellG='G'.$activeRow;
            $cellH='H'.$activeRow;
            $cellK='K'.$activeRow;

            $stringA=$lastClient->FAM.' '.$n. '.' .$o;
            $stringJ=$summDolg;
            $stringG=$summNal;
            $stringI=$clientSumm;
            $stringH=$summBnal;

            $sheet->setCellValue($cellA,$stringA);
            $sheet->setCellValue($cellJ,$stringJ);
            $sheet->setCellValue($cellG,$stringG);
            $sheet->setCellValue($cellI,$stringI);
            $sheet->setCellValue($cellH,$stringH);

            $sheet->getStyle($cellA, $stringA)->getFont()->applyFromArray(['bold' => TRUE]);
            $sheet->getStyle($cellG, $stringG)->getFont()->applyFromArray(['bold' => TRUE]);
            $sheet->getStyle($cellH, $stringH)->getFont()->applyFromArray(['bold' => TRUE]);
            $sheet->getStyle($cellI, $stringI)->getFont()->applyFromArray(['bold' => TRUE]);
            $sheet->getStyle($cellJ, $stringJ)->getFont()->applyFromArray(['bold' => TRUE]);
            $sheet->mergeCells($cellA.':'.$cellF);
            $sheet->getStyle('A4:' . $cellK)
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);


            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(19);
            $sheet->getColumnDimension('C')->setWidth(12);
            $sheet->getColumnDimension('D')->setWidth(35);
            $sheet->getColumnDimension('E')->setWidth(9);
            $sheet->getColumnDimension('F')->setWidth(9);
            $sheet->getColumnDimension('G')->setWidth(9);
            $sheet->getColumnDimension('H')->setWidth(9);
            $sheet->getColumnDimension('I')->setWidth(11);
            $sheet->getColumnDimension('J')->setWidth(9);
            $sheet->getColumnDimension('K')->setWidth(35);








                $filename='/Отчет об услугах по пациентам c '.date("d.m.Y", strtotime($firstdate)).' по '.date("d.m.Y", strtotime($secondtdate)).'.xlsx';


            $writer = new Xlsx($spreadsheet);
            $writer->save(Yii::getAlias('@reports').$filename);
            $path = \Yii::getAlias('@reports') ;

            $file = $path.$filename;
            if (file_exists($file)) {
                \Yii::$app->response->sendFile($file);
            }else {
                throw new \Exception('Файл не найден');
            }

        }









        if($_GET["vid"]==1){

            //-------------------------Отчет по специалистам--------------------------------------
            $doctor=Doctor::find()->where(['STATUS_R'=>1])->all();

            $spreadsheet = new Spreadsheet();
            $titles = array(
                array(
                    'name' => 'Дата',
                    'cell' => 'A',
                ),
                array(
                    'name' => 'Фио клиента',
                    'cell' => 'B',
                ),
                array(
                    'name' => 'Пациент',
                    'cell' => 'C',
                ),
                array(
                    'name' => 'Процедура',
                    'cell' => 'D',
                ),
                array(
                    'name' => 'Цена',
                    'cell' => 'E',
                ),
                array(
                    'name' => 'Кол-во',
                    'cell' => 'F',
                ),
                array(
                    'name' => 'Сумма',
                    'cell' => 'G',
                ),

            );

            for($i=0;$i < count($doctor);$i++){





                $page = $spreadsheet->setActiveSheetIndex($i);

                $page->setTitle($doctor[$i]->NAME);



                $facility = Facility::find()->where(['between', 'DATA', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->andWhere(['ID_DOC' => $doctor[$i]->ID_DOC])->all();



                $spreadsheet->getActiveSheet()->setCellValue('A1', 'ЗооДоктор');
                $spreadsheet->getActiveSheet()->setCellValue('D1', date("d.m.Y"));

                $spreadsheet->getActiveSheet()->setCellValue('A2', 'Отчет об услугах по специалисту: ' . $doctor[$i]->NAME);
                $spreadsheet->getActiveSheet()->setCellValue('A3', ' с ' . date("d.m.Y", strtotime($firstdate)) . ' по ' . date("d.m.Y", strtotime($secondtdate)));
                $spreadsheet->getActiveSheet()->mergeCells('A2:F2');
                for ($j = 0; $j < count($titles); $j++) {
                    $string = $titles[$j]['name'];
                    $cellLatter = $titles[$j]['cell'] . 4;
                    $spreadsheet->getActiveSheet()->setCellValue($cellLatter, $string);
                }

                $totalSumm = 0;
                $count = count($facility);
                for ($y = 0; $y< $count; $y++) {

                    $pacient = Pacient::findOne(['ID_PAC' => $facility[$y]->ID_PAC]);

                    $client = Client::findOne(['ID_CL' => $pacient->ID_CL]);
                    $procedure = Price::findOne(['ID_PR' => $facility[$y]->ID_PR]);


                    $c = $y + 5;
                    $cellA = 'A' . $c;
                    $cellB = 'B' . $c;
                    $cellC = 'C' . $c;
                    $cellD = 'D' . $c;
                    $cellE = 'E' . $c;
                    $cellF = 'F' . $c;
                    $cellG = 'G' . $c;


                    $n = StringHelper::truncate($client->NAME, 1, '');
                    $o = StringHelper::truncate($client->OTCH, 1, '');

                    $stringA = $facility[$y]->DATA;
                    $stringA = date('d.m.Y', strtotime($stringA));

                    $stringB = $client->FAM . ' ' . $n . '. ' . $o . '.';
                    $stringC = $pacient->KLICHKA;

                    $stringD = $procedure->NAME;

                    $stringE = $procedure->PRICE;
                    $stringF = $facility[$y]->KOL;
                    $stringG = $stringE * $stringF;

                    $spreadsheet->getActiveSheet()->setCellValue($cellA, $stringA);
                    $spreadsheet->getActiveSheet()->setCellValue($cellB, $stringB);
                    $spreadsheet->getActiveSheet()->setCellValue($cellC, $stringC);

                    $spreadsheet->getActiveSheet()->setCellValue($cellD, $stringD);
                    $spreadsheet->getActiveSheet()->setCellValue($cellE, $stringE);
                    $spreadsheet->getActiveSheet()->setCellValue($cellF, $stringF);

                    $spreadsheet->getActiveSheet()->setCellValue($cellG, $stringG);
                    $totalSumm = $totalSumm + $stringG;

                    $spreadsheet->getActiveSheet()->getStyle('C')->getAlignment()->setWrapText(true);
                    $spreadsheet->getActiveSheet()->getStyle('D')->getAlignment()->setWrapText(true);


                }
                $eCount = $count + 5;
                $e = 'E' . $eCount;
                $f = 'F' . $eCount;
                $a = 'A' . $eCount;
                $g = 'G' . $eCount;


                $spreadsheet->getActiveSheet()->mergeCells($a . ':' . $e);

                $spreadsheet->getActiveSheet()->getStyle('A4:' . $g)
                    ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(19);
                $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12);
                $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(19);
                $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(7);
                $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(7);
                $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(9);


                $spreadsheet->getActiveSheet()->setCellValue($f, 'Итого:');
                $spreadsheet->getActiveSheet()->setCellValue($g, $totalSumm);

                $spreadsheet->createSheet();



            }


            $filename='/Отчет об услугах по специалистам c '.date("d.m.Y", strtotime($firstdate)).' по '.date("d.m.Y", strtotime($secondtdate)).'.xlsx';
            $writer = new Xlsx($spreadsheet);
            $writer->save(Yii::getAlias('@reports').$filename);
            $path = \Yii::getAlias('@reports') ;

            $file = $path.$filename;


            if (file_exists($file)) {
                \Yii::$app->response->sendFile($file);
            }else {
                throw new \Exception('Файл не найден');
            }
        }

        if($_GET["vid"]==2){
            //-----------------------------------------Отчет по пациентам сокращенный ----------------------------------------------------------






            $spreadsheet= new Spreadsheet();
            $sheet=$spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'ЗооДоктор');
            $sheet->setCellValue('D1', date("d.m.Y"));


            $sheet->setCellValue('A2', 'Отчет об услугах по пациентам (сокращенный)');


            $sheet->setCellValue('A3', ' с ' . date("d.m.Y", strtotime($firstdate)) . ' по ' . date("d.m.Y", strtotime($secondtdate)));
            $sheet->mergeCells('A2:F2');

            $titles = array(
                array(
                    'name' => 'Клиент',
                    'cell' => 'A',
                ),
                array(
                    'name' => 'Сумма',
                    'cell' => 'B',
                ),
                array(
                    'name' => 'Наличные',
                    'cell' => 'C',
                ),
                array(
                    'name' => 'Б/нал',
                    'cell' => 'D',
                ),
                array(
                    'name' => 'Долг',
                    'cell' => 'E',
                ),


            );

            for ($j = 0; $j < count($titles); $j++) {
                $string = $titles[$j]['name'];
                $cellLatter = $titles[$j]['cell'] . 4;
                $sheet->setCellValue($cellLatter, $string);
            }
            $totalSumm=0;
            $totaltDolg=0;
            $totaltNal=0;
            $totaltBnal=0;
            $activeRow=4;

            $clients[]=0;
            $clientsCount=0;
            $facility=Facility::find()->where(['between', 'DATA', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->orderBy(['ID_CL' => SORT_ASC])->all();
            for($i=0; $i< count($facility); $i++){


                for($j=0;$j<count($facility);$j++){
                    if(!(in_array($facility[$j]->ID_CL, $clients))){
                        $clients[$clientsCount]=$facility[$j]->ID_CL;
                        $clientsCount++;

                    }
                }

            }
            $clientSumm=0;
            $clientDolg=0;
            $clientNal=0;
            $clientBnal=0;
            for($i=0;$i<count($clients);$i++){
                $activeRow=$activeRow+1;
                $cellA='A'.$activeRow;
                $cellB='B'.$activeRow;
                $cellC='C'.$activeRow;
                $cellD='D'.$activeRow;
                $cellE='E'.$activeRow;
                $client=Client::findOne(['ID_CL'=>$clients[$i]]);
                $visits=Vizit::find()->where(['between', 'DATE', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->andWhere(['ID_CL'=>$clients[$i]])->all();
                $oplata=Oplata::find()->where(['between', 'DATE', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->andWhere(['ID_CL'=>$clients[$i]])->all();

                $clientFacility=Facility::find()->where(['between', 'DATA', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->andWhere(['ID_CL'=>$clients[$i]])->all();
                $n = StringHelper::truncate($client->NAME, 1, '');
                $o = StringHelper::truncate($client->OTCH, 1, '');


                $stringA = $client->FAM . ' ' . $n . '. ' . $o . '.';

                $sheet->setCellValue($cellA,$stringA);
                for($j=0;$j<count($clientFacility);$j++){
                    $clientSumm=$clientFacility[$j]->SUMMA+$clientSumm;
                }

                $stringB=$clientSumm;
                $sheet->setCellValue($cellB,$stringB);
                for($j=0;$j<count($visits);$j++){
                    $clientDolg=$visits[$j]->DOLG+$clientDolg;
                }
                for($j=0;$j<count($oplata);$j++){
                    if($oplata->VID_OPL==0){
                        $clientNal=$clientNal+$oplata[$j]->SUMM;
                    }else{
                        $clientBnal=$clientBnal+$oplata[$j]->SUMM;
                    }
                }
                $totalSumm=$totalSumm+$clientSumm;
                $totaltDolg=$totaltDolg+$clientDolg;
                $totaltNal=$totaltNal+$clientNal;
                $totaltBnal=$totaltBnal+$clientBnal;


                $sheet->setCellValue($cellE,$clientDolg);
                $sheet->setCellValue($cellC,$clientNal);
                $sheet->setCellValue($cellD,$clientBnal);
            }
            $activeRow=$activeRow+1;
            $cellA='A'.$activeRow;
            $cellB='B'.$activeRow;
            $cellC='C'.$activeRow;
            $cellD='D'.$activeRow;
            $cellE='E'.$activeRow;


            $sheet->setCellValue($cellA,$clientsCount);
            $sheet->setCellValue($cellB,$totalSumm);
            $sheet->setCellValue($cellC,$totaltNal);
            $sheet->setCellValue($cellD,$totaltBnal);
            $sheet->setCellValue($cellE,$totaltDolg);
            $sheet->getStyle('A4:' . $cellE)
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getColumnDimension('A')->setWidth(17);
            $sheet->getColumnDimension('B')->setWidth(10);
            $sheet->getColumnDimension('C')->setWidth(10);
            $sheet->getColumnDimension('D')->setWidth(10);
            $sheet->getColumnDimension('E')->setWidth(10);



            $filename='/Отчет об услугах по пациентам (сокращенный) c '.date("d.m.Y", strtotime($firstdate)).' по '.date("d.m.Y", strtotime($secondtdate)).'.xlsx';


            $writer = new Xlsx($spreadsheet);
            $writer->save(Yii::getAlias('@reports').$filename);
            $path = \Yii::getAlias('@reports') ;

            $file = $path.$filename;
            if (file_exists($file)) {
                \Yii::$app->response->sendFile($file);
            }else {
                throw new \Exception('Файл не найден');
            }

        }



        $date=date('d.m.Y');
        return $this->render('otchet_uslugi', compact('date'));
    }
    public function actionReport_sale(){
        $date=date('d.m.Y');
        return $this->render('report_sale', compact('date'));
    }



    //----------------------------------------------Отчет по продажам--------------------------------------------------
    public function actionReport_sale_form(){
        Yii::setAlias('@reports', Yii::$app->basePath.'/отчеты');
        $firstdate= ($_GET['FIRST_DATE']);
        $secondtdate= ($_GET['SECOND_DATE']);


        $firstdate =date("Y-m-d", strtotime($firstdate));
        $secondtdate =date("Y-m-d", strtotime($secondtdate));

        $sales=Sale::find()->where(['between', 'DATE', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->all();

        $spreadsheet = new Spreadsheet();
        $titles = array(
            array(
                'name' => '№',
                'cell' => 'A',
            ),
            array(
                'name' => 'Наименование',
                'cell' => 'B',
            ),
            array(
                'name' => 'Цена',
                'cell' => 'C',
            ),
            array(
                'name' => 'Кол-во',
                'cell' => 'D',
            ),
            array(
                'name' => 'Нал.',
                'cell' => 'E',
            ),
            array(
                'name' => 'Б/бнал.',
                'cell' => 'F',
            ),
            array(
                'name' => 'Сумма',
                'cell' => 'G',
            ),

        );

        $specs[]=0;
        $specsCount=0;
        for($i=0; $i<count($sales); $i++){
            if(!in_array($sales[$i]->SOTRUDNIK,$specs)){
                $specs[$specsCount]=$sales[$i]->SOTRUDNIK;
                $specsCount++;


            }
        }
        for($i=0;$i<count($specs);$i++){
            $spreadsheet->setActiveSheetIndex($i);
            $sheet=$spreadsheet->getActiveSheet();
            $doctor=Doctor::findOne(['ID_DOC'=>$specs[$i]]);
            $sheet->setTitle($doctor->NAME);
            $activeRow=5;
            $saleNum=0;
            $totalNal=0;
            $totalBnal=0;
            $totalSumm=0;
            for ($j = 0; $j < count($titles); $j++) {

                $string = $titles[$j]['name'];
                $cellLatter = $titles[$j]['cell'] . 4;
                $sheet->setCellValue($cellLatter, $string);
            }
            $sheet->setCellValue('A1', 'ЗооДоктор');
            $sheet->setCellValue('D1', date("d.m.Y"));
            $sheet->setCellValue('A2', 'Отчет по продажам ('.$doctor->NAME.')');
            $sheet->setCellValue('A3', ' с ' . date("d.m.Y", strtotime($firstdate)) . ' по ' . date("d.m.Y", strtotime($secondtdate)));
            $sheet->mergeCells('A2:F2');

            $specSales=Sale::find()->where(['SOTRUDNIK'=>$specs[$i]])->all();
            for($j=0;$j<count($specSales);$j++){
                $saleNum=$saleNum+1;
                $cellA='A'.$activeRow;
                $cellB='B'.$activeRow;
                $cellC='C'.$activeRow;
                $cellD='D'.$activeRow;
                $cellE='E'.$activeRow;
                $cellF='F'.$activeRow;
                $cellG='G'.$activeRow;
                $tovar=Kattov::findOne(['ID_TOV'=>$specSales[$j]->ID_TOV]);
                $sheet->setCellValue($cellA,$saleNum);
                $sheet->setCellValue($cellB,$tovar->NAME);
                $sheet->setCellValue($cellC,$tovar->PRICE);
                $sheet->setCellValue($cellD,$specSales[$j]->KOL);

                if($specSales[$j]->VID_OPL==0){
                    $sheet->setCellValue($cellE,$specSales[$j]->SUMM);
                    $totalNal=$totalNal+$specSales[$j]->SUMM;
                }else{
                    $sheet->setCellValue($cellF,$specSales[$j]->SUMM);
                    $totalBnal=$totalBnal+$specSales[$j]->SUMM;
                }
                $sheet->setCellValue($cellG,$specSales[$j]->SUMM);
                $totalSumm=$totalSumm+$specSales[$j]->SUMM;
                $activeRow++;
            }
            $cellA='A'.$activeRow;
            $cellD='D'.$activeRow;

            $cellE='E'.$activeRow;
            $cellF='F'.$activeRow;
            $cellG='G'.$activeRow;

            $sheet->setCellValue($cellE,$totalNal);
            $sheet->setCellValue($cellF,$totalBnal);
            $sheet->setCellValue($cellG,$totalSumm);
            $sheet->getStyle('A4:' . $cellG)
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->mergeCells($cellA.':'.$cellD);
            $sheet->getColumnDimension('B')->setWidth(25);
            $spreadsheet->createSheet();

        }


        $filename='/Отчет по продажам c '.date("d.m.Y", strtotime($firstdate)).' по '.date("d.m.Y", strtotime($secondtdate)).'.xlsx';


        $writer = new Xlsx($spreadsheet);
        $writer->save(Yii::getAlias('@reports').$filename);
        $path = \Yii::getAlias('@reports') ;

        $file = $path.$filename;
        if (file_exists($file)) {
            \Yii::$app->response->sendFile($file);
        }else {
            throw new \Exception('Файл не найден');
        }

        return $this->render('report_sale', compact('date'));
    }

    public function actionReport_spec(){
        $date=date('d.m.Y');




        return $this->render('report_spec', compact('date'));
    }
    public function actionReport_spec_form(){
        $date=date('d.m.Y');

        Yii::setAlias('@reports', Yii::$app->basePath.'/отчеты');
        $firstdate= ($_GET['FIRST_DATE']);
        $secondtdate= ($_GET['SECOND_DATE']);


        $firstdate =date("Y-m-d", strtotime($firstdate));
        $secondtdate =date("Y-m-d", strtotime($secondtdate));
        $spreadsheet = new Spreadsheet();
        $sheet=$spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ЗооДоктор');
        $sheet->setCellValue('D1', date("d.m.Y"));
        $sheet->setCellValue('A2', 'Отчет по специалистам');
        $sheet->setCellValue('A3', ' с ' . date("d.m.Y", strtotime($firstdate)) . ' по ' . date("d.m.Y", strtotime($secondtdate)));
        $sheet->mergeCells('A2:F2');
        $titles = array(
            array(
                'name' => 'Специалист',
                'cell' => 'A',
            ),
            array(
                'name' => 'Кол-во пациентов',
                'cell' => 'B',
            ),
            array(
                'name' => 'Выручка',
                'cell' => 'C',
            ),
            array(
                'name' => 'Ср. чек',
                'cell' => 'D',
            ),


        );

        $docs=[];
        $facility=Facility::find()->where(['between', 'DATA', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->all();
        $totalSumm=0;
        for($i=0;$i<count($facility); $i++){
            $idDoc=$facility[$i]->ID_DOC;
            if(!array_key_exists($facility[$i]->ID_DOC, $docs)){
                $docs[$idDoc]=new Doc;
                $doctor=Doctor::findOne(['ID_DOC'=>$idDoc]);
                $docs[$idDoc]->name=$doctor->NAME;
            }
            if(array_key_exists($idDoc, $docs)){
                $docs[$idDoc]->summ=$docs[$idDoc]->summ+$facility[$i]->SUMMA;
                $totalSumm=$totalSumm+$facility[$i]->SUMMA;
                    if(!in_array($facility[$i]->ID_PAC,$docs[$idDoc]->pacients)){
                        array_push($docs[$idDoc]->pacients, $facility[$i]->ID_PAC);
                    }

            }
        }
        for ($j = 0; $j < count($titles); $j++) {

            $string = $titles[$j]['name'];
            $cellLatter = $titles[$j]['cell'] . 4;
            $sheet->setCellValue($cellLatter, $string);
        }
        $activeRow=5;
        foreach ($docs as $d){
            $d->chek=round($d->summ/count($d->pacients),2);
            $rowA='A'.$activeRow;
            $rowB='B'.$activeRow;
            $rowC='C'.$activeRow;
            $rowD='D'.$activeRow;
            $sheet->setCellValue($rowA, $d->name);
            $sheet->setCellValue($rowB, count($d->pacients));
            $sheet->setCellValue($rowC, $d->summ);
            $sheet->setCellValue($rowD, $d->chek);


            $activeRow++;
        }
        $rowA='A'.$activeRow;
        $rowB='B'.$activeRow;
        $rowC='C'.$activeRow;
        $rowD='D'.$activeRow;
        $sheet->setCellValue($rowC, $totalSumm);
        $sheet->mergeCells($rowA.':'.$rowB);

        $sheet->getStyle('A4:' . $rowD)
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(17);





        $filename='/Отчет по специалистам c '.date("d.m.Y", strtotime($firstdate)).' по '.date("d.m.Y", strtotime($secondtdate)).'.xlsx';


        $writer = new Xlsx($spreadsheet);
        $writer->save(Yii::getAlias('@reports').$filename);
        $path = \Yii::getAlias('@reports') ;

        $file = $path.$filename;
        if (file_exists($file)) {
            \Yii::$app->response->sendFile($file);
        }else {
            throw new \Exception('Файл не найден');
        }

        return $this->render('report_spec', compact('date'));


    }

    public function actionReport_dolg(){
        $date=date('d.m.Y');

        return $this->render('report_dolg', compact('date'));
    }

    public function actionReport_dolg_form()
    {
        $date = date('d.m.Y');

        Yii::setAlias('@reports', Yii::$app->basePath . '/отчеты');
        $firstdate = ($_GET['FIRST_DATE']);
        $secondtdate = ($_GET['SECOND_DATE']);
        $visitDolg=Vizit::find()->where(['between', 'DATE', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->andWhere(['>', 'DOLG', 0])->all();

        $spreadsheet=new Spreadsheet();
        $sheet=$spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ЗооДоктор');
        $sheet->setCellValue('D1', date("d.m.Y"));
        $sheet->setCellValue('A2', 'Отчет по долгам');
        $sheet->setCellValue('A3', ' с ' . date("d.m.Y", strtotime($firstdate)) . ' по ' . date("d.m.Y", strtotime($secondtdate)));
        $sheet->mergeCells('A2:F2');
        $titles = array(
            array(
                'name' => 'Дата',
                'cell' => 'A',
            ),
            array(
                'name' => 'Клиент, пациент',
                'cell' => 'B',
            ),
            array(
                'name' => 'Сумма',
                'cell' => 'C',
            ),


        );
        for ($j = 0; $j < count($titles); $j++) {

            $string = $titles[$j]['name'];
            $cellLatter = $titles[$j]['cell'] . 4;
            $sheet->setCellValue($cellLatter, $string);
        }
        $dolgs=[];
        $totalSumm=0;
        for($i=0;$i<count($visitDolg);$i++){
            $idVisit=$visitDolg[$i]->ID_VISIT;
            if(!array_key_exists($visitDolg[$idVisit], $dolgs)){
                $dolgs[$idVisit]=new Dolg();

                $client=Client::findOne(['ID_CL'=>$visitDolg[$i]->ID_CL]);
                $pacient=Pacient::findOne(['ID_PAC'=>$visitDolg[$i]->ID_PAC]);
                $dolgs[$idVisit]->dolgDate=date('d.m.Y', strtotime($visitDolg[$i]->DATE));
                $dolgs[$idVisit]->pacient=$pacient->KLICHKA;
                $dolgs[$idVisit]->fio=$client->FAM.' '.$client->NAME.' '.$client->OTCH;
                $dolgs[$idVisit]->summ=$visitDolg[$i]->DOLG;
                $totalSumm=$dolgs[$idVisit]->summ+$totalSumm;

            }

        }

        $activeRow=5;
        foreach ($dolgs as $dolg) {
            $cellA='A'.$activeRow;
            $cellB='B'.$activeRow;
            $cellC='C'.$activeRow;

            $sheet->setCellValue($cellA, $dolg->dolgDate);
            $sheet->setCellValue($cellB, $dolg->fio.', '.$dolg->pacient);
            $sheet->setCellValue($cellC, $dolg->summ);
            $activeRow++;

        }
        $cellA='A'.$activeRow;
        $cellB='B'.$activeRow;
        $cellC='C'.$activeRow;
        $sheet->setCellValue($cellC, $totalSumm);
        $sheet->mergeCells($cellA.':'.$cellB);
        $sheet->getStyle('A4:' . $cellC)
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(42);







        $filename='/Отчет по долгам c '.date("d.m.Y", strtotime($firstdate)).' по '.date("d.m.Y", strtotime($secondtdate)).'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save(Yii::getAlias('@reports').$filename);
        $path = \Yii::getAlias('@reports');

        $file = $path.$filename;
        if (file_exists($file)) {
            \Yii::$app->response->sendFile($file);
        }else {
            throw new \Exception('Файл не найден');
        }


        return $this->render('report_dolg', compact('date'));
    }

    public function actionReport_newpacient(){
        $date=date('d.m.Y');
        return $this->render('report_newpacient', compact('date'));
    }

    public function actionReport_newpacient_form()
    {
        $date = date('d.m.Y');

        Yii::setAlias('@reports', Yii::$app->basePath . '/отчеты');
        $firstdate = ($_GET['FIRST_DATE']);
        $secondtdate = ($_GET['SECOND_DATE']);
        $spreadsheet= new Spreadsheet();
        $sheet=$spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ЗооДоктор');
        $sheet->setCellValue('D1', date("d.m.Y"));
        $sheet->setCellValue('A2', 'Отчет по новым пациентам');
        $sheet->setCellValue('A3', ' с ' . date("d.m.Y", strtotime($firstdate)) . ' по ' . date("d.m.Y", strtotime($secondtdate)));
        $sheet->mergeCells('A2:F2');
        $titles = array(
            array(
                'name' => '№',
                'cell' => 'A',
            ),
            array(
                'name' => 'Клиент',
                'cell' => 'B',
            ),
            array(
                'name' => 'Пациент',
                'cell' => 'C',
            ),
            array(
                'name' => 'Дата первого визита',
                'cell' => 'D',
            ),


        );
        for ($j = 0; $j < count($titles); $j++) {

            $string = $titles[$j]['name'];
            $cellLatter = $titles[$j]['cell'] . 4;
            $sheet->setCellValue($cellLatter, $string);
        }
        $newPacients=Pacient::find()->where(['between', 'DATE', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->all();
        $pacients=[];
        for($i=0;$i<count($newPacients);$i++){
            $idPac=$newPacients[$i]->ID_PAC;
            $client=Client::findOne(['ID_CL'=>$newPacients[$i]->ID_CL]);

            if(!array_key_exists($idPac, $pacients)){
                $pacients[$idPac]= new NewPacient();
                $pacients[$idPac]->fio= $client->FAM.' '.$client->NAME.' '.$client->OTCH;
                $vid=Vid::findOne(['ID_VID'=>$newPacients[$i]->ID_VID]);
                $pacients[$idPac]->name=$newPacients[$i]->KLICHKA;
                $pacients[$idPac]->vid=$vid->NAMEVID;
                $pacients[$idPac]->date=date('d.m.Y', strtotime($newPacients[$i]->DATE));
            }
        }
        $activeRow=5;
        $rowCount=1;
        foreach ($pacients as $pacient){
            $cellA='A'.$activeRow;
            $cellB='B'.$activeRow;
            $cellC='C'.$activeRow;
            $cellD='D'.$activeRow;

            $sheet->setCellValue($cellA, $rowCount);
            $sheet->setCellValue($cellB, $pacient->fio);
            $sheet->setCellValue($cellC, $pacient->vid.' '.$pacient->name);
            $sheet->setCellValue($cellD, $pacient->date);
            $rowCount++;
            $activeRow++;
        }


        $sheet->getStyle('A4:' . $cellD)
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(42);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(20);




        $filename='/Отчет по новым пациентам c '.date("d.m.Y", strtotime($firstdate)).' по '.date("d.m.Y", strtotime($secondtdate)).'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save(Yii::getAlias('@reports').$filename);
        $path = \Yii::getAlias('@reports');

        $file = $path.$filename;
        if (file_exists($file)) {
            \Yii::$app->response->sendFile($file);
        }else {
            throw new \Exception('Файл не найден');
        }

        return $this->render('report_newpacient', compact('date'));
    }
    public function actionReport_predusl(){
        $date=date('d.m.Y');
        return $this->render('report_predusl', compact('date'));
    }
    public function actionReport_predusl_form(){
        $date=date('d.m.Y');
        Yii::setAlias('@reports', Yii::$app->basePath . '/отчеты');
        $firstdate = ($_GET['FIRST_DATE']);
        $secondtdate = ($_GET['SECOND_DATE']);
        $spreadsheet= new Spreadsheet();
        $sheet=$spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ЗооДоктор');
        $sheet->setCellValue('D1', date("d.m.Y"));
        $sheet->setCellValue('A2', 'Отчет по предстоящим услугам');
        $sheet->setCellValue('A3', ' с ' . date("d.m.Y", strtotime($firstdate)) . ' по ' . date("d.m.Y", strtotime($secondtdate)));
        $sheet->mergeCells('A2:F2');
        $titles = array(
            array(
                'name' => 'Дата слeд.',
                'cell' => 'A',
            ),
            array(
                'name' => 'Дата',
                'cell' => 'B',
            ),
            array(
                'name' => 'Услуга',
                'cell' => 'C',
            ),
            array(
                'name' => 'Клиент',
                'cell' => 'D',
            ),
            array(
                'name' => 'Пациент',
                'cell' => 'E',
            ),
            array(
                'name' => 'Телефоны',
                'cell' => 'F',
            ),


        );
        for ($j = 0; $j < count($titles); $j++) {

            $string = $titles[$j]['name'];
            $cellLatter = $titles[$j]['cell'] . 4;
            $sheet->setCellValue($cellLatter, $string);
        }
        $facility=Facility::find()->where(['between', 'DATASL', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->all();


        $uslugi=[];

        for($i=0;$i<count($facility);$i++){
            $idFacility=$facility[$i]->ID_FAC;

            $usl=Price::findOne(['ID_PR'=>$facility[$i]->ID_PR]);
            $client=Client::findOne(['ID_CL'=>$facility[$i]->ID_CL]);
            $pacient=Pacient::findOne(['ID_PAC'=>$facility[$i]->ID_PAC]);

            if(!array_key_exists($idFacility,$uslugi)){

                $uslugi[$idFacility]=new Predusl();
                $uslugi[$idFacility]->firstdate=date('d.m.Y', strtotime($facility[$i]->DATA));
                $uslugi[$idFacility]->seconddate=date('d.m.Y', strtotime($facility[$i]->DATASL));
                $uslugi[$idFacility]->usluga=$usl->NAME;
                $uslugi[$idFacility]->fio=$client->FAM.' '.$client->NAME.' '.$client->OTCH;
                $uslugi[$idFacility]->pacient=$pacient->KLICHKA;
                $uslugi[$idFacility]->numbers=$client->PHONED.', '.$client->PHONES;


            }


        }
        $activeRow=5;
        foreach ($uslugi as $usluga){
            $cellA='A'.$activeRow;
            $cellB='B'.$activeRow;
            $cellC='C'.$activeRow;
            $cellD='D'.$activeRow;
            $cellE='E'.$activeRow;
            $cellF='F'.$activeRow;

            $sheet->setCellValue($cellA, $usluga->seconddate);
            $sheet->setCellValue($cellB, $usluga->firstdate);
            $sheet->setCellValue($cellC, $usluga->usluga);
            $sheet->setCellValue($cellD, $usluga->fio);
            $sheet->setCellValue($cellE, $usluga->pacient);
            $sheet->setCellValue($cellF, $usluga->numbers);
            $activeRow++;
        }

        $sheet->getStyle('A4:' . $cellF)
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(21);
        $sheet->getColumnDimension('F')->setWidth(40);




        $filename='/Отчет по предстоящим услугам c '.date("d.m.Y", strtotime($firstdate)).' по '.date("d.m.Y", strtotime($secondtdate)).'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save(Yii::getAlias('@reports').$filename);
        $path = \Yii::getAlias('@reports');

        $file = $path.$filename;
        if (file_exists($file)) {
            \Yii::$app->response->sendFile($file);
        }else {
            throw new \Exception('Файл не найден');
        }
        return $this->render('report_predusl', compact('date'));
    }

    public function actionReport_pay(){
        $date=date('d.m.Y');
        return $this->render('report_pay', compact('date'));
    }

    public function actionReport_pay_form(){
        $date=date('d.m.Y');
        Yii::setAlias('@reports', Yii::$app->basePath . '/отчеты');
        $firstdate = ($_GET['FIRST_DATE']);
        $secondtdate = ($_GET['SECOND_DATE']);
        $spreadsheet= new Spreadsheet();


        $doctors=Doctor::find()->all();
        foreach ($doctors as $doc){
            if($doc->STATUS_R==1) {
                $id = $doc->ID_DOC;
                $pays[$id] = new DoctorPay();
                $pays[$id]->id = $id;
                $pays[$id]->name = $doc->NAME;
                $pays[$id]->thProcent = $doc->THERAPY;
                $pays[$id]->suProcent = $doc->SURGERY;
                $pays[$id]->uzProcent = $doc->UZI;
                $pays[$id]->vakProcent = $doc->VAKC;
                $pays[$id]->medProcent = $doc->MED;
                $pays[$id]->degProcent = $doc->DEG;
                $pays[$id]->anProcent = $doc->ANALYZ;
                $pays[$id]->kormProcent = $doc->KORM;
            }
        }

        foreach ($pays as $pay){


            $facility=Facility::find()->where(['between', 'DATA', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])
                ->andWhere(['ID_DOC'=>$pay->id])->all();

            foreach ($facility as $fac){
                $usluga=Price::findOne(['ID_PR'=>$fac->ID_PR]);
                $spdoc=$usluga->ID_SPDOC;
                switch ($spdoc){
                    case 1:
                        $pay->thSumm=$pay->thSumm+$fac->SUMMA;
                        break;
                    case 2:
                        $pay->suSumm=$pay->suSumm+$fac->SUMMA;
                        break;
                    case 3:
                        $pay->uzsumm=$pay->uzsumm+$fac->SUMMA;
                        break;
                    case 4:
                        $pay->vakSumm=$pay->medSumm+$fac->SUMMA;
                        break;
                    case 5:
                        $pay->medSumm=$pay->vakSumm+$fac->SUMMA;
                        break;
                    case 6:
                        $pay->degSumm=$pay->degSumm+$fac->SUMMA;
                        break;
                    case 7:
                        $pay->anSumm=$pay->anSumm+$fac->SUMMA;
                        break;
                    case 8:
                        $pay->kormSumm=$pay->kormSumm+$fac->SUMMA;
                        break;
                }

            }

        }

        $titles = array(
            array(
                'name' => 'Вид манипуляций',
                'cell' => 'A',
            ),
            array(
                'name' => 'Процент',
                'cell' => 'B',
            ),
            array(
                'name' => 'Сумма поступлений',
                'cell' => 'C',
            ),
            array(
                'name' => 'Зарплата',
                'cell' => 'D',
            ),

        );
        $activeRow=5;
        $activeSheet=0;
        foreach ($pays as $pay){
            $sheet=$spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'ЗооДоктор');
            $sheet->setCellValue('D1', date("d.m.Y"));
            $sheet->setCellValue('A2', 'Расчет зарплаты - '.$pay->name);
            $sheet->setCellValue('A3', ' с ' . date("d.m.Y", strtotime($firstdate)) . ' по ' . date("d.m.Y", strtotime($secondtdate)));
            $sheet->mergeCells('A2:E2');


            for ($j = 0; $j < count($titles); $j++) {

                $string = $titles[$j]['name'];
                $cellLatter = $titles[$j]['cell'] . 4;
                $sheet->setCellValue($cellLatter, $string);
            }
            $sheet->setCellValue('A5', 'Терапия');
            $sheet->setCellValue('A6', 'Хиругия');
            $sheet->setCellValue('A7', 'УЗИ');
            $sheet->setCellValue('A8', 'Медикаменты');
            $sheet->setCellValue('A9', 'Вакцинация');
            $sheet->setCellValue('A10', 'Дегельминтизация');
            $sheet->setCellValue('A11', 'Анализы');
            $sheet->setCellValue('A12', 'Корм');

            $sheet->setCellValue('B5', $pay->thProcent);
            $sheet->setCellValue('B6', $pay->suProcent);
            $sheet->setCellValue('B7', $pay->uzProcent);
            $sheet->setCellValue('B8', $pay->vakProcent);
            $sheet->setCellValue('B9', $pay->medProcent);
            $sheet->setCellValue('B10', $pay->degProcent);
            $sheet->setCellValue('B11', $pay->anProcent);
            $sheet->setCellValue('B12', $pay->kormProcent);

            $sheet->setCellValue('C5', $pay->thSumm);
            $sheet->setCellValue('C6', $pay->suSumm);
            $sheet->setCellValue('C7', $pay->uzSumm);
            $sheet->setCellValue('C8', $pay->vakSumm);
            $sheet->setCellValue('C9', $pay->medSumm);
            $sheet->setCellValue('C10', $pay->degSumm);
            $sheet->setCellValue('C11', $pay->anSumm);
            $sheet->setCellValue('C12', $pay->kormSumm);

            $sheet->setCellValue('D5', $pay->thSumm*($pay->thProcent/100));
            $sheet->setCellValue('D6', $pay->suSumm*($pay->suProcent/100));
            $sheet->setCellValue('D7', $pay->uzSumm*($pay->uzProcent/100));
            $sheet->setCellValue('D8', $pay->vakSumm*($pay->vakProcent/100));
            $sheet->setCellValue('D9', $pay->medSumm*($pay->medProcent/100));
            $sheet->setCellValue('D10', $pay->degSumm*($pay->degProcent/100));
            $sheet->setCellValue('D11', $pay->anSumm*($pay->anProcent/100));
            $sheet->setCellValue('D12', $pay->kormSumm*($pay->kormProcent/100));

            $totalSumm=$pay->thSumm*($pay->thProcent/100)+$pay->suSumm*($pay->suProcent/100)+$pay->uzSumm*($pay->uzProcent/100)+$pay->vakSumm*($pay->vakProcent/100)+
                $pay->medSumm*($pay->medProcent/100)+$pay->degSumm*($pay->degProcent/100)+$pay->anSumm*($pay->anProcent/100)+$pay->kormSumm*($pay->kormProcent/100);

            $sheet->setCellValue('D13', $totalSumm);

            $sheet->getColumnDimension('A')->setWidth(18);
            $sheet->getColumnDimension('B')->setWidth(9);
            $sheet->getColumnDimension('C')->setWidth(19);
            $sheet->getColumnDimension('D')->setWidth(11);

            $sheet->setTitle($pay->name);
            $sheet->getStyle('A4:' . 'D13')
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->mergeCells('A13:C13');
            $activeSheet++;
            $totalSumm=0;
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($activeSheet);
        }
//        class DoctorPay{
//            public $name;
//            public $id;
//            public $thProcent;
//            public $suProcent;
//            public $uzProcent;
//            public $vakProcent;
//            public $medProcent;
//            public $degProcent;
//            public $anProcent;
//            public $kormProcent;
//
//            public $thSumm;
//            public $suSumm;
//            public $uzSumm;
//            public $vakSumm;
//            public $medSumm;
//            public $degSumm;
//            public $anSumm;
//            public $kormSumm;
//        }


        $filename='/Расчет зарплаты c '.date("d.m.Y", strtotime($firstdate)).' по '.date("d.m.Y", strtotime($secondtdate)).'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save(Yii::getAlias('@reports').$filename);
        $path = \Yii::getAlias('@reports');

        $file = $path.$filename;
        if (file_exists($file)) {
            \Yii::$app->response->sendFile($file);
        }else {
            throw new \Exception('Файл не найден');
        }
        return $this->render('report_pay', compact('date'));

    }

    public function actionReport_stat(){
        $date=date('d.m.Y');

        return $this->render('report_stat', compact('date'));
    }

    public function actionReport_stat_form(){
        $date=date('d.m.Y');
        Yii::setAlias('@reports', Yii::$app->basePath . '/отчеты');
        $firstdate = ($_GET['FIRST_DATE']);
        $secondtdate = ($_GET['SECOND_DATE']);
        $spreadsheet= new Spreadsheet();
        $sheet=$spreadsheet->getActiveSheet();
        $sheet=$spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ЗооДоктор');
        $sheet->setCellValue('D1', date("d.m.Y"));
        $sheet->setCellValue('A2', 'Средние данные');
        $sheet->setCellValue('A3', ' с ' . date("d.m.Y", strtotime($firstdate)) . ' по ' . date("d.m.Y", strtotime($secondtdate)));
        $sheet->mergeCells('A2:D2');
        $d1_ts = strtotime($firstdate);
        $d2_ts = strtotime($secondtdate);

        $seconds = abs($d1_ts - $d2_ts);
        $days=floor($seconds / 86400);

        $sheet->setCellValue('E2','Количество дней: '.$days);

        $sheet->setCellValue('A5','Количество пациентов за период:');
        $sheet->setCellValue('A6','Сумма выручки за период:');
        $sheet->setCellValue('A7','Ср. количество пациентов в сутки:');
        $sheet->setCellValue('A8','Ср. сумма чека:');
        $sheet->setCellValue('A9','Ср. выручка в сутки:');
        $sheet->getColumnDimension('A')->setWidth(34);

        $facility=Facility::find()->where(['between', 'DATA', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->all();
        $totalSumm=0;
        $visits=Vizit::find()->where(['between', 'DATE', date('Y-m-d',strtotime($firstdate)),date('Y-m-d',strtotime($secondtdate))])->all();
        foreach ($visits as $visit){
            $totalSumm=$totalSumm+($visit->SUMMAV-$visit->DOLG);
        }

        $sheet->setCellValue('B5', count($facility));
        $sheet->setCellValue('B6', $totalSumm);
        $sheet->setCellValue('B7', count($facility)/$days);
        $sheet->setCellValue('B8', $totalSumm/count($visits));
        $sheet->setCellValue('B9', $totalSumm/$days);
        $sheet->getColumnDimension('E')->setWidth(20);




        $filename='/Средние данные c '.date("d.m.Y", strtotime($firstdate)).' по '.date("d.m.Y", strtotime($secondtdate)).'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save(Yii::getAlias('@reports').$filename);
        $path = \Yii::getAlias('@reports');

        $file = $path.$filename;
        if (file_exists($file)) {
            \Yii::$app->response->sendFile($file);
        }else {
            throw new \Exception('Файл не найден');
        }


        return $this->render('report_stat', compact('date'));
    }


}