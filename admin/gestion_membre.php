<?php
require_once('../inc/init.inc.php');

if (!adminConnect()) {
    header('location: ' . URL . 'connexion.php');
}

if (isset($_GET['action']) && $_GET['action'] == "suppression") {

    $delete = $bdd->prepare("DELETE FROM  membre WHERE id_membre = :id_membre");

    $delete->bindValue(':id_membre', $_GET['id_membre'], PDO::PARAM_INT);

    $delete->execute();

    $_GET['action'] = 'affichage';
    $validationDelete = "<p class='col-md-6 mx-auto bg-success text-center text-white p-3 rounded'>Le membre<strong><ID $_GET[id_membre]></strong> à bien été supprimé!</p>";
}

if ($_POST) {

    if (isset($_GET['action']) && $_GET['action'] == "ajout") {
        $data = $bdd->prepare("INSERT INTO membre(pseudo,nom,prenom,email,civilite,ville,code_postale,adresse,statut)VALUES(:pseudo,:nom, :prenom, :email, :civilite, :ville, :code_postale, :adresse, :statut)");

        $_GET['action'] = 'affichage';
    } else {
        //sinon action modification
        $data = $bdd->prepare("UPDATE membre set pseudo = :pseudo, nom = :nom, prenom = :prenom, email = :email, civilite = :civilite, ville = :ville,code_postale = :code_postale, adresse = :adresse, statut = :statut WHERE id_membre = :id_membre");
        $data->bindValue(':id_membre', $_GET['id_membre'], PDO::PARAM_INT);

        $_GET['action'] = 'affichage';
    }


    if($_POST)
    {
        //Dans tt les cas bind + execution
        $data->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
        $data->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
        $data->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
        $data->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
        $data->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
        $data->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
        $data->bindValue(':code_postale', $_POST['code_postale'], PDO::PARAM_INT);
        $data->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
        $data->bindValue(':statut', $_POST['statut'], PDO::PARAM_INT);

        $data->execute();

        $vUpdt = "<p class= 'col-md-3 mx-auto bg-success text-center text-white p-3 rounded my-4'>Le membre Id n° $_GET[id_membre] a bien été modifié!</p>";

        $_GET['action'] = 'affichage';
    }

    
}

require_once('../inc/header.inc.php');
require_once('../inc/nav.inc.php');

?>
    <ul class="col-md-4 mx-auto list-group text-center mt-5">
        <li class="list-group-item bg-dark text-white">BACK OFFICE</li>
        <li class="list-group-item bg-secondary"><a href="?action=affichage" class="col-md-8 btn btn-dark p-2">AFFICHAGE MEMBRE</a></li>

    </ul>
<?php
    echo "<h1 class='text-center text-secondary'> LISTE MEMBRE</h1>";
if (isset($_GET['action']) && $_GET['action'] == 'affichage') {



    if (isset($validationDelete)) echo $validationDelete;
    if(isset($vUpdt)) echo $vUpdt;

//     //CONDITION AFFICHAGE NOMBRE MEMBRE
// $n = $bdd->query("SELECT * FROM membre WHERE statut = 1");
//  if($data->rowCount() == 1)
//  $txtM = 'membre enregistré';
//  else
//  $textM = 'membre enregistré';


//  if($n->rowCount()==1)
//  $textA = 'adminitrateur';
//  else
//  $textA = 'administrateurs';

 ?>


<?php



    $data = $bdd->query("SELECT id_membre, pseudo, nom, prenom, email,civilite,ville, code_postale, adresse, statut FROM membre");
    echo '<table class=" col-md-8 mx-auto p-5 table table-bordered bg-secondary text-white text-center"><tr>';

    for ($i = 0; $i < $data->columnCount(); $i++) {
        $c = $data->getColumnMeta($i);
        echo "<th>" . strtoupper($c['name']) . "</th>";
    }
    echo "<th>MODIFIER</th>";
    echo "<th>SUPPRIMER</th>";


    echo '</tr>';
    while ($p = $data->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        foreach ($p as $k => $value)
         {
           
            if ($k == 'statut') {
                echo "<td class='align-middle '>$value</td>";
            } else {
                echo "<td class='align-middle'>$value</td>";
            }
            
        }


        echo "<td class='align-middle'><a href='?action=modification&id_membre=$p[id_membre]' class='btn btn-light'><i class='fas fa-edit'></i></a></td>";


        echo "<td class='align-middle'><a href='?action=suppression&id_membre=$p[id_membre]' class='btn btn-danger'onclick='return(confirm(\"Êtes vous sûr de vouloir supprimer?\"));'><i class='far fa-trash-alt'></i></a></td>";

        echo '</tr>';
    }



    echo '</table>';
}


/*if (isset($_GET['action']) && $_GET['action'] == "suppression")
{
$positionMembre = array_search($_GET['id_membre'], $_SESSION['id_membre']);

$vd = "<p class='col-md-6 mx-auto bg-success text-center text-white p-3 rounded'>Le produit titre <strong>" .$_SESSION['id_membre'][$positionMembre] .".<strong> a bien été retiré du panier !</p>";

suppMembre($_GET['id_membre']);


}*/

if (isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) :

    //On selectionne tout dns la BDD à condition que id_prp = à l'id_prod dns l'url
    //On selectionne toutes les données en BDD du produit que l'on souhaite modifier

    if (isset($_GET['id_membre']) && !empty($_GET['id_membre']) && $_GET['action']) {
        $requete = $bdd->prepare("SELECT* FROM membre WHERE id_membre = :id_membre");
        $requete->bindValue(':id_membre', $_GET['id_membre'], PDO::PARAM_INT);
        $requete->execute();


        //Si la requête SELECT retourne 1 resultat, le produit est connu en BDD; on entre dns la condition IF

        if ($requete->rowCount()) {
            $pa = $requete->fetch(PDO::FETCH_ASSOC);
            /* echo '<pre>';
            print_r($pa);
            echo '</pre>';
            */
        } else
        //Sinon l'id_produit de l'URL n'est pas connu en BDD, on redirige vers l'affichage des produits 

        {
            header('location:' . URL . 'admin/gestion_boutique.php?action=affichage'); /////////////////////////////////////////////////////////////////
        }
    } elseif ($_GET['action'] == 'modification ' && (!isset($_GET['id_membre']) || empty($_GET['id_membre'])))
    //Sinon l'indice id_produit
    {
        header('location:' . URL . 'admin/gestion_boutique.php?action=affichage'); //////////////////////////////////////////////////////////////////////
    }
    //on stock la ref du prod selsctionné en BDD dns la variable $reference afin de l'affecter à
    //l'attribut 'value' du champs reference comme valeur par defaut
    $pseudo = (isset($pa['pseudo'])) ? $pa['pseudo'] : '';
    $nom = (isset($pa['nom'])) ? $pa['nom'] : '';
    $prenom = (isset($pa['prenom'])) ? $pa['prenom'] : '';
    $email = (isset($pa['email'])) ? $pa['email'] : '';
    $civilite = (isset($pa['civilite'])) ? $pa['civilite'] : '';
    $ville = (isset($pa['ville'])) ? $pa['ville'] : '';
    $code_postale = (isset($pa['code_postale'])) ? $pa['code_postale'] : '';
    $adresse = (isset($pa['adresse'])) ? $pa['adresse'] : '';
    $statut = (isset($pa['statut'])) ? $pa['statut'] : '';



?>
    <!-- -->

    <!--On va crocheter à l'indice 'action dns l'url afin de modofier le titre en fonction d'un
    'ajout' ou d'une modif de produit
       ucfirst() : fonction prédéfinie permettant d'afficher la première lettre d'une chaine de caractères en majuscule
    -->



    <h1 class=" text-center text-info"><?= ucfirst($_GET['action']) ?> MEMBRE</h1>
    <form method="post" class="p-4 m-4 col-md-5 mx-auto table-bordered border border-dark table-rounded bg-secondary text-center text-white rounded">
        <div class="form-group">
            <label for="pseudo">Pseudo</label>
            <input class="bg-info" type="text" class="form-control" id="pseudo" name="pseudo" placeholder="" value="<?= $pseudo ?>">
        </div>
        <div class="form-group">
            <label for="nom">Nom</label>
            <input class="bg-info" type="text" class="form-control" id="nom" name="nom" value="<?= $nom ?>">
        </div>
        <div class=" form-group">
            <label for="prenom">Prenom</label>
            <input class="bg-info" type="text" class="form-control" id="prenom" name="prenom" value="<?= $prenom ?>">
            <!-- -->
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input class="bg-info" type="text" class="form-control" id="email" name="email" value="<?= $email ?>">
        </div>
        <div class="form-group">
            <label for="civilite">Civilité</label>
            <input class="bg-info" type="text" class="form-control" id="civilite" name="civilite" value="<?= $civilite ?>">
        </div>
        <div class="form-group">
            <label for="ville">Ville</label>
            <input class="bg-info" type="text" class="form-control" id="ville" name="ville" value="<?= $ville ?>">
        </div>
        <div class="form-group">
            <label for="code_postale">Code Postale</label>
            <input class="bg-info" type="text" class="form-control" id="code_postale" name="code_postale" value="<?= $code_postale ?>">
        </div>
        <div class="form-group">
            <label for="adresse">Adresse</label>
            <input class="bg-info" type="text" class="form-control" id="adresse" name="adresse" value="<?= $adresse ?>">
        </div>
        <div class="form-group">
            <label for="statut">Statut</label>
            <input class="bg-info" type="text" class="form-control" id="statut" name="statut" value="<?= $statut ?>">
        </div>



        <div class="row justify-content-center">
            <button type="submit" class=" col-md-5 btn btn-dark mx-auto text-info "><?= strtoupper($_GET['action']) ?> INSCRIPTION</button>

        </div>
    </form>






<?php
endif;
require_once('../inc/footer.inc.php');
?>