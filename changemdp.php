<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Connexion à la base de données
try {
    $bdd = new PDO('mysql:host=localhost;port=3306;dbname=compte_utilisateur', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->exec("set names utf8");
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Traitement du formulaire de changement de mot de passe
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $ancien_mdp = $_POST['ancien_mdp'];
    $nouveau_mdp = $_POST['nouveau_mdp'];
    $confirmer_mdp = $_POST['confirmer_mdp'];

    // Valider les champs
    if (empty($ancien_mdp) || empty($nouveau_mdp) || empty($confirmer_mdp)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($nouveau_mdp !== $confirmer_mdp) {
        $error = "Les nouveaux mots de passe ne correspondent pas.";
    } else {
        // Récupérer l'ID de l'utilisateur connecté
        $id = $_SESSION['user_id'];

        // Récupérer le mot de passe actuel de l'utilisateur
        $stmt = $bdd->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifier si l'ancien mot de passe est correct
        if ($user && password_verify($ancien_mdp, $user['password'])) {
            // Hasher le nouveau mot de passe
            $nouveau_mdp_hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);

            // Mettre à jour le mot de passe dans la base de données
            $stmt = $bdd->prepare("UPDATE users SET password = :nouveau_mdp WHERE id = :id");
            $stmt->bindParam(':nouveau_mdp', $nouveau_mdp_hash, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $success = "Votre mot de passe a été mis à jour avec succès.";
        } else {
            $error = "L'ancien mot de passe est incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le mot de passe</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style1.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            margin-bottom: 10px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Changer le mot de passe</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="ancien_mdp">Ancien mot de passe :</label>
            <input type="password" id="ancien_mdp" name="ancien_mdp" required>

            <label for="nouveau_mdp">Nouveau mot de passe :</label>
            <input type="password" id="nouveau_mdp" name="nouveau_mdp" required>

            <label for="confirmer_mdp">Confirmer le nouveau mot de passe :</label>
            <input type="password" id="confirmer_mdp" name="confirmer_mdp" required>

            <button type="submit">Changer le mot de passe</button>
        </form>

        <br>
    
        <a class="btn btn-primary" href="dashboard.php" role="button">Retour<i class="bi bi-arrow-left"></i></a>
    </div>
</body>

</html>