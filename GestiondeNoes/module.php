<?php require_once "adminOnly.php"; 
require_once('inc/dbConnect.php');



// --- Handle Delete ---
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Check if module has grhades
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM grades WHERE module_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if ($res['count'] > 0) {
        $error_msg = "Impossible de supprimer ce module, il est utilisé par des notes existantes.";
    } else {
        $stmt = $conn->prepare("DELETE FROM modules WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// --- Handle Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'], $_POST['name'], $_POST['coef'])) {
    $update_id = $_POST['update_id'];
    $name = $_POST['name'];
    $coef = $_POST['coef'];

    $stmt = $conn->prepare("UPDATE modules SET name = ?, coef = ? WHERE id = ?");
    $stmt->bind_param("sdi", $name, $coef, $update_id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}




$sql='SELECT * FROM modules';
$result = $conn->query($sql) or die ($conn->error);
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['submit'], $_POST['name'], $_POST['coef'])
) {

    $sql = "INSERT INTO modules (name, coef) VALUES (?, ?)";

    $stmt = $conn->stmt_init();
    $stmt->prepare($sql);

    // s = string, d = double (number)
    $stmt->bind_param(
        "sd",
        $_POST['name'],
        $_POST['coef']
    );

if ($stmt->execute()) {
    // Redirect after successful insertion to prevent duplicate on refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
} else {
    echo "Error: " . $stmt->error;
}

    $stmt->close();
}
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
    <div style="">
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
                    <li class="nav-item"><a href="#" class="nav-link active">Modules</a></li>
                    <li class="nav-item"><a href="notes.php" class="nav-link">Saisie Notes</a></li>
                </ul>
            </nav>

            <div class="main-content">
                <h1 class="page-title">Gestion des Modules</h1>
                <p class="subtitle">Ajouter et modifier les modules d'enseignement</p>

                <div class="form-section">
                    <form method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Intitulé du Module</label>
                                <input type="text" class="form-input" name="name" placeholder="Exemple: Développement Web">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Coefficient</label>
                                <input type="number" name="coef" class="form-input" min="1" max="5" placeholder="Entre 1 et 5">
                            </div>
                        </div>
                        <button name="submit" type="submit" class="btn btn-success">Ajouter un Module</button>
                    </form>
                </div>

                <h2 style="margin: 30px 0 20px; color: #2c3e50; font-size: 20px;">Liste des Modules</h2>
                <?php while ($module = $result->fetch_assoc()): ?>
                <div class="module-item">
                    <div class="module-details">
                        <h3><?php echo $module['name']; ?></h3>
                        <div class="module-coef"><?php echo "Coefficient:".$module['coef']; ?></div>
                    </div>
                    <div class="action-buttons">
    <!-- Update: show a form to update -->
    <form method="post" style="display:inline-block;">
        <input type="hidden" class="inputos" name="update_id" value="<?php echo $module['id']; ?>">
        <input type="text" class="inputos" name="name" value=" <?php echo $module['name']; ?>" required>
        <input type="number" class="inputos" name="coef" value="<?php echo $module['coef']; ?>" min="1" max="5" required>
        <button type="submit" class="btn btn-secondary btn-sm">Modifier</button>
    </form>

    <!-- Delete -->
    <a href="?delete_id=<?php echo $module['id']; ?>" onclick="return confirm('Voulez-vous vraiment supprimer ce module ?');" class="btn btn-danger btn-sm">Supprimer</a>
</div>

                </div>
                <?php endwhile; ?>

            </div>
        </div>
    </div>
</body>
</html>