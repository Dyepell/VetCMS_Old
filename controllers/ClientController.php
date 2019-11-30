<?php


namespace app\controllers;
use app\models\Analys_blood;
use app\models\AnalysbloodForm;
use app\models\Client;
use app\models\ClientForm;
use app\models\Doctor;
use app\models\DoctorForm;
use app\models\Facility;
use app\models\FacilityForm;
use app\models\Istbol;
use app\models\IstbolForm;
use app\models\KattovForm;
use app\models\OplataForm;
use app\models\PacientForm;
use app\models\Poroda;
use app\models\Pred_uslForm;
use app\models\PriceForm;
use app\models\Sale;
use app\models\SaleForm;
use app\models\SearchForm;
use app\models\SelectForm;
use app\models\Diagnoz;
use app\models\Price;
use app\models\Biohim;
use app\models\Mocha;
use app\models\MochaForm;
use app\models\Uzi;
use app\models\UziForm;
use app\models\Other_isl;
use app\models\Other_islForm;
use app\models\Kattov;
use app\models\TovarForm;
use app\models\Prihod_tovaraForm;
use app\models\Prihod_tovara;
use yii\helpers\StringHelper;
use app\models\Pred_usl;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use app\models\Facility_test;


use app\models\Vid;
use app\models\Vizit;
use app\models\Sl_vakc;
use app\models\VizitForm;
use PHPExcel;
use yii\base\Model;
use yii\debug\models\timeline\Search;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use app\models\BiohimForm;

use app\models\Pacient;
use Yii;

use yii\data\Pagination;


class ClientController extends AppController
{
    public $layout='basic';

    public function beforeAction($action)
    {
        if ($action->id=='index'){
            $this->enableCsrfValidation=false;
        }
        return parent::beforeAction($action);
    }

    public function actionIndex(){
        $model = new SearchForm();
        if(Yii::$app->request->isPjax){

            $test = $_POST[SearchForm];


            $answer = $test['FAM'];
            if ($answer==''){
                $searchProvider=NULL;
            }else{
                $searchProvider = new ActiveDataProvider([
                    'query' => Client::find()->where(['like', 'FAM', $answer.'%', false]),
                    'pagination'=>false,
                    'sort'=> ['defaultOrder' => ['NAME' => SORT_ASC]],



                ]);




                }

            $model->load(Yii::$app->request->post());




            return $this->render('registration', compact('model', 'answer',  'searchProvider'));
        }
        $this->view->title='Регистрация';









        $selectClientId=$_GET['selectClientId'];
        $selectClient=Client::find()->where(['ID_CL'=>$selectClientId])->with('pacients')->all();
        $pacients=Pacient::find()->where(['ID_CL'=>$selectClientId]);

        $dataProvider = new ActiveDataProvider([
            'query' => Client::find(),
            'pagination' => [
                'pageSize' => 10,

            ],
        ]);
        if(!Yii::$app->request->get('page')){
            $dataProvider->pagination->page = ceil($dataProvider->getTotalCount() / $dataProvider->pagination->pageSize) - 1;
        }
//        $clients = Client::find();
//        $pageCount=$clients->count();
//        $pages= new Pagination(['totalCount'=>$pageCount, 'pageSize'=>20]);
//        $clientsPagination=$clients->offset($pages->offset)->limit($pages->limit)->all();





        return $this->render('registration', compact('pages',  'searchValue', 'model', 'dataProvider', 'selectClient', 'selectClientId'));
    }

    public function actionClientout(){
        $selectClient =1;
        return '123';

    }
    public function actionAnketa(){
        $this->enableCsrfValidation = false;

        $clientId=$_GET['clientId'];
        $model = ClientForm::findOne(['ID_CL'=>$clientId]);
        $pacients=Pacient::find()->where(['ID_CL'=>$clientId])->all();
        $pacModel= PacientForm::find()->where(['ID_CL'=>$clientId])->with('vid')->with('poroda')->with('doctor')->all();
        $newPacient=new PacientForm();
        $this->view->title=$model->FAM.' '.$model->NAME;

        if ( $model->load(Yii::$app->request->post()) ){
            if ($model->save()){

                return $this->refresh();
            }
        }


        if (Yii::$app->request->post(PacientForm)!=null){


            $PAC= Yii::$app->request->post(PacientForm);
            $PAC=$PAC['$i'];
            if ($PAC['ID_PAC']==''){
                if ( $newPacient->load(Yii::$app->request->post()) ){
                    $newPacient->DATE=date('Y-m-d');
                    if ($newPacient->save()){

                        return $this->refresh();
                    }
                }
            }



            $selectPacient=PacientForm::find()->where(['ID_PAC'=>$PAC['ID_PAC']])->all();

            $selectPacient[0]->KLICHKA=$PAC['KLICHKA'];
            $selectPacient[0]->ID_VID=$PAC['ID_VID'];
            $selectPacient[0]->ID_POR=$PAC['ID_POR'];
            $selectPacient[0]->BDAY=$PAC['BDAY'];
            $selectPacient[0]->POL=$PAC['POL'];
            $selectPacient[0]->VOZR=$PAC['VOZR'];
            $selectPacient[0]->ID_LDOC=$PAC['ID_LDOC'];
            $selectPacient[0]->PRIMECH=$PAC['PRIMECH'];




            $selectPacient[0]->save();
            $this->refresh();

        }




//        if ( $pacModel[]->load(Yii::$app->request->post()) ){
//            if ($pacModel[Yii::$app->request->post(['$i'])]->save()){
//
//                return $this->refresh();
//            }
//        }



        return $this->render('anketa', compact('clientId', 'model', 'pacients', 'pacModel', 'newPacient'));
    }
    public function actionClientadd(){

        $model = new ClientForm();
        $model->FIRST_DATE_N=date("Y-m-d");
        if ( $model->load(Yii::$app->request->post()) ){
            if ($model->save()){
                $clientId=$model->ID_CL;

                $this->redirect("index.php?r=client/anketa&clientId=".$clientId);
            }
        }else{
            return $this->render('anketa', compact('clientId', 'model'));
        }



    }
    public function actionClientdelete(){
        $clientId=$_GET['clientId'];

        $model=ClientForm::findOne(['ID_CL'=>$clientId]);
        $visits=Vizit::find()->where(['ID_CL'=>$model->ID_CL])->all();
        $pacients=Pacient::find()->where(['ID_CL'=>$model->ID_CL])->all();
        foreach ($pacients as $pac){
            $pac->delete();
        }
        foreach ($visits as $visit){
            $visit->delete();
        }
        $facility=Facility::find()->where(['ID_CL'=>$model->ID_CL])->all();
        foreach ($facility  as $fac){
            $fac->delete();
        }
        $model->delete();
        $this->redirect("index.php?r=client");


    }
    public function actionPacientdelete(){
        $clientId=$_GET['clientId'];
        $deletePacient=$_GET['deletePacient'];
        $model=PacientForm::findOne(['ID_PAC'=>$deletePacient]);
        $visits=Vizit::find()->where(['ID_PAC'=>$model->ID_PAC])->all();
        foreach ($visits as $visit){
            $visit->delete();
        }
        $facility=Facility::find()->where(['ID_PAC'=>$model->ID_PAC])->all();
        foreach ($facility  as $fac){
            $fac->delete();
        }
        $model->delete();
        $this->redirect("index.php?r=client/anketa&clientId=".$clientId);


    }

    public function actionVisits(){

       $pacientId=$_GET['pacientId'];
       $clientId=$_GET['clientId'];
        $dataProvider = new ActiveDataProvider([
            'query' => Vizit::find()->where(['ID_PAC'=>$pacientId])->with('diagnoz'),
            'pagination'=>false,

        ]);
        $vakcineProvider = new ActiveDataProvider([
            'query' => Facility::find()->Where(['ID_PAC'=>$pacientId])->andWhere(['!=', 'DATASL', '']),
            'pagination' => [
                'pageSize'=>10,
            ],

        ]);

        $client=Client::findOne(['ID_CL'=>$clientId]);
        $pacient=Pacient::findOne(['ID_PAC'=>$pacientId]);
        $this->view->title='Визиты: '.$pacient->KLICHKA;

        return $this->render('visits', compact('dataProvider', 'client', 'pacient', 'vakcineProvider'));
    }

    public function actionVisit(){
        if ($_GET['ID_VISIT']!=NULL){
            $visit=VizitForm::findOne(['ID_VISIT'=>$_GET['ID_VISIT']]);
            $pacient=Pacient::findOne(['ID_PAC'=>$visit->ID_PAC]);
            $prFacProvider = new ActiveDataProvider([
                'query' => Facility::find()->where(['ID_PAC'=>$pacient->ID_PAC]),
                'pagination' => false,


            ]);
            $FacilityProvider = new ActiveDataProvider([
                'query' => Facility::find()->where(['ID_VISIT'=>$visit->ID_VISIT]),
                'pagination' => false,


            ]);
            $fac =Facility::find()->where(['ID_VISIT'=>$visit->ID_VISIT])->all();

            $istbolProvider = new ActiveDataProvider([
                'query' => Istbol::find()->where(['ID_PAC'=>$visit->ID_PAC]),
                'pagination' => false,


            ]);
        $visit->DATE=date("d.m.Y", strtotime($visit->DATE));
        }else{
            $visit=new VizitForm();
            $visit->ID_PAC=$_GET['ID_PAC'];
            $pac=Pacient::findOne(['ID_PAC'=>$visit->ID_PAC]);

            $visit->ID_CL=$pac->ID_CL;
            $visit->DATE=date("d.m.Y");
        }
        if ( $visit->load(Yii::$app->request->post()) ){
            $visit->DATE=date("Y-m-d", strtotime($visit->DATE));
            if ($visit->SUMMAO!=''){
                $client=Client::findOne(['ID_CL'=>$visit->ID_CL]);
                $oplata=new OplataForm();
                $oplata->ID_CL=$client->ID_CL;
                $oplata->DATE=date('Y-m-d');
                $oplata->VID_OPL=$visit->VIDOPL;
                $oplata->SUMM=$visit->SUMMAO;
                $oplata->ID_VIZIT=$visit->ID_VISIT;
                $oplata->save();
                $visit->DOLG=$visit->DOLG-$visit->SUMMAO;
                $visit->SUMMAO='';

                if($visit->DOLG==0){
                    $visit->DATE_OPL=date("Y-m-d");
                }
            }

            if ($visit->save()){
                $ID_VISIT=$visit->ID_VISIT;

                return $this->redirect("index.php?r=client/visit&ID_VISIT=".$ID_VISIT.'&ID_PAC='.$_GET['ID_PAC']);
            }
        }








        $this->view->title='Визит: '.$pacient->KLICHKA;
        return $this->render('visit', compact('pacient', 'visit', 'prFacProvider', 'FacilityProvider', 'totalSumm', 'istbolProvider'));
    }

    public function actionPrice(){


        $teraphyProvider = new ActiveDataProvider([
            'query' => Price::find()->where(['ID_SPDOC'=>'1']),
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['NAME' => SORT_ASC]],

        ]);
        $surgaryProvider = new ActiveDataProvider([
            'query' => Price::find()->where(['ID_SPDOC'=>'2']),
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['NAME' => SORT_ASC]],

        ]);
        $uziProvider = new ActiveDataProvider([
            'query' => Price::find()->where(['ID_SPDOC'=>'3']),
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['NAME' => SORT_ASC]],

        ]);
        $medicinesProvider = new ActiveDataProvider([
            'query' => Price::find()->where(['ID_SPDOC'=>'4']),
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['NAME' => SORT_ASC]],

        ]);
        $vakcineProvider = new ActiveDataProvider([
            'query' => Price::find()->where(['ID_SPDOC'=>'5']),
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['NAME' => SORT_ASC]],

        ]);
        $degProvider = new ActiveDataProvider([
            'query' => Price::find()->where(['ID_SPDOC'=>'6']),
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['NAME' => SORT_ASC]],

        ]);
        $analysisProvider = new ActiveDataProvider([
            'query' => Price::find()->where(['ID_SPDOC'=>'7']),
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['NAME' => SORT_ASC]],

        ]);
        $feedProvider = new ActiveDataProvider([
            'query' => Price::find()->where(['ID_SPDOC'=>'8']),
            'pagination' => false,
            'sort'=> ['defaultOrder' => ['NAME' => SORT_ASC]],

        ]);



        return $this->render('price', compact('teraphyProvider', 'surgaryProvider',
            'uziProvider', 'medicinesProvider', 'vakcineProvider', 'degProvider','analysisProvider',
        'feedProvider'));
    }
    public function actionAddprice(){
        if ($_GET['ID_PR']!=NULL){
            $model=PriceForm::findOne(['ID_PR'=>$_GET['ID_PR']]);


        }else{
            $model=new PriceForm();
            $model->DATA=date('d.m.Y');
        }

            if ( $model->load(Yii::$app->request->post()) ){

                if ($model->save()){

                    return $this->redirect("index.php?r=client/price");
                }
            }
        return $this->render('addPrice', compact('model'));
    }
    public function  actionPricedelete(){
        $model=Price::findOne(['ID_PR'=>$_GET['ID_PR']]);
        $model->delete();
        return $this->redirect("index.php?r=client/price");
    }
    public function actionDoctor(){
        $dataProvider = new ActiveDataProvider([
            'query' => Doctor::find(),
            'pagination' => false,


        ]);
        return $this->render('doctor', compact('dataProvider'));
    }
    public function actionAdddoctor(){
        if ($_GET['ID_DOC']!=NULL){
            $model = DoctorForm::findOne(['ID_DOC'=>$_GET['ID_DOC']]);
        }else{
            $model = new DoctorForm();
        }
        if ( $model->load(Yii::$app->request->post()) ){

            if ($model->save()){

                return $this->redirect("index.php?r=client/doctor");
            }
        }
        return $this->render('adddoctor', compact('model'));
    }
    public function  actionDoctordelete(){
        $model=Doctor::findOne(['ID_DOC'=>$_GET['ID_DOC']]);
        $model->delete();
        return $this->redirect("index.php?r=client/doctor");
    }
    public function actionFacility(){
        $facility = new FacilityForm();
        $facility->DATA=date("d.m.Y");
        $visit=Vizit::findOne(['ID_VISIT'=>$_GET['ID_VISIT']]);
        $pacient=Pacient::findOne(['ID_PAC'=>$visit->ID_PAC]);
        $client=Client::findOne(['ID_CL'=>$pacient->ID_CL]);
        $facility->ID_CL=$client->ID_CL;
        $facility->ID_PAC=$pacient->ID_PAC;
        $ID_PAC=$facility->ID_PAC;

        if ( $facility->load(Yii::$app->request->post()) ){
            $facility->ID_VISIT=$_GET['ID_VISIT'];
            $price=Price::findOne(['ID_PR'=>$facility->ID_PR]);
            $facility->PRICE=$price->PRICE;
            $facility->SUMMA=$price->PRICE * $facility->KOL;
            $visit->DOLG=$visit->DOLG+$facility->SUMMA;
            $visit->SUMMAV=$visit->SUMMAV + $facility->SUMMA;

            $facility->DATA=date("Y-m-d", strtotime($facility->DATA));
            if($facility->DATASL!=NULL){
                $facility->DATASL=date('Y-m-d', strtotime($facility->DATASL));
            }
            if($facility->DATASL==''){
                $facility->DATASL=NULL;
            }

            if($visit->DATE_OPL!=NULL){
                $visit->DATE_OPL='';
            }



            $visit->save();
            if ($facility->save()){

                return $this->redirect("index.php?r=client/visit&ID_VISIT=".$_GET['ID_VISIT'].'&ID_PAC='.$ID_PAC);
            }
        }


        return $this->render('facility', compact('facility'));
    }
    public function actionIstbol(){
        if ($_GET['ID_IST']!=NULL){
            $istbol = IstbolForm::findOne(['ID_IST'=>$_GET['ID_IST']]);
            $pacient = Pacient::findOne(['ID_PAC'=>$istbol->ID_PAC]);

        }else{

            $istbol =new IstbolForm();
            $istbol->DIST=date("d.m.Y");
            $pacient = Pacient::findOne(['ID_PAC'=>$_GET['ID_PAC']]);
        }


        if ( $istbol->load(Yii::$app->request->post()) ){

            if ($istbol->save()){

                return $this->redirect("index.php?r=client/istbol&ID_IST=".$istbol->ID_IST);
            }
        }
        return $this->render('istbol', compact('istbol', 'pacient'));
    }
    public function  actionIstboldelete(){
        $model=Istbol::findOne(['ID_IST'=>$_GET['ID_IST']]);
        $pacient=Pacient::findOne(['ID_PAC'=>$model->ID_PAC]);
        $model->delete();
        return $this->redirect("index.php?r=client/anketa&clientId=".$pacient->ID_CL);
    }

    public function actionAnalysis(){
        $pacient=Pacient::findOne(['ID_PAC'=>$_GET['ID_PAC']]);
        $this->view->title='Исследования: '.$pacient->KLICHKA;
        $bloodProvider = new ActiveDataProvider([
            'query' => Analys_blood::find()->where(['ID_PAC'=>$pacient->ID_PAC]),
            'pagination' => false,


        ]);
        $BiohimProvider = new ActiveDataProvider([
            'query' => Biohim::find()->where(['ID_PAC'=>$pacient->ID_PAC]),
            'pagination' => false,


        ]);
        $MochaProvider = new ActiveDataProvider([
            'query' => Mocha::find()->where(['ID_PAC'=>$pacient->ID_PAC]),
            'pagination' => false,


        ]);
        $UziProvider = new ActiveDataProvider([
            'query' => Uzi::find()->where(['ID_PAC'=>$pacient->ID_PAC]),
            'pagination' => false,


        ]);
        $OtherProvider = new ActiveDataProvider([
            'query' => Other_isl::find()->where(['ID_PAC'=>$pacient->ID_PAC]),
            'pagination' => false,


        ]);
        return $this->render('analysis', compact('pacient', 'bloodProvider', 'BiohimProvider', 'MochaProvider',
        'UziProvider', 'OtherProvider'));
    }
    public function actionBlood(){
        if ($_GET['ID_BLOOD']!=NULL){
            $blood = AnalysbloodForm::findOne(['ID_BLOOD'=>$_GET['ID_BLOOD']]);
        }else{
            $blood=new AnalysbloodForm();
            $blood->ID_PAC=$_GET['ID_PAC'];
            $blood->DATE=date('d.m.Y');
        }

        $pacient=Pacient::findOne(['ID_PAC'=>$_GET['ID_PAC']]);

        if ( $blood->load(Yii::$app->request->post()) ){
            $blood->DATE=date('Y-m-d', strtotime($blood->DATE));
            if ($blood->save()){
                $this->debug($blood);
                $ID_BLOOD=$blood->ID_BLOOD;


                return $this->redirect('index.php?r=client/blood&ID_PAC='.$pacient->ID_PAC.'&ID_BLOOD='.$ID_BLOOD);
            }
        }
        return $this->render('blood', compact('blood', 'pacient'));
    }
    public function actionBlooddelete(){
        $blood = Analys_blood::findOne(['ID_BLOOD'=>$_GET['ID_BLOOD']]);
        $ID_PAC=$blood->ID_PAC;
        $blood->delete();
        return $this->redirect('index.php?r=client/analysis&ID_PAC='.$ID_PAC);
    }
    public function actionFacilitydelete(){
        $facility=Facility::findOne(['ID_FAC'=>$_GET['ID_FAC']]);

        $visit=Vizit::findOne(['ID_VISIT'=>$_GET['ID_VISIT']]);
        $summ=$facility->SUMMA;

        $visit->SUMMAV=$visit->SUMMAV-$summ;
        $visit->DOLG=$visit->DOLG-$summ;
        $facility->delete();
        $visit->save();

        return $this->redirect('index.php?r=client/visit&ID_VISIT='.$_GET['ID_VISIT']);
    }


    public function actionBiohim(){
        if ($_GET['ID_BIOHIM']!=NULL){
            $blood = BiohimForm::findOne(['ID_BIOHIM'=>$_GET['ID_BIOHIM']]);
        }else{
            $blood=new BiohimForm();
            $blood->ID_PAC=$_GET['ID_PAC'];
            $blood->DATE=date('d.m.Y');

        }

        $pacient=Pacient::findOne(['ID_PAC'=>$_GET['ID_PAC']]);

        if ( $blood->load(Yii::$app->request->post()) ){
            $blood->DATE=date('Y-m-d', strtotime($blood->DATE));
            if ($blood->save()){

                $ID_BIOHIM=$blood->ID_BIOHIM;
                return $this->redirect('index.php?r=client/biohim&ID_PAC='.$pacient->ID_PAC.'&ID_BIOHIM='.$ID_BIOHIM);
            }
        }
        return $this->render('biohim', compact('blood', 'pacient'));
    }

    public function actionBiohimdelete(){
        $blood = Biohim::findOne(['ID_BIOHIM'=>$_GET['ID_BIOHIM']]);
        $ID_PAC=$blood->ID_PAC;
        $blood->delete();
        return $this->redirect('index.php?r=client/analysis&ID_PAC='.$ID_PAC);
    }



    public function actionMocha(){
        if ($_GET['ID_MOCHA']!=NULL){
            $blood = MochaForm::findOne(['ID_MOCHA'=>$_GET['ID_MOCHA']]);

        }else{
            $blood=new MochaForm();
            $blood->ID_PAC=$_GET['ID_PAC'];
            $blood->DATE=date('d.m.Y');
        }

        $pacient=Pacient::findOne(['ID_PAC'=>$_GET['ID_PAC']]);

        if ( $blood->load(Yii::$app->request->post()) ){
            $blood->DATE=date('Y-m-d', strtotime($blood->DATE));
            if ($blood->save()){

                $ID_MOCHA=$blood->ID_MOCHA;
                return $this->redirect('index.php?r=client/mocha&ID_PAC='.$pacient->ID_PAC.'&ID_MOCHA='.$ID_MOCHA);
            }
        }
        return $this->render('mocha', compact('blood', 'pacient'));
    }

    public function actionMochadelete(){
        $blood = Mocha::findOne(['ID_MOCHA'=>$_GET['ID_MOCHA']]);
        $ID_PAC=$blood->ID_PAC;
        $blood->delete();
        return $this->redirect('index.php?r=client/analysis&ID_PAC='.$ID_PAC);
    }



    public function actionUzi(){
        if ($_GET['ID_UZI']!=NULL){
            $blood = UziForm::findOne(['ID_UZI'=>$_GET['ID_UZI']]);
        }else{
            $blood=new UziForm();
            $blood->ID_PAC=$_GET['ID_PAC'];
            $blood->DATE=date('d.m.Y');
        }

        $pacient=Pacient::findOne(['ID_PAC'=>$_GET['ID_PAC']]);

        if ( $blood->load(Yii::$app->request->post()) ){
            $blood->DATE=date('Y-m-d', strtotime($blood->DATE));
            if ($blood->save()){

                $ID_UZI=$blood->ID_UZI;
                return $this->redirect('index.php?r=client/uzi&ID_PAC='.$pacient->ID_PAC.'&ID_UZI='.$ID_UZI);
            }
        }
        return $this->render('uzi', compact('blood', 'pacient'));
    }

    public function actionUzidelete(){
        $blood = Uzi::findOne(['ID_UZI'=>$_GET['ID_UZI']]);
        $ID_PAC=$blood->ID_PAC;
        $blood->delete();
        return $this->redirect('index.php?r=client/analysis&ID_PAC='.$ID_PAC);
    }



public function actionOther(){
    if ($_GET['ID_OTHER']!=NULL){
        $blood = Other_islForm::findOne(['ID_OTHER'=>$_GET['ID_OTHER']]);
    }else{
        $blood=new Other_islForm();
        $blood->ID_PAC=$_GET['ID_PAC'];
        $blood->DATE=date('d.m.Y');
    }

    $pacient=Pacient::findOne(['ID_PAC'=>$_GET['ID_PAC']]);

    if ( $blood->load(Yii::$app->request->post()) ){
        $blood->DATE=date('Y-m-d', strtotime($blood->DATE));
        if ($blood->save()){
            $ID_OTHER=$blood->ID_OTHER;

            return $this->redirect('index.php?r=client/other&ID_PAC='.$pacient->ID_PAC.'&ID_OTHER='.$ID_OTHER);
        }
    }
    return $this->render('other', compact('blood', 'pacient'));
}

public function actionOtherdelete(){
    $blood = Other_isl::findOne(['ID_OTHER'=>$_GET['ID_OTHER']]);
    $ID_PAC=$blood->ID_PAC;
    $blood->delete();
    return $this->redirect('index.php?r=client/analysis&ID_PAC='.$ID_PAC);
}


public function actionCatalog(){
    $KattovProvider = new ActiveDataProvider([
        'query' => Kattov::find(),
        'pagination' => false,
    ]);

    $model=new TovarForm();
    if ( $model->load(Yii::$app->request->post()) ){

        if ($model->save()){

            return $this->refresh();
        }
    }
        return $this->render('catalog', compact('KattovProvider', 'model'));
}
    public function actionTovardelete(){
        $ID_TOV=$_GET['ID_TOV'];

        $model=TovarForm::findOne(['ID_TOV'=>$ID_TOV]);
        $model->delete();
        $this->redirect("index.php?r=client/catalog");


    }

    public function actionPrihod_tovara(){
        $PrihodProvider = new ActiveDataProvider([
            'query' => Prihod_tovara::find(),
            'pagination' => false,
        ]);
        $date=date('d.m.Y');
        return $this->render('prihod_tovara', compact('PrihodProvider', 'date'));
    }
    public function actionPrihod(){
        if ($_GET['ID_PRIHOD']!=NULL){
            $model=Prihod_tovara::findOne(['ID_PRIHOD'=>$_GET['ID_PRIHOD']]);

        }else{
            $model=new Prihod_tovaraForm();
            $model->DATE=date("d.m.Y");
        }

        if ( $model->load(Yii::$app->request->post()) ){
            $tovar=Kattov::findOne(['ID_TOV'=>$model->ID_TOV]);
            $tovar->KOL=$tovar->KOL+$model->KOL;
            $model->SUMM=$model->KOL*$model->PRICE;
            $model->DATE=date("Y-m-d", strtotime( $model->DATE));
            $tovar->save();

            if ($model->save()){


                return $this->redirect("index.php?r=client/prihod_tovara");
            }
        }
        return $this->render('prihod', compact('model'));
    }

    public function actionPrihoddelete(){
        $ID_PRIHOD=$_GET['ID_PRIHOD'];

        $model=Prihod_tovaraForm::findOne(['ID_PRIHOD'=>$ID_PRIHOD]);
        if ($model->ID_TOV) {
            $tovar = Kattov::findOne(['ID_TOV' => $model->ID_TOV]);
            $tovar->KOL = $tovar->KOL - $model->KOL;
            $tovar->save();
        }
        $model->delete();
        $this->redirect("index.php?r=client/prihod_tovara");


    }
    public function actionTovar(){
        if ($_GET['ID_TOV']!=NULL){
            $model=KattovForm::findOne(['ID_TOV'=>$_GET['ID_TOV']]);

        }else{
            $model=new KattovForm();
        }

        if ( $model->load(Yii::$app->request->post()) ){

            if ($model->save()){

                return $this->redirect("index.php?r=client/catalog");
            }
        }
        return $this->render('tovar', compact('model'));
    }

    public function actionSale(){
        $SaleProvider = new ActiveDataProvider([
            'query' => Sale::find(),
            'pagination' => [
                'pageSize' => 10,

            ],
        ]);
        if(!Yii::$app->request->get('page')){
            $SaleProvider->pagination->page = ceil($SaleProvider->getTotalCount() / $SaleProvider->pagination->pageSize) - 1;
        }
        return $this->render('sale', compact('SaleProvider'));
    }

    public function actionAddsale(){
        if ($_GET['ID_SALE']!=NULL){
            $model=SaleForm::findOne(['ID_SALE'=>$_GET['ID_SALE']]);

        }else{
            $model=new SaleForm();
            $model->DATE=date("d.m.Y");
            $model->SKIDKA=0;

        }

        if ( $model->load(Yii::$app->request->post()) ){
            $tovar=Kattov::findOne(['ID_TOV'=>$model->ID_TOV]);
            $tovar->KOL=$tovar->KOL-$model->KOL;

            $model->SUMM=$model->KOL*$tovar->PRICE;
            $model->DATE=date('Y-m-d', strtotime($model->DATE));

            $model->SUMM=$model->SUMM*((100-$model->SKIDKA)/100);
            $tovar->save();
            if ($model->save()){

                return $this->redirect("index.php?r=client/sale");
            }
        }
        return $this->render('addsale', compact('model'));
    }

    public function  actionSaledelete(){
        $model=Sale::findOne(['ID_SALE'=>$_GET['ID_SALE']]);
        $tovar=Kattov::findOne(['ID_TOV'=>$model->ID_TOV]);
        $tovar->KOL=$tovar->KOL+$model->KOL;
        $tovar->save();
        $model->delete();
        return $this->redirect("index.php?r=client/sale");
    }

    public function actionReport_ostatki_form(){

        Yii::setAlias('@reports', Yii::$app->basePath.'/отчеты');

        $spreadsheet=new Spreadsheet();
        $sheet=$spreadsheet->getActiveSheet();
        $tovar=Kattov::find()->where(['!=', 'KOL', 0])->all();
        $sheet->setCellValue('A1', 'ЗооДоктор');
        $sheet->setCellValue('D1', date("d.m.Y"));


        $sheet->setCellValue('A2', 'Отчет по остаткам товара');
        $titles=[
            [
                'name'=>'Наименование товара',
                'cell'=>'A',
            ],
            [
                'name'=>'Кол-во',
                'cell'=>'B',
            ],
            [
                'name'=>'Цена продажи',
                'cell'=>'C',
            ],



        ];
        for ($j = 0; $j < count($titles); $j++) {
            $string = $titles[$j]['name'];
            $cellLatter = $titles[$j]['cell'] . 4;
            $sheet->setCellValue($cellLatter, $string);
        }
        $activeRow=4;
        for($i=0;$i<count($tovar); $i++){
            $activeRow++;
            $cellA='A'.$activeRow;
            $cellB='B'.$activeRow;
            $cellC='C'.$activeRow;
            $sheet->setCellValue($cellA,$tovar[$i]->NAME);
            $sheet->setCellValue($cellB, $tovar[$i]->KOL);
            $sheet->setCellValue($cellC, $tovar[$i]->PRICE);
            $sheet->getColumnDimension('A')->setWidth(25);
            $sheet->getColumnDimension('C')->setWidth(14);
            $sheet->getStyle('A4:' . $cellC)
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        }

        $filename='/Отчет по остаткам товара от '.date('d.m.Y').'.xlsx';


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

    public function actionReport_prihod(){

        Yii::setAlias('@reports', Yii::$app->basePath.'/отчеты');

        $spreadsheet=new Spreadsheet();
        $sheet=$spreadsheet->getActiveSheet();
        $firstdate= ($_GET['FIRST_DATE']);
        $secondtdate= ($_GET['SECOND_DATE']);


        $firstdate =date("Y-m-d", strtotime($firstdate));
        $secondtdate =date("Y-m-d", strtotime($secondtdate));

        $prihod=Prihod_tovara::find()->where(['between', 'DATE', $firstdate, $secondtdate])->all();
        $sheet->setCellValue('A1', 'ЗооДоктор');
        $sheet->setCellValue('D1', date("d.m.Y"));


        $sheet->setCellValue('A2', 'Отчет по поставкам товара c '.date('d.m.Y', strtotime($firstdate)).' по '.date('d.m.Y', strtotime($secondtdate)));
        $titles=[
            [
                'name'=>'Дата',
                'cell'=>'A',
            ],
            [
                'name'=>'Наименование',
                'cell'=>'B',
            ],
            [
                'name'=>'Кол-во',
                'cell'=>'C',
            ],
            [
                'name'=>'Цена закупки',
                'cell'=>'D',
            ],
            [
                'name'=>'Сумма',
                'cell'=>'E',
            ]



        ];
        for ($j = 0; $j < count($titles); $j++) {
            $string = $titles[$j]['name'];
            $cellLatter = $titles[$j]['cell'] . 4;
            $sheet->setCellValue($cellLatter, $string);
        }
        $activeRow=4;
        $totalSumm=0;
        for($i=0;$i<count($prihod); $i++){
            $activeRow++;
            $tovar=Kattov::findOne(['ID_TOV'=>$prihod[$i]->ID_TOV]);
            $cellA='A'.$activeRow;
            $cellB='B'.$activeRow;
            $cellC='C'.$activeRow;
            $cellD='D'.$activeRow;
            $cellE='E'.$activeRow;
            $sheet->setCellValue($cellA, date('d.m.Y', strtotime($prihod[$i]->DATE)));
            $sheet->setCellValue($cellB,$tovar->NAME);
            $sheet->setCellValue($cellC, $prihod[$i]->KOL);
            $sheet->setCellValue($cellD, $prihod[$i]->PRICE);
            $sheet->setCellValue($cellE, $prihod[$i]->SUMM);
            $totalSumm=$totalSumm+$prihod[$i]->SUMM;
            $sheet->getColumnDimension('A')->setWidth(11);
            $sheet->getColumnDimension('B')->setWidth(40);
            $sheet->getColumnDimension('D')->setWidth(14);


        }
        $activeRow++;
        $cellA='A'.$activeRow;
        $cellD='D'.$activeRow;
        $cellE='E'.$activeRow;
        $sheet->setCellValue($cellE, $totalSumm);
        $spreadsheet->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A4:' . $cellE)
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->mergeCells($cellA . ':' . $cellD);

        $filename='/Отчет по поставкам товара c '.date('d.m.Y', strtotime($firstdate)).' по '.date('d.m.Y', strtotime($secondtdate)).'.xlsx';


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
    public function actionVisitdelete(){
        $visit=Vizit::findOne(['ID_VISIT'=>$_GET['ID_VISIT']]);
        $idClient=$visit->ID_CL;
        $fasility=Facility::find()->where(['ID_VISIT'=>$visit->ID_VISIT])->all();
        foreach ($fasility as $fac){
            $fac->delete();
        }
        $visit->delete();
        return $this->redirect('index.php?r=client/anketa&clientId='.$idClient);

    }



}

