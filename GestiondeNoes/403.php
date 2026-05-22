<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
        <div >
        <div class="topbar">
            <div class="topbar-content">
                <div class="logo">SGN - Système de Gestion des Notes</div>
                <div class="user-menu">
                    <span class="username">403</span>
                    <button href="logout.php" class="logout-btn"><a class="linkss" href="logout.php">Déconnexion</a></button>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="main-content">
                <div class="error-page">
                    <div class="error-code">403</div>
                    <div class="error-title">Accès Non Autorisé</div>
                    <p class="error-text">
                        Vous n'avez pas les permissions nécessaires pour accéder à cette ressource.<br>
                        Cette page est réservée aux administrateurs ou aux propriétaires des données.
                    </p>
                    <button class="btn btn-primary"><a class="linkss" href="user.php">Retour à l'accueil</a></button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>