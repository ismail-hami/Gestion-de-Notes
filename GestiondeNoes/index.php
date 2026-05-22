<?php  require_once "passprotection.php"  ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion des Notes</title>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion des Notes</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Page Connexion -->
    <div style="display: none;">
        <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background-color: #ecf0f1;">
            <div style="background: white; padding: 40px; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 400px;">
                <h2 style="text-align: center; margin-bottom: 30px; color: #2c3e50;">Connexion</h2>
                <form>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input" placeholder="votreemail@exemple.com" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" class="form-input" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Se connecter</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Vue Étudiant - Mes Notes -->

    <!-- Vue Admin - Gestion Modules -->


    <!-- Vue Admin - Saisie de Notes -->

    <!-- Page 403 - Accès Refusé -->
    <div style="display: none;">
        <div class="topbar">
            <div class="topbar-content">
                <div class="logo">SGN - Système de Gestion des Notes</div>
                <div class="user-menu">
                    <span class="username">Ahmed Bennani</span>
                    <button href="logout.php" class="logout-btn">Déconnexion</button>
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
                    <button class="btn btn-primary">Retour à l'accueil</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>