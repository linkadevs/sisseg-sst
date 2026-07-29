const modal = document.querySelector('.modal-overlay')
const adicionar_funcionario = document.querySelector('.adicionar_funcionario')
const modal_card = document.querySelector(".modal-card")
const fechar_modal_btn = document.querySelector('.btn-fechar-modal')
const cancelar = document.querySelector('#btn-cancelar')
const editar = document.querySelectorAll('.editar')
const deletar = document.querySelectorAll('.deletar')
const submit = document.querySelector('.funcionario')
const form_pesquisa = document.querySelector('.barra-ferramentas')
const limpar = document.querySelector('.btn-perigo')
const pesquisa = document.getElementById('pesquisa')

// MODAL
const modal_h2 = document.querySelector('.modal-header h2')
const nome = document.getElementById('nome')
const cpf = document.getElementById('cpf')
const turno = document.getElementById('turno')
const setor = document.getElementById('setor')
const cargo = document.getElementById('cargo')
const senha = document.getElementById('senha')
const turno_option = document.querySelectorAll('.turno_option')
const setor_option = document.querySelectorAll('.setor_option')

const abrir_modal = () => {
    modal.style.display = 'flex'
    modal.style.opacity = 1
}

const fechar_modal = () => {
    modal.style.display = 'none'
    modal.style.opacity = 0
}

const criar_funcionario = () => {
    modal_h2.textContent = 'Cadastrar novo funcionário'
    nome.value = ''
    cpf.value = ''
    cargo.value = ''
    senha.value = ''
    senha.required = true
    turno_option.forEach((option) => {
        option.selected = false
    })
    setor_option.forEach((option) => {
        option.selected = false
    })
    document.getElementById('placeholder').selected = true
    document.getElementById('placeholder2').selected = true
    submit.value = 'criar'
    abrir_modal()
}

const editar_funcionario = (btn) => {
    modal_h2.textContent = 'Editar funcionário'
    nome.value = btn.dataset.nome
    cpf.value = btn.dataset.cpf
    cargo.value = btn.dataset.cargo
    senha.required = false
    turno_option.forEach((option) => {
        option.selected = false
    })
    turno_option.forEach((option) => {
        if(option.id == btn.dataset.turno) {
            option.selected = true
        }
    })
    setor_option.forEach((option) => {
        option.selected = false
    })
    setor_option.forEach((option) => {
        if(option.id == btn.dataset.setor) {
            option.selected = true
        }
    })
    submit.value = btn.dataset.id
    abrir_modal()
}

adicionar_funcionario.addEventListener('click', () => {
    criar_funcionario()
})

modal_card.addEventListener('click', (e) => {
    e.stopPropagation()
})

modal.addEventListener('click', () => {
    fechar_modal()
})

fechar_modal_btn.addEventListener('click', () => {
    fechar_modal()
})

cancelar.addEventListener('click', () => {
    fechar_modal()
})

editar.forEach((btn) => {
    btn.addEventListener('click', () => {
        editar_funcionario(btn)
    })
})

deletar.forEach((btn) => {
    btn.addEventListener('click', () => {
        if(confirm('Você deseja mesmo deletar esse funcionário?')) {
            btn.parentElement.submit()
        }
    })
})

if(limpar) {
    limpar.addEventListener('click', () => {
        pesquisa.value = ''
        form_pesquisa.submit()
    })
}