<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

require 'db_connect.php';

// --- LÓGICA DE GERENCIAMENTO DE RÁDIOS (COM NOVOS CAMPOS) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_radio'])) {
    $name = trim($_POST['name']);
    $logoUrl = trim($_POST['logoUrl']);
    $streamUrls = trim($_POST['streamUrls']);
    $genre = trim($_POST['genre']); // Novo campo
    $artists = trim($_POST['artists']); // Novo campo
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $id = $_POST['id'];

    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO radios (name, logoUrl, streamUrls, genre, artists, is_featured) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $name, $logoUrl, $streamUrls, $genre, $artists, $is_featured);
    } else {
        $stmt = $conn->prepare("UPDATE radios SET name = ?, logoUrl = ?, streamUrls = ?, genre = ?, artists = ?, is_featured = ? WHERE id = ?");
        $stmt->bind_param("sssssii", $name, $logoUrl, $streamUrls, $genre, $artists, $is_featured, $id);
    }
    $stmt->execute();
    header("location: admin.php?tab=radios&status=saved");
    exit;
}

// ... (Lógica de delete e outras abas permanecem as mesmas) ...

// Buscar dados para edição (com novos campos)
$radio_to_edit = ['id' => '', 'name' => '', 'logoUrl' => '', 'streamUrls' => '', 'genre' => '', 'artists' => '', 'is_featured' => 0];
if (isset($_GET['edit_radio'])) {
    $id = $_GET['edit_radio'];
    $stmt = $conn->prepare("SELECT * FROM radios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) $radio_to_edit = $result->fetch_assoc();
}

// Buscar todas as rádios para a lista (com novos campos)
$radios = $conn->query("SELECT id, name, logoUrl, genre, is_featured FROM radios ORDER BY is_featured DESC, name ASC");
$active_tab = 'radios'; // Supondo que só temos a aba de rádios por enquanto
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração - Rádio Central</title>
    <link rel="stylesheet" href="admin-style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container">
    <header class="admin-header">
        <div class="header-title">
            <img src="assets/logo.png" alt="Logo" class="admin-logo">
            <h1>Painel Rádio Central</h1>
        </div>
        <a href="logout.php" class="btn btn-secondary"><i class="ph ph-sign-out"></i>Sair</a>
    </header>

    <div class="tab-content active" id="tab-radios">
        <div class="form-container">
            <h2><?php echo empty($radio_to_edit['id']) ? 'Adicionar Nova Rádio' : 'Editar Rádio: ' . htmlspecialchars($radio_to_edit['name']); ?></h2>
            <form action="admin.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($radio_to_edit['id']); ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Nome da Rádio:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($radio_to_edit['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="logoUrl">URL do Logo:</label>
                        <input type="url" id="logoUrl" name="logoUrl" value="<?php echo htmlspecialchars($radio_to_edit['logoUrl']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="genre">Gênero Principal:</label>
                        <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($radio_to_edit['genre']); ?>" placeholder="Ex: Pop, Rock, Sertanejo">
                    </div>
                    <div class="form-group">
                         <label for="is_featured_checkbox">Destaque:</label>
                        <div class="form-group-checkbox">
                           <input type="checkbox" id="is_featured_checkbox" name="is_featured" value="1" <?php echo ($radio_to_edit['is_featured'] == 1) ? 'checked' : ''; ?>>
                           <label for="is_featured_checkbox">Marcar como Destaque na página inicial</label>
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label for="streamUrls">URL do Stream:</label>
                        <input type="text" id="streamUrls" name="streamUrls" required value="<?php echo htmlspecialchars($radio_to_edit['streamUrls']); ?>">
                    </div>
                    <div class="form-group full-width">
                        <label for="artists">Artistas Principais (separados por vírgula):</label>
                        <textarea id="artists" name="artists" rows="3"><?php echo htmlspecialchars($radio_to_edit['artists']); ?></textarea>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" name="save_radio" class="btn btn-primary"><i class="ph ph-floppy-disk"></i>Salvar Rádio</button>
                    <?php if (!empty($radio_to_edit['id'])): ?>
                        <a href="admin.php" class="btn btn-secondary">Cancelar Edição</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <h2>Rádios Cadastradas</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Destaque</th>
                        <th>Logo</th>
                        <th>Nome</th>
                        <th>Gênero</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($radios && $radios->num_rows > 0): while ($row = $radios->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <i class="ph-fill ph-star status-icon <?php echo ($row['is_featured'] == 1) ? 'featured' : ''; ?>"></i>
                        </td>
                        <td><img src="<?php echo htmlspecialchars($row['logoUrl']); ?>" alt="logo" class="logo-preview"></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><span class="genre-tag"><?php echo htmlspecialchars($row['genre']); ?></span></td>
                        <td class="actions-cell">
                            <a href="?edit_radio=<?php echo $row['id']; ?>" title="Editar"><i class="ph ph-pencil-simple"></i></a>
                            <a href="?delete_radio=<?php echo $row['id']; ?>" class="delete" title="Excluir" onclick="return confirm('Tem certeza?');"><i class="ph ph-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" style="text-align:center; padding: 20px;">Nenhuma rádio cadastrada ainda.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>