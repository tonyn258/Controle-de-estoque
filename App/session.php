<?php
session_start();

/**pegar a criptografia: veja como na aula 4  8:20min*/

$username = $_POST['username'];
$password = md5($_POST['password']);



if($username == NULL || $password == NULL){

    echo "<script>alert('Você dever digitar seu nome e senha');</script>";
    echo "<script> window.location.href='../login.php'</script>";
    exit;
}else{

    require_once 'Models/connect.php';

    $connect->login($username, $password);
}
?>