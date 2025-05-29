function carregarFuncionarios(servico_id){
    fetch('agendamento_funcionario.php?servico_id=' + servicoId)
    .then(res => res.json())
    .then(dados =>{
        let select = document.getElementById('funcionarios');
        select.innerHTML = '<option value="">Selecione</option>';
        dados.forEach(f =>{
            select.innerHTML += `<option value="${f.id}">${f.nome} ${f.sobrenome}</option>`;            
        });
        document.getElementById('container_funcionario').style.display = 'block';
    });

}

function carregarCalendario(){
    const funcionarioId = document.getElementById('funcionarios').value;
    const servicoId = document.getElementById('servicos').value;
    fetch(`agendamento_calendario.php?funcionario_id= ${funcionarioId}&servico_id=${servicoId}`)
    .then(res => res.text())
    .then(html =>{
        document.getElementById('calendario').innerHTML = html;
    });
}
