/**
 * hello.js — часы реального времени
 * Обновляет часы, минуты, секунды и дату каждую секунду
 * на основе системного времени компьютера пользователя.
 */

function pad(n) {
  return String(n).padStart(2, '0');
}

function updateClock() {
  var now = new Date();

  var hours   = pad(now.getHours());
  var minutes = pad(now.getMinutes());
  var seconds = pad(now.getSeconds());

  document.getElementById('clockHours').textContent   = hours;
  document.getElementById('clockMinutes').textContent = minutes;
  document.getElementById('clockSeconds').textContent = seconds;

  // Дата: "понедельник, 29 мая 2026"
  var dateStr = now.toLocaleDateString('ru-RU', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
  document.getElementById('clockDate').textContent = dateStr;
}

// Запустить сразу, затем каждую секунду
updateClock();
setInterval(updateClock, 1000);
