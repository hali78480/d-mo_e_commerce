<?php
require_once('inc/init.inc.php');




    //-----------------------------------------------------------------------

if (isset($_POST['ajout_panier'])) {
    // echo '<pre>';
    // print_r($_POST);
    // echo '</pre>';

    $r = $bdd->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
    $r->bindValue(':id_produit', $_POST['id_produit'], PDO::PARAM_INT);
    $r->execute();



    //le $p = variabliser le ajoutPanier
    $p = $r->fetch(PDO::FETCH_ASSOC);


    // on ajoute dns la session un produit à la validation du formu
    ajoutPanier($p['id_produit'], $p['photo'], $p['reference'], $p['titre'], $_POST['quantite'], $p['prix']);
}

//SUPPRESSION AJOUT PANIER


if (isset($_GET['action']) && $_GET['action'] == "suppression")
{
$positionProduit = array_search($_GET['id_produit'], $_SESSION['panier']['id_produit']);

$vd = "<p class='col-md-6 mx-auto bg-success text-center text-white p-3 rounded'>Le produit titre <strong>" .$_SESSION['panier']['titre'][$positionProduit] ."</strong> référence <strong>".$_SESSION['panier']['reference'][$positionProduit]. "<strong> a bien été retiré du panier !</p>";

suppProduit($_GET['id_produit']);


}


//CONTROLE STOCK PRODUIT

//si l'indice payé est bien définit ça veut dire que l'internaute a cliqué sue le bouton VALIDER LE PAIMENENT et que l'attribut name="payer" a été détécté

if (isset($_POST['payer'])) {

    //LORENE DEPLACE DE IF STOCK
    $error = '';
    //FIN LORENE 
    for ($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++) {
        $r = $bdd->query("SELECT stock FROM produit WHERE id_produit =" . $_SESSION['panier']['id_produit'][$i]);
        $s = $r->fetch(PDO::FETCH_ASSOC);
        // echo '<pre>';
        // print_r($s);
        // echo '</pre>';


        //seulement si la quantite du  stock en bdd est inf à la quantité dns la session cad à la quant commandé par l'internaute

        //DEBUT LORENE
        if ($s['stock'] < $_SESSION['panier']['quantite'][$i]) {
            $error .= "<div class='bg-danger col-md-3 mx-auto text-center text-white rounded'>Stock restant du produit :<strong>" . $s['stock'] . "</strong></div>";

            $error .= "<div class='bg-success col-md-3 mx-auto text-center text-white rounded p-2'>Quantité du produit : <strong>" . $_SESSION['panier']['quantite'][$i] . "</strong></div>";


            // si le stock en BDD est supperieur à 0 mais infereieur à la quantité demandéé par l'interaute, alors  on entre dans la condiction if
            if ($s['stock'] > 0) {
                $error .= "<div class='bg-danger col-md-3 mx-auto text-center text-white rounded p-2 mb-2'>La quantite du produit <strong>" . $_SESSION['panier']['quantite'][$i] . "</strong> référence <strong>" . $_SESSION['panier']['reference'][$i] . " </strong> a été modifie car notre stock est insuffisant</div>";

                $_SESSION['panier']['quantite'][$i] = $s['stock'];
            } else {
                //sinon le stock du produit en BDD est à 0, on entre dans la condition else

                $error .= "<div class='bg-danger col-md-3 mx-auto text-center text-white rounded p-2 mb-2'>Le produit <strong>" . $_SESSION['panier']['quantite'][$i] . "</strong> referece <strong>" . $_SESSION['panier']['reference'][$i] . " </strong> a été supprime car le produit est en rupture de stock</div>";

                // on supprime dans la session le produit qui a un stock de 0, en rupture de stock
                suppProduit($_SESSION['panier']['id_produit'][$i]);
                
                //Besoin de decrementé la boucle une fois, afin de pas zaper le produit qui avant été dans l'indice $i+1 et qui est devenu $i

                $i--; // on fait un tour de boucle arriere, car array_splice remonte les indices inférieurs vers les indices suppereieur, cela permet de ne pas oublier de controler un produit qui aurait ronté d'un indice dans le tableau ARRAY de la session.
            }
            $e = true;
        }
         ////////
    }
    //si la var $e n'est pa définit, ça veut dire que les stockes sont sup à la quantit commandé par l'internaute, on entre dns le IF
    if(!isset($e))
    {
        //ENREGISTREMENT DE LA COMMANDE
        $r = $bdd->exec("INSERT INTO commande (membre_id, montant, date_enregistrement)VALUES(". $_SESSION['user']['id_membre'] .", ". montantTotal().", NOW())");


$idCommande = $bdd->lastInsertId();// permet de récuperer le dernier id_commande créer dns la bdd pr l'enre dns la table details_commande pr chaque produit à la bonne commande


//pr chaque tour de boucle for on execute une req d'insertion de la table details_commande pour chaque produit ajoutés au panier
//on recup le dernier id_commande en bdd afin de relier chaque produit à la bonne commande dns la table_commande
        for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++)
        {
$r = $bdd->exec("INSERT INTO details_commande (commande_id, produit_id, quantite, prix)VALUES($idCommande," . $_SESSION['panier']['id_produit'][$i]. ",".$_SESSION['panier']['quantite'][$i].",".$_SESSION['panier']['prix'][$i]. ")");


//DEPERCIATION DES STOCKS
//on modifi la table 'produit' afin que le stock soit égal au stock de la bdd moins la quantité du produit commandé à condition que l'id_produit de la bdd soit égal à l'id du produit stocké dns le panier de la session
$r = $bdd->exec("UPDATE produit SET stock = stock - ". $_SESSION['panier']['quantite'][$i]. " WHERE id_produit = " . $_SESSION['panier']['id_produit'][$i]);
;
        }
        unset($_SESSION['panier']);//On supprime les  éléments du panier dns la session aprés la validation du panier et l'insertion dns les tables commande et details commande

        $_SESSION['num_cmd'] = $idCommande;// on stock l'id_commande dns la session aprés validation du panier
        header ('location:validation_cmd.php');// on le redirige apres la validation du panier
    }
}


//echo '<pre>'; print_r($_SESSION); echo '</pre>';

require_once('inc/header.inc.php');
require_once('inc/nav.inc.php');
?>

<h2 class="text-center text-info">Mon panier</h2>




<?php if (isset($error)) echo $error;
 if (isset($vd)) echo $vd; ?>

<table class="col-md-8 mx-auto table table-bordered tex-center bg-secondary text-white">
    <tr>
        <th>PHOTO</th>
        <th>REFERENCE</th>
        <th>TITRE</th>
        <th>QUANTITE</th>
        <th>PRIX unitaire</th>
        <th>PRIX total/produit</th>
        <th>SUPP</th>
    </tr>

    <?php if (empty($_SESSION['panier']['id_produit'])) : ?>
        <tr>
            <td colspan="7" class="text-center text-dark"> Aucun produit dans le panier</td>
        </tr>
        <!--si lindice est vide ou non defini dns la session 
on enrtre dns la condition IF -->

</table>
<?php else : ?>
    <!--si lindice est bien defini dns la session 
on enrtre dns la condition ELSE -->

    <?php for ($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++) : ?>
        <tr>
            <td><a href="fiche-produit.php?id_produit=<?= $_SESSION['panier']['id_produit'][$i]; ?>"><img src="<?= $_SESSION['panier']['photo'][$i]; ?>" alt="<?$_SESSION['panier']['titre'][$i];?> " style="width:100px;"></a></td>

            <td><?= $_SESSION['panier']['reference'][$i]; ?></td>
            <td><?= $_SESSION['panier']['titre'][$i]; ?></td>
            <td><?= $_SESSION['panier']['quantite'][$i]; ?></td>
            <td><?= $_SESSION['panier']['prix'][$i]; ?></td>
            <td><?= $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i]; ?>€</td>

            <td><a href="?action=suppression&id_produit=<?=$_SESSION['panier']['id_produit'][$i] ?>" class='btn btn-danger'><i class='far fa-trash-alt'></i></a></td>
        </tr>

    <?php endfor; ?>

    <tr>
        <th>MONTANT TOTAL</th>
        <td colspan="4"></td>
        <th><?= montantTotal(); ?>€</th>
        <td></td>
    </tr>


    </table>

    <?php if (connect()) : ?>

        <form action="" method="post" class="col-md-8 mx-auto pl-0">
            <input type="submit" name="payer" value="VALIDER LE PAIEMENT" class="btn btn-success">
        </form>

    <?php else : ?>

        <a href="<?= URL ?>connexion.php" class="offset-md-4 btn btn-success mb-4">IDENTIFIEZ VOUS POUR VALIDER LA COMMANDE</a>

    <?php endif; ?>
<?php endif; ?>


<?php
require_once('inc/footer.inc.php');
?>