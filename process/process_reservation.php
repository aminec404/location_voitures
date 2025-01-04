<?php
session_start();
include '../DB/db_connection.php'; // Assurez-vous que ce fichier contient les détails de connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $annonce_id = $_POST['annonce_id'] ?? null;

    if ($annonce_id) {
        // Mettre à jour la colonne est_reservee pour passer à 'réservée'
        $sql = "UPDATE annonces SET est_reservee = 'réservée' WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('i', $annonce_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $_SESSION['success_message'] = "Réservation réussie !";

                // Récupérer l'utilisateur qui a déposé l'annonce
                $sql_annonce = "SELECT utilisateur_id FROM annonces WHERE id = ?";
                $stmt_annonce = $conn->prepare($sql_annonce);
                $stmt_annonce->bind_param('i', $annonce_id);
                $stmt_annonce->execute();
                $result_annonce = $stmt_annonce->get_result();
                $annonce = $result_annonce->fetch_assoc();

                if ($annonce) {
                    $utilisateur_id_annonceur = $annonce['utilisateur_id'];

                    // Insérer la notification
                    $message = "Votre annonce a été réservée par un utilisateur.";
                    $sql_notification = "INSERT INTO notifications (utilisateur_id, message) VALUES (?, ?)";
                    $stmt_notification = $conn->prepare($sql_notification);
                    $stmt_notification->bind_param("is", $utilisateur_id_annonceur, $message);
                    $stmt_notification->execute();
                }

                $stmt_annonce->close();
                $stmt_notification->close();
            } else {
                $_SESSION['error_message'] = "Erreur lors de la réservation.";
            }

            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Erreur dans la préparation de la requête.";
        }
    } else {
        $_SESSION['error_message'] = "Aucune annonce sélectionnée.";
    }

    // Redirection vers la page des annonces
    header('Location: ../annonces.php');
    exit();
}
?>
