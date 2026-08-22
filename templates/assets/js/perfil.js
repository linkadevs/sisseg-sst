document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('profileForm');
  const statusMsg = document.getElementById('statusMsg');
  const editButtons = document.querySelectorAll('[data-edit-target]');
  const allInputs = form ? Array.from(form.querySelectorAll('.field__input')) : [];

  // Mapeamento para controle de valores originais do 1º código
  const originalValues = new Map();
  allInputs.forEach((input) => originalValues.set(input.id, input.value));

  // Helper para exibir mensagens de status (do 1º código)
  let statusTimeout;
  function showStatus(text) {
    if (!statusMsg) return;
    statusMsg.textContent = text;
    statusMsg.classList.add('is-visible');

    clearTimeout(statusTimeout);
    statusTimeout = setTimeout(() => {
      statusMsg.classList.remove('is-visible');
    }, 2500);
  }

  // ---------- Mascaramento do CPF (do 2º código) ----------
  const cpfInput = document.getElementById('cpf');
  const toggleCpfBtn = document.getElementById('toggleCpf');

  function maskCpf(value) {
    return value.replace(/\d/g, '•');
  }

  if (cpfInput) {
    cpfInput.dataset.real = cpfInput.value;
    cpfInput.value = maskCpf(cpfInput.dataset.real);
  }

  if (toggleCpfBtn && cpfInput) {
    toggleCpfBtn.addEventListener('click', function () {
      const isMasked = toggleCpfBtn.getAttribute('aria-pressed') !== 'true';
      cpfInput.value = isMasked ? cpfInput.dataset.real : maskCpf(cpfInput.dataset.real);
      toggleCpfBtn.setAttribute('aria-pressed', isMasked ? 'true' : 'false');
      toggleCpfBtn.setAttribute('aria-label', isMasked ? 'Ocultar CPF' : 'Mostrar CPF');
    });
  }

  // ---------- Mostrar/ocultar senha (do 2º código, com SVG dinâmico do 1º) ----------
  const senhaInput = document.getElementById('senha');
  const toggleSenhaBtn = document.getElementById('toggleSenha');
  const eyeIcon = document.getElementById('eyeIcon');

  const eyeOpenPath = `
    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
    <circle cx="12" cy="12" r="3"></circle>
  `;
  const eyeClosedPath = `
    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a19.42 19.42 0 0 1 5.06-6.06M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a19.5 19.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
    <path d="M1 1l22 22"></path>
  `;

  if (toggleSenhaBtn && senhaInput) {
    toggleSenhaBtn.addEventListener('click', function () {
      const isHidden = senhaInput.type === 'password';
      senhaInput.type = isHidden ? 'text' : 'password';
      
      if (eyeIcon) {
        eyeIcon.innerHTML = isHidden ? eyeOpenPath : eyeClosedPath;
      }
      toggleSenhaBtn.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
      toggleSenhaBtn.setAttribute('aria-label', isHidden ? 'Ocultar senha' : 'Mostrar senha');
    });
  }

  // ---------- Edição dos campos ----------
  editButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const targetId = btn.getAttribute('data-edit-target');
      const input = document.getElementById(targetId);
      if (!input) return;

      // Se for o CPF, revela o número real ao editar
      if (targetId === 'cpf' && input.dataset.real) {
        input.value = input.dataset.real;
        if (toggleCpfBtn) {
          toggleCpfBtn.setAttribute('aria-pressed', 'true');
          toggleCpfBtn.setAttribute('aria-label', 'Ocultar CPF');
        }
      }

      // Se for a senha, limpa para digitar uma nova
      if (targetId === 'senha') {
        input.value = '';
        input.type = 'text';
        input.placeholder = 'Digite a nova senha';
        if (toggleSenhaBtn) {
          toggleSenhaBtn.setAttribute('aria-pressed', 'true');
          toggleSenhaBtn.setAttribute('aria-label', 'Ocultar senha');
          if (eyeIcon) eyeIcon.innerHTML = eyeOpenPath;
        }
      }

      input.readOnly = false;
      input.classList.remove('is-saved');
      input.classList.add('is-editing', 'field__input--editing');
      btn.classList.add('is-active');

      input.focus();
      const valLength = input.value.length;
      input.setSelectionRange(valLength, valLength); // Move o cursor para o final (do 1º código)
    });
  });

  // ---------- Envio do formulário ----------
  if (form) {
    form.addEventListener('submit', function (e) {
      // Garante envio correto dos dados mascarados ao backend
      if (cpfInput && cpfInput.readOnly) {
        cpfInput.value = cpfInput.dataset.real;
      }
      if (senhaInput && senhaInput.readOnly) {
        senhaInput.value = ''; // Não envia hash ou placeholder se não mudou
      }

      // Estilização visual pós-salvar (do 1º código)
      allInputs.forEach((input) => {
        originalValues.set(input.id, input.value);
        input.setAttribute('readonly', 'true');
        input.classList.remove('is-editing', 'field__input--editing');
        input.classList.add('is-saved');
      });

      editButtons.forEach((btn) => btn.classList.remove('is-active'));

      setTimeout(() => {
        allInputs.forEach((input) => input.classList.remove('is-saved'));
      }, 1200);

      showStatus('Alterações salvas com sucesso!');
    });
  }

  // ---------- Logout (do 2º código com confirmação do 1º) ----------
  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function () {
      const confirmado = confirm('Tem certeza que deseja sair da sua conta?');
      if (confirmado) {
        window.location.href = 'logout.php';
      }
    });
  }
});