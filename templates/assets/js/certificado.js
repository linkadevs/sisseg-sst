// ==========================================================================
// SISSEG OBRA — Certificado de Conclusão
// Interações: Voltar, Baixar PDF (print) e Compartilhar (Web Share API)
// ==========================================================================

document.addEventListener('DOMContentLoaded', () => {

  const btnVoltar = document.getElementById('btnVoltar');
  const btnBaixarPdf = document.getElementById('btnBaixarPdf');
  const btnCompartilhar = document.getElementById('btnCompartilhar');
  const certificateCard = document.getElementById('certificateCard');

  // --------------------------------------------------------------------
  // Botão Voltar: retorna à página anterior do histórico do navegador
  // --------------------------------------------------------------------
  btnVoltar.addEventListener('click', () => {
    if (window.history.length > 1) {
      window.history.back();
    } else {
      // Fallback: caso não haja histórico, redireciona para a home
      window.location.href = 'index.html';
    }
  });

  // --------------------------------------------------------------------
  // Botão Baixar PDF: abre a janela de impressão do navegador
  // O CSS (@media print) já esconde botões e a seção de conteúdo
  // programático, mantendo o foco apenas no card do certificado.
  // --------------------------------------------------------------------
  btnBaixarPdf.addEventListener('click', () => {
    // Pequeno feedback visual antes de imprimir
    btnBaixarPdf.disabled = true;
    const originalHTML = btnBaixarPdf.innerHTML;
    btnBaixarPdf.innerHTML = 'Gerando...';

    setTimeout(() => {
      window.print();
      btnBaixarPdf.disabled = false;
      btnBaixarPdf.innerHTML = originalHTML;
    }, 250);
  });

  // --------------------------------------------------------------------
  // Botão Compartilhar: usa a Web Share API quando disponível;
  // caso contrário, copia o link para a área de transferência.
  // --------------------------------------------------------------------
  btnCompartilhar.addEventListener('click', async () => {
    const shareData = {
      title: 'Certificado de Conclusão — SISSEG OBRA',
      text: 'Certificado NR-06 – EPIs, emitido para João Silva Santos. Código: CERT-NR06-2024-7908',
      url: window.location.href
    };

    if (navigator.share) {
      try {
        await navigator.share(shareData);
      } catch (err) {
        // Usuário cancelou o compartilhamento ou ocorreu um erro silencioso
        if (err.name !== 'AbortError') {
          console.error('Erro ao compartilhar:', err);
        }
      }
    } else if (navigator.clipboard) {
      // Fallback: copia o link para a área de transferência
      try {
        await navigator.clipboard.writeText(shareData.url);
        alert('Link do certificado copiado para a área de transferência!');
      } catch (err) {
        alert('Não foi possível copiar o link automaticamente. Copie manualmente: ' + shareData.url);
      }
    } else {
      // Fallback final: alerta simples
      alert('Compartilhamento não suportado neste navegador. Link: ' + shareData.url);
    }
  });

  // --------------------------------------------------------------------
  // Cálculo dinâmico de "dias restantes" para validade do certificado
  // (Exemplo ilustrativo: pode ser conectado a uma data real de expiração)
  // --------------------------------------------------------------------
  const diasRestantesEl = document.getElementById('diasRestantes');
  if (diasRestantesEl) {
    // Valor padrão exibido; substitua pela lógica de data real quando integrado ao backend
    diasRestantesEl.textContent = '30';
  }

});
