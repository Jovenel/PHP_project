<?php
include 'inc/init.inc.php';
include 'inc/functions.inc.php';

if (empty($_GET['id_produit'])) {
     header('location: index.php');
}
$msg= '';

// récupération des informations du produit en BDD
$infos_produit = $pdo->prepare("SELECT * FROM produit, salle WHERE produit.id_salle = salle.id_salle AND id_produit = :id_produit");
// print_r($infos_produit);

$infos_produit->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
$infos_produit->execute();

if ($infos_produit->rowCount() < 1) {
     header('location: index.php');
}


//-----------------------------------------------------
//-----------------------------------------------------
// Faire une réservation
//-----------------------------------------------------
//-----------------------------------------------------

$id_membre = '';
$id_salle = $_GET['id_produit'];
$commentaire = '';

// echo '<pre>';
// print_r($_POST);
// echo '</pre>';


$produit = $infos_produit->fetch(PDO::FETCH_ASSOC);

// récupération des informations des avis en BDD
$recup_avis = $pdo->prepare("SELECT * FROM avis, membre, salle WHERE avis.id_meembre = membre.id_membre AND id_avis = :id_avis");

// Nous aimerions connaitre les numéros des livres que Chloe a emprunté
// SELECT id_livre FROM emprunt WHERE id_abonne IN 
// (SELECT id_abonne FROM abonne WHERE prenom = 'chloe'); 

// INSERT INTO `avis` (`id_avis`, `id_membre`, `id_salle`, `commentaire`, `note`, `date_enregistrement`) VALUES ('2', '3', '8', 'Salle très agréable nous avons adoré.', '5', '2022-03-22 15:57:55.000000');


///  Réservation  $_SESSION['membre']['id_membre']

if (isset($_POST['id_membre']) && isset($_POST['id_produit'])) {
    $id_membre = trim($_POST['id_membre']);
    $id_produit = trim($_POST['id_produit']);

    $req_preparee = $pdo->prepare("INSERT INTO commande (id_commande, id_membre, id_produit, date_enregistrement) VALUES (NULL, :id_membre, :id_produit, NOW())");
    $req_preparee->bindParam(':id_membre', $id_membre, PDO::PARAM_STR);
    $req_preparee->bindParam(':id_produit', $id_produit, PDO::PARAM_STR);
    $req_preparee->execute();
    $msg .= '<div class="alert alert-success mb-3">Le produit n°' . $_GET['id_produit'] . ' a bien été réservé.</div>';


    $req_preparee = $pdo->prepare("UPDATE produit SET etat = 'Réservation' WHERE produit.id_produit = :id_produit");
    $req_preparee->bindParam(':id_produit', $id_produit, PDO::PARAM_STR);
    $req_preparee->execute();

    // header('location:fiche_produit.php'.$_GET['id_produit']);
    // exit();

}



// Début des affichages
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>
<div class="bg-light p-5 rounded text-center">
    <h1><i class="fa-solid fa-bag-shopping text-royalblue"></i> Produit : <?= ucfirst($produit['titre']); ?> <i class="fa-solid fa-bag-shopping text-royalblue"></i></h1>
    <p class="lead">Bienvenue sur notre boutique.</p>
</div>

<div class="row mt-4">
    <div class="col-sm-12">
        <?= $msg; // affichage des messages utilisateur  
        ?>
        <div class="row">
            <div class="col-sm-6">
                <ul class="list-group">




                    <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">N°produit : </span><span><?= $produit['id_produit'] ?></span></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Titre : </span><span><?= $produit['titre'] ?></span></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Catégorie : </span><span><a href="index.php?categorie=<?= $produit['categorie'] ?>"><?= $produit['categorie'] ?></a></span></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">ville : </span><span><?= $produit['ville'] ?></span></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Adresse : </span><span><?= $produit['adresse'] ?></span></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Date arrivée : </span><span><?= $produit['date_arrivee'] ?></span></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Date depart : </span><span><?= $produit['date_depart'] ?></span></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">capacite : </span><span><?= $produit['capacite'] ?></span></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Prix : </span><span><?= $produit['prix'] ?> €</span></li>
                    <li class="list-group-item "><span class="fw-bold">Etat : </span><span class="danger"><?= $produit['etat'] ?></span></li>
                    <li class="list-group-item "><span class="fw-bold">Description : </span><span><?= $produit['description'] ?></span></li>
                </ul>
            </div>
            <div class="col-sm-6">
                <?php
                if (user_is_connected() && $produit['etat'] == 'Libre') : ?>
                    <form action="" method="POST">

                        <input type="hidden" readonly name="id_membre" value="<?= $_SESSION['membre']['id_membre'] ?>">
                        <input type="hidden" readonly name="id_produit" value="<?= $produit['id_produit'] ?>">
                        <button type="" class="reservez btn btn-outline-primary w-50">Réserver</button>
                    </form>
                <?php endif; ?>


                <img src="<?= URL; ?>assets/img/<?= $produit['photo'] ?>" class="w-100 img-thumbnail">
            </div>

            <?php
            if (user_is_connected()) {
                // echo '<td colspan="3"><a href="?action=payer" class=" w-50">  </a></td>';

            } else {
                echo '<td colspan="3">Veuillez vous <a href="connexion.php">connecter</a> ou vous <a href="inscription.php">inscrire</a> afin de faire une réservation .</td>';
            }
            ?>
            <?php $resultat = $pdo->query("SELECT COUNT(*) as nbCommentaire FROM avis WHERE id_salle=2");
            while ($list = $resultat->fetch(PDO::FETCH_ASSOC)) {

                // foreach ($list as $_salle) {
                // echo '<p>'  . 'Nombre total de commentaire est de:' . $list['nbCommentaire'] . '</p>';
                // }


            }
            ?>



        </div>

        <?php 
        if (user_is_connected()) {
            // echo '<td colspan="3"><a href="?action=payer" class=" w-50">  </a></td>';  <?php 
            
        
        if (isset($_POST['commentaire'])) {
            $id_membre = $_POST['id_membre'];
            $id_salle = $_POST['id_salle'];
            $commentaire = trim($_POST['commentaire']);
            $note = trim($_POST['note']);

            echo 'test';

            // AVEC PREPARE
            $req_preparee = $pdo->prepare("INSERT INTO avis (id_avis, id_membre, id_salle, commentaire, note, date_enregistrement) VALUES (NULL, :id_membre, :id_salle, :commentaire, :note, NOW())");
            $req_preparee->bindParam(':id_membre', $id_membre, PDO::PARAM_STR);
            $req_preparee->bindParam(':id_salle', $id_salle, PDO::PARAM_STR);
            $req_preparee->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
            $req_preparee->bindParam(':note', $note, PDO::PARAM_STR);
            $req_preparee->execute();
        }
    }

        // 6. récupération des commentaires
        // date en
        // $liste_message = $pdo->query("SELECT * FROM commentaire ORDER BY date_enregistrement DESC");
        // date fr
        $liste_message = $pdo->query("SELECT COUNT(*) as nbCommentaire FROM avis WHERE id_salle=$id_salle");
        $liste_salle = $pdo->query("SELECT * FROM avis WHERE id_salle=$id_salle");


        

        if (isset($_POST['subtmit_comment'])) {
            // header('location:fiche_produit.php?id_produit=' . $id_salle);
        }
        if (user_is_connected()) {

        ?>




        <form method="post" action="" class="border p-3">
            <div class="row">
                <div class="col-sm-6">
                    <!-- champ caché id_salle pour la modification -->
                    <input type="hidden" readonly name="id_membre" id="id_membre" value="<?= $_SESSION['membre']['id_membre'] ?>">
                    <p>Notez votre expérience</p>
                    <div class="d-flex">
                    <div>
                        <input type="radio" id="1" name="note" value="1" checked>
                        <label for="1">1</label>
                    </div>

                    <div>
                        <input type="radio" id="2" name="note" value="2">
                        <label for="2">2</label>
                    </div>

                    <div>
                        <input type="radio" id="3" name="note" value="3">
                        <label for="3">3</label>
                    </div>
                    <div>
                        <input type="radio" id="4" name="note" value="4">
                        <label for="4">4</label>
                    </div>
                    <div>
                        <input type="radio" id="5" name="note" value="5">
                        <label for="5">5</label>
                    </div>
                    </div>
                    <textarea class="form-control" id="" name="commentaire" id="commentaire" rows="3"><?= $commentaire; ?></textarea>
                    <input type="hidden" readonly name="id_salle" id="id_salle" value="<?= $produit['id_salle'] ?>">
                    <!-- champ caché id_produit pour la modification -->

                    <div class="mb-3"> Laissez un commentaire
                        <!-- <input type="text" rows="3" class="form-control" name="commentaire" id="commentaire" value="<?= $commentaire; ?>" class="form-control"> -->
                        <button name="submit_comment" class="btn btn-outline-primary" type="submit">envoyer </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- // 8. affichage du nb de commentaire -->
        <?php 
        
        
        }
        while ($list = $liste_salle->fetch(PDO::FETCH_ASSOC)) {

            // foreach ($list as $_salle) {
            echo '<p>' .$_SESSION['membre']['pseudo'] . ' - ' . $list['commentaire'] . '</p>';
            // }


        }
        ?>
    </div>
</div>


<?php
include 'inc/footer.inc.php';
