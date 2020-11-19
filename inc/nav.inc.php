<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Ma Boutique en Ligne</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExample04">

        <ul class="navbar-nav mr-auto">

            <?php
            //calculer la soomme avc sum = le total de la quantité else affiche 0

            if (isset($_SESSION['panier'])) {
                $badge = "<span class='badge badge-info text-dark'>" . array_sum($_SESSION['panier']['quantite']) . "</span>";
            } else {
                $badge = "<span class='badge badge-info text-dark'>0</span>";
            }

            ?>

            <?php if (connect()) : // si connecté on rentre dns la condition
            ?>

                <li class="nav-item active">
                    <a class="nav-link" href="<?= URL ?>profil.php">Votre compte</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="<?= URL ?>connexion.php">Identifiez vous</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="<?= URL ?>boutique.php">Accès à la boutique</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="<?= URL ?>panier.php">Mon panier  <?= $badge?></a>
                </li>


                <li class="nav-item active">
                    <a class="nav-link" href="<?= URL ?>connexion.php?action=deconnexion">Déconnexion</a>
                </li>


            <?php else : //acces visiteur non connecté
            ?>


                <li class="nav-item active">
                    <a class="nav-link" href="<?= URL ?>profil.php">Votre compte</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="<?= URL ?>connexion.php">Identifiez vous</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="<?= URL ?>inscription.php">Inscrivez vous</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="<?= URL ?>boutique.php">Accès à la boutique</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="<?= URL ?>panier.php">Mon panier <?= $badge?></a>

                </li>


            <?php endif; ?>
            <?php if (adminConnect()) : //I l'utilisateur a pr valeur 1 pour le statut dans la session donc dns la bdd
                //alors il est admin du site et nous lui donnons accès au backoffice 
            ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">BACK OFFICE</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown04">

                        <a class="dropdown-item" href="<?= URL ?>admin/gestion_boutique.php">Gestion boutique</a>

                        <a class="dropdown-item" href="<?= URL ?>admin/gestion_commande.php">Gestion commande</a>

                        <a class="dropdown-item" href="<?= URL ?>admin/gestion_membre.php">Gestion membre</a>
                    </div>
                </li>

            <?php endif; ?>



        </ul>
        <form class="form-inline my-2 my-md-0">
            <input class="form-control" type="text" placeholder="Recherche">
        </form>
    </div>
</nav>

<main class="container-fluid" style="min-height:90vh;">