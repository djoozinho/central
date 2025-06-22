<?php
/**
 * api.php
 * 
 * API para fornecer a lista de rádios em formato JSON.
 * Inclui tratamento de erros e boas práticas.
 */

// Define o cabeçalho de conteúdo como JSON com charset UTF-8 desde o início.
// Isso garante que mesmo as mensagens de erro sejam formatadas corretamente.
header('Content-Type: application/json; charset=utf-8');

try {
    // 1. Inclui o script de conexão segura com o banco de dados.
    // Se a conexão falhar, o script db_connect.php já encerrará a execução.
    require 'db_connect.php';

    // 2. Prepara um array para guardar os dados das rádios
    $radios_data = [];

    // 3. Consulta o banco de dados, ordenando por destaque e nome
    $query = "SELECT id, name, logoUrl, streamUrls, is_featured FROM radios ORDER BY is_featured DESC, name ASC";
    
    // Executa a consulta
    $result = $conn->query($query);

    // 4. Percorre os resultados e os formata para o array de dados
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Converte 'is_featured' de 0/1 para um booleano (true/false) para o JavaScript
            $row['is_featured'] = (bool)$row['is_featured']; 
            
            // Converte 'id' para inteiro para consistência
            $row['id'] = (int)$row['id'];

            $radios_data[] = $row;
        }
    }

    // 5. Libera a memória do resultado da consulta assim que não for mais necessário.
    $result->free();

    // 6. Fecha a conexão com o banco de dados.
    $conn->close();

    // 7. Define o cabeçalho Cache-Control para evitar que o navegador use uma lista antiga.
    header('Cache-Control: no-cache, must-revalidate');

    // 8. Imprime o array de dados codificado em formato JSON.
    // JSON_UNESCAPED_UNICODE garante que caracteres especiais (ç, ã, etc.) sejam exibidos corretamente.
    echo json_encode($radios_data, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Se qualquer exceção ocorrer no bloco 'try' (seja da conexão ou da query)...

    // 1. Registra o erro detalhado no log do servidor (invisível para o usuário).
    @error_log("Erro na API em " . date("Y-m-d H:i:s") . ": " . $e->getMessage() . "\n", 3, "error_log.log");

    // 2. Define o código de status HTTP para 500 (Erro Interno do Servidor).
    http_response_code(500);

    // 3. Envia uma resposta JSON de erro clara para o cliente.
    echo json_encode([
        'success' => false,
        'message' => 'Ocorreu um erro ao processar a sua solicitação.'
    ]);
}
?>