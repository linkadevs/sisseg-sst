/* ------------------------------------------------------------------
       PREVIEW DAS IMAGENS SELECIONADAS
    ------------------------------------------------------------------ */
    document.getElementById('fieldFotos').addEventListener('change', function () {
      const preview = document.getElementById('uploadPreview');
      preview.innerHTML = '';

      Array.from(this.files).forEach(file => {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
          const img = document.createElement('img');
          img.src = e.target.result;
          img.alt = file.name;
          preview.appendChild(img);
        };
        reader.readAsDataURL(file);
      });
    });

    /* ------------------------------------------------------------------
       VALIDAÇÃO E ENVIO
    ------------------------------------------------------------------ */
    const REQUIRED_FIELDS = [
      { id: 'fieldTipo',       errId: 'errTipo' },
      { id: 'fieldGravidade',  errId: 'errGravidade' },
      { id: 'fieldLocal',      errId: 'errLocal' },
      { id: 'fieldAtividade',  errId: 'errAtividade' },
      { id: 'fieldDescricao',  errId: 'errDescricao' },
      { id: 'fieldAcao',       errId: 'errAcao' },
    ];

    function validateForm() {
      let valid = true;

      REQUIRED_FIELDS.forEach(({ id, errId }) => {
        const el  = document.getElementById(id);
        const err = document.getElementById(errId);
        const empty = !el.value.trim();

        el.classList.toggle('error', empty);
        err.classList.toggle('visible', empty);
        if (empty) valid = false;
      });

      return valid;
    }

    /* Remove erro ao digitar / mudar */
    REQUIRED_FIELDS.forEach(({ id, errId }) => {
      const el  = document.getElementById(id);
      const err = document.getElementById(errId);
      el.addEventListener('input',  () => { el.classList.remove('error'); err.classList.remove('visible'); });
      el.addEventListener('change', () => { el.classList.remove('error'); err.classList.remove('visible'); });
    });

    /* Submit */
    document.getElementById('incidentForm').addEventListener('submit', function (e) {
      e.preventDefault();
      if (!validateForm()) return;

      /* Coleta os dados (para integração futura) */
      const formData = {
        tipo:         document.getElementById('fieldTipo').value,
        gravidade:    document.getElementById('fieldGravidade').value,
        local:        document.getElementById('fieldLocal').value,
        atividade:    document.getElementById('fieldAtividade').value,
        descricao:    document.getElementById('fieldDescricao').value,
        testemunhas:  document.getElementById('fieldTestemunhas').value,
        acaoImediata: document.getElementById('fieldAcao').value,
        treinamento:  document.getElementById('fieldTreinamento').value,
      };

      console.log('Novo incidente:', formData);

      /* Exibe toast de sucesso */
      const toast = document.getElementById('toast');
      toast.classList.add('show');
      setTimeout(() => {
        toast.classList.remove('show');
        /* Redireciona de volta para a lista após o toast */
        setTimeout(() => history.back(), 300);
      }, 2200);
    });