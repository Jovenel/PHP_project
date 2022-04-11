<?php
include '../inc/init.inc.php';
include '../inc/functions.inc.php';

if (!user_is_admin()) {
    header('location:../connexion.php');
    exit();

}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
// Suppression produit
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_produit'])) {

    // on va chercher en bdd les infos de ce produit afin de connaitre la photo qui doit être supprimée
    $recup_photo = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
    $recup_photo->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
    $recup_photo->execute();

    if ($recup_photo->rowCount() > 0) {
        $infos = $recup_photo->fetch(PDO::FETCH_ASSOC);
        $chemin_photo = ROOT_PATH . ROOT_SITE . 'assets/img/' . $infos['photo'];
        if (!empty($infos['photo']) && file_exists($chemin_photo)) {
            // unlink() permet de supprimer un fichier sur le serveur
            unlink($chemin_photo);
        }
    }

    $suppression = $pdo->prepare("DELETE FROM produit WHERE id_produit = :id_produit");
    $suppression->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
    $suppression->execute();
    $msg .= '<div class="alert alert-success mb-3">Le produit n°' . $_GET['id_produit'] . ' a bien été supprimé.</div>';
}

// CODE ...
$prix = '';
$date_arrivee = '';
$date_depart = '';
$id_salle = '';
$id_produit = '';
$etat = '';

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
// Modification produit
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_produit'])) {
    $recup_produit = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
    $recup_produit->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
    $recup_produit->execute();

    if ($recup_produit->rowCount() > 0) {
        $infos_produit = $recup_produit->fetch(PDO::FETCH_ASSOC);

        $id_produit = $infos_produit['id_produit']; // utilisée pour la modif
        $id_salle = $infos_produit['id_salle'];
        $date_arrivee = $infos_produit['date_arrivee'];
        $date_depart = $infos_produit['date_depart'];
        $prix = $infos_produit['prix'];
        $etat = $infos_produit['etat'];
    }
}

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
// Enregistrement produit 
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

if (isset($_POST['date_arrivee']) && isset($_POST['date_depart']) && isset($_POST['prix']) && isset($_POST['etat']) && isset($_POST['id_salle'])) {
    echo 'test';
    $prix = trim($_POST['prix']);
    $date_arrivee = trim($_POST['date_arrivee']);
    $date_depart = trim($_POST['date_depart']);
    $id_salle = trim($_POST['id_salle']);
    // $id_produit= trim($_POST['']);
    $etat = trim($_POST['etat']);
    $erreur = false;
    // Pour la modification :
    // récupération de l'id_produit

    if (!empty($_POST['id_produit'])) {
        $id_produit = $_POST['id_produit'];
    }
    // Si prix et stock sont vides, on affecte 0 pour éviter une erreur sql
    if (empty($prix) || !is_numeric($prix)) {
        $_SESSION['message_utilisateur'] .= '<div class="alert alert-warning mb-3">Attention,<br>le prix a été affecté à 0.</div>';
        $prix = 0;
    }

    if (!$erreur) {

        // si l'id_produit est vide : INSERT INTO sinon : UPDATE
        if (!empty($id_produit)) {
            // Modification du produit 
            $enregistrement = $pdo->prepare("UPDATE produit SET date_arrivee= :date_arrivee, id_salle= :id_salle, date_depart= :date_depart, prix= :prix, etat= :etat WHERE id_produit = :id_produit");
            $enregistrement->bindParam(':id_produit', $id_produit, PDO::PARAM_STR);

            // on crée un message dans la session pour confirmation la modif:
            $_SESSION['message_utilisateur'] .= '<div class="alert alert-success mb-3">Le produit n°' . $id_produit . ' a bien été modifié.</div>';
        } else {
            // Enregistrement du produit 
            $enregistrement = $pdo->prepare("INSERT INTO produit (id_produit, id_salle, date_arrivee, date_depart, prix, etat) VALUES (NULL, :id_salle, :date_arrivee, :date_depart, :prix, :etat)");
        }
        $enregistrement->bindParam(':date_arrivee', $date_arrivee, PDO::PARAM_STR);
        $enregistrement->bindParam(':date_depart', $date_depart, PDO::PARAM_STR);
        $enregistrement->bindParam(':prix', $prix, PDO::PARAM_STR);
        $enregistrement->bindParam(':etat', $etat, PDO::PARAM_STR);
        $enregistrement->bindParam(':id_salle', $id_salle, PDO::PARAM_STR);
        $enregistrement->execute();
    }
}
// Message si modification
if (!empty($_SESSION['message_utilisateur'])) {
    $msg .= $_SESSION['message_utilisateur']; // on affiche le message
    $_SESSION['message_utilisateur'] = ''; // on vide le message
}


$liste_produits = $pdo->query("SELECT * FROM produit, salle WHERE produit.id_salle = salle.id_salle ORDER BY prix");

// Début des affichages
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
print_r($_POST);
?>
<div class="bg-light p-5 rounded text-center">
    <h1><i class="fa-solid fa-bag-shopping text-royalblue"></i> Gestion de produits <i class="fa-solid fa-bag-shopping text-royalblue"></i></h1>
    <p class="lead">Bienvenue sur notre Site.</p>
</div>

<div class="row mt-4">
    <div class="col-sm-12">

        <table class="table table-bordered">
            <thead class="bg-royalblue text-white">
                <tr>
                    <th>Id Produit</th>
                    <th>Datte d'arrivée</th>
                    <th>Datte de depart</th>
                    <th>ID salle</th>
                    <th>Prix</th>
                    <th>Etat</th>
                    <th>Modification</th>
                    <th>suppression</th>

                </tr>
            </thead>
            <tbody>
                <?php
                while ($ligne = $liste_produits->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $ligne['id_produit'] . '</td>';
                    echo '<td>' . $ligne['date_arrivee'] . '</td>';
                    echo '<td>' . $ligne['date_depart'] . '</td>';
                    echo '<td>' . $ligne['id_salle'].'-' .$ligne['titre']. '<br>'. '<img src="' . URL . 'assets/img/'.$ligne['photo'].'"width="100"'. '</td>';
                    // echo '<td>' . substr($ligne['description'], 0, 5) . ' <a href="#">...</a></td>';
                    echo '<td>' . $ligne['prix'] .' € '. '</td>';
                    echo '<td>' . $ligne['etat'] . '</td>';
                    echo '<td><a href="?action=modifier&id_produit=' . $ligne['id_produit'] . '" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i></a></td>';

                    echo '<td><a href="?action=supprimer&id_produit=' . $ligne['id_produit'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr de bien vouloir supprimer cette personne ?\'))"><i class="fa-solid fa-trash-can"></i></a></td>';

                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>


        <?= $msg; // affichage des messages utilisateur  
        ?>
        <form method="post" action="" class="border p-3">
            <div class="row">
                <div class="col-sm-6">
                    <!-- champ caché id_salle pour la modification -->
                    <input type="hidden" readonly name="id_produit" id="id_produit" value="<?= $id_produit; ?>">
                    <input type="hidden" readonly name="id_salle" id="id_salle" value="<?= $id_salle; ?>">
                    <!-- champ caché id_produit pour la modification -->

                    <div class="mb-3"> Date d'arrivée
                        <input type="datetime-local" name="date_arrivee" id="todays-date" value="<?= $date_arrivee; ?>" class="form-control">
                    </div>
                    <div class="mb-3">Date de depart
                        <input type="datetime-local" name="date_depart"id="todays-date" value=" 2022-03-21 <?= $date_depart; ?>" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="etat">Etat</label>
                        <select name="etat" id="etat" class="form-select">
                            <option value="libre">Libre</option>
                            <option value="reservation">Réservation</option>
                        </select>
                    </div>
                </div>


                <div class="col-sm-6">

                    <div>Tarif
                        <i class="fa-solid fa-euro-sign"></i><input type="text" name="prix" id="prix" class="form-control" value="<?= $prix; ?>">
                    </div>

                    <label for="salle" class="mt-3"> Salle</label>
                    <?php $list_salle = $pdo->query("SELECT id_salle, titre, ville, cp, adresse, categorie FROM salle");
                    // echo '<pre>';
                    // print_r($list);
                    // echo '</pre>';
                    // $listabonne = $pdo->query("SELECT prenom FROM abonne");

                    echo '<select name="id_salle" id="salle" class="form-select">';


                    while ($list = $list_salle->fetch(PDO::FETCH_ASSOC)) {

                        // foreach ($list as $_salle) {
                        echo '<option value="' . $list['id_salle'] . '">'  . '-' . $list['titre'] . '-' . $list['ville'] . '-' . $list['cp'] . '-' . $list['adresse'] . '-' . $list['categorie'] . '</option>';
                        // }


                    }
                    echo '</select>';
                    ?>

                    <div class="mb-3 mt-3">
                        <button type="submit" id="inscription" class="w-100 btn btn-outline-primary mt-3"><i class="fa-solid fa-right-to-bracket"></i> Enregistrement <i class="fa-solid fa-right-to-bracket"></i></button>
                    </div>

        </form>
    </div>
</div>



</div>
</div>

<?php
// include '../atelierRoom/assets/js/script.js';
include '../inc/footer.inc.php';
