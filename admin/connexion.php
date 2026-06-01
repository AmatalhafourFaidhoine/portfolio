<?php
session_start();
require '../Config/connexion.php';
require '../Composants/fonctions.php';

// Si déjà connecté → redirection vers dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Génération du token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token'])) {
    // Vérification CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token invalide");
    }

    $email = trim($_POST['email'] ?? '');
    $motdepasse = trim($_POST['motdepasse'] ?? '');

    if (!empty($email) && !empty($motdepasse)) {
        // Vérifie bien que la colonne est "motdepasse" dans ta base
        $sql = "SELECT id, prenom, nom, motdepasse FROM administrateurs WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($motdepasse, $admin['motdepasse'])) {
            // Sécurité : régénérer l’ID de session
            session_regenerate_id(true);

            // Stocker les infos de session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_prenom'] = !empty($admin['prenom']) ? $admin['prenom'] : 'Admin';
            $_SESSION['admin_nom'] = !empty($admin['nom']) ? $admin['nom'] : '';

            header("Location: dashboard.php");
            exit;
        } else {
            $erreur = "Identifiants incorrects.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }

}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion Admin</title>
  <link rel="stylesheet" href="../Css/admin.css">
</head>
<body>

<div class="admin-container">
  <h2><strong><em>Connexion à l’espace d’administration</em></strong></h2>
  <?php if ($erreur): ?>
    <p style="color:red;"><?= htmlspecialchars($erreur) ?></p>
  <?php endif; ?>

  <form method="post" action="connexion.php">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <label>Email :</label>
    <input type="email" name="email" required><br><br>

    <label>Mot de passe :</label>
    <input type="password" name="motdepasse" required><br><br>

    <button type="submit">Se connecter</button>
  </form>
  </div>
</body>
</html>
