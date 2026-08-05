const cards = document.querySelectorAll('.card')

cards.forEach((card) => {
    card.addEventListener('click', () => {
        window.location.href = `selecao_dados_colaborador.php?id_funcao=${card.id}`
    })
})