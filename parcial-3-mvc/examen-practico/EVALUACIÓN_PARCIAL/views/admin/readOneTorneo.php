<?php
// Cristhian Alexis ortiz Valentin 3-3
    
    require_once("../admin/templates/header.php");
    require_once("../../controller/torneosController.php");
    
    $objTorneosController = new torneosController();
    $lstTorneo = $objTorneosController->readOneTorneo($_GET['id'])
?>



    <div class="mx-auto p-5">
        <div class="card">
            <div class="card-header">
                <span class="fa-brands fa-readme"></span> Informacin del torneo.
            </div>
            <div class="card-body">
                <form action="torneosInsert.php" method="post">
                    <div class="mb-3">
                        <label for="nombreTorneo" class="form-label">Nombre del torneo (ID: <?= $lstTorneo['id'] ?>)</label>
                        <input type="text" class="form-control" name="txtNombreTorneo" 
                        id="nombreTorneo" value="<?= $lstTorneo['nombreTorneo'] ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="organizador" class="form-label">organizador (nombre completo)
                        </label>
                        <input type="text" name="txtOrganizador" id="organizador"
                        class="form-control" value="<?= $lstTorneo['organizador'] ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="patrocinador" class="form-label">Patrocinadores</label>
                        <textarea name="txtPatrocinador" id="patrocinador" cols="30" rows="2"
                        class="form-control" readonly><?= $lstTorneo['patrocinadores'] ?></textarea>
                        <span id="patrocinador" class="form-text">
                            Atención: se puede separa con "," si hay más de un patrocinador.
                        </span>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="sede" class="form-label">SEDE</label>
                            <input type="text" name="txtSede" id="sede" class="form-control"
                            value="<?= $lstTorneo['sede'] ?>" readonly>
                        </div>
                        <div class="col mb-3">
                            <label for="categoria" class="form-label">CATEGORIA</label>
                            <input list="lstCategorias" name="txtCategoria" id="categoria" 
                            class="form-control" value="<?= $lstTorneo['categoria'] ?>" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="premio1" class="form-label">PREMIO 1ER. LUGAR</label>
                            <input type="text" name="txtPremio1" id="premio1" class="form-control"
                            value="<?= $lstTorneo['premio1'] ?>" readonly>
                        </div>
                        <div class="col mb-3">
                            <label for="premio2" class="form-label">PREMIO 2DO. LUGAR</label>
                            <input type="text" name="txtPremio2" id="premio2" class="form-control"
                            value="<?= $lstTorneo['premio2'] ?>" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="premio3" class="form-label">PREMIO 3ER. LUGAR</label>
                            <input type="text" name="txtPremio3" id="premio3" class="form-control"
                            value="<?= $lstTorneo['premio3'] ?>" readonly>
                        </div>
                        <div class="col mb-3">
                            <label for="premio" class="form-label">OTRO PREMIO </label>
                            <input type="text" name="txtOtropremio" id="otroPremio" class="form-control"
                            value="<?= $lstTorneo['otroPremio'] ?>" readonly>
                        </div>
                    </div>
                
                    <div class="row">
                        <div class="col mb-3">
                            <label for="usuarop" class="form-label">USUARIO</label>
                            <input type="text" name="txtUsuario" id="usuario" class="form-control"
                            value="<?= $lstTorneo['usuario'] ?>" readonly>
                        </div>
                        <div class="col mb-3">
                            <label for="contrasena" class="form-label">CONTRASEÑA</label>
                            <input type="text" name="txtContrasena" id="contrasena" class="form-control"
                            value="<?= $lstTorneo['contrasena'] ?>" readonly>
                        </div>
                    </div>
                    <div class="colo-12">
                        <a href="readAllTorneos.php" class="btn btn-success">REGRESAR</a>
                    </div>
                </form>
            </div>
            <div class="card-footer text-body-secondary">
                DETALLES
            </div>
        </div>
    </div>




<?php
    require_once("../admin/templates/footer.php");
?>