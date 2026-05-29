/**
 * main.js — UI-логика: обработка нажатий, вывод результата
 */

document.addEventListener('DOMContentLoaded', function () {
  const input      = document.getElementById('equationInput');
  const solveBtn   = document.getElementById('solveBtn');
  const resultBox  = document.getElementById('result');
  const errorBox   = document.getElementById('error');

  const resOperator = document.getElementById('resOperator');
  const resPosition = document.getElementById('resPosition');
  const resValue    = document.getElementById('resValue');
  const resStep     = document.getElementById('resStep');

  // Примеры-чипы
  document.querySelectorAll('.chip').forEach(function (chip) {
    chip.addEventListener('click', function () {
      input.value = chip.dataset.eq;
      input.focus();
      solve();
    });
  });

  // Кнопка «Решить»
  solveBtn.addEventListener('click', solve);

  // Enter в поле ввода
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') solve();
  });

  // Подставить пример при первой загрузке
  input.value = 'X + 3 = 7';

  function solve() {
    const equation = input.value.trim();
    hideResult();
    hideError();

    if (!equation) {
      showError('Введите уравнение.');
      return;
    }

    try {
      const res = solveEquation(equation); // из solver.js
      showResult(res);
    } catch (err) {
      showError(err.message);
    }
  }

  function showResult(res) {
    resOperator.textContent = res.operatorName;
    resPosition.textContent = res.positionLabel;
    resValue.textContent    = 'X = ' + res.value;
    resStep.textContent     = res.step;

    resultBox.classList.remove('hidden');
    resultBox.classList.remove('pop');
    void resultBox.offsetWidth;
    resultBox.classList.add('pop');
  }

  function showError(msg) {
    errorBox.textContent = '⚠ ' + msg;
    errorBox.classList.remove('hidden');
  }

  function hideResult() { resultBox.classList.add('hidden'); }
  function hideError()  { errorBox.classList.add('hidden'); }
});
