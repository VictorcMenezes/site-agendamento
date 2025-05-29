// Verifica se o email está no formato correto
function validarEmail(email){
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Verifica se o telefone tem exatamente 11 dígitos numéricos
function validarTelefone(telefone){
    const apenasNumeros = telefone.replace(/\D/g, '');
    return apenasNumeros.length === 11;
}

//Verifica se a senha tem no mínimo 6 caracteres
function validarSenha(senha){
    return senha.length >= 6;
}

//Verifica se senha e confirmação são iguais (usado em tempo real)
function verificarSenhasIguais(senha,confirmaSenha,mensagem){
      const senhaInput = document.getElementById('senha');
      const confirmaSenhaInput = document.getElementById('confirmaSenha');
      const mensagemSenha = document.getElementById('mensagemSenha');

    function compararSenhas(){
        const senha = senha.value;
        const confirmaSenha = confirmaSenha.value;

        if(confirma === ''){
            mensagemSenha.textContent = '';
            return;
        }

        if(senha !== confirmaSenha){
            mensagemSenha.textContent = 'Senhas não iguais';
            mensagemSenha.style.color = 'red';
        }else{
            mensagemSenha.textContent = 'Senhas iguais';
            mensagemSenha.style.color = 'green';
        }
    }
    
    senha.addEventListener('input', compararSenhas);
    confirmaSenha.addEventListener('input', compararSenhas);
}

//Consulta o PHP para saber se o e-mail ou contato já estão cadastrados
async function verificarDuplicado(email, contato) {
    const resposta = await fetch('verificar_usuario.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email, contato})
    });
    
    const dados = await resposta.json();
    return dados;// { emailExiste: true/false, contatoExiste: true/false }
}
//Validação geral do formulário
async function validarFormulario(email,contato,senha){
    if(!validarEmail(email)){
        alert('Email inválido');
        return false;
    }
    if(!validarTelefone(contato)){
        alert('O contato deve ter 11 dígitos numéricos');
        return false;
    }
    if(!validarSenha(senha)){
        alert('A senha deve ter no mínimo 6 caracteres');
        return false;
    }
    const duplicado = await verificarDuplicado(email,contato);
    if(duplicado.emailExiste){
        alert('Email já cadastrado');
        return false;
    } 
    
    return true;
}

document.addEventListener("DOMContentLoaded", function(){
    const msg = document.getElementById('mensagem-sucesso');
    if(msg){
        setTimeout(function() {
            window.location.href = "../php/login.php";
        }, 2000);
    }
});
