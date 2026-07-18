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
    if (btnColab.getAttribute('status') === "assinado") {
        if (confirm("Essa assinatura já foi realizada, deseja refazer?")) {
            assinatura = 'colaborador'
            sombra.style.display = 'block'
            modal.style.display = 'flex'
        }
    } else {
        assinatura = 'colaborador'
        sombra.style.display = 'block'
        modal.style.display = 'flex'
    }
})

btnSuper.addEventListener('click', () => {
    if (btnSuper.getAttribute('status') === "assinado") {
        if (confirm("Essa assinatura já foi realizada, deseja refazer?")) {
            assinatura = 'supervisor'
            sombra.style.display = 'block'
            modal.style.display = 'flex'
        }
    } else {
        assinatura = 'supervisor'
        sombra.style.display = 'block'
        modal.style.display = 'flex'
    }
})

let desenhando = false

ctx.lineWidth = 3
ctx.lineCap = "round"
ctx.strokeStyle = "#000"

let posicao = false

// Função para calcular a posição exata do cursor ou toque em relação ao Canvas
function obterPosicao(e) {
    const rect = canvas.getBoundingClientRect();
    if (posicao === false) {
        canvas.width = rect.width;
        canvas.height = rect.height;
        posicao = true
    }
    ctx.lineWidth = 3;
    ctx.lineCap = "round";
    ctx.lineJoin = "round";
    ctx.strokeStyle = "#000";
    // Tratamento para telas touch (mobile)
    if (e.touches && e.touches.length > 0) {
        return {
            x: e.touches[0].clientX - rect.left,
            y: e.touches[0].clientY - rect.top
        };
    }
    
    // Tratamento para mouse convencional (desktop)
    return {
        x: e.clientX - rect.left,
        y: e.clientY - rect.top
    };
}

// Funções de manipulação do desenho
function iniciarDesenho(e) {
    if (e.type === 'touchstart') e.preventDefault(); // Evita scroll na tela ao desenhar no celular
    
    limpar.classList.remove('desativado')
    assinar.classList.remove('desativado')
    placeholder.style.display = 'none'
    desenhando = true
    
    const pos = obterPosicao(e);
    ctx.beginPath()
    ctx.moveTo(pos.x, pos.y)
}

function moverDesenho(e) {
    if (!desenhando) return;
    if (e.type === 'touchmove') e.preventDefault();

    const pos = obterPosicao(e);
    ctx.lineTo(pos.x, pos.y)
    ctx.stroke()
}

function pararDesenho() {
    desenhando = false
}

// Eventos para Mouse (Desktop)
canvas.addEventListener("mousedown", iniciarDesenho)
canvas.addEventListener("mousemove", moverDesenho)
canvas.addEventListener("mouseup", pararDesenho)
canvas.addEventListener("mouseleave", pararDesenho)

// Eventos para Touch (Celular/Tablet)
canvas.addEventListener("touchstart", iniciarDesenho)
canvas.addEventListener("touchmove", moverDesenho)
canvas.addEventListener("touchend", pararDesenho)

// Eventos dos botões do modal
document.querySelector(".limpar").addEventListener("click", () => {
    limpar.classList.add('desativado')
    assinar.classList.add('desativado')
    placeholder.style.display = 'block'
    ctx.clearRect(0, 0, canvas.width, canvas.height)
})

cancelar.addEventListener('click', () => {
    sombra.style.display = 'none'
    modal.style.display = 'none'
})

sair.addEventListener('click', () => {
    sombra.style.display = 'none'
    modal.style.display = 'none'
    ctx.clearRect(0, 0, canvas.width, canvas.height)
    placeholder.style.display = 'block'
})

assinar.addEventListener('click', () => {
    limpar.classList.add('desativado')
    assinar.classList.add('desativado')
    placeholder.style.display = 'block'
    ctx.clearRect(0, 0, canvas.width, canvas.height)
    sombra.style.display = 'none'
    modal.style.display = 'none'
    
    if(assinatura == 'colaborador') {
        colabText.textContent = 'Assinado digitalmente'
        iconeCanetaC.style.display = 'none'
        iconeCheckC.style.display = 'block'
        btnColab.style.borderColor = '#00a63e'
        btnColab.style.color = '#00a63e'
        btnColab.style.backgroundColor = '#f0fdf4'
        btnColab.setAttribute("status", "assinado")
        // btnColab.style.pointerEvents = 'none'
    }
    if(assinatura == 'supervisor') {
        superText.textContent = 'Assinado digitalmente'
        iconeCanetaS.style.display = 'none'
        iconeCheckS.style.display = 'block'
        btnSuper.style.borderColor = '#00a63e'
        btnSuper.style.color = '#00a63e'
        btnSuper.style.backgroundColor = '#f0fdf4'
        btnSuper.setAttribute("status", "assinado")
        // btnSuper.style.pointerEvents = 'none'
    }
    alert("Assinatura enviada")
})

// Outros botões do fluxo
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

// CORREÇÃO DOS CHECKBOXES: O preventDefault agora roda no 'submit' para não bloquear cliques de interação
form.addEventListener('submit', (e) => {
    e.preventDefault()
})