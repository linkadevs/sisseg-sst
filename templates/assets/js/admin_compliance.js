const auditoria_titulo = document.getElementById('auditoria_titulo')
const auditoria_responsavel = document.getElementById('auditoria_responsavel')
const auditoria_data = document.getElementById('auditoria_data')
const auditoria_options = document.querySelectorAll('.auditoria_options')
const auditoria_placeholder = document.querySelector('.auditoria_placeholder')
const auditoria_id = document.getElementById('id_auditoria')
const doc_nome = document.getElementById('doc_nome')
const doc_data = document.getElementById('doc_data')
const doc_options = document.querySelectorAll('.doc_options')
const doc_id = document.getElementById('id_documento')

function abrirModal(id) {
    document.getElementById(id).classList.add('active');
}

function fecharModal(id) {
    document.getElementById(id).classList.remove('active');
    auditoria_titulo.value = ''
    auditoria_responsavel.value = ''
    auditoria_data.value = ''
    auditoria_options.forEach(option => {
        option.selected = false
    })
    auditoria_placeholder.selected = true
    auditoria_id.value = ''
    doc_nome.value = ''
    doc_data.value = ''
    doc_options.forEach(options => {
        options.selected = false
    })
    doc_id.value = ''
}

function editarModalAuditoria(btn) {
    auditoria_titulo.value = btn.dataset.nome
    auditoria_responsavel.value = btn.dataset.auditor
    auditoria_data.value = btn.dataset.date
    auditoria_options.forEach(option => {
        option.selected = false
    })
    auditoria_options.forEach(option => {
        if(option.value == btn.dataset.status) {
            option.selected = true
        }
    })
    auditoria_id.value = btn.id
    abrirModal('modalAuditoria')
}

function criarModalAuditoria() {
    auditoria_titulo.value = ''
    auditoria_responsavel.value = ''
    auditoria_data.value = ''
    auditoria_options.forEach(option => {
        option.selected = false
    })
    auditoria_placeholder.selected = true
    auditoria_id.value = ''
    abrirModal('modalAuditoria')
}

function editarModalDocumento(btn) {
    doc_nome.value = btn.dataset.nome
    doc_data.value = btn.dataset.date
    doc_options.forEach(options => {
        options.selected = false
    })
    doc_options.forEach(option => {
        if(option.value == btn.dataset.status) {
            option.selected = true
        }
    })
    doc_id.value = btn.id
    abrirModal('modalDocumento')
}

function criarModalDocumento() {
    doc_nome.value = ''
    doc_data.value = ''
    doc_options.forEach(options => {
        options.selected = false
    })
    doc_id.value = ''
    abrirModal('modalDocumento')
}

// Submissão via JS (pode ajustar para enviar via fetch/API para seus Controllers)
document.getElementById('formAuditoria').addEventListener('submit', function(e) {
    e.preventDefault();
    document.getElementById('formAuditoria').submit()
    fecharModal('modalAuditoria');
});

document.getElementById('formDocumento').addEventListener('submit', function(e) {
    e.preventDefault();
    document.getElementById('formDocumento').submit()
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