<?php
require_once '../src/models/HomeModel.php';

class HomeController
{
    private $model;
 
    public function __construct() {

    require_once '../src/config/database.php';
    
        $this->model = new HomeModel();
        $this->model->connectWithDataBase($config);
    }
    public function home()
    {
        // Logika biznesowa dla strony głównej
        // Pobranie danych z modelu, przetworzenie ich
        // i przekazanie do widoku

        require '../src/views/home.php';
    }

    public function notFound()
    {
        // Obsługa błędu 404
        // Wyświetlanie odpowiedniego widoku
    }
}
?>