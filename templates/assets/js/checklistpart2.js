document.addEventListener("DOMContentLoaded", () => {

    const modal =
        document.getElementById("modalAssinatura");

    const abrirAssinatura =
        document.getElementById("abrirAssinatura");

    const fecharModalX =
        document.getElementById("fecharModalX");

    const fecharModalCancelar =
        document.getElementById("fecharModalCancelar");

    const btnSalvar =
        document.getElementById("btnSalvarAssinatura");

    const textoAssinatura =
        document.getElementById("textoAssinatura");

    const assinaturaBase64 =
        document.getElementById("assinaturaBase64");

    const canvas =
        document.getElementById("canvasAssinatura");

    const btnLimpar =
        document.getElementById("btnLimparCanvas");



    if (
        !modal ||
        !abrirAssinatura ||
        !canvas
    ) {
        console.log(
            "Elementos da assinatura não encontrados."
        );
        return;
    }

    const ctx =
    canvas.getContext("2d");
    ctx.lineWidth = 3;
    ctx.lineCap = "round";
    ctx.lineJoin = "round";
    ctx.strokeStyle = "#000";

    let desenhando = false;
    let assinou = false;

    function ajustarCanvas() {

        const rect =
            canvas.getBoundingClientRect();

        canvas.width =
            rect.width;

        canvas.height =
            rect.height;
    }

    ajustarCanvas();

    window.addEventListener(
        "resize",
        ajustarCanvas
    );

    abrirAssinatura.addEventListener(
        "click",
        () => {

            modal.classList.add("ativo");

            setTimeout(() => {

                const rect =
                    canvas.getBoundingClientRect();

                canvas.width =
                    rect.width;

                canvas.height =
                    rect.height;

            }, 100);

        }
    );

    fecharModalX.addEventListener(
        "click",
        () => modal.classList.remove("ativo")
    );

    fecharModalCancelar.addEventListener(
        "click",
        () => modal.classList.remove("ativo")
    );

    btnLimpar.addEventListener(
        "click",
        () => {

            ctx.clearRect(
                0,
                0,
                canvas.width,
                canvas.height
            );

            assinou = false;

            btnSalvar.classList.remove(
                "ativo"
            );
        }
    );

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

        ctx.lineWidth = 2;

        ctx.lineCap = "round";

        ctx.strokeStyle = "#0F172A";

        ctx.lineTo(
            e.offsetX,
            e.offsetY
        );

        ctx.stroke();

        assinou = true;

        btnSalvar.classList.add("ativo");
    }

    function parar() {

        desenhando = false;
    }

    canvas.addEventListener(
        "mousedown",
        iniciar
    );

    canvas.addEventListener(
        "mousemove",
        desenhar
    );

    canvas.addEventListener(
        "mouseup",
        parar
    );

    canvas.addEventListener(
        "mouseleave",
        parar
    );


    function iniciarTouch(e) {

        e.preventDefault();

        const rect =
            canvas.getBoundingClientRect();

        const touch =
            e.touches[0];

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

        const rect =
            canvas.getBoundingClientRect();

        const touch =
            e.touches[0];

        ctx.lineWidth = 2;

        ctx.lineCap = "round";

        ctx.strokeStyle = "#0F172A";

        ctx.lineTo(
            touch.clientX - rect.left,
            touch.clientY - rect.top
        );

        ctx.stroke();

        assinou = true;

        btnSalvar.classList.add("ativo");
    }

    canvas.addEventListener(
        "touchstart",
        iniciarTouch
    );

    canvas.addEventListener(
        "touchmove",
        desenharTouch
    );

    canvas.addEventListener(
        "touchend",
        parar
    );




    btnSalvar.addEventListener(
        "click",
        () => {

            if (!assinou) {

                alert(
                    "Faça a assinatura primeiro."
                );

                return;
            }

            assinaturaBase64.value =
                canvas.toDataURL();

            textoAssinatura.innerHTML =
                "✓ Assinado digitalmente";

            abrirAssinatura.classList.add(
                "assinado"
            );

            modal.classList.remove(
                "ativo"
            );
        }
    );

    document
.querySelectorAll('.item_check input')
.forEach(checkbox => {

    checkbox.addEventListener('change', () => {

        const item =
        checkbox.closest('.item_check');

        if(checkbox.checked){

            item.classList.add('marcado');

        }else{

            item.classList.remove('marcado');

        }

    });

});
});

