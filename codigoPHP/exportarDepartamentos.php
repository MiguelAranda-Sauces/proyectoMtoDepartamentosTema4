<?php
/*
 * @autor: Miguel Angel Aranda Garcia <miguela.aragar@educa.jcyl.es>
 * @version 1.0
 * @since 18/11/2020 1.0: creación de la exportación departamentos
 */
require_once "../config/conexionBDPDO.php"; //incluimos la conexión a la BD
try {
    $miDB = new PDO(DNS, USER, PASSWORD, CODIFICACION); //Creamos el objeto PDO

    $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlDepatamentos = "SELECT * FROM Departamento";
    $consultaDepartamento = $miDB->prepare($sqlDepatamentos); //preparamos la consulta
    $consultaDepartamento->execute();
    //Busqueda de exportación https://www.php.net/manual/es/domdocument.savexml.php
    $fileXML = new DOMDocument('1.0', 'utf-8'); //creamos el objeto Dom document
    $fileXML->formatOutput = True; //Da formato a la salida con identación y espacios extra.
    $oDepartamento = $consultaDepartamento->fetchObject(); //Iniciamos el puntero en la 1º posición
    $root = $fileXML->appendChild($fileXML->createElement('Departamentos')); // Creamos en nodo Padre con el elemento "Departamentos"
    //Recorremos las tablas y los campos de las tablas asignandolas a cada uno de los elementos xml
    while ($oDepartamento) {
        $departamento = $root->appendChild($fileXML->createElement('Departamento'));
        $departamento->appendChild($fileXML->createElement('CodDepartamento', $oDepartamento->CodDepartamento));
        $departamento->appendChild($fileXML->createElement('DescDepartamento', $oDepartamento->DescDepartamento));
        $departamento->appendChild($fileXML->createElement('FechaBaja', $oDepartamento->FechaBaja));
        $departamento->appendChild($fileXML->createElement('VolumenNegocio', $oDepartamento->VolumenNegocio));
        $oDepartamento = $consultaDepartamento->fetchObject();
    }
    $fileXML->save('../tmp/exportacionXML.xml'); // guardar el XML en la ruta 

    header('Content-type: application/xml');
    header('Content-Disposition: attachment; filename="exportacionXML.xml"');
    readfile('../tmp/exportacionXML.xml');
} catch (PDOException $miExcepcionPDO) {
    ?>
    <!DOCTYPE html>

    <html>
        <head>
            <meta charset="UTF-8">
            <title>Ejercicio08</title>
            <link rel="stylesheet" type="text/css" href="../webroot/css/styleForm2.css">
        </head>
        <body>
            <div id="contenido">
                <div class='contenedorError'>
                    <div class='box'>
                        <p class='error'>Error  <?php echo $miExcepcionPDO->getMessage(); ?> </p>
                        <p class='error'>Cod.Error <?php echo $miExcepcionPDO->getCode(); ?></p>
                        <h2 class='error'>Error en la conexión con la base de datos</h2>
                    </div>
                </div>
            </div>
        </body>
    </html>
    <?php
} finally {
    unset($miConexion); //cerramos la conexión
}
?>