/* ── Shared axis labels ── */
  const months = ['Jun/2025','Jul/2025','Ago/2025','Set/2025','Out/2025','Nov/2025','Dez/2025'];

  /* ── Shared grid/tick style ── */
  const gridStyle = {
    color: 'rgba(0,0,0,0.06)',
    lineWidth: 1,
  };
  const tickStyle = {
    color: '#9ca3af',
    font: { family: "'Inter', sans-serif", size: 11 },
  };

  /* ── Responsive X-axis label skip helper ── */
  function xTickCallback(val, index, ticks) {
    // On very small screens skip alternate labels
    if (window.innerWidth < 420) {
      return index % 2 === 0 ? months[index] : null;
    }
    return months[index];
  }

  /* ────────────────────────────────────
     CHART 1 — Linha: Taxa de Frequência
  ──────────────────────────────────── */
  const ctxLine = document.getElementById('chartFrequencia').getContext('2d');

  new Chart(ctxLine, {
    type: 'line',
    data: {
      labels: months,
      datasets: [{
        label: 'Taxa de Frequência',
        data: [17, 7, 23, 7, 1, 6, 12],
        borderColor: '#7c3aed',
        backgroundColor: 'transparent',
        borderWidth: 2.5,
        tension: 0.4,                 // smooth spline
        pointRadius: 5,
        pointHoverRadius: 7,
        pointBackgroundColor: '#ffffff',
        pointBorderColor: '#7c3aed',
        pointBorderWidth: 2.5,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            usePointStyle: true,
            pointStyle: 'circle',
            color: '#7c3aed',
            font: { family: "'Inter', sans-serif", size: 12 },
            padding: 16,
          }
        },
        tooltip: {
          backgroundColor: '#fff',
          borderColor: '#e5e7eb',
          borderWidth: 1,
          titleColor: '#111827',
          bodyColor: '#6b7280',
          padding: 10,
        }
      },
      scales: {
        x: {
          grid: { ...gridStyle, borderDash: [4, 4] },
          ticks: { ...tickStyle, callback: xTickCallback, maxRotation: 0 },
          border: { display: false },
        },
        y: {
          min: 0,
          max: 27,
          ticks: { ...tickStyle, stepSize: 6 },
          grid: { ...gridStyle, borderDash: [4, 4] },
          border: { display: false },
        }
      }
    }
  });

  /* ────────────────────────────────────
     CHART 2 — Barras: Treinamentos
  ──────────────────────────────────── */
  const ctxBar = document.getElementById('chartTreinamentos').getContext('2d');

  new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: months,
      datasets: [{
        label: 'Treinamentos',
        data: [45, 52, 47, 58, 65, 72, 38],
        backgroundColor: '#10b981',
        borderRadius: 4,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            usePointStyle: true,
            pointStyle: 'rect',
            color: '#10b981',
            font: { family: "'Inter', sans-serif", size: 12 },
            padding: 16,
          }
        },
        tooltip: {
          backgroundColor: '#fff',
          borderColor: '#e5e7eb',
          borderWidth: 1,
          titleColor: '#111827',
          bodyColor: '#6b7280',
          padding: 10,
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { ...tickStyle, callback: xTickCallback, maxRotation: 0 },
          border: { display: false },
        },
        y: {
          min: 0,
          max: 85,
          ticks: { ...tickStyle, stepSize: 20 },
          grid: { ...gridStyle, borderDash: [4, 4] },
          border: { display: false },
        }
      }
    }
  });