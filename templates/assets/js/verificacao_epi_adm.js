const criar_atividade = document.querySelector('.criar_atividade')

const form = document.querySelector('form')
const sombra = document.querySelector('.sombra')

criar_atividade.addEventListener('click', () => {
    form.style.display = 'flex'
    sombra.style.display = 'block'
})

sombra.addEventListener('click', () => {
    sombra.style.display = 'none'
    form.style.display = 'none'
})

