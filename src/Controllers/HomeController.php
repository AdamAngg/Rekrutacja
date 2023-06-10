<?php


class HomeController
{
    private $model;
 
    public function __construct() {
   
    }
    public function home()
    {
        require_once '../src/models/HomeModel.php';
       
        
        $this->model = new HomeModel();
        
        $this->model->passDataToDB();
        $this->model->generateTable();

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