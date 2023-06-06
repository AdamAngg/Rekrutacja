<?php

class HomeController
{
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