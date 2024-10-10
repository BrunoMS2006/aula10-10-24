<?php
// Inclua a conexão com o banco de dados
include('conexaobanco.php');

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pega os dados do formulário
    $descricao_produto = $_POST['produto'];
    $quantidade = $_POST['quantidade'];
    $preco = $_POST['preco'];

    // Começa uma transação
    $conn->begin_transaction();

    try {
        // Verifica se o produto já está cadastrado
        $sqlProduto = "SELECT Codigo_Produto FROM Produto WHERE Descricao_Produto = ?";
        $stmtProduto = $conn->prepare($sqlProduto);
        $stmtProduto->bind_param("s", $descricao_produto);
        $stmtProduto->execute();
        $resultProduto = $stmtProduto->get_result();

        if ($resultProduto->num_rows > 0) {
            // Produto já existe, pegamos o Código_Produto
            $rowProduto = $resultProduto->fetch_assoc();
            $codigo_produto = $rowProduto['Codigo_Produto'];
        } else {
            // Produto não existe, então vamos inseri-lo
            $sqlInsertProduto = "INSERT INTO Produto (Descricao_Produto, Preco) VALUES (?, ?)";
            $stmtInsertProduto = $conn->prepare($sqlInsertProduto);
            $stmtInsertProduto->bind_param("sd", $descricao_produto, $preco);
            $stmtInsertProduto->execute();

            // Pegamos o Código_Produto recém inserido
            $codigo_produto = $conn->insert_id;
        }

        // Insere uma nova nota fiscal
        $sqlInsertNF = "INSERT INTO Nota_fiscal (Data_NF, Valor_NF) VALUES (CURDATE(), ?)";
        $stmtInsertNF = $conn->prepare($sqlInsertNF);
        $valor_nf = $preco * $quantidade; // Valor total da nota fiscal
        $stmtInsertNF->bind_param("d", $valor_nf);
        $stmtInsertNF->execute();

        // Pegamos o Numero_NF recém inserido
        $numero_nf = $conn->insert_id;

        // Insere os itens na tabela Itens
        $sqlInsertItens = "INSERT INTO Itens (Codigo_Produto, Numero_NF, Quantidade) VALUES (?, ?, ?)";
        $stmtInsertItens = $conn->prepare($sqlInsertItens);
        $stmtInsertItens->bind_param("iii", $codigo_produto, $numero_nf, $quantidade);
        $stmtInsertItens->execute();

        // Se tudo der certo, comita a transação
        $conn->commit();
        $mensagem = "Venda cadastrada com sucesso!";
    } catch (Exception $e) {
        // Se algo der errado, desfaz a transação
        $conn->rollback();
        $mensagem = "Erro ao cadastrar venda: " . $e->getMessage();
    }

    // Fecha a conexão
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Vendas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Cadastro de Vendas</h1>

        <?php
        // Exibe a mensagem de sucesso ou erro, se existir
        if (isset($mensagem)) {
            echo "<p>$mensagem</p>";
        }
        ?>

        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="produto">Produto:</label>
                <input type="text" id="produto" name="produto" required>
            </div>
            <div class="form-group">
                <label for="quantidade">Quantidade:</label>
                <input type="number" id="quantidade" name="quantidade" required>
            </div>
            <div class="form-group">
                <label for="preco">Preço (R$):</label>
                <input type="number" step="0.01" id="preco" name="preco" required>
            </div>
            <button type="submit">Cadastrar Venda</button>
        </form>
    </div>
</body>
</html>
