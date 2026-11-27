/*
 * Preenchimento automático de endereço pelo CEP (API pública ViaCEP, resposta em JSON).
 * O site funciona sem este arquivo: os campos continuam editáveis à mão.
 */
(function () {
  'use strict';

  var campoCep = document.querySelector('input[name="end_cep"]');
  if (!campoCep) return;

  var campoRua = document.querySelector('input[name="end_rua"]');
  var campoNumero = document.querySelector('input[name="end_numero"]');
  var campoCidade = document.querySelector('input[name="end_cidade"]');
  var campoEstado = document.querySelector('select[name="end_estado"]');
  var status = document.getElementById('end-cep-status');
  var ultimoCep = '';

  function mostrar(texto, tipo) {
    if (!status) return;
    status.textContent = texto;
    status.className = 'cep-status' + (tipo ? ' cep-' + tipo : '');
  }

  function preencher(dados) {
    if (dados.logradouro) campoRua.value = dados.logradouro;
    if (dados.localidade) campoCidade.value = dados.localidade;
    if (dados.uf) campoEstado.value = dados.uf;
    var partes = [dados.logradouro, dados.bairro, (dados.localidade || '') + (dados.uf ? '/' + dados.uf : '')];
    mostrar('Endereço encontrado: ' + partes.filter(Boolean).join(' - ') + '. Informe o número.', 'ok');
    if (campoNumero) campoNumero.focus();
  }

  function buscarCep() {
    var cep = campoCep.value.replace(/\D/g, '');
    if (cep.length !== 8 || cep === ultimoCep) return;
    ultimoCep = cep;
    mostrar('Buscando endereço...', '');

    fetch('https://viacep.com.br/ws/' + cep + '/json/')
      .then(function (resposta) {
        if (!resposta.ok) throw new Error('HTTP ' + resposta.status);
        return resposta.json();
      })
      .then(function (dados) {
        if (dados.erro) {
          mostrar('CEP não encontrado. Preencha o endereço manualmente.', 'erro');
          return;
        }
        preencher(dados);
      })
      .catch(function () {
        ultimoCep = '';
        mostrar('Não foi possível consultar o CEP agora. Preencha o endereço manualmente.', 'erro');
      });
  }

  campoCep.addEventListener('blur', buscarCep);
  campoCep.addEventListener('input', function () {
    if (campoCep.value.replace(/\D/g, '').length === 8) buscarCep();
  });
})();
