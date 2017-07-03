<?php 
require_once('functions/check-cookies.php');
header("Access-Control-Allow-Origin: https://api.zalando.com/*"); ?>

    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Bienvenue sur la page d'accueil du meilleur site de vente de chaussures !">
        <meta name="author" content="216338 | Corentin POSTIC">

        <title>Accueil | TimeToShoes</title>

        <!-- JS nécessaire au bon affichage de la page -->
        <script src="assets/js/api-management.js"></script>
        <script src="assets/js/jquery.min.js"></script>

        <!-- Feuilles CSS -->
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
        <link rel="stylesheet" href="assets/css/main.css">
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">


    </head>

    <body class="main-body">

        <?php require_once 'functions/header.php'; ?>


            <div class="container">
            </div>
            <div class="page-number">

            </div>
            <?php require_once 'functions/footer.php'; ?>

                <!-- JavaScript -->
                <script src="assets/js/bootstrap.min.js"></script>
                <script src="assets/js/scripts.js"></script>

    </body>

    </html>