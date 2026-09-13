/**
 * Comportamentos de interface (apenas conveniência).
 * Toda validação de verdade acontece no servidor, em PHP.
 */

/**
 * Máscara de CPF: aceita só dígitos e formata como 000.000.000-00 enquanto digita.
 * Aplicada a todo <input data-mascara="cpf">.
 */
document.querySelectorAll('input[data-mascara="cpf"]').forEach(function (campo) {
    campo.addEventListener('input', function () {
        var digitos = campo.value.replace(/\D/g, '').slice(0, 11);

        var formatado = digitos;
        if (digitos.length > 9) {
            formatado = digitos.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
        } else if (digitos.length > 6) {
            formatado = digitos.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
        } else if (digitos.length > 3) {
            formatado = digitos.replace(/(\d{3})(\d{1,3})/, '$1.$2');
        }

        campo.value = formatado;
    });
});

/**
 * Mapa de assentos: mostra os assentos escolhidos e o total estimado.
 * O valor final é sempre recalculado no servidor.
 */
(function () {
    var mapa = document.getElementById('form-ingresso');
    var dadosSessao = document.getElementById('dados-sessao');
    if (!mapa || !dadosSessao) return;

    var preco = parseFloat(dadosSessao.dataset.preco) || 0;
    var resumo = document.getElementById('resumo-assentos');
    var total = document.getElementById('total-ingressos');

    function atualizar() {
        var marcados = Array.prototype.slice.call(mapa.querySelectorAll('input[name="assentos[]"]:checked'));
        var meia = document.getElementById('tipo_meia');
        var unitario = (meia && meia.checked) ? preco / 2 : preco;

        var nomes = marcados.map(function (c) { return c.value.replace('-', ''); });
        resumo.textContent = nomes.length ? 'Selecionados: ' + nomes.join(', ') : '';
        total.textContent = nomes.length
            ? '(' + nomes.length + ' x R$ ' + unitario.toFixed(2).replace('.', ',') + ' = R$ ' + (nomes.length * unitario).toFixed(2).replace('.', ',') + ')'
            : '';
    }

    document.addEventListener('change', function (e) {
        if (e.target.name === 'assentos[]' || e.target.name === 'tipo') atualizar();
    });
    atualizar();
})();
