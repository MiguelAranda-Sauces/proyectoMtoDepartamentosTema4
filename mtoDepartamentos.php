<?php
/*
 * @autor: Miguel Angel Aranda Garcia <miguela.aragar@educa.jcyl.es>
 * @version 1.1
 * @since 18/11/2020 1.0: creación de la pagina matenimiento Depatarmentos 1.1: actualización de aspecto visual y programar buscar.
 */
if (isset($_REQUEST["add"])) {
    header('Location: codigoPHP/altaDepartamento.php');
    exit;
}
?>
<!DOCTYPE html>

<html>
    <head>
        <title>Mantenimiento Departamento Tema 4</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="webroot/css/style.css">
    </head>
    <body>
        <div id="cabecera">
            <div id="titulo">
                <h1>Mantenimiento Departamento Miguel Angel</h1>
            </div>
            <div class="nav">
                <a href="../proyectoTema4/proyectoTema4.html" class="boton volver"><img class="icoBoton" src="img/volver-flecha-izquierda.png"><span class="texto">Volver</span></a>
                <span id="acciones">
                    <a href="codigoPHP/mostrarCodigo.php" class="boton"><img class="icoBoton" src="img/enfocar.png"><span class="texto">Codigo</span></a>
                    <a href="codigoPHP/exportarDepartamentos.php" class="boton"><img class="icoBoton" src="img/database_export_icon_137698.png"><span class="texto">Exportar</span></a>
                    <a href="codigoPHP/importarDepartamentos.php" class="boton"><img class="icoBoton" src="img/database_import_icon_135719.png"><span class="texto">Importar</span></a>
                </span>

            </div>
        </div>
        <div id="contenedor"> 


            <?php
            require_once 'core/201008libreriaValidacion.php'; //incluimos la libreria de validación
            require_once 'config/conexionBDPDO.php'; //incluimos la conexión a la BD

            $entradaOK = true; //declaramos y inicializamos la variable entradaObligatorioK, esta variable decidira si es correcta la entrada de datos del formulario

            $aFormulario = [//declaramos y inicializamos el array de los campos del formulario a null
                "descripDepartamento" => null
            ];
            $aError = [//declaramos y inicializamos el array de los errores de los campos del formulario a null
                "descripDepartamento" => null
            ];


            if (isset($_REQUEST["buscar"])) {
                $aError["descripDepartamento"] = validacionFormularios::comprobarAlfabetico(strtoupper($_REQUEST["descripDepartamento"]), 250, 0, 0); //Validamos la entrada del formulario para el campo textfieldObligatorio siendo este alfabetico

                foreach ($aError as $errores => $value) { //Recorremos todos los campos del array $aError
                    if ($value != null) { //Si algun campo de $aError tiene un valor diferente null entonces entra
                        $entradaOK = false; // asignamos el valor a false en caso de que entre
                    }
                }
            } else {
                $entradaOK = false;
            }
            if ($entradaOK) {
                $aFormulario["descripDepartamento"] = $_REQUEST["descripDepartamento"];
                ?>
                <div id="form">
                    <form class="descript" action= "<?php echo $_SERVER["PHP_SELF"] ?>" method= "POST">
                        <div class="campos">
                            <label class="labelTitle" for="descripDepartamento">Descripción del Departamento:</label>
                            <input  class="inputText" type="text" name="descripDepartamento" placeholder="Introduzca la descripción del departamento." 
                                    value=""><!-- Comprobamos si el campo descripDepartamento a sido enviada, en cado positivo comprobamos si tiene error, en caso afirmativo borra el value del campo, en caso contrario deja el value se mantiene con el valor insertado correctamente y si el campo no a sido enviado el value se queda blanco-->
                        </div>
                        <div class="botonSend">
                            <input class="botonEnvio icoSearch" type= "submit" value= "Buscar" name= "buscar">
                        </div>
                        <div class="botonSend aniadir">
                            <input class="botonEnvio icoAdd" type="submit" value= "Añadir" name= "add">
                        </div>
                    </form>
                </div>
                <div id="table">
                    <?php
                    try {
                        $miDB = new PDO(DNS, USER, PASSWORD, CODIFICACION);

                        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $consultarLike = "SELECT * FROM Departamento WHERE DescDepartamento LIKE CONCAT('%', :descripDepartamento, '%')"; //Creamos la consulta mysq
                        $consultaDepartamento = $miDB->prepare($consultarLike); //Preparamos la consulta
                        $consultaDepartamento->bindParam(":descripDepartamento", $aFormulario["descripDepartamento"]); //Declaramos el parametro bind
                        $consultaDepartamento->execute(); //Ejecutamos la consulta preparada
                        if ($consultaDepartamento->rowCount() >= 1) {
                            ?>
                            <div class="formulario">
                                <table class="tablaResult">
                                    <tbody>
                                        <tr>
                                            <th>Codigo departamento</th>
                                            <th>Nombre departamento</th>
                                            <th>Fecha baja</th>
                                            <th>Volumen de negocio</th>
                                            <th>Acciones</th>

                                        </tr>
                                        <?php
                                        //para poder mostrar el contenido de dicha query usaremos el comando fetchObject() Obtiene la siguiente fila y la devuelve como un objeto por eso usaremos algun tipo de bucle para recorrerlo
                                        $oDepartamento = $consultaDepartamento->fetchObject();
                                        while ($oDepartamento) {
                                            //Tener cuidado cuando llamas a los campos y con la consulta no sea que tengas errores de sistaxis por maysculas, minusculas o equivocación de nombres de los campos dentro de la base de datos
                                            ?>
                                            <tr>
                                                <td><?php echo $oDepartamento->CodDepartamento; ?></td>
                                                <td><?php echo $oDepartamento->DescDepartamento; ?></td>
                                                <td><?php echo $oDepartamento->FechaBaja; ?></td>
                                                <td><?php echo $oDepartamento->VolumenNegocio; ?></td>
                                                <td>
                                                    <a href="codigoPHP/editarDepartamento.php?codDep=<?php echo $oDepartamento->CodDepartamento; ?>"><image class="ico" src="img/editar.png"></a>
                                                    <a href="codigoPHP/mostrarDepartamento.php?codDep=<?php echo $oDepartamento->CodDepartamento; ?>"><image class="ico" src="img/ojo.png"></a>
                                                    <a href="codigoPHP/bajaDepartamento.php?codDep=<?php echo $oDepartamento->CodDepartamento; ?>"><image class="ico" src="img/eliminar.png"></a>
                                                    <?php if ($oDepartamento->FechaBaja == null) { ?>
                                                        <a href="codigoPHP/bajalogicaDepartamento.php?codDep=<?php echo $oDepartamento->CodDepartamento; ?>"><image class="ico" src="img/apagado.png"></a>
                                                    <?php } else { ?>
                                                        <a href="codigoPHP/rehabilitacionDepartamento.php?codDep=<?php echo $oDepartamento->CodDepartamento; ?>"><image class="ico" src="img/encendido.png"></a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $oDepartamento = $consultaDepartamento->fetchObject();
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <p class="totaRegistros">La tabla contiene: <?php echo $consultaDepartamento->rowCount(); ?> registros.</p>
                            </div>
                            <div id ="botonesInferior">
                                <a id ="primera"><img class="ico" src="img/backward.png"></a>
                                <a id ="anterior"><img class="ico" src="img/back.png"></a>
                                <a id ="siguiente"><img class="ico" src="img/next.png"></a>
                                <a id ="ultima"><img class="ico" src="img/forward.png"></a>
                            </div>

                            <?php
                        } else {
                            echo "<p class='result'>No hay ningun Departamento con esa descripción</p>";
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
                    ?>
                </div>
                <?php
            } else {
                ?>
                <div id="form">
                    <form class="descript" action= "<?php echo $_SERVER["PHP_SELF"] ?>" method= "POST">
                        <div class="campos">
                            <label class="labelTitle" for="descripDepartamento">Descripción del Departamento:</label>
                            <input  class="inputText" type="text" name="descripDepartamento" placeholder="Introduzca la descripción del departamento." 
                                    value=""><!-- Comprobamos si el campo descripDepartamento a sido enviada, en cado positivo comprobamos si tiene error, en caso afirmativo borra el value del campo, en caso contrario deja el value se mantiene con el valor insertado correctamente y si el campo no a sido enviado el value se queda blanco-->
                        </div>
                        <div class="botonSend">
                            <input class="botonEnvio icoSearch" type= "submit" value= "Buscar" name= "buscar">
                        </div>
                        <div class="botonSend aniadir">
                            <input class="botonEnvio icoAdd" type="submit" value= "Añadir" name= "add">
                        </div>
                    </form>
                </div>
                <div id="table">
                    <?php
                    try {
                        $miDB = new PDO(DNS, USER, PASSWORD, CODIFICACION);

                        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $consultarAuto = "SELECT * FROM Departamento";
                        $consultaDepartamento = $miDB->prepare($consultarAuto); //preparamos la consulta
                        $consultaDepartamento->execute();
                        ?>
                        <div class="formulario">
                            <table class="tablaResult">
                                <tbody>
                                    <tr>
                                        <th>Codigo departamento</th>
                                        <th>Nombre departamento</th>
                                        <th>Fecha baja</th>
                                        <th>Volumen de negocio</th>
                                        <th>Acciones</th>

                                    </tr>
                                    <?php
                                    //para poder mostrar el contenido de dicha query usaremos el comando fetchObject() Obtiene la siguiente fila y la devuelve como un objeto por eso usaremos algun tipo de bucle para recorrerlo
                                    $oDepartamento = $consultaDepartamento->fetchObject();
                                    while ($oDepartamento) {
                                        //Tener cuidado cuando llamas a los campos y con la consulta no sea que tengas errores de sistaxis por maysculas, minusculas o equivocación de nombres de los campos dentro de la base de datos
                                        ?>
                                        <tr>
                                            <td><?php echo $oDepartamento->CodDepartamento; ?></td>
                                            <td><?php echo $oDepartamento->DescDepartamento; ?></td>
                                            <td><?php echo $oDepartamento->FechaBaja; ?></td>
                                            <td><?php echo $oDepartamento->VolumenNegocio; ?></td>
                                            <td>
                                                <a href="codigoPHP/editarDepartamento.php?codDep=<?php echo $oDepartamento->CodDepartamento; ?>"><image class="ico" src="img/editar.png"></a>
                                                <a href="codigoPHP/mostrarDepartamento.php?codDep=<?php echo $oDepartamento->CodDepartamento; ?>"><image class="ico" src="img/ojo.png"></a>
                                                <a href="codigoPHP/bajaDepartamento.php?codDep=<?php echo $oDepartamento->CodDepartamento; ?>"><image class="ico" src="img/eliminar.png"></a>
                                                <?php if ($oDepartamento->FechaBaja == null) { ?>
                                                    <a href="codigoPHP/bajalogicaDepartamento.php?codDep=<?php echo $oDepartamento->CodDepartamento; ?>"><image class="ico" src="img/apagado.png"></a>
                                                <?php } else { ?>
                                                    <a href="codigoPHP/rehabilitacionDepartamento.php?codDep=<?php echo $oDepartamento->CodDepartamento; ?>"><image class="ico" src="img/encendido.png"></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $oDepartamento = $consultaDepartamento->fetchObject();
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <p class="totaRegistros">La tabla contiene: <?php echo $consultaDepartamento->rowCount(); ?> registros.</p>
                            <?php
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
                        ?>
                    </div>
                    <div id ="botonesInferior">
                        <a id ="primera"><img class="ico" src="img/backward.png"></a>
                        <a id ="anterior"><img class="ico" src="img/back.png"></a>
                        <a id ="siguiente"><img class="ico" src="img/next.png"></a>
                        <a id ="ultima"><img class="ico" src="img/forward.png"></a>
                    </div>
                </div>

                <?php
            }
            ?>
        </div>
        <footer>
            <div class="pie">
                <a href="../index.html" class="nombre">Miguel Ángel Aranda García</a>
                <a href="https://github.com/MiguelAranda-Sauces" class="git" ><img class="git" src="img/git.png"></a>
            </div>

        </footer>
    </body>
</html>
