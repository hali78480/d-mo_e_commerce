<?php
require_once('inc/init.inc.php');
require_once('inc/header.inc.php');
require_once('inc/nav.inc.php');


?>



<h1 class="display-1 text-center text-danger my-5">Félicitation</h1>

<h4 class="text-center text-info"> Votre Commande est validée!!</h4>

<h4 class="text-center text-info"> Voici votre numéro de commande:<? $_SESSION['num_cmd']?></h4>

<p class="text-center">
    <a href="profil.php" class="btn btn-success mt-5">Voir mes commandes</a>
</p>

<?php
require_once('inc/footer.inc.php')

?>

