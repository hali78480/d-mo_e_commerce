<?php
require_once('../inc/init.inc.php');

if (!adminConnect()) {
    header('location: ' . URL . 'connexion.php');
}


//AFFICHAGE DETAIL COMMANDE
if(isset($_GET['action'])&& $_GET['action'] == 'detail')
    
 
if(isset($_GET['id_commande']) && !empty($_GET['id_commande']))

{

    $data1 = $bdd->query("SELECT commande_id,produit_id,quantite,photo,reference,categorie,details_commande.prix FROM details_commande INNER JOIN produit ON id_produit=produit_id");
    $data1->bindValue(':id_commande',$_GET['id_commande'], PDO::PARAM_INT);
    $data1->execute();

    if(!$data1->rowCount())
    {
        header('location: ' . URL . 'admin/gestion_commande.php');   
    }

}
else
{
    header('location:'. URL .'admin/gestion_commande.php');
}

require_once('../inc/header.inc.php');
require_once('../inc/nav.inc.php');

echo "<h1 class='text-center text-secondary'>LISTE DE COMMANDES </h1>";
if (isset($validationDelete)) echo $validationDelete;

$data = $bdd->query("SELECT id_commande,nom,prenom,email,montant,DATE_FORMAT(date_enregistrement, '%d/%m/%Y à %H:%i:%s') as 'Date de commande', etat FROM membre INNER JOIN commande ON membre_id=membre_id ");


echo '<table class=" col-md-8 mx-auto p-5 table table-bordered bg-secondary text-white text-center"><tr>';

for ($i = 0; $i < $data->columnCount(); $i++) {
    $c = $data->getColumnMeta($i);
    echo "<th>" . strtoupper($c['name']) . "</th>";
}
echo "<th>DETAIL</th>";
echo "<th>MODIFIER</th>";
echo "<th>SUPPRIMER</th>";


echo '</tr>';
while ($p = $data->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr>';
    foreach ($p as $k => $value) 
    {
        if ($k == 'statut') {
            echo "<td>$value</td>";
        } else {
            echo "<td class='align-middle'>$value</td>";
        }
    
    }

    // if  ($k=='montant')
    // {
    //   echo "<td>$v.'€'</td>";
    // }
    // else{
    //    echo "<td>$v</td>";
    // }
  

    echo "<td class='align-middle'><a href='?action=detail&id_commande=$p[id_commande]' class='btn btn-info'><i class='fas fa-glasses'></i></a></td>";

    echo "<td class='align-middle'><a href='?action=modification&id_commande=$p[id_commande]' class='btn btn-light'><i class='fas fa-edit'></i></a></td>";


    echo "<td class='align-middle'><a href='?action=suppression&id_commande=$p[id_commande]' class='btn btn-danger'onclick='return(confirm(\"Êtes vous sûr de vouloir supprimer?\"));'><i class='far fa-trash-alt'></i></a></td>";

    echo '</tr>';
}

echo '</table>';
//////////////////////////////////////////////////////////////////////////////////////////






?>

<? if(isset($_GET['action'])&& $_GET['action'] == 'details'):?>

  <?php 
  echo '<h4 class=" text-center text-primary">Détail de la commande</h4>';
  echo '<table class=" col-md-8 mx-auto p-5 table table-bordered bg-secondary text-white text-center"><tr>';

for ($i = 0; $i < $data1->columnCount(); $i++) {
    $c = $data1->getColumnMeta($i);
    echo "<th>" . strtoupper($c['name']) . "</th>";
}

echo '</tr>';
while ($p = $data1->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr>';
   foreach ($p as $k => $value) 
    {
        if ($k == 'photo') {
           
        } else {
            echo "<td class='align-middle'>$value</td>";
        }
    
    }
}
?>

<?endif;?>

<?php

require_once('../inc/footer.inc.php')

?>