<?php
require_once "adminOnly.php";
require_once('inc/dbConnect.php');

// --- Handle grade insertion ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'], $_POST['module_id'], $_POST['value'])) {
    $student_id = $_POST['student_id'];
    $module_id = $_POST['module_id'];
    $value = $_POST['value'];

    if (!empty($_POST['edit_grade_id'])) {
        // --- Update existing grade ---
        $grade_id = $_POST['edit_grade_id'];
        $stmt = $conn->prepare("UPDATE grades SET student_user_id = ?, module_id = ?, value = ?, created_at = NOW() WHERE id = ?");
        $stmt->bind_param("iidi", $student_id, $module_id, $value, $grade_id);
        if ($stmt->execute()) {
            $success_msg = "La note a été mise à jour avec succès.";
        } else {
            $error_msg = "Erreur lors de la mise à jour : " . $stmt->error;
        }
    } else {
        // --- Insert new grade ---
        $stmt = $conn->prepare("INSERT INTO grades (student_user_id, module_id, value, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iid", $student_id, $module_id, $value);
        if ($stmt->execute()) {
            $success_msg = "La note a été enregistrée avec succès.";
        } else {
            $error_msg = "Erreur : " . $stmt->error;
        }
    }
}

// --- Load grade to edit if requested ---
$edit_grade = null;
if (isset($_GET['edit_grade_id'])) {
    $edit_id = intval($_GET['edit_grade_id']);
    $stmt = $conn->prepare("SELECT * FROM grades WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_grade = $result->fetch_assoc();
}

// --- Get all students (use email as display) ---
$students_result = $conn->query("SELECT id, email FROM users ORDER BY email");

// --- Get all modules ---
$modules_result = $conn->query("SELECT id, name FROM modules ORDER BY name");

// --- Get recently entered grades ---
$grades_sql = "
    SELECT g.id, g.value, g.created_at, u.email AS student_email, m.name AS module_name
    FROM grades g
    INNER JOIN users u ON g.student_user_id = u.id
    INNER JOIN modules m ON g.module_id = m.id
    ORDER BY g.created_at DESC
    LIMIT 10
";
$grades_result = $conn->query($grades_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Saisie des Notes</title>
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
                <li class="nav-item"><a href="listeNotes.php" class="nav-link">Liste des Notes</a></li>
                <li class="nav-item"><a href="module.php" class="nav-link">Modules</a></li>
                <li class="nav-item"><a href="#" class="nav-link active">Saisie Notes</a></li>

            </ul>
        </nav>

        <div class="main-content">
            <h1 class="page-title">Saisie des Notes</h1>
            <p class="subtitle">Enregistrer ou modifier les notes des étudiants</p>

            <?php if (isset($success_msg)): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php elseif (isset($error_msg)): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <div class="form-section">
                <form method="post">
                    <?php if ($edit_grade): ?>
                        <input type="hidden" name="edit_grade_id" value="<?php echo $edit_grade['id']; ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Sélectionner l'étudiant</label>
                            <select class="form-select" name="student_id" required>
                                <option value="">-- Choisir un étudiant --</option>
                                <?php 
                                $students_result->data_seek(0); // reset pointer
                                while ($student = $students_result->fetch_assoc()): ?>
                                    <option value="<?php echo $student['id']; ?>" 
                                        <?php if ($edit_grade && $edit_grade['student_user_id'] == $student['id']) echo 'selected'; ?>>
                                        <?php echo $student['email']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Sélectionner le module</label>
                            <select class="form-select" name="module_id" required>
                                <option value="">-- Choisir un module --</option>
                                <?php 
                                $modules_result->data_seek(0); // reset pointer
                                while ($module = $modules_result->fetch_assoc()): ?>
                                    <option value="<?php echo $module['id']; ?>" 
                                        <?php if ($edit_grade && $edit_grade['module_id'] == $module['id']) echo 'selected'; ?>>
                                        <?php echo $module['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Note (sur 20)</label>
                        <input type="number" class="form-input" name="value" min="0" max="20" step="0.25" 
                               value="<?php echo $edit_grade ? $edit_grade['value'] : ''; ?>" 
                               placeholder="Saisir la note entre 0 et 20" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <?php echo $edit_grade ? 'Mettre à jour la Note' : 'Enregistrer la Note'; ?>
                    </button>
                </form>
            </div>

            <h2 style="margin: 30px 0 20px; color: #2c3e50; font-size: 20px;">Notes Récemment Saisies</h2>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Module</th>
                        <th>Note</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($grade = $grades_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $grade['student_email']; ?></td>
                            <td><?php echo $grade['module_name']; ?></td>
                            <td><span class="note-value note-high"><?php echo $grade['value']; ?></span></td>
                            <td><?php echo $grade['created_at']; ?></td>
                            <td>
                                <form method="get" style="display:inline">
                                    <input type="hidden" name="edit_grade_id" value="<?php echo $grade['id']; ?>">
                                    <button class="btn btn-secondary btn-sm" type="submit">Modifier</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
</body>
</html>
