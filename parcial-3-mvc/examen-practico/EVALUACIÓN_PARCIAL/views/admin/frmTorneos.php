<?php
    //Cristhian Alexis ortiz Valentin 3-3

    require_once("../admin/templates/header.php");



?>
<div class="mx-auto p-5">
    <div class="card">
        <div class="card-header">
            <span class="fa-solid fa-trophy"></span> Capturar informacion del torneo.
        </div>
        <div class="card-body">
            <form action="torneosInsert.php" method="post">
                <div class="mb-3">
                    <label for="nombreTorneo" class="form-label">Nombre del torneo</label>
                    <input type="text" class="form-control" name="txtNombreTorneo" 
                    id="nombreTorneo">
                </div>
                <div class="mb-3">
                    <label for="organizador" class="form-label">Organizador</label>
                    <input type="text" name="txtOrganizador" id="organizador"
                    class="form-control">
                </div>
                <div class="mb-3">
                    <label for="patrocinador" class="form-label">Patrocinador</label>
                    <textarea name="txtPatrocinador" id="patrocinador" cols="30" rows="2"
                    class="form-control"></textarea>
                    <span id="patrocinador" class="form-text">
                        Atención: se puede separar con "," si hay más de un patrocinador.
                    </span>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="sede" class="form-label">SEDE</label>
                        <input type="text" name="txtSede" id="sede" class="form-control">
                    </div>
                    <div class="col mb-3">
                        <label for="categoria" class="form-label">CATEGORIA</label>
                        <input list="lstCategorias" name="txtCategoria" id="categoria" 
                        class="form-control">
                        <datalist id="lstCategorias">
                            <option value="1era. fuerza">
                            <option value="2da. fuerza">
                            <option value="Veteranos">
                            <option value="Libre">
                            <option value="Juvenil">
                            <option value="Empresarial">
                            <option value="Infantil">
                            <option value="Minibasket">
                        </datalist>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="premio1" class="form-label">PREMIO 1ER. LUGAR</label>
                        <input type="text" name="txtPremio1" id="premio1" class="form-control">
                    </div>
                    <div class="col mb-3">
                        <label for="premio2" class="form-label">PREMIO 2DO. LUGAR</label>
                        <input type="text" name="txtPremio2" id="premio2" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="premio3" class="form-label">PREMIO 3ER. LUGAR</label>
                        <input type="text" name="txtPremio3" id="premio3" class="form-control">
                    </div>
                    <div class="col mb-3">
                        <label for="premio" class="form-label">OTRO PREMIO (CAMPEÓN
                        CANASTERO)</label>
                        <input type="text" name="txtOtroPremio" id="otroPremio" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="usuario" class="form-label">USUARIO</label>
                        <input type="text" name="txtUsuario" id="usuario" class="form-control">
                    </div>
                    <div class="col mb-3">
                        <label for="contrasena" class="form-label">CONTRASEÑA</label>
                        <input type="password" name="txtContrasena" id="contrasena" class="form-control">
                    </div>
                </div>
                <div class="col mb-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="admin.php" class="btn btn-danger">Cancelar</a>
                </div>
            </form>
        </div>
        <div class="card-footer text-body-secondary">
            FORMULARIO PARA REGISTRAR TORNEOS.
        </div>
    </div>
</div>



<?php
    require_once("../admin/templates/footer.php");
?>