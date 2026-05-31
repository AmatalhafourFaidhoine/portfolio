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

    $titre = nettoyer($_POST['titre'] ?? '');
    $description = nettoyer($_POST['description'] ?? '');
    $technologies = nettoyer($_POST['technologies'] ?? '');
    $lien_externe = nettoyer($_POST['lien_externe'] ?? '');
    $image_nom = null;

    if (!champ_requis($titre)) $erreurs[] = "Le titre est obligatoire.";
    if (!champ_requis($description)) $erreurs[] = "La description est obligatoire.";

    if (!empty($_FILES['image']['name'])) {
        $extensions_autorisees = ['jpg','jpeg','png','webp','gif'];
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $extensions_autorisees)) {
            $erreurs[] = "Format d'image non autorisé.";
        } else {
            $image_nom = uniqid() . "." . $extension;
            $destination = "../../Images/projets/" . $image_nom;
            move_uploaded_file($_FILES['image']['tmp_name'], $destination);
        }
    }

    if (empty($erreurs)) {
        $sql = "INSERT INTO projets (titre, description, technologies, lien_externe, image, date_creation) 
                VALUES (:titre, :description, :technologies, :lien_externe, :image, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'titre' => $titre,
            'description' => $description,
            'technologies' => $technologies,
            'lien_externe' => $lien_externe,
            'image' => $image_nom
        ]);
        $succes = true;
        $action = 'liste';
    }
}

// -------------------- MODIFIER --------------------
if ($action === 'modifier' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token invalide");
    }

    $id = (int)$_POST['id'];
    $titre = nettoyer($_POST['titre'] ?? '');
    $description = nettoyer($_POST['description'] ?? '');
    $technologies = nettoyer($_POST['technologies'] ?? '');
    $lien_externe = nettoyer($_POST['lien_externe'] ?? '');
    $image_nom = $_POST['ancienne_image'] ?? null;

    if (!empty($_FILES['image']['name'])) {
        $extensions_autorisees = ['jpg','jpeg','png','webp','gif'];
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($extension, $extensions_autorisees)) {
            $image_nom = uniqid() . "." . $extension;
            $destination = "../../Images/projets/" . $image_nom;
            move_uploaded_file($_FILES['image']['tmp_name'], $destination);
        }
    }

    $sql = "UPDATE projets SET titre=:titre, description=:description, technologies=:technologies, 
            lien_externe=:lien_externe, image=:image WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'titre' => $titre,
        'description' => $description,
        'technologies' => $technologies,
        'lien_externe' => $lien_externe,
        'image' => $image_nom,
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
    $sql = "DELETE FROM projets WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $succes = true;
    $action = 'liste';
}

// -------------------- LISTE --------------------
$sql = "SELECT * FROM projets ORDER BY date_creation DESC";
$projets = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des projets</title>
  <link rel="stylesheet" href="../../Css/admin.css">
</head>
<body>
  <div class="admin-container">
    <h2><strong><em>Gestion des projets</em></strong></h2>

  <?php if ($succes): ?>
    <p style="color:green;"> Opération réussie !</p>
  <?php endif; ?>

  <?php if ($action === 'liste'): ?>
    <a href="?action=ajouter" class="btn-ajouter"> Ajouter un projet</a>
    <table border="1" cellpadding="5">
      <tr><th>Titre</th><th>Technologies</th><th>Date</th><th>Actions</th></tr>
      <?php foreach ($projets as $projet): ?>
        <tr>
          <td><?= htmlspecialchars($projet['titre']) ?></td>
          <td><?= htmlspecialchars($projet['technologies']) ?></td>
          <td><?= htmlspecialchars($projet['date_creation']) ?></td>
          <td>
            <a href="?action=modifier&id=<?= $projet['id'] ?>"> Modifier</a>
            <form method="post" action="?action=supprimer" style="display:inline;">
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
              <input type="hidden" name="id" value="<?= $projet['id'] ?>">
              <button type="submit" onclick="return confirm('Supprimer ce projet ?')"> Supprimer</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php elseif ($action === 'ajouter'): ?>
    <h3>Ajouter un projet</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <label>Titre :</label><input type="text" name="titre" required><br>
      <label>Description :</label><textarea name="description" required></textarea><br>
      <label>Technologies :</label><input type="text" name="technologies"><br>
      <label>Lien externe :</label><input type="url" name="lien_externe"><br>
      <label>Image :</label><input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif"><br>
      <button type="submit">Ajouter</button>
    </form>
  <?php elseif ($action === 'modifier'): 
    $id = (int)$_GET['id'];
    $projet = $pdo->prepare("SELECT * FROM projets WHERE id=:id");
    $projet->execute(['id'=>$id]);
    $projet = $projet->fetch();
  ?>
    <h3>Modifier un projet</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <input type="hidden" name="id" value="<?= $projet['id'] ?>">
      <input type="hidden" name="ancienne_image" value="<?= $projet['image'] ?>">
      <label>Titre :</label><input type="text" name="titre" value="<?= htmlspecialchars($projet['titre']) ?>" required><br>
      <label>Description :</label><textarea name="description" required><?= htmlspecialchars($projet['description']) ?></textarea><br>
      <label>Technologies :</label><input type="text" name="technologies" value="<?= htmlspecialchars($projet['technologies']) ?>"><br>
      <label>Lien externe :</label><input type="url" name="lien_externe" value="<?= htmlspecialchars($projet['lien_externe']) ?>"><br>
      <label>Image :</label><input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif"><br>
        <?php if ($projet['image']): ?>
        <img src="../../Images/projets/<?= htmlspecialchars($projet['image']) ?>" width="100"><br>
      <?php endif; ?>
      <button type="submit">Mettre à jour</button>
    </form>
  <?php endif; ?>
  </div>
</body>
</html>

