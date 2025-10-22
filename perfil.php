<?php

$serverName = 'localhost';
$nomeUsuario = 'root';
$senha = '';
$db    = 'db_jardimsecret';


//aqui voce verifica se está logado
session_start();
if (isset($_SESSION['id'])) {
    $id=$_SESSION["id"];
} else {
    header("Location: login.html");
}


$mysqli = new mysqli($serverName,$nomeUsuario,$senha,$db);

if ($mysqli == false){
    echo "erro de conexao";
    exit;
}else {
    $stmt = $mysqli->prepare("SELECT * FROM tb_usuario WHERE id = ?");
    $stmt->bind_param('i', $id);

    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil | Jardim Secret</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
            line-height: 1.6;
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

        main {
            padding: 40px 10%;
            display: flex;
            justify-content: center;
        }

        .profile-container {
            width: 100%;
            max-width: 800px;
        }

        .profile-card {
            background-color: var(--branco);
            border-radius: 12px;
            padding: 40px;
            box-shadow: var(--sombra);
            margin-bottom: 30px;
        }

        .profile-title {
            font-size: 1.8rem;
            color: var(--verde-secundario);
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--verde-claro);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .profile-title i {
            color: var(--destaque);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--verde-secundario);
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--cinza-medio);
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transicao);
            background-color: var(--cinza-claro);
        }

        .form-input:focus {
            border-color: var(--verde-primario);
            outline: none;
            background-color: var(--branco);
            box-shadow: 0 0 0 3px rgba(106, 141, 115, 0.2);
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--cinza-escuro);
            transition: var(--transicao);
        }

        .toggle-password:hover {
            color: var(--verde-primario);
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transicao);
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background-color: var(--verde-primario);
            color: var(--branco);
        }

        .btn-primary:hover {
            background-color: var(--verde-secundario);
            transform: translateY(-2px);
            box-shadow: var(--sombra);
        }

        .btn-danger {
            background-color: var(--destaque);
            color: var(--branco);
        }

        .btn-danger:hover {
            background-color: #6a3a46;
            transform: translateY(-2px);
            box-shadow: var(--sombra);
        }

        .btn-secondary {
            background-color: var(--cinza-medio);
            color: var(--cinza-escuro);
        }

        .btn-secondary:hover {
            background-color: var(--cinza-escuro);
            color: var(--branco);
            transform: translateY(-2px);
            box-shadow: var(--sombra);
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

        /* Modal de confirmação */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: var(--branco);
            margin: 15% auto;
            padding: 25px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            box-shadow: var(--sombra);
        }

        .modal-title {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: var(--verde-secundario);
        }

        .modal-text {
            margin-bottom: 20px;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        @media (max-width: 768px) {
            header {
                padding: 15px 5%;
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-links {
                gap: 15px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            main {
                padding: 30px 5%;
            }
            
            .profile-card {
                padding: 25px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
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
            <a href="home.php">Início</a>
            <a href="produtos.php">Produtos</a>
            <a href="perfil.php">Perfil</a>
            <a href="contato.html">Contato</a>
            <a href="carrinho.html" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count">0</span>
            </a>
        </nav>
    </header>

    <main>
        <div class="profile-container">
            <div class="profile-card">
                <h1 class="profile-title">
                    <i class="fas fa-user-circle"></i>
                    Meu Perfil
                </h1>
                
                <form method="POST" action="updatePerfil.php" id="profileForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nomeCompleto" class="form-label">Nome completo</label>
                            <input type="text" id="nomeCompleto" name="nomeCompleto" class="form-input" required value="<?php echo htmlspecialchars($usuario['nomeCompleto']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="nomeUsuario" class="form-label">Nome de usuário</label>
                            <input type="text" id="nomeUsuario" name="nomeUsuario" class="form-input" required value="<?php echo htmlspecialchars($usuario['nomeUsuario']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-input" required value="<?php echo htmlspecialchars($usuario['emailUsuario']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="senha" class="form-label">Senha</label>
                            <div class="password-wrapper">
                                <input type="password" id="senha" name="senha" class="form-input" required value="<?php echo htmlspecialchars($usuario['senhaUsuario']); ?>">
                                <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" class="form-input" required value="<?php echo htmlspecialchars($usuario['telefoneUsuario']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="dataNasc" class="form-label">Data de Nascimento</label>
                            <input type="text" id="dataNasc" name="dataNasc" class="form-input" required value="<?php echo htmlspecialchars($usuario['dataNasc']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" id="cpf" name="cpf" class="form-input" required value="<?php echo htmlspecialchars($usuario['cpfUsuario']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" id="cep" name="cep" class="form-input" required value="<?php echo htmlspecialchars($usuario['cepUsuario']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" id="endereco" name="endereco" class="form-input" required value="<?php echo htmlspecialchars($usuario['enderecoUsuario']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" id="numero" name="numero" class="form-input" required value="<?php echo htmlspecialchars($usuario['numUsuario']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" id="bairro" name="bairro" class="form-input" required value="<?php echo htmlspecialchars($usuario['bairroUsuario']); ?>">
                        </div>
                    </div>
                    
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Atualizar Perfil
                        </button>
                        
                        <button type="button" class="btn btn-danger" onclick="confirmarExclusao()">
                            <i class="fas fa-trash-alt"></i> Excluir Conta
                        </button>
                        
                        <a href="sair.php" class="btn btn-secondary">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Modal de Confirmação -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3 class="modal-title">Confirmar Exclusão</h3>
            <p class="modal-text">Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.</p>
            <div class="modal-buttons">
                <button onclick="excluirConta()" class="btn btn-danger">Confirmar</button>
                <button onclick="fecharModal()" class="btn btn-secondary">Cancelar</button>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <p>© 2025 Jardim Secret - Todos os direitos reservados</p>
            <p>CNPJ: 00.000.000/0001-00 | Rua Exemplo, 123 - Centro, Bauru/SP</p>
        </div>
    </footer>

    <script>
        // Alternar visibilidade da senha
        function togglePassword() {
            const senhaInput = document.getElementById('senha');
            const icon = document.querySelector('.toggle-password');
            
            if (senhaInput.type === 'password') {
                senhaInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                senhaInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Máscara de telefone
        function mascaraTelefone(input) {
            let value = input.value.replace(/\D/g, '');
            
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            
            if (value.length > 2) {
                value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
            }
            
            if (value.length > 10) {
                value = `${value.substring(0, 10)}-${value.substring(10)}`;
            }
            
            input.value = value;
        }
        
        // Máscara de data
        function mascaraData(input) {
            let value = input.value.replace(/\D/g, '');
            
            if (value.length > 8) {
                value = value.substring(0, 8);
            }
            
            if (value.length > 2) {
                value = `${value.substring(0, 2)}/${value.substring(2)}`;
            }
            
            if (value.length > 5) {
                value = `${value.substring(0, 5)}/${value.substring(5)}`;
            }
            
            input.value = value;
        }
        
        // Máscara de CPF
        function mascaraCPF(input) {
            let value = input.value.replace(/\D/g, '');
            
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            
            if (value.length > 3) {
                value = `${value.substring(0, 3)}.${value.substring(3)}`;
            }
            
            if (value.length > 7) {
                value = `${value.substring(0, 7)}.${value.substring(7)}`;
            }
            
            if (value.length > 11) {
                value = `${value.substring(0, 11)}-${value.substring(11)}`;
            }
            
            input.value = value;
        }
        
        // Máscara de CEP
        function mascaraCEP(input) {
            let value = input.value.replace(/\D/g, '');
            
            if (value.length > 8) {
                value = value.substring(0, 8);
            }
            
            if (value.length > 5) {
                value = `${value.substring(0, 5)}-${value.substring(5)}`;
            }
            
            input.value = value;
        }
        
        // Modal de confirmação
        function confirmarExclusao() {
            document.getElementById('confirmModal').style.display = 'block';
        }
        
        function fecharModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }
        
        function excluirConta() {
            window.location.href = 'excluirConta.php';
        }
        
        // Aplicar máscaras quando a página carrega
        document.addEventListener('DOMContentLoaded', function() {
            const telefoneInput = document.getElementById('telefone');
            if (telefoneInput) {
                telefoneInput.addEventListener('input', function() {
                    mascaraTelefone(this);
                });
            }
            
            const dataInput = document.getElementById('dataNasc');
            if (dataInput) {
                dataInput.addEventListener('input', function() {
                    mascaraData(this);
                });
            }
            
            const cpfInput = document.getElementById('cpf');
            if (cpfInput) {
                cpfInput.addEventListener('input', function() {
                    mascaraCPF(this);
                });
            }
            
            const cepInput = document.getElementById('cep');
            if (cepInput) {
                cepInput.addEventListener('input', function() {
                    mascaraCEP(this);
                });
            }
        });
    </script>
</body>
</html>