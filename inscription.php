<?php
include 'inc/init.inc.php';
include 'inc/functions.inc.php';

// Restriction d'accès : si l'utilisateur est connecté, on le redirige sur profil.php
if (user_is_connected()) {
    header('location: profil.php');
}


$pseudo = '';
$nom = '';
$prenom = '';
$email = '';
$civilite = '';



if (isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['confirm_mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['civilite']) && isset($_POST['email'])) {
echo 'test';
    $pseudo = trim($_POST['pseudo']);
    $mdp = trim($_POST['mdp']);
    $confirm_mdp = trim($_POST['confirm_mdp']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $civilite = trim($_POST['civilite']);
    $email = trim($_POST['email']);



    // Création d'une variable de contrôle permettant plus bas de savori s'il y a eu un cas d'erreur
    $erreur = 'non';


    // vérifier la taille du pseudo
    if (iconv_strlen($pseudo) < 4 || iconv_strlen($pseudo) > 14) {
        $msg .= '<div class="alert alert-danger mb-3">Attention,<br>le pseudo doit avoir entre 4 et 14 caractères inclus.</div>';
        // cas d'erreur
        $erreur = 'oui';
    }

    // vérification des caractères du pseudo : uniquement chiffres, lettres _ - . (pas de caractère spécial)
    $verif_caractere = preg_match('#^[a-zA-Z0-9._-]+$#', $pseudo);
    /*
    Regex (expression régulière)
    
    */
    // if(!$verif_caractere) {
    if ($verif_caractere == false) {
        $msg .= '<div class="alert alert-danger mb-3">Attention,<br>caractères autorisés pour le pseudo : a-z 0-9 _ . -</div>';
        // cas d'erreur
        $erreur = 'oui';
    }

    // Vérification de la disponibilité du pseudo
    $verif_pseudo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    $verif_pseudo->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $verif_pseudo->execute();
    // s'il y a plus de zéro ligne : le pseudo n'est pas disponible
    if ($verif_pseudo->rowCount() > 0) {
        $msg .= '<div class="alert alert-danger mb-3">Attention,<br>Pseudo indisponible.</div>';
        // cas d'erreur
        $erreur = 'oui';
    }


    // vérifier que le mdp n'est pas vide    
    if (empty($mdp)) {
        $msg .= '<div class="alert alert-danger mb-3">Attention,<br>le mot de passe est obligatoire.</div>';
        // cas d'erreur
        $erreur = 'oui';
    } else {
        // vérifier que le mdp et le confirm_mdp sont similaire si le mdp n'est pas vide
        if ($mdp != $confirm_mdp) {
            $msg .= '<div class="alert alert-danger mb-3">Attention,<br>le mot de passe et la confirmation du mot de passe doivent être identiques.</div>';
            // cas d'erreur
            $erreur = 'oui';
        }
    }

    // Vérification du format mail
    if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        $msg .= '<div class="alert alert-danger mb-3">Attention,<br>le format du mail n\'est pas correct.</div>';
        // cas d'erreur
        $erreur = 'oui';
    }



    // enregistrement en BDD
    if ($erreur == 'non') {
        // pas de cas d'erreur, on peut enregistrer.

        // cryptage du mdp : password_hash()
        // https://www.php.net/manual/fr/function.password-hash.php
        $mdp = password_hash($mdp, PASSWORD_DEFAULT);

        /*
            valeur pour le statut :
            1 : membre
            2 : administrateur
            3 : référenceur
            4 : commercial
            ...
        */ 
        $enregistrement = $pdo->prepare("INSERT INTO membre (id_membre, pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES (NULL, :pseudo, :mdp, :nom, :prenom, :email, :civilite, 1, NOW())");

        echo '<pre>';
        print_r($enregistrement);
        echo '</pre>';
        
        $enregistrement->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $enregistrement->bindParam(':mdp', $mdp, PDO::PARAM_STR);
        $enregistrement->bindParam(':nom', $nom, PDO::PARAM_STR);
        $enregistrement->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $enregistrement->bindParam(':email', $email, PDO::PARAM_STR);
        $enregistrement->bindParam(':civilite', $civilite, PDO::PARAM_STR);
        $enregistrement->execute();

        // Créez les deux comptes suivants
        // login : jovenel | mdp : xmljsonpython
        // login : test | mdp : test

        // on redirige sur la page connexion.php
        header('location: connexion.php');
    } 
}  // FIN DES IF ISSET






// Début des affichages
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
echo '<pre>';
print_r($_POST);
echo '</pre>';
?>
<div class="bg-light p-5 rounded text-center">
    <h1><i class="fa-solid fa-bag-shopping text-royalblue"></i> Inscription <i class="fa-solid fa-bag-shopping text-royalblue"></i></h1>
    <p class="lead">Bienvenue sur notre site.</p>
</div>

<div class="row mt-4">
    <div class="col-sm-12">

        <?= $msg; // affichage des messages utilisateur  
        ?>

        <form method="post" action="" class="border p-3">
            <div class="row">



                <div class="col-sm-6">
                    <div class="mb-3">
                        <label for="pseudo">Pseudo</label>
                        <input type="text" name="pseudo" id="pseudo" class="form-control" value="<?= $pseudo; 
                                                                                                    ?>">
                    </div>
                    <div class="mb-3">
                        <label for="mdp">Mot de passe</label>
                        <input type="password" name="mdp" id="mdp" class="form-control" value="">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_mdp">Confirmation du mot de passe</label>
                        <input type="password" name="confirm_mdp" id="confirm_mdp" class="form-control" value="">
                    </div>

                    <div class="mb-3">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" id="nom" class="form-control" value="<?= $nom; ?>">
                    </div>
                    
                    
                </div>






                <div class="col-sm-6">


                <div class="mb-3">
                        <label for="prenom">Prénom</label>
                        <input type="text" name="prenom" id="prenom" class="form-control" value="<?= $prenom; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="civilite">civilite</label>
                        <select name="civilite" id="civilite" class="form-select">
                            <option value="m">Homme</option>
                            <option value="f">Femme</option>
                        </select>
                    </div>

                    
                    

                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= $email; ?>">
                    </div>
                    <div class="mb-3 mt-3">
                        <button type="submit" id="inscription" class="w-100 btn btn-outline-primary mt-3"><i class="fa-solid fa-right-to-bracket"></i> Inscription <i class="fa-solid fa-right-to-bracket"></i></button>
                    </div>






                </div>





            </div>
        </form>
    </div>
</div>


<?php
include 'inc/footer.inc.php';
