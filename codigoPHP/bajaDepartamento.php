<?php
/*
 * @autor: Miguel Angel Aranda Garcia <miguela.aragar@educa.jcyl.es>
 * @version 1.0
 * @since 18/11/2020 1.0: creación de la pagina baja departamentos
 */
if (isset($_REQUEST["volver"])) {
    header('Location: ../mtoDepartamentos.php');
    exit;
}
?>
<!DOCTYPE html>

<html>
    <head>
        <title>Baja de Departamento Tema 4</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../webroot/css/styleEdit.css">
        <link rel="stylesheet" type="text/css" href="../webroot/css/style.css">
    </head>
    <body>
        <div id="cabecera">
            <div id="titulo">
                <h1>Baja de Departamento</h1>
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
                    } else if (isset($_REQUEST['delete'])) {
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
                if ($existe == 1) {
                    if (isset($_REQUEST['codDepartamento'])) {
                        $codDep = $_REQUEST["codDepartamento"];

                        //CONEXION BD PARA DELETE
                        try {
                            $miDB = new PDO(DNS, USER, PASSWORD);
                            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $sqlDelete = "DELETE FROM Departamento WHERE CodDepartamento = :CodDepartamento"; //Creamos la consulta mysql con los parametros bind

                            $borradoDepartamento = $miDB->prepare($sqlDelete); //Preparamos la consulta
                            //bindeamos los campos para la consulta
                            $borradoDepartamento->bindParam(":CodDepartamento", $codDep);
                            $borradoDepartamento->execute(); //Ejecutamos la consulta preparada

                            echo "<div id='menError'><h3>El departamento " . $codDep . " se a borrado correctamente</h3></div>";
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
                            $cod = $_REQUEST['codDep'];
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
                                        <div class = "campos">
                                            <label class = "labelTitle" for = "fechaBaja">Fecha de Baja </label>
                                            <input class = "inputText" type = "date" name = "fechaBaja" disabled
                                                   value = "<?php echo $dep->FechaBaja; ?>">
                                        </div>
                                        <div class = "campos">
                                            <label class = "labelTitle" for = "volumenNegocio">Volumen de Negocio: </label>
                                            <input class = "inputText" type = "text" name = "volumenNegocio" disabled
                                                   value = "<?php echo $dep->VolumenNegocio; ?>"> 

                                        </div>
                                        <div class = "botonSend">
                                            <input class = "botonEnvio" type = "submit" value = "Borrar" name = "delete">
                                            <input class = "botonEnvio" type = "submit" value = "Cancelar" name = "volver">
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
                        <p>No se a elegido ningun departamento</p>
                    </div>
                    <?php
                }
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
