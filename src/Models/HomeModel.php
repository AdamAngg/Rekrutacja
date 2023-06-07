<?php 
class HomeModel {
    private $dataBase;
    //łączenie z baza danych
    public function connectWithDataBase($config){
        $this->dataBase = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);

        if($this->dataBase->connect_errno){
            die('Nie udało się połączyć z bazą danych');
        }

    }
    public function fetchDataFromAPI() {
        $url =  "http://api.nbp.pl/api/exchangerates/tables/a";
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        if($response !== false) {
            $data = json_decode($response,true);
            
            return $data;
        } else {
            echo "U cant fetch here";
            return null;
        }
      
    }
}
?>