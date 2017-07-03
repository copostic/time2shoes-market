<?php require 'functions/check-cookies.php';
require'functions/db-connection.php';
require'functions/pdf-printer.php';

if (isset($_SESSION['first_name'])){
     if(isset($_GET['action'])){
         $action = $_GET['action'];
         if($action == 'print'){
                 if(isset($_GET['id'])){
                    PrintUserPDF($_GET['id']);   
                 }
             }     
         }
?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="216338 | Corentin POSTIC">

        <title>Member Space | TimeToShoes</title>


        <script src="assets/js/jquery.min.js"></script>
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
        <link rel="stylesheet" href="assets/css/main.css">
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
    </head>

    <body>

        <?php require 'functions/header.php'; ?>

            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-primary article-list">
                            <div class="panel-heading">
                                <h2>Liste des commandes</h2>
                            </div>
                            <div class="panel-body">
                                <ul class="list-group">
                                    <?php
                                    $query = "SELECT *"
                                        . " FROM transactions"
                                        . " WHERE user = ?"
                                        . " ORDER BY id DESC";
                                    $prep = $pdo->prepare($query);
                                    $prep->bindValue(1, $_SESSION['email_user'], PDO::PARAM_STR);
                                    //Compiler et exécuter la requête
                                    $prep->execute();
                                    
                                    foreach ($prep as $row) {
                                    echo'<li class="list-group-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="checkbox3" />
                                                <label for="checkbox3" class="command-list-label">
                                                    <div class="col-md-12"><div class="col-md-6">Date de la commande: '. date("d/m/Y - H:i:s",$row["timestamp"]). '</div><div class="col-md-6">'. $row["status"] .'</div></div>
                                                </label>
                                            </div>';
                                        //Affiche le bouton imprimer seulement si la commande est validée
                                            if($row["status"] == 'Validée'){
                                                echo'
                                            <div class="pull-right action-buttons">
                                                <a href="?action=print&id='. $row["id"] . '" target="_blank" class="flag"><span class="glyphicon glyphicon-print"></span></a>
                                            </div>
                                        </li>';
                                            }
                                    }
                                    
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <?php require 'functions/footer.php'; ?>

                <!-- Bootstrap core JavaScript -->
                <script src="assets/js/jquery.min.js"></script>
                <script src="assets/js/bootstrap.min.js"></script>

    </body>

    </html>
    <?php
}
else{
    }
?>