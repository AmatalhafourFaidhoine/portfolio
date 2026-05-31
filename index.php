<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portfolio - Développeuse</title>
 <link rel="stylesheet" href="Css/style.css">


</head>
<body>

<?php
  require 'config/connexion.php';
  require 'composants/navigation.php';
  require 'composants/fonctions.php';

  // Journaliser la visite
  log_visite($pdo, 'index.php');
?>

<header class="hero">
  <div class="hero-text">
    <h1><strong><em>Bienvenue sur mon Portfolio</em></strong></h1>
    <div class="hero-box">
      <p><em>Je m'appelle Amatal-hafour Faidhoine, étudiante en informatique en 2ème année Génie Logiciel et Administrateur Réseau. 
        Passionnée par le développement web et la gestion de bases de données. 
        Mon ambition est de devenir une ingénieure capable de transformer des idées en projets concrets et utiles.</em>
      </p>
    </div>
    <a href="projets.php" class="btn-nav">Voir mes projets</a>
  </div>

  <div class="hero-photo">
    <img src="Images/profil.png" alt="Photo de profil">
  </div>
</header>

<?php require 'composants/pied-de-page.php'; ?>

</body>
</html>


