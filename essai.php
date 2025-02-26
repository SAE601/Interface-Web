<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Informations de connexion à la base de données
$host = 'localhost';
$dbname = 'optiplant_fillupdate';
$username = 'root';
$password = '';

// Connexion à la base de données
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Requête SQL pour récupérer les données
$sql = "SELECT * FROM bacs";
$stmt = $pdo->prepare($sql);
$stmt->execute();
// Récupération des résultats
$bacs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL pour filtrer les données
$sql = "SELECT * FROM `irrigation` ORDER BY dateHeure DESC;";
$stmt = $pdo->prepare($sql);
$stmt->execute();
// Récupération des résultats
$irrigations = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html>
<head>
    <title>Information sur le bac</title>
    <meta charset='utf-8'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="/css/bootstrap.css" rel="stylesheet">
</head>
<body>


<div class="container py-4">
    <h4 class="text-center">Données d'Irrigation</h4>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Date et Heure</th>
            <th>Recette</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($irrigations)): ?>
            <?php foreach ($irrigations as $irrigation): ?>
                <tr>
                    <?php if ($irrigation['idBac'] == $_GET['bac']) { ?>
                        <td><?php echo htmlspecialchars($irrigation['dateHeure']); ?></td>
                        <td><?php echo htmlspecialchars($irrigation['idRecette']); ?></td>
                    <?php } ?>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2" class="text-center">Aucune donnée trouvée</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>


<script src="js/index.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>

