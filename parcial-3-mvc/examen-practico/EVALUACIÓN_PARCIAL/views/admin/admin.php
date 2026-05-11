<?php
// Cristhian Alexis ortiz Valentin 3-3
    
    require_once("../admin/templateS/header.php");
?>


<div class="mx-auto p-5">
    <div class="card text-center">
    <div class="card-header">
        MENU
    </div>
    <div class="card-body">
        <h5 class="card-title"></h5>
            <div class="row mb-3">
                <div class="col">
                    <div class="card text-center">
                    <div class="card-header">
                        Crear torneo
                    </div>
                    <div class="card-body">

                    <a href="frmTorneos.php" class="btn btn-primary">
                        <img src="../img/torneo.png" alt="Crear un torneo." width="180"
                        height="180">
                    </a>
                    </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card text-center">
                    <div class="card-header">
                       Listado torneos
                    </div>
                    <div class="card-body">
                        <a href="readAllTorneos.php" class="btn btn-primary">
                            <img src="../img/torneo_lista.png" alt="Listar torneos." width="180"
                            height="180">
                        </a>
                    </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <div class="card text-center">
                    <div class="card-header">
                        Estadisticas
                    </div>
                    <div class="card-body">



                    </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card text-center">
                    <div class="card-header">
                        Anuncios
                    </div>
                    <div class="card-body">



                    </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="card-footer text-body-secondary">
        Configuración de torneos. Web App Basketball.
    </div>
    </div>
</div>


<?php
    require_once("../admin/templates/footer.php");
?>