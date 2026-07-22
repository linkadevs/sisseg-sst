document.addEventListener("DOMContentLoaded", () => {

    const formChecklist = document.getElementById("formChecklist");

    const modal = document.getElementById("modalAssinatura");
    const abrirAssinatura = document.getElementById("abrirAssinatura");
    const fecharModalX = document.getElementById("fecharModalX");
    const fecharModalCancelar = document.getElementById("fecharModalCancelar");
    const btnSalvar = document.getElementById("btnSalvarAssinatura");
    const textoAssinatura = document.getElementById("textoAssinatura");
    const assinaturaBase64 = document.getElementById("assinaturaBase64");
    const canvas = document.getElementById("canvasAssinatura");
    const btnLimpar = document.getElementById("btnLimparCanvas");
    const btnFinalizar = document.getElementById("btnFinalizarChecklist");
    const erroAssinatura = document.getElementById("erroAssinatura");

    const progressoTexto = document.querySelector(".progresso-text");
    const progressoHidden = document.getElementById("progressoChecklist");

    if (
        !modal ||
        !abrirAssinatura ||
        !fecharModalX ||
        !fecharModalCancelar ||
        !btnSalvar ||
        !textoAssinatura ||
        !assinaturaBase64 ||
        !canvas ||
        !btnLimpar ||
        !btnFinalizar
    ) {
        console.error("Elementos obrigatórios não encontrados.");
        return;
    }

    const ctx = canvas.getContext("2d");

    let desenhando = false;
    let assinou = false;

    // ===========================
    // CONFIGURA CANVAS
    // ===========================

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

        btnSalvar.disabled = true;
        btnSalvar.classList.remove("ativo");

    }

    // ===========================
    // ABRIR MODAL
    // ===========================

    abrirAssinatura.addEventListener("click", () => {

        if (assinaturaBase64.value.trim() !== "") {

            if (!confirm("Deseja refazer a assinatura?")) {
                return;
            }

            assinaturaBase64.value = "";

            textoAssinatura.textContent =
                "Assinatura digital — toque para assinar";

            abrirAssinatura.classList.remove("assinado");

        }

        modal.classList.add("ativo");

        setTimeout(() => {

            configurarCanvas();

            if (assinaturaBase64.value === "") {
                limparAssinaturaTemporaria();
            }

        }, 100);

    });

    // ===========================
    // FECHAR MODAL
    // ===========================

    function fecharModal() {

        modal.classList.remove("ativo");

        if (assinaturaBase64.value.trim() === "") {
            limparAssinaturaTemporaria();
        }

    }

    fecharModalX.addEventListener("click", fecharModal);
    fecharModalCancelar.addEventListener("click", fecharModal);

    // ===========================
    // LIMPAR
    // ===========================

    btnLimpar.addEventListener("click", limparAssinaturaTemporaria);

    // ===========================
    // DESENHO MOUSE
    // ===========================

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

        btnSalvar.disabled = false;
        btnSalvar.classList.add("ativo");

    }

    function parar() {

        desenhando = false;

    }

    canvas.addEventListener("mousedown", iniciar);
    canvas.addEventListener("mousemove", desenhar);
    canvas.addEventListener("mouseup", parar);
    canvas.addEventListener("mouseleave", parar);

    // ===========================
    // TOUCH
    // ===========================

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

        btnSalvar.disabled = false;
        btnSalvar.classList.add("ativo");

    }

    canvas.addEventListener("touchstart", iniciarTouch);
    canvas.addEventListener("touchmove", desenharTouch);
    canvas.addEventListener("touchend", parar);

    // ===========================
    // SALVAR ASSINATURA
    // ===========================

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

        alert("Assinatura salva.");

    });

    // ===========================
    // CONTADORES
    // ===========================

    const grupos =
        document.querySelectorAll(".grupo_checklist");

    const todosCheckbox =
        document.querySelectorAll(
            ".grupo_checklist input[type='checkbox']"
        );

    function atualizarProgresso() {

        const total = todosCheckbox.length;

        const marcados =
            document.querySelectorAll(
                ".grupo_checklist input[type='checkbox']:checked"
            ).length;

        const porcentagem =
            Math.round((marcados / total) * 100);

        progressoTexto.textContent =
            porcentagem + "% Concluído";

        if (progressoHidden) {

            progressoHidden.value = porcentagem;

        }

    }

    grupos.forEach(grupo => {

        const contador =
            grupo.querySelector(".contador");

        const checks =
            grupo.querySelectorAll(
                "input[type='checkbox']"
            );

        function atualizarGrupo() {

            const marcados =
                grupo.querySelectorAll(
                    "input[type='checkbox']:checked"
                ).length;

            contador.textContent =
                marcados + "/" + checks.length;

        }

        checks.forEach(check => {

            check.addEventListener("change", () => {

                check
                    .closest(".item_check")
                    .classList.toggle(
                        "marcado",
                        check.checked
                    );

                atualizarGrupo();

                atualizarProgresso();

            });

        });

        atualizarGrupo();

    });

    atualizarProgresso();

    // ===========================
    // FINALIZAR
    // ===========================

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

        atualizarProgresso();

        formChecklist.submit();

    });

});