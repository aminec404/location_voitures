<?php
session_start();
include 'DB/db_connection.php'; // Assurez-vous que ce fichier contient les détails de connexion à la base de données

try {
    // Récupérer les annonces non réservées
    $sql = "SELECT * FROM annonces 
            WHERE est_reservee = 'non réservée' 
            AND statut = 'validée' 
            ORDER BY date_creation DESC";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $_SESSION['resultats'] = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Erreur dans la préparation de la requête.";
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Annonces</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <?php include('includes/includes_header.php'); ?>
</header>
<aside class="barre-filtres">
    <h3>Filtres Sélectionnés</h3>

    <!-- Formulaire pour les filtres -->
    <form action="process/process_filtrage.php" method="POST">
        <!-- Filtre de prix -->
        <div class="filtre">
            <input type="checkbox" id="filtre-prix" class="toggle-filtre">
            <label class="filtre-titre" for="filtre-prix">Prix</label>
            <div class="filtre-contenu">
                <label for="prix-min">De :</label>
                <input type="number" id="prix-min" name="price_min" placeholder="Min">
                <label for="prix-max">À :</label>
                <input type="number" id="prix-max" name="price_max" placeholder="Max">
                <br>
                <label><input type="radio" name="tri" value="prix_croissant"> prix croissant</label><br>
                <label><input type="radio" name="tri" value="prix_decroissant"> prix décroissant</label>
            </div>
        </div>

        <!-- Bouton Appliquer les filtres -->
        <button type="submit" class="bouton-appliquer-filtres">Appliquer</button>
    </form>
</aside>
<div class="resultats-recherche">
    <h2 style="text-align:center;">Annonces Disponibles</h2>

    <div style="text-align:center; margin-bottom: 20px;">
        <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
            <a href="deposer_annonce.php" class="btn-deposer-annonce">Déposer une annonce</a>
        <?php else: ?>
            <p style="color: red; font-size: 16px;">Connectez-vous pour déposer une annonce</p>
        <?php endif; ?>
    </div>

    <?php if (!empty($_SESSION['resultats'])): ?>
        <div class="annonces-container">
            <?php foreach ($_SESSION['resultats'] as $annonce): ?>
                <div class="annonce-card">
                    <img src="<?= htmlspecialchars($annonce['photo'] ?? 'default-image.jpg') ?>" alt="Photo de <?= htmlspecialchars($annonce['marque']) ?>">
                    <div class="annonce-card-content">
                        <h3><?= htmlspecialchars($annonce['marque']) ?> <?= htmlspecialchars($annonce['modele']) ?></h3>
                        <p class="price"><?= htmlspecialchars($annonce['prix_par_jour']) ?> € / jour</p>
                        <p><?= htmlspecialchars($annonce['description']) ?></p>
                        <p class="availability">Disponible du <?= htmlspecialchars($annonce['date_debut']) ?> à <?= htmlspecialchars($annonce['heure_debut']) ?><br> 
                            au <?= htmlspecialchars($annonce['date_fin']) ?> à <?= htmlspecialchars($annonce['heure_fin']) ?>
                        </p>

                        <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                            <form action="process/process_reservation.php" method="POST">
                                <input type="hidden" name="annonce_id" value="<?= htmlspecialchars($annonce['id']) ?>">
                                <button type="submit" class="btn-reserver">Réserver</button>
                            </form>
                        <?php else: ?>
                            <p style="color: red; font-size: 14px;">Connectez-vous pour réserver</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif (!empty($_SESSION['error_message'])): ?>
        <p class="message-erreur"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
    <?php else: ?>
        <p class="message-erreur">Aucune annonce disponible.</p>
    <?php endif; ?>
</div>

<footer>
    <?php include('includes/includes_footer.php'); ?>
</footer>
</body>
</html>
