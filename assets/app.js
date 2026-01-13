const api='api/api.php';
const lista=document.getElementById('lista');
const form=document.getElementById('form');
const btnCancelar =document.getElementById('btnCancelar');

function carregar(){
    fetch(api)
    .then(r=>r.json())        
    .then(d=>{
        lista.innerHTML='';        
        mostrarBtnCancelar('none');
        d.forEach(u=>{
            lista.innerHTML+=`
            <tr>
            <td>${u.id}</td>
            <td>${u.nome}</td>
            <td>${u.email}</td>
            <td>
            <a href="javascript:editar(${u.id})">Editar</a> |
            <a href="javascript:excluir(${u.id})">Excluir</a>
            </td>
            </tr>`;
        });
    });
}

function editar(id){
    mostrarBtnCancelar('block');
    fetch(api+'?id='+id)
    .then(r=>r.json())
    .then(u=>{
        idField.value=u.id;
        nome.value=u.nome;
        email.value=u.email;
    });
}

function excluir(id){
    if(confirm('Excluir registro '+id+'?')){
        fetch(api,{method:'DELETE',body:'id='+id})
        .then(()=>carregar());
    }
}

function cancelar(){    
    mostrarBtnCancelar('none');
    form.reset();
}

form.onsubmit=e=>{    
    e.preventDefault();
    //console.log(e.values,idField.value,nome.value);
    const id=idField.value;
    const metodo=id?'PUT':'POST';
    fetch(api,{
        method:metodo,
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({id,nome:nome.value,email:email.value})
    })
    .then(()=>{
        form.reset();
        carregar();
    });
};

function mostrarBtnCancelar(flag){
    btnCancelar.style.display = flag;
}
carregar();
