<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['radio_name']) || empty($data['stream_url'])) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'Nome e URL do Stream são obrigatórios.']); exit; }
$radio_name = htmlspecialchars($data['radio_name']);
$stream_url = htmlspecialchars($data['stream_url']);
$radio_website = isset($data['radio_website']) ? htmlspecialchars($data['radio_website']) : 'N/A';
$user_notes = isset($data['user_notes']) ? htmlspecialchars($data['user_notes']) : 'N/A';
$log_message = "NOVA SUGESTÃO: " . date('Y-m-d H:i:s') . " | Nome: $radio_name | Stream: $stream_url | Site: $radio_website | Notas: $user_notes\n";
if (file_put_contents('sugestoes.txt', $log_message, FILE_APPEND | LOCK_EX)) {
    echo json_encode(['success' => true, 'message' => 'Obrigado! Sua sugestão foi recebida.']);
} else { http_response_code(500); echo json_encode(['success' => false, 'message' => 'Erro ao salvar a sugestão.']); }
?>