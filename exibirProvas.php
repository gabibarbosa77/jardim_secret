<?php

//aqui voce verifica se está logado
session_start();
if (isset($_SESSION['id'])) {
    $id=$_SESSION["id"];
} else {
    header("Location: login.html");
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provas</title>
    <style>

body, h1, ul, li, p {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
}

header {
        position: relative;
        width: 100%;
        height: 80px;
        background: rgba(255, 182, 193, 0.5);
        padding: 20px;
    }

    /* Estiliza o container do cabeçalho */
    .container {
        display: flex;
        justify-content: center; /* Centraliza o conteúdo no cabeçalho */
        align-items: center;
    }

    .logo img {
        height: 70px;
        margin-right: 15px;
    }

    .title {
        text-align: center;
        font-size: 35px;
        color: black;
        font-family: 'Times New Roman', Times, serif;
    }

   
    nav {
        background-color: #007bff; 
        padding: 10px 0;
    }

    nav ul {
        list-style: none;
        display: flex;
        justify-content: center; 
        padding: 0;
        margin: 0; 
    }

    nav ul li {
        margin-right: 20px;
    }

    nav ul li a {
        color: white; 
        text-decoration: none !important;
        font-size: 16px;
        padding: 5px 10px;
        display: block;
    }

    nav ul li a:hover {
        background-color: #0056b3; 
        border-radius: 5px;
    } 

    footer{
            background:rgba(255, 182, 193, 0.5);
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: relative !important;
            width: 100%;
            bottom: 0;
            margin-left: -9px;
        }

            footer {
                margin-top: 10px !important;
                text-align: center;
                padding: 10px;
            }

            footer p {
                font-size: 14px;
                color: #333;
            }

            footer a {
                color: #007bff;
                text-decoration: none;
                font-size: 14px;
            }

            footer a:hover {
                text-decoration: underline;
            }

form {
    width: 100%;
    margin: 50px auto;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.input-container {
    display: flex;
    align-items: center;
    width: 50%;
}

input[type="text"] {
    width: 70%;
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-right: 10px;
}

input[type="text"]:focus {
    border-color: #0056b3;
    outline: none;
}

button {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #0056b3;
}

.link {
    text-decoration: none;
    font-size: 18px;
    color: black;
    margin-left: 25px;
    transition: transform 0.2s ease, color 0.2s ease;
}

.link:hover {
    transform: scale(1.05);
    color: #BA55D3;
}

.prov {
    padding: 10px;
    margin: 10px 0;
    font-size: 20px;
    text-align: left;
    background-color: #f4f4f4;
    border-radius: 4px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

@media screen and (max-width: 768px) {
    .container {
        flex-direction: column;
        align-items: flex-start;
    }

    .logo img {
        height: 50px;
    }

    .input-container {
        width: 80%;
    }

    input[type="text"] {
        width: 100%;
    }

    button {
        width: 100%;
        margin-top: 10px;
    }
}

    a{
        text-decoration: none !important;
    }

</style>
</head>
<body>
    
<header>
    <div class="container">
        <div class="logo">
            <img src="logonova.png" alt="Logo">
        </div>
        <h1 class="title">
            CAMINHO DO SABER
        </h1>
    </div>
</header>

<!-- Faixa azul com os links -->
<nav>
    <ul>
      <li><a href="home.php">Home</a></li>
        <li><a href="exibirProvas.php">Provas</a></li>
        <li><a href="corretor.php">Corretor</a></li>
        <li><a href="progresso.php">Progresso</a></li>
        <li><a href="perfil.php">Perfil</a></li>
    </ul>
</nav>

    
    <form method="GET">
        <div class="input-container">
            <input type="text" id="nome" name="nome" placeholder="Pesquise...">
            <button type="submit">Buscar</button>
        </div>
    </form>

        <!-- barra de pesquisa -->

    <?php
        $host = 'localhost'; 
        $db = 'db_scholarsupport';
        $user = 'root';
        $pass = '';

        $conn = new mysqli($host, $user, $pass, $db);

        if ($conn->connect_error) {
            die("Erro na conexão: " . $conn->connect_error);
        }

        // Verifica se foi feita uma pesquisa
        if (isset($_GET['nome']) && !empty($_GET['nome'])) {
            $nome = $_GET['nome'];

            $sql = "SELECT * FROM tb_prova WHERE nome LIKE ?";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                die("Erro ao preparar a consulta: " . $conn->error);
            }

            // Associando o parâmetro de busca com o valor
            $nome_like = "%" . $nome . "%";
            $stmt->bind_param("s", $nome_like);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<h2>Resultados encontrados:</h2>";
                echo "<ul>";

                // Exibindo os resultados
                while ($row = $result->fetch_assoc()) {
                    echo "<li>" . "<b><a href='mostraQuest.php?id=" . $row['id'] . "'><p class='link'>" . $row['nome'] . "</p></a></b><br>" . "</li>";
                }

                echo "</ul>";
            } else {
                echo "Nenhum resultado encontrado.";
            }

            $stmt->close();
        } else {
           
        }

        $conn->close();
?>

            <?php
            $host = 'localhost';
            $db = 'db_scholarsupport';
            $user = 'root';
            $pass = '';

            $conn = new mysqli($host, $user, $pass, $db);

            if ($conn->connect_error) {
                die("Conexão falhou: " . $conn->connect_error);
            }

            $sql = "SELECT id, nome, anoProva FROM tb_prova ORDER BY anoProva DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {

                $anosExibidos = array(); // Array para armazenar os anos que já foram exibidos

                while ($row = $result->fetch_assoc()) {
                    $ano = $row['anoProva']; 

                    // Verifica se o ano já foi exibido
                    if (!in_array($ano, $anosExibidos)) {
                        // Se não foi exibido, exibe o ano e adiciona ao array de anos exibidos
                        echo "<div class='prov'>$ano</div><br>";
                        $anosExibidos[] = $ano;
                    }
                    echo "<b><a href='mostraQuest.php?id=" . $row['id'] . "'><p class='link'>" . $row['nome'] . "</p></a></b><br>";
                }

                echo "</ul>";
            } else {
                echo "Nenhum resultado encontrado.";
            }

            $conn->close();
            ?>


            <br>
            <br>

            <footer>
                <p>&copy; 2024 SCHOLARSUPPORT. Todos os direitos reservados.</p>
                <a href="POLITICA.php">Política de privacidade</a>
            </footer>

    </footer>
</body>
</html>
