<?php


class HomeController
{
    private $model;
 
    public function __construct() {
   
    }
    public function conversion($amount, $currencyFrom, $currencyTo) {
        $currencyFromInfo = explode('|',$currencyFrom);
        $midFrom = $currencyFromInfo[0];
        $idFrom = $currencyFromInfo[1];

        $currencyToInfo = explode('|',$currencyTo);
        $midTo = $currencyToInfo[0];
        $idTo = $currencyToInfo[1];
        
        if(!(filter_var($amount,FILTER_VALIDATE_FLOAT))){
            echo "An error ocured, added value is not a number. Please re-enter";
            return 0;
        }
        $convertedAmount = round(($amount/$midTo)*$midFrom,2);
        if($amount !== ""){
            $this->model->addLatestConversions($idFrom, $idTo, $convertedAmount);
        }
        
        
    }
    public function home()
    {
        require_once '../src/models/HomeModel.php';
       
        
        $this->model = new HomeModel();
        
        $tableMarkUp = $this->model->generateMarkUPTable();
        $currencies = $this->model->dataDB;

        if (isset($_POST['amount'])) {
            $this->conversion($_POST['amount'],$_POST['currencyFrom'],$_POST['currencyTo']);
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