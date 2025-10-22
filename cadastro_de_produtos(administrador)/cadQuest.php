<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Questões</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group input[type="file"] {
            padding: 0;
        }
        .form-group .alternativas {
            display: flex;
            flex-direction: column;
        }
        .form-group .alternativas input {
            margin-bottom: 5px;
        }
        .form-group .alternativas label {
            margin-bottom: 0;
        }
        .form-group button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #218838;
        }
        @media (max-width: 600px) {
            .form-group {
                margin-bottom: 10px;
            }
        }
    </style>

</head>
<body>
    <div class="container">
        <h1>Cadastro de Produtos</h1>
        <form action="CADASTRAR_IAMGEM_E_QUESTAO.php" method="post" enctype="multipart/form-data">
            <!-- Campo da imagem -->
            <div class="form-group">
                <label for="imagemProduto">Foto do Produto</label>
                <input type="file" id="imagemProduto" name="imagemProduto" accept="image/*" required>
            </div>

            <!-- Outros campos -->
            <div class="form-group">
                <label for="nomeProduto">Nome do Produto</label>
                <input type="text" id="nomeProduto" name="nomeProduto" required>
            </div>

            <div class="form-group">
                <label for="marcaProduto">Marca do Produto</label>
                <input type="text" id="marcaProduto" name="marcaProduto" required>
            </div>

            <div class="form-group">
                <label for="valorProduto">Valor do Produto (R$)</label>
                <input type="number" step="0.01" id="valorProduto" name="valorProduto" required>
            </div>

            <div class="form-group">
                <label for="descricaoProduto">Descrição do Produto</label>
                <textarea id="descricaoProduto" name="descricaoProduto" rows="4" required></textarea>
            </div>

            <!-- Lista de tipos de produto -->
            <?php
                $host = 'localhost';
                $db = 'db_jardimsecret';
                $user = 'root';
                $pass = '';

                $conn = new mysqli($host, $user, $pass, $db);
                if ($conn->connect_error) {
                    die("Conexão falhou: " . $conn->connect_error);
                }

                $sql = "SELECT idTipoProduto, tipo FROM tb_tipoproduto";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo '<div class="form-group">';
                    echo '<label for="tipoProduto">Tipo do Produto</label>';
                    echo '<select id="tipoProduto" name="tipoProduto" required>';
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['idTipoProduto'] . '">' . $row['tipo'] . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';
                } else {
                    echo "<p>Nenhum tipo de produto cadastrado.</p>";
                }
                $conn->close();
            ?>

            <div class="form-group">
                <button type="submit">Cadastrar Produto</button>
            </div>
        </form>
    </div>
</body>
</html>
