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
}
?>