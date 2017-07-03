<?php

/* * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * *
 * *                                           * *
 * *      Récupère les infos dans la BDD       * * 
 * *      et gère l'impression PDF via la      * *
 * *              librairie FPDF               * *
 * *                                           * *
 * * * * * * * * * * * * * * * * * * * * * * * * *
 * * * * * * * * * * * * * * * * * * * * * * * * */
require('assets/fpdf/fpdf.php');
date_default_timezone_set("Europe/Paris"); 

if(!isset($_SESSION['first_name']))
{
    echo'Veuillez vous connecter pour accéder à cette page';
}


else
{
    
    class PDF extends FPDF
    {
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * *
         *    Gère l'impression du titre de la facture         *
         * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        function Header()
        {
            global $titre;
            
            // Arial gras 15
            $this->SetFont('Arial','B',15);
            // Calcul de la largeur du titre et positionnement
            $w = $this->GetStringWidth($titre)+6;
            $this->SetX((210-$w)/2);
            // Couleurs du cadre, du fond et du texte
            $this->SetDrawColor(0,0,0);
            $this->SetFillColor(195,195,195);
            $this->SetTextColor(0,0,0);
            // Epaisseur du cadre (1 mm)
            $this->SetLineWidth(1);
            // Titre
            $this->Cell($w,9,$titre,1,1,'C',true);
            // Saut de ligne
            $this->Ln(30);
        }
        
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * *
         *    Imprime le numéro de page en pied de page        *
         * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        function Footer()
        {
            // Positionnement à 1,5 cm du bas
            $this->SetY(-15);
            // Arial italique 8
            $this->SetFont('Arial','I',8);
            // Couleur du texte en gris
            $this->SetTextColor(128);
            // Numéro de page
            $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
        }
        
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * *
         *    Ajoute l'adresse de l'entreprise TimeToShoes     *
         * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        function AddTimeToShoesAddress(){
            $this->SetFont('Arial','B',10);
            $this->SetTextColor(0);
            
            $this->AddPage();
            $this->Cell(0,5,"TimeToShoes SARL",0,1);
            $this->SetFont('Arial','',10);
            $this->SetTextColor(0);
            $this->Cell(0,5,"21, Boulevard Francois Grosso",0,1);
            $this->Cell(0,5,"06000 NICE",0,1);
        }
        
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * *
         *          Ajoute l'adresse de l'utilisateur          *
         * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        function AddCustomerAddress($id)
        {
            $email = $_SESSION['email_user'];
            include('db-connection.php');
            //Préparer la requête
            $query = "SELECT timestamp, article_list, quantity_list"
                . " FROM transactions"
                . " WHERE user = ?"
                . " AND id = ?";
            $prep = $pdo->prepare($query);
            $prep->bindValue(1, $email, PDO::PARAM_STR);            
            $prep->bindValue(2, $id, PDO::PARAM_INT);
            
            //Compiler et exécuter la requête
            $prep->execute();
            
            $data = $prep->fetch(PDO::FETCH_ASSOC);
            $timestamp= $data["timestamp"];
            
            $first_name = utf8_decode($_SESSION['first_name']);
            $last_name = utf8_decode($_SESSION['last_name']);
            $delivery_address= utf8_decode($_SESSION['delivery_address']);
            $delivery_town= utf8_decode($_SESSION['delivery_town']);
            $this->SetFont('Arial','B',10);
            $this->SetTextColor(0);
            $this->Cell(0,5,"Adresse de livraison:",0,1,'R');
            $this->SetFont('Arial','',10);
            $this->SetTextColor(0);
            $this->Cell(0,5,$first_name." ".$last_name,0,1,'R');
            $this->Cell(0,5,$delivery_address,0,1,'R');
            $this->Cell(0,5,$delivery_town,0,1,'R');
            $this->Ln(10);
            $this->Cell(0,5,"Date et heure de la commande: ". date("d/m/Y - H:i:s",$timestamp),0,1,'R');
            $this->Ln(40);
        }
        
        /* * * * * * * * * * * * * * * * * * * * * * * * * * * *
         *     Récupère la liste des articles dans la BDD      *
         *                     et l'imprime                    *
         * * * * * * * * * * * * * * * * * * * * * * * * * * * */
        function PrintArticleList($id)
        {   
            $email = $_SESSION['email_user'];
            include('db-connection.php');
            //Préparer la requête
            $query = "SELECT article_list, quantity_list"
                . " FROM transactions"
                . " WHERE user = ?"
                . " AND id = ?";
            $prep = $pdo->prepare($query);
            $prep->bindValue(1, $email, PDO::PARAM_STR);
            $prep->bindValue(2, $id, PDO::PARAM_INT);
            
            //Compiler et exécuter la requête
            $prep->execute();
            
            $data = $prep->fetch(PDO::FETCH_ASSOC);
            $article_list   = $data["article_list"];
            $quantity_list  = $data["quantity_list"];
            // Mise en place des headers du file_get_content 
            //pour obtenir les valeurs françaises
            $opts = [
                "http" => [
                    "method" => "GET",
                    "header" => "Accept-language: fr-FR\r\n"
                ]
            ];
            
            $context = stream_context_create($opts);
            $article_array = explode(",", $article_list);
            $quantity_array = explode(",", $quantity_list);
            $totalPrice = 0;
            $i=0;
            //Boucle sur chaque article de la liste et print le nom et le prix
            foreach($article_array as $article){
                $json_source = file_get_contents('https://api.zalando.com/articles/'.$article,false, $context);
                
                $json_data = json_decode($json_source);
                $cutted_name = strlen($json_data->name) > 50 ? substr($json_data->name,0,50)."..." : $json_data->name;
                $this->Ln(10);
                $this->Cell(110,5,utf8_decode($cutted_name),0,0);
                $this->Cell(8,5,$json_data->units[0]->price->value,0,0); 
                $this->Cell(15,5," euros",0,0);
                $this->Cell(15,5," X ",0,0);
                $this->Cell(15,5,$quantity_array[$i],0,0);
                $this->Cell(15,5," = ",0,0);
                $totalUnitPrice=$json_data->units[0]->price->value;
                $totalUnitPrice*= $quantity_array[$i];
                $this->Cell(15,5,$totalUnitPrice,0,0);
                $totalPrice +=$totalUnitPrice;
                $i++;
            }
            
            $this->Ln(30);
            $this->SetFont('Arial','B',15);
            $this->Cell(150,5,"TOTAL: ".$totalPrice." euros" ,0,0,'R');
            
        }
    }
    
    function PrintUserPDF($id){
        
        global $titre;
        $email = $_SESSION['email_user'];
        include('db-connection.php');
        //Préparer la requête
        $query = "SELECT id, status"
            . " FROM transactions"
            . " WHERE user = ?";
        $prep = $pdo->prepare($query);
        $prep->bindValue(1, $email, PDO::PARAM_STR);
        
        //Compiler et exécuter la requête
        $prep->execute();
        foreach($prep as $row){
            //Vérifie que la commande appartient bien à l'utilisateur et a été validée
            if ($id == $row['id'] and $row['status'] == 'Validée'){
                $pdf = new PDF();
               //Définit le titre et le met en place sur la fenêtre
               $titre = 'Facture TimeToShoes du ' . date("d-m-Y") . " a " . date("H:i:s");
               $pdf->SetTitle($titre);
               
               //Définit l'auteur
               $pdf->SetAuthor('TimeToShoes');
               
               //Ajout de l'adresse de livraison du client et sa liste d'achats
               $pdf->AddTimeToShoesAddress();
               $pdf->AddCustomerAddress($id);
               $pdf->PrintArticleList($id);
               
               //Sort le PDF
                $pdf->Output();
            }
            else{
            }
        }
    }
}

?>