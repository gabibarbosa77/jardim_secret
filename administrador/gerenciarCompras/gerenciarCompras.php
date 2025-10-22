<?php
// Conexão com o banco de dados
$conn = new mysqli("localhost", "root", "", "db_jardimsecret");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$compras = [];

// Consulta SQL para buscar os dados das compras
// A consulta usa GROUP_CONCAT para listar todos os produtos e suas quantidades em uma única linha por compra
$sql = "
    SELECT
        c.idCompra,
        u.nomeCompleto,
        c.dataCompra,
        c.modoPagamento,
        c.valorTotal,
        GROUP_CONCAT(
            CONCAT(p.nomeProduto, ' (', ic.quantProduto, 'x)')
            SEPARATOR '<br>'
        ) AS itensCompra
    FROM
        tb_compra c
    JOIN
        tc_usuario u ON c.idUsuario = u.id
    JOIN
        tb_itemcompra ic ON c.idCompra = ic.idCompra
    JOIN
        tb_produto p ON ic.idProduto = p.idProduto
    GROUP BY
        c.idCompra
    ORDER BY
        c.dataCompra DESC
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $compras[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Compras | Caminho do Saber</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6a8d73;
            --secondary-color: #f4f4f4;
            --accent-color: #ff7e5f;
            --dark-color: #2c3e50;
            --light-color: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            color: var(--dark-color);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        header {
            background: linear-gradient(135deg, var(--primary-color), #4a6b5b);
            color: white;
            padding: 10px 20px;
            box-shadow: var(--shadow);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo img {
            height: 50px;
        }

        .site-title {
            margin: 0;
            font-size: 1.5rem;
            flex-grow: 1;
            text-align: center;
        }

        .user-menu {
            position: relative;
        }

        .user-toggle {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 8px 12px;
            border-radius: 50px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .user-toggle:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .user-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: var(--light-color);
            min-width: 200px;
            box-shadow: var(--shadow);
            z-index: 1000;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 10px;
        }

        .user-dropdown.show {
            display: block;
        }

        .user-dropdown a {
            color: var(--dark-color);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: var(--transition);
        }

        .user-dropdown a:hover {
            background-color: var(--secondary-color);
        }

        @media (max-width: 768px) {
            .site-title {
                display: none;
            }
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: var(--secondary-color);
        }

        .table thead th {
            background-color: var(--primary-color);
            color: white;
        }

        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>

    <div class="container mt-4">
        <h2>Gerenciar Compras</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID da Compra</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Forma de Pagamento</th>
                        <th>Itens da Compra</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($compras)): ?>
                        <?php foreach ($compras as $compra): ?>
                            <tr>
                                <td><?= htmlspecialchars($compra['idCompra']) ?></td>
                                <td><?= htmlspecialchars($compra['nomeCompleto']) ?></td>
                                <td><?= htmlspecialchars($compra['dataCompra']) ?></td>
                                <td><?= htmlspecialchars($compra['modoPagamento']) ?></td>
                                <td><?= $compra['itensCompra'] ?></td>
                                <td>R$ <?= htmlspecialchars($compra['valorTotal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Nenhuma compra encontrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Menu do usuário
        document.getElementById('userToggle').addEventListener('click', function() {
            document.getElementById('userDropdown').classList.toggle('show');
        });

        // Fechar menu quando clicar fora
        window.addEventListener('click', function(event) {
            if (!event.target.matches('.user-toggle') && !event.target.closest('.user-toggle')) {
                var dropdowns = document.getElementsByClassName("user-dropdown");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        });
    </script>
</body>
</html>