<?php 
class HomeModel {
    private $database;
   
    private $dataAPI;

    private $dataDB;

    //łączenie z baza danych
    public function __construct(){
       $this->fetchDataFromAPI();
    }

    private function connectWithDatabase(){
        require_once '../src/config/database.php';
        $this->database = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);

        if($this->database->connect_errno){
            $error = $this->database->connect_errno;
            die("An error occurred with Database connection $error");
        }

    }
    private function fetchDataFromAPI() {
        $url =  "http://api.nbp.pl/api/exchangerates/tables/a";
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
       
        if($response !== false ) {
            $this->dataAPI = json_decode($response,true);        
            return $this->dataAPI;
        } else {
            $error_code = curl_errno($curl);
            echo "An error occurred with API connection  $error_code";
            return null;
           
        }
      curl_close($curl);
    }
    public function passDataToDB() {
    
    $this->connectWithDatabase();
    $selectAllQuery = "SELECT * FROM currencies";
    $this->dataDB = $this->database->query($selectAllQuery);

       if($this->dataDB->num_rows > 0) {
        foreach($this->dataAPI[0]['rates'] as $currency){

            $currencyName = $this->database->real_escape_string($currency['currency']);
            $currencyCode = $this->database->real_escape_string($currency['code']);
            $currencyMid = $currency['mid'];
        
            $singleRecordQuery = "SELECT * FROM currencies WHERE currency = '$currencyName' AND code = '$currencyCode'";
        
            try {
                $result = $this->database->query($singleRecordQuery);
                $databaseRecord = $result->fetch_assoc();
        
                if($databaseRecord['currency'] === $currencyName && $databaseRecord['mid'] !== $currencyMid){
                    $updateSingleRecordQuery = "UPDATE currencies SET mid = '$currencyMid' WHERE id = ".$databaseRecord['id'];
                    $this->database->query($updateSingleRecordQuery);
                }
                if($result->num_rows === 0 ){
                    $insertSingleRecordQuery = "INSERT INTO currencies (currency, code, mid) VALUES ('$currencyName', '$currencyCode', '$currencyMid')";
                    $this->database->query($insertSingleRecordQuery);
                }
            } catch (Exception $e) {
                echo "An error occured with database query: " .$e->getMessage();
            }
        }
    } else {

        foreach($this->dataAPI[0]['rates'] as $currency) {

            $currencyName = $this->database->real_escape_string($currency['currency']);
            $currencyCode = $this->database->real_escape_string($currency['code']);
            $currencyMid = $currency['mid'];

            $insertQuery = "INSERT INTO currencies (currency, code, mid) VALUES ('$currencyName', '$currencyCode', '$currencyMid')";

            try{
               $result = $this->database->query($insertQuery);
            } catch (Exception $e) {
                echo "An error occured with database query: ". $e->getMessage();
            }  
        }
       }
        $this->database->close();
    }

   
    }

?>