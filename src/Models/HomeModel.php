<?php 
class HomeModel {
    private $database;
   
    private $dataAPI;

    public $dataDB;

    //łączenie z baza danych
    public function __construct(){
       $this->fetchDataFromAPI();
       $this->passDataToDB();
    }

    private function connectWithDatabase(){
        require '../src/config/database.php';
        
        $this->database = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
    
        if($this->database->connect_errno){
            $error = $this->database->connect_error;
            throw new Exception("An error occurred with Database connection: $error");
        }

    }
    private function fetchDataFromAPI() {
        $url =  "http://api.nbp.pl/api/exchangerates/tables/a";
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
       
        if($response !== false ) {
            $this->dataAPI = json_decode($response,true);   
            curl_close($curl);     
            return $this->dataAPI;
        } else {
            $error_code = curl_errno($curl);
            curl_close($curl);
            throw new Exception("Api error".$error_code);  
        }
    }
    private function passDataToDB() {
    
    $this->connectWithDatabase();
    $selectAllQuery = "SELECT * FROM currencies";
    $this->dataDB = $this->database->query($selectAllQuery);

       if($this->dataDB->num_rows > 0) {
        foreach($this->dataAPI[0]['rates'] as $currency){

            $currencyName = $this->database->real_escape_string($currency['currency']);
            $currencyCode = $this->database->real_escape_string($currency['code']);
            $currencyMid = $currency['mid'];
        
            $singleRecordQuery = "SELECT * FROM currencies WHERE currency = '$currencyName' AND code = '$currencyCode'";
        
            $result = $this->database->query($singleRecordQuery);
            $databaseRecord = $result->fetch_assoc();
        
            if($databaseRecord['currency'] === $currencyName && $databaseRecord['mid'] !== $currencyMid){
                $updateSingleRecordQuery = "UPDATE currencies SET mid = '$currencyMid' WHERE id = ".$databaseRecord['id'];
                $updateResult = $this->database->query($updateSingleRecordQuery);

                if(!$updateResult) throw new Exception($this->database->error);
            }
            if($result->num_rows === 0 ){
                $insertSingleRecordQuery = "INSERT INTO currencies (currency, code, mid) VALUES ('$currencyName', '$currencyCode', '$currencyMid')";
                $insertResult = $this->database->query($insertSingleRecordQuery);

                if(!$insertResult) throw new Exception($this->database->error);
            }
        }
    } else {

        foreach($this->dataAPI[0]['rates'] as $currency) {

            $currencyName = $this->database->real_escape_string($currency['currency']);
            $currencyCode = $this->database->real_escape_string($currency['code']);
            $currencyMid = $currency['mid'];

            $insertQuery = "INSERT INTO currencies (currency, code, mid) VALUES ('$currencyName', '$currencyCode', '$currencyMid')";

            $result = $this->database->query($insertQuery);
            if(!$result) throw new Exception($this->database->error);
           
        }
        $this->database->close();
       }
      
    }
    public function generateMarkUPTable() {
        $tableMarkUp = '';
        if($this->dataDB->num_rows > 0){
            $tableMarkUp .= "<table>";
            $tableMarkUp .=  "<tr><th>Currency</th><th>Code</th><th>Mid</th></tr>";
            foreach ($this->dataDB as $row) {
                $tableMarkUp .=  "<tr>";
                $tableMarkUp .=  "<td>" . $row['currency'] . "</td>";
                $tableMarkUp .=  "<td>" . $row['code'] . "</td>";
                $tableMarkUp .=  "<td>" . $row['mid'] . "</td>";
                $tableMarkUp .=  "</tr>";
            }
            $tableMarkUp .=  "</table>";
        } else {
            $tableMarkUp .=  "No data available.";
        }
        return $tableMarkUp;
        }
    
    public function addLatestConversions($idFrom, $idTo, $convertedAmount) {
       
        
        $insertQuery = " INSERT INTO conversions (convertedAmount, midFrom, codeFrom, currencyFrom, midTo, codeTo, currencyTo)
        SELECT
            '$convertedAmount' AS convertedAmount,
            (SELECT mid FROM currencies WHERE id = '$idFrom') AS midFrom,
            (SELECT code FROM currencies WHERE id = '$idFrom') AS codeFrom,
            (SELECT currency FROM currencies WHERE id = '$idFrom') AS currencyFrom,
            (SELECT mid FROM currencies WHERE id = '$idTo') AS midTo,
            (SELECT code FROM currencies WHERE id = '$idTo') AS codeTo,
            (SELECT currency FROM currencies WHERE id = '$idTo') AS currencyTo;
    ";
       
        
            $this->connectWithDatabase();
            $result = $this->database->query($insertQuery);
            if(!$result) throw new Exception($this->database->error);
        
        $this->database->close();
    }
    }
    
?>