<?php
// Démarrer la session
session_start();
require_once '../DB/db_connection.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'client') {
    // Redirection vers la page de connexion si non connecté ou rôle incorrect
    $_SESSION['error'] = "Veuillez vous connecter pour accéder à votre profil.";
    header("Location: ../login.php");
    exit();
}

// Récupérez les informations de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

// Requête préparée pour récupérer les informations utilisateur
$stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    die("Erreur : utilisateur non trouvé.");
}

$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="../style1.css">
</head>
<body>
<header>
        <?php include('../includes/includes_header_dashboard.php'); ?>
        <h1>Bienvenue, <?php echo htmlspecialchars($user['prenom']); ?> !</h1>
    </header>
    <main>
        <!-- Section des informations utilisateur -->
        <section>
            <h2>Mes Informations</h2>
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['nom']); ?></p>
            <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user['prenom']); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <a href="modifier_profile.php" class="btn">Modifier mes informations</a>
        </section>

        <!-- Section des réservations -->
        <section>
    <h2>Mes Réservations</h2>
    <?php
    // Requête pour récupérer les annonces validées et réservées
    $stmt_annonces = $conn->prepare("
        SELECT * 
        FROM annonces 
        WHERE statut = 'validée' 
        AND est_reservee = 'Réservée'
    ");
    $stmt_annonces->execute();
    $annonces_result = $stmt_annonces->get_result();
    if ($annonces_result && $annonces_result->num_rows > 0) {
        while ($annonce = $annonces_result->fetch_assoc()) {
            echo "<div class='annonce'>";
            echo "<h3>Annonce ID: " . htmlspecialchars($annonce['id']) . "</h3>";
            echo "<p>Marque: " . htmlspecialchars($annonce['marque']) . "</p>";
            echo "<p>Modèle: " . htmlspecialchars($annonce['modele']) . "</p>";
            echo "<p>Prix par jour : " . htmlspecialchars($annonce['prix_par_jour']) . " €</p>";
            echo "<p>Lieu: " . htmlspecialchars($annonce['location']) . "</p>";
            echo "<p>Date début: " . htmlspecialchars($annonce['date_debut']) . "</p>";
            echo "<p>Date fin: " . htmlspecialchars($annonce['date_fin']) . "</p>";
            echo "<p>Statut: " . htmlspecialchars($annonce['statut']) . "</p>";

            // Vérifier si l'utilisateur a déjà laissé un avis pour cette annonce
            $stmt_avis = $conn->prepare("
                SELECT note, commentaire 
                FROM avis 
                WHERE utilisateur_id = ? 
                AND annonce_id = ?
            ");
            $stmt_avis->bind_param("ii", $user_id, $annonce['id']);
            $stmt_avis->execute();
            $avis_result = $stmt_avis->get_result();

            if ($avis_result && $avis_result->num_rows > 0) {
                // Avis déjà soumis - afficher la note et le commentaire
                $avis = $avis_result->fetch_assoc();
                echo "<p><strong>Votre note :</strong> " . htmlspecialchars($avis['note']) . " / 5</p>";
                echo "<p><strong>Votre commentaire :</strong> " . htmlspecialchars($avis['commentaire']) . "</p>";
            } else {
                // Formulaire pour donner un avis et une note
                echo "<form method='POST' action='enregistrer_avis.php'>";
                echo "<input type='hidden' name='annonce_id' value='" . htmlspecialchars($annonce['id']) . "'>";
                echo "<label for='note'>Note (sur 5) :</label>";
                echo "<select name='note' id='note' required>";
                for ($i = 1; $i <= 5; $i++) {
                    echo "<option value='$i'>$i</option>";
                }
                echo "</select><br>";
                echo "<label for='commentaire'>Commentaire :</label>";
                echo "<textarea name='commentaire' id='commentaire' rows='4' required></textarea><br>";
                echo "<button type='submit'>Envoyer</button>";
                echo "</form>";
            }

            echo "</div>";
        }
    } else {
        echo "<p>Aucune annonce réservée trouvée.</p>";
    }
    ?>
</section>


        <!-- Section des annonces -->
        <section>
            <h2>Mes Annonces</h2>
            <?php
            // Requête pour récupérer les annonces de l'utilisateur
            $stmt_annonces = $conn->prepare("SELECT * FROM annonces WHERE utilisateur_id = ?");
            $stmt_annonces->bind_param("i", $user_id);
            $stmt_annonces->execute();
            $annonces_result = $stmt_annonces->get_result();

            if ($annonces_result && $annonces_result->num_rows > 0) {
                while ($annonce = $annonces_result->fetch_assoc()) {
                    $marque = htmlspecialchars($annonce['marque'] ?? "Non spécifiée");
                    $modele = htmlspecialchars($annonce['modele'] ?? "Non spécifié");
                    $description = htmlspecialchars($annonce['description'] ?? "Non spécifiée");
                    $prix_par_jour = htmlspecialchars($annonce['prix_par_jour'] ?? "Non spécifié");
                    $statut = htmlspecialchars($annonce['statut'] ?? "Non spécifié");
                    $motif_refus = htmlspecialchars($annonce['motif_refus'] ?? "Aucun motif spécifié");

                    echo "<div class='annonce'>";
                    echo "<h3>{$marque} - {$modele}</h3>";
                    echo "<p>Description : {$description}</p>";
                    echo "<p>Prix par jour : {$prix_par_jour} €</p>";
                    echo "<p>État de l'annonce : {$statut}</p>";

                    // Afficher le motif de refus si l'annonce est refusée
                    if ($statut === 'refusée') {
                        echo "<p><strong>Motif de refus :</strong> {$motif_refus}</p>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p>Aucune annonce trouvée.</p>";
            }
            ?>
        </section>
    </main>

    <footer>
        <p>© 2024 Location de Voitures. Tous droits réservés.</p>
    </footer>
</body>
</html>
