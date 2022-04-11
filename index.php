<?php
include 'inc/init.inc.php';
include 'inc/functions.inc.php';



// Récupération de la liste des catégories présentes en BDD sans doublons
$liste_categories = $pdo->query("SELECT DISTINCT categorie FROM salle ORDER BY categorie");
// Récupération de la liste des tailles présentes en BDD sans doublons
$liste_capacite= $pdo->query("SELECT DISTINCT capacite FROM salle");
// Récupération de la liste des valeurs de ville présentes en BDD sans doublons
$liste_ville = $pdo->query("SELECT DISTINCT ville FROM salle");
// Récupération des couleurs présentes sur les salles sans doublons
$liste_prix = $pdo->query("SELECT prix FROM produit");

//$liste_salle = $pdo->query("SELECT DISTINCT salle FROM salle, couleur WHERE salle.id_couleur = couleur.id_couleur ORDER BY couleur");

// Récupération de tous les produits en BDD (avec le nom de la couleur)
if (isset($_GET['categorie'])) {

    $liste_produits = $pdo->prepare("SELECT * FROM produit, salle WHERE produit.id_salle = salle.id_salle AND categorie = :categorie ORDER BY categorie, titre");
    $liste_produits->bindParam(':categorie', $_GET['categorie'], PDO::PARAM_STR);
    $liste_produits->execute();
} elseif (isset($_GET['capacite'])) {

    $liste_produits = $pdo->prepare("SELECT * FROM produit, salle WHERE produit.id_salle = salle.id_salle AND capacite = :capacite ORDER BY categorie, titre");
    $liste_produits->bindParam(':capacite', $_GET['capacite'], PDO::PARAM_STR);
    $liste_produits->execute();
} elseif (isset($_GET['ville'])) {

    $liste_produits = $pdo->prepare("SELECT * FROM produit, salle WHERE produit.id_salle = salle.id_salle AND ville = :ville ORDER BY categorie, titre");
    $liste_produits->bindParam(':ville', $_GET['ville'], PDO::PARAM_STR);
    $liste_produits->execute();
}elseif (isset($_GET['prix'])) {

    $liste_produits = $pdo->prepare("SELECT * FROM produit, salle WHERE produit.id_salle = salle.id_salle AND prix = :prix ORDER BY prix");
    $liste_produits->bindParam(':prix', $_GET['prix'], PDO::PARAM_STR);
    $liste_produits->execute();
}
elseif (isset($_GET['rechercher'])) {

    $liste_produits = $pdo->prepare("SELECT * FROM produit, salle WHERE produit.id_salle = salle.id_salle AND (titre LIKE :rechercher OR description LIKE :rechercher) ORDER BY categorie, titre");
    // on prépare l'argument car il faut les % pour le LIKE
    $rechercher = '%' . trim($_GET['rechercher']) . '%';

    $liste_produits->bindParam(':rechercher', $rechercher, PDO::PARAM_STR);
    $liste_produits->execute();
} else {
    $liste_produits = $pdo->query("SELECT * FROM produit, salle WHERE produit.id_salle = salle.id_salle ORDER BY categorie, titre");
}






// Début des affichages
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>
<div class="bg-light p-5 rounded text-center">
    <h1><i class="fa-solid fa-bag-shopping text-royalblue"></i> Room <i class="fa-solid fa-bag-shopping text-royalblue"></i></h1>
    <p class="lead">Bienvenue sur notre boutique.</p>
</div>

<div class="row mt-4">
    <div class="col-sm-12">
        <?= $msg; // affichage des messages utilisateur  
        ?>
        <div class="row">
            <div class="col-sm-3 filtres">

                <?php
                // si $_GET n'est pas vide : un filtre est appliqué, on propose un lien pour annuler les filtres.
                if (!empty($_GET)) {
                    echo '<a href="index.php" class="btn btn-outline-primary w-100">Annuler les filtres</a><hr>';
                }
                ?>


                <h3 class="pb-3 border-bottom">Catégories</h3>
                <ul class="list-group">
                    <?php
                    while ($categorie = $liste_categories->fetch(PDO::FETCH_ASSOC)) {
                        echo '<li class="list-group-item"><a href="?categorie=' . $categorie['categorie'] . '" class="stretched-link">' . $categorie['categorie'] . '</a></li>';
                    }

                    ?>
                </ul>
                <h3 class="pb-3 border-bottom mt-3">Ville</h3>
                <ul class="list-group">
                    <?php
                    while ($ville = $liste_ville->fetch(PDO::FETCH_ASSOC)) {
                        echo '<li class="list-group-item"><a href="?ville=' . $ville['ville'] . '" class="stretched-link">' . $ville['ville'] . '</a></li>';
                    }

                    ?>
                </ul>
                <h3 class="pb-3 border-bottom mt-3">capacite</h3>
                <ul class="list-group">
                    <?php
                    while ($capacite = $liste_capacite->fetch(PDO::FETCH_ASSOC)) {
                        echo '<li class="list-group-item"><a href="?capacite=' . $capacite['capacite'] . '" class="stretched-link">' . $capacite['capacite'] . '</a></li>';
                    }

                    ?>
                </ul>
                <form>
                <h3 class="pb-3 border-bottom mt-3">prix</h3>
            <input type="range" min="100" max="3000" id="prix" name="prix" value="100" >
                    <?php
                   while ($prix = $liste_prix->fetch(PDO::FETCH_ASSOC)) {
                        //echo '<li class="list-group-item"><a href="?capacite=' . $prix['min'] . '" class="stretched-link">' . $prix['max'] . '</a></li>';
                    }

                    ?>
                    <button>filtrer</button>
                </form>
            </div>
            <div class="col-sm-9">
                <div class="row">
                    <?php

                    if ($liste_produits->rowCount() > 0) {
                        // affichage des salle
                        while ($salle = $liste_produits->fetch(PDO::FETCH_ASSOC)) {
                            echo '<div class="col-lg-3 col-md-4 col-sm-6 mb-3">';
                            echo '<div class="card">
                            <img src="' . URL . 'assets/img/' . $salle['photo'] . '" class="card-img-top" alt="Image salle : ' . $salle['titre'] . '">
                            <div class="card-body">
                                <h3 class="card-title">' . $salle['titre'] . '</h3>
                                <p class="card-text">Catégorie : ' . $salle['categorie'] . '<br>Prix : ' . $salle['prix'] . ' €</p>
                                <h5 class="card-title">' . $salle['etat'] . '</h5>

                                <p class="card-text"> <i class="fa-solid fa-calendar"></i> : ' . $salle['date_arrivee'] .' au '.$salle['date_depart']. '<br>Prix : ' . $salle['prix'] . ' €</p>
                                <a href="fiche_produit.php?id_produit=' . $salle['id_produit'] . '" class="btn btn-outline-primary w-100 stretched-link">détails salle</a>
                            </div>
                        </div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="col-12"><h3 class="text-center text-royalblue pb-3 border-bottom">Aucun résultat ne correspond à votre recherche !</h3></div>';
                    }


                    ?>
                </div>
            </div>
        </div>

    </div>
</div>


<?php
include 'inc/footer.inc.php';
