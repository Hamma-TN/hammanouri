<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

$erreur = '';
$idbij = strtoupper(trim((string) ($_POST['idbij'] ?? '')));
$idExp = trim((string) ($_POST['idExp'] ?? ''));
$finition = (string) ($_POST['finition'] ?? '');
$originalite = (string) ($_POST['originalite'] ?? '');
$commentaire = trim((string) ($_POST['commentaire'] ?? ''));

if (!preg_match('/^[A-Z]{2}[0-9]{3}$/', $idbij)) {
    $erreur = 'Identifiant de bijou invalide.';
} elseif (!in_array($idExp, ['A22', 'A23', 'A24'], true)) {
    $erreur = 'Choisissez un expert valide.';
} elseif (!ctype_digit($finition) || (int) $finition < 1 || (int) $finition > 5) {
    $erreur = 'La finition doit être une note entière entre 1 et 5.';
} elseif (!in_array($originalite, ['O', 'N'], true)) {
    $erreur = 'Indiquez si le bijou est original.';
} elseif ($commentaire === '' || mb_strlen($commentaire) > 200) {
    $erreur = 'Le commentaire est obligatoire et ne doit pas dépasser 200 caractères.';
} elseif (!preg_match('/^[a-z]+ [a-z]+$/i', $commentaire)) {
    $erreur = 'Le commentaire doit contenir exactement deux mots alphabétiques.';
}

if ($erreur === '') {
    try {
        $checkBijou = $pdo->prepare('SELECT COUNT(*) FROM bijou WHERE idbij = ?');
        $checkBijou->execute([$idbij]);
        if ((int) $checkBijou->fetchColumn() === 0) {
            $erreur = 'Ce bijou n’existe pas. Enregistrez-le avant de l’évaluer.';
        } else {
            $save = $pdo->prepare(
                'INSERT INTO evaluation (idExp, idbij, finition, originalite, commentaire, dateEV)
                 VALUES (?, ?, ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE
                    finition = VALUES(finition),
                    originalite = VALUES(originalite),
                    commentaire = VALUES(commentaire),
                    dateEV = NOW()'
            );
            $save->execute([$idExp, $idbij, (int) $finition, $originalite, $commentaire]);
            header('Location: liste.php?evaluation=ok&idbij=' . urlencode($idbij));
            exit;
        }
    } catch (Throwable $exception) {
        $erreur = 'Enregistrement de l’évaluation impossible. Vérifiez la base de données et réessayez.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur d’évaluation - Aurythia</title>
    <link rel="stylesheet" href="mesStyle1.css">
</head>
<body>
    <fieldset>
        <legend>Évaluation non enregistrée</legend>
        <p><?= h($erreur) ?></p>
        <a href="evaluation.html" style="color: azure;">Retourner au formulaire d’évaluation</a>
        <br>
        <a href="liste.php" target="zone" style="color: azure;">Retourner à la liste des bijoux</a>
    </fieldset>
</body>
</html>
