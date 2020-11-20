<?php
/*
 * @autor: Miguel Angel Aranda Garcia <miguela.aragar@educa.jcyl.es>
 * @version 1.0
 * @since 18/11/2020 1.0: creación de la pagina editar departamentos
 */
if (isset($_REQUEST["volver"])) {
    header('Location: ../mtoDepartamentos.php');
}
?>
<!DOCTYPE html>

<html>
    <head>
        <title>Editar Departamento Tema 4</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../webroot/css/styleEdit.css">
        <link rel="stylesheet" type="text/css" href="../webroot/css/style.css">
    </head>
    <body>
        <div id="cabecera">
            <div id="titulo">
                <h1>Editar Departamento</h1>
            </div>
            <div class="nav">
                <a href="../mtoDepartamentos.php" class="boton volver"><img class="icoBoton" src="../img/volver-flecha-izquierda.png"><span class="texto">Volver</span></a>
            </div>
        </div>
        <div id="contenedor"> 


            <?php
            require_once '../core/201008libreriaValidacion.php'; //incluimos la libreria de validación
            require_once "../config/conexionBDPDO.php"; //incluimos la conexión a la BD


            if (isset($_REQUEST['codDep']) || isset($_REQUEST['codDepartamento'])) {//si el codigo de departamento a sido enviado por get entra
                //consultamos la bd a ver si existe el departamento para evitar la inyeción de codigo
                //CONEXION BD PARA SELECT
                try {
                    $miDB = new PDO(DNS, USER, PASSWORD, CODIFICACION); //Creamos el objeto PDO
                    $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    if (isset($_REQUEST['codDep'])) {
                        $cod = $_REQUEST['codDep'];
                    } else if (isset($_REQUEST['edit'])) {
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
                        "nombDepartamento" => null,
                        "fechaBaja" => null,
                        "volumenNegocio" => null
                    ];
                    $aError = [//declaramos y inicializamos el array de los errores de los campos del formulario a null
                        "codDepartamento" => null,
                        "nombDepartamento" => null,
                        "fechaBaja" => null,
                        "volumenNegocio" => null
                    ];

                    $entradaOK = true; //declaramos y inicializamos la variable entradaObligatorioK, esta variable decidira si es correcta la entrada de datos del formulario

                    if (isset($_REQUEST['edit'])) {//si se pulsa el boton de editar comprobaremos que no hay errores
                        $aError["nombDepartamento"] = validacionFormularios::comprobarAlfabetico($_REQUEST["descripDepartamento"], 50, 3, 1); //Validamos la entrada del formulario para el campo nombDepartamento siendo este alfabetico de tamaño max 50 minimo 3
                        $aError["volumenNegocio"] = validacionFormularios::comprobarEntero($_REQUEST["volumenNegocio"], 20, 1, 0); //Validamos la entrada del formulario para el campo volumenNegocio siendo este numerico siendo este de tamaño max 20, minimo 1 y opcional
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
                        $aFormulario["nombDepartamento"] = $_REQUEST["descripDepartamento"];

                        if ($_REQUEST["volumenNegocio"] === "") {// si el campo $_REQUEST["volumenNegocio"] es "" asignamos el valor null, en mysql "" no es null y da error en la consulta
                            $aFormulario["volumenNegocio"] = null;
                        } else {
                            $aFormulario["volumenNegocio"] = $_REQUEST["volumenNegocio"];
                        }
                        //CONEXION BD PARA UPDATE
                        try {
                            $miDB = new PDO(DNS, USER, PASSWORD);
                            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $sqlUpdate = "UPDATE Departamento SET DescDepartamento=:nombDep, VolumenNegocio = :volumenNegocio WHERE CodDepartamento = :CodDepartamento"; //Creamos la consulta mysql con los parametros bind

                            $actualizarDepartamento = $miDB->prepare($sqlUpdate); //Preparamos la consulta
                            //bindeamos los campos para la consulta
                            $actualizarDepartamento->bindParam(":CodDepartamento", $aFormulario["codDepartamento"]);
                            $actualizarDepartamento->bindParam(":nombDep", $aFormulario["nombDepartamento"]);
                            $actualizarDepartamento->bindParam(":volumenNegocio", $aFormulario["volumenNegocio"]);
                            $actualizarDepartamento->execute(); //Ejecutamos la consulta preparada

                            echo "<div id='menError'><h3>El departamento " . $aFormulario["codDepartamento"] . " a editado el departamento correctamente</h3></div>";
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
                            } else if (isset($_REQUEST['edit'])) {
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
                                                    value="<?php echo $dep->CodDepartamento; ?>"><!-- Comprobamos si el campo codDepartamento a sido enviada, en cado positivo comprobamos si tiene error, en caso afirmativo borra el value del campo, en caso contrario deja el value se mantiene con el valor insertado correctamente y si el campo no a sido enviado el value se queda blanco-->                                
                                        </div>
                                        <div class="campos">
                                            <label class="labelTitle" for="descripDepartamento">Descripción del Departamento: </label>
                                            <input  class="inputText" type="text" name="descripDepartamento" 
                                                    value="<?php
                                                    if (isset($_REQUEST["descripDepartamento"])) {//si esta iniciado $_REQUEST entra
                                                        if (isset($aError["nombDepartamento"])) {//Si hay errores entra
                                                            echo $dep->DescDepartamento; //asignamos el valor que esta en la base de datos
                                                        } else {//si no hay error mantenemos el valor nuevo asignado
                                                            echo $_REQUEST["descripDepartamento"];
                                                        }
                                                    } else if (isset($_REQUEST['codDep'])) {//si no esta iniciado $_REQUEST y si $_get entra
                                                        echo $dep->DescDepartamento; //asignamos el valor que esta en la base de datos
                                                    }
                                                    ?>"><!-- Comprobamos si el campo nombDepartamento a sido enviada, en cado positivo comprobamos si tiene error, en caso afirmativo borra el value del campo, en caso contrario deja el value se mantiene con el valor insertado correctamente y si el campo no a sido enviado el value se queda blanco-->
                                            <?php echo isset($aError["nombDepartamento"]) ? "<span class='error'>" . "<br>" . $aError["nombDepartamento"] . "</span>" : null ?><!-- Comprobamos si el campo nombDepartamento tiene error en caso afirmativo muestra un mensaje de error, en caso contrario no h ace nada-->

                                        </div>
                                        <div class="campos">
                                            <label class="labelTitle" for="fechaBaja">Fecha de Baja </label>
                                            <input  class="inputText" type="date" name="fechaBaja" disabled
                                                    value="<?php echo $dep->FechaBaja; ?>">  
                                        </div>
                                        <div class="campos">
                                            <label class="labelTitle" for="volumenNegocio">Volumen de Negocio: </label>
                                            <input  class="inputText" type="text" name="volumenNegocio" 
                                                    value="<?php
                                                    if (isset($_REQUEST["volumenNegocio"])) {//si esta iniciado $_REQUEST entra
                                                        if (isset($aError["volumenNegocio"])) {//Si hay errores entra
                                                            echo $dep->VolumenNegocio; //asignamos el valor que esta en la base de datos
                                                        } else {//si no hay error mantenemos el valor nuevo asignado
                                                            echo $_REQUEST["volumenNegocio"];
                                                        }
                                                    } else if (isset($_REQUEST['codDep'])) {//si no esta iniciado $_REQUEST y si $_get entra
                                                        echo $dep->VolumenNegocio; //asignamos el valor que esta en la base de datos
                                                    }
                                                    ?>">   
                                            <?php echo isset($aError["volumenNegocio"]) ? "<span class='error'>" . $aError["volumenNegocio"] . "</span>" : null ?><!-- Comprobamos si el campo volumenNegocio tiene error en caso afirmativo muestra un mensaje de error, en caso contrario no h ace nada-->
                                        </div>
                                        <div class="botonSend">
                                            <input class="botonEnvio" type= "submit" value= "Editar" name= "edit">
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
