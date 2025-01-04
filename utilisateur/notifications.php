<?php
session_start();
include '../DB/db_connection.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM notifications WHERE utilisateur_id = ? ORDER BY date_creation DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<header>
    <?php include('../includes/includes_header_dashboard.php'); ?>
</header>

<div class="notifications-container">
    <h2>Vos Notifications</h2>
    <?php if ($result->num_rows > 0): ?>
        <ul class="notifications-list">
            <?php while ($notification = $result->fetch_assoc()): ?>
                <li class="<?= $notification['lu'] ? 'notification-lue' : 'notification-non-lue' ?>">
                    <?= htmlspecialchars($notification['message']) ?>
                    <span class="date-notification"><?= htmlspecialchars($notification['date_creation']) ?></span>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Aucune notification.</p>
    <?php endif; ?>
</div>

<footer>
    <?php include('../includes/includes_footer.php'); ?>
</footer>

</body>
</html>
