<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

function getStreamTitle($url) {
    $context = stream_context_create(['http' => ['timeout' => 2, 'header' => "Icy-MetaData: 1\r\n"]]);
    $stream = @fopen($url, 'r', false, $context);

    if (!$stream) {
        return null;
    }

    $meta_data = stream_get_meta_data($stream);
    $headers = $meta_data['wrapper_data'];
    $meta_interval = 0;

    foreach ($headers as $header) {
        if (stripos($header, 'icy-metaint') !== false) {
            $meta_interval = (int)str_replace('icy-metaint:', '', strtolower($header));
            break;
        }
    }

    if ($meta_interval > 0) {
        stream_set_timeout($stream, 2);
        fread($stream, $meta_interval);
        $meta_length_byte = fread($stream, 1);
        if ($meta_length_byte === false) {
            fclose($stream);
            return null;
        }
        $meta_length = ord($meta_length_byte) * 16;
        if ($meta_length > 0) {
            $metadata = fread($stream, $meta_length);
            preg_match("/StreamTitle='(.*?)';/", $metadata, $matches);
            if (isset($matches[1])) {
                fclose($stream);
                return trim($matches[1]);
            }
        }
    }
    
    fclose($stream);
    return null;
}

if (!isset($_GET['id'])) {
    echo json_encode(['title' => 'Rádio não especificada']);
    exit;
}

require 'admin/db_connect.php';
$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT streamUrls FROM radios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $streamUrls = explode(',', trim($row['streamUrls']));
    
    $title = 'Ao Vivo';
    foreach ($streamUrls as $url) {
        $current_title = getStreamTitle(trim($url));
        if ($current_title && !empty(trim($current_title))) {
            $title = $current_title;
            break;
        }
    }
    echo json_encode(['title' => $title]);
} else {
    echo json_encode(['title' => 'Rádio não encontrada']);
}

$stmt->close();
$conn->close();
?>