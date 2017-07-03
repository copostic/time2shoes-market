<?php

require_once'functions/check-cookies.php';
require_once'functions/fonctions-panier.php';

$erreur = false;

if(!isset($_SESSION['first_name'])){
    echo "<script>alert(\"Veuillez vous connecter pour ajouter un élément à votre panier !\")</script>"; 
}
 else
 {   
    $action = (isset($_POST['action'])? $_POST['action']:  (isset($_GET['action'])? $_GET['action']:null )) ;
    if($action !== null)
    {
        if(!in_array($action,array('ajout', 'suppression', 'refresh', 'Valider')))
            $erreur=true;
        
        //récuperation des variables en POST ou GET
        $l = (isset($_POST['l'])? $_POST['l']:  (isset($_GET['l'])? $_GET['l']:null )) ;
        $p = (isset($_POST['p'])? $_POST['p']:  (isset($_GET['p'])? $_GET['p']:null )) ;
        $q = (isset($_POST['q'])? $_POST['q']:  (isset($_GET['q'])? $_GET['q']:null )) ;
        $id = (isset($_POST['id'])? $_POST['id']:  (isset($_GET['id'])? $_GET['id']:null )) ;
        
        //Suppression des espaces verticaux
        $l = preg_replace('#\v#', '',$l);
        //On verifie que $p soit un float
        $p = floatval($p);
        
        //On traite $q qui peut etre un entier simple ou un tableau d'entier
        
        if (is_array($q)){
            $QteArticle = array();
            $i=0;
            foreach ($q as $contenu){
                $QteArticle[$i++] = intval($contenu);
            }
        }
        else
            $q = intval($q);
        
    }
    
    if (!$erreur){
        switch($action){
                Case "ajout":
                addArticle($l,$q,$p,$id);
                break;
                
                Case "suppression":
                deleteArticle($l);
                break;
                
                Case "refresh" :
                for ($i = 0 ; $i < count($QteArticle) ; $i++)
                {
                    modifyArticleQty($_SESSION['cart']['productName'][$i],round($QteArticle[$i]));
                }
                break;
                
                Case "Valider":
                validateTransaction();
                echo'<div class="alert alert-success">
                        <strong>Commande effectuée!</strong> Vous servez informé de sa validation dans les plus brefs délais.
                    </div>';
                break;
                
                Default:
                break;
        }
    }
    
    echo '<?xml version="1.0" encoding="utf-8"?>';?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <title>Votre panier</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/main.css">
    </head>

    <body>

        <form method="post" action="panier.php">
            <table style="width: 600px">
                <tr>
                    <td colspan="4">Votre panier</td>
                </tr>
                <tr>
                    <td>Libellé</td>
                    <td>Quantité</td>
                    <td>Prix Unitaire</td>
                    <td>Action</td>
                </tr>


                <?php
    if (createCart())
    {
        $nbArticles=count($_SESSION['cart']['productName']);
        if ($nbArticles <= 0)
            echo "<tr><td>Votre panier est vide </ td></tr>";
        else
        {
            for ($i=0 ;$i < $nbArticles ; $i++)
            {
                echo "<tr>";
                echo "<td>".htmlspecialchars($_SESSION['cart']['productName'][$i])."</ td>";
                echo "<td><input type=\"text\" size=\"4\" name=\"q[]\" value=\"".htmlspecialchars($_SESSION['cart']['productQty'][$i])."\"/></td>";
                echo "<td>".htmlspecialchars($_SESSION['cart']['productPrice'][$i])."</td>";
                echo "<td><a href=\"".htmlspecialchars("panier.php?action=suppression&l=".rawurlencode($_SESSION['cart']['productName'][$i]))."\"><span class=\" glyphicon glyphicon-remove\"></span></a></td>";
                echo "</tr>";
            }
            
            echo "<tr><td colspan=\"2\"> </td>";
            echo "<td colspan=\"2\">";
            echo "Total : ".globalPrice();
            echo "</td></tr>";
            
            echo "<tr><td colspan=\"4\">";
            echo "<input type=\"submit\" value=\"Rafraichir\"/>";
            echo "<input type=\"hidden\" name=\"action\" value=\"refresh\"/>";
            
            echo "<input type=\"submit\" name=\"action\" value=\"Valider\"/>";
            
            echo "</td></tr>";
        }
    }
                ?>
            </table>
        </form>
    </body>

    </html>
    <?php } ?>