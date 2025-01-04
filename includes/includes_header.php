<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Déterminer le rôle de l'utilisateur
$role = $_SESSION['role'] ?? null; // Null si non connecté
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil: Location de voitures</title>
    <link rel="stylesheet" href="/location_voitures/style.css">
</head>
<body>
    <!-- Image principale -->
    <div class="image-container">
        <img src="/location_voitures/images/renault-stock-home.webp" alt="Renault Stock" class="image_voiture">
    </div>

    <!-- Barre de navigation -->
    <header class="top-bar">
        <div class="logo">
            L<img src="/location_voitures/images/roue.jpg" alt="Roue" class="logo-wheel">CAR
        </div>
        <nav class="nav-links">
            <?php if ($role === 'administrateur'): ?>
                <!-- Menu pour l'administrateur -->
                <a href="/location_voitures/index.php">Accueil</a>
                <a href="/location_voitures/annonces.php">Gérer Annonces</a>
                <a href="/location_voitures/administrateur/dashboard_admin.php">Tableau de bord</a>
                <a href="/location_voitures/logout.php">Se déconnecter</a>
            <?php elseif ($role === 'client'): ?>
                <!-- Menu pour le client -->
                <a href="/location_voitures/index.php">Accueil</a>
                <a href="/location_voitures/annonces.php">Annonces</a>
                <a href="/location_voitures/utilisateur/profile.php">Profil</a>
                <a href="/location_voitures/utilisateur/notifications.php">Notifications</a>
                <a href="/location_voitures/logout.php">Se déconnecter</a>
            <?php else: ?>
                <!-- Menu pour les utilisateurs non connectés -->
                <a href="/location_voitures/index.php">Accueil</a>
                <a href="/location_voitures/annonces.php">Annonces</a>
                <a href="/location_voitures/inscription.php">Inscription</a>
                <a href="/location_voitures/login.php">Connexion</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Message de connexion/déconnexion -->
    <div class="status-connexion">
        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Vous êtes connecté en tant que 
                <strong>
                    <?php echo htmlspecialchars($_SESSION['prenom']) . " " . htmlspecialchars($_SESSION['nom']); ?>
                </strong>
            </p>
        <?php else: ?>
            <p>Vous n'êtes pas connecté</p>
        <?php endif; ?>
    </div>
</body>
</html>
