<?php
include 'inc/init.inc.php';
include 'inc/functions.inc.php';
// $sexe='';
// restriction d'accès : si l'utilisateur n'est pas connecté, on le renvoie sur connexion.php
if (!user_is_connected()) {
    header('location: connexion.php');
}


if ($_SESSION['membre']['civilite'] == 'm') {
    $civilite = 'homme';
} else {
    $civilite = 'femme';
}

if ($_SESSION['membre']['statut'] == 1) {
    $statut = 'membre';
} else {
    $statut = 'administrateur';
}




// Début des affichages
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
/*
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
*/
?>
<div class="bg-light p-5 rounded text-center">
    <h1><i class="fa-solid fa-bag-shopping text-royalblue"></i> Profil : <?= $_SESSION['membre']['pseudo']; ?> <i class="fa-solid fa-bag-shopping text-royalblue"></i></h1>
    <p class="lead">Bienvenue sur notre boutique.</p>
</div>

<div class="row mt-4">
    <div class="col-sm-12">
        <?= $msg; // affichage des messages utilisateur  
        ?>
        <div class="row">
            <div class="col-sm-6">
                <ul class="list-group">
                    <li class="list-group-item bg-royalblue text-white" aria-current="true">Profil : </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><span class="fw-bold">N° client :</span> <?= $_SESSION['membre']['id_membre']; ?></span><i class="fa-solid fa-hashtag text-royalblue"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><span class="fw-bold">Pseudo :</span> <?= $_SESSION['membre']['pseudo']; ?></span><i class="fa-solid fa-face-meh text-royalblue"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><span class="fw-bold">Nom :</span> <?= $_SESSION['membre']['nom']; ?></span><i class="fa-solid fa-id-card text-royalblue"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><span class="fw-bold">Prénom :</span> <?= $_SESSION['membre']['prenom']; ?></span><i class="fa-solid fa-file-signature text-royalblue"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><span class="fw-bold">Sexe :</span> <?= $civilite; ?></span><i class="fa-solid fa-venus-mars text-royalblue"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><span class="fw-bold">Email :</span> <?= $_SESSION['membre']['email']; ?></span><i class="fa-solid fa-envelope text-royalblue"></i>
                    </li>
                    
                    
                    
                    <li class="list-group-item d-flex justify-content-between">
                        <span><span class="fw-bold">Statut :</span> <?= $statut; ?></span><i class="fa-solid fa-chess text-royalblue"></i>
                    </li>
                </ul>
            </div>
            <div class="col-sm-6">
                <img src="assets/img/profil.png" class="img-thumbnail w-100" alt="image de profil">
            </div>
        </div>

    </div>
</div>


<?php
include 'inc/footer.inc.php';
