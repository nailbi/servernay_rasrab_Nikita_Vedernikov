/**
 * main.js — логика для обеих страниц сайта «Feedback form»
 * Страница 1: обработка формы обратной связи
 * Страница 2: функция get_headers() и вывод результата
 */

/* ══════════════════════════════════════════
   СТРАНИЦА 1 — Форма обратной связи
   ══════════════════════════════════════════ */

(function initFeedbackForm() {
  const form = document.getElementById('feedbackForm');
  if (!form) return; // не на этой странице

  form.addEventListener('submit', function (e) {
    // Проверяем, выбран ли хотя бы один вариант ответа
    const sms   = form.querySelector('input[name="reply_sms"]');
    const email = form.querySelector('input[name="reply_email"]');

    if (!sms.checked && !email.checked) {
      e.preventDefault();
      alert('Пожалуйста, выберите хотя бы один вариант ответа (СМС или E-mail).');
      return;
    }

    // Форма отправляется стандартным образом на https://httpbin.org/post
    // (метод POST прописан в атрибуте action/method тега <form>)
  });
})();


/* ══════════════════════════════════════════
   СТРАНИЦА 2 — Вывод HTTP-заголовков
   ══════════════════════════════════════════ */

/**
 * get_headers()
 * Аналог PHP-функции get_headers():
 * делает запрос к публичному сервису httpbin.org/headers,
 * который возвращает заголовки нашего браузерного запроса,
 * и возвращает их в виде отформатированной строки.
 *
 * @returns {Promise<string>} строка с заголовками
 */
async function get_headers() {
  const response = await fetch('https://httpbin.org/headers', {
    method: 'GET',
    cache: 'no-cache'
  });

  if (!response.ok) {
    throw new Error(`Ошибка сети: ${response.status} ${response.statusText}`);
  }

  const data = await response.json();
  const headers = data.headers; // объект { "Header-Name": "value", ... }

  // Форматируем как список строк «Имя: Значение»
  const lines = Object.entries(headers).map(
    ([name, value]) => `${name}: ${value}`
  );

  return lines.join('\n');
}

(function initHeadersPage() {
  const fetchBtn      = document.getElementById('fetchBtn');
  const output        = document.getElementById('headersOutput');
  const statusText    = document.getElementById('headersStatus');

  if (!fetchBtn || !output) return; // не на этой странице

  async function loadHeaders() {
    fetchBtn.disabled = true;
    fetchBtn.textContent = 'Загрузка…';
    statusText.textContent = '';
    output.value = '';

    try {
      const result = await get_headers();
      output.value = result;
      statusText.textContent =
        `Получено заголовков: ${result.split('\n').length} — ${new Date().toLocaleTimeString('ru-RU')}`;
    } catch (err) {
      output.value = `Ошибка при получении заголовков:\n${err.message}`;
      statusText.textContent = 'Не удалось загрузить данные. Проверьте подключение к интернету.';
    } finally {
      fetchBtn.disabled = false;
      fetchBtn.textContent = 'Обновить заголовки';
    }
  }

  // Загружаем автоматически при открытии страницы
  loadHeaders();

  // И по нажатию кнопки
  fetchBtn.addEventListener('click', loadHeaders);
})();
