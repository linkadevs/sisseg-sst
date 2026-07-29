const modal = document.querySelector('.modal-overlay')
const adicionar_funcionario = document.querySelector('.adicionar_funcionario')
const modal_card = document.querySelector(".modal-card")
const fechar_modal_btn = document.querySelector('.btn-fechar-modal')
const cancelar = document.querySelector('#btn-cancelar')

const abrir_modal = () => {
    modal.style.display = 'flex'
    modal.style.opacity = 1
}

const fechar_modal = () => {
    modal.style.display = 'none'
    modal.style.opacity = 0
}

adicionar_funcionario.addEventListener('click', () => {
    abrir_modal()
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