<?php
require_once('inc/init.inc.php');

if(!connect())
   /*si l'internaute nest PAS(!) connecté cela veut dire que l'indice 'user' n'est pas définit dns
   la session alors, il n'a rien à faire sur la page profil et on le redirige vers la page de connexion 
    */
{
    header('location: connexion.php');
}
require_once('inc/header.inc.php');
require_once('inc/nav.inc.php');




?>
<!--Afficher les infos sur la card en retirant l'id et le statut-->
<h1 class="display-1 text-center my-5">Bonjour<span class="text-info">
<?= $_SESSION['user']['pseudo'] 
//<? = <?php echo en gros c un raccourci pour faire un php et echo
?></span> </h1>

<div class="col-md-3 mx-auto card mb-3 shadow-lg ">
  <div class="card-body">
    <h5 class="card-title text-center">Vos info perso<hr></h5>
   <?php
   foreach($_SESSION['user'] as $key => $value): ?>

   <?php if($key !='id_membre' && $key != 'statut'): ?>

    <p class="card-text"><strong><?=$key?></strong> : <?=$value?></p>
   
<?php
endif;
?>


<?php
endforeach;
?>
    <a href="#" class="card-link">Modifier</a>
   
  </div>
</div>







<?php
require_once('inc/footer.inc.php')
?>

