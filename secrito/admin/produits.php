<?php
require_once '../config/config.php';
requireAdmin();
$pdo = getDB();
$msg = '';
$msg_type = 'success';

// DELETE
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM produits WHERE id=:id")->execute([':id'=>(int)$_GET['delete']]);
    header('Location: produits.php?msg=Produit+supprimé.'); exit;
}

// TOGGLE actif
if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE produits SET actif = 1 - actif WHERE id=:id")->execute([':id'=>(int)$_GET['toggle']]);
    header('Location: produits.php'); exit;
}

// CREATE / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = (int)($_POST['id'] ?? 0);
    $nom   = trim($_POST['nom'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $prix  = (float)str_replace(',', '.', $_POST['prix'] ?? 0);
    $cat   = $_POST['categorie'] ?? '';
    $actif = isset($_POST['actif']) ? 1 : 0;
    $cats  = ['brunch','sucree','sale','fresh'];

    if (empty($nom) || $prix <= 0 || !in_array($cat, $cats)) {
        $msg = 'Données invalides (nom, prix et catégorie requis).';
        $msg_type = 'error';
    } else {

        // ── Gestion upload image ──────────────────────────────
        $image = trim($_POST['image_actuelle'] ?? '');  // garder l'ancienne par défaut

        if (!empty($_FILES['image_upload']['name'])) {
            $allowed_ext  = ['jpg','jpeg','png','webp','gif'];
            $allowed_mime = ['image/jpeg','image/png','image/webp','image/gif'];

            $original_name = $_FILES['image_upload']['name'];
            $tmp_path      = $_FILES['image_upload']['tmp_name'];
            $file_size     = $_FILES['image_upload']['size'];
            $file_ext      = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $file_mime     = mime_content_type($tmp_path);

            if (!in_array($file_ext, $allowed_ext) || !in_array($file_mime, $allowed_mime)) {
                $msg = 'Format image invalide. Utilisez JPG, PNG ou WEBP.';
                $msg_type = 'error';
            } elseif ($file_size > 5 * 1024 * 1024) {
                $msg = 'Image trop lourde (max 5 Mo).';
                $msg_type = 'error';
            } else {
                // Nom unique pour éviter les conflits
                $new_name = uniqid('prod_') . '.' . $file_ext;
                $dest     = __DIR__ . '/../images/' . $new_name;

                if (move_uploaded_file($tmp_path, $dest)) {
                    $image = $new_name;
                } else {
                    $msg = 'Erreur lors de l\'upload. Vérifiez les droits du dossier images/.';
                    $msg_type = 'error';
                }
            }
        }

        if ($msg_type !== 'error') {
            if ($id) {
                $sql = "UPDATE produits SET nom=:n,description=:d,prix=:p,categorie=:c,image=:i,actif=:a WHERE id=:id";
                $pdo->prepare($sql)->execute([':n'=>$nom,':d'=>$desc,':p'=>$prix,':c'=>$cat,':i'=>$image,':a'=>$actif,':id'=>$id]);
                $msg = 'Produit modifié avec succès.';
            } else {
                $sql = "INSERT INTO produits (nom,description,prix,categorie,image,actif) VALUES (:n,:d,:p,:c,:i,:a)";
                $pdo->prepare($sql)->execute([':n'=>$nom,':d'=>$desc,':p'=>$prix,':c'=>$cat,':i'=>$image,':a'=>$actif]);
                $msg = 'Produit ajouté avec succès.';
            }
            header('Location: produits.php?msg=' . urlencode($msg)); exit;
        }
    }
}

if (isset($_GET['msg'])) $msg = clean($_GET['msg']);

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id=:id");
    $stmt->execute([':id'=>(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$produits     = $pdo->query("SELECT * FROM produits ORDER BY categorie, nom")->fetchAll();
$cats_labels  = ['brunch'=>'Brunch','sucree'=>'Sucrée','sale'=>'Salé','fresh'=>'Fresh Bar'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Produits — Admin Secrito</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        /* ── Zone upload ── */
        .upload-zone {
            border: 2px dashed var(--border-color);
            border-radius: 1.2rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: 0.2s ease;
            background: rgba(38,71,85,0.04);
            position: relative;
        }
        .upload-zone:hover {
            border-color: var(--color-primary);
            background: rgba(38,71,85,0.08);
        }
        .upload-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        .upload-zone .upload-icon {
            font-size: 3rem;
            color: var(--color-accent);
            margin-bottom: 0.8rem;
        }
        .upload-zone p {
            font-size: 1.4rem;
            color: var(--text-secondary);
            margin: 0;
        }
        .upload-zone span {
            font-size: 1.2rem;
            color: var(--text-secondary);
            opacity: 0.7;
        }
        /* Prévisualisation */
        #preview-container {
            margin-top: 1.2rem;
            display: none;
            align-items: center;
            gap: 1.2rem;
        }
        #preview-container img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 0.8rem;
            border: 2px solid var(--color-accent);
        }
        #preview-container span {
            font-size: 1.3rem;
            color: var(--text-main);
        }
        #preview-container button {
            background: none;
            border: none;
            color: #ef4444;
            font-size: 1.6rem;
            cursor: pointer;
            padding: 0.3rem;
        }
    </style>
</head>
<body class="admin-body">
<?php include 'sidebar.php'; ?>
<main class="admin-main">
    <div class="admin-topbar">
        <h1>Produits</h1>
        <a href="produits.php" class="btn-sm">+ Nouveau</a>
    </div>

    <?php if ($msg): ?>
    <div style="background:<?php echo $msg_type==='error'?'#fee2e2':'#d1fae5'; ?>;
                color:<?php echo $msg_type==='error'?'#991b1b':'#065f46'; ?>;
                padding:1.2rem 2rem;border-radius:1rem;margin-bottom:2rem;font-size:1.4rem;">
        <?php echo clean($msg); ?>
    </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:3rem;align-items:start;">

        <!-- ══ FORMULAIRE ══ -->
        <div class="admin-card">
            <h2 style="margin-bottom:2rem;"><?php echo $edit ? 'Modifier le produit' : 'Ajouter un produit'; ?></h2>

            <!-- enctype obligatoire pour l'upload -->
            <form method="POST" enctype="multipart/form-data">
                <?php if ($edit): ?>
                <input type="hidden" name="id" value="<?php echo $edit['id']; ?>">
                <!-- Garde l'ancienne image si aucun nouvel upload -->
                <input type="hidden" name="image_actuelle" value="<?php echo clean($edit['image']??''); ?>">
                <?php endif; ?>

                <div class="form-row">
                    <label>Nom *</label>
                    <input type="text" name="nom" required value="<?php echo clean($edit['nom']??''); ?>">
                </div>

                <div class="form-row">
                    <label>Description</label>
                    <textarea name="description" rows="3"><?php echo clean($edit['description']??''); ?></textarea>
                </div>

                <div class="form-row">
                    <label>Prix (DT) *</label>
                    <input type="number" name="prix" step="0.01" min="0" required value="<?php echo $edit['prix']??''; ?>">
                </div>

                <div class="form-row">
                    <label>Catégorie *</label>
                    <select name="categorie">
                        <?php foreach ($cats_labels as $v => $l): ?>
                        <option value="<?php echo $v; ?>" <?php echo ($edit['categorie']??'')===$v?'selected':''; ?>>
                            <?php echo $l; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ── UPLOAD IMAGE ── -->
                <div class="form-row">
                    <label>Photo du produit</label>

                    <?php if ($edit && !empty($edit['image'])): ?>
                    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
                        <img src="../images/<?php echo clean($edit['image']); ?>"
                             style="width:60px;height:60px;object-fit:cover;border-radius:0.8rem;border:2px solid var(--color-accent);"
                             onerror="this.style.display='none'">
                        <span style="font-size:1.3rem;color:var(--text-secondary);">
                            Image actuelle — choisir un nouveau fichier pour remplacer
                        </span>
                    </div>
                    <?php endif; ?>

                    <div class="upload-zone" id="uploadZone">
                        <input type="file" name="image_upload" id="imageInput"
                               accept="image/jpeg,image/png,image/webp,image/gif">
                        <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <p>Cliquez ou glissez une photo ici</p>
                        <span>JPG, PNG, WEBP — max 5 Mo</span>
                    </div>

                    <!-- Prévisualisation -->
                    <div id="preview-container">
                        <img id="previewImg" src="" alt="Aperçu">
                        <span id="previewName"></span>
                        <button type="button" id="removeImage" title="Supprimer">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>

                <div class="form-row" style="flex-direction:row;align-items:center;gap:1rem;">
                    <input type="checkbox" name="actif" id="actif" value="1"
                           <?php echo ($edit['actif']??1)?'checked':''; ?> style="width:auto;">
                    <label for="actif" style="margin-bottom:0;">Visible sur le menu</label>
                </div>

                <button type="submit" style="width:100%;margin-top:1.5rem;padding:1.2rem;
                        background:var(--color-primary);color:#fff;border:none;
                        border-radius:2rem;font-size:1.5rem;cursor:pointer;">
                    <?php echo $edit ? 'Modifier' : 'Ajouter'; ?>
                </button>

                <?php if ($edit): ?>
                <a href="produits.php" style="display:block;text-align:center;
                   margin-top:1rem;font-size:1.3rem;color:var(--text-secondary);">Annuler</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- ══ LISTE ══ -->
        <div class="admin-card">
            <h2 style="margin-bottom:2rem;">Liste des produits (<?php echo count($produits); ?>)</h2>
            <table class="admin-table">
                <thead>
                    <tr><th>Photo</th><th>Nom</th><th>Cat.</th><th>Prix</th><th>Actif</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($produits as $p): ?>
                <tr style="<?php echo !$p['actif']?'opacity:0.5':''; ?>">
                    <td>
                        <?php if (!empty($p['image'])): ?>
                        <img src="../images/<?php echo clean($p['image']); ?>"
                             style="width:45px;height:45px;object-fit:cover;border-radius:0.6rem;"
                             onerror="this.style.display='none'">
                        <?php else: ?>
                        <span style="font-size:2rem;">🍽️</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo clean($p['nom']); ?></td>
                    <td><?php echo $cats_labels[$p['categorie']] ?? $p['categorie']; ?></td>
                    <td><?php echo number_format($p['prix'],2,',',' '); ?> DT</td>
                    <td>
                        <a href="?toggle=<?php echo $p['id']; ?>" title="<?php echo $p['actif']?'Désactiver':'Activer'; ?>">
                            <i class="fas fa-<?php echo $p['actif']?'eye':'eye-slash'; ?>"
                               style="color:<?php echo $p['actif']?'#10b981':'#888'; ?>;font-size:1.6rem;"></i>
                        </a>
                    </td>
                    <td style="display:flex;gap:0.8rem;">
                        <a href="?edit=<?php echo $p['id']; ?>" class="btn-sm">✏️</a>
                        <a href="?delete=<?php echo $p['id']; ?>" class="btn-sm" style="background:#ef4444;"
                           onclick="return confirm('Supprimer ce produit ?')">🗑️</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<script>
// ── Prévisualisation image avant upload ──
const imageInput       = document.getElementById('imageInput');
const previewContainer = document.getElementById('preview-container');
const previewImg       = document.getElementById('previewImg');
const previewName      = document.getElementById('previewName');
const removeBtn        = document.getElementById('removeImage');
const uploadZone       = document.getElementById('uploadZone');

imageInput.addEventListener('change', () => {
    const file = imageInput.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        previewImg.src             = e.target.result;
        previewName.textContent    = file.name + ' (' + (file.size / 1024).toFixed(0) + ' Ko)';
        previewContainer.style.display = 'flex';
        uploadZone.style.borderColor   = 'var(--color-primary)';
    };
    reader.readAsDataURL(file);
});

// Supprimer la sélection
removeBtn.addEventListener('click', () => {
    imageInput.value               = '';
    previewContainer.style.display = 'none';
    uploadZone.style.borderColor   = 'var(--border-color)';
});

// Drag & drop visuel
uploadZone.addEventListener('dragover',  () => uploadZone.style.borderColor = 'var(--color-primary)');
uploadZone.addEventListener('dragleave', () => uploadZone.style.borderColor = 'var(--border-color)');
</script>
</body>
</html>