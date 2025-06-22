<?php
header('Content-Type: application/json');
require 'admin/db_connect.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID da rádio não fornecido.']);
    exit;
}

$radio_id = (int)$_GET['id'];

// 1. Obter detalhes da rádio
$stmt = $conn->prepare("SELECT * FROM radios WHERE id = ?");
$stmt->bind_param("i", $radio_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Rádio não encontrada.']);
    exit;
}
$radio = $result->fetch_assoc();
$radio['streamUrls'] = explode(',', trim($radio['streamUrls']));

// 2. Calcular a avaliação média
$stmt = $conn->prepare("SELECT AVG(rating_value) as avg_rating, COUNT(*) as total_ratings FROM ratings WHERE radio_id = ?");
$stmt->bind_param("i", $radio_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$radio['avg_rating'] = $result['avg_rating'] ? round($result['avg_rating'], 1) : 0;
$radio['total_ratings'] = (int)$result['total_ratings'];

// 3. Obter comentários
$stmt = $conn->prepare("SELECT username, comment_text, created_at FROM comments WHERE radio_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $radio_id);
$stmt->execute();
$result = $stmt->get_result();
$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}
$radio['comments'] = $comments;

echo json_encode($radio);
$stmt->close();
$conn->close();
?>