<?php

require_once "login.php";
$conexion = new mysqli($hs, $us, $ps, $db);

if($conexion->connect_error) die("Error de conexion");

if(isset($_POST["correo"]) && isset($_POST["recuperar"])){
    $correo = mysql_entities_fix_string($conexion, $_POST['correo']);
    $recuperar = mysql_entities_fix_string($conexion, $_POST['recuperar']);
    $query="SELECT * FROM users WHERE correoElectronico='$correo' and recuperar='$recuperar'";
    $result=$conexion->query($query);

    if(!$result) die ("Error de conexion");
    elseif($result->num_rows){
        if(isset($_POST["pass"])){
            $result->close();
            $pass = mysql_entities_fix_string($conexion, (password_hash($_POST['pass'], PASSWORD_DEFAULT)));
            $query = "UPDATE users set passwor='$pass' where correoElectronico='$correo'";
            $result = $conexion->query($query);
            if(!$result) die("Error de conexion");
            else{
                echo "Bien $correo, su contraseña a sido cambiada correctamente";
                echo "<br>Ahora <a href='signin.php'>Ingrese</a>";
            }
        }else{
            echo <<<_END
    <h1>Bienvenido $correo</h1>
    <form action="recuperaruser.php" method="post"><pre>
    Nueva contraseña <input type="password" name="pass" required>
                     <input type="hidden" name="correo" value="$correo">
                     <input type="hidden" name="recuperar" value="$recuperar">
                     <input type="submit" value="CREAR">
    </form>
_END;
        }
    }else{
        echo "Usuario o recuperar user incorrecto";
        echo "<br>Intentar <a href='recuperaruser.php'>Recuperar usuario</a>";
        echo "<br>Crear un nuevo usuario <a href='signup.php'>Registrarse</a>";
        echo "<br>Volver a <a href='signin.php'>Ingresar</a>";
    }
}else{
    echo <<<_END
    <h1>Recuperar usuario</h1>
    <h2>Para recuperar su usuario debe escribir la informacion que escribio al registarse</h2>
    <form action="recuperaruser.php" method="post"><pre>
    Correo Electronico <input type="text" name="correo" required>
    Recuperar user     <input type="text" name="recuperar" required>
                       <input type="submit" value="RECUPERAR">
    </form>
_END;
}

function mysql_entities_fix_string($conexion, $string){
    return htmlentities(mysql_fix_string($conexion, $string));
}

function mysql_fix_string($coneccion, $string){
    if (get_magic_quotes_gpc())
        $string = stripcslashes($string);
    return $coneccion->real_escape_string($string);
}

$conexion->close();

?>