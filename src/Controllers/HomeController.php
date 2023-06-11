<?php


class HomeController
{
    private $model;
     
    public function __construct() {
    
    }
    public function conversion($amount, $currencyFrom, $currencyTo) {
        $currencyFromInfo = explode('|',$currencyFrom);
        $midFrom = $currencyFromInfo[0];
        isset($currencyFromInfo[1]) ? $idFrom = $currencyFromInfo[1] : $idFrom = "";
        

        $currencyToInfo = explode('|',$currencyTo);
        $midTo = $currencyToInfo[0];
        isset($currencyTo[1])? $idTo = $currencyToInfo[1] : $idTo = "";
       
        
        $digitArray = [$amount,$midFrom, $idFrom, $midTo, $idTo];
        foreach($digitArray as $digit){
        if(!(filter_var($digit,FILTER_VALIDATE_FLOAT))){
            throw new Exception("added value is not a number. Please re-enter. Wrong value: ".$digit);
            break; 
        }}

        $convertedAmount = round(($amount/$midTo)*$midFrom,2);
        if($amount !== ""){
            $this->model->addLatestConversions($idFrom, $idTo, $convertedAmount);
        }
        
        
    }
    public function home(){
        require_once '../src/models/HomeModel.php';
        $error = "";

    try{
        $this->model = new HomeModel();  
        $tableMarkUp = $this->model->generateMarkUPTable();
        $currencies = $this->model->dataDB;  

        if (isset($_POST['amount'])) {
            $this->conversion($_POST['amount'],$_POST['currencyFrom'],$_POST['currencyTo']);
        }
    } catch (Exception $e){
           $error = "An error occured: ".$e->getMessage();
    }  
        require '../src/views/home.php';
    }

    public function notFound()
    {
        // Obsługa błędu 404
        // Wyświetlanie odpowiedniego widoku
    }
}
?>