const criar_atividade = document.querySelector('.criar_atividade')
const voltar = document.querySelector('form figure')
const form = document.querySelector('form')
const sombra = document.querySelector('.sombra')
const inputs_div = document.querySelector('.inputs')
const h2_modal = document.querySelector('form h2')
const adicionar_epi = document.querySelector('.adicionar_epi')
const editar_atividade = document.querySelectorAll('.edit')
const search_btn = document.querySelector('.send_search')
const search_form = document.querySelector('.search_form')

// INPUTS
let nome = document.getElementById('nome_atividade')
let nr = document.getElementById('nr')
let epis = document.querySelectorAll('.epi')
let input_file = document.getElementById('foto')
let button_file = document.querySelector('.foto')
let icons = document.querySelectorAll('.icone')
let icone = document.getElementById('icone')

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
            <label for="icone">Ícone</label>
            <div class="icones">
                <button class="icone" value="chave_inglesa" type="button">🔧️</button>
                <button class="icone" value="guindaste" type="button">🏗️</button>
                <button class="icone" value="ferramentas" type="button">🛠️</button>
                <button class="icone" value="alta_tensao" type="button">⚡️</button>
                <button class="icone" value="engrenagem" type="button">⚙️</button>
                <button class="icone" value="fogo" type="button">🔥</button>
                <button class="icone" value="escada" type="button">🪜</button>
                <button class="icone" value="trator" type="button">🚜</button>
                <button class="icone" value="caixa_pacote" type="button">📦</button>
                <button class="icone" value="caminhao" type="button">🚛</button>
                <button class="icone" value="deposito_galpao" type="button">🏬</button>
                <button class="icone" value="etiqueta" type="button">🏷️</button>
                <button class="icone" value="colete_seguranca" type="button">🦺</button>
                <button class="icone" value="bota_protecao" type="button">🥾</button> 
                <button class="icone" value="oculos_protecao" type="button">🥽</button>
                <button class="icone" value="protetor_auricular" type="button">🎧</button>
                <button class="icone" value="luvas" type="button">🧤</button>
                <button class="icone" value="mascara_protecao" type="button">😷</button>
                <button class="icone" value="corda_no" type="button">🪢</button>
                <button class="icone" value="capacete_obras" type="button">👷‍♀️</button>
                <button class="icone" value="capacete_obras_sol" type="button">👷‍♂️</button>
            </div>
        </div>
        <input type="hidden" id="icone" name="icone">
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
    let icons = document.querySelectorAll('.icone')
    let icone = document.getElementById('icone')
    icons.forEach((icon) => {
        icon.addEventListener('click', () => {
            icons.forEach((icon) => {
                icon.classList.remove('active')
            })
        icon.classList.add('active')
        icone.value = icon.value
        })

    })
    modal_criar()
    abrir_modal()
})

editar_atividade.forEach((btn) => {
    btn.addEventListener('click', () => {
        qtd_epi = 1
        epis_array = btn.dataset.epis ? btn.dataset.epis.split(',').map(epi => epi.trim()).filter(Boolean) : [];
        inputs_div.innerHTML = `
            <div class="input">
                <label for="nome_atividade">Nome</label>
                <input type="text" name="nome_atividade" id="nome_atividade" placeholder="Digite o nome da atividade" value="${btn.dataset.name}">
            </div>
            <div class="input">
                <label for="nr">NR</label>
                <select name="nr" id="nr">
                    <option value="placeholder" selected>Selecione a NR da atividade</option>
                    ${opcoesNr}
                </select>
            </div>
            <div class="input">
                <label for="icone">Ícone</label>
                <div class="icones">
                    <button class="icone" value="chave_inglesa" type="button">🔧️</button>
                    <button class="icone" value="guindaste" type="button">🏗️</button>
                    <button class="icone" value="ferramentas" type="button">🛠️</button>
                    <button class="icone" value="alta_tensao" type="button">⚡️</button>
                    <button class="icone" value="engrenagem" type="button">⚙️</button>
                    <button class="icone" value="fogo" type="button">🔥</button>
                    <button class="icone" value="escada" type="button">🪜</button>
                    <button class="icone" value="trator" type="button">🚜</button>
                    <button class="icone" value="caixa_pacote" type="button">📦</button>
                    <button class="icone" value="caminhao" type="button">🚛</button>
                    <button class="icone" value="deposito_galpao" type="button">🏬</button>
                    <button class="icone" value="etiqueta" type="button">🏷️</button>
                    <button class="icone" value="colete_seguranca" type="button">🦺</button>
                    <button class="icone" value="bota_protecao" type="button">🥾</button> 
                    <button class="icone" value="oculos_protecao" type="button">🥽</button>
                    <button class="icone" value="protetor_auricular" type="button">🎧</button>
                    <button class="icone" value="luvas" type="button">🧤</button>
                    <button class="icone" value="mascara_protecao" type="button">😷</button>
                    <button class="icone" value="corda_no" type="button">🪢</button>
                    <button class="icone" value="capacete_obras" type="button">👷‍♀️</button>
                    <button class="icone" value="capacete_obras_sol" type="button">👷‍♂️</button>
                </div>
            </div>
            <input type="hidden" id="icone" name="icone">
        `
        epis_array.forEach((epi, index) => {
            inputs_div.innerHTML += `
                <div class="input">
                    <label for="epi-${index + 1}">EPI - ${index + 1}</label>
                    <select name="epis[]" id="epi-${index + 1}" class="epi" data-epi="${epi}">
                        <option value="placeholder" selected>Selecione um EPI</option>
                        ${opcoesEpi}
                    </select>
                </div>
            `
        })
        inputs_div.innerHTML += `<input type="hidden" name="id_atividade" value="${btn.dataset.id}">`
        let nome = document.getElementById('nome_atividade')
        let nr = document.getElementById('nr')
        let epis = document.querySelectorAll('.epi')
        let icons = document.querySelectorAll('.icone')
        let icone = document.getElementById('icone')
        let epi_options = document.querySelectorAll('.epi_option')
        let nr_options = document.querySelectorAll('.nr_option')
        icons.forEach((icon) => {
            if(icon.value == btn.dataset.icone) {
                icon.classList.add('active')
            }
        })
        icons.forEach((icon) => {
            icon.addEventListener('click', () => {
                icons.forEach((icon) => {
                    icon.classList.remove('active')
                })
            icon.classList.add('active')
            icone.value = icon.value
            })
        })
        epi_options.forEach((option) => {
            if(option.value == option.parentElement.dataset.epi) {
                option.selected = true
                option.parentElement.style.color = '#000000'
            }
        })

        nr_options.forEach((option) => {
            if(option.value == btn.dataset.nr) {
                option.selected = true
                option.parentElement.style.color = '#000000'
            }
        })
        h2_modal.textContent = 'Editar atividade'
        abrir_modal()
    })
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

const verificar_placeholder = (select) => {
    if(select.value == 'placeholder') {
        select.style.color = '#707170'
    } else {
        select.style.color = '#000000'
    }
}

inputs_div.addEventListener('change', (event) => {
    if (event.target.classList.contains('epi')) {
        verificar_placeholder(event.target)
    }
    if (event.target.id === 'nr') {
        verificar_placeholder(event.target)
    }
})


search_btn.addEventListener('click', () => {
    search_form.submit()
})