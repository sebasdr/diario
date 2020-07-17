<?php

require_once "login.php";
$conexion = new mysqli($hs, $us, $ps, $db);

if($conexion->connect_error) die("Error de conexion");

if(isset($_POST["correo"]) && isset($_POST["pass"]) && isset($_POST["recuperar"])){
    $correo = mysql_entities_fix_string($conexion, $_POST['correo']);
    $pass = mysql_entities_fix_string($conexion, (password_hash($_POST['pass'],PASSWORD_DEFAULT)));
    $recuperar = mysql_entities_fix_string($conexion, $_POST['recuperar']);
    $query="SELECT * FROM users WHERE correoElectronico='$correo'";
    $result=$conexion->query($query);

    if($result->num_rows){
        echo "<p>El correo $correo ya existe</p>";
        echo "<br>Intente con otro correo <a href='signup.php'>Registrese</a>";
    }else{
        $result->close();
        $stm = "INSERT INTO users VALUES (?,?,?)";
        $result = $conexion->prepare($stm);

        if (!$result) die("Error de conexion");
        else{
            $result->bind_param('sss',$correo,$pass,$recuperar);
            $result->execute();
            $result->close();

            if(!$result) die("Error al registrarse");
            else{
                echo "<p>Se ha registrado exitosamente como $correo</p>";
                echo "<br>Ahora intente <a href='signin.php'>Ingresar</a>";
            }
        }
    }
}else{
    echo <<<_END
    <h1>Registrate</h1>
    <form action="signup.php" method="post"><pre>
    Correo Electronico <input type="text" name="correo" required>
    Password           <input type="password" name="pass" required>
    Recuperar user     <input type="text" name="recuperar" required placeholder="Escriba algo que recuerde">
                       <input type="submit" value="REGISTRAR">
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