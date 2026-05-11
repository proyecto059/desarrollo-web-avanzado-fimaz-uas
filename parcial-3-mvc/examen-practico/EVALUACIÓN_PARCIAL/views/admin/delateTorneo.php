<?php
// Cristhian Alexis ortiz Valentin 3-3
    
    require_once("../../controller/torneosController.php");
    $objTorneosController = new torneosController();

    $objTorneosController->delete($_GET['id']);
    
?>