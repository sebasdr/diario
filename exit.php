<?php

session_start();

if (isset($_SESSION['correo'])){
    $correo = $_SESSION['correo'];

    destroy_session_and_data();

    echo "<p>Hasta luego $correo</p>";
    echo "<br>Cree un nuevo usuario <a href='signup.php'>Registrese</a>";
    echo "<br>Volver a <a href='signin.php'>Ingresar</a>";
}else{
    echo "Cree un nuevo usuario <a href='signup.php'>Registrese</a>";
    echo "<br>Volver a <a href='signin.php'>Ingresar</a>";
}

function destroy_session_and_data(){
    $_SESSION = array();//Vacia el array $_SESSION
    setcookie(session_name(), '', time()-2592000, '/');
    session_destroy();
}

?>