const canvas = document.getElementById("signature")
const ctx = canvas.getContext("2d")
const placeholder = document.querySelector('.placeholder')
const limpar = document.querySelector('.limpar')
const assinar = document.querySelector('.assinar')
const cancelar = document.querySelector('.cancelar')
const sair = document.querySelector('.sair')
const modal = document.querySelector('.modal')
const sombra = document.querySelector('.sombra')
const btnColab = document.getElementById('assinarColaborador')
const btnSuper = document.getElementById('assinarSupervisor')
const colabText = document.querySelector('#assinarColaborador>p')
const superText = document.querySelector('#assinarSupervisor>p')
const iconeCanetaC = document.querySelector('#assinarColaborador>#iconeCaneta')
const iconeCheckC = document.querySelector('#assinarColaborador>#iconeCheck')
const iconeCanetaS = document.querySelector('#assinarSupervisor>#iconeCaneta')
const iconeCheckS = document.querySelector('#assinarSupervisor>#iconeCheck')
const submit = document.querySelector('.submit')
const fotoDiv = document.querySelector('.foto')
const cancel = document.querySelector('.cancel')
const capturar = document.querySelector('.capturar')
const form = document.querySelector('form')

let assinatura = null

btnColab.addEventListener('click', () => {
    assinatura = 'colaborador'
    sombra.style.display = 'block'
    modal.style.display = 'flex'
})

btnSuper.addEventListener('click', () => {
    assinatura = 'supervisor'
    sombra.style.display = 'block'
    modal.style.display = 'flex'
})

let desenhando = false

ctx.lineWidth = 3
ctx.lineCap = "round"
ctx.strokeStyle = "#000"

canvas.addEventListener("mousedown", (e) => {
    limpar.classList.remove('desativado')
    assinar.classList.remove('desativado')
    placeholder.style.display = 'none'
    desenhando = true

    ctx.beginPath()
    ctx.moveTo(e.offsetX, e.offsetY)
})

canvas.addEventListener("mousemove", (e) => {

    if (!desenhando) return

    ctx.lineTo(e.offsetX, e.offsetY)
    ctx.stroke()

})

canvas.addEventListener("mouseup", () => {
    desenhando = false
})

canvas.addEventListener("mouseleave", () => {
    desenhando = false
})

document.querySelector(".limpar").addEventListener("click", () => {
    limpar.classList.add('desativado')
    assinar.classList.add('desativado')
    placeholder.style.display = 'block'
    ctx.clearRect(
        0,
        0,
        canvas.width,
        canvas.height
    )
})

cancelar.addEventListener('click', () => {
    sombra.style.display = 'none'
    modal.style.display = 'none'
})

sair.addEventListener('click', () => {
    sombra.style.display = 'none'
    modal.style.display = 'none'
})

assinar.addEventListener('click', () => {
    limpar.classList.add('desativado')
    assinar.classList.add('desativado')
    placeholder.style.display = 'block'
    ctx.clearRect(
        0,
        0,
        canvas.width,
        canvas.height
    )
    sombra.style.display = 'none'
    modal.style.display = 'none'
    if(assinatura == 'colaborador') {
        colabText.textContent = 'Assinado digitalmente'
        iconeCanetaC.style.display = 'none'
        iconeCheckC.style.display = 'block'
        btnColab.style.borderColor = '#00a63e'
        btnColab.style.color = '#00a63e'
        btnColab.style.backgroundColor = '#f0fdf4'
        btnColab.style.pointerEvents = 'none'
    }
    if(assinatura == 'supervisor') {
        superText.textContent = 'Assinado digitalmente'
        iconeCanetaS.style.display = 'none'
        iconeCheckS.style.display = 'block'
        btnSuper.style.borderColor = '#00a63e'
        btnSuper.style.color = '#00a63e'
        btnSuper.style.backgroundColor = '#f0fdf4'
        btnSuper.style.pointerEvents = 'none'
    }
})

submit.addEventListener('click', () => {
    fotoDiv.style.display = 'flex'
    submit.style.display = 'none'
})

cancel.addEventListener('click', () => {
    submit.style.display = 'block'
    fotoDiv.style.display = 'none'
})

capturar.addEventListener('click', () => {
    window.location.href = 'resultado_inspecao.html'
})

form.addEventListener('click', (e) => {
    e.preventDefault()
})