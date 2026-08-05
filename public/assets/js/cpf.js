function cpfMask(value) {
  var v = value.replace(/\D/g, '').slice(0, 11);
  if (v.length <= 3) return v;
  if (v.length <= 6) return v.slice(0,3) + '.' + v.slice(3);
  if (v.length <= 9) return v.slice(0,3) + '.' + v.slice(3,6) + '.' + v.slice(6);
  return v.slice(0,3) + '.' + v.slice(3,6) + '.' + v.slice(6,9) + '-' + v.slice(9,11);
}

function cpfValido(value) {
  var cpf = value.replace(/\D/g, '');
  if (cpf.length !== 11) return false;
  if (/^(\d)\1{10}$/.test(cpf)) return false;
  for (var t = 9; t < 11; t++) {
    var sum = 0;
    for (var i = 0; i < t; i++) sum += parseInt(cpf[i]) * (t + 1 - i);
    var rem = sum % 11;
    var digit = rem < 2 ? 0 : 11 - rem;
    if (parseInt(cpf[t]) !== digit) return false;
  }
  return true;
}

function cpfBindInput(inputEl, feedbackEl) {
  inputEl.addEventListener('input', function () {
    this.value = cpfMask(this.value);
    var raw = this.value.replace(/\D/g, '');
    if (feedbackEl) {
      if (raw.length === 0) {
        feedbackEl.textContent = '';
        feedbackEl.className = 'cpf-feedback';
      } else if (raw.length < 11) {
        feedbackEl.textContent = '';
        feedbackEl.className = 'cpf-feedback';
      } else if (cpfValido(this.value)) {
        feedbackEl.textContent = '✓ CPF válido';
        feedbackEl.className = 'cpf-feedback cpf-ok';
      } else {
        feedbackEl.textContent = '✗ CPF inválido';
        feedbackEl.className = 'cpf-feedback cpf-err';
      }
    }
  });
}
