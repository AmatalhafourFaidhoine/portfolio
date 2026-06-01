<?php
session_start();
require '../Config/connexion.php';
require '../Composants/fonctions.php';

// Vérifier si l'admin est connecté
if (!isset($_SESSION['admin_id'])) {
    header("Location: connexion.php");
    exit;
}

// Nombre total de projets
$sql = "SELECT COUNT(*) FROM projets";
$total_projets = $pdo->query($sql)->fetchColumn();

// Nombre total de messages
$sql = "SELECT COUNT(*) FROM messages_contact";
$messages_total = $pdo->query($sql)->fetchColumn();

// Nombre total de demandes
$sql = "SELECT COUNT(*) FROM demandes_projet";
$demandes_total = $pdo->query($sql)->fetchColumn();

// 5 dernières visites
$sql = "SELECT ip, page, date_visite FROM visites ORDER BY date_visite DESC LIMIT 5";
$dernieres_visites = $pdo->query($sql)->fetchAll();

// 5 dernières demandes
$sql = "SELECT description, date_demande FROM demandes_projet ORDER BY date_demande DESC LIMIT 5";
$dernieres_demandes = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <link rel="stylesheet" href="../Css/admin.css">
</head>
<body>
  <div class="admin-container">

    
    <h2><strong><em>Bienvenue 👋</em></strong></h2>
    <p class="subtitle">Vue d’ensemble du portfolio</p>

    <ul>
      <li>Total de projets : <strong><?= $total_projets ?></strong></li>
      <li>Messages de contact : <strong><?= $messages_total ?></strong></li>
      <li>Demandes de projet : <strong><?= $demandes_total ?></strong></li>
    </ul>

    <h3>5 dernières visites</h3>
    <table class="dashboard-table">
      <tr><th>IP</th><th>Page</th><th>Date</th></tr>
      <?php foreach ($dernieres_visites as $visite): ?>
        <tr>
          <td><?= htmlspecialchars($visite['ip']) ?></td>
          <td><?= htmlspecialchars($visite['page']) ?></td>
          <td><?= htmlspecialchars($visite['date_visite']) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <h3>5 dernières demandes de projet</h3>
    <table class="dashboard-table">
      <tr><th>Description</th><th>Date</th></tr>
      <?php foreach ($dernieres_demandes as $demande): ?>
        <tr>
          <td><?= htmlspecialchars($demande['description']) ?></td>
          <td><?= htmlspecialchars($demande['date_demande']) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

  </div>
</body>
</html>
