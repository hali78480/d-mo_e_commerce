<?php

require_once('inc/init.inc.php');


/* I l'utilis est connecté ça veut dire que l'indice 'user' est ien définit
dans la session alors il n'a rien à faire dns la page connexion on le redirige vers la page profil
*/
if (connect()) {
    header("location: profil.php");
}


$bdd = new PDO('mysql:host=localhost;dbname=boutique', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));



/*echo '<pre>';
print_r($_POST);
echo '</pre>';*/

if ($_POST) {

    $border = "border border-danger";


    foreach ($_POST as $key => $value) {

        $key = htmlspecialchars($value);
    }


    ////Cryptage du mdp en BDD
    /*Les mots de passe ne sont jamais clair en BDD et cette fonction sert à les hasher crypter*/






    $verifpseudo = $bdd->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    $verifpseudo->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);

    $verifpseudo->execute();


    if (empty($_POST['pseudo'])) {

        $errorPseudo =  "<p class='text-danger font-italic'>Merci de rensigner un Pseudo</p>";

        $error = true;
    } else if ($verifpseudo->rowCount()) {
        $errorPseudo = "<p class='text-danger font-italic'>Pseudo Indispo. Veuillez en saisir un nouveau</p>";

        $error = true;
    }
    //$insert = $bdd->prepare($req);


    //insère la requete
    $verifEmail = $bdd->prepare("SELECT * FROM membre WHERE email = :email");
    $verifEmail->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
    $verifEmail->execute();


    if (empty($_POST['email'])) {

        $errorEmail =  "<p class='text-danger font-italic'>Merci de rensigner un email</p>";

        $error = true;
    } else if ($verifEmail->rowCount()) {
        $errorEmail = "<p class='text-danger font-italic'>Compte existant. Veuillez en saisir un nouveau</p>";

        $error = true;
    }
    //--------------------------------------------------------------------------------



    if ($_POST['mdp'] != $_POST['confirm_mdp']) {
        $errorMdp =  "<p class='text-danger font-italic'>Vérifiez votre saisie
 </p>";

        $error = true;
    }


    if (!isset($error)) {
        $_POST['mdp'] = password_hash($_POST['mdp'], PASSWORD_BCRYPT);

        $insert = $bdd->prepare("INSERT INTO membre(pseudo,mdp,nom,prenom,email,civilite,ville,code_postale,adresse)VALUES(:pseudo,:mdp,:nom,:prenom,:email,:civilite,:ville,:code_postale,:adresse)");


        $insert->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
        $insert->bindValue(':mdp', $_POST['mdp'], PDO::PARAM_STR);
        $insert->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
        $insert->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
        $insert->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
        $insert->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
        $insert->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
        $insert->bindValue(':code_postale', $_POST['code_postale'], PDO::PARAM_INT);
        $insert->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);

        $insert->execute();

        //insertion de membre en BDD qui redirige vers la page valida° inscrip
        header("location:validation_inscription.php");
    }



    //execution de la requête




}

//--------------------------------------------------------------



require_once('inc/header.inc.php');
require_once('inc/nav.inc.php');

?>

<!-- 

Nous sommes dans la balise <main></main> 

Exo : 
1. Réaliser un formulaire d'inscription correspondant à la table 'membre' de la BDD 'boutique' (sauf id_membre) et ajouter le champ 'confirmer mot de passe' (name="confirm_mdp")

2. Contrôler en PHP que l'on receptionne bien toute les données saisies dans le formulaire 

3. Contrôler la validité du pseudo, si le pseudo est existant en BDD, alors on affiche un message d'erreur. Faites de même pour le champ 'email'

4. Informer l'internaute si les mots de passe ne correspondent pas.

5. Gérer les failles XSS

6. SI l'internaute a correctement remplit le formulaire, réaliser le traitement PHP + SQL permettant d'insérer le membre en BDD (requete préparée | prepare() + bindValue())

-->





<!-- Nous sommes dns la balise <main></main>-->
<h1 class="text-center text-danger">Bonjour et Bienvenue <br> Vous souhaitez ouvrir un compte!!<br> Rien de plus simple! <br>A vous de jouer!</h1>

<form method="post" class="m-4 col-md-6 mx-auto table-bordered border border-dark bg-secondary  text-center" action="">
    <div class="form-group">
        <label for="pseudo">Pseudo</label>
        <input type="text" class="bg-info form-control <?php if (isset($errorPseudo)) echo $border; ?>" id="pseudo" name="pseudo" placeholder="ex:toto78">

        <?php if (isset($errorPseudo)) echo $errorPseudo; ?>
    </div>

    <div class="form-group">
        <label for="mdp">Mot de passe</label>
        <input type="text" class="bg-info form-control" id="mdp" name="mdp">
    </div>
    <div class="form-group">
        <label for="confirm_mdp">Confirmez votre mot de passe</label>
        <input type="text" class="bg-info form-control <?php if (isset($errorMdp)) echo $border; ?>" id="confirm_mdp" name="confirm_mdp">
        <?php if (isset($errorMdp)) echo $errorMdp; ?>
        <?php

        ?>
    </div>

    <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" class="bg-info form-control" id="nom" name="nom">
    </div>
    <div class="form-group">
        <label for="prenom">Prénom</label>
        <input type="text" class="bg-info form-control" id="prenom" name="prenom">
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="text" class="bg-info form-control <?php if (isset($errorEmail)) echo $border; ?>" id="email" name="email" placeholder="ex: exemple@exemple.com" value="<?php if (isset($_POST['email'])) echo $_POST['email'] ?>">

        <?php if (isset($errorEmail)) echo $errorEmail; ?>
    </div>
    <div class="form-group">
        <label for="civilite">Civilité</label>
        <select class="bg-info border border-dark" name="civilite" id="civilite">
            <option value="homme">Homme</option>
            <option value="femme">Femme</option>
        </select>
    </div>
    <div class="form-group">
        <label for="ville">Ville</label>
        <input type="text" class="bg-info form-control" id="ville" name="ville">
    </div>

    <div class="form-group">
        <label for="code_postale">Code postale</label>
        <input type="text" class="bg-info form-control" id="code_postale" name="code_postale">
    </div>
    <div class="form-group">
        <label for="adresse">Adresse</label>
        <input type="text" class="bg-info form-control" id="adresse" name="adresse">
    </div>


    <div class="row justify-content-center">
        <button type="submit" class="bg-dark text-info col-md-5 btn btn-secondary mx-auto ">INSCRIPTION</button>

    </div>
</form>





<?php
require_once('inc/footer.inc.php');
?>