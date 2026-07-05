<?php
require_once 'config/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Méthode non autorisée.']); exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['items']) || !is_array($data['items'])) {
    echo json_encode(['success'=>false,'message'=>'Panier vide.']); exit;
}

$clientId  = $_SESSION['client_id'] ?? null;
$nomClient = htmlspecialchars(trim($data['nom'] ?? 'Client'), ENT_QUOTES, 'UTF-8');
$telephone = htmlspecialchars(trim($data['telephone'] ?? ''), ENT_QUOTES, 'UTF-8');
$adresse   = htmlspecialchars(trim($data['adresse'] ?? ''), ENT_QUOTES, 'UTF-8');

if ($clientId) {
    $pdo  = getDB();
    $info = $pdo->prepare("SELECT * FROM clients WHERE id=:id");
    $info->execute([':id'=>$clientId]);
    $info = $info->fetch();
    if ($info) {
        $nomClient = $info['prenom'].' '.$info['nom'];
        $telephone = $telephone ?: $info['telephone'];
        $adresse   = $adresse   ?: $info['adresse'];
    }
}

$total = 0;
foreach ($data['items'] as $item) {
    $total += (float)($item['prix']??0) * max(1,(int)($item['qty']??1));
}

try {
    $pdo = getDB();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO commandes (client_id,nom_client,telephone,adresse,total,livraison) VALUES (:cid,:nom,:tel,:adr,:total,:liv)");
    $stmt->execute([':cid'=>$clientId,':nom'=>$nomClient,':tel'=>$telephone,':adr'=>$adresse,':total'=>$total,':liv'=>FRAIS_LIVRAISON]);
    $commandeId = $pdo->lastInsertId();

    $stmtItem = $pdo->prepare("INSERT INTO commande_items (commande_id,produit_id,nom_produit,prix_unit,quantite) VALUES (:cid,:pid,:nom,:prix,:qty)");
    foreach ($data['items'] as $item) {
        $stmtItem->execute([
            ':cid'  => $commandeId,
            ':pid'  => isset($item['id']) ? (int)$item['id'] : null,
            ':nom'  => htmlspecialchars(trim($item['name']??''), ENT_QUOTES,'UTF-8'),
            ':prix' => (float)($item['prix']??0),
            ':qty'  => max(1,(int)($item['qty']??1)),
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success'     => true,
        'message'     => 'Commande enregistrée !',
        'commande_id' => $commandeId,
        'total'       => $total + FRAIS_LIVRAISON,
    ]);

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    error_log('Secrito checkout: '.$e->getMessage());
    echo json_encode(['success'=>false,'message'=>'Erreur serveur. Réessayez.']);
}
exit;
