<?php
include '../inc/init.inc.php';
include '../inc/functions.inc.php';

if (!user_is_admin()) {
    header('location:../connexion.php');
    exit();

}

// CODE ...
$pseudo = '';
$nom = '';
$prenom = '';
$email = '';
$civilite = '';
$id_membre = '';

$erreur = false;


//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
// Modification membre
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_membre'])) {
    $recup_membre = $pdo->prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
    $recup_membre->bindParam(':id_membre', $_GET['id_membre'], PDO::PARAM_STR);
    $recup_membre->execute();

    if ($recup_membre->rowCount() > 0) {
        $infos_membre = $recup_membre->fetch(PDO::FETCH_ASSOC);

        $id_membre = $infos_membre['id_membre']; // utilisée pour la modif

        $pseudo = $infos_membre['pseudo'];
        $nom = $infos_membre['nom'];
        $prenom = $infos_membre['prenom'];
        $civilite = $infos_membre['civilite'];
        $email = $infos_membre['email'];
        $statut = $infos_membre['statut'];
    }
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
// enregistrement membre
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------


if (isset($_POST['pseudo']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['civilite']) && isset($_POST['email']) && isset($_POST['statut']) && isset($_POST['id_membre'])) {


    $id_membre = trim($_POST['id_membre']);
    $pseudo = trim($_POST['pseudo']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $civilite = trim($_POST['civilite']);
    $email = trim($_POST['email']);
    $statut = trim($_POST['statut']);


    $erreur = false;
    if (!$erreur) {

        // si l'id_produit est vide : INSERT INTO sinon : UPDATE
        if (!empty($id_membre)) {
            // Modification du produit 
            $enregistrement = $pdo->prepare("UPDATE membre SET pseudo = :pseudo, nom = :nom, prenom = :prenom, civilite = :civilite, email= :email, statut = :statut WHERE id_membre = :id_membre");
            $enregistrement->bindParam(':id_membre', $id_membre, PDO::PARAM_STR);
            $enregistrement->bindParam(':statut', $statut, PDO::PARAM_STR);


            // on crée un message dans la session pour confirmation la modif:
            $_SESSION['message_utilisateur'] .= '<div class="alert alert-success mb-3">Le produit n°' . $id_membre . ' a bien été modifié.</div>';
        } 
        
        else {
            // Enregistrement du produit 
            $enregistrement = $pdo->prepare("INSERT INTO membre (id_membre, pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES (NULL, :pseudo, :mdp, :nom, :prenom, :email, :civilite, 1, NOW())");
        }
        // $enregistrement->bindParam(':id_membre',$id_membre,PDO::PARAM_STR);
        $enregistrement->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $enregistrement->bindParam(':nom', $nom, PDO::PARAM_STR);
        $enregistrement->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $enregistrement->bindParam(':email', $email, PDO::PARAM_STR);
        $enregistrement->bindParam(':civilite', $civilite, PDO::PARAM_STR);
        $enregistrement->execute();

        // header('location: gestion_produit.php');
        // exit();
    }
}
// Message si modification
if (!empty($_SESSION['message_utilisateur'])) {
    $msg .= $_SESSION['message_utilisateur']; // on affiche le message
    $_SESSION['message_utilisateur'] = ''; // on vide le message
}





//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
// Suppression Membre
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_membre'])) {

    // on va chercher en bdd les infos de ce produit afin de connaitre la photo qui doit être supprimée
    $recup_membre = $pdo->prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
    $recup_membre->bindParam(':id_membre', $_GET['id_membre'], PDO::PARAM_STR);
    $recup_membre->execute();



    $suppression = $pdo->prepare("DELETE FROM membre WHERE id_membre = :id_membre");
    $suppression->bindParam(':id_membre', $_GET['id_membre'], PDO::PARAM_STR);
    $suppression->execute();
    $msg .= '<div class="alert alert-success mb-3">Le membre n°' . $_GET['id_membre'] . ' a bien été supprimé.</div>';
}










$liste_produits = $pdo->query("SELECT * FROM membre ORDER BY nom");

// Début des affichages
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
?>
<div class="bg-light p-5 rounded text-center">
    <h1><i class="fa-solid fa-bag-shopping text-royalblue"></i> Gestion membre <i class="fa-solid fa-bag-shopping text-royalblue"></i></h1>
    <p class="lead">Bienvenue sur notre boutique.</p>
</div>

<div class="row mt-4">
    <div class="col-12">
        <table class="table table-bordered">
            <thead class="bg-royalblue text-white">
                <tr>
                    <th>Id membre</th>
                    <th>Pseudo</th>
                    <th>Nom</th>
                    <th>Prenom</th>
                    <th>Email</th>
                    <th>Civilite</th>
                    <th>Statut</th>
                    <th>Date_enregistrement</th>
                    <th>Modification</th>
                    <th>suppression</th>

                </tr>
            </thead>
            <tbody>
                <?php
                while ($ligne = $liste_produits->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $ligne['id_membre'] . '</td>';
                    echo '<td>' . $ligne['pseudo'] . '</td>';
                    echo '<td>' . $ligne['nom'] . '</td>';
                    echo '<td>' . $ligne['prenom'] . '</td>';
                    // echo '<td>' . substr($ligne['description'], 0, 5) . ' <a href="#">...</a></td>';
                    echo '<td>' . $ligne['email'] . '</td>';
                    echo '<td>' . $ligne['civilite'] . '</td>';
                    echo '<td>' . $ligne['statut'] . '</td>';
                    //  echo '<td><img src="' . URL . 'assets/img_produit/' . $ligne['photo'] . '" width="50"></td>';
                    echo '<td>' . $ligne['date_enregistrement'] . '</td>';
                    //  echo '<td>' . $ligne['stock'] . '</td>';

                    echo '<td><a href="?action=modifier&id_membre=' . $ligne['id_membre'] . '" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i></a></td>';

                    echo '<td><a href="?action=supprimer&id_membre=' . $ligne['id_membre'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr de bien vouloir supprimer cette personne ?\'))"><i class="fa-solid fa-trash-can"></i></a></td>';

                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
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
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" id="nom" class="form-control" value="<?= $nom; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="prenom">Prénom</label>
                        <input type="text" name="prenom" id="prenom" class="form-control" value="<?= $prenom; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="id_membre"></label>
                        <input readonly type="hidden" name="id_membre" id="id_membre" class="form-control" value="<?= $id_membre;
                                                                                                                ?>">
                    </div>


                </div>






                <div class="col-sm-6">




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

                    <div class="mb-3">
                        <label for="statut">statut</label>
                        <select name="statut" id="statut" class="form-select">
                            <option value="2">Admin</option>
                            <option value="1">Membre</option>
                        </select>
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
include '../inc/footer.inc.php';
