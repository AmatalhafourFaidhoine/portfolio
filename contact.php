<?php
session_start(); 

require 'config/connexion.php';
require 'composants/fonctions.php';
require 'composants/navigation.php';

log_visite($pdo, 'contact.php');

// Génération du token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// -------------------- FORMULAIRE CONTACT --------------------
$erreurs = [];
$succes = false;
$nom = $email = $contenu = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && isset($_POST['message'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token invalide");
    }

    $nom = nettoyer($_POST['nom'] ?? '');
    $email = nettoyer($_POST['email'] ?? '');
    $contenu = nettoyer($_POST['message'] ?? '');

    if (!champ_requis($nom)) {
        $erreurs[] = "Le nom est obligatoire.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'adresse e-mail est invalide.";
    }
    if (!champ_requis($contenu)) {
        $erreurs[] = "Le message ne peut pas être vide.";
    }

    if (empty($erreurs)) {
        $sql = "INSERT INTO messages_contact (nom, email, contenu, date_envoi) 
                VALUES (:nom, :email, :contenu, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nom' => $nom,
            'email' => $email,
            'contenu' => $contenu
        ]);
        $succes = true;
    }
}

// -------------------- FORMULAIRE DEMANDE PROJET --------------------
$erreursProjet = [];
$demande = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && isset($_POST['titre'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token invalide");
    }

    $demande = [
        'titre' => nettoyer($_POST['titre'] ?? ''),
        'description' => nettoyer($_POST['description'] ?? ''),
        'budget' => nettoyer($_POST['budget'] ?? ''),
        'delai' => nettoyer($_POST['delai'] ?? '')
    ];

    if (!champ_requis($demande['titre'])) {
        $erreursProjet[] = "Le titre du projet est obligatoire.";
    }
    if (!champ_requis($demande['description'])) {
        $erreursProjet[] = "La description est obligatoire.";
    }

    if (empty($erreursProjet)) {
        $sql = "INSERT INTO demandes_projet (titre, description, budget, delai, date_demande) 
                VALUES (:titre, :description, :budget, :delai, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($demande);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="Css/style.css">
  <title>Contacter moi</title>
</head>
<body>

<section class="contact-intro">
  <p><strong><em>Pour toute question ou collaboration, n’hésitez pas à me contacter via ce formulaire.</em></strong></p>
</section>

<!-- FORMULAIRE CONTACT -->
<section class="contact">
  <h2>Contactez-moi</h2>

  <?php if ($succes): ?>
    <p style="color:green;">Merci, votre message a bien été envoyé !</p>
  <?php endif; ?>

  <?php if (!empty($erreurs)): ?>
    <ul style="color:red;">
      <?php foreach ($erreurs as $erreur): ?>
        <li><?= htmlspecialchars($erreur) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

    <label for="message">Message :</label>
    <textarea id="message" name="message" required><?= htmlspecialchars($contenu) ?></textarea>

    <button type="submit" class="btn-nav">Envoyer</button>
  </form>
</section>

<!-- FORMULAIRE DEMANDE PROJET -->
<section id="demande-projet" class="form-projet">
  <h2>Proposer un projet</h2>

  <?php if (!empty($demande) && empty($erreursProjet)): ?>
    <p style="color:green;">Merci, votre demande a bien été envoyée !</p>
    <h3>Récapitulatif :</h3>
    <ul>
      <li><strong>Titre :</strong> <?= htmlspecialchars($demande['titre']) ?></li>
      <li><strong>Description :</strong> <?= htmlspecialchars($demande['description']) ?></li>
      <li><strong>Budget :</strong> <?= htmlspecialchars($demande['budget']) ?></li>
      <li><strong>Délai :</strong> <?= htmlspecialchars($demande['delai']) ?></li>
    </ul>
  <?php endif; ?>

  <?php if (!empty($erreursProjet)): ?>
    <ul style="color:red;">
      <?php foreach ($erreursProjet as $erreur): ?>
        <li><?= htmlspecialchars($erreur) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post">
    <label for="titre">Titre du projet :</label>
    <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($demande['titre'] ?? '') ?>" required>

    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <label for="description">Description :</label>
    <textarea id="description" name="description" required><?= htmlspecialchars($demande['description'] ?? '') ?></textarea>

    <label for="budget">Budget estimé :</label>
    <input type="number" id="budget" name="budget" value="<?= htmlspecialchars($demande['budget'] ?? '') ?>">

    <label for="delai">Délai souhaité :</label>
    <input type="date" id="delai" name="delai" value="<?= htmlspecialchars($demande['delai'] ?? '') ?>">

    <button type="submit">Envoyer la demande</button>
  </form>
</section>

<section class="contact-info">
  <h3>Mes coordonnées</h3>
  <p>Email : <a href="mailto:amatalhafourfaidhoine@gmail.com">amatalhafourfaidhoine@gmail.com</a></p>
  <p>Téléphone : <a href="tel:+221781042635">+221 78 104 26 35</a></p>
  <p>Adresse : Dakar, Sénégal</p>
</section>

<?php require 'composants/pied-de-page.php'; ?>

</body>
</html>
