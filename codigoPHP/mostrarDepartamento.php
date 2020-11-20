<?php
/*
 * @autor: Miguel Angel Aranda Garcia <miguela.aragar@educa.jcyl.es>
 * @version 1.0
 * @since 18/11/2020 1.0: creación de la pagina mostrar departamentos
 */
if (isset($_REQUEST["volver"])) {
    header('Location: ../mtoDepartamentos.php');
    exit;
}
?>
<!DOCTYPE html>

<html>
    <head>
        <title>Mostrar Departamento Tema 4</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../webroot/css/styleView.css">
        <link rel="stylesheet" type="text/css" href="../webroot/css/style.css">
    </head>
    <body>
        <div id="cabecera">
            <div id="titulo">
                <h1>Mostrar Departamento</h1>
            </div>
            <div class="nav">
                <a href="../mtoDepartamentos.php" class="boton volver"><img class="icoBoton" src="../img/volver-flecha-izquierda.png"><span class="texto">Volver</span></a>
            </div>
        </div>
        <div id="contenedor"> 


            <?php
            require_once '../core/201008libreriaValidacion.php'; //incluimos la libreria de validación
            require_once "../config/conexionBDPDO.php"; //incluimos la conexión a la BD
            if (isset($_REQUEST['codDep'])) {
                try {
                    $miDB = new PDO(DNS, USER, PASSWORD, CODIFICACION);
                    $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $cod = $_REQUEST['codDep'];
                    $consultarDep = "SELECT * FROM Departamento WHERE CodDepartamento=:CodDepartamento"; //Creamos la consulta mysq
                    $consultaDepartamento = $miDB->prepare($consultarDep); //Preparamos la consulta
                    $consultaDepartamento->bindParam(":CodDepartamento", $cod); //Declaramos el parametro bind
                    $consultaDepartamento->execute(); //Ejecutamos la consulta preparada
                    if ($consultaDepartamento->rowCount() == 1) {

                        while ($dep = $consultaDepartamento->fetchObject()) {
                            ?>
                            <div id="form">
                                <form class="descript" action= "<?php echo $_SERVER["PHP_SELF"] ?>" method= "POST">
                                    <div class="campos">
                                        <label class="labelTitle" for="codDepartamento">Codigo del Departamento: </label>
                                        <input  class="inputText" type="text" name="codDepartamento" disabled
                                                value="<?php echo $dep->CodDepartamento; ?>"><!-- Comprobamos si el campo codDepartamento a sido enviada, en cado positivo comprobamos si tiene error, en caso afirmativo borra el value del campo, en caso contrario deja el value se mantiene con el valor insertado correctamente y si el campo no a sido enviado el value se queda blanco-->                                
                                    </div>
                                    <div class="campos">
                                        <label class="labelTitle" for="descripDepartamento">Descripción del Departamento: </label>
                                        <input  class="inputText" type="text" name="descripDepartamento" disabled
                                                value="<?php echo $dep->DescDepartamento; ?>">           
                                    </div>
                                    <div class="campos">
                                        <label class="labelTitle" for="fechaBaja">Fecha de Baja </label>
                                        <input  class="inputText" type="date" name="fechaBaja" disabled
                                                value="<?php echo $dep->FechaBaja; ?>">   
                                    </div>
                                    <div class="campos">
                                        <label class="labelTitle" for="volumenNegocio">Volumen de Negocio: </label>
                                        <input  class="inputText" type="text" name="volumenNegocio" disabled
                                                value="<?php echo $dep->VolumenNegocio; ?>">   
                                    </div>
                                    <div class="botonSend">
                                        <input class="botonEnvio" type= "submit" value= "Volver" name= "volver">
                                    </div>
                                </form>

                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div id="menError">
                            <p>No existe ningun departamento con ese codigo de departamento</p>
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
