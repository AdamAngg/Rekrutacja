<?php

require "../src/controllers/HomeController.php";

// Tworzenie kontrolera
$controller = new HomeController();

// Wywołanie odpowiedniej metody w zależności od żądania
if (isset($_GET["action"])) {
    $action = $_GET["action"];

    switch ($action) {
        case "home":
            $controller->home();
            break;
        // Dodaj inne przypadki dla innych akcji
        default:
            $controller->notFound();
            break;
    }
} else {
    $controller->home();
}
?>

