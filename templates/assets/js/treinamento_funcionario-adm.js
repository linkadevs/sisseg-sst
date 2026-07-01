
// ── Placeholder SVG image ──
const PLACEHOLDER = `data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='260'>
  <defs>
    <linearGradient id='sky' x1='0' y1='0' x2='0' y2='1'>
      <stop offset='0%25' stop-color='%2338BDF8'/>
      <stop offset='100%25' stop-color='%237DD3FC'/>
    </linearGradient>
    <linearGradient id='ground' x1='0' y1='0' x2='0' y2='1'>
      <stop offset='0%25' stop-color='%23B45309'/>
      <stop offset='100%25' stop-color='%2392400E'/>
    </linearGradient>
  </defs>
  <rect width='400' height='260' fill='url(%23sky)'/>
  <rect y='180' width='400' height='80' fill='url(%23ground)'/>
  <rect x='320' y='30' width='6' height='150' fill='%23374151' opacity='0.6'/>
  <rect x='260' y='30' width='66' height='5' fill='%23374151' opacity='0.6'/>
  <rect x='265' y='35' width='3' height='30' fill='%23374151' opacity='0.5'/>
  <ellipse cx='155' cy='95' rx='22' ry='22' fill='%23D97706'/>
  <rect x='133' y='95' width='44' height='60' rx='8' fill='%23FBBF24'/>
  <rect x='140' y='90' width='30' height='12' rx='3' fill='%23F59E0B'/>
  <rect x='148' y='155' width='10' height='30' fill='%23374151'/>
  <rect x='162' y='155' width='10' height='30' fill='%23374151'/>
  <rect x='177' y='105' width='30' height='8' rx='4' fill='%23FBBF24' transform='rotate(-20 177 105)'/>
  <ellipse cx='245' cy='88' rx='20' ry='20' fill='%23065F46'/>
  <rect x='225' y='88' width='40' height='58' rx='8' fill='%2310B981'/>
  <rect x='232' y='84' width='26' height='10' rx='3' fill='%23059669'/>
  <rect x='236' y='146' width='9' height='28' fill='%231F2937'/>
  <rect x='248' y='146' width='9' height='28' fill='%231F2937'/>
  <ellipse cx='150' cy='88' rx='6' ry='4' fill='%23FDE68A' opacity='0.5'/>
  <ellipse cx='241' cy='82' rx='5' ry='3' fill='%2386EFAC' opacity='0.4'/>
  <rect x='30' y='60' width='4' height='130' fill='%23475569' opacity='0.5'/>
  <rect x='30' y='80' width='40' height='3' fill='%23475569' opacity='0.4'/>
  <rect x='30' y='110' width='40' height='3' fill='%23475569' opacity='0.4'/>
  <rect x='30' y='140' width='40' height='3' fill='%23475569' opacity='0.4'/>
  <rect x='70' y='60' width='4' height='130' fill='%23475569' opacity='0.5'/>
  <circle cx='340' cy='55' r='22' fill='%23FEF08A' opacity='0.85'/>
  <circle cx='340' cy='55' r='16' fill='%23FDE047'/>
</svg>`;

// ── Render course cards ──
const courses = Array.from({length: 8}, () => ({
  nr: 'NR-06', title: 'EPIs', badge: 'Válido',
  desc: 'Uso e responsabilidade de Equipamentos de Proteção Individual.',
  hours: '2 horas',
}));

function createCard(c) {
  return `
  <div class="course-card">
    <div class="course-img-wrap">
      <img src="${PLACEHOLDER}" alt="EPIs" loading="lazy">
      <span class="nr-tag">${c.nr}</span>
    </div>
    <div class="course-body">
      <div class="course-header-row">
        <span class="course-title">${c.title}</span>
        <span class="badge-valid">${c.badge}</span>
      </div>
      <p class="course-desc">${c.desc}</p>
      <div class="course-meta">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
        </svg>
        ${c.hours}
      </div>
    </div>
    <div class="course-actions">
      <button class="btn-edit">Editar</button>
      <div class="card-secondary-btns">
        <button class="btn-secondary">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
          </svg>
          Criar prova
        </button>
        <button class="btn-secondary">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="3" width="20" height="14" rx="2"/><polyline points="8 21 12 17 16 21"/>
          </svg>
          Videoaulas
        </button>
      </div>
    </div>
  </div>`;
}
document.getElementById('coursesGrid').innerHTML = courses.map(createCard).join('');

// ── Modal logic ──
const overlay = document.getElementById('modalOverlay');

function openModal() {
  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeModal() {
  overlay.classList.remove('open');
  document.body.style.overflow = '';
}

document.getElementById('btnCriarTreinamento').addEventListener('click', openModal);
document.getElementById('modalBackBtn').addEventListener('click', closeModal);
document.getElementById('btnExcluir').addEventListener('click', closeModal);
document.getElementById('btnSalvar').addEventListener('click', function() {
  alert('Treinamento salvo com sucesso!');
  closeModal();
});

// Close on backdrop click
overlay.addEventListener('click', function(e) {
  if (e.target === overlay) closeModal();
});

// ESC key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeModal();
});

// Sem validade toggle
const toggleValidade = document.getElementById('toggleSemValidade');
const inputValidade  = document.getElementById('inputValidade');
toggleValidade.addEventListener('change', function() {
  inputValidade.disabled = this.checked;
});
// Initial state
inputValidade.disabled = true;