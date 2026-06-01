<?php
session_start();
require '../../Config/connexion.php';
require '../../Composants/fonctions.php';

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

// -------------------- OUVRIR UN MESSAGE --------------------
if ($action === 'voir' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM messages_contact WHERE id=:id");
    $stmt->execute(['id'=>$id]);
    $message = $stmt->fetch();
}

// -------------------- SUPPRIMER UN MESSAGE --------------------
if ($action === 'supprimer' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token invalide");
    }
    $id = (int)$_POST['id'];
    $sql = "DELETE FROM messages_contact WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id'=>$id]);
    $action = 'liste';
}

// -------------------- LISTE --------------------
// ✅ Utiliser la colonne 'contenu' et 'date_envoi'
$sql = "SELECT id, nom, email, contenu, date_envoi 
        FROM messages_contact ORDER BY date_envoi DESC";
$messages = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Messages de contact</title>
  <link rel="stylesheet" href="../../Css/admin.css">
</head>
<body>
  <div class="admin-container">
    <h2><strong><em>Messages de contact</em></strong></h2>

    <?php if ($action === 'liste'): ?>
      <table class="dashboard-table">
        <tr>
          <th>Nom</th>
          <th>Email</th>
          <th>Message</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
        <?php foreach ($messages as $msg): ?>
          <tr>
            <td><?= htmlspecialchars($msg['nom']) ?></td>
            <td><?= htmlspecialchars($msg['email']) ?></td>
            <td><?= htmlspecialchars($msg['contenu']) ?></td>
            <td><?= htmlspecialchars($msg['date_envoi']) ?></td>
            <td>
              <a href="?action=voir&id=<?= $msg['id'] ?>">Voir</a>
              <form method="post" action="?action=supprimer" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                <button type="submit" onclick="return confirm('Supprimer ce message ?')">Supprimer</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>

    <?php elseif ($action === 'voir' && $message): ?>
      <h3>Message reçu</h3>
      <p><strong>De :</strong> <?= htmlspecialchars($message['nom']) ?> (<?= htmlspecialchars($message['email']) ?>)</p>
      <p><strong>Date :</strong> <?= htmlspecialchars($message['date_envoi']) ?></p>
      <p><strong>Message :</strong><br><?= nl2br(htmlspecialchars($message['contenu'])) ?></p>
      <p><a href="index.php" class="btn-retour">⬅️ Retour à la liste</a></p>
    <?php endif; ?>
  </div>
</body>
</html>
