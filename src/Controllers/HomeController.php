<?php


class HomeController
{
    private $model;
     
    public function __construct() {
        
    }
    public function conversion($amount, $currencyFrom, $currencyTo) {
        //tworze tablice z dwoma zmiennymi 
        $currencyFromInfo = explode('|',$currencyFrom);
        $midFrom = $currencyFromInfo[0];
        //sprawdzam czy dana zmienna istnieje inaczej nie zapisuje do niej danych
        isset($currencyFromInfo[1]) ? $idFrom = $currencyFromInfo[1] : $idFrom = "";
        
        
        $currencyToInfo = explode('|',$currencyTo);
        $midTo = $currencyToInfo[0];
        isset($currencyTo[1])? $idTo = $currencyToInfo[1] : $idTo = "";
       
        //sprawdzam dla zmiennej czy jest zmienną jeżeli nie wysyłam wyjątek
        $digitArray = [$amount,$midFrom, $idFrom, $midTo, $idTo];
        foreach($digitArray as $digit){
        if(!(filter_var($digit,FILTER_VALIDATE_FLOAT)) && $amount !== ""){
            throw new Exception("added value is not a number. Please re-enter. Wrong value: ".$digit);
            break; 
        }}
        
        if($amount !== ""){ 
            //przewalutowana wartość
            $convertedAmount = round(($amount/$midTo)*$midFrom,2);
            $this->model->addLatestConversions($idFrom, $idTo, $convertedAmount);
        } else {  
        throw new Exception("Fill missing field");
        }
        
        
    }
    public function home(){
        require_once 'src\Models\HomeModel.php';
        $error = "";

    try{
        //Inicjalizacja funckji na stronie głównej
        $this->model = new HomeModel();  
        $tableMarkUp = $this->model->generateMarkUPTable();
        $currencies = $this->model->dataCurrencies;
        $tableMarkUpLatestConversion = $this->model->generateMarkUPTableLatestConversions();  
        
        if (isset($_POST['amount'])) {
            $this->conversion($_POST['amount'],$_POST['currencyFrom'],$_POST['currencyTo']);
            header('Location: /adrespect/');
            exit();
        }
    //obsługa wyjątków 
    } catch (Exception $e){
           $error = "An error occured: ".$e->getMessage();
    }  
        require 'src\Views\home.php';
    }

    public function notFound()
    {
        // Obsługa błędu 404
        // Wyświetlanie odpowiedniego widoku
    }
}
?>