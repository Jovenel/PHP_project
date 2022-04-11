<?php
include 'inc/init.inc.php';
include 'inc/functions.inc.php';

// déconnexion utilisateur
if (isset($_GET['action']) && $_GET['action'] == 'deconnexion') {
    session_destroy();
    // unset($_SESSION['membre']);
}

// Restriction d'accès : si l'utilisateur est connecté, on le redirige sur profil.php
if (user_is_connected()) {
    header('location: profil.php');
}



if (isset($_POST['pseudo']) && isset($_POST['mdp'])) {
    $pseudo = trim($_POST['pseudo']);
    $mdp = trim($_POST['mdp']);

    $connexion = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    $connexion->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $connexion->execute();

    if ($connexion->rowCount() < 1) {
        // si on a récupéré 0 ligne : le pseudo n'existe pas en BDD
        $msg .= '<div class="alert alert-danger mb-3">Attention,<br>Erreur sur le pseudo et/ou le mot de passe.</div>';
    } else {
        // pseudo ok
        // on compare le mdp
        // pour comparer le mdp traité avec password_hash() : password_verify()
        // https://www.php.net/manual/fr/function.password-verify.php

        $infos = $connexion->fetch(PDO::FETCH_ASSOC);

        if (password_verify($mdp, $infos['mdp'])) {

            // on conserve les données utilisateur (sauf le mdp) dans la session dans un sous tableau "membre"
            $_SESSION['membre'] = array();
            $_SESSION['membre']['id_membre'] = $infos['id_membre'];
            $_SESSION['membre']['pseudo'] = $infos['pseudo'];
            $_SESSION['membre']['nom'] = $infos['nom'];
            $_SESSION['membre']['prenom'] = $infos['prenom'];
            $_SESSION['membre']['email'] = $infos['email'];
            $_SESSION['membre']['civilite'] = $infos['civilite'];
            $_SESSION['membre']['statut'] = $infos['statut'];

            // on redirige sur profil.php
            header('location: profil.php');
        } else {
            // mdp incorrect
            $msg .= '<div class="alert alert-danger mb-3">Attention,<br>Erreur sur le pseudo et/ou le mot de passe.</div>';
        }
    }
}






// Début des affichages
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
?>
<div class="bg-light p-5 rounded text-center">
    <h1><i class="fa-solid fa-bag-shopping text-royalblue"></i> Connexion <i class="fa-solid fa-bag-shopping text-royalblue"></i></h1>
    <p class="lead">Bienvenue sur notre boutique.</p>
</div>

<div class="row mt-4">
    <div class="col-sm-4 mx-auto">
        <?= $msg; // affichage des messages utilisateur  
        ?>
        <form method="post" action="" class="p-3 border">
            <div class="mb-3">
                <label for="pseudo">Pseudo</label>
                <input type="text" name="pseudo" id="pseudo" class="form-control" value="">
            </div>
            <div class="mb-3">
                <label for="mdp">Mot de passe</label>
                <input type="text" name="mdp" id="mdp" class="form-control" value="">
            </div>
            <div class="mb-3">
                <button type="submit" id="connexion" class="w-100 btn btn-outline-primary"><i class="fa-solid fa-right-to-bracket"></i> Connexion <i class="fa-solid fa-right-to-bracket"></i></button>
            </div>
        </form>

    </div>
</div>


<?php
include 'inc/footer.inc.php';
