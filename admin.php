<?php require 'functions/check-cookies.php';
require'functions/db-connection.php';

if (!isset($_SESSION['first_name']))
    echo('Accès refusé.');

if(isset($_GET['id']) and isset($_GET['status'])){
    $status = $_GET['status'];
    $id =  $_GET['id'];
    if($status == 'Validée' or $status =='Annulée'){
        $query = "UPDATE transactions"
        . " SET status = ?"
        . " WHERE id = ?";
        $prep = $pdo->prepare($query);
        $prep->bindValue(1, $status, PDO::PARAM_STR);
        $prep->bindValue(2, $id, PDO::PARAM_STR);
        $prep->execute();
    }
    else{
                echo '<div class="alert alert-danger login-register-alert">
    <strong>Oups !</strong> Ce statut est incorrect !
    </div>';
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
                                <div class="pull-right settings-button">
                                    <div class="btn-group pull-right">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                            <span class="glyphicon glyphicon-cog" style="margin-right: 0px;"></span>
                                        </button>
                                        <ul class="dropdown-menu slidedown">
                                            <li><a href=""><span class="glyphicon glyphicon-ok"></span> Valider</a></li>
                                            <li><a href=""><span class="glyphicon glyphicon-remove"></span> Annuler</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <ul class="list-group">
                                    <?php
                                    $query = "SELECT *"
                                        . " FROM transactions"
                                        . " ORDER BY id DESC";
                                    $prep = $pdo->prepare($query);
                                    
                                    //Compiler et exécuter la requête
                                    $prep->execute();
                                    
                                    foreach ($prep as $row) {
                                    echo'<li class="list-group-item">
                                            <div class="checkbox">
                                                <input type="checkbox" id="checkbox" />
                                                <label for="checkbox" class="command-list-label">
                                                    <div class="col-md-12"><div class="col-md-3">Date: '. date("d/m/Y - H:i:s",$row["timestamp"]). '</div><div class="col-md-4"> Email: '. $row["user"] . '</div><div class="col-md-5">'. $row["status"] .'</div></div>
                                                </label>
                                            </div>
                                            <div class="pull-right action-buttons">
                                                <a href="?id='. $row["id"] .'&status=Validée"><span class="glyphicon glyphicon-ok"></span></a>
                                                <a href="?id='. $row["id"] .'&status=Annulée"><span class="glyphicon glyphicon-remove"></span></a>
                                            </div>
                                        </li>';
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