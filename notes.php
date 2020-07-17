<?php
require_once 'login.php';
$conexion = new mysqli($hs, $us, $ps, $db);
session_start();
if($conexion->connect_error) die("Error fatal");

echo <<<_END
    <h1>Elige</h1>
    <form method="post" action="notes.php"><pre>
    Nueva nota      <input type="radio" name="choose" value="1">
    Mostrar Notas   <input type="radio" name="choose" value="2">
    Buscar Notas    <input type="radio" name="choose" value="3">
    Modificar Notas <input type="radio" name="choose" value="4">
    Salir           <input type="radio" name="choose" value="5">
    <input type="submit">
    </form>
_END;

if(isset($_POST['choose'])){
    if($_POST['choose']==1){
        new_notes($conexion);
    }elseif($_POST['choose']==2){
        show_notes($conexion);
    }elseif($_POST['choose']==3){
        search_notes($conexion);
    }elseif($_POST['choose']==4){
        modify_notes($conexion);
    }elseif($_POST['choose']==5){
        exit_notes();
    }
}else die("<br>Elija una opcion");


// Funciones
function mysql_entities_fix_string($conexion, $string){
    return htmlentities(mysql_fix_string($conexion, $string));
}

function mysql_fix_string($conexion, $string){
    if (get_magic_quotes_gpc()) $string = stripslashes($string);
    return $conexion->real_escape_string($string);
}

// --Nuevas notas
function new_notes($conexion){
    if(isset($_POST["titulo"]) && isset($_POST["fecha"]) && isset($_POST["descripcion"])){
        $correo=$_SESSION["correo"];
        $titulo=mysql_entities_fix_string($conexion, $_POST["titulo"]);
        $fecha=mysql_entities_fix_string($conexion, $_POST["fecha"]);
        $descripcion=mysql_entities_fix_string($conexion, $_POST["descripcion"]);
        $query="SELECT * FROM notas WHERE users_correoElectronico='$correo' AND titulo='$titulo'";
        $result=$conexion->query($query);

        if($result->num_rows) die("<br>El titulo de esta nota ya existe");
        else{
            $result->close();
            $stm="INSERT INTO notas VALUES(?,?,?,?)";
            $result=$conexion->prepare($stm);
            $result->bind_param("ssss",$correo,$titulo,$fecha,$descripcion);
            $result->execute();
            $result->close();
            if(!$result) die("No se ha podido crear la nota");
            else die ("<br>La nota $titulo se creo con exito");
        }
    }else{
        $date=date('Y-m-d');
        echo <<<_END
        <h1>Nueva Nota</h1>
        <form action="notes.php" method="post"><pre>
        Titulo       <input type="text" name="titulo" autofocus required>
        Fecha        <input type="date" name="fecha" min="$date" required>
        Descripcion  <textarea name="descripcion" rows="10" cols="50"></textarea>
                     <input type="hidden" name="choose" value="1">
                     <input type="submit" value="SUBIR">
        </form>
_END;
    }
}

// --Mostrar notas
function show_notes($conexion){
    $correo=$_SESSION["correo"];
    $query="SELECT * FROM notas WHERE users_correoElectronico='$correo' ORDER BY fecha";
    $result=$conexion->query($query);

    if($result->num_rows){
        $rows = $result->num_rows;
        echo "<h1>Mostrando Notas</h1>";
        for($j=0; $j<$rows; $j++){
            $row=$result->fetch_array(MYSQLI_NUM);
            $titulo=htmlspecialchars($row[1]);
            $fecha=htmlspecialchars($row[2]);
            $descripcion=htmlspecialchars($row[3]);

            echo <<<_END
        <pre>
        Titulo      $titulo
        Fecha       $fecha
        Descripcion $descripcion
        </pre>
_END;
        }
        $result->close();
    }else die("<br>No se han encontrado notas");
}

// --Buscar notas
function search_notes($conexion){
    if(isset($_POST["titulo"])){
        $titulo=mysql_entities_fix_string($conexion,$_POST["titulo"]);
        $correo=$_SESSION["correo"];
        $query="SELECT * FROM notas WHERE users_correoElectronico='$correo' AND titulo like '%$titulo%' ORDER BY fecha";
        $result=$conexion->query($query);

        if($result->num_rows){
            $rows = $result->num_rows;
            echo "<h1>Notas Encontradas</h1>";
            for($j=0; $j<$rows; $j++){
                $row=$result->fetch_array(MYSQLI_NUM);
                $titulo=htmlspecialchars($row[1]);
                $fecha=htmlspecialchars($row[2]);
                $descripcion=htmlspecialchars($row[3]);

                echo <<<_END
        <pre>
        Titulo      $titulo
        Fecha       $fecha
        Descripcion $descripcion
        </pre>
_END;
            }
        $result->close();
        }else die("<br>No se han encontrado notas");
    }else{
        echo <<<_END
        <h1>Buscar Nota</h1>
        <form action="notes.php" method="post"><pre>
        Titulo       <input type="text" name="titulo" autofocus required>
                     <input type="hidden" name="choose" value="3">
                     <input type="submit" value="BUSCAR">
        </form>
_END;
    }
}

// --Modificar notas
function modify_notes($conexion){
    if(isset($_POST["titulo"])){
        $correo=$_SESSION["correo"];
        $titulo=mysql_entities_fix_string($conexion,$_POST["titulo"]);
        $query="SELECT * FROM notas WHERE users_correoElectronico='$correo' AND titulo='$titulo'";
        $result=$conexion->query($query);
        
        if($result->num_rows){
            $result->close();
            if(isset($_POST["mtitulo"]) && isset($_POST["mfecha"]) && isset($_POST["mdescripcion"])){
                $mtitulo=mysql_entities_fix_string($conexion,$_POST["mtitulo"]);
                $mfecha=mysql_entities_fix_string($conexion,$_POST["mfecha"]);
                $mdescripcion=mysql_entities_fix_string($conexion,$_POST["mdescripcion"]);
                $query="SELECT * FROM notas WHERE users_correoElectronico='$correo' AND titulo='$mtitulo'";
                $result=$conexion->query($query);
                if($result->num_rows) die("No se puede poner el mismo titulo de otra nota");
                else{
                    $result->close();
                    $query="UPDATE notas set titulo='$mtitulo', fecha='$mfecha', descripcion='$mdescripcion' WHERE users_correoElectronico='$correo' AND titulo='$titulo'";
                    $result=$conexion->query($query);
                    if(!$result) die("<br>No se ha podido modificar");
                    else die ("<br>La nota se ha modificado correctamente");
                    $result->close();
                }
            }else{
                $date=date('Y-m-d');
                echo <<<_END
        <h1>Modificar Nota</h1>
        <form action="notes.php" method="post"><pre>
        Titulo       <input type="text" name="mtitulo" autofocus required>
        Fecha        <input type="date" name="mfecha" min="$date" required>
        Descripcion  <textarea name="mdescripcion" rows="10" cols="50"></textarea>
                     <input type="hidden" name="titulo" value="$titulo">
                     <input type="hidden" name="choose" value="4">
                     <input type="submit" value="MODIFICAR">
        </form>
_END;
            }
        }else die ("<br>No se ha encontrado la nota para modificar");
    }else{
        echo <<<_END
        <h1>Modificar Nota</h1>
        <form action="notes.php" method="post"><pre>
        Titulo       <input type="text" name="titulo" autofocus required>
                     <input type="hidden" name="choose" value="4">
                     <input type="submit" value="BUSCAR">
        </form>
_END;
    }
}

// --Salir
function exit_notes(){
    echo <<<_END
    <h1>Salir</h1>
    <form action="exit.php" method="post"><pre>
    <input type="submit" value="¿SEGUR@?">
    </form>
_END;
}

$conexion->close();

?>