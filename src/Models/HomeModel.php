<?php 
class HomeModel {
    private $database;
    
    private $data;
    //łączenie z baza danych
    public function __construct(){
       
    }

    private function connectWithDataBase(){
        require_once '../src/config/database.php';
        $this->database = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);

        if($this->database->connect_errno){
            $error = $this->database->connect_errno;
            die("An error occurred with Database connection $error");
        }

    }
    public function fetchDataFromAPI() {
        $url =  "http://api.nbp.pl/api/exchangerates/tables/a";
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
       
        if($response !== false ) {
            $this->data = json_decode($response,true);        
            return $this->data;
        } else {
            $error_code = curl_errno($curl);
            echo "An error occurred with API connection  $error_code";
            return null;
           
        }
      curl_close($curl);
    }
    public function passDataToDB() {
    
    $this->connectWithDataBase();
       if($this->database === null) echo "An error occurred with sending data"; 
       
       $deleteQuery = "DELETE FROM currencies";
       $deleteResult = $this->database->query($deleteQuery);


        foreach($this->data[0]['rates'] as $currency) {

            $currencyName = $this->database->real_escape_string($currency['currency']);
            $currencyCode = $this->database->real_escape_string($currency['code']);
            $currencyMid = $currency['mid'];

            $query = "INSERT INTO currencies (currency, code, mid) VALUES ('$currencyName', '$currencyCode', '$currencyMid')";

           $result = $this->database->query($query);
            if(!$result) echo "An error occurred with query ".$this->database->error;
        }
        $this->database->close();
    }
}
?>