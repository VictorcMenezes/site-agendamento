<?php
session_start();

//Conecta ao banco
$pdo = new PDO("sqlite:../../banco/banco.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//Verifica se veio o servico escolhido da etapa anterior
$servico_id = $_POST['servico_id'] ?? null;

if(!$servico_id){
    echo "Serviço não selecioando!";
    exit;
}

//1.busca o serviço para descobrir qual funcao esta associada
$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->execute([$servico_id]);
$servico = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$servico_id){
    echo "Serviço não selecioando!";
    exit;
}   

//Pegamos a função que pode executar esse serviço
$funcao  = $servico['funcao'] ?? $servico['nome'];//voce pode ter um campo 'funcao' ou usar o nome


//2.Busca todos os funcionarios com essa função
$stmt = $pdo->prepare("SELECT * FROM funcionarios where funcao = ?");
$stmt->execute([$funcao]);
$funcionarios = $stmt->fetchALL(PDO::FETCH_ASSOC);

if(count($funcionarios) == 0){
    echo "Nenhum funcionário disponivel para o serviço.";
    exit;
}