<?php
header('Content-Type: application/json');
require 'admin/db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['radio_id']) || !isset($data['action'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos.']);
    exit;
}

$radio_id = (int)$data['radio_id'];
$action = $data['action'];

if ($action === 'comment') {
    if (empty($data['username']) || empty($data['comment_text'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Nome e comentário são obrigatórios.']);
        exit;
    }
    $username = htmlspecialchars($data['username']);
    $comment_text = htmlspecialchars($data['comment_text']);

    $stmt = $conn->prepare("INSERT INTO comments (radio_id, username, comment_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $radio_id, $username, $comment_text);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Comentário adicionado!']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Falha ao salvar comentário.']);
    }
    $stmt->close();

} elseif ($action === 'rating') {
    if (!isset($data['rating_value']) || $data['rating_value'] < 1 || $data['rating_value'] > 5) {
        http_response_code(400);
        echo json_encode(['error' => 'Avaliação inválida.']);
        exit;
    }
    $rating_value = (int)$data['rating_value'];

    $stmt = $conn->prepare("INSERT INTO ratings (radio_id, rating_value) VALUES (?, ?)");
    $stmt->bind_param("ii", $radio_id, $rating_value);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Obrigado por sua avaliação!']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Falha ao salvar avaliação.']);
    }
    $stmt->close();
}

$conn->close();
?>