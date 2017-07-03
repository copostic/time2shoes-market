<?php
/* * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * *
 * *                                           * *
 * *             Affiche le header             * *
 * *           et gère les catégories          * *
 * *                                           * *
 * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * */
if(isset($_GET['action'])){
    $action=$_GET['action'];
    if($action == 'logout'){
        $_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}


setcookie('token', '');
// Finalement, on détruit la session.
session_destroy();
    }
}


$valid_categories = array("chaussures", "chaussures-homme","chaussures-femme","chaussures-enfant", "sport-chaussures-enfant", "chaussures-sport-enfant","baskets-classiques-ado", "baskets-enfant","baskets-homme", "baskets-montantes-homme", "baskets-basses-homme", "sport-chaussures-femme", "chaussures-sport-femme", "escarpins-femme", "escarpins-classiques-femme", "escarpins-talons-hauts-femme", "baskets-basses-femme", "baskets-montantes-femme", "boots-bottines-femme", "bottines-classiques-femme");

if(isset($_GET['category']) and in_array($_GET['category'], $valid_categories)){
    if(isset($_GET['pagesize'])){
        if(isset($_GET['pagenumber'])){ //if category, pagesize and pagenumber are sets
            echo"<script type='text/javascript'> $(document).ready(function () {getArticles(". $_GET['pagenumber'] . ", " . $_GET['pagesize'] . ", '" . $_GET['category'] . "');})</script>";
        }
        else{//if category and pagesize set but not pagenumber
            echo"<script type='text/javascript'> $(document).ready(function () {getArticles(1, " . $_GET['pagesize'] . ", '" . $_GET['category'] . "');})</script>";
        }
    }
    else{//if category set but not pagesize and pagenumber
        echo"<script type='text/javascript'> $(document).ready(function () {getArticles(1, 25, '" . $_GET['category'] . "');})</script>";
    }
}
else{//if category, pagesize and pagenumber not set
    echo"<script type='text/javascript'> $(document).ready(function () {getArticles(1, 25, 'chaussures');})</script>";
}
?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php">TimeToShoes</a>
            </div>
            <ul class="nav navbar-nav">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="index.php?category=chaussures">Tous</a></li>
                <li><a href="index.php?category=chaussures-homme">Homme</a></li>
                <li><a href="index.php?category=chaussures-femme">Femme</a></li>
                <li><a href="index.php?category=chaussures-enfant">Enfant</a></li>
            </ul>
            <form class="navbar-form navbar-right">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Ex: chaussures">
                </div>
                <button type="submit" class="btn btn-default">Rechercher</button>
            </form>
            <ul class="nav navbar-nav navbar-right">
                <?php if(!isset($_SESSION['first_name'])){ ?>
                    <li><a href="register.php"><span class="glyphicon glyphicon-user"></span> Inscription</a></li>
                    <li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Connexion</a></li>
                    <?php } 
            else{ 
            ?>

                        <li><a href="member.php">Bonjour <?php echo $_SESSION['first_name']; ?> !</a></li>
                        <li><a href="?action=logout"><span class="glyphicon glyphicon-log-out"></span> Deconnexion</a></li>

                        <?php } ?>
            </ul>
        </div>
    </nav>