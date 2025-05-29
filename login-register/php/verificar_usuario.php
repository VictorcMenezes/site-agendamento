<?php
header('Content-Type: application/json');

//Lê o corpo bruto da requisição (os dados JSON que o JS enviou).
$dados = json_decode(file_get_contents("php://input"), true);
$email = $dados['email'] ?? '';
$contato = $dados['contato']?? '';

//Abre a conexão com o banco SQLite.
$pdo = new PDO("sqlite:../../banco/banco.db");

//Prepara uma consulta SQL para procurar registros com o mesmo e-mail ou telefone.
$stmt = $db->prepare("SELECT email, contato FROM usuarios WHERE email = :email OR contato = :contato");
$stmt->execute(['email' => $email, 'contato' => $contato]);

//Inicializa as variáveis que vão indicar se o e-mail ou o telefone já existem.
$emailExiste = false;
$contatoExiste = false;

//Percorre os resultados retornados pelo banco.
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($row['email'] === $email) {
        $emailExiste = true;
    }
    if ($row['contato'] === $contato) {
        $contatoExiste = true;
    }
}

//Converte o resultado em JSON e envia de volta ao JavaScript.
echo json_encode(['emailExiste' => $emailExiste, 'contatoExiste' => $contatoExiste]);
