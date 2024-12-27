<?php
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

function listarConsultas() {
    $conexao = conectarBanco();

    $sql = "SELECT * FROM consultas ORDER BY data_hora DESC"; 
    $stmt = $conexao->query($sql);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$consultas = listarConsultas();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Consultas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        h2 {
            text-align: center;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color:rgb(0, 0, 0);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 30%;
            margin: 20px
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Lista de Consultas de CEP</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>CEP</th>
                    <th>Data e Hora</th>
                    <th>Endereço Retornado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($consultas) > 0): ?>
                    <?php foreach ($consultas as $consulta): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($consulta['id']); ?></td>
                            <td><?php echo htmlspecialchars($consulta['cep']); ?></td>
                            <td><?php echo htmlspecialchars($consulta['data_hora']); ?></td>
                            <td><?php echo htmlspecialchars($consulta['endereco_retornado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">Nenhuma consulta registrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="index.php">
                            <button type="button">Consultar outro Cep </button>
    </div>
</body>
</html>
