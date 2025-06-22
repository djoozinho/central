<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

require 'db_connect.php';

// Lógica de Salvar (Adicionar/Editar)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_radio'])) {
    $name = trim($_POST['name']);
    $logoUrl = trim($_POST['logoUrl']);
    $streamUrls = trim($_POST['streamUrls']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $id = $_POST['id'];

    if (empty($id)) { // Inserir novo
        $stmt = $conn->prepare("INSERT INTO radios (name, logoUrl, streamUrls, is_featured) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $logoUrl, $streamUrls, $is_featured);
    } else { // Atualizar existente
        $stmt = $conn->prepare("UPDATE radios SET name = ?, logoUrl = ?, streamUrls = ?, is_featured = ? WHERE id = ?");
        $stmt->bind_param("sssii", $name, $logoUrl, $streamUrls, $is_featured, $id);
    }
    $stmt->execute();
    $stmt->close();
    header("location: admin.php?status=saved");
    exit;
}

// Lógica de Deletar
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM radios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("location: admin.php?status=deleted");
    exit;
}

// Buscar dados para edição
$radio_to_edit = ['id' => '', 'name' => '', 'logoUrl' => '', 'streamUrls' => '', 'is_featured' => 0];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT id, name, logoUrl, streamUrls, is_featured FROM radios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $radio_to_edit = $result->fetch_assoc();
    }
    $stmt->close();
}

// Buscar todas as rádios para a lista
$radios = $conn->query("SELECT id, name, logoUrl, is_featured FROM radios ORDER BY is_featured DESC, name ASC");

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
        <h1>Painel Rádio Central</h1>
        <a href="logout.php" class="btn btn-secondary"><i class="ph ph-sign-out"></i>Sair</a>
    </header>

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
                <div class="form-group full-width">
                    <label for="streamUrls">URLs do Stream (se mais de uma, separe por vírgula):</label>
                    <textarea id="streamUrls" name="streamUrls" rows="3" required><?php echo htmlspecialchars($radio_to_edit['streamUrls']); ?></textarea>
                </div>
                <div class="form-group-checkbox full-width">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" <?php echo ($radio_to_edit['is_featured'] == 1) ? 'checked' : ''; ?>>
                    <label for="is_featured">Marcar como Destaque</label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="save_radio" class="btn btn-primary"><i class="ph ph-floppy-disk"></i>Salvar Rádio</button>
                <?php if (!empty($radio_to_edit['id'])): ?>
                    <a href="admin.php" class="btn btn-secondary" style="margin-left:10px;">Cancelar Edição</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <h2>Rádios Cadastradas</h2>
    <table>
        <thead>
            <tr>
                <th>Destaque</th>
                <th>Logo</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($radios && $radios->num_rows > 0): while ($row = $radios->fetch_assoc()): ?>
            <tr>
                <td>
                    <i class="ph-fill ph-star status-icon <?php echo ($row['is_featured'] == 1) ? 'featured' : ''; ?>"></i>
                </td>
                <td>
                    <img src="<?php echo htmlspecialchars($row['logoUrl']); ?>" alt="logo" class="logo-preview">
                </td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td class="actions-cell">
                    <a href="admin.php?edit=<?php echo $row['id']; ?>" title="Editar"><i class="ph ph-pencil-simple"></i></a>
                    <a href="admin.php?delete=<?php echo $row['id']; ?>" class="delete" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta rádio?');"><i class="ph ph-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="4" style="text-align:center; padding: 20px;">Nenhuma rádio cadastrada ainda.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>