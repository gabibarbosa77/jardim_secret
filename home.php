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
if (isset($_SESSION['id'])) {
    $id = $_SESSION["id"];
} else {
    header("Location: login.html");
}

// Buscar categorias de produtos
$categorias = $conn->query("SELECT * FROM tb_tipoproduto");

// Buscar produtos por categoria
$produtosPorCategoria = [];
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
            padding: 0 40px;
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
            min-width: 280px;
            width: 280px;
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

        .product-image-container {
            height: 200px;
            width: 100%;
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f8f8;
        }

        .product-image {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            transition: var(--transicao);
        }

        .product-card:hover .product-image {
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
            overflow: hidden;
            position: relative;
        }

        .product-description.collapsed {
            max-height: 60px;
            -webkit-line-clamp: 3;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            text-overflow: ellipsis;
        }

        .read-more {
            color: var(--verde-primario);
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 5px;
            display: inline-block;
        }

        .read-more:hover {
            text-decoration: underline;
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
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--sombra);
            z-index: 10;
            transition: var(--transicao);
            border: none;
            font-size: 1.2rem;
        }

        .carousel-btn:hover {
            background: var(--verde-primario);
            color: var(--branco);
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-btn.prev {
            left: 0;
        }

        .carousel-btn.next {
            right: 0;
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
            
            .products-container {
                padding: 0 20px;
            }
            
            .carousel-btn {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .product-card {
                min-width: 220px;
                width: 220px;
            }
        }

        /* Modal para descrição completa */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 100;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--branco);
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--cinza-escuro);
        }

        .modal-title {
            font-size: 1.5rem;
            color: var(--verde-secundario);
            margin-bottom: 15px;
        }

        .modal-description {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--cinza-escuro);
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
            <a href="home.php">home</a>
            <a href="produtos.php">Produtos</a>
            <a href="perfil.php">perfil</a>
            <a href="contato.html">Contato</a>
            <a href="carrinho.html" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count">0</span>
            </a>
        </nav>
    </header>

    <main>
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
                        <div class="product-image-container">
                            <img class="product-image" src="data:image/jpeg;base64,<?php echo base64_encode($produto['imagemProduto']); ?>" alt="<?php echo htmlspecialchars($produto['nomeProduto']); ?>">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($produto['nomeProduto']); ?></h3>
                            <div class="product-description collapsed">
                                <?php echo htmlspecialchars($produto['descricaoProduto']); ?>
                            </div>
                            <span class="read-more">Leia mais</span>
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
    </main>

    <!-- Modal para descrição completa -->
    <div class="modal" id="descriptionModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3 class="modal-title" id="modalProductName"></h3>
            <p class="modal-description" id="modalProductDescription"></p>
        </div>
    </div>

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
            const productWidth = 280 + 25; // Largura do card + gap
            const scrollAmount = productWidth * 2; // Quantidade de rolagem (2 cards)
            
            nextBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
            
            prevBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
            
            // Esconder botões quando não houver mais para rolar
            const updateButtons = () => {
                prevBtn.style.display = carousel.scrollLeft > 0 ? 'flex' : 'none';
                nextBtn.style.display = carousel.scrollLeft < (carousel.scrollWidth - carousel.clientWidth - 1) ? 'flex' : 'none';
            };
            
            carousel.addEventListener('scroll', updateButtons);
            updateButtons();
        });

        // Controle do "Leia mais"
        document.querySelectorAll('.read-more').forEach(btn => {
            btn.addEventListener('click', function() {
                const descriptionContainer = this.previousElementSibling;
                const productName = this.closest('.product-info').querySelector('.product-name').textContent;
                const productDescription = descriptionContainer.textContent;
                
                // Abrir modal com descrição completa
                const modal = document.getElementById('descriptionModal');
                document.getElementById('modalProductName').textContent = productName;
                document.getElementById('modalProductDescription').textContent = productDescription;
                modal.style.display = 'flex';
            });
        });

        // Fechar modal
        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('descriptionModal').style.display = 'none';
        });

        // Fechar modal ao clicar fora
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('descriptionModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>