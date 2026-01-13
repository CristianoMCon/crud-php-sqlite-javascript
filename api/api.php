<?php
header("Content-Type: application/json");

$db = new PDO("sqlite:database.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("
CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT NOT NULL
);
");

//Insere registro ao iniciar
$stmt = $db->query("SELECT * FROM usuarios ORDER BY id DESC");

if(sizeof($stmt->fetchAll()) == 0){   

    //Exclui banco de dados e cria um novo para zerar sequencial
    $db->exec("
    DROP TABLE usuarios;
    CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT NOT NULL
    );
    ");
    //Insere registros para exemplos
    $exemplos = [
        ['kristos','gamer@estudos.com.br'],
        ['cristiano','dev@estudos.com.br'],        
    ];
    $stmt = $db->prepare("INSERT INTO usuarios (nome,email) VALUES (?,?)");
    foreach($exemplos as $linha){
        $stmt->execute($linha);
    }
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE id=?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            $stmt = $db->query("SELECT * FROM usuarios ORDER BY id DESC");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $db->prepare("INSERT INTO usuarios (nome,email) VALUES (?,?)");
        $stmt->execute([$data['nome'], $data['email']]);
        echo json_encode(["success"=>true]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $db->prepare("UPDATE usuarios SET nome=?, email=? WHERE id=?");
        $stmt->execute([$data['nome'], $data['email'], $data['id']]);
        echo json_encode(["success"=>true]);
        break;

    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        $stmt = $db->prepare("DELETE FROM usuarios WHERE id=?");
        $stmt->execute([$data['id']]);
        echo json_encode(["success"=>true]);
        break;
}
