<?php
require_once('inc/init.inc.php');
//----------------------------------------------------------------------------------------------
//Requête de selection 
//Pour que soit afficher la valeur de la cate dns l'url  ex= caté=teeshirt

if (isset($_GET['categorie']) && !empty($_GET['categorie'])) {

    // On selectionne tout en BDD par rapport à la catégorie transmise dans l'URL, afin 
    //d'afficher tout les produits liés à la catégorie

    $r = $bdd->prepare("SELECT* FROM produit WHERE categorie = :categorie");
    $r->bindValue(':categorie', $_GET['categorie'], PDO::PARAM_STR);
    $r->execute();

    //si la re de selection ne retourne pas de result
    //rowcount retourne false et dnc la cate dns l'url n'est pas connu en bdd dnc ça
    // redirige vers la boutique

    if ($r->rowCount() == false) {
        header('location: boutique.php');
    }
} else {
    $r = $bdd->query("SELECT * FROM produit");
}




require_once('inc/header.inc.php');
require_once('inc/nav.inc.php');
?>




<?php
//-----------------------------------------------------------------------------------------
//recuperer les categorie et les faire boucler avc les liens
//On choisi DISTINCT pr éliminer les doublons dns la BDD

$d = $bdd->query("SELECT DISTINCT categorie FROM produit");


?>
<!-- Page Content -->
<div class="container">

    <div class="row">

        <div class="col-lg-3">

            <h4 class="my-4 text-center">Une selection de vêtements à petit prix</h4>
            <div class="list-group">

                <?php while ($c = $d->fetch(PDO::FETCH_ASSOC)) :
                    //echo '<pre>'; print_r($c); echo '</pre>';
                ?>
                    <a href="?categorie=<?= $c['categorie'] ?>" class="list-group-item text-white text-center bg-secondary"><?= $c['categorie'] ?></a>
                <?php endwhile;

                //Création d'un lien par categorie pr chaque tour de boucle
                //--------------------------------------------------------------------------------------------
                ?>
            </div>


        </div>
        <!-- /.col-lg-3 -->

        <div class="col-lg-9">

            <div id="carouselExampleIndicators" class="carousel slide my-4" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                    <div class="carousel-item active">
                        <img class="d-block img-fluid" src="<?= URL ?>photo/vetements_fitness.jpg" alt="First slide">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block img-fluid" src="<?= URL ?>photo/ob_57bb14_3474-406825292720525-7890405-n.jpg" alt="Second slide">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block img-fluid" src="<?= URL ?>photo/4f3622d81958bdf4114e6f2c41da94627dc30940_casquettes.jpg" alt="Third slide">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>

            <div class="row">

                <?php
                // Boucle pr dupliquer le nombre de card dont on a besoin par produit---------------------------
                while ($p = $r->fetch(PDO::FETCH_ASSOC)) :
                    //echo '<pre>'; print_r($p); echo'</pre>';
                ?>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100">
                            <a href="fiche_produit.php?id_produit=<?= $p['id_produit'] ?>"><img class="card-img-top" src="<?= $p['photo'] ?>" alt=""></a>
                            <div class="card-body">
                                <h4 class="card-title">
                                    <a href="fiche_produit.php?id_produit=<?= $p['id_produit'] ?>"><?= $p['categorie'] ?></a>
                                </h4>
                                <h5><?= $p['prix'] ?>€</h5>
                                <p class="card-text"><?= $p['description'] ?></p>

                                <!--
                            if(iconv_strlen($p['description'])>50)
                            echo substr($p['description'],0,80) . '...';
                            else
                            echo $p['description'];-->


                            </div>
                            <div class="card-footer">
                                <a href="fiche_produit.php?id_produit=<?= $p['id_produit'] ?>" class="btn btn-info">Pour plus d'info &raquo;</a>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>

            </div>
            <!-- /.row -->

        </div>
        <!-- /.col-lg-9 -->

    </div>
    <!-- /.row -->

</div>
<!-- /.container -->



<?php
require_once('inc/footer.inc.php');
