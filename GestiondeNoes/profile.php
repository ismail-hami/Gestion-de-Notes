<?php
require_once "userOnly.php"; 
require_once('inc/dbConnect.php');

$user_id = $_SESSION['user_id'];

// Fetch user information
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


$email_parts = explode('@', $user['email']);
$first_name = ucfirst($email_parts[0]);

// Generate random phone number starting with 06
$phone = '06' . str_pad(rand(10, 99), 2, '0', STR_PAD_LEFT)."-".str_pad(rand(1000, 9999), 6, '0', STR_PAD_LEFT);

// School is always EMSI
$school = 'EMSI - École Marocaine des Sciences de l\'Ingénieur';

// Year is always 2023
$year = '2023';

// Generate random address in Casablanca
$streets = [
    'Boulevard Zerktouni',
    'Avenue Hassan II',
    'Rue Mohamed V',
    'Boulevard Moulay Youssef',
    'Avenue des FAR',
    'Rue Abdelmoumen',
    'Boulevard Rachidi',
    'Rue Bnou Sina'
];
$address = rand(10, 250) . ' ' . $streets[array_rand($streets)] . ', Casablanca';

// Calculate student statistics
$stats_sql = "
    SELECT 
        COUNT(DISTINCT g.module_id) as modules_count,
        AVG(g.value) as average_grade,
        COUNT(g.id) as total_grades
    FROM grades g
    WHERE g.student_user_id = ?
";
$stmt_stats = $conn->prepare($stats_sql);
$stmt_stats->bind_param("i", $user_id);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();

// Get recent activity - last 5 grades
$recent_sql = "
    SELECT g.value, g.created_at, m.name as module_name
    FROM grades g
    INNER JOIN modules m ON g.module_id = m.id
    WHERE g.student_user_id = ?
    ORDER BY g.created_at DESC
    LIMIT 5
";
$stmt_recent = $conn->prepare($recent_sql);
$stmt_recent->bind_param("i", $user_id);
$stmt_recent->execute();
$recent_grades = $stmt_recent->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - SGN</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div>
        <div class="topbar">
            <div class="topbar-content">
                <div class="logo">SGN - Système de Gestion des Notes</div>
                <div class="user-menu">
                    <span class="username"><?php echo htmlspecialchars($user['email']); ?></span>
                    <button class="logout-btn"><a class="linkss" href="logout.php">Déconnexion</a></button>
                </div>
            </div>
        </div>

        <div class="container">
            <nav class="navigation">
                <ul class="nav-list">
                    <li class="nav-item"><a href="user.php" class="nav-link">Mes Notes</a></li>
                    <li class="nav-item"><a href="403.php" class="nav-link active">Saisie Notes</a></li>
                    <li class="nav-item"><a href="403.php" class="nav-link">Modules</a></li>
                    <li class="nav-item"><a href="profile.php" class="nav-link active">Profil</a></li>
                </ul>
            </nav>

            <div class="main-content">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-image-container">
                        <div class="profile-image-placeholder">
                            <?php echo strtoupper(substr($first_name, 0, 1)); ?>
                        </div>
                    </div>
                    <div class="profile-info">
                        <h2><?php echo $first_name ?></h2>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>École:</strong> EMSI Casablanca</p>
                        <p><strong>Promotion:</strong> 2023</p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $stats['modules_count'] ?? 0; ?></div>
                        <div class="stat-label">Modules Suivis</div>
                    </div>
                    <div class="stat-card" style="border-top-color: #27ae60;">
                        <div class="stat-value"><?php echo isset($stats['average_grade']) ? round($stats['average_grade'], 2) : 0; ?></div>
                        <div class="stat-label">Moyenne Générale</div>
                    </div>
                    <div class="stat-card" style="border-top-color: #f39c12;">
                        <div class="stat-value"><?php echo $stats['total_grades'] ?? 0; ?></div>
                        <div class="stat-label">Notes Enregistrées</div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="info-section">
                    <h3>📋 Informations Personnelles</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Nom Complet</div>
                            <div class="info-value"><?php echo htmlspecialchars($first_name); ?></div>
                        </div>
                        <div class="info-item" style="border-left-color: #27ae60;">
                            <div class="info-label">Email Institutionnel</div>
                            <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div class="info-item" style="border-left-color: #9b59b6;">
                            <div class="info-label">Téléphone</div>
                            <div class="info-value"><?php echo $phone; ?></div>
                        </div>
                        <div class="info-item" style="border-left-color: #e74c3c;">
                            <div class="info-label">Identifiant Étudiant</div>
                            <div class="info-value">EMSI<?php echo str_pad($user_id, 6, '0', STR_PAD_LEFT); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="info-section">
                    <h3>🎓 Informations Académiques</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Établissement</div>
                            <div class="info-value"><?php echo htmlspecialchars($school); ?></div>
                        </div>
                        <div class="info-item" style="border-left-color: #27ae60;">
                            <div class="info-label">Promotion</div>
                            <div class="info-value"><?php echo $year; ?></div>
                        </div>
                        <div class="info-item" style="border-left-color: #9b59b6;">
                            <div class="info-label">Adresse</div>
                            <div class="info-value"><?php echo htmlspecialchars($address); ?></div>
                        </div>
                        <div class="info-item" style="border-left-color: #f39c12;">
                            <div class="info-label">Statut</div>
                            <div class="info-value">
                                <?php 
                                $avg = isset($stats['average_grade']) ? $stats['average_grade'] : 0;
                                echo $avg >= 10 ? '✓ Actif' : '⚠ À surveiller';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="recent-activity">
                    <h3 style="color: #2c3e50; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #ecf0f1;">
                        📊 Activité Récente
                    </h3>
                    <?php if ($recent_grades->num_rows > 0): ?>
                        <?php while ($activity = $recent_grades->fetch_assoc()): ?>
                            <div class="activity-item">
                                <div>
                                    <div class="activity-module"><?php echo htmlspecialchars($activity['module_name']); ?></div>
                                    <div class="activity-date"><?php echo date('d/m/Y à H:i', strtotime($activity['created_at'])); ?></div>
                                </div>
                                <div>
                                    <span class="note-value <?php 
                                        if ($activity['value'] >= 16) echo 'note-high';
                                        elseif ($activity['value'] >= 14) echo 'note-medium';
                                        elseif ($activity['value'] >= 10) echo 'note-low';
                                        else echo 'note-fail';
                                    ?>"><?php echo $activity['value']; ?>/20</span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #7f8c8d; padding: 20px;">
                            Aucune activité récente
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>