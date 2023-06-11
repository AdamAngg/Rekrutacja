<?php 
class HomeModel {
    private $database;
   
    private $dataAPI;

    public $dataCurrencies;
    
    public $dataConversions;

    //Inicjalizacja pobrania danych z API i wysłania ich do bazy danych
    public function __construct(){
       $this->fetchDataFromAPI();
       $this->passDataToDB();
    }
    //łączenie z baza danych
    private function connectWithDatabase(){
        require '../src/config/database.php';
        
        $this->database = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
        //wysyłanie błędu gdy połączenie napotka error
        if($this->database->connect_errno){
            $error = $this->database->connect_error;
            throw new Exception("An error occurred with Database connection: $error");
        }

    }
    //Funckja odpowiadająca za pobranie informacji z API 
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
    //wyciągniecie wszystkich rekordów z tabeli currencies i dodanie do zmiennej stanu klasy
    private function getDataFromDB(){
        $this->connectWithDatabase();
        $selectAllQuery = "SELECT * FROM currencies";
        $this->dataCurrencies = $this->database->query($selectAllQuery);
        $this->database->close();
    }
    //Wysyłanie informacji z API do bazydanych 
    private function passDataToDB() {
        $this->getDataFromDB();
        $this->connectWithDatabase(); 

       if($this->dataCurrencies->num_rows > 0) {
        foreach($this->dataAPI[0]['rates'] as $currency){

            $currencyName = $this->database->real_escape_string($currency['currency']);
            $currencyCode = $this->database->real_escape_string($currency['code']);
            $currencyMid = $currency['mid'];
        
            $singleRecordQuery = "SELECT * FROM currencies WHERE currency = '$currencyName' AND code = '$currencyCode'";
        
            $result = $this->database->query($singleRecordQuery);
            $databaseRecord = $result->fetch_assoc();
        //Sprawdzam czy w którymś z rekordów nastąpiła zmiana kursu jeżeli tak aktualizuje go
            if($databaseRecord['currency'] === $currencyName && $databaseRecord['mid'] !== $currencyMid){
                $updateSingleRecordQuery = "UPDATE currencies SET mid = '$currencyMid' WHERE id = ".$databaseRecord['id'];
                $updateResult = $this->database->query($updateSingleRecordQuery);

                if(!$updateResult) throw new Exception($this->database->error);
            }
        //Sprawdzm czy nie pojawiła się nowa waluta jeżeli tak dodaje tylko ją do bazy
            if($result->num_rows === 0 ){
                $insertSingleRecordQuery = "INSERT INTO currencies (currency, code, mid) VALUES ('$currencyName', '$currencyCode', '$currencyMid')";
                $insertResult = $this->database->query($insertSingleRecordQuery);

                if(!$insertResult) throw new Exception($this->database->error);
            }
        }
        //Jeżeli zapytanie zwróci mi pustą bazę dodaje wszystkie kursy z API 
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
    //Generuje tabele zależną od tabeli currencies która zostanie wyświetlona we view
    public function generateMarkUPTable() {
        $tableMarkUp = " ";
        if($this->dataCurrencies->num_rows > 0){
            $tableMarkUp .= "<table>";
            $tableMarkUp .=  "<tr><th>Currency</th><th>Code</th><th>Mid</th></tr>";
            foreach ($this->dataCurrencies as $row) {
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
    //Generuje tabele zależną od tabeli currencies która zostanie wyświetlona we view
    public function generateMarkUPTableLatestConversions() {
        $this->getLatestDataConversions();
        $tableMarkUp = " ";
        if($this->dataConversions->num_rows > 0){
            $tableMarkUp .= "<table>";
            $tableMarkUp .=  "<tr><th>Amount Converted</th><th>Mid</th><th>Code</th><th>Currency</th><th>Mid</th><th>Code</th><th>Currency</th></tr>";
            foreach ($this->dataConversions as $row) {
                $tableMarkUp .=  "<tr>";
                $tableMarkUp .=  "<td>" . $row['convertedAmount'] . "</td>";
                $tableMarkUp .=  "<td>" . $row['midFrom'] . "</td>";
                $tableMarkUp .=  "<td>" . $row['codeFrom'] . "</td>";
                $tableMarkUp .=  "<td>" . $row['currencyFrom'] . "</td>";
                $tableMarkUp .=  "<td>" . $row['midTo'] . "</td>";
                $tableMarkUp .=  "<td>" . $row['codeTo'] . "</td>";
                $tableMarkUp .=  "<td>" . $row['currencyTo'] . "</td>";
                $tableMarkUp .=  "</tr>";
            }
            $tableMarkUp .=  "</table>";
        } else {
            $tableMarkUp .=  "No data available.";
        }
        return $tableMarkUp;
        }
    //Dodaje do bazy danych szczegółowe informacje o przewalutowaniach
    public function addLatestConversions($idFrom, $idTo, $convertedAmount) {
       
        // zapytanie które wyszukuje dwa różne rekordy 
        // są inne w zależności od id które zostaje podane we select'cie
        // następnie zostają one dodane do innej tabeli z roszerzonymi informacjami
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
        //Wysłanie i obsługa wyjątku
            $result = $this->database->query($insertQuery);
            if(!$result) throw new Exception($this->database->error);
        
        $this->database->close();
    }
    // ściaganie wzystkich informacji z tablicy conversions
    public function getLatestDataConversions(){
        $selectQuery = "SELECT * FROM conversions";
        $this->connectWithDatabase();
        $this->dataConversions = $this->database->query($selectQuery);
        $this->database->close();
    }
    }
    
?>