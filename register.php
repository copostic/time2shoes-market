<?php 
require_once'functions/check-cookies.php';

if(isset($_POST['form-first-name']) and isset($_POST['form-last-name']) and isset($_POST['form-email']) and isset($_POST['form-password']) and isset($_POST['form-delivery-address']) and isset($_POST['form-delivery-town']) and isset($_POST['form-billing-address']) and isset($_POST['form-billing-town'])){
    include('functions/db-connection.php');

    $fname= $_POST['form-first-name'];
    $lname= $_POST['form-last-name'];
    $email= $_POST['form-email'];
    $password= $_POST['form-password'];
    $delivery_address= $_POST['form-delivery-address'];
    $delivery_town = $_POST['form-delivery-town'];
    $billing_address= $_POST['form-billing-address'];
    $billing_town= $_POST['form-billing-town'];
    
    
    //Vérifie si l'user existe déjà dans la BDD
    $query = "SELECT id"
        . " FROM users"
        . " WHERE email = ?";
    $prep = $pdo->prepare($query);
    $prep->bindValue(1, $email, PDO::PARAM_STR);
    $prep->execute();
    $data = $prep->fetch(PDO::FETCH_ASSOC);
    if($data['id'] == ''){
        
    
    
    //Recupere le mot de passe et le chiffre
    $encrypted_password = password_hash($password,PASSWORD_BCRYPT,['cost' => 11]);
    
    //Préparer la requête
    $query = "INSERT INTO"
        . " users (first_name,last_name,email,password,delivery_address,delivery_town,billing_address,billing_town)"
        . " VALUES (?,?,?,?,?,?,?,?)";
    $prep = $pdo->prepare($query);
    
    $prep->bindValue(1, $fname, PDO::PARAM_STR);
    $prep->bindValue(2, $lname, PDO::PARAM_STR);
    $prep->bindValue(3, $email, PDO::PARAM_STR);
    $prep->bindValue(4, $encrypted_password, PDO::PARAM_STR);
    $prep->bindValue(5, $delivery_address, PDO::PARAM_STR);
    $prep->bindValue(6, $delivery_town, PDO::PARAM_STR);
    $prep->bindValue(7, $billing_address, PDO::PARAM_STR);
    $prep->bindValue(8, $billing_town, PDO::PARAM_STR);
    
    //Compiler et exécuter la requête
    $prep->execute();
    
    //Clore la requête préparée
    $prep->closeCursor();
    $prep = NULL;
    
    echo '<div class="alert alert-success login-register-alert">
    <strong>Inscription réussie!</strong> Veuillez maintenant vous connecter pour accéder à toutes les fonctionnalités.
    </div>';
    }//endif email already exist
    else{
        echo '<div class="alert alert-danger login-register-alert">
    <strong>Oups !</strong> Cette adresse email est déjà inscrite sur notre site !
    </div>';
    }
}


?>

    <!DOCTYPE html>
    <html lang="fr">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="216338 | Corentin POSTIC">
        <title>Inscription | TimeToShoes</title>

        <!-- CSS -->
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="assets/css/form-elements.css">
        <link rel="stylesheet" href="assets/css/style-login-register.css">
        <link rel="stylesheet" href="assets/css/main.css">

        <script src="assets/js/jquery.min.js"></script>



        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

    </head>

    <body class="login-register">

        <!-- Top content -->

        <?php require_once'functions/header.php'; ?>
            <div class="container">
                <div class="sign-up col-sm-12">
                    <div class="form-box">
                        <div class="form-top">
                            <div class="form-top-left">
                                <h3>M'inscrire</h3>
                                <p>Remplissez les champs ci-dessous pour accéder à notre fantastique boutique</p>
                            </div>
                            <div class="form-top-right">
                                <i class="fa fa-pencil"></i>
                            </div>
                        </div>
                        <div class="form-bottom">
                            <form role="form" action="register.php" method="post" class="registration-form">
                                <div class="form-group">
                                    <label class="sr-only" for="form-first-name">Prénom</label>
                                    <input type="text" name="form-first-name" placeholder="Prénom..." class="form-first-name form-control" id="form-first-name">
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="form-last-name">Nom</label>
                                    <input type="text" name="form-last-name" placeholder="Nom..." class="form-last-name form-control" id="form-last-name">
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="form-email">Email</label>
                                    <input type="text" name="form-email" placeholder="Email..." class="form-email form-control" id="form-email">
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="form-password">Mot de Passe</label>
                                    <input type="password" name="form-password" placeholder="Mot de Passe..." class="form-password form-control" id="form-password">
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="form-delivery-address">Adresse de Livraison</label>
                                    <input name="form-delivery-address" placeholder="Adresse de Livraison..." class="form-delivery-address form-control" id="form-delivery-address">
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="form-delivery-town">Code postal et ville de livraison</label>
                                    <input name="form-delivery-town" placeholder="Code postal et ville de livraison..." class="form-delivery-town form-control" id="form-delivery-town">
                                </div>
                                <label>
                                    <input type="checkbox" name="check1" onclick="copyTextValue(this);"> Mon adresse de livraison et de facturation sont les mêmes</label>
                                <div class="form-group">
                                    <label class="sr-only" for="form-billing-address">Adresse de Facturation</label>
                                    <input name="form-billing-address" placeholder="Adresse de Facturation..." class="form-billing-address form-control" id="form-billing-address">
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="form-billing-town">Code postal et ville de facturation</label>
                                    <input name="form-billing-town" placeholder="Code postal et ville de facturation..." class="form-billing-town form-control" id="form-billing-town">
                                </div>
                                <button type="submit" class="btn">Inscrivez-moi !</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            <?php require 'functions/footer.php'; ?>

                <!-- JavaScript -->
                <script src="assets/js/jquery.min.js"></script>
                <script src="assets/js/bootstrap.min.js"></script>
                <script src="assets/js/scripts.js"></script>

                <!--[if lt IE 10]>
<script src="assets/js/placeholder.js"></script>
<![endif]-->

    </body>

    </html>