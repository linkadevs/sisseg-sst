document.addEventListener("DOMContentLoaded", () => {

    const modal = document.getElementById("modalAssinatura");
    const abrirAssinatura = document.getElementById("abrirAssinatura");
    const fecharModalX = document.getElementById("fecharModalX");
    const fecharModalCancelar = document.getElementById("fecharModalCancelar");
    const btnSalvar = document.getElementById("btnSalvarAssinatura");
    const textoAssinatura = document.getElementById("textoAssinatura");
    const assinaturaBase64 = document.getElementById("assinaturaBase64");
    const canvas = document.getElementById("canvasAssinatura");
    const btnLimpar = document.getElementById("btnLimparCanvas");

    if (
        !modal ||
        !abrirAssinatura ||
        !fecharModalX ||
        !fecharModalCancelar ||
        !btnSalvar ||
        !textoAssinatura ||
        !assinaturaBase64 ||
        !canvas ||
        !btnLimpar
    ) {
        console.error("Elementos da assinatura não encontrados.");
        return;
    }

    const ctx = canvas.getContext("2d");

    let desenhando = false;
    let assinou = false;

    function configurarCanvas() {
        const rect = canvas.getBoundingClientRect();

        canvas.width = rect.width;
        canvas.height = rect.height;

        ctx.lineWidth = 3;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";
        ctx.strokeStyle = "#000";
    }

    function canvasEstaVazio() {
        const pixels = ctx.getImageData(
            0,
            0,
            canvas.width,
            canvas.height
        ).data;

        return !pixels.some(pixel => pixel !== 0);
    }

    function limparAssinaturaTemporaria() {
        ctx.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );

        assinou = false;

        btnSalvar.classList.remove("ativo");
        btnSalvar.disabled = true;
    }

    // =========================
    // ABRIR ASSINATURA
    // =========================
    abrirAssinatura.addEventListener("click", () => {

        if (assinaturaBase64.value.trim() !== "") {

            const confirmar = confirm(
                "A assinatura já foi realizada. Tem certeza que deseja refazer?"
            );

            if (!confirmar) {
                return;
            }

            assinaturaBase64.value = "";

            textoAssinatura.innerHTML =
                "Assinatura digital — toque para assinar";

            abrirAssinatura.classList.remove("assinado");

            assinou = false;

            btnSalvar.classList.remove("ativo");
            btnSalvar.disabled = true;
        }

        modal.classList.add("ativo");

        setTimeout(() => {
            configurarCanvas();

            if (!assinaturaBase64.value) {
                limparAssinaturaTemporaria();
            }
        }, 100);
    });

    // =========================
    // FECHAR MODAL
    // =========================
    function fecharModal() {
        modal.classList.remove("ativo");

        // Se ainda não salvou a assinatura,
        // limpa o desenho temporário.
        if (assinaturaBase64.value.trim() === "") {
            limparAssinaturaTemporaria();
        }
    }

    fecharModalX.addEventListener("click", fecharModal);
    fecharModalCancelar.addEventListener("click", fecharModal);

    // =========================
    // LIMPAR
    // =========================
    btnLimpar.addEventListener("click", () => {
        limparAssinaturaTemporaria();
    });

    // =========================
    // DESENHO MOUSE
    // =========================
    function iniciar(e) {
        desenhando = true;

        ctx.beginPath();

        ctx.moveTo(
            e.offsetX,
            e.offsetY
        );
    }

    function desenhar(e) {
        if (!desenhando) return;

        ctx.lineTo(
            e.offsetX,
            e.offsetY
        );

        ctx.stroke();

        assinou = true;

        btnSalvar.classList.add("ativo");
        btnSalvar.disabled = false;
    }

    function parar() {
        desenhando = false;
    }

    canvas.addEventListener("mousedown", iniciar);
    canvas.addEventListener("mousemove", desenhar);
    canvas.addEventListener("mouseup", parar);
    canvas.addEventListener("mouseleave", parar);

    // =========================
    // DESENHO TOUCH
    // =========================
    function iniciarTouch(e) {
        e.preventDefault();

        const rect = canvas.getBoundingClientRect();
        const touch = e.touches[0];

        desenhando = true;

        ctx.beginPath();

        ctx.moveTo(
            touch.clientX - rect.left,
            touch.clientY - rect.top
        );
    }

    function desenharTouch(e) {
        if (!desenhando) return;

        e.preventDefault();

        const rect = canvas.getBoundingClientRect();
        const touch = e.touches[0];

        ctx.lineTo(
            touch.clientX - rect.left,
            touch.clientY - rect.top
        );

        ctx.stroke();

        assinou = true;

        btnSalvar.classList.add("ativo");
        btnSalvar.disabled = false;
    }

    canvas.addEventListener("touchstart", iniciarTouch);
    canvas.addEventListener("touchmove", desenharTouch);
    canvas.addEventListener("touchend", parar);

    // =========================
    // CHECKBOXES
    // =========================
    document
        .querySelectorAll(".item_check input")
        .forEach((checkbox) => {

            checkbox.addEventListener("change", () => {

                const item =
                    checkbox.closest(".item_check");

                if (checkbox.checked) {
                    item.classList.add("marcado");
                } else {
                    item.classList.remove("marcado");
                }
            });

        });

    const btnFinalizar = document.getElementById("btnFinalizarChecklist");
    const erroAssinatura = document.getElementById("erroAssinatura");

    // =========================
    // SALVAR ASSINATURA
    // =========================
    btnSalvar.addEventListener("click", () => {

        if (!assinou || canvasEstaVazio()) {
            alert("Faça a assinatura primeiro.");
            return;
        }

        assinaturaBase64.value =
            canvas.toDataURL("image/png");

        textoAssinatura.innerHTML =
            "✓ Assinado digitalmente";

        abrirAssinatura.classList.add("assinado");

        erroAssinatura.style.display = "none";
        abrirAssinatura.classList.remove("erro_assinatura");

        modal.classList.remove("ativo");

        alert("A assinatura foi salva.");
    });

    // =========================
    // FINALIZAR CHECKLIST
    // =========================
    btnFinalizar.addEventListener("click", () => {

        if (assinaturaBase64.value.trim() === "") {

            erroAssinatura.style.display = "block";

            abrirAssinatura.classList.add(
                "erro_assinatura"
            );

            return;
        }

        erroAssinatura.style.display = "none";

        abrirAssinatura.classList.remove(
            "erro_assinatura"
        );

        window.location.href =
            "checklistresultado.php";
    });

});