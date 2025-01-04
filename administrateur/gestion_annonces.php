<?php
include('../DB/db_connection.php');
session_start();

// Vérification si l'utilisateur est administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    die("Accès refusé : Vous devez être un administrateur pour accéder à cette page.");
}

// Récupérer les annonces en attente
$sql_annonces = "SELECT * FROM annonces WHERE statut = 'en attente'";
$result = $conn->query($sql_annonces);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des annonces</title>
    <link rel="stylesheet" href="../style.css"> 
</head>
<body>
     <!-- Image principale -->
    <div class="image-container">
        <img src="/location_voitures/images/renault-stock-home.webp" alt="Renault Stock" class="image_voiture">
    </div>
    <h2>Gestion des annonces en attente</h2>

    <!-- Affichage des annonces -->
    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Photo</th>
                    <th>Marque</th>
                    <th>Modèle</th>
                    <th>Description</th>
                    <th>Prix par jour</th>
                    <th>Date de début</th>
                    <th>Heure de début</th>
                    <th>Date de fin</th>
                    <th>Heure de fin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($annonce = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($annonce['id']) ?></td>
                        <td>
                            <?php if (!empty($annonce['photo'])): ?>
                                <img src="../<?= htmlspecialchars($annonce['photo']) ?>" alt="Photo de la voiture" style="width: 100px; height: auto;">
                            <?php else: ?>
                                <span>Aucune photo</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($annonce['marque']) ?></td>
                        <td><?= htmlspecialchars($annonce['modele']) ?></td>
                        <td><?= htmlspecialchars($annonce['description']) ?></td>
                        <td><?= htmlspecialchars($annonce['prix_par_jour']) ?> €</td>
                        <td><?= htmlspecialchars($annonce['date_debut']) ?></td>
                        <td><?= htmlspecialchars($annonce['heure_debut']) ?></td>
                        <td><?= htmlspecialchars($annonce['date_fin']) ?></td>
                        <td><?= htmlspecialchars($annonce['heure_fin']) ?></td>
                        <td>
                            <form action="traiter_annonce.php" method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $annonce['id'] ?>">
                                <button type="submit" name="action" value="valider">Valider</button>
                            </form>
                            <form action="traiter_annonce.php" method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $annonce['id'] ?>">
                                <button type="button" name="action" value="refuser" onclick="toggleMotif(<?= $annonce['id'] ?>)">Refuser</button>
                                <textarea name="motif" id="motif_<?= $annonce['id'] ?>" placeholder="Motif de refus" style="display:none;"></textarea>
                                <button type="submit" name="action" value="refuser" style="display:none;" id="submit_<?= $annonce['id'] ?>">Confirmer le refus</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune annonce en attente.</p>
    <?php endif; ?>

    <!-- Lien pour retourner au tableau de bord -->
    <a href="dashboard_admin.php">Retour au tableau de bord</a>

    <script>
        function toggleMotif(id) {
            const motifField = document.getElementById('motif_' + id);
            const submitButton = document.getElementById('submit_' + id);
            motifField.style.display = 'block';
            submitButton.style.display = 'inline-block';
        }
    </script>
</body>
</html>
