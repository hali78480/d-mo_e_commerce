<?php
require_once('../inc/init.inc.php');



if (!adminConnect()) {
    header('location: ' . URL . 'connexion.php');
}
//Si l'internaute n'est pas (!) admin il a rien à faire ici et on le redirige vers la page de connexion

if (!adminConnect()) {
    header('location: ' . URL . 'connexion.php');
}

//SUPRESSION
/* ON entre dans la condition IF seulement dans le cas où l'internaute
à cliqué sur un lien suppression produit et par conséquent a transmit dans 
l'URL les paramètres 'action=suppresion'*/


if (isset($_GET['action']) && $_GET['action'] == "suppression") {

    $delete = $bdd->prepare("DELETE FROM  produit WHERE id_produit = :id_produit");

    $delete->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);

    $delete->execute();

    //on redéfinit la valeur de l'indice 'action dans l'url afin d'être redirigé vers l'affichage des produits

    $_GET['action'] = 'affichage';
    $validationDelete = "<p class='col-md-6 mx-auto bg-success text-center text-white p-3 rounded'>Le produit<strong><ID $_GET[id_produit]</strong> à bien été supprimé!</p>";
}


//ENREGISTREMENT PRODUIT
if ($_POST) {



    //TRAITEMENT DE LA PHOTO UPLOADER
    $photoBdd = '';

    if (isset($_GET['action']) && $_GET['action'] == 'modification') {
        //c'est pour que la photo ne parte pas quand on fait une modif 
        $photoBdd = $_POST['photo_actuelle'];
    }

    if (!empty($_FILES['photo']['name']))

    //on renome la photo en concaténant la référence saisie dns le formu et le nom 
    //de la photo récupérer dns $_files
    {
        $nomPhoto = $_POST['reference'] . '-' . $_FILES['photo']['name'];
        // echo $nomPhoto;


        //on definit l'URL de la photo qui sera enreg en BDD
        $photoBdd = URL . "photo/$nomPhoto";
        //echo $photoBdd;


        //on de finit le chemin physique de la photo vers le dossier photo sur le serveur
        //ce qui nous permet de copier l'image dns le bon doss
        $photoDossier = RACINE_SITE . "photo/$nomPhoto";

        // echo $photoDossier;


        /* 
       copy() fonction predefinie permettant de copier un fichier
       -arguments:
       1-le nom de chemin de l'image accessible dns $_files
       2-le chemin physique de la photo jusqu'au dossier photo sur le serveur
       */
        copy($_FILES['photo']['tmp_name'], $photoDossier);
    }



    // si l'indice 'action' est bien définit dns l'url et qu'il a pour valeur 'ajout alors on execute
    //une requete d'insertion à la validation du formu
    if (isset($_GET['action']) && $_GET['action'] == 'ajout') {
        //INSERTION EN BDD
        $data = $bdd->prepare("INSERT INTO produit(reference,categorie,titre,description,couleur,taille,public,photo,prix,stock)VALUES(:reference,:categorie,:titre,:description,:couleur,:taille,:public,:photo,:prix,:stock)");

        $_GET['action'] = 'affichage';

        $v = "<p class='col-md-6 mx-auto bg-success text-center text-white p-3 rounded'> Le produit <strong> $_POST[titre]</strong> référence <strong> $_POST[reference]</strong> a bien été enregistré!</p>";
    } else // sinon dns l'url il y 'a 'action=modification', alors on execute une requete de modif update

    {

        //UPDATE BDD PRODUIT
        $data = $bdd->prepare("UPDATE produit SET reference = :reference, categorie = :categorie,titre = :titre, description = :description, couleur = :couleur, taille = :taille, public = :public, photo = :photo, prix = :prix, stock= :stock WHERE  id_produit = :id_produit");

        $data->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);

        $_GET['action'] = 'affichage';

        $v = "<p class='col-md-6 mx-auto bg-success text-center text-white p-3 rounded p-3'> Le produit $_POST[titre] référence $_POST[reference] a bien été modifié!</p>";


        //$data->execute();

    }



    //$bdd = new PDO('mysql:host=localhost;dbname=boutique', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));



    $data->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
    $data->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
    $data->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
    $data->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
    $data->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
    $data->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
    $data->bindValue(':public', $_POST['public'], PDO::PARAM_STR);
    $data->bindValue(':photo', $photoBdd, PDO::PARAM_STR);
    $data->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
    $data->bindValue(':stock', $_POST['stock'], PDO::PARAM_INT);

    $data->execute();
}


/*echo '<pre>';
print_r($_POST);
echo '</pre>';


echo '<pre>';
print_r($_FILES);
echo '</pre>';
*/


require_once('../inc/header.inc.php');
require_once('../inc/nav.inc.php');
?>
<!--------------------------------------------------------------------------------------
AFFICHAGE DES PRODUITS
LIEN PRODUIT EN HTML-->



<ul class="col-md-4 mx-auto list-group text-center mt-5">
    <li class="list-group-item bg-dark text-white">BACK OFFICE</li>
    <li class="list-group-item bg-secondary"><a href="?action=affichage" class="col-md-8 btn btn-dark p-2">AFFICHAGE PRODUIT</a></li>
    <li class="list-group-item bg-secondary"><a href="?action=ajout" class="col-md-8 btn btn-dark p-2">AJOUT PRODUIT</a></li>

</ul>
<?php
if (isset($_GET['action']) && $_GET['action'] == 'affichage') {

    /* SI l'indice 'action' est bien définit dans l'URL et qu'il a pour valeur 
'affichage', cela veut dire que l'internaute a cliqué sur le lien 'AFFICHAGE PRODUITS'
 et par conséquent que les paramètres 'action=affichage' ont été transmit dans l'URL*/
    if (isset($vd)) echo $vd;
    if (isset($v)) echo $v;
    //-------------------------------------------------------------------------
    echo "<h1 class='text-center text-info'>Affichage des produit</h1>";
?>
<?php

    //DEBUT DU TABLEAU


    if (isset($validationDelete)) echo $validationDelete;
    $r = $bdd->query("SELECT* FROM  produit");

    echo '<table class="table table-bordered bg-secondary text-white text-center"><tr>';

    for ($i = 0; $i < $r->columnCount(); $i++) {
        $c = $r->getColumnMeta($i);
        echo "<th>" . strtoupper($c['name']) . "</th>";
    }

    echo "<th>MODIFIER</th>";
    echo "<th>SUPPRIMER</th>";

    echo '</tr>';

    //fin de ligne du haut
    //------------------------------------------------------------------------------
    while ($p = $r->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        foreach ($p as $k => $value) {
            if ($k == 'photo') {
                echo "<td><img src='$value' alt=''style='width:100px;'></td>";
            } else {
                echo "<td class='align-middle'>$value</td>";
            }
        }

        echo "<td class='align-middle'><a href='?action=modification&id_produit=$p[id_produit]' class='btn btn-dark'><i class='fas fa-edit'></i></a></td>";


        echo "<td class='align-middle'><a href='?action=suppression&id_produit=$p[id_produit]' class='btn btn-danger'onclick='return(confirm(\"Êtes vous sûr de vouloir supprimer?\"));'><i class='far fa-trash-alt'></i></a></td>";

        echo '</tr>';
    }



    echo '</table>';
}
?> <?php

    /*
    enctype="multipart/form-data: Si le formulaire contient uplaod de fichier il ne faut oublier l'attribut enctype
et la valeur multipart /form-data qui permettent de stocker les info du fichier uploder directement dns la superglobale
$_FILE (type, nom, extension,nom temporaire
*/

    //Si l'indice id_pro est bien défini dns l'url et que sa valeur est différente de vide, alors on entre dns la condition IF

    if (isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) :

        //On selectionne tout dns la BDD à condition que id_prp = à l'id_prod dns l'url
        //On selectionne toutes les données en BDD du produit que l'on souhaite modifier

        if (isset($_GET['id_produit']) && !empty($_GET['id_produit']) && $_GET['action']) {
            $requete = $bdd->prepare("SELECT* FROM produit WHERE id_produit = :id_produit");
            $requete->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
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
                header('location:' . URL . 'admin/gestion_boutique.php?action=affichage');
            }
        } elseif ($_GET['action'] == 'modification ' && (!isset($_GET['id_produit']) || empty($_GET['id_produit'])))
        //Sinon l'indice id_produit
        {
            header('location:' . URL . 'admin/gestion_boutique.php?action=affichage');
        }
        //on stock la ref du prod selsctionné en BDD dns la variable $reference afin de l'affecter à
        //l'attribut 'value' du champs reference comme valeur par defaut
        $reference = (isset($pa['reference'])) ? $pa['reference'] : '';
        $categorie = (isset($pa['categorie'])) ? $pa['categorie'] : '';
        echo "DEBUG MODE";
        //echo $pa['titre'];
        $titre = (isset($pa['titre'])) ? $pa['titre'] : '';
        //echo $titre;
        $description = (isset($pa['description'])) ? $pa['description'] : '';
        $couleur = (isset($pa['couleur'])) ? $pa['couleur'] : '';
        $taille = (isset($pa['taille'])) ? $pa['taille'] : '';
        $public = (isset($pa['public'])) ? $pa['public'] : '';
        $photo = (isset($pa['photo'])) ? $pa['photo'] : '';
        $prix = (isset($pa['prix'])) ? $pa['prix'] : '';
        $stock = (isset($pa['stock'])) ? $pa['stock'] : '';


    ?>
    <!-- -->

    <!--On va crocheter à l'indice 'action dns l'url afin de modofier le titre en fonction d'un
        'ajout' ou d'une modif de produit
           ucfirst() : fonction prédéfinie permettant d'afficher la première lettre d'une chaine de caractères en majuscule
        -->


    <h1 class=" text-center text-info"><?= ucfirst($_GET['action']) ?> Produit </h1>
    <form method="post" class="p-4 m-4 col-md-5 mx-auto table-bordered border border-dark table-rounded bg-secondary text-center text-white rounded" enctype="multipart/form-data">
        <div class="form-group">
            <label for="reference">Réference</label>
            <input class="bg-info" type="text" class="form-control" id="reference" name="reference" placeholder="ex:02" value="<?= $reference ?>">
        </div>
        <div class="form-group">
            <label for="categorie">Catégorie</label>
            <input class="bg-info" type="text" class="form-control" id="categorie" name="categorie" value="<?= $categorie ?>">
        </div>
        <div class=" form-group">
            <label for="titre">Titre</label>
            <input class="bg-info" type="text" class="form-control" id="titre" name="titre" value="<?= $titre ?>">
<!-- -->
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="bg-info" type="text" class="form-control" id="description" name="description" row="5"><?= $description ?></textarea>
        </div>
        <div class="form-group">
            <labelfor="couleur">Couleur</label>
                <input class="bg-info" type="text" class="form-control" id="couleur" name="couleur" value="<?= $couleur ?>">
        </div>
        <div class="form-group">
            <label for="taille">Taille</label>

            <select name="taille" id="taille">
                <option value="s" <?php if ($taille == 's') echo 'selected'; ?>>S</option>
                <option value="m" <?php if ($taille == 'm') echo 'selected'; ?>>M</option>
                <option value="l" <?php if ($taille == 'l') echo 'selected'; ?>>L</option>
                <option value="xl" <?php if ($taille == 'xl') echo 'selected'; ?>>XL</option>
                <option value="xxl" <?php if ($taille == 'xxl') echo 'selected'; ?>>XXL</option>

                <!--Si la taille dns la bdd est xl alors on affecte l'attribut 'selected à la balise option
            pour que l'option avc la taille reste selectionner en cas de modif -->
            </select>

        </div>
        <div class="form-group">
            <label for="public">Public</label>
            <select name="public" id="public">
                <option value="homme" <?php if ($public == 'homme') echo 'selected'; ?>>Homme</option>
                <option value="femme" <?php if ($public == 'homme') echo 'selected'; ?>>Femme</option>
                <option value="mixte" <?php if ($public == 'homme') echo 'selected'; ?>>Mixte</option>
            </select>
        </div>
        <div class="form-group">
            <label for="photo">Photo</label>
            <input class="bg-info" type="file" class="form-control-file" id="photo" name="photo">
        </div>
        <!--Un champ de type 'file' ne pas avoir d'attribue 'value', c'est pourquoi nous définissons un champ de type 'hidden' ci-dessous afin de récupérer l'URL de la photo en cas de modification
-->
        <input type="hidden" id="photo_actuelle" name="photo_actuelle" value="<?= $photo ?>">
        <!-- Affichage de la photo actuellement selectionner ds produit-->
        <?php if (!empty($photo)) : ?>

            <div class="text-center">
                <em> Vous pouvez télécharger une nouvelle image si vous souhaitez la modifier</em>
                <img src="<?= $photo ?>" alt="<? $titre ?>" style="width: 150px" ;>
            </div>

        <?php endif; ?>

        <div class="form-group">
            <label for="prix">Prix</label>
            <input class="bg-info" type="text" class="form-control" id="prix" name="prix" value="<?= $prix ?>">
        </div>
        <div class="form-group">
            <label for="stock">Stock</label>
            <input class="bg-info" type="text" class="form-control" id="stock" name="stock" value="<?= $stock ?>">
        </div>


        <div class="row justify-content-center">
            <button type="submit" class=" col-md-5 btn btn-dark mx-auto text-info "><?= strtoupper($_GET['action']) ?> INSCRIPTION</button>

        </div>
    </form>

<?php
    endif;
    require_once('../inc/footer.inc.php');
?>