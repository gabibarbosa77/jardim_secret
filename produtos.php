<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_jardimsecret";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verificar se está logado
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.html");
    exit();
}

// Processar busca
$termoBusca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$produtosPorCategoria = [];

if (!empty($termoBusca)) {
    // Busca produtos que correspondam ao termo de busca
    $termoLike = "%" . $conn->real_escape_string($termoBusca) . "%";
    $query = $conn->prepare("SELECT p.*, t.tipo 
                            FROM tb_produto p
                            JOIN tb_tipoproduto t ON p.tipoProduto = t.idTipoProduto
                            WHERE p.nomeProduto LIKE ? OR p.descricaoProduto LIKE ? OR t.tipo LIKE ?");
    $query->bind_param("sss", $termoLike, $termoLike, $termoLike);
    $query->execute();
    $result = $query->get_result();
    
    // Organizar por categoria
    while ($produto = $result->fetch_assoc()) {
        $produtosPorCategoria[$produto['tipo']][] = $produto;
    }
} else {
    // Buscar todas as categorias e produtos (como no código original)
    $categorias = $conn->query("SELECT * FROM tb_tipoproduto");
    
    while ($categoria = $categorias->fetch_assoc()) {
        $query = $conn->prepare("SELECT * FROM tb_produto WHERE tipoProduto = ?");
        $query->bind_param("i", $categoria['idTipoProduto']);
        $query->execute();
        $result = $query->get_result();
        $produtos = [];
        
        while ($produto = $result->fetch_assoc()) {
            $produtos[] = $produto;
        }
        
        if (!empty($produtos)) {
            $produtosPorCategoria[$categoria['tipo']] = $produtos;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos | Jardim Secret</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --verde-primario: #6a8d73;
            --verde-secundario: #4a6b5b;
            --verde-claro: #e9f5e9;
            --branco: #ffffff;
            --cinza-claro: #f8f9fa;
            --cinza-medio: #e9ecef;
            --cinza-escuro: #495057;
            --destaque: #7b4c58;
            --sombra: 0 4px 20px rgba(0, 0, 0, 0.1);
            --transicao: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--verde-claro);
            color: var(--cinza-escuro);
        }

        header {
            background: linear-gradient(135deg, var(--verde-primario), var(--verde-secundario));
            color: var(--branco);
            padding: 20px 10%;
            box-shadow: var(--sombra);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            font-size: 1.8rem;
        }

        .nav-links {
            display: flex;
            gap: 25px;
        }

        .nav-links a {
            color: var(--branco);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transicao);
            position: relative;
        }

        .nav-links a:hover {
            color: var(--verde-claro);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--verde-claro);
            transition: var(--transicao);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .cart-icon {
            position: relative;
            font-size: 1.3rem;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--destaque);
            color: var(--branco);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }

        main {
            padding: 40px 10%;
        }

        .category-section {
            margin-bottom: 50px;
        }

        .category-title {
            font-size: 1.8rem;
            color: var(--verde-secundario);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--verde-claro);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .category-title i {
            color: var(--destaque);
        }

        .products-container {
            position: relative;
            overflow: hidden;
        }

        .products-carousel {
            display: flex;
            gap: 25px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 20px 0;
            scrollbar-width: none; /* Firefox */
        }

        .products-carousel::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .product-card {
            min-width: 250px;
            background: var(--branco);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--sombra);
            transition: var(--transicao);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            height: 200px;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transicao);
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--verde-secundario);
        }

        .product-description {
            font-size: 0.9rem;
            color: var(--cinza-escuro);
            margin-bottom: 15px;
            flex-grow: 1;
        }

        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--destaque);
            margin-bottom: 15px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            border: 1px solid var(--cinza-medio);
            border-radius: 8px;
            overflow: hidden;
        }

        .quantity-btn {
            background: var(--cinza-claro);
            border: none;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transicao);
        }

        .quantity-btn:hover {
            background: var(--cinza-medio);
        }

        .quantity-input {
            width: 40px;
            height: 30px;
            text-align: center;
            border: none;
            border-left: 1px solid var(--cinza-medio);
            border-right: 1px solid var(--cinza-medio);
            font-weight: 500;
        }

        .add-to-cart {
            flex-grow: 1;
            background: var(--verde-primario);
            color: var(--branco);
            border: none;
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transicao);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .add-to-cart:hover {
            background: var(--verde-secundario);
            transform: translateY(-2px);
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: var(--branco);
            color: var(--verde-primario);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--sombra);
            z-index: 1;
            transition: var(--transicao);
            border: none;
        }

        .carousel-btn:hover {
            background: var(--verde-primario);
            color: var(--branco);
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-btn.prev {
            left: -20px;
        }

        .carousel-btn.next {
            right: -20px;
        }

        footer {
            background: linear-gradient(135deg, var(--verde-primario), var(--verde-secundario));
            color: var(--branco);
            text-align: center;
            padding: 30px 10%;
            margin-top: 50px;
        }

        .footer-content {
            max-width: 800px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            header {
                padding: 15px 5%;
            }
            
            main {
                padding: 30px 5%;
            }
            
            .carousel-btn {
                display: none;
            }
            
            .product-card {
                min-width: 200px;
            }
        }
        
        .search-container {
            margin: 30px 10% 40px;
            display: flex;
            justify-content: center;
        }
        
        .search-box {
            position: relative;
            width: 100%;
            max-width: 600px;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 20px;
            padding-right: 50px;
            border: 2px solid var(--verde-primario);
            border-radius: 30px;
            font-size: 1rem;
            outline: none;
            transition: var(--transicao);
            box-shadow: var(--sombra);
        }
        
        .search-input:focus {
            border-color: var(--verde-secundario);
            box-shadow: 0 0 10px rgba(106, 141, 115, 0.3);
        }
        
        .search-button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--verde-primario);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: var(--transicao);
        }
        
        .search-button:hover {
            background: var(--verde-secundario);
        }
        
        .search-results-info {
            text-align: center;
            margin-bottom: 30px;
            color: var(--verde-secundario);
            font-size: 1.1rem;
        }
        
        .no-results {
            text-align: center;
            padding: 50px;
            color: var(--cinza-escuro);
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <i class="fas fa-leaf"></i>
            <span>Jardim Secret</span>
        </div>
        <nav class="nav-links">
            <a href="home.php">Home</a>
            <a href="produtos.php">Produtos</a>
            <a href="perfil.php">Perfil</a>
            <a href="contato.html">Contato</a>
            <a href="carrinho.html" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count">0</span>
            </a>
        </nav>
    </header>

    <div class="search-container">
        <form class="search-box" action="produtos.php" method="get">
            <input type="text" class="search-input" name="busca" placeholder="Buscar produtos..." value="<?php echo htmlspecialchars($termoBusca); ?>">
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <main>
        <?php if (!empty($termoBusca)): ?>
            <div class="search-results-info">
                Resultados da busca por: <strong>"<?php echo htmlspecialchars($termoBusca); ?>"</strong>
            </div>
        <?php endif; ?>
        
        <?php if (empty($produtosPorCategoria)): ?>
            <div class="no-results">
                <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 15px;"></i>
                <p>Nenhum produto encontrado<?php echo !empty($termoBusca) ? ' para "' . htmlspecialchars($termoBusca) . '"' : ''; ?>.</p>
                <?php if (!empty($termoBusca)): ?>
                    <p>Tente usar termos diferentes ou <a href="produtos.php">ver todos os produtos</a>.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($produtosPorCategoria as $categoria => $produtos): ?>
            <section class="category-section">
                <h2 class="category-title">
                    <i class="fas fa-tag"></i>
                    <?php echo htmlspecialchars($categoria); ?>
                </h2>
                
                <div class="products-container">
                    <button class="carousel-btn prev">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    
                    <div class="products-carousel" id="carousel-<?php echo preg_replace('/\s+/', '-', strtolower($categoria)); ?>">
                        <?php foreach ($produtos as $produto): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($produto['imagemProduto']); ?>" alt="<?php echo htmlspecialchars($produto['nomeProduto']); ?>">
                            </div>
                            <div class="product-info">
                                <h3 class="product-name"><?php echo htmlspecialchars($produto['nomeProduto']); ?></h3>
                                <p class="product-description"><?php echo htmlspecialchars($produto['descricaoProduto']); ?></p>
                                <div class="product-price">R$ <?php echo number_format($produto['valorProduto'], 2, ',', '.'); ?></div>
                                <div class="product-actions">
                                    <div class="quantity-control">
                                        <button class="quantity-btn minus">-</button>
                                        <input type="number" class="quantity-input" value="1" min="1">
                                        <button class="quantity-btn plus">+</button>
                                    </div>
                                    <button class="add-to-cart" data-product-id="<?php echo $produto['idProduto']; ?>">
                                        <i class="fas fa-cart-plus"></i>
                                        Adicionar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button class="carousel-btn next">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <footer>
        <div class="footer-content">
            <p>© 2025 Jardim Secret - Todos os direitos reservados</p>
            <p>CNPJ: 00.000.000/0001-00 | Rua Exemplo, 123 - Centro, Bauru/SP</p>
        </div>
    </footer>

    <script>
        // Controle de quantidade
        document.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.quantity-input');
                let value = parseInt(input.value);
                
                if (this.classList.contains('minus')) {
                    if (value > 1) {
                        input.value = value - 1;
                    }
                } else {
                    input.value = value + 1;
                }
            });
        });

        // Adicionar ao carrinho
        document.querySelectorAll('.add-to-cart').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const quantity = this.closest('.product-actions').querySelector('.quantity-input').value;
                
                // Simular adição ao carrinho (substituir por AJAX na implementação real)
                const cartCount = document.querySelector('.cart-count');
                cartCount.textContent = parseInt(cartCount.textContent) + parseInt(quantity);
                
                // Feedback visual
                this.innerHTML = '<i class="fas fa-check"></i> Adicionado';
                this.style.backgroundColor = '#4CAF50';
                
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-cart-plus"></i> Adicionar';
                    this.style.backgroundColor = '';
                }, 2000);
                
                // Aqui você faria uma requisição AJAX para adicionar ao carrinho no backend
                console.log(`Adicionado ao carrinho: Produto ID ${productId}, Quantidade: ${quantity}`);
            });
        });

        // Controle do carrossel
        document.querySelectorAll('.products-container').forEach(container => {
            const carousel = container.querySelector('.products-carousel');
            const prevBtn = container.querySelector('.prev');
            const nextBtn = container.querySelector('.next');
            const productWidth = 250; // Largura aproximada do card + gap
            const scrollAmount = productWidth * 3; // Quantidade de rolagem
            
            nextBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
            
            prevBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
            
            // Esconder botões quando não houver mais para rolar
            carousel.addEventListener('scroll', () => {
                prevBtn.style.display = carousel.scrollLeft > 0 ? 'flex' : 'none';
                nextBtn.style.display = carousel.scrollLeft < (carousel.scrollWidth - carousel.clientWidth) ? 'flex' : 'none';
            });
            
            // Esconder botões inicialmente se não houver overflow
            if (carousel.scrollWidth <= carousel.clientWidth) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
            }
        });
    </script>
</body>
</html>