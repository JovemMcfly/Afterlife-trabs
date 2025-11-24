<?php
// api.php
header('Content-Type: application/json');
require 'conexao.php';

$acao = $_GET['acao'] ?? '';

// 1. LEITURA (READ): Listar Eventos
if ($acao == 'listar') {
    $stmt = $pdo->query("SELECT * FROM eventos ORDER BY id DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// 2. CRIAÇÃO (CREATE): Adicionar Novo Evento (Admin)
if ($acao == 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    $sql = "INSERT INTO eventos (dia, titulo, descricao, preco, imagem) VALUES (:dia, :titulo, :desc, :preco, :img)";
    $stmt = $pdo->prepare($sql); // Prepared Statement (Segurança) 
    
    $sucesso = $stmt->execute([
        ':dia' => $dados['dia'],
        ':titulo' => $dados['titulo'],
        ':desc' => $dados['descricao'],
        ':preco' => $dados['preco'],
        ':img' => $dados['imagem']
    ]);

    echo json_encode(['sucesso' => $sucesso]);
    exit;
}

// 3. EXCLUSÃO (DELETE): Remover Evento
if ($acao == 'deletar') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM eventos WHERE id = :id");
    $stmt->execute([':id' => $id]);
    echo json_encode(['sucesso' => true]);
    exit;
}

// 4. REGISTRO DE PEDIDOS: Salvar compra no banco 
if ($acao == 'finalizar_pedido' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    $sql = "INSERT INTO pedidos (cliente_nome, cliente_email, total) VALUES (:nome, :email, :total)";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':nome' => $dados['nome'],
        ':email' => $dados['email'],
        ':total' => $dados['total']
    ]);
    
    echo json_encode(['mensagem' => 'Pedido registrado com sucesso!']);
    exit;
}
?>