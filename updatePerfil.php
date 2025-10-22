<?php
session_start();

$servername = "localhost";
$username = "root";
$password = '';
$dbname = "db_jardimsecret";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header("Location: perfil.php?erro=1");
    exit();
}

$id = $_SESSION['id'];
$nomeCompleto = trim($_POST["nomeCompleto"]);
$nomeUsuario = trim($_POST["nomeUsuario"]);
$email = $_POST["email"];
$senha = $_POST["senha"];
$telefone = $_POST["telefone"];
$dataNasc = $_POST["dataNasc"];
$cpf = $_POST["cpf"];
$cep = $_POST["cep"];
$endereco = $_POST["endereco"];
$numero = $_POST["numero"];
$bairro = $_POST["bairro"];

$sql = "UPDATE tb_usuario SET nomeCompleto = ?, nomeUsuario = ?, emailUsuario = ?, senhaUsuario = ?, telefoneUsuario = ?, dataNasc = ?, cpfUsuario = ?, cepUsuario = ?, enderecoUsuario = ?, numUsuario = ?, bairroUsuario = ? WHERE id = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    header("Location: perfil.php?erro=1");
    exit();
}

$stmt->bind_param("sssssssssssi", $nomeCompleto, $nomeUsuario, $email, $senha, $telefone, $dataNasc, $cpf, $cep, $endereco, $numero, $bairro, $id);

if ($stmt->execute()) {
    header("Location: perfil.php?sucesso=1");
} else {
    header("Location: perfil.php?erro=1");
}

$stmt->close();
$conn->close();
?>