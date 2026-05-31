<?php
session_start(); // Toujours démarrer la session

require 'config/connexion.php';
require 'composants/fonctions.php';
require 'composants/navigation.php';

log_visite($pdo, 'projets.php');



// Génération du token CSRF pour la recherche
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mot_cle = nettoyer($_GET['motcle'] ?? '');
$resultats = [];

if ($mot_cle !== '') {
    // Vérification CSRF si le formulaire est soumis
    if (isset($_GET['csrf_token']) && !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
        die(" CSRF token invalide");
    }

    $sql = "SELECT * FROM projets 
            WHERE titre LIKE :motcle 
               OR description LIKE :motcle 
               OR technologies LIKE :motcle
            ORDER BY date_creation DESC";
    $stmt = $pdo->prepare($sql);
    $like = "%$mot_cle%";
    $stmt->bindParam(':motcle', $like);
    $stmt->execute();
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sql = "SELECT * FROM projets ORDER BY date_creation DESC";
    $stmt = $pdo->query($sql);
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mes Projets</title>
  <link rel="stylesheet" href="Css/style.css">
</head>
<body>

<section class="recherche-projets">
  <h2><em>Recherche de projets</em></h2>
  <form class="search-form" method="get" action="projets.php">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="text" name="motcle" placeholder="Entrez un mot-clé..." value="<?= htmlspecialchars($mot_cle) ?>" required>
    <button type="submit">Rechercher</button>
  </form>
</section>


<section class="projets">
  <h2><em>Mes Réalisations</em></h2>

  <?php if (!empty($resultats)): ?>
    <?php foreach ($resultats as $projet): ?>
      <div class="carte-projet">
        <img src="<?= htmlspecialchars($projet['image']) ?>" alt="<?= htmlspecialchars($projet['titre']) ?>">
        <div class="details">
          <h3><?= htmlspecialchars($projet['titre']) ?></h3>
          <p><?= htmlspecialchars($projet['description']) ?></p>
          <p><strong>Technologies :</strong> <?= htmlspecialchars($projet['technologies']) ?></p>
          <?php if (!empty($projet['lien'])): ?>
            <a href="<?= htmlspecialchars($projet['lien']) ?>" class="btn-code" target="_blank">Voir le code sur GitHub</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="color:red;"> Aucun projet ne correspond à votre recherche.</p>
  <?php endif; ?>
</section>


<?php require 'composants/pied-de-page.php'; ?>

</body>
</html>
