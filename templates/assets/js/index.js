const login = document.querySelector('.login')
const cadastro = document.querySelector('.cadastro')

login.addEventListener('click', () => {
    window.location.href = 'View/login.php'
})

cadastro.addEventListener('click', () => {
    window.location.href = 'View/cadastro.php'
})