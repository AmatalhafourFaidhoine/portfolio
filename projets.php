<?php
require 'composants/fonctions.php';
require 'composants/navigation.php';


$projets = [
    [
        'titre' => 'Système d’inversion de nombres en Java',
        'description' => 'Ce projet, développé en Java sous Eclipse, applique la programmation orientée objet pour structurer le code et manipuler efficacement les données grâce à des méthodes simples et organisées.',
        'technologies' => ['Java', 'Eclipse', 'POO'],
        'image' => 'Images/Projet1.jpeg'
    ],
    [
        'titre' => 'Système de chiffrement et déchiffrement de données avec OpenSSL',
        'description' => 'Ce projet utilise AES avec OpenSSL pour chiffrer et déchiffrer des données à l’aide d’un mot de passe, afin d’assurer leur confidentialité. Cette approche illustre les bases de la sécurité informatique, notamment la confidentialité et la protection des informations.',
        'technologies' => ['Kali Linux', 'AES', 'Terminal Linux'],
        'image' => 'Images/Projet2.jpeg'
    ],
    [
        'titre' => 'Poubelle intelligente',
        'description' => 'Ce prototype est un système automatisé basé sur une carte Arduino, équipé d’un capteur à ultrasons et d’un servomoteur. Il détecte la présence d’un objet à l’avant et déclenche automatiquement l’ouverture ou la fermeture du couvercle, illustrant une solution simple d’objet intelligent sans contact, à la fois pratique et hygiénique.',
        'technologies' => ['Arduino', 'Capteur ultrason', 'Servomoteur'],
        'image' => 'Images/projet3.jpeg',
        'lien' => 'https://github.com/AmatalhafourFaidhoine/projet3-boubelle-intelligente/blob/main/projet3-boubelle-intelligente.ino'
    ],
    [
        'titre' => 'Mini site web en PHP',
        'description' => 'Un projet de site web dynamique en PHP avec gestion de formulaires et affichage conditionnel. Ce projet illustre les bases du développement web côté serveur.',
        'technologies' => ['PHP', 'HTML', 'CSS'],
        'image' => 'Images/projet4.jpeg'
    ]
];


$mot_cle = nettoyer($_GET['motcle'] ?? '');
$resultats = [];

if ($mot_cle !== '') {
    foreach ($projets as $projet) {
        if (stripos($projet['titre'], $mot_cle) !== false || stripos($projet['description'], $mot_cle) !== false) {
            $resultats[] = $projet;
        }
    }
} else {
    $resultats = $projets;
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
  <h2>Recherche de projets</h2>
  <form class="search-form" method="get" action="projets.php">
    <input type="text" name="motcle" placeholder="Entrez un mot-clé..." value="<?= $mot_cle ?>" required>
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
          <p><strong>Technologies :</strong> <?= implode(', ', $projet['technologies']) ?></p>
          <?php if (!empty($projet['lien'])): ?>
            <a href="<?= htmlspecialchars($projet['lien']) ?>" class="btn-code" target="_blank">Voir le code sur GitHub</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="color:red;">❌ Aucun projet ne correspond à votre recherche.</p>
  <?php endif; ?>
</section>

<?php require 'composants/pied-de-page.php'; ?>

</body>
</html>
