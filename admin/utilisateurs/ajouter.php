<?php
session_start();
require '../../composants/fonctions.php';
require '../../config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = nettoyer($_POST['prenom']);
    $nom = nettoyer($_POST['nom']);
    $email = nettoyer($_POST['email']);
    $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO administrateurs (prenom, nom, email, motdepasse, date_creation) 
            VALUES (:prenom, :nom, :email, :motdepasse, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'prenom' => $prenom,
        'nom' => $nom,
        'email' => $email,
        'motdepasse' => $motdepasse
    ]);

    header("Location: index.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un administrateur</title>
</head>
<body>
    <h1>Ajouter un administrateur</h1>
    <form method="post">
        <label>Prénom :</label>
        <input type="text" name="prenom" value="Cherif" required><br><br>

        <label>Nom :</label>
        <input type="text" name="nom" value="Diouf" required><br><br>

        <label>Email :</label>
        <input type="email" name="email" value="el.hadji.ahmadou.cherif.diouf@gmail.com" required><br><br>

        <label>Mot de passe :</label>
        <input type="password" name="motdepasse" value="123Cherif@Diouf" required><br><br>

        <button type="submit">Créer le compte</button>
    </form>

    <h1>Ajouter un administrateur</h1>
    <form method="post">
        <label>Prénom :</label>
        <input type="text" name="prenom" value="Amatal" required><br><br>

        <label>Nom :</label>
        <input type="text" name="nom" value="Faidhoine" required><br><br>

        <label>Email :</label>
        <input type="email" name="email" value="amatalhafourfaidhoine@gmail.com" required><br><br>

        <label>Mot de passe :</label>
        <input type="password" name="motdepasse" value="123Amatal" required><br><br>

        <button type="submit">Créer le compte</button>
    </form>
</body>
</html>
