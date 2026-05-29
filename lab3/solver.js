/**
 * solver.js — логика решения линейного уравнения с одной переменной
 *
 * Поддерживаемые форматы:
 *   X + a = b   |   a + X = b
 *   X - a = b   |   a - X = b
 *   X * a = b   |   a * X = b
 *   X / a = b   |   a / X = b
 *
 * Переменная может быть X или x.
 */

/**
 * Определяет оператор из строки выражения.
 * Возвращает символ оператора: '+', '-', '*', '/'
 */
function detectOperator(expr) {
  // Ищем оператор между двумя операндами (игнорируем унарный минус)
  const match = expr.trim().match(/[xX\d.]\s*([+\-*/])\s*[xX\d.]/);
  if (!match) throw new Error('Оператор не найден в выражении: ' + expr);
  return match[1];
}

/**
 * Определяет позицию переменной X в выражении.
 * Возвращает 'left' (X слева) или 'right' (X справа).
 */
function detectPosition(expr) {
  const trimmed = expr.trim();
  // Если выражение начинается с X/x — переменная слева
  if (/^[xX]/.test(trimmed)) return 'left';
  return 'right';
}

/**
 * Вычисляет значение переменной X.
 *
 * @param {string}  operator  — оператор (+, -, *, /)
 * @param {string}  position  — 'left' | 'right'
 * @param {number}  operand   — числовой операнд (не X)
 * @param {number}  result    — правая часть уравнения
 * @returns {{ value: number, step: string }}
 */
function computeX(operator, position, operand, result) {
  let value;
  let step;

  if (position === 'left') {
    // X op operand = result  =>  X = result inv_op operand
    switch (operator) {
      case '+': value = result - operand;  step = `X = ${result} − ${operand} = ${result - operand}`; break;
      case '-': value = result + operand;  step = `X = ${result} + ${operand} = ${result + operand}`; break;
      case '*': value = result / operand;  step = `X = ${result} ÷ ${operand} = ${result / operand}`; break;
      case '/': value = result * operand;  step = `X = ${result} × ${operand} = ${result * operand}`; break;
      default: throw new Error('Неизвестный оператор: ' + operator);
    }
  } else {
    // operand op X = result  =>  X = operand inv_op result
    switch (operator) {
      case '+': value = result - operand;  step = `X = ${result} − ${operand} = ${result - operand}`; break;
      case '-': value = operand - result;  step = `X = ${operand} − ${result} = ${operand - result}`; break;
      case '*': value = result / operand;  step = `X = ${result} ÷ ${operand} = ${result / operand}`; break;
      case '/': value = operand / result;  step = `X = ${operand} ÷ ${result} = ${operand / result}`; break;
      default: throw new Error('Неизвестный оператор: ' + operator);
    }
  }

  // Округляем до 8 знаков после запятой чтобы избежать floating-point артефактов
  value = Math.round(value * 1e8) / 1e8;

  return { value, step };
}

/**
 * Главная функция: принимает строку уравнения, возвращает результат.
 *
 * @param {string} equation — например "X + 3 = 7"
 * @returns {{
 *   operator: string,
 *   operatorName: string,
 *   position: string,
 *   positionLabel: string,
 *   value: number,
 *   step: string
 * }}
 */
function solveEquation(equation) {
  // ── 1. Разбить по знаку равенства ──
  const parts = equation.split('=');
  if (parts.length !== 2) {
    throw new Error('Уравнение должно содержать ровно один знак «=»');
  }

  const lhs = parts[0].trim(); // левая часть
  const rhs = parts[1].trim(); // правая часть

  // ── 2. Проверить, что X присутствует ──
  if (!/[xX]/.test(lhs)) {
    throw new Error('Переменная X не найдена в левой части уравнения');
  }
  if (/[xX]/.test(rhs)) {
    throw new Error('X должен быть только в левой части уравнения');
  }

  // ── 3. Определить оператор и позицию ──
  const operator = detectOperator(lhs);
  const position = detectPosition(lhs);

  // ── 4. Извлечь числовой операнд ──
  // Убираем X и оператор, остаток — число
  const numStr = lhs.replace(/[xX]/g, '').replace(operator, '').trim();
  const operand = parseFloat(numStr);
  if (isNaN(operand)) {
    throw new Error('Не удалось распознать числовой операнд: «' + numStr + '»');
  }

  // ── 5. Правая часть — число ──
  const result = parseFloat(rhs);
  if (isNaN(result)) {
    throw new Error('Правая часть уравнения не является числом: «' + rhs + '»');
  }

  // ── 6. Вычислить X ──
  if (operator === '/' && operand === 0 && position === 'left') {
    throw new Error('Деление на ноль: X / 0 не определено');
  }
  if (operator === '*' && operand === 0) {
    throw new Error('Умножение на 0: X не имеет единственного решения');
  }

  const { value, step } = computeX(operator, position, operand, result);

  // ── 7. Красивые названия ──
  const operatorNames = { '+': 'Сложение (+)', '-': 'Вычитание (−)', '*': 'Умножение (×)', '/': 'Деление (÷)' };
  const positionLabels = { left: 'Слева (X op a = b)', right: 'Справа (a op X = b)' };

  return {
    operator,
    operatorName: operatorNames[operator],
    position,
    positionLabel: positionLabels[position],
    value,
    step
  };
}
