<?php 
/* * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * *
 * *                                           * *
 * *      Vérifie si le cookie est présent     * *
 * *      et créé les variables de session     * *
 * *                                           * *
 * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * */
        
session_start();
if(isset($_COOKIE['token'])){
    if(!isset($_SESSION['first_name'])){
try {
    $connStr = 'mysql:host=localhost;dbname=timetoshoes_db'; //Ligne 1
    $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); //Ligne 2
    $pdo = new PDO($connStr, 'timetoshoes', 'Time2Shoes', $arrExtraParam); //Ligne 3; Instancie la connexion
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Ligne 4
}
catch(PDOException $e) {
    $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
    die($msg);
}
        $query = "SELECT first_name, last_name, password, delivery_address, delivery_town, billing_address, billing_town, email" 
            . " FROM users"
            . " WHERE token = ?";
        $prep = $pdo->prepare($query);
        
        $prep->bindValue(1, $_COOKIE['token'], PDO::PARAM_STR);
        
        //Compiler et exécuter la requête
        $prep->execute();
        
        $data = $prep->fetch(PDO::FETCH_ASSOC);
        
        $_SESSION['first_name']         = $data["first_name"];
        $_SESSION['last_name']          = $data["last_name"]; 
        $_SESSION['token']              = $_COOKIE['token'];
        $_SESSION['delivery_address']   = $data["delivery_address"];
        $_SESSION['delivery_town']      = $data["delivery_town"];
        $_SESSION['billing_address']    = $data["billing_address"];
        $_SESSION['billing_town']       = $data["billing_town"];
        $_SESSION['email_user']       = $data["email"];
 }   
}
?>