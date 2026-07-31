document.addEventListener('DOMContentLoaded', () => {

  const profileForm = document.getElementById('profileForm');
  const allInputs = Array.from(profileForm.querySelectorAll('.field__input'));
  const editButtons = document.querySelectorAll('[data-edit-target]');
  const statusMsg = document.getElementById('statusMsg');
  const btnSave = document.querySelector('.btn--save')

  const originalValues = new Map();
  allInputs.forEach((input) => originalValues.set(input.id, input.value));

  const fieldsBeingEdited = new Set();

  let statusTimeout;
  function showStatus(text) {
    statusMsg.textContent = text;
    statusMsg.classList.add('is-visible');
    clearTimeout(statusTimeout);
    statusTimeout = setTimeout(() => {
      statusMsg.classList.remove('is-visible');
    }, 2500);
  }

  /* Alternar visibilidade da senha */
  const toggleSenhaBtn = document.getElementById('toggleSenha');
  const senhaInput = document.getElementById('senha');
  const eyeIcon = document.getElementById('eyeIcon');

  const eyeOpenPath = `
    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
    <circle cx="12" cy="12" r="3"></circle>
  `;
  const eyeClosedPath = `
    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a19.42 19.42 0 0 1 5.06-6.06M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a19.5 19.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
    <path d="M1 1l22 22"></path>
  `;

  toggleSenhaBtn.addEventListener('click', () => {
    const isPassword = senhaInput.type === 'password';
    senhaInput.type = isPassword ? 'text' : 'password';
    eyeIcon.innerHTML = isPassword ? eyeOpenPath : eyeClosedPath;
    toggleSenhaBtn.setAttribute('aria-pressed', String(isPassword));
    toggleSenhaBtn.setAttribute('aria-label', isPassword ? 'Ocultar senha' : 'Mostrar senha');
  });

  /* Habilitar edição de campo ao clicar no lápis */
  editButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-edit-target');
      const input = document.getElementById(targetId);
      if (!input) return;

      input.removeAttribute('readonly');
      input.classList.remove('is-saved');
      input.classList.add('is-editing');
      btn.classList.add('is-active');

      fieldsBeingEdited.add(targetId);

      input.focus();
      const value = input.value;
      input.setSelectionRange(value.length, value.length);
    });
  });

  /* Salvar Alterações: grava os valores digitados e trava os campos */
  profileForm.addEventListener('submit', (e) => {
    e.preventDefault();

    allInputs.forEach((input) => {
      originalValues.set(input.id, input.value);
      input.setAttribute('readonly', '');
      input.classList.remove('is-editing');
      input.classList.add('is-saved');
    });

    editButtons.forEach((btn) => btn.classList.remove('is-active'));
    fieldsBeingEdited.clear();

    setTimeout(() => {
      allInputs.forEach((input) => input.classList.remove('is-saved'));
    }, 1200);

    showStatus('Alterações salvas com sucesso!');
  });

  /* Fazer Logout */
  const logoutBtn = document.getElementById('logoutBtn');
  logoutBtn.addEventListener('click', () => {
    const confirmado = confirm('Tem certeza que deseja sair da sua conta?');
    if (confirmado) {
      alert('Logout realizado com sucesso!');
    }
  });

  btnSave.addEventListener('click', () => {
    profileForm.submit()
  })

});