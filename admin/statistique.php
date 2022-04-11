<?php
include '../inc/init.inc.php';
include '../inc/functions.inc.php';

if (!user_is_admin()) {
    header('location:../connexion.php');
    exit();

}

// CODE ...

// Top 5 des salles les mieux notées
$mieu_notees = $pdo->query("SELECT salle.id_salle, titre, avg(note) AS note_moyenne FROM salle, avis WHERE salle.id_salle = avis.id_salle GROUP BY salle.id_salle ORDER BY note_moyenne DESC LIMIT 5");

// Top 5 des salles les plus commandées
$meilleures_commandes = $pdo->query("SELECT salle.id_salle, titre, COUNT(id_commande) AS nbre_commande FROM commande, produit, salle WHERE produit.id_produit = commande.id_produit AND salle.id_salle = produit.id_salle GROUP BY salle.id_salle ORDER BY nbre_commande DESC LIMIT 5");

// Top 5 des membres qui achètent le plus (en termes de quantité).
$membres_achat_quantity = $pdo->query("SELECT m.id_membre, email, count(id_commande) AS nbre_commande FROM membre m, commande c WHERE m.id_membre = c.id_membre GROUP BY m.id_membre ORDER BY nbre_commande DESC LIMIT 5");

// Top 5 des membres qui achètent le plus cher (en termes de prix)
$most_expensives = $pdo->query("SELECT m.id_membre, email, SUM(prix) AS prix_commande FROM membre m, commande c, produit p WHERE m.id_membre = c.id_membre AND c.id_produit = p.id_produit GROUP BY m.id_membre ORDER BY prix_commande DESC LIMIT 5 ");




// $infos_produit = $pdo->prepare("SELECT titre, COUNT(id_avis) AS nombre FROM avis a, ");

// Début des affichages
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
?>
<div class="bg-light p-5 rounded text-center">
    <h1><i class="fa-solid fa-bag-shopping text-royalblue"></i> Statistique <i class="fa-solid fa-bag-shopping text-royalblue"></i></h1>
    <p class="lead">Bienvenue sur notre boutique.</p>
</div>
<h1>Top  des salles les mieux notées </h1>


<div class="row mt-4">
    <div class="col-sm-12">
        <?= $msg; // affichage des messages utilisateur  
        ?>
 <table class="table table-bordered">
            <thead class="bg-royalblue text-white">
                <tr>
                    <th>Id_salle</th>
                    <th>Titre</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php
                while ($ligne = $mieu_notees->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $ligne['id_salle'] . '</td>';
                    echo '<td>' . $ligne['titre'] . '</td>';                  
                   
                }
                ?>
            </tbody>
        </table>

<h1>Top 5 des salles les plus commandées </h1>
<table class="table table-bordered">
            <thead class="bg-royalblue text-white">
                <tr>
                    <th>Id_salle</th>
                    <th>Titre</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php
                while ($ligne = $meilleures_commandes->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $ligne['id_salle'] . '</td>';
                    echo '<td>' . $ligne['titre'] . '</td>';                  
                   
                }
                ?>
            </tbody>
        </table>

        <h1>Top 5 des membres qui achètent le plus (en termes de quantité </h1>
<table class="table table-bordered">
            <thead class="bg-royalblue text-white">
                <tr>
                    <th>id_membre</th>
                    <th>E_mail</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php
                while ($ligne = $membres_achat_quantity->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $ligne['id_membre'] . '</td>';
                    echo '<td>' . $ligne['email'] . '</td>';                  
                   
                }
                ?>
            </tbody>
        </table>

        <h1> Top 5 des membres qui achètent le plus cher (en termes de prix) </h1>
<table class="table table-bordered">
            <thead class="bg-royalblue text-white">
                <tr>
                    <th>id_membre</th>
                    <th>E_mail</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php
                while ($ligne = $most_expensives->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $ligne['id_membre'] . '</td>';
                    echo '<td>' . $ligne['email'] . '</td>';                  
                   
                }
                ?>
            </tbody>
        </table>
    </div>
</div>


<?php
include '../inc/footer.inc.php';
