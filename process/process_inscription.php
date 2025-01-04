<?php
// Inclure la connexion à la base de données
include('../DB/db_connection.php');

// Démarrer la session pour gérer les messages d'erreur et de succès
session_start();

// Vérifier que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et valider les données du formulaire
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : null;
    $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $mot_de_passe = isset($_POST['mot_de_passe']) ? trim($_POST['mot_de_passe']) : null;
    $confirmer_mot_de_passe = isset($_POST['confirmer_mot_de_passe']) ? trim($_POST['confirmer_mot_de_passe']) : null;

    // Vérifier que tous les champs sont remplis
    if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe) || empty($confirmer_mot_de_passe)) {
        $_SESSION['error'] = "Tous les champs sont requis.";
        header("Location: ../inscription.php");
        exit;
    }

    // Vérifier si les mots de passe correspondent
    if ($mot_de_passe !== $confirmer_mot_de_passe) {
        $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
        header("Location: ../inscription.php");
        exit;
    }

    // Vérifier si l'email est valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Adresse email invalide.";
        header("Location: ../inscription.php");
        exit;
    }

    // Vérifier si l'email existe déjà
    $sql_check_email = "SELECT id FROM utilisateurs WHERE email = ?";
    $stmt = $conn->prepare($sql_check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email déjà utilisé
        $_SESSION['error'] = "Cet email est déjà utilisé.";
        header("Location: ../inscription.php");
        exit;
    }

    // Hasher le mot de passe pour la sécurité
    $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Insérer le nouvel utilisateur dans la base de données
    $sql_insert_user = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, date_creation) 
                        VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql_insert_user);
    $stmt->bind_param("ssss", $nom, $prenom, $email, $mot_de_passe_hache);

    if ($stmt->execute()) {
        // Succès de l'inscription
        $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        header("Location: ../index.php");
        exit;
    } else {
        // Erreur lors de l'insertion
        $_SESSION['error'] = "Une erreur est survenue. Veuillez réessayer.";
        header("Location: ../inscription.php");
        exit;
    }
    
    // Fermer la connexion
    $stmt->close();
    $conn->close();
} else {
    // Rediriger vers la page d'inscription si l'accès direct est tenté
    header("Location: ../inscription.php");
    exit;
}
?>
