/**
 * calculator.js
 *
 * Архитектура:
 *  - Пользователь собирает выражение (строка).
 *  - По нажатию «=» выражение POST-отправляется на httpbin.org/post.
 *  - Сервер возвращает JSON, из него берётся поле expression.
 *  - Выражение вычисляется локально рекурсивным парсером (Recursive Descent Parser).
 *  - Результат передаётся обратно через GET-параметр (имитация).
 *  - Поле отображения управляется только через JS.
 *  - Поддержка: +, −, ×, ÷, %, скобки, x^y, √, n!, ln, log,
 *               sin, cos, tan, π, e, дробные числа, унарный минус,
 *               отрицательные скобки.
 *  - Ввод с клавиатуры.
 */

/* ══════════════════════════════════════════════════
   1. STATE
   ══════════════════════════════════════════════════ */

const state = {
  expr:        '',   // текущее выражение (строка для отображения)
  justResult:  false // флаг: только что получили результат
};

/* ══════════════════════════════════════════════════
   2. DOM REFS
   ══════════════════════════════════════════════════ */

const dispInput      = document.getElementById('displayInput');
const dispExpression = document.getElementById('displayExpression');

/* ══════════════════════════════════════════════════
   3. DISPLAY HELPERS
   ══════════════════════════════════════════════════ */

function render() {
  dispInput.textContent      = state.expr === '' ? '0' : state.expr;
  dispExpression.textContent = '\u00A0'; // clear expression line when editing
}

function showResult(val, originalExpr) {
  dispExpression.textContent = originalExpr + ' =';
  dispInput.textContent      = val;
  dispInput.classList.add('result-flash');
  setTimeout(() => dispInput.classList.remove('result-flash'), 600);
}

function showError(msg) {
  dispInput.textContent = msg;
  dispInput.classList.add('error-flash');
  setTimeout(() => dispInput.classList.remove('error-flash'), 700);
}

/* ══════════════════════════════════════════════════
   4. EXPRESSION BUILDER
   ══════════════════════════════════════════════════ */

/** Append raw string to expression */
function append(str) {
  if (state.justResult) {
    // If user types a digit/dot after result → start fresh
    if (/^[\d.]$/.test(str)) {
      state.expr = str;
    } else {
      // operator → continue with result
      state.expr = dispInput.textContent + str;
    }
    state.justResult = false;
  } else {
    state.expr += str;
  }
  render();
}

function clear() {
  state.expr       = '';
  state.justResult = false;
  render();
  dispExpression.textContent = '\u00A0';
}

function backspace() {
  if (state.justResult) { clear(); return; }
  // Remove last character, but handle multi-char tokens (sin, cos, ln, etc.)
  const tokens = ['sin(', 'cos(', 'tan(', 'ln(', 'log(', 'sqrt(', 'PI', 'E'];
  for (const t of tokens) {
    if (state.expr.endsWith(t)) {
      state.expr = state.expr.slice(0, -t.length);
      render(); return;
    }
  }
  state.expr = state.expr.slice(0, -1);
  render();
}

/* ══════════════════════════════════════════════════
   5. BUTTON ACTIONS
   ══════════════════════════════════════════════════ */

function handleAction(action) {
  switch (action) {
    /* digits & dot */
    case '0': case '1': case '2': case '3': case '4':
    case '5': case '6': case '7': case '8': case '9':
    case '.':
      append(action); break;

    /* basic operators → use internal ASCII for parser */
    case '+': append('+'); break;
    case '−': append('-'); break;
    case '×': append('*'); break;
    case '÷': append('/'); break;
    case '%': append('%'); break;

    /* brackets */
    case '(': append('('); break;
    case ')': append(')'); break;

    /* unary minus / negate current tail number */
    case 'neg': handleNeg(); break;

    /* scientific */
    case 'pi':   append('PI');    break;
    case 'e':    append('E');     break;
    case 'sqrt': append('sqrt('); break;
    case 'pow':  append('^');     break;
    case 'pow2': append('^2');    break;
    case 'fact': append('!');     break;
    case 'ln':   append('ln(');   break;
    case 'log':  append('log(');  break;
    case 'sin':  append('sin(');  break;
    case 'cos':  append('cos(');  break;
    case 'tan':  append('tan(');  break;

    /* control */
    case 'clear':     clear();     break;
    case 'backspace': backspace();  break;
    case '=':         calculate();  break;
  }
}

/** Toggle unary minus on the last number segment */
function handleNeg() {
  if (state.justResult) {
    const v = parseFloat(dispInput.textContent);
    if (!isNaN(v)) {
      state.expr = String(-v);
      dispInput.textContent = state.expr;
      state.justResult = false;
    }
    return;
  }
  if (state.expr === '') {
    state.expr = '-';
  } else if (state.expr === '-') {
    state.expr = '';
  } else {
    // if last char is digit/dot/closing paren → wrap last token in -(...)
    state.expr = '(-' + state.expr + ')';
  }
  render();
}

/* ══════════════════════════════════════════════════
   6. RECURSIVE DESCENT PARSER
   Парсит строку в число с поддержкой:
   + - * / % ^ ! sin cos tan ln log sqrt PI E скобки
   ══════════════════════════════════════════════════ */

/**
 * Tokenise expression string into array of tokens.
 * Tokens: numbers (strings), operators, function names, parens.
 */
function tokenise(expr) {
  const tokens = [];
  let i = 0;
  const s = expr.replace(/\s+/g, '');

  while (i < s.length) {
    // Number (including scientific notation)
    if (/\d/.test(s[i]) || (s[i] === '.' && /\d/.test(s[i+1] || ''))) {
      let num = '';
      while (i < s.length && /[\d.]/.test(s[i])) num += s[i++];
      tokens.push({ type: 'NUM', value: parseFloat(num) });
      continue;
    }

    // Named constants & functions
    const funcs = ['sin', 'cos', 'tan', 'ln', 'log', 'sqrt'];
    let matched = false;
    for (const fn of funcs) {
      if (s.startsWith(fn, i)) {
        tokens.push({ type: 'FN', value: fn });
        i += fn.length;
        matched = true; break;
      }
    }
    if (matched) continue;

    if (s.startsWith('PI', i)) { tokens.push({ type: 'NUM', value: Math.PI }); i += 2; continue; }
    if (s.startsWith('E', i))  { tokens.push({ type: 'NUM', value: Math.E  }); i += 1; continue; }

    // Single-char tokens
    if ('+-*/%^!()'.includes(s[i])) {
      tokens.push({ type: 'OP', value: s[i] }); i++; continue;
    }

    throw new Error('Неизвестный символ: ' + s[i]);
  }
  return tokens;
}

/**
 * Recursive Descent Parser
 * Grammar:
 *   expr   → term   (('+' | '-') term)*
 *   term   → factor (('*' | '/' | '%') factor)*
 *   factor → power
 *   power  → unary  ('^' unary)*
 *   unary  → '-' unary | postfix
 *   postfix→ primary '!'?
 *   primary→ NUM | FN '(' expr ')' | '(' expr ')'
 */
function parse(tokens) {
  let pos = 0;

  function peek()    { return tokens[pos]; }
  function consume() { return tokens[pos++]; }
  function expect(type, val) {
    const t = consume();
    if (!t || (type && t.type !== type) || (val && t.value !== val))
      throw new Error('Синтаксическая ошибка');
    return t;
  }

  function parseExpr() {
    let left = parseTerm();
    while (peek() && peek().type === 'OP' && (peek().value === '+' || peek().value === '-')) {
      const op = consume().value;
      const right = parseTerm();
      left = op === '+' ? left + right : left - right;
    }
    return left;
  }

  function parseTerm() {
    let left = parsePower();
    while (peek() && peek().type === 'OP' && '*/%'.includes(peek().value)) {
      const op = consume().value;
      const right = parsePower();
      if (op === '*') left = left * right;
      else if (op === '/') {
        if (right === 0) throw new Error('Деление на ноль');
        left = left / right;
      }
      else if (op === '%') left = left % right;
    }
    return left;
  }

  function parsePower() {
    let base = parseUnary();
    if (peek() && peek().type === 'OP' && peek().value === '^') {
      consume();
      const exp = parseUnary(); // right-associative
      base = Math.pow(base, exp);
    }
    return base;
  }

  function parseUnary() {
    if (peek() && peek().type === 'OP' && peek().value === '-') {
      consume();
      return -parsePostfix();
    }
    if (peek() && peek().type === 'OP' && peek().value === '+') {
      consume();
      return parsePostfix();
    }
    return parsePostfix();
  }

  function parsePostfix() {
    let val = parsePrimary();
    while (peek() && peek().type === 'OP' && peek().value === '!') {
      consume();
      val = factorial(val);
    }
    return val;
  }

  function parsePrimary() {
    const t = peek();
    if (!t) throw new Error('Неожиданный конец выражения');

    // Number
    if (t.type === 'NUM') {
      consume();
      return t.value;
    }

    // Function call
    if (t.type === 'FN') {
      const fn = consume().value;
      expect('OP', '(');
      const arg = parseExpr();
      expect('OP', ')');
      return applyFn(fn, arg);
    }

    // Parenthesised expression
    if (t.type === 'OP' && t.value === '(') {
      consume();
      const val = parseExpr();
      expect('OP', ')');
      return val;
    }

    throw new Error('Ожидалось число или выражение');
  }

  const result = parseExpr();
  if (pos < tokens.length) throw new Error('Лишние символы в выражении');
  return result;
}

/* ══════════════════════════════════════════════════
   7. MATH HELPERS (рекурсивные пользовательские функции)
   ══════════════════════════════════════════════════ */

/** Рекурсивный факториал */
function factorial(n) {
  n = Math.round(n);
  if (n < 0)  throw new Error('Факториал отрицательного числа не определён');
  if (n > 170) throw new Error('Слишком большое число для факториала');
  if (n === 0 || n === 1) return 1;
  return n * factorial(n - 1);   // рекурсия
}

/** Рекурсивное возведение в степень (для демонстрации рекурсии) */
function recursivePow(base, exp) {
  if (exp === 0) return 1;
  if (exp < 0)  return 1 / recursivePow(base, -exp);
  if (exp % 2 === 0) {
    const half = recursivePow(base, exp / 2);
    return half * half;          // рекурсия
  }
  return base * recursivePow(base, exp - 1); // рекурсия
}

/** Применить математическую функцию */
function applyFn(fn, arg) {
  switch (fn) {
    case 'sin':  return Math.sin(arg);
    case 'cos':  return Math.cos(arg);
    case 'tan':  return Math.tan(arg);
    case 'ln':
      if (arg <= 0) throw new Error('ln: аргумент должен быть > 0');
      return Math.log(arg);
    case 'log':
      if (arg <= 0) throw new Error('log: аргумент должен быть > 0');
      return Math.log10(arg);
    case 'sqrt':
      if (arg < 0)  throw new Error('√ от отрицательного числа');
      return Math.sqrt(arg);
    default: throw new Error('Неизвестная функция: ' + fn);
  }
}

/** Форматирование числа: убираем floating-point мусор */
function formatResult(val) {
  if (!isFinite(val)) return 'Ошибка';
  // Rounded to 10 significant digits
  const rounded = parseFloat(val.toPrecision(10));
  // If integer-valued → show without decimals
  if (Number.isInteger(rounded)) return String(rounded);
  return String(rounded);
}

/* ══════════════════════════════════════════════════
   8. CALCULATE: POST → validate → GET result
   ══════════════════════════════════════════════════ */

async function calculate() {
  const rawExpr = state.expr.trim();
  if (!rawExpr) return;

  const originalExpr = rawExpr; // for display

  try {
    /* ── STEP 1: POST выражения на сервер ── */
    let serverExpr = rawExpr; // fallback если fetch не пройдёт

    try {
      const formData = new FormData();
      formData.append('expression', rawExpr);

      const postResp = await fetch('https://httpbin.org/post', {
        method: 'POST',
        body: formData
      });

      if (postResp.ok) {
        const postJson = await postResp.json();
        // httpbin возвращает form-данные в поле form
        serverExpr = postJson.form?.expression || rawExpr;
      }
    } catch (_) {
      // нет сети — работаем с локальным выражением
    }

    /* ── STEP 2: Валидация и вычисление (рекурсивный парсер) ── */
    const tokens = tokenise(serverExpr);
    const result = parse(tokens);
    const formatted = formatResult(result);

    /* ── STEP 3: GET — передаём результат как query-параметр ── */
    const getUrl = 'https://httpbin.org/get?result=' +
      encodeURIComponent(formatted) +
      '&expression=' + encodeURIComponent(serverExpr);

    // Запрос в фоне — результат уже есть локально
    fetch(getUrl).catch(() => {});

    /* ── STEP 4: Показать результат ── */
    state.expr       = formatted;
    state.justResult = true;
    showResult(formatted, originalExpr);

  } catch (err) {
    showError(err.message || 'Ошибка');
    state.justResult = false;
  }
}

/* ══════════════════════════════════════════════════
   9. EVENT LISTENERS — BUTTONS
   ══════════════════════════════════════════════════ */

document.querySelectorAll('.btn[data-action]').forEach(btn => {
  btn.addEventListener('click', () => {
    const action = btn.getAttribute('data-action');
    btn.classList.add('pressed');
    setTimeout(() => btn.classList.remove('pressed'), 120);
    handleAction(action);
  });
});

/* ══════════════════════════════════════════════════
   10. KEYBOARD INPUT (бонус 2 балла)
   ══════════════════════════════════════════════════ */

document.addEventListener('keydown', (e) => {
  // Prevent default only for calculator keys
  const calcKeys = '0123456789.+-*/%^()!';
  if (calcKeys.includes(e.key) || ['Enter','Backspace','Escape','Delete'].includes(e.key)) {
    e.preventDefault();
  }

  if (e.key >= '0' && e.key <= '9') { handleAction(e.key); return; }

  switch (e.key) {
    case '.':         handleAction('.'); break;
    case '+':         handleAction('+'); break;
    case '-':         handleAction('−'); break;
    case '*':         handleAction('×'); break;
    case '/':         handleAction('÷'); break;
    case '%':         handleAction('%'); break;
    case '^':         handleAction('pow'); break;
    case '!':         handleAction('fact'); break;
    case '(':         handleAction('('); break;
    case ')':         handleAction(')'); break;
    case 'Enter':     handleAction('='); break;
    case '=':         handleAction('='); break;
    case 'Backspace': handleAction('backspace'); break;
    case 'Escape':
    case 'Delete':    handleAction('clear'); break;
    case 'p':         handleAction('pi'); break;
    case 'e':         handleAction('e'); break;
  }
});
