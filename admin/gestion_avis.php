<?php
include '../inc/init.inc.php';
include '../inc/functions.inc.php';

if (!user_is_admin()) {
    header('location:../connexion.php');
    exit();

}

// CODE ...





$liste_produits =$pdo->query("SELECT * FROM avis, membre, salle WHERE avis.id_membre = membre.id_membre AND avis.id_salle = salle.id_salle ORDER BY note");
// Début des affichages
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
?>
<div class="bg-light p-5 rounded text-center">
    <h1><i class="fa-solid fa-bag-shopping text-royalblue"></i> Gestion Avis <i class="fa-solid fa-bag-shopping text-royalblue"></i></h1>
    <p class="lead">Bienvenue sur notre boutique.</p>
</div>

<div class="row mt-4">
    <div class="col-sm-12">
    <table class="table table-bordered">
            <thead class="bg-royalblue text-white">
                <tr>
                    <th>Id Avis</th>
                    <th>Id membre</th>
                    <th>ID salle</th>
                    <th>Commentaire</th>
                    <th>note</th>
                    <th>Date enregistrement</th>
                    <th>Modification</th>
                    <th>suppression</th>

                </tr>
            </thead>
            <tbody>
                <?php
                while ($ligne = $liste_produits->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $ligne['id_avis'] . '</td>';
                    echo '<td>' . $ligne['id_membre'] . '</td>';
                    echo '<td>' . $ligne['id_salle'] . '</td>';
                    echo '<td>' . $ligne['commentaire'] . '</td>';
                    // echo '<td>' . substr($ligne['description'], 0, 5) . ' <a href="#">...</a></td>';
                    echo '<td>' . $ligne['note'] . '</td>';
                    echo '<td>' . $ligne['date_enregistrement'] . '</td>';
                    echo '<td><a href="?action=modifier&id_avis=' . $ligne['id_avis'] . '" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i></a></td>';

                    echo '<td><a href="?action=supprimer&id_avis=' . $ligne['id_avis'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr de bien vouloir supprimer cette personne ?\'))"><i class="fa-solid fa-trash-can"></i></a></td>';

                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
        <?= $msg; // affichage des messages utilisateur  
        ?>


    </div>
</div>


<?php
include '../inc/footer.inc.php';
