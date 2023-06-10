<?php


class HomeController
{
    private $model;
 
    public function __construct() {
   
    }
    public function conversion($amount, $currencyFrom, $currencyTo) {
        if(filter_var($amount,FILTER_VALIDATE_FLOAT) && filter_var($currencyFrom, $currencyTo,FILTER_VALIDATE_FLOAT)){
            return 0;
        }
        $convertedAmount = round(($amount/$currencyTo)*$currencyFrom,2);
        
        
    }
    public function home()
    {
        require_once '../src/models/HomeModel.php';
       
        
        $this->model = new HomeModel();
        
        $tableMarkUp = $this->model->generateTable();
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