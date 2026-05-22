<?php
require_once "userOnly.php"; 
require_once('inc/dbConnect.php');

$user_id = $_SESSION['user_id']; // Get the logged-in student ID

// Join grades with modules to get module names and coefficients
$sql = "
    SELECT g.value, g.created_at, m.name, m.coef
    FROM grades g
    INNER JOIN modules m ON g.module_id = m.id
    WHERE g.student_user_id = ?
    ORDER BY g.created_at DESC
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the user's email from the database
$stmt_user = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_info = $result_user->fetch_assoc();

// Calculate summary: number of modules, average, total coef
$total_value = 0;
$total_coef = 0;
$grades_data = [];
while ($row = $result->fetch_assoc()) {
    $grades_data[] = $row;
    $total_value += $row['value'] * $row['coef'];
    $total_coef += $row['coef'];
}


$average = $total_coef > 0 ? round($total_value / $total_coef, 2) : 0;

// Determine result and mention
if ($average >= 16) {
    $result_text = "Admis";
    $mention = "Très Bien";
} elseif ($average >= 14) {
    $result_text = "Admis";
    $mention = "Bien";
} elseif ($average >= 12) {
    $result_text = "Admis";
    $mention = "Assez Bien";
} elseif ($average >= 10) {
    $result_text = "Admis";
    $mention = "Passable";
} else {
    $result_text = "Ajourné";
    $mention = "Sans mention";
}
$num_modules = count($grades_data);

$email = "ismail@emsi.ma";
$name = strstr($email, '@', true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
        <div>
        <div class="topbar">
            <div class="topbar-content">
                <div class="logo">SGN - Système de Gestion des Notes</div>
                <div class="user-menu">
                    <span class="username"><?php echo $user_info['email']; ?></span>
                    <button class="logout-btn"><a class="linkss" href="logout.php">Déconnexion</a></button>
                </div>
            </div>
        </div>

        <div class="container">
            <nav class="navigation">
                <ul class="nav-list">
                    <li class="nav-item"><a href="#" class="nav-link active">Mes Notes</a></li>
                    <li class="nav-item"><a href="403.php" class="nav-link active">Saisie Notes</a></li>
                    <li class="nav-item"><a href="http://localhost:8000/liste/n" class="nav-link">Modules</a></li>
                    <li class="nav-item"><a href="profile.php" class="nav-link">Profil</a></li>
                </ul>
            </nav>

            <div class="main-content">
                <h1 class="page-title">Relevé de Notes</h1>
                <p class="subtitle">Consultation de vos résultats académiques</p>

                <div class="summary-cards">
                    <div class="card">
                        <div class="card-label">Moyenne Générale</div>
                        <div class="card-value"><?php echo $average; ?> / 20</div>
                    </div>
                    <div class="card" style="border-left-color: #27ae60;">
                        <div class="card-label">Résultat</div>
                        <div class="card-value"><?php echo $result_text ?></div>
                        <span class="mention-badge">Mention: <?php echo $mention; ?></span>
                    </div>
                    <div class="card" style="border-left-color: #f39c12;">
                        <div class="card-label">Modules</div>
                        <div class="card-value"><?php echo $num_modules; ?></div>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th>Coefficient</th>
                            <th>Note</th>
                            <th>Date d'évaluation</th>
                        </tr>
                    </thead>
                    <tbody>
<?php foreach ($grades_data as $row): ?>
    <tr>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['coef']; ?></td>
        <td><span class="note-value note-high"><?php echo $row['value']; ?></span></td>
        <td><?php echo $row['created_at']; ?></td>
    </tr>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</body>
</html>