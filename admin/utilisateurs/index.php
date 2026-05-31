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
$erreurs = [];
$succes = false;

// -------------------- AJOUTER --------------------
if ($action === 'ajouter' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die(" CSRF token invalide");
    }

    $prenom = nettoyer($_POST['prenom'] ?? '');
    $nom = nettoyer($_POST['nom'] ?? '');
    $email = nettoyer($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!champ_requis($prenom)) $erreurs[] = "Le prénom est obligatoire.";
    if (!champ_requis($nom)) $erreurs[] = "Le nom est obligatoire.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = "Email invalide.";
    if (!champ_requis($password)) $erreurs[] = "Le mot de passe est obligatoire.";

    if (empty($erreurs)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO administrateurs (prenom, nom, email, motdepasse, date_creation) 
                VALUES (:prenom, :nom, :email, :motdepasse, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'motdepasse' => $hash
        ]);
        $succes = true;
        $action = 'liste';
    }
}

// -------------------- MODIFIER --------------------
if ($action === 'modifier' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die(" CSRF token invalide");
    }

    $id = (int)$_POST['id'];
    $prenom = nettoyer($_POST['prenom'] ?? '');
    $nom = nettoyer($_POST['nom'] ?? '');
    $email = nettoyer($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $sql = "SELECT motdepasse FROM administrateurs WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id'=>$id]);
    $ancien_hash = $stmt->fetchColumn();

    $hash = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $ancien_hash;

    $sql = "UPDATE administrateurs SET prenom=:prenom, nom=:nom, email=:email, motdepasse=:motdepasse WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'prenom' => $prenom,
        'nom' => $nom,
        'email' => $email,
        'motdepasse' => $hash,
        'id' => $id
    ]);
    $succes = true;
    $action = 'liste';
}

// -------------------- SUPPRIMER --------------------
if ($action === 'supprimer' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die(" CSRF token invalide");
    }
    $id = (int)$_POST['id'];

    // Interdire la suppression de son propre compte
    if ($id == $_SESSION['admin_id']) {
        $erreurs[] = " Vous ne pouvez pas supprimer votre propre compte.";
    } else {
        $sql = "DELETE FROM administrateurs WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $succes = true;
    }
    $action = 'liste';
}

// -------------------- LISTE --------------------
$sql = "SELECT id, prenom, nom, email, date_creation FROM administrateurs ORDER BY date_creation DESC";
$admins = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des administrateurs</title>
  <link rel="stylesheet" href="../../Css/admin.css">
</head>
<body>
  <div class="admin-container">
  <h2><strong><em>Gestion des administrateurs</em></strong></h2>

  <?php if ($succes): ?>
    <p style="color:green;"> Opération réussie !</p>
  <?php endif; ?>

  <?php if (!empty($erreurs)): ?>
    <ul style="color:red;">
      <?php foreach ($erreurs as $erreur): ?>
        <li><?= htmlspecialchars($erreur) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if ($action === 'liste'): ?>
    <p><a href="?action=ajouter" class="btn-ajouter-admin"> Ajouter un administrateur</a></p>
    <table border="1" cellpadding="5">
      <tr><th>Prénom</th><th>Nom</th><th>Email</th><th>Date</th><th>Actions</th></tr>
      <?php foreach ($admins as $admin): ?>
        <tr>
          <td><?= htmlspecialchars($admin['prenom']) ?></td>
          <td><?= htmlspecialchars($admin['nom']) ?></td>
          <td><?= htmlspecialchars($admin['email']) ?></td>
          <td><?= htmlspecialchars($admin['date_creation']) ?></td>
          <td>
            <a href="?action=modifier&id=<?= $admin['id'] ?>">Modifier</a>
            <form method="post" action="?action=supprimer" style="display:inline;">
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
              <input type="hidden" name="id" value="<?= $admin['id'] ?>">
              <button type="submit" onclick="return confirm('Supprimer cet administrateur ?')"> Supprimer</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php elseif ($action === 'ajouter'): ?>
    <h3>Ajouter un administrateur</h3>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <label>Prénom :</label><input type="text" name="prenom" required><br>
      <label>Nom :</label><input type="text" name="nom" required><br>
      <label>Email :</label><input type="email" name="email" required><br>
      <label>Mot de passe :</label><input type="password" name="password" required><br>
      <button type="submit">Ajouter</button>
    </form>
  <?php elseif ($action === 'modifier'): 
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE id=:id");
    $stmt->execute(['id'=>$id]);
    $admin = $stmt->fetch();
  ?>
    <h3>Modifier un administrateur</h3>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <input type="hidden" name="id" value="<?= $admin['id'] ?>">
      <label>Prénom :</label><input type="text" name="prenom" value="<?= htmlspecialchars($admin['prenom']) ?>" required><br>
      <label>Nom :</label><input type="text" name="nom" value="<?= htmlspecialchars($admin['nom']) ?>" required><br>
      <label>Email :</label><input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required><br>
      <label>Nouveau mot de passe :</label><input type="password" name="password"><br>
      <small>(laisser vide pour conserver l’ancien)</small><br>
      <button type="submit">Mettre à jour</button>
    </form>
  <?php endif; ?>
  </div>
</body>
</html>
