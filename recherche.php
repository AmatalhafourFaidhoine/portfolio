<?php
require 'config/connexion.php';
require 'composants/fonctions.php';

// Journaliser la visite
log_visite($pdo, '.php');

$resultats = [];
$message = '';

if (isset($_GET['motcle'])) {
    $motcle = trim($_GET['motcle']);

    if (!empty($motcle)) {
        $sql = "SELECT * FROM projets 
                WHERE titre LIKE :motcle 
                   OR description LIKE :motcle";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['motcle' => "%$motcle%"]);
        $resultats = $stmt->fetchAll();

        if (empty($resultats)) {
            $message = " Aucun projet ne correspond à votre recherche.";
        }
    } else {
        $message = " Veuillez entrer un mot-clé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Recherche de projets</title>
</head>
<body>
  <h2>Recherche de projets</h2>

  <form method="get" action="recherche.php">
    <label>Mot-clé :</label>
    <input type="text" name="motcle" value="<?= htmlspecialchars($_GET['motcle'] ?? '') ?>" required>
    <button type="submit">Rechercher</button>
  </form>

  <?php if ($message): ?>
    <p style="color:red;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <?php if (!empty($resultats)): ?>
    <h3>Résultats :</h3>
    <ul>
      <?php foreach ($resultats as $projet): ?>
        <li>
          <strong><?= htmlspecialchars($projet['titre']) ?></strong><br>
          <?= htmlspecialchars($projet['description']) ?><br>
          <em>Technologie : <?= htmlspecialchars($projet['technologie']) ?></em>
        </li>
        <br>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</body>
</html>
