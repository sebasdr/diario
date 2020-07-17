<?php

require_once "login.php";
$conexion = new mysqli($hs, $us, $ps, $db);

if($conexion->connect_error) die("Error de conexion");

if(isset($_POST['correo']) && isset($_POST['pass'])){
    $correo = mysql_entities_fix_string($conexion, $_POST['correo']);
    $pass = mysql_entities_fix_string($conexion,$_POST['pass']);
    $query = "SELECT * FROM users WHERE correoElectronico='$correo'";
    $result = $conexion->query($query);

    if (!$result){
        echo "<p>-No existe el usuario</p>";
        echo "<br>Crear un nuevo usuario <a href='signup.php'>Registrarse</a>";
        echo "<br>Volver a <a href='signin.php'>Ingresar</a>";
    }elseif ($result->num_rows){
        $row = $result->fetch_array(MYSQLI_NUM);
        $result->close();

        if (password_verify($pass, $row[1])){
            session_start();
            $_SESSION["correo"]=$row[0];
            echo htmlspecialchars("Hola $row[0], Bienvenido");
            die ("<p><a href='notes.php'>Click para continuar</a></p>");
        }else{
        echo "<p>Password incorrecto</p>";
        echo "<br>Crear un nuevo usuario <a href='signup.php'>Registrarse</a>";
        echo "<br>Volver a <a href='signin.php'>Ingresar</a>";
        echo "<br>!!!Olvide mi contraseña¡¡¡ <a href='recuperaruser.php'>Cambiar contraseña</a>";
        }
    }else{
        echo "<p>No existe el usuario</p>";
        echo "<br>Crear un nuevo usuario <a href='signup.php'>Registrarse</a>";
        echo "<br>Volver a <a href='signin.php'>Ingresar</a>";
    }
}else{
    echo <<<_END
    <h1>Ingresa</h1>
    <form action="signin.php" method="post"><pre>
    Correo   <input type="text" name="correo" required>
    Password <input type="password" name="pass" required>
             <input type="submit" value="INGRESAR">
    </form>
_END;
}

$conexion->close();

function mysql_entities_fix_string($conexion, $string){
    return htmlentities(mysql_fix_string($conexion, $string));
}

function mysql_fix_string($coneccion, $string){
    if (get_magic_quotes_gpc())
        $string = stripcslashes($string);
    return $coneccion->real_escape_string($string);
}

?>