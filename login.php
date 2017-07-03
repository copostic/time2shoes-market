<?php require 'functions/check-cookies.php';
require('functions/db-connection.php');


if(isset($_POST['form-username']) and isset($_POST['form-password'])){
$email= $_POST['form-username'];
$password= $_POST['form-password'];
$token = bin2hex(openssl_random_pseudo_bytes(16));

//Préparer la requête
$query = "SELECT first_name, last_name, password, delivery_address, delivery_town, billing_address, billing_town"
	. " FROM users"
	. " WHERE email = ?";
$prep = $pdo->prepare($query);

$prep->bindValue(1, $email, PDO::PARAM_STR);
 
//Compiler et exécuter la requête
$prep->execute();

$data = $prep->fetch(PDO::FETCH_ASSOC);
$encrypted_password = $data["password"];

//Si le password est bon, créé les variables de session
//Et envoie le nouveau token à la base de donnée
if (password_verify($password, $encrypted_password)) {
    $_SESSION['first_name']         = $data["first_name"];
    $_SESSION['last_name']          = $data["last_name"]; 
    $_SESSION['token']              = $token;
    $_SESSION['delivery_address']   = $data["delivery_address"];
    $_SESSION['delivery_town']      = $data["delivery_town"];
    $_SESSION['billing_address']    = $data["billing_address"];
    $_SESSION['billing_town']       = $data["billing_town"];
    $_SESSION['email_user']         = $email;
    echo 'Connexion authorized';
    $query = "UPDATE users"
        . " SET token = ?"
        . " WHERE email = ?";
    $prep = $pdo->prepare($query);
    $prep->bindValue(1, $token, PDO::PARAM_STR);
    $prep->bindValue(2, $email, PDO::PARAM_STR);
    $prep->execute();
    setcookie('token', $token, time() + 365*24*3600);
    header('Location:index.php');
} 
else {
    echo '<div class="alert alert-danger login-register-alert">
  <strong>Connexion impossible !</strong> Veuillez vérifier votre identifiant/mot de passe.
</div>';
}
 
//Clore la requête préparée
$prep->closeCursor();
$prep = NULL;
    
    }
?>

    <!DOCTYPE html>
    <html lang="fr">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Connexion | TimeToShoes</title>
        <meta name="author" content="216338 | Corentin POSTIC">

        <!-- CSS -->
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="assets/css/form-elements.css">
        <link rel="stylesheet" href="assets/css/style-login-register.css">
        <link rel="stylesheet" href="assets/css/main.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body class="login-register">

        <!-- Top content -->

        <?php require 'functions/header.php'; ?>

            <div class="container">
                <div class="sign-up col-sm-12">
                    <div class="form-box">
                        <div class="form-top">
                            <div class="form-top-left">
                                <h3>Connxion à l'espace membre</h3>
                                <p>Entrez votre email et votre mot de passe pour vous connecter:</p>
                            </div>
                            <div class="form-top-right">
                                <i class="fa fa-key"></i>
                            </div>
                        </div>
                        <div class="form-bottom">
                            <form role="form" action="login.php" method="post" class="login-form">
                                <div class="form-group">
                                    <label class="sr-only" for="form-email">Email</label>
                                    <input type="text" name="form-username" placeholder="Email..." class="form-email form-control" id="form-email">
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="form-password">Mot de Passe</label>
                                    <input type="password" name="form-password" placeholder="Mot de Passe..." class="form-password form-control" id="form-password">
                                </div>
                                <button type="submit" class="btn form-button">Me Connecter !</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php require 'functions/footer.php'; ?>
                <!-- Javascript -->

                <!-- Bootstrap core JavaScript -->
                <script src="assets/js/jquery.min.js"></script>
                <script src="assets/js/bootstrap.min.js"></script>
                <script src="assets/js/scripts.js"></script>

                <!--[if lt IE 10]>
            <script src="assets/js/placeholder.js"></script>
        <![endif]-->

    </body>

    </html>