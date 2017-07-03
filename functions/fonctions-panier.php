<?php
/* * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * *
 * *                                           * *
 * *      Contient toutes les fonctions        * *
 * *      dédiées à la gestion du panier       * *
 * *                                           * *
 * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * */


/* * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *    Vérifie si le panier existe, sinon le créé       *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * */
function createCart(){
   if (!isset($_SESSION['cart'])){
      $_SESSION['cart']=array();
      $_SESSION['cart']['productName'] = array();
      $_SESSION['cart']['productQty'] = array();
      $_SESSION['cart']['productPrice'] = array();
      $_SESSION['cart']['productId'] = array();
      $_SESSION['cart']['lock'] = false;
   }
   return true;
}

/* * * * * * * * * * * * * * * * * * * * * * * *
 *      Ajoute un article dans le panier       *
 * * * * * * * * * * * * * * * * * * * * * * * */
function addArticle($productName,$productQty,$productPrice,$productId){

   //Si le panier existe
   if (createCart() && !isLocked())
   {
      //Si le produit existe déjà on ajoute seulement la quantité
      $productPosition = array_search($productName,  $_SESSION['cart']['productName']);

      if ($productPosition !== false)
      {
         $_SESSION['cart']['productQty'][$productPosition] += $productQty ;
      }
      else
      {
         //Sinon on ajoute le produit
         array_push( $_SESSION['cart']['productName'],$productName);
         array_push( $_SESSION['cart']['productQty'],$productQty);
         array_push( $_SESSION['cart']['productPrice'],$productPrice);
         @array_push( $_SESSION['cart']['productId'],$productId);
         
      }
   }
   else
   echo "Un problème est survenu veuillez contacter l'administrateur du site.";
}


/* * * * * * * * * * * * * * * * * * * * * * * *
 *       Modifie la quantité d'articles        *
 * * * * * * * * * * * * * * * * * * * * * * * */
function modifyArticleQty($productName,$productQty){
   //Si le panier existe
   if (createCart() && !isLocked())
   {
      //Si la quantité est positive on modifie sinon on supprime l'article
      if ($productQty > 0)
      {
         //Recharche du produit dans le panier
         $productPosition = array_search($productName,  $_SESSION['cart']['productName']);

         if ($productPosition !== false)
         {
            $_SESSION['cart']['productQty'][$productPosition] = $productQty ;
         }
      }
      else
      deleteArticle($productName);
   }
   else
   echo "Un problème est survenu veuillez contacter l'administrateur du site.";
}

/* * * * * * * * * * * * * * * * * * * * * * * *
 *        Supprime un article du panier        *
 * * * * * * * * * * * * * * * * * * * * * * * */
function deleteArticle($productName){
   //Si le panier existe
   if (createCart() && !isLocked())
   {
      //Panier temporaire
      $tmp=array();
      $tmp['productName'] = array();
      $tmp['productQty'] = array();
      $tmp['productPrice'] = array();
      $tmp['lock'] = $_SESSION['cart']['lock'];

      for($i = 0; $i < count($_SESSION['cart']['productName']); $i++)
      {
         if ($_SESSION['cart']['productName'][$i] !== $productName)
         {
            array_push( $tmp['productName'],$_SESSION['cart']['productName'][$i]);
            array_push( $tmp['productQty'],$_SESSION['cart']['productQty'][$i]);
            array_push( $tmp['productPrice'],$_SESSION['cart']['productPrice'][$i]);
         }

      }
      //On remplace le panier en session par notre panier temporaire à jour
      $_SESSION['cart'] =  $tmp;
      //On efface notre panier temporaire
      unset($tmp);
   }
   else
   echo "Un problème est survenu veuillez contacter l'administrateur du site.";
}


/* * * * * * * * * * * * * * * * * * * * * * * *
 *      Calcul le montant total du panier      *
 * * * * * * * * * * * * * * * * * * * * * * * */
function globalPrice(){
   $total=0;
   for($i = 0; $i < count($_SESSION['cart']['productName']); $i++)
   {
      $total += $_SESSION['cart']['productQty'][$i] * $_SESSION['cart']['productPrice'][$i];
   }
   return $total;
}


/* * * * * * * * * * * * * * * * * * * * * * * *
 *             Supprime le panier              *
 * * * * * * * * * * * * * * * * * * * * * * * */
function deleteCart(){
   unset($_SESSION['cart']);
}


/* * * * * * * * * * * * * * * * * * * * * * * *
 *    Vérifie si le panier est verrouillé      *
 * * * * * * * * * * * * * * * * * * * * * * * */
function isLocked(){
   if (isset($_SESSION['cart']) && $_SESSION['cart']['lock'])
   return true;
   else
   return false;
}

/* * * * * * * * * * * * * * * * * * * * * * * *
 *  Compte le nombre d'articles dans le panier *
 * * * * * * * * * * * * * * * * * * * * * * * */
function countArticles()
{
   if (isset($_SESSION['cart']))
   return count($_SESSION['cart']['productName']);
   else
   return 0;

}

/* * * * * * * * * * * * * * * * * * * * * * * *
 *  Valide la transaction et envoie à la BDD   *
 * * * * * * * * * * * * * * * * * * * * * * * */
function validateTransaction(){
    include('db-connection.php');
    date_default_timezone_set("Europe/Paris"); 
    $productArray = array();
    $quantityArray = array();
    $nbArticles=count($_SESSION['cart']['productName']);
    if ($nbArticles <= 0){
        echo "<tr><td>Votre panier est vide </ td></tr>";
    }
    else
    {
        for ($i=0 ;$i < $nbArticles ; $i++)
        {   
            array_push($productArray, $_SESSION['cart']['productId'][$i]);
            array_push($quantityArray, $_SESSION['cart']['productQty'][$i]);
        }
        
        $productList = implode(",", $productArray);
        $quantityList = implode(",", $quantityArray);
        $timestamp = time();
        //Préparer la requête
        $query = "INSERT INTO"
            . " transactions (timestamp,user,article_list,quantity_list,status)"
            . " VALUES (?,?,?,?,?)";
        $prep = $pdo->prepare($query);
        
        $prep->bindValue(1, $timestamp, PDO::PARAM_INT);
        $prep->bindValue(2, $_SESSION['email_user'], PDO::PARAM_STR);
        $prep->bindValue(3, $productList, PDO::PARAM_STR);
        $prep->bindValue(4, $quantityList, PDO::PARAM_STR);
        $prep->bindValue(5,"Validation en cours", PDO::PARAM_STR); 
        //Compiler et exécuter la requête
        $prep->execute();
        //Clore la requête préparée
        $prep->closeCursor();
        $prep = NULL;
        deleteCart();
    }
        
}

?>