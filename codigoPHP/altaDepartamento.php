<?php
/*
 * @autor: Miguel Angel Aranda Garcia <miguela.aragar@educa.jcyl.es>
 * @version 1.0
 * @since 18/11/2020 1.0: creación de la pagina alta departamentos
 */
if (isset($_REQUEST["volver"])) {
    header('Location: ../mtoDepartamentos.php');
    exit;
}
?>
?>
<!DOCTYPE html>

<html>
    <head>
        <title>Alta Departamento Tema 4</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../webroot/css/styleAdd.css">
        <link rel="stylesheet" type="text/css" href="../webroot/css/style.css">
    </head>
    <body>
        <div id="cabecera">
            <div id="titulo">
                <h1>Alta de Departamento</h1>
            </div>
            <div class="nav">
                <a href="../mtoDepartamentos.php" class="boton volver"><img class="icoBoton" src="../img/volver-flecha-izquierda.png"><span class="texto">Volver</span></a>
            </div>
        </div>
        <div id="contenedor"> 


            <?php
            require_once '../core/201008libreriaValidacion.php'; //incluimos la libreria de validación
            require_once "../config/conexionBDPDO.php"; //incluimos la conexión a la BD
            //definimos las constantes que vamos a usar más de una vez
            define("OBLIGATORIO", 1); //definimos e inicializamos la constante obligatorio a 1

            $entradaOK = true; //declaramos y inicializamos la variable entradaObligatorioK, esta variable decidira si es correcta la entrada de datos del formulario

            $aFormulario = [//declaramos y inicializamos el array de los campos del formulario a null
                "codDepartamento" => null,
                "descripDepartamento" => null,
                "volumenNegocio" => null
            ];
            $aError = [//declaramos y inicializamos el array de los errores de los campos del formulario a null
                "codDepartamento" => null,
                "descripDepartamento" => null,
                "volumenNegocio" => null
            ];

            if (isset($_REQUEST["add"])) {
                $aError["codDepartamento"] = validacionFormularios::comprobarAlfabetico(strtoupper($_REQUEST["codDepartamento"]), 3, 3, OBLIGATORIO); //Validamos la entrada del formulario para el campo textfieldObligatorio siendo este alfabetico
                if ($aError["codDepartamento"] === null) {//entra si no da error en la validación del campo
                    try {
                        $miDB = new PDO(DNS, USER, PASSWORD, CODIFICACION); //Creamos el objeto PDO
                        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql = "SELECT CodDepartamento FROM Departamento where CodDepartamento=:codDepartamento"; //Creamos la consulta mysql con los parametros bind

                        $consultaDepartamento = $miDB->prepare($sql); //Preparamos la consulta
                        $consultaDepartamento->bindParam(":codDepartamento", $_REQUEST["codDepartamento"]); //Declaramos el parametro bind
                        $consultaDepartamento->execute(); //Ejecutamos la consulta preparada                           
                        if ($consultaDepartamento->rowCount() != 0) {//si es diferente a 0 declaramos un error ya que la primary key estaria creada ya
                            $aError["codDepartamento"] = "Ya existe un departamento con esas iniciales";
                        }
                    } catch (PDOException $miExcepcionPDO) {//declaración de excepcionesPDO
                        echo "<p class='error'>Error " . $miExcepcionPDO->getMessage() . "</p>";
                        echo "<p class='error'>Cod.Error " . $miExcepcionPDO->getCode() . "</p>";
                        echo "<h2 class='error'>Error en la conexión con la base de datos</h2>";
                    } finally {
                        unset($miDB); //cerramos la conexión
                    }
                }
                $aError["descripDepartamento"] = validacionFormularios::comprobarAlfabetico($_REQUEST["descripDepartamento"], 50, 3, OBLIGATORIO); //Validamos la entrada del formulario para el campo descripDepartamento siendo este alfabetico de tamaño max 50 minimo 3
                $aError["volumenNegocio"] = validacionFormularios::comprobarEntero($_REQUEST["volumenNegocio"], 20, 1, 0); //Validamos la entrada del formulario para el campo volumenNegocio siendo este numerico siendo este de tamaño max 20, minimo 1 y opcional

                foreach ($aError as $errores => $value) { //Recorremos todos los campos del array $aError
                    if ($value != null) { //Si algun campo de $aError tiene un valor diferente null entonces entra
                        $entradaOK = false; // asignamos el valor a false en caso de que entre
                    }
                }
            } else {//si el usuario no ha pulsado el boton de enviar
                $entradaOK = false; //asignamos el valor a false ya que no se a enviado nada.
            }
            if ($entradaOK) {// si el valor es true entra
                $aFormulario["codDepartamento"] = strtoupper($_REQUEST["codDepartamento"]);
                $aFormulario["descripDepartamento"] = $_REQUEST["descripDepartamento"];
                if ($_REQUEST["volumenNegocio"] === "") {// si el campo $_REQUEST["volumenNegocio"] es "" asignamos el valor null, en mysql "" no es null y da error en la consulta
                    $aFormulario["volumenNegocio"] = null;
                } else {
                    $aFormulario["volumenNegocio"] = $_REQUEST["volumenNegocio"];
                }

                try {
                    $miDB = new PDO(DNS, USER, PASSWORD);
                    $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $sqlInsert = "INSERT INTO Departamento (CodDepartamento, DescDepartamento, VolumenNegocio) VALUES(:codDep, :nombDep, :volumenNegocio)"; //Creamos la consulta mysql con los parametros bind

                    $insertarDepartamento = $miDB->prepare($sqlInsert); //Preparamos la consulta
                    //bindeamos los campos para la consulta
                    $insertarDepartamento->bindParam(":codDep", $aFormulario["codDepartamento"]);
                    $insertarDepartamento->bindParam(":nombDep", $aFormulario["descripDepartamento"]);
                    $insertarDepartamento->bindParam(":volumenNegocio", $aFormulario["volumenNegocio"]);
                    $insertarDepartamento->execute(); //Ejecutamos la consulta preparada

                    echo "<div id='form'><h3>Se a insertado el departamento correctamente</h3></div>";
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
                ?>
                <div id="form">
                    <form class="descript" action= "<?php echo $_SERVER["PHP_SELF"] ?>" method= "POST">
                        <div class="campos">
                            <label class="labelTitle" for="codDepartamento">Codigo del Departamento: </label>
                            <input  class="inputText" type="text" name="codDepartamento" placeholder="Introduzca un codigo de departamento '3 letras'" 
                                    value="<?php echo isset($_REQUEST["codDepartamento"]) ? isset($aError["codDepartamento"]) ? null : $_REQUEST["codDepartamento"] : null ?>"><!-- Comprobamos si el campo codDepartamento a sido enviada, en cado positivo comprobamos si tiene error, en caso afirmativo borra el value del campo, en caso contrario deja el value se mantiene con el valor insertado correctamente y si el campo no a sido enviado el value se queda blanco-->
                                    <?php echo isset($aError["codDepartamento"]) ? "<span class='error'>" . "<br>" . $aError["codDepartamento"] . "</span>" : null ?>
                        </div>
                        <div class="campos">
                            <label class="labelTitle" for="descripDepartamento">Descripción del Departamento: </label>
                            <input  class="inputText" type="text" name="descripDepartamento" placeholder="Introduzca la descripción del departamento." 
                                    value="<?php echo isset($_REQUEST["descripDepartamento"]) ? isset($aError["descripDepartamento"]) ? null : $_REQUEST["descripDepartamento"] : null ?>"><!-- Comprobamos si el campo descripDepartamento a sido enviada, en cado positivo comprobamos si tiene error, en caso afirmativo borra el value del campo, en caso contrario deja el value se mantiene con el valor insertado correctamente y si el campo no a sido enviado el value se queda blanco-->
                            <?php echo isset($aError["descripDepartamento"]) ? "<span class='error'>" . "<br>" . $aError["descripDepartamento"] . "</span>" : null ?><!-- Comprobamos si el campo descripDepartamento tiene error en caso afirmativo muestra un mensaje de error, en caso contrario no h ace nada-->
                        </div>
                        <div class="campos">
                            <label class="labelTitle" for="volumenNegocio">Volumen de Negocio: </label>
                            <input  class="inputText" type="text" name="volumenNegocio" placeholder="Introduzca un volumen de Negocio."
                                    value="<?php echo isset($_REQUEST["volumenNegocio"]) ? isset($aError["volumenNegocio"]) ? null : $_REQUEST["volumenNegocio"] : null ?>"><!-- Comprobamos si el campo volumenNegocio a sido enviada, en cado positivo comprobamos si tiene error, en caso afirmativo borra el value del campo, en caso contrario deja el value se mantiene con el valor insertado correctamente y si el campo no a sido enviado el value se queda blanco -->
                            <?php echo isset($aError["volumenNegocio"]) ? "<span class='error'>" . $aError["volumenNegocio"] . "</span>" : null ?><!-- Comprobamos si el campo volumenNegocio tiene error en caso afirmativo muestra un mensaje de error, en caso contrario no h ace nada-->
                        </div>
                        <div class="botonSend">
                            <input class="botonEnvio" type= "submit" value= "Añadir" name= "add">
                            <input class = "botonEnvio" type = "submit" value = "Cancelar" name = "volver">
                        </div>
                    </form>

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
