<?php
// Démarrer la session
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil: Location de voitures</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<header>
    <?php include('../includes/includes_header_dashboard.php'); ?>
</header>

<!-- Message de connexion/déconnexion -->
<div class="status-connexion">
    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Vous êtes connecté en tant que <strong><?php echo htmlspecialchars($_SESSION['prenom']) . " " . htmlspecialchars($_SESSION['nom']); ?></strong></p>
    <?php else: ?>
        <p>Vous n'êtes pas connecté.</p>
    <?php endif; ?>
</div>

<main>
    <h1>Bienvenue sur notre site de location de voitures</h1>
    <p>Nous offrons une large sélection de véhicules pour répondre à vos besoins.</p>
</main>
<!-- Barre de réservation -->
<div class="reservation-bar">
    <form action="../process/process_recherche.php" method="POST">
        <!-- Lieu de prise en charge -->
        <div class="location">
            <label for="location">Lieu de prise en charge</label>
            <input type="text" id="location" name="location" placeholder="Calais, Pas-de-Calais, F
        </div>

        <!-- Date de début -->
        <div class="date">
            <label for="date_debut">Date de début</label>
            <input type="date" id="date_debut" name="date_debut" required>
        </div>

        <!-- Heure de début -->
        <div class="time">
            <label for="heure_debut">Heure de début</label>
            <input type="time" id="heure_debut" name="heure_debut" required>
        </div>

        <!-- Date de fin -->
        <div class="date">
            <label for="date_fin">Date de fin</label>
            <input type="date" id="date_fin" name="date_fin" required>
        </div>

        <!-- Heure de fin -->
        <div class="time">
            <label for="heure_fin">Heure de fin</label>
            <input type="time" id="heure_fin" name="heure_fin" required>
        </div>
        <!-- Bouton de recherche -->
        <div class="submit">
            <button type="submit" name="rechercher">Rechercher</button>
        </div>
    </form>
</div>

<footer>
    <?php include('../includes/includes_footer.php'); ?>
</footer>
</body>
</html>
