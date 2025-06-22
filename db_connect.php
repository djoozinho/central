<?php
/**
 * db_connect.php
 * 
 * Arquivo de conexão segura com o banco de dados.
 * Configurado para o banco de dados 'radio_nexus_db'.
 */

// --- DADOS DE CONEXÃO ---
// Idealmente, em um ambiente de produção, estes dados viriam de variáveis de ambiente.
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');      // Padrão do XAMPP
define('DB_PASSWORD', '');          // Padrão do XAMPP
define('DB_NAME', 'radio_nexus_db');

/**
 * Ativa o modo de relatório de erros do MySQLi para lançar exceções.
 * Isso nos permite usar um bloco try-catch para um tratamento de erros mais limpo.
 * MYSQLI_REPORT_ERROR: Lança uma exceção para erros.
 * MYSQLI_REPORT_STRICT: Usa exceções em vez de avisos.
 */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Tenta estabelecer a conexão com o banco de dados
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Define o charset da conexão para UTF-8, garantindo a codificação correta de caracteres.
    $conn->set_charset("utf8mb4"); // utf8mb4 é mais moderno e suporta mais caracteres (como emojis)

} catch (mysqli_sql_exception $e) {
    /**
     * Se a conexão falhar, o bloco catch é executado.
     * NUNCA exponha os detalhes do erro para o usuário final em um site em produção.
     */
     
    // 1. Registra o erro detalhado em um arquivo de log (invisível para o usuário)
    // O @ suprime a saída de erro padrão do PHP se o arquivo não puder ser escrito.
    // A data garante que cada log seja único e fácil de rastrear.
    @error_log(
        "Erro de conexão com o banco de dados em " . date("Y-m-d H:i:s") . ": " . $e->getMessage() . "\n", 
        3, 
        "error_log.log" // Nome do arquivo de log que será criado na mesma pasta.
    );

    // 2. Exibe uma mensagem de erro genérica e amigável para o usuário e encerra o script.
    // O código de status HTTP 500 indica um erro interno do servidor.
    http_response_code(500);
    die("Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.");
}

// Se o script chegou até aqui, a conexão foi um sucesso e a variável $conn está pronta para ser usada.
?>