<?php


//FONCTION INTERNAUTE CONNECTE

function connect()
{



    /*si l'indice user dans la session n'est pas définit cela veut dire que l'utilisat n'est pas
     passé par la page de connexion (c'est dns cette page que l'on créer l'indice user dans la session)
     cela veut dire que l'itilisat.. n'est pas connécté et  n'ettr ppt pas inscrit sur le site
    */
    if (!isset($_SESSION['user'])) {
        return false;
    } else {
        return true;
    }
}

// FUNCTION INTERNAUATE ADMINISTRATEUR
function adminConnect()
{
    /*
si l'utilisateur est bien connecté et sii le statut est de 1 = l'utilisateur est administrateur
du site
*/
    if (connect() && $_SESSION['user']['statut'] == 1) {
        return true;
    } else/* Sinon le statut de l'utilisateur dns la session n'a pas pr valeur '1' donc l'utilist
    n'est pas admin ou pttr n'est pas connecté
    */ {
        return false;
    }
}

//FONCTION CREATION DU PANIER DNS LA SESSION
/* Les données ne sont jamais conservées en BDD, beaucoup de panier n'aboutissent jamais
Donc nous allons stocker les info du panier directement ds le fichier de seesion de l'internauute
Dans la session nous définissons differents tableau Array qui permettent de stocker pr ex toutes
les références des produits ajoutés au panier dns un array*/
function creationPanier()
{



    //croches vides = ajout d'indice numerique
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = array();
        $_SESSION['panier']['id_produit'] = array();
        $_SESSION['panier']['photo'] = array();
        $_SESSION['panier']['reference'] = array();
        $_SESSION['panier']['titre'] = array();
        $_SESSION['panier']['quantite'] = array();
        $_SESSION['panier']['prix'] = array();
    }
}

//FONCTION AJOUTE PRODUIT DS LA SESSION
// FONCTION AJOUTER PRODUIT DANS LA SESSION
// Les paramètres définit dans la fonction permettront de receptionner les 
//informations du produit ajouté dasn le panier afin de stocker chaque donnée 
//dans les différents tableau ARRAY


function ajoutPanier($id_produit, $photo, $reference, $titre, $quantite, $prix)
{


    // On contrôle si le panier est crée dans la session ou non ($_SESSION['panier'])
    creationPanier();

    //array_serach= va me chercher à quel indicie se trouve telle id qui vient
    // dêtre ajouté dns le panier
    $positionProduit = array_search($id_produit, $_SESSION['panier']['id_produit']);

    //si $positionProduit est diff de false ça va dire que array_srch  a bien trouvé
    //l'indice du prod dns la session
    if ($positionProduit !== false) {

        // On modifie la quantité du produit à l'indice correspondant, retourné par 
        //array_search()
        // Chaque indice numérique dans les tableaux 'photo,reference, prix' etc... 
        //correspondent au même produit ajouté dans le panier 
        $_SESSION['panier']['quantite'][$positionProduit] += $quantite;
        //+= permet d'ajouter une quantité sans écraser l'autre
    } else {



        // Les crochets vide [] permettent de générer des indices numérique dans 
        //les tableau ARRAY
        // $_SESSION['panier']['id_produit'][0] = 29;

        $_SESSION['panier']['id_produit'][] = $id_produit;
        $_SESSION['panier']['photo'][] = $photo;
        $_SESSION['panier']['reference'][] = $reference;
        $_SESSION['panier']['titre'][] = $titre;
        $_SESSION['panier']['quantite'][] = $quantite;
        $_SESSION['panier']['prix'][] = $prix;
    }
}

//FONCTION MONTANT TOTAL PANIER
function montantTotal()
{
    $total = 0;
    for ($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++) {
        $total += $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];
    }
    return round($total, 2);
}



//LORENE

//fonction suppression produit dans le panier
function suppProduit($id_produit) // example ref 30
{
    //on transmet à la foncton predefini araay_search l'id_produit du produit en rupture de stock
    // array_search() retourne l'indice du tableau ARRAY auquel se trouve l'id_produit à supprimer

    //recupere l'indice du tableau afin de pouvoir supprime tt les lignes du tableau panier
    $positionProduit = array_search($id_produit, $_SESSION['panier']['id_produit']); //[1] example car produit rupture de stock


    //si la valeur de $positionProduit est different de FALSE, cela veut dire que l'id produit a supprimer a bien été trouvé
    //dans le panier de la session
    if ($positionProduit !== false) {


        //array_splice() permet de supprimer des elements d'un tableay ARRAY
        //on supprime chaque ligne dans les tableaux ARRAY du produit en rupture de stock
        //array_splice() re organise les tableaux ARRAY, c'est à dire que tout les elements aux indices inférieur
        //remontent aux indices superieur, le produit stocké à l'indice 3 du teableau ARRAY remonte à l'indice 2 du tableau ARRAY


        // supprimer dans tab photo ==> indice $positionproduit et 1 correspond à 1 element
        array_splice($_SESSION['panier']['id_produit'], $positionProduit, 1);
        array_splice($_SESSION['panier']['photo'], $positionProduit, 1);
        array_splice($_SESSION['panier']['reference'], $positionProduit, 1);
        array_splice($_SESSION['panier']['titre'], $positionProduit, 1);
        array_splice($_SESSION['panier']['quantite'], $positionProduit, 1);
        array_splice($_SESSION['panier']['prix'], $positionProduit, 1);
    }
}

/*
    array
    (
        [user] => ARRAY(infos de l'utilisateur connecté)

        [panier] => array(
                
                [id_produit] =>array(
                            0 => 15
                            1 => 40 
                        )

                [reference] => array(
                            0 => 12A45
                            1 => 46F56
                        )

                [photo] => array(
                            0 => http://localhost/PHP/09-boutique/photo/img.jpg
                            1 => http://localhost/PHP/09-boutique/photo/img3.jpg
                        )
        )
    )
*/

