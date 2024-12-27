<?php
date_default_timezone_set('America/Sao_Paulo');
function consultarCep($cep) {
    $cep = preg_replace("/[^0-9]/", "", $cep);

    if (strlen($cep) == 8) {
        $url = "https://viacep.com.br/ws/{$cep}/json/";

        $response = file_get_contents($url);

        $data = json_decode($response, true);

        return isset($data['erro']) ? false : $data;
    } else {
        return false;
    }
}

$cidade = $estado = $bairro = $rua = $cep = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cep = $_POST['cep'];


    $dados_cep = consultarCep($cep);

    if ($dados_cep) {

        $cidade = $dados_cep['localidade'];
        $estado = $dados_cep['uf'];
        $bairro = $dados_cep['bairro'];
        $rua = $dados_cep['logradouro'];

}
}
?>
<?php
function salvarConsulta($cep, $endereco_retornado) {
    $conexao = conectarBanco();


    $sql = "INSERT INTO consultas (cep, data_hora, endereco_retornado) VALUES (:cep, NOW(), :endereco_retornado)";

    $stmt = $conexao->prepare($sql);

    $stmt->bindParam(':cep', $cep);
    $stmt->bindParam(':endereco_retornado', $endereco_retornado);

    $stmt->execute();
}

$cidade = $estado = $bairro = $rua = $cep = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cep = $_POST['cep'];

    $dados_cep = consultarCep($cep);

    if ($dados_cep) {
        $cidade = $dados_cep['localidade'];
        $estado = $dados_cep['uf'];
        $bairro = $dados_cep['bairro'];
        $rua = $dados_cep['logradouro'];

        $endereco_retornado = "Rua: $rua, Bairro: $bairro, Cidade: $cidade, Estado: $estado";

        
        salvarConsulta($cep, $endereco_retornado);
    } else {
        $error = "CEP inválido ou não encontrado.";
    }
}
function conectarBanco() {
    $host = "localhost"; 
    $usuario = "root"; 
    $senha = ""; 
    $banco = "consultacep"; 

    try {
        $conexao = new PDO("mysql:host=$host;dbname=$banco;charset=utf8", $usuario, $senha);
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexao;
    } catch (PDOException $e) {
        die("Erro na conexão: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Endereço</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f9;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 360px;
            align-items: center
        }
        .form-container h2 {
            text-align: center;
        }
        .form-container input {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-container button {
            width: 50%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0px auto; 
            display: block; 
        }
        .form-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Consulta CEP</h2>
        <form action="" method="POST">
            <input type="text" name="cep" id="cep" placeholder="Digite o CEP" value="<?php echo $cep; ?>" required>

            <button type="submit">Consultar CEP</button>

            <input type="text" disabled name="cidade" id="cidade" placeholder="Cidade" value="<?php echo $cidade; ?>" required>
            <input type="text" disabled name="estado" id="estado" placeholder="Estado" value="<?php echo $estado; ?>" required>
            <input type="text" disabled name="bairro" id="bairro" placeholder="Bairro" value="<?php echo $bairro; ?>" required>
            <input type="text" disabled name="rua" id="rua" placeholder="Rua" value="<?php echo $rua; ?>" required>

            <a href="listas_consultas.php">
                <button id=botao type="button">Ver Consultas</button>
            </a>
        </form>
    </div>
</body>
</html>
