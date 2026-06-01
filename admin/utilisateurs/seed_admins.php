<?php
require '../../Config/connexion.php';

// Supprimer tous les anciens comptes
$pdo->exec("TRUNCATE TABLE administrateurs");

// Créer les comptes avec mot de passe haché
$admins = [
    [
        'prenom' => 'Cherif',
        'nom' => 'Diouf',
        'email' => 'el.hadji.ahmadou.cherif.diouf@gmail.com',
        'motdepasse' => '123Cherif@Diouf'
    ],
    [
        'prenom' => 'Amatal',
        'nom' => 'Faidhoine',
        'email' => 'amatalhafourfaidhoine@gmail.com',
        'motdepasse' => '123Amatal'
    ]
];

$sql = "INSERT INTO administrateurs (prenom, nom, email, motdepasse, date_creation) 
        VALUES (:prenom, :nom, :email, :motdepasse, NOW())";
$stmt = $pdo->prepare($sql);

foreach ($admins as $admin) {
    $stmt->execute([
        'prenom' => $admin['prenom'],
        'nom' => $admin['nom'],
        'email' => $admin['email'],
        'motdepasse' => password_hash($admin['motdepasse'], PASSWORD_DEFAULT)
    ]);
}

echo "Comptes insérés avec succès.\n";

?>