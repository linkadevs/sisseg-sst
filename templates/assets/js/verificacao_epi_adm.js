const criar_atividade = document.querySelector('.criar_atividade')
const voltar = document.querySelector('form figure')
const form = document.querySelector('form')
const sombra = document.querySelector('.sombra')
const inputs_div = document.querySelector('.inputs')
const h2_modal = document.querySelector('form h2')
const adicionar_epi = document.querySelector('.adicionar_epi')

// INPUTS
let nome = document.getElementById('nome_atividade')
let nr = document.getElementById('nr')
let epis = document.querySelectorAll('.epi')
let input_file = document.getElementById('foto')
let button_file = document.querySelector('.foto')

// VARIÁVEIS
let qtd_epi = 1

const abrir_modal = () => {
    form.style.display = 'flex'
    sombra.style.display = 'block'
}

const fechar_modal = () => {
    sombra.style.display = 'none'
    form.style.display = 'none'
}

const modal_criar = () => {
    nome.value = ''
    nr.value = ''
    h2_modal.textContent = 'Criar nova atividade'
    document.querySelector('#epi-1').value = 'placeholder'
    document.querySelector('#epi-1').style.color = '#707170'
}

const modal_editar = (btn) => {
    h2_modal.textContent = 'Editar atividade'
}

criar_atividade.addEventListener('click', () => {
    qtd_epi = 1
    inputs_div.innerHTML = `
        <div class="input">
            <label for="nome_atividade">Nome</label>
            <input type="text" name="nome_atividade" id="nome_atividade" placeholder="Digite o nome da atividade">
        </div>
        <div class="input">
            <label for="nr">NR</label>
            <select name="nr" id="nr">
                <option value="placeholder" selected>Selecione a NR da atividade</option>
                ${opcoesNr}
            </select>
        </div>
        <div class="input">
            <label for="foto">Foto</label>
            <button class="foto" type="button" onclick="document.querySelector('#foto').value = ''; document.querySelector('#foto').click()">Selecione a foto da atividade</button>
        </div>
        <input type="file" id="foto" name="foto" accept="image/*">
        <div class="input">
            <label for="epi-1">EPI - 1</label>
            <select name="epis[]" id="epi-1" class="epi">
                <option value="placeholder" selected>Selecione um EPI</option>
                ${opcoesEpi}
            </select>
        </div>
        <input type="hidden" name="id_atividade" value="">
    `
    let nome = document.getElementById('nome_atividade')
    let nr = document.getElementById('nr')
    let epis = document.querySelectorAll('.epi')
    let input_file = document.getElementById('foto')
    let button_file = document.querySelector('.foto')
    modal_criar()
    abrir_modal()
})

adicionar_epi.addEventListener('click', () => {
    qtd_epi += 1
    inputs_div.insertAdjacentHTML('beforeend', `
        <div class="input">
            <label for="epi-${qtd_epi}">EPI - ${qtd_epi}</label>
            <select name="epis[]" id="epi-${qtd_epi}" class="epi">
                <option value="placeholder" selected>Selecione um EPI</option>
                ${opcoesEpi}
            </select>
        </div>
    `);
})

sombra.addEventListener('click', () => {
    fechar_modal()
})

voltar.addEventListener('click', () => {
    fechar_modal()
})

const verificar_placeholder = (epi) => {
    if(epi.value == 'placeholder') {
        epi.style.color = '#707170'
    } else {
        epi.style.color = '#000000'
    }
}

epis.forEach((epi) => {
    epi.addEventListener('change', () => {
        verificar_placeholder(epi)
    })
})

inputs_div.addEventListener('change', (event) => {
    if (event.target.classList.contains('epi')) {
        verificar_placeholder(event.target)
    }
    if (event.target.id === 'foto') {
        
        const btn_foto = inputs_div.querySelector('.foto')
        
        if (event.target.files.length > 0) {
            btn_foto.style.color = '#000000'
            btn_foto.innerHTML = 'Foto selecionada'
        } else {
            btn_foto.style.color = '#707170'
            btn_foto.innerHTML = 'Selecione a foto da atividade'
        }
    }
})
