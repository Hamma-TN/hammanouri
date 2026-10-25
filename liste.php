<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

$message = '';
$erreur = '';
$resultatId = isset($_GET['idbij']) ? strtoupper(trim((string) $_GET['idbij'])) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idbij = strtoupper(trim((string) ($_POST['idbij'] ?? '')));
    $description = trim((string) ($_POST['description'] ?? ''));
    $prixvent = (string) ($_POST['prixvent'] ?? '');
    $poids = (string) ($_POST['poids'] ?? '');
    $composition = $_POST['composition'] ?? [];

    if (!is_array($composition)) {
        $composition = [];
    }
    $composition = array_values(array_unique($composition));
    $metauxPermis = ['A', 'O', 'P'];

    if (!preg_match('/^[A-Z]{2}[0-9]{3}$/', $idbij)) {
        $erreur = 'Identifiant invalide : deux lettres suivies de trois chiffres sont nécessaires.';
    } elseif ($description === '' || mb_strlen($description) > 100) {
        $erreur = 'La description est obligatoire et ne doit pas dépasser 100 caractères.';
    } elseif (!ctype_digit($prixvent) || (int) $prixvent < 100) {
        $erreur = 'Le prix doit être un nombre entier supérieur ou égal à 100.';
    } elseif (!ctype_digit($poids) || (int) $poids < 1) {
        $erreur = 'Le poids doit être un nombre entier supérieur ou égal à 1.';
    } elseif ($composition === [] || array_diff($composition, $metauxPermis) !== []) {
        $erreur = 'Choisissez au moins une composition valide.';
    } else {
        try {
            $pdo->beginTransaction();
            $insertBijou = $pdo->prepare(
                'INSERT INTO bijou (idbij, description, prixvent, poids) VALUES (?, ?, ?, ?)'
            );
            $insertBijou->execute([$idbij, $description, (int) $prixvent, (int) $poids]);

            $insertComposition = $pdo->prepare(
                'INSERT INTO composotion (idbij, idmet) VALUES (?, ?)'
            );
            foreach ($composition as $idmet) {
                $insertComposition->execute([$idbij, $idmet]);
            }
            $pdo->commit();
            $message = 'Le bijou a été enregistré. Vous pouvez maintenant l’évaluer.';
            $resultatId = $idbij;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $erreur = $exception instanceof PDOException && $exception->getCode() === '23000'
                ? 'Cet identifiant existe déjà, ou une donnée liée est invalide.'
                : 'Enregistrement impossible. Vérifiez la base de données et réessayez.';
        }
    }
}

$bijoux = $pdo->query(
    "SELECT b.idbij, b.description, b.prixvent, b.poids,
        (SELECT GROUP_CONCAT(m.nomMet ORDER BY m.nomMet SEPARATOR ', ')
         FROM composotion c
         JOIN metal m ON m.idmet = c.idmet
         WHERE c.idbij = b.idbij) AS composition,
        (SELECT GROUP_CONCAT(
            CONCAT(e.nomExp, ' — finition ', ev.finition, '/5, originalité ',
                IF(ev.originalite = 'O', 'oui', 'non'), ', ', ev.commentaire)
            ORDER BY ev.dateEV DESC SEPARATOR ' | ')
         FROM evaluation ev
         JOIN expert e ON e.idExp = ev.idExp
         WHERE ev.idbij = b.idbij) AS evaluations
     FROM bijou b
     ORDER BY b.idbij"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des bijoux - Aurythia</title>
    <link rel="stylesheet" href="mesStyle1.css">
    <style>
        body { padding: 12px; text-transform: none; }
        fieldset { width: auto; height: auto; left: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid blueviolet; padding: 8px; text-align: left; }
        .message { color: lightgreen; }
        .erreur { color: #ff8a8a; }
        .action { display: inline-block; margin: 12px 0; color: azure; }
        .resultat { border: 2px solid lightgreen; border-radius: 12px; margin: 12px 0; padding: 12px; }
    </style>
</head>
<body>
    <fieldset>
        <legend>Liste des bijoux Aurythia</legend>
        <?php if ($message !== ''): ?><p class="message"><?= h($message) ?></p><?php endif; ?>
        <?php if ($erreur !== ''): ?><p class="erreur"><?= h($erreur) ?></p><?php endif; ?>
        <?php if (isset($_GET['evaluation']) && $_GET['evaluation'] === 'ok'): ?>
            <p class="message">L’évaluation a été enregistrée.</p>
        <?php endif; ?>
        <?php foreach ($bijoux as $bijou): ?>
            <?php if ($resultatId !== '' && $bijou['idbij'] === $resultatId): ?>
                <section class="resultat">
                    <h2>Résultat enregistré dans la base de données</h2>
                    <p><strong>Identifiant :</strong> <?= h((string) $bijou['idbij']) ?></p>
                    <p><strong>Description :</strong> <?= h((string) $bijou['description']) ?></p>
                    <p><strong>Prix :</strong> <?= h((string) $bijou['prixvent']) ?> dinars</p>
                    <p><strong>Poids :</strong> <?= h((string) $bijou['poids']) ?> g</p>
                    <p><strong>Composition :</strong> <?= h((string) ($bijou['composition'] ?? '')) ?></p>
                    <p><strong>Évaluation :</strong> <?= h((string) ($bijou['evaluations'] ?? 'Pas encore évalué')) ?></p>
                </section>
            <?php endif; ?>
        <?php endforeach; ?>
        <form action="evaluation.html" target="zone">
            <button class="action" type="submit">Évaluer un bijou</button>
        </form>
        <?php if ($bijoux === []): ?>
            <p>Aucun bijou n’a encore été enregistré.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Identifiant</th>
                        <th>Description</th>
                        <th>Prix (dinars)</th>
                        <th>Poids (g)</th>
                        <th>Composition</th>
                        <th>Évaluations</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($bijoux as $bijou): ?>
                    <tr>
                        <td><?= h((string) $bijou['idbij']) ?></td>
                        <td><?= h((string) $bijou['description']) ?></td>
                        <td><?= h((string) $bijou['prixvent']) ?></td>
                        <td><?= h((string) $bijou['poids']) ?></td>
                        <td><?= h((string) ($bijou['composition'] ?? '')) ?></td>
                        <td><?= h((string) ($bijou['evaluations'] ?? 'Pas encore évalué')) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </fieldset>
</body>
</html>