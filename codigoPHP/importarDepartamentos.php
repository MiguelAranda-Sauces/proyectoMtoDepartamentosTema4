
<div id="contenido">
    <?php
    /*
     * @autor: Miguel Angel Aranda Garcia <miguela.aragar@educa.jcyl.es>
     * @version 1.0
     * @since 18/11/2020 1.0: creación de la inportación departamentos
     */
    if (isset($_REQUEST["volver"])) {
        header('Location: ../mtoDepartamentos.php');
        exit;
    }
    ?>
    <!DOCTYPE html>

    <html>
        <head>
            <title>Importar Departamentos Tema 4</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" type="text/css" href="../webroot/css/styleInport.css">
            <link rel="stylesheet" type="text/css" href="../webroot/css/style.css">
        </head>
        <body>
            <div id="cabecera">
                <div id="titulo">
                    <h1>Importar Departamentos</h1>
                </div>
                <div class="nav">
                    <a href="../mtoDepartamentos.php" class="boton volver"><img class="icoBoton" src="../img/volver-flecha-izquierda.png"><span class="texto">Volver</span></a>
                </div>
            </div>
            <div id="contenedor"> 
                <?php
                require_once "../config/conexionBDPDO.php"; //incluimos la conexión a la BD

                $entradaOK = true; //declaramos y inicializamos la variable entradaObligatorioK, esta variable decidira si es correcta la entrada de datos del formulario
                $errorTipo = null;
                if (isset($_REQUEST["inport"])) {
                    if ($_FILES["inportFile"]["type"] != "text/xml") {
                        $errorTipo = "El formato no es correcto, debe de ser .xml";
                        $entradaOK = false;
                    }
                } else {
                    $entradaOK = false;
                }
                if ($entradaOK) {
                    try {
                        $miDB = new PDO(DNS, USER, PASSWORD, CODIFICACION); //Creamos el objeto PDO
                        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Le asignamos los atributos al objeto PDO de la conexión
                        //Borrar los datos de la tabla Departamento
                        

                        $sql = "SELECT CodDepartamento FROM Departamento";
                        $consultaDepartamento = $miDB->prepare($sql);
                        $consultaDepartamento->execute();

                        if ($consultaDepartamento->rowCount() > 0) {
                            $sqlBorrar = "TRUNCATE TABLE Departamento;";
                            $borrarDep = $miDB->prepare($sqlBorrar);
                            $borrarDep->execute();
                        }

                        $archivo = $_FILES["inportFile"]["tmp_name"];
                        $fileXML = new DOMDocument("1.0", "utf-8"); //creamos el documento //creamos un objeto DOMDocument que no se olvide poner la codificación del archivo
                        $fileXML->formatOutput = true;
                        $fileXML->load($archivo); //inportamos  el objeto DOM

                        $queryInsert = "INSERT INTO Departamento VALUES(:codDep,:descDep,:fechBaja,:volumen)"; //creamos la consulta generica que usaremos

                        $numDep = $fileXML->getElementsByTagName('Departamento')->length; //almacenamos la cantidad de elementos que tiene el Departamento

                        $insertDepartamentos = $miDB->prepare($queryInsert);
                        //extraemos los valores de cada uno de los item del archivo XML
                        for ($puntero = 0; $puntero < $numDep; $puntero++) {
                            
                            $elementosDep = $fileXML->getElementsByTagName('Departamento')->item($puntero)->childNodes; //buscamos todos los elementos hijos de Departamento y los asignamos a la sentecia prepare en un array

                            $values = ["codDep" => $elementosDep->item(1)->nodeValue,
                                "descDep" => $elementosDep->item(3)->nodeValue,
                                "fechBaja" => $elementosDep->item(5)->nodeValue,
                                "volumen" => $elementosDep->item(7)->nodeValue];

                            if (empty($values[":fechBaja"])) {//si la fecha baja logica no esta definida la pondremos a null ya que da error de tipo al insertarla en la bd
                                $values[":fechBaja"] = null;
                            }
                            if (empty($values[":volumen"])) {//si el volumenNegocio no esta definida la pondremos a null ya que da error de tipo al insertarla en la bd
                                $values[":volumen"] = null;
                            }
                            $insertDepartamentos->execute($values);
                        }
                         echo "<div id='menError'><h3>Se ha inportado correctamente el archivo, un total de: " . $numDep . " Departamentos</h3></div>";
                        
                    } catch (PDOException $miExcepcionPDO) {
                        ?>
                        }

                        <p class='error'>Error  <?php echo $miExcepcionPDO->getMessage(); ?> </p>
                        <p class='error'>Cod.Error <?php echo $miExcepcionPDO->getCode(); ?></p>
                        <h2 class='error'>Error en la conexión con la base de datos</h2>
                        <?php
                    } finally {
                        unset($miDB); //cerramos la conexión
                    }
                } else {
                    ?>
                    <div id="form">
                    <form class="descript" action= "<?php echo $_SERVER["PHP_SELF"] ?>" method= "POST" enctype="multipart/form-data">
                        <div class="campos">
                            <label class="labelTitle" for="inportFile">inportar archivo XML </label>
                            <input  class="inputText" type="file" name="inportFile" 
                                    value="<?php echo isset($_REQUEST["inportFile"]) ? isset($errorTipo) ? null : $_REQUEST["inportFile"] : null ?>"><!-- Comprobamos si el campo inportFile a sido enviada, en cado positivo comprobamos si tiene error, en caso afirmativo borra el value del campo, en caso contrario deja el value se mantiene con el valor insertado correctamente y si el campo no a sido enviado el value se queda blanco-->
                                    <?php echo isset($errorTipo) ? "<span class='error'>" . "<br>" . $errorTipo . "</span>" : null ?>
                        </div>
                        <div class="botonSend">
                            <input class="botonEnvio" type= "submit" value= "Importar" name= "inport">
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