<?php
session_start();
require '../../config/connexion.php';
require '../../composants/fonctions.php';

// Vérifier si l'admin est connecté
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../connexion.php");
    exit;
}

// Générer le token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$action = $_GET['action'] ?? 'liste';

// -------------------- OUVRIR UNE DEMANDE --------------------
if ($action === 'voir' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM demandes_projet WHERE id=:id");
    $stmt->execute(['id'=>$id]);
    $demande = $stmt->fetch();
}

// -------------------- SUPPRIMER UNE DEMANDE --------------------
if ($action === 'supprimer' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token invalide");
    }
    $id = (int)$_POST['id'];
    $sql = "DELETE FROM demandes_projet WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id'=>$id]);
    $action = 'liste';
}

// -------------------- LISTE --------------------
$sql = "SELECT id, titre, description, budget, delai, date_demande 
        FROM demandes_projet ORDER BY date_demande DESC";
$demandes = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Demandes de projet</title>
  <link rel="stylesheet" href="../../Css/admin.css">
</head>
<body>
  <div class="admin-container">
    <h2><strong><em>Demandes de projet</em></strong></h2>

    <?php if ($action === 'liste'): ?>
      <table class="dashboard-table">
        <tr>
          <th>Titre</th>
          <th>Description</th>
          <th>Budget</th>
          <th>Délai</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
        <?php foreach ($demandes as $dem): ?>
          <tr>
            <td><?= htmlspecialchars($dem['titre']) ?></td>
            <td><?= htmlspecialchars($dem['description']) ?></td>
            <td><?= htmlspecialchars($dem['budget']) ?></td>
            <td><?= htmlspecialchars($dem['delai']) ?></td>
            <td><?= htmlspecialchars($dem['date_demande']) ?></td>
            <td>
              <a href="?action=voir&id=<?= $dem['id'] ?>">Voir</a>
              <form method="post" action="?action=supprimer" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="id" value="<?= $dem['id'] ?>">
                <button type="submit" onclick="return confirm('Supprimer cette demande ?')">Supprimer</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>

    <?php elseif ($action === 'voir' && $demande): ?>
      <h3>Demande reçue</h3>
      <p><strong>Titre :</strong> <?= htmlspecialchars($demande['titre']) ?></p>
      <p><strong>Description :</strong><br><?= nl2br(htmlspecialchars($demande['description'])) ?></p>
      <p><strong>Budget :</strong> <?= htmlspecialchars($demande['budget']) ?></p>
      <p><strong>Délai :</strong> <?= htmlspecialchars($demande['delai']) ?></p>
      <p><strong>Date :</strong> <?= htmlspecialchars($demande['date_demande']) ?></p>
      <p><a href="index.php" class="btn-retour">⬅️Retour à la liste</a></p>
    <?php endif; ?>
  </div>
</body>
</html>
