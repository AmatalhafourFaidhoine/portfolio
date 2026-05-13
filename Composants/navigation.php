<?php
$page_courante = basename($_SERVER['PHP_SELF']);
?>

<nav>
    <input type="text"  placeholder="🔍Rechercher..." class="search-bar">
    <ul class="nav-right">
        <li><a href="index.php" class="btn-nav <?php if ($page_courante === 'index.php') echo 'actif'; ?>">Accueil</a></li>
        <li><a href="about.php" class="btn-nav <?php if ($page_courante === 'about.php') echo 'actif'; ?>">À propos</a></li>
        <li><a href="projets.php" class="btn-nav <?php if ($page_courante === 'projets.php') echo 'actif'; ?>">Projets</a></li>
        <li><a href="contact.php" class="btn-nav <?php if ($page_courante === 'contact.php') echo 'actif'; ?>">Contact</a></li>
    </ul>
</nav>