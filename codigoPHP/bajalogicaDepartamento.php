<?php
/*
 * @autor: Miguel Angel Aranda Garcia <miguela.aragar@educa.jcyl.es>
 * @version 1.0
 * @since 18/11/2020 1.0: creación de la pagina bajaLogica de departamentos
 */
if (isset($_REQUEST["volver"])) {
    header('Location: ../mtoDepartamentos.php');
    exit;
}
?>
<!DOCTYPE html>

<html>
    <head>
        <title>Baja Logica Departamento Tema 4</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../webroot/css/styleEdit.css">
        <link rel="stylesheet" type="text/css" href="../webroot/css/style.css">
    </head>
    <body>
        <div id="cabecera">
            <div id="titulo">
                <h1>Baja logica de Departamento</h1>
            </div>
            <div class="nav">
                <a href="../mtoDepartamentos.php" class="boton volver"><img class="icoBoton" src="../img/volver-flecha-izquierda.png"><span class="texto">Volver</span></a>
            </div>
        </div>
        <div id="contenedor"> 


            <?php
            require_once '../core/201109libreriaValidacion.php'; //incluimos la libreria de validación
            require_once "../config/conexionBDPDO.php"; //incluimos la conexión a la BD


            if (isset($_REQUEST['codDep']) || isset($_REQUEST['codDepartamento'])) {//si el codigo de departamento a sido enviado por get entra
                //consultamos la bd a ver si existe el departamento para evitar la inyeción de codigo
                //CONEXION BD PARA SELECT
                try {
                    $miDB = new PDO(DNS, USER, PASSWORD, CODIFICACION); //Creamos el objeto PDO
                    $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    if (isset($_REQUEST['codDep'])) {
                        $cod = $_REQUEST['codDep'];
                    } else if (isset($_REQUEST['baja'])) {
                        $cod = $_REQUEST["codDepartamento"];
                    }

                    $sql = "SELECT CodDepartamento FROM Departamento where CodDepartamento=:codDepartamento"; //Creamos la consulta mysql con los parametros bind

                    $consultaDepartamento = $miDB->prepare($sql); //Preparamos la consulta
                    $consultaDepartamento->bindParam(":codDepartamento", $cod); //Declaramos el parametro bind
                    $consultaDepartamento->execute(); //Ejecutamos la consulta preparada
                    $existe = $consultaDepartamento->rowCount();
                } catch (PDOException $miExcepcionPDO) {//declaración de excepcionesPDO
                    echo "<p class='error'>Error " . $miExcepcionPDO->getMessage() . "</p>";
                    echo "<p class='error'>Cod.Error " . $miExcepcionPDO->getCode() . "</p>";
                    echo "<h2 class='error'>Error en la conexión con la base de datos</h2>";
                } finally {
                    unset($miDB); //cerramos la conexión
                }
                if ($existe == 1) { //si existe el departamento iniciamos las variables
                    $aFormulario = [//declaramos y inicializamos el array de los campos del formulario a null
                        "codDepartamento" => null,
                        "fechaBaja" => null
                    ];
                    $aError = [//declaramos y inicializamos el array de los errores de los campos del formulario a null
                        "fechaBaja" => null
                    ];

                    $entradaOK = true; //declaramos y inicializamos la variable entradaObligatorioK, esta variable decidira si es correcta la entrada de datos del formulario
                    $date = new DateTime();
                    /*$fechaAhora2= $date;
                    var_dump($fechaAhora2->format('d/m/Y'));
                    $fechaAhora = "01/01/2021";
                    var_dump($fechaAhora);*/
                    if (isset($_REQUEST['baja'])) {//si se pulsa el boton de bajaar comprobaremos que no hay errores
                        $aError["fechaBaja"] = validacionFormularios::validarFecha($_REQUEST["fechaBaja"], $date->format("Y/m/d") , "01/01/1900", 1); //Validamos la entrada del formulario para el campo nombDepartamento siendo este alfabetico de tamaño max 50 minimo 3

                        foreach ($aError as $errores => $value) { //Recorremos todos los campos del array $aError
                            if ($value != null) { //Si algun campo de $aError tiene un valor diferente null entonces entra
                                $entradaOK = false; // asignamos el valor a false en caso de que entre
                            }
                        }
                    } else { //si no se a pulsado el boton entra 
                        $entradaOK = false;
                    }
                    if ($entradaOK) { //en caso de que no haya ningun error entra 
                        //ASIGNACIÓN Y ULTIMA COMPROBACIÓN
                        $aFormulario["codDepartamento"] = $_REQUEST["codDepartamento"];
                        $aFormulario["fechaBaja"] = $_REQUEST["fechaBaja"];


                        //CONEXION BD PARA UPDATE
                        try {
                            $miDB = new PDO(DNS, USER, PASSWORD);
                            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $sqlUpdate = "UPDATE Departamento SET fechaBaja=:fechaBaja WHERE CodDepartamento = :CodDepartamento"; //Creamos la consulta mysql con los parametros bind

                            $actualizarDepartamento = $miDB->prepare($sqlUpdate); //Preparamos la consulta
                            //bindeamos los campos para la consulta
                            $actualizarDepartamento->bindParam(":CodDepartamento", $aFormulario["codDepartamento"]);
                            $actualizarDepartamento->bindParam(":fechaBaja", $aFormulario["fechaBaja"]);
                            $actualizarDepartamento->execute(); //Ejecutamos la consulta preparada

                            echo "<div id='menError'><h3>El departamento " . $aFormulario["codDepartamento"] . " se le ha asignado correctamente la baja logica</h3></div>";
                        } catch (PDOException $miExcepcionPDO) {//declaración de excepcionesPDO
                            echo "<div class='box'>";
                            echo "<p class='error'>Error " . $miExcepcionPDO->getMessage() . "</p>";
                            echo "<p class='error'>Cod.Error " . $miExcepcionPDO->getCode() . "</p>";
                            echo "<h2 class='error'>Error en la conexión con la base de datos</h2>";
                            echo "</div>";
                        } finally {
                            unset($miDB); //cerramos la conexión
                        }
                    } else {
                        try {
                            $miDB = new PDO(DNS, USER, PASSWORD, CODIFICACION);
                            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            if (isset($_REQUEST['codDep'])) {
                                $cod = $_REQUEST['codDep'];
                            } else if (isset($_REQUEST['baja'])) {
                                $cod = $_REQUEST["codDepartamento"];
                            }
                            $consultarDep = "SELECT * FROM Departamento WHERE CodDepartamento=:CodDepartamento"; //Creamos la consulta mysq
                            $consultaDepartamento = $miDB->prepare($consultarDep); //Preparamos la consulta
                            $consultaDepartamento->bindParam(":CodDepartamento", $cod); //Declaramos el parametro bind
                            $consultaDepartamento->execute(); //Ejecutamos la consulta preparada
                            $consultaDepartamento->rowCount();
                            while ($dep = $consultaDepartamento->fetchObject()) {
                                ?>
                                <div id="form">
                                    <form class="descript" action= "<?php echo $_SERVER["PHP_SELF"] ?>" method= "POST">
                                        <div class="campos">
                                            <label class="labelTitle" for="codDepartamento">Codigo del Departamento: </label>
                                            <input  class="inputText" type="text" name="codDepartamento" readonly="readonly"
                                                    value="<?php echo $dep->CodDepartamento; ?>">          
                                        </div>
                                        <div class="campos">
                                            <label class="labelTitle" for="descripDepartamento">Descripción del Departamento: </label>
                                            <input  class="inputText" type="text" name="descripDepartamento" disabled
                                                    value="<?php echo $dep->DescDepartamento; ?>">
                                        </div>
                                        <div class="campos">
                                            <label class="labelTitle" for="fechaBaja">Fecha de Baja </label>
                                            <input  class="inputText" type="date" name="fechaBaja" 
                                                    value="<?php echo $dep->FechaBaja; ?>"> 
                                            <?php echo isset($aError["fechaBaja"]) ? "<span class='error'>" . $aError["fechaBaja"] . "</span>" : null ?><!-- Comprobamos si el campo fechaBaja tiene error en caso afirmativo muestra un mensaje de error, en caso contrario no h ace nada-->

                                        </div>
                                        <div class="campos">
                                            <label class="labelTitle" for="volumenNegocio">Volumen de Negocio: </label>
                                            <input  class="inputText" type="text" name="volumenNegocio" disabled
                                                    value="<?php echo $dep->VolumenNegocio; ?>"> 
                                        </div>
                                        <div class="botonSend">
                                            <input class="botonEnvio" type= "submit" value= "Baja" name= "baja">
                                            <input class="botonEnvio" type= "submit" value= "Cancelar" name= "volver">
                                        </div>
                                    </form>

                                </div>
                                <?php
                            }
                        } catch (PDOException $miExcepcionPDO) {
                            echo "<div class='contenedorError'>";
                            echo "<div class='box'>";
                            echo "<p class='error'>Error " . $miExcepcionPDO->getMessage() . "</p>";
                            echo "<p class='error'>Cod.Error " . $miExcepcionPDO->getCode() . "</p>";
                            echo "<h2 class='error'>Error en la conexión con la base de datos</h2>";
                            echo "</div>";
                        } finally {
                            unset($miConexion); //cerramos la conexión
                        }
                    }
                } else {
                    ?>
                    <div id="menError">
                        <p>No existe ningun departamento con ese codigo de departamento</p>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div id="menError">
                    <p>No se a elegido ningun departamento</p>
                </div>
                <?php
            }
            ?>
        </div>
        <footer>
            <div class="pie">
                <a href="../../index.html" class="nombre">Miguel Ángel Aranda García</a>
                <a href="https://github.com/MiguelAranda-Sauces" class="git" ><img class="git" src="../img/git.png"></a>
            </div>

        </footer>
    </body>
</html>
