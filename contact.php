<?php
require 'composants/fonctions.php'; 

$erreurs = [];
$succes = false;
$nom = $email = $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = nettoyer($_POST['nom'] ?? '');
    $email = nettoyer($_POST['email'] ?? '');
    $message = nettoyer($_POST['message'] ?? '');

    if (!champ_requis($nom)) {
        $erreurs[] = "Le nom est obligatoire.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'adresse e-mail est invalide.";
    }
    if (!champ_requis($message)) {
        $erreurs[] = "Le message ne peut pas être vide.";
    }

    if (empty($erreurs)) {
        $succes = true;
    }
}


$erreursProjet = [];
$demande = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titre'])) {
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
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="Css/style.css">
  <title>Contacter moi</title>
</head>
<body>
  
<?php require 'composants/navigation.php'; ?>

<section class="contact-intro">
  <p><strong><em>Pour toute question ou collaboration, n’hésitez pas à me contacter via ce formulaire.</em></strong></p>
</section>

<section class="contact">
  <h2>Contactez-moi</h2>

  <!-- Messages -->
  <?php if ($succes): ?>
    <p style="color:green;">Merci, votre message a bien été envoyé !</p>
  <?php endif; ?>

  <?php if (!empty($erreurs)): ?>
    <ul style="color:red;">
      <?php foreach ($erreurs as $erreur): ?>
        <li><?= $erreur ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>


  <form method="post">
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" value="<?= $nom ?>" required>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" value="<?= $email ?>" required>

    <label for="message">Message :</label>
    <textarea id="message" name="message" required><?= $message ?></textarea>

    <button type="submit" class="btn-nav">Envoyer</button>
  </form>
</section>

<section id="demande-projet" class="form-projet">
  <h2>Proposer un projet</h2>

  <?php if (!empty($demande) && empty($erreursProjet)): ?>
    <p style="color:green;">Merci, votre demande a bien été envoyée !</p>
    <h3>Récapitulatif :</h3>
    <ul>
      <li><strong>Titre :</strong> <?= $demande['titre'] ?></li>
      <li><strong>Description :</strong> <?= $demande['description'] ?></li>
      <li><strong>Budget :</strong> <?= $demande['budget'] ?></li>
      <li><strong>Délai :</strong> <?= $demande['delai'] ?></li>
    </ul>
  <?php endif; ?>
  

  <?php if (!empty($erreursProjet)): ?>
    <ul style="color:red;">
      <?php foreach ($erreursProjet as $erreur): ?>
        <li><?= $erreur ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post">
    <label for="titre">Titre du projet :</label>
    <input type="text" id="titre" name="titre" value="<?= $demande['titre'] ?? '' ?>" required>

    <label for="description">Description :</label>
    <textarea id="description" name="description" required><?= $demande['description'] ?? '' ?></textarea>

    <label for="budget">Budget estimé :</label>
    <input type="number" id="budget" name="budget" value="<?= $demande['budget'] ?? '' ?>">

    <label for="delai">Délai souhaité :</label>
    <input type="date" id="delai" name="delai" value="<?= $demande['delai'] ?? '' ?>">

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
