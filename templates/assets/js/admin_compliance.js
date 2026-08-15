function abrirModal(id) {
    document.getElementById(id).classList.add('active');
}

function fecharModal(id) {
    document.getElementById(id).classList.remove('active');
}

// Submissão via JS (pode ajustar para enviar via fetch/API para seus Controllers)
document.getElementById('formAuditoria').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Auditoria agendada com sucesso!');
    fecharModal('modalAuditoria');
});

document.getElementById('formDocumento').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Documento adicionado com sucesso!');
    fecharModal('modalDocumento');
});

document.getElementById('modalAuditoria').addEventListener('click', (e) => {
    if(e.target.id == 'modalAuditoria') {
        fecharModal('modalAuditoria')
    }
})

document.getElementById('modalDocumento').addEventListener('click', (e) => {
    if(e.target.id == 'modalDocumento') {
        fecharModal('modalDocumento')
    } 
})