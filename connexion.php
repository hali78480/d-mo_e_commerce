<?php
require_once('inc/init.inc.php');


//qd l'internaute clique sur le lien 'deco' il transmet dns le même tmps dns l'url les param 'action=déco
//la condi° if permet de verifier que l'indice 'action' est bien définit dns l'url et qu'il a pr valeur
//'déco', on entre dns le IF seulement dns le cas ou l'internaute clique sur 'déco'
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion')
{


/*Pour que l'internaute soit déconnecté, il faut soit supprimer la session ou vider 
une partie afin que l'indice 'user' dans la session ne soit plus définit */




    //session_destroy();
    unset($_SESSION['user']);
}


/*Si l'internaute est connecté ça veut dire que l'indice 'user' est ien définit
dans la session alors il n'a rien à faire dns la page connexion on le redirige vers la page profil
*/
if(connect())
{
header("location: profil.php");
}






if ($_POST) {
    /* 
    on selectionne tout en bdd à condition que le champ pseudo et email soit soit égale à la donnée
    saisie par l'internaute dns le formu dns champ pseudo/email*/
    $data = $bdd->prepare("SELECT* FROM membre WHERE pseudo = :pseudo OR email = :email");
    $data->bindValue(':pseudo', $_POST['pseudo_email'], PDO::PARAM_STR);
    $data->bindValue(':email', $_POST['pseudo_email'], PDO::PARAM_STR);

    $data->execute();


    /*si la requête de selection retourne un résultat ça veut dire que l'email ou le pseudo saisie existe 
en bdd alors on entre dns la condition if*/

    if ($data->rowCount()) {
        //echo "pseudo ou email existant en BDD";

        $user = $data->fetch(PDO::FETCH_ASSOC);
        echo '<pre>';
        print_r($user);
        echo '</pre>';





        //Contrôle mdp en clair
        //if($_POST['password']==$user['mdp'])

        // pass verify permet de comparer une clé de hashage à une chaine de caractère 
        //arguments: pass_verif + ('la chaine de caract')+ la clé de hashage
        if (password_verify($_POST['password'], $user['mdp']))
         {
          //  echo 'MDP ok!!';



          //on passe en revu toutes les données de l'internaute récuperer en bdd dle concernant
          //si il a correctement rempli le formu

          //$user tableau array avc ttes les info et les données de l'internaute 
foreach($user as $key=>$value)
{//pour ne pas afficher le mdp lors de la boucle session
    if($key !='mdp')
    {
        //$_sesion  user    pseudo  titi78
        $_SESSION['user'][$key] = $value;
    }

}

/*on créer dns la session un indice 'user' contetant un tableau array avc ttes les données
de l'utilisateur

ce qui permetttra d'identifier l'utilisateur connécté sur le site et lui permettra de naviguer
sur le site en restant connecter */

/*echo '<pre>';
print_r($_SESSION);
echo '</pre>';*/
header('location: profil.php');

        } 
        
        else {
            $error = "<p class='col-md-4 mx-auto bg-danger text-white text-center p-3'>Mauvais identifiants ou mot de passe </p>";
        }
    } else {
        $error = "<p class='col-md-4 mx-auto bg-danger text-white text-center p-3'>Mauvais identifiants ou mot de passe </p>";
    }
}

require_once('inc/header.inc.php');
require_once('inc/nav.inc.php');

?>




<h1 class="display-4 text-center my-4">Identifiez vous</h1>

<?php if (isset($error)) echo $error; //affichage erreur identif
?>


<form method="post" class="m-4 bg-secondary col-md-6 mx-auto table-bordered border border-dark  text-center" action="">
    <div class="form-group">
        <label for="pseudo">Pseudo/Email</label>
        <input type="text" class=" bg-info form-control" id="pseudo_email" name="pseudo_email" value="<?php if (isset($_POST['pseudo_email'])) echo $_POST['pseudo_email']; ?>">

    </div>
    <div class="form-group">
        <label for="mdp">Mot de passe</label>
        <input type="password" class="bg-info form-control" id="password" name="password">
    </div>

    <div class="row justify-content-center">
        <button type="submit" class=" text-info col-md-5 btn btn-secondary mx-auto bg-dark ">CONNEXION</button>

    </div>
</form>




<?php
require_once('inc/footer.inc.php')

?>