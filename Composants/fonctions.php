<?php
/**
 * Vérifie qu'un champ n'est pas vide après nettoyage.
 * @param string $valeur La valeur à vérifier
 * @return bool true si le champ est valide, false sinon
 */
function champ_requis(string $valeur): bool {
    return !empty(trim($valeur));
}

/**
 * Nettoie une valeur pour l'afficher sans risque dans du HTML.
 * @param string $valeur La valeur brute
 * @return string La valeur nettoyée
 */
function nettoyer(string $valeur): string {
    return htmlspecialchars(trim($valeur));
}

function log_visite($pdo, $page) {
    // Récupérer l'adresse IP du visiteur
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'inconnue';

    // Vérifier si on est derrière un proxy/CDN
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    // Préparer et insérer la visite
    $sql = "INSERT INTO visites (ip, page, date_visite) 
            VALUES (:ip, :page, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'ip' => $ip,
        'page' => $page
    ]);
}

function generer_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifier_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>

