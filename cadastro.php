<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conecta = mysqli_connect("localhost", "root", "", "db_jardimsecret");

if ($conecta == false) {
    die("Erro de conexão: " . mysqli_connect_error());
}

$nomeCompleto = trim($_POST["nomeCompleto"]);
$nomeUsuario = trim($_POST["nomeUsuario"]);
$emailUsuario = $_POST["emailUsuario"];
$telefoneUsuario = $_POST["telefoneUsuario"];
$cpfUsuario = $_POST["cpfUsuario"];
$dataNasc = $_POST["dataNasc"];
$senhaUsuario = $_POST["senhaUsuario"];
$cepUsuario = $_POST["cepUsuario"];
$enderecoUsuario = $_POST["enderecoUsuario"];
$numUsuario = $_POST["numUsuario"];
$bairroUsuario = $_POST["bairroUsuario"];
$tipoUsuario = 'cliente';


$sql2 = "SELECT nomeUsuario FROM tb_usuario WHERE nomeUsuario = ?";
$stmt = mysqli_prepare($conecta, $sql2);
mysqli_stmt_bind_param($stmt, 's', $nomeUsuario);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo "Não é possível cadastrar, pois o nome de usuário já existe!";
} else {
    
    $sql = "INSERT INTO tb_usuario (nomeCompleto, nomeUsuario, senhaUsuario, telefoneUsuario, cpfUsuario, emailUsuario, cepUsuario , enderecoUsuario, numUsuario, bairroUsuario, dataNasc, tipoUsuario ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = mysqli_prepare($conecta, $sql);
    mysqli_stmt_bind_param($stmtInsert, 'ssssssssssss', $nomeCompleto, $nomeUsuario, $senhaUsuario, $telefoneUsuario, $cpfUsuario, $emailUsuario, $cepUsuario , $enderecoUsuario, $numUsuario, $bairroUsuario, $dataNasc, $tipoUsuario );
    
    if (mysqli_stmt_execute($stmtInsert)) {
        echo "Cadastrado com sucesso, nome de usuário válido!";

         echo "<script>
                    window.location.href = 'home.php';
                </script>";

    } else {
        echo "Erro ao cadastrar: " . mysqli_error($conecta);
    }
}

mysqli_stmt_close($stmt);
mysqli_stmt_close($stmtInsert);
mysqli_close($conecta);
?>
