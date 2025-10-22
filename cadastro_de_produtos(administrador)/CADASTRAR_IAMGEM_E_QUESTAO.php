<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_jardimsecret";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Processa o formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Valida campos obrigatórios
    $required = ['nomeProduto', 'marcaProduto', 'valorProduto', 'descricaoProduto', 'tipoProduto'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("O campo $field é obrigatório!");
        }
    }

    $nomeProduto = $_POST['nomeProduto'];
    $marcaProduto = $_POST['marcaProduto'];
    $valorProduto = $_POST['valorProduto'];
    $descricaoProduto = $_POST['descricaoProduto'];
    $tipoProduto = $_POST['tipoProduto'];
    $imagemProduto = null; // Inicializa a variável

    // Processa a imagem
    if (isset($_FILES['imagemProduto']) && $_FILES['imagemProduto']['error'] == UPLOAD_ERR_OK) {
        $image = $_FILES['imagemProduto']['tmp_name'];
        if (is_uploaded_file($image)) {
            $imagemProduto = file_get_contents($image);
            if ($imagemProduto === false) {
                die("Erro ao ler o conteúdo do arquivo.");
            }
        } else {
            die("O arquivo não foi enviado corretamente.");
        }
    } else {
        die("Nenhum arquivo enviado ou erro no upload: " . $_FILES['imagemProduto']['error']);
    }

    // Prepara a consulta
    $stmt = $conn->prepare("INSERT INTO tb_produto (nomeProduto, marcaProduto, valorProduto, descricaoProduto, imagemProduto, tipoProduto) VALUES (?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    // Insere os dados - note que usamos $imagemProduto agora
    $stmt->bind_param("ssdsss", $nomeProduto, $marcaProduto, $valorProduto, $descricaoProduto, $imagemProduto, $tipoProduto);

    // Executa e verifica
    if ($stmt->execute()) {
        echo "<script>
                    window.location.href = 'gerenciar_produtos.php';
                </script>";
    } else {
        echo "Erro ao cadastrar o produto: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
