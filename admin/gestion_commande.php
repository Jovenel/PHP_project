<?php
include '../inc/init.inc.php';
include '../inc/functions.inc.php';
// restriction
if (!user_is_admin()) {
    header('location:../connexion.php');
    exit();

}

// CODE ...
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_commande'])) {

    // on va chercher en bdd les infos de ce produit afin de connaitre la photo qui doit être supprimée
    $recup_photo = $pdo->prepare("SELECT * FROM commande WHERE id_commande = :id_commande");
    $recup_photo->bindParam(':id_commande', $_GET['id_commande'], PDO::PARAM_STR);
    $recup_photo->execute();

    $suppression = $pdo->prepare("DELETE FROM commande WHERE id_commande = :id_commande");
    $suppression->bindParam(':id_commande', $_GET['id_commande'], PDO::PARAM_STR);
    $suppression->execute();
    $msg .= '<div class="alert alert-success mb-3">La commande n°' . $_GET['id_commande'] . ' a bien été supprimé.</div>';
   
   // Update satus
    $req_preparee = $pdo->prepare("UPDATE produit SET etat = 'Libre' WHERE produit.id_produit = :id_produit");
    $req_preparee->bindParam(':id_produit', $id_produit, PDO::PARAM_STR);
    $req_preparee->execute();
}





$liste_commande = $pdo->query("SELECT * FROM commande ");
// Début des affichages
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
?>
<div class="bg-light p-5 rounded text-center">
    <h1><i class="fa-solid fa-bag-shopping text-royalblue"></i> Gestion commande <i class="fa-solid fa-bag-shopping text-royalblue"></i></h1>
    <p class="lead">Bienvenue sur notre boutique.</p>
</div>

<div class="row mt-4">
    <div class="col-sm-12">
        <?= $msg; // affichage des messages utilisateur  
        ?>
<table class="table table-bordered">
            <thead class="bg-royalblue text-white">
                <tr>
                    <th>Id commande</th>
                    <th>Id membre</th>
                    <th>ID produit</th>
                    <th>Date d'enregistrement</th>
                    
                    <th>suppression</th>

                </tr>
            </thead>
            <tbody>
                <?php
                while ($ligne = $liste_commande->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $ligne['id_commande'] . '</td>';
                    echo '<td>' . $ligne['id_membre'] . '</td>';
                    echo '<td>' . $ligne['id_produit'] . '</td>';
                    // echo '<td>' . substr($ligne['description'], 0, 5) . ' <a href="#">...</a></td>';
                    echo '<td>' . $ligne['date_enregistrement'] . '</td>';

                    echo '<td><a href="?action=supprimer&id_commande=' . $ligne['id_commande'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr de bien vouloir supprimer cette commande ?\'))"><i class="fa-solid fa-trash-can"></i></a></td>';

                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>

    </div>
</div>


<?php
include '../inc/footer.inc.php';
