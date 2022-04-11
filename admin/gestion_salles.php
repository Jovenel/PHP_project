<?php
include '../inc/init.inc.php';
include '../inc/functions.inc.php';

if (!user_is_admin()) {
    header('location:../connexion.php');
    exit();

}

// CODE ...

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
// Suppression produit
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_salle'])) {

    // on va chercher en bdd les infos de ce produit afin de connaitre la photo qui doit être supprimée
    $recup_photo = $pdo->prepare("SELECT * FROM salle WHERE id_salle = :id_salle");
    $recup_photo->bindParam(':id_salle', $_GET['id_salle'], PDO::PARAM_STR);
    $recup_photo->execute();

    if ($recup_photo->rowCount() > 0) {
        $infos = $recup_photo->fetch(PDO::FETCH_ASSOC);
        $chemin_photo = ROOT_PATH . ROOT_SITE . 'assets/img_produit/' . $infos['photo'];
        if (!empty($infos['photo']) && file_exists($chemin_photo)) {
            // unlink() permet de supprimer un fichier sur le serveur
            unlink($chemin_photo);
        }
    }

    $suppression = $pdo->prepare("DELETE FROM salle WHERE id_salle = :id_salle");
    $suppression->bindParam(':id_salle', $_GET['id_salle'], PDO::PARAM_STR);
    $suppression->execute();
    $msg .= '<div class="alert alert-success mb-3">Le salle n°' . $_GET['id_salle'] . ' a bien été supprimé.</div>';
}


$id_salle = '';
$titre = '';
$description = '';
$photo = '';
$pays = '';
$ville = '';
$adresse = '';
$cp = '';
$capacite = '';
$categorie = '';

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
// Modification produit
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_salle'])) {
    $recup_salle = $pdo->prepare("SELECT * FROM salle WHERE id_salle = :id_salle");
    $recup_salle->bindParam(':id_salle', $_GET['id_salle'], PDO::PARAM_STR);
    $recup_salle->execute();

    if ($recup_salle->rowCount() > 0) {
        $infos_salle = $recup_salle->fetch(PDO::FETCH_ASSOC);

        $id_salle = $infos_salle['id_salle']; // utilisée pour la modif

        $titre = $infos_salle['titre'];
        $description = $infos_salle['description'];
        $photo_actuelle = $infos_salle['photo']; // utilisée pour conserver l'ancienne photo pour la modif
        $pays = $infos_salle['pays'];
        $ville = $infos_salle['ville'];
        $adresse = $infos_salle['adresse'];
        $cp = $infos_salle['cp'];
        $capacite = $infos_salle['capacite'];
        $categorie = $infos_salle['categorie'];
    }
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
// Enregistrement salle
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

if (isset($_POST['titre']) && isset($_POST['description']) && isset($_POST['pays']) && isset($_POST['ville']) && isset($_POST['adresse']) && isset($_POST['cp']) && isset($_POST['capacite']) && isset($_POST['categorie'])) {


    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    // $photo='';
    $pays = trim($_POST['pays']);
    $ville = trim($_POST['ville']);
    $adresse = trim($_POST['adresse']);
    $cp = trim($_POST['cp']);
    $capacite = trim($_POST['capacite']);
    $categorie = trim($_POST['categorie']);

    $erreur = false;

    // Pour la modification :
    // récupération de l'id_salle et de la photo actuelle
    if (!empty($_POST['id_salle'])) {
        $id_salle = $_POST['id_salle'];
    }
    if (!empty($_POST['photo_actuelle'])) {
        $photo = $_POST['photo_actuelle'];
    }

    // echo'test';

    // Contrôles sur la photo
    // Superglobale pour les pièces jointes d'un formulaire : $_FILES (obligatoire de mettre l'attribut enctype="" sur le form)
    if (!empty($_FILES['photo']['name']) && !$erreur) {
        // on déclare un tableau avec les formats acceptés 
        $tab_formats = array('png', 'jpg', 'jpeg', 'gif', 'webp');

        // on récupère le format du fichier chargé : on découpe depuis la fin et on remonte au point : strrchr()
        $extension = strrchr($_FILES['photo']['name'], '.'); // exemple : pour le fichier photo.png on récupère .png

        // On enlève le . de la chaine et on passe la chaine en minuscule :
        $extension = strtolower(substr($extension, 1)); // exemple : .png on obtient png

        // on vérifie si l'extension correspond à une des valeurs placées dans le tableau array :
        // in_array('valeur', 'tableau');
        if (in_array($extension, $tab_formats)) {

            // le nom de la photo peut correspondre à une autre photo déjà enregistrée. Pour éviter de l'écraser, on place la référence (qui est unique) devant le nom de la photo
            $photo =  '-' . $_FILES['photo']['name'];

            // on enlève les caractères spéciaux
            $photo = preg_replace('/[^a-zA-Z0-9._-]/', '', $photo);

            // copy(emplacement, dossier cible)
            $dossier_cible = ROOT_PATH . ROOT_SITE . 'assets/img/' . $photo;
            copy($_FILES['photo']['tmp_name'], $dossier_cible);
        } else {
            $msg .= '<div class="alert alert-danger mb-3">Attention,<br>la photo n\'a pas un format valide pour le web.</div>';
            // cas d'erreur 
            $erreur = true;
        }
    }


    if (!$erreur) {

        // si l'id_produit est vide : INSERT INTO sinon : UPDATE
        if (!empty($id_salle)) {
            // Modification du produit 
            $enregistrement = $pdo->prepare("UPDATE salle SET titre = :titre, description = :description, photo = :photo, categorie = :categorie, capacite = :capacite, pays = :pays, ville = :ville, adresse = :adresse, cp = :cp WHERE id_salle = :id_salle");
            $enregistrement->bindParam(':id_salle', $id_salle, PDO::PARAM_STR);

            // on crée un message dans la session pour confirmation la modif:
            $_SESSION['message_utilisateur'] .= '<div class="alert alert-success mb-3">Le produit n°' . $id_salle . ' a bien été modifié.</div>';
        } else {
            // Enregistrement du produit 
            $enregistrement = $pdo->prepare("INSERT INTO salle (id_salle, titre, description, photo, categorie, capacite, pays, ville, adresse, cp) VALUES (NULL, :titre, :description, :photo, :categorie, :capacite, :pays, :ville, :adresse, :cp)");
            // $enregistrement->bindParam(':titre', $titre, PDO::PARAM_STR);
        }

        $enregistrement->bindParam(':titre', $titre, PDO::PARAM_STR);
        $enregistrement->bindParam(':description', $description, PDO::PARAM_STR);
        $enregistrement->bindParam(':photo', $photo, PDO::PARAM_STR);
        $enregistrement->bindParam(':categorie', $categorie, PDO::PARAM_STR);
        $enregistrement->bindParam(':capacite', $capacite, PDO::PARAM_STR);
        $enregistrement->bindParam(':pays', $pays, PDO::PARAM_STR);
        $enregistrement->bindParam(':ville', $ville, PDO::PARAM_STR);
        $enregistrement->bindParam(':adresse', $adresse, PDO::PARAM_STR);
        $enregistrement->bindParam(':cp', $cp, PDO::PARAM_STR);
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




// Récupération des produits en BDD
$liste_salle = $pdo->query("SELECT * FROM salle ORDER BY titre");
// echo '<pre>';
// print_r($liste_salle);
// echo '</pre>';

// Début des affichages
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
?>
<div class="bg-light p-5 rounded text-center">
    <h1><i class="fa-solid fa-bag-shopping text-royalblue"></i> Gestion Salles <i class="fa-solid fa-bag-shopping text-royalblue"></i></h1>
    <p class="lead">Bienvenue sur notre boutique.</p>
</div>

<div class="row mt-4">
    <div class="col-12">
        <form method="post" action="" class="border p-3 row" enctype="multipart/form-data">
            <!-- champ caché id_salle pour la modification -->
            <input type="hidden" name="id_salle" id="id_salle" value="<?= $id_salle; ?>">
            <!-- champ caché id_produit pour la modification -->
            <div class="col-sm-6">

                <div class="mb-3">
                    <label for="titre">Titre</label>
                    <input type="text" name="titre" id="titre" class="form-control" value="<?= $titre; ?>">
                </div>
                <div class="mb-3">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4"><?= $description; ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="photo">Photo</label>
                    <input type="file" name="photo" id="photo" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="categorie">Catégorie</label>
                    <select name="categorie" id="categorie" class="form-select">
                        <option>Réunion</option>
                        
                        <option <?php if ($categorie == 'Bureau') {
                                    echo ' selected ';
                                } ?>>Bureau</option>
                        <option <?php if ($categorie == 'Formation') {
                                    echo ' selected ';
                                } ?>>Formation</option>
                    </select>
                </div>


            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="capacite">Capacité</label>
                    <select name="capacite" id="capacite" class="form-select">
                        <option>8</option>
                        <option <?php if ($capacite == '12') {
                                    echo ' selected ';
                                } ?>>12</option>
                        <option <?php if ($capacite == '16') {
                                    echo ' selected ';
                                } ?>>16</option>
                        <option <?php if ($capacite == '20') {
                                    echo ' selected ';
                                } ?>>20</option>
                        <option <?php if ($capacite == '30') {
                                    echo ' selected ';
                                } ?>>30</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="pays">Pays</label>
                    <select name="pays" id="pays" class="form-select">
                        <option value="france">France</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="ville">Ville</label>
                    <select name="ville" id="ville" class="form-select">
                        <option value="paris">Paris</option>
                        <option value="lyon" <?php if ($ville == 'lyon') {
                                                    echo ' selected ';
                                                } ?>>lyon</option>
                        <option value="marseille" <?php if ($ville == 'marseille') {
                                                    echo ' selected ';
                                                } ?>>Marseille</option>
                    </select>
                </div>

                <?php
                // conservation de l'ancienne image lors d'une modification produit.
                if (!empty($photo_actuelle)) {
                    echo '<div class="mb-3">';
                    echo '<label for="photo_actuelle">Photo actuelle</label><hr>';
                    echo '<img src="' . URL . 'assets/img/' . $photo_actuelle . '" width="100">';
                    echo '<input type="hidden" name="photo_actuelle" value="' . $photo_actuelle . '">';
                    echo '</div>';
                }

                ?>


                <div class="mb-3">
                    <label for="adresse">Adresse</label>
                    <input type="text" name="adresse" id="adresse" class="form-control" value="<?= $adresse; ?>">
                </div>
                <div class="mb-4">
                    <label for="cp">Code postal</label>
                    <input type="text" name="cp" id="cp" class="form-control" value="<?= $cp; ?>">
                </div>
                <div class="mb-3">
                    <button type="submit" id="enregistrement_produit" class="w-100 btn btn-outline-primary"><i class="fa-solid fa-right-to-bracket"></i> Enregistrer <i class="fa-solid fa-right-to-bracket"></i></button>
                </div>
            </div>
        </form>

    </div>
</div>

<div class="row mt-4">
    <div class="col-sm-12">
        <?= $msg; // affichage des messages utilisateur  
        ?>
        <table class="table table-bordered">
            <thead class="bg-royalblue text-white">
                <tr>
                    <th>Id Salles</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Photo</th>
                    <th>Pays</th>
                    <th>Ville</th>
                    <th>Adresse</th>
                    <th>Code postal</th>
                    <th>Capacité</th>
                    <th>Catégorie</th>
                    <th>Modification</th>
                    <th>Suppression</th>

                </tr>
            </thead>
            <tbody>
                <?php
                while ($ligne = $liste_salle->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $ligne['id_salle'] . '</td>';
                    echo '<td>' . $ligne['titre'] . '</td>';
                    echo '<td>' . substr($ligne['description'], 0, 50) . ' <a href="#">...</a></td>';
                    echo '<td><img src="' . URL . 'assets/img/' . $ligne['photo'] . '" width="50"></td>';
                    echo '<td>' . $ligne['pays'] . '</td>';
                    echo '<td>' . $ligne['ville'] . '</td>';
                    echo '<td>' . $ligne['adresse'] . '</td>';
                    echo '<td>' . $ligne['cp'] . '</td>';
                    echo '<td>' . $ligne['capacite'] . '</td>';
                    echo '<td>' . $ligne['categorie'] . '</td>';
                    echo '<td><a href="?action=modifier&id_salle=' . $ligne['id_salle'] . '" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i></a></td>';

                    echo '<td><a href="?action=supprimer&id_salle=' . $ligne['id_salle'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr de vouloir supprimer cette salle ?\'))"><i class="fa-solid fa-trash-can"></i></a></td>';

                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>




    </div>
</div>


<?php
include '../inc/footer.inc.php';
