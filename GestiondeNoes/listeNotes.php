<?php
require_once "adminOnly.php";
require_once('inc/dbConnect.php');

// --- Handle grade deletion ---
if (isset($_GET['delete_grade_id'])) {
    $delete_id = intval($_GET['delete_grade_id']);
    $stmt = $conn->prepare("DELETE FROM grades WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success_msg = "La note a été supprimée avec succès.";
    } else {
        $error_msg = "Erreur lors de la suppression : " . $stmt->error;
    }
}

// --- Get all grades with student email and module name ---
$sql = "
    SELECT g.id, g.value, g.created_at, u.email AS student_email, m.name AS module_name, m.coef
    FROM grades g
    INNER JOIN users u ON g.student_user_id = u.id
    INNER JOIN modules m ON g.module_id = m.id
    ORDER BY g.created_at DESC
";
$grades_result = $conn->query($sql);

// --- Calculate statistics ---
$total_grades = $grades_result->num_rows;
$avg_query = "SELECT AVG(value) as avg_grade FROM grades";
$avg_result = $conn->query($avg_query);
$avg_data = $avg_result->fetch_assoc();
$average_grade = $avg_data['avg_grade'] ? round($avg_data['avg_grade'], 2) : 0;

$students_query = "SELECT COUNT(DISTINCT student_user_id) as student_count FROM grades";
$students_result = $conn->query($students_query);
$students_data = $students_result->fetch_assoc();
$total_students = $students_data['student_count'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Notes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div>
    <div class="topbar">
        <div class="topbar-content">
            <div class="logo">SGN - Administration</div>
            <div class="user-menu">
                <span class="username">Administrateur</span>
                <button class="logout-btn"><a class="linkss" href="logout.php">Déconnexion</a></button>
            </div>
        </div>
    </div>

    <div class="container">
        <nav class="navigation">
            <ul class="nav-list">
                <li class="nav-item"><a href="#" class="nav-link active">Liste des Notes</a></li>
                <li class="nav-item"><a href="module.php" class="nav-link">Modules</a></li>
                <li class="nav-item"><a href="notes.php" class="nav-link">Saisie Notes</a></li>
            </ul>
        </nav>

        <div class="main-content">
            <h1 class="page-title">Liste Complète des Notes</h1>
            <p class="subtitle">Consultation et gestion de toutes les notes enregistrées</p>

            <?php if (isset($success_msg)): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php elseif (isset($error_msg)): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="summary-cards">
                <div class="card">
                    <div class="card-label">Total des Notes</div>
                    <div class="card-value"><?php echo $total_grades; ?></div>
                </div>
                <div class="card" style="border-left-color: #27ae60;">
                    <div class="card-label">Moyenne Générale</div>
                    <div class="card-value"><?php echo $average_grade; ?> / 20</div>
                </div>
                <div class="card" style="border-left-color: #f39c12;">
                    <div class="card-label">Étudiants</div>
                    <div class="card-value"><?php echo $total_students; ?></div>
                </div>
            </div>

            <!-- Grades Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Étudiant</th>
                        <th>Module</th>
                        <th>Coefficient</th>
                        <th>Note</th>
                        <th>Date de Saisie</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grades_result->data_seek(0); // Reset pointer
                    while ($grade = $grades_result->fetch_assoc()): 
                        // Determine note class based on value
                        if ($grade['value'] >= 16) {
                            $note_class = 'note-high';
                        } elseif ($grade['value'] >= 14) {
                            $note_class = 'note-medium';
                        } elseif ($grade['value'] >= 10) {
                            $note_class = 'note-low';
                        } else {
                            $note_class = 'note-fail';
                        }
                    ?>
                        <tr>
                            <td><?php echo $grade['id']; ?></td>
                            <td><?php echo $grade['student_email']; ?></td>
                            <td><?php echo $grade['module_name']; ?></td>
                            <td><?php echo $grade['coef']; ?></td>
                            <td>
                                <span class="note-value <?php echo $note_class; ?>">
                                    <?php echo $grade['value']; ?>
                                </span>
                            </td>
                            <td><?php echo $grade['created_at']; ?></td>
                            <td>
                                <a href="notes.php?edit_grade_id=<?php echo $grade['id']; ?>" 
                                   class="btn btn-secondary btn-sm">Modifier</a>
                                <a href="?delete_grade_id=<?php echo $grade['id']; ?>" 
                                   onclick="return confirm('Voulez-vous vraiment supprimer cette note ?');" 
                                   class="btn btn-danger btn-sm">Supprimer</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    
                    <?php if ($total_grades == 0): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: #7f8c8d;">
                                Aucune note enregistrée pour le moment.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
</body>
</html>