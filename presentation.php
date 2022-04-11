<?php
include 'inc/init.inc.php';
include 'inc/functions.inc.php';

// CODE ...






// Début des affichages
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>
<div class="bg-light p-5 rounded text-center">
    <h1><i class="fa-solid fa-bag-shopping text-royalblue"></i> Notre histoire <i class="fa-solid fa-bag-shopping text-royalblue"></i></h1>
    <p class="lead">Bienvenue sur notre Site.</p>
</div>

<div class="row mt-4 bg-white">
    <div class="col-sm-12">
        <?= $msg; // affichage des messages utilisateur  
        ?>
        <h2> Préssentation</h2>
        <div>
        <strong>ROOM :</strong>  est une société proposant la location de salles de réunion à ses clients. <br>
   <strong>Raison sociale :</strong>  ROOM <br>
<strong>Adresse : </strong> <address>300 Boulevard de Sébastopol, 75003 Paris, France</address> <br>
<strong> Mission :</strong> La société est spécialisée dans la location de salle pour l’organisation de réunions par les entreprises ou les particuliers. 
Périmètre géographique de l’activité : La société dispose de salles de réunions à Paris, Lyon et Marseille. <br>
<strong>Objectifs : </strong>  L'enjeu est d’attribuer plusieurs périodes de location sur chacune des salles, ce qui nous donnera plusieurs produits.
        </div>
        <p>Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500, quand un imprimeur anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n'a pas fait que survivre cinq siècles, mais s'est aussi adapté à la bureautique informatique, sans que son contenu n'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.</p>

        
    </div>
</div>


<?php
include 'inc/footer.inc.php';
