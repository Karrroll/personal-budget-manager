// ----------- sidebar menu action -----------
const sidebarMenu = document.querySelector(".sidebar-nav-menu");

function showSidebar() {
  sidebarMenu.classList.add('active');
}

function hideSidebar() {
  sidebarMenu.classList.remove('active');
}

// ----------- custom date actions -----------
const selectPeriod = document.querySelector("#select-period");
const customDatesPicker = document.querySelector("#select-custom-dates");

selectPeriod.addEventListener("change", () => {
  if(selectPeriod.value === "4") {
    customDatesPicker.hidden = false;
    selectPeriod.setAttribute("aria-expanded", "true");
  } else {
    customDatesPicker.hidden = true;
    selectPeriod.setAttribute("aria-expanded", "false");
  }
});

// ----------- progress ring animation -----------
const totalIncome = 1470;  // TEMPORARY VALUE! Value will be calculated 
const totalExpense = 809; // TEMPORARY VALUE! Value will be calculated
const totalBalance = totalIncome + totalExpense;

// Set pathLength=100 on all SVG rings - allows calculate stroke-dasharray as percentages (0-100).
const PATH_LENGTH = 100;

document.querySelectorAll('.ring').forEach(circle => {
  circle.setAttribute('pathLength', PATH_LENGTH);
  circle.style.setProperty('--total-path-length', PATH_LENGTH);
});

// calculate % of total balance
const incomePercentScore = totalBalance ? Math.round((totalIncome / totalBalance) * 100) : 0;
const expensePercentScore = totalBalance ? 100 - incomePercentScore : 0;

// Convert percentages to semi-ring values (half ring) => Ring Value range: 0-50 
const greenRingValue = Math.max(0, Math.min(50, 0.5 * incomePercentScore));
const redRingValue = Math.max(0, Math.min(50, 0.5 * expensePercentScore));


// SET PROGRESS RING VALUE
/**
 * Updates CSS custom properties for progress ring animation
 * @param {string} selector - CSS selector for the ring element
 * @param {number} value - Progress value: 0-50 => half ring 
 * @throws {Error} If element not found or value is invalid
 */

function updateRingVariables(selector, value) {
  // Parameter validation
  if (typeof selector !== 'string' || !selector.trim()) {
    console.error('Invalid selector provided to updateRingVariables');
    return;
  }
  
  if (typeof value !== 'number' || value < 0 || value > 50) {
    console.error(`Invalid value: ${value}. Must be a number between 0-50`);
    return;
  }
  
  // Element existence check
  const element = document.querySelector(selector);
  if (!element) {
    console.error(`Element not found for selector: ${selector}`);
    return;
  }
  
  // Update CSS variables with error handling
  try {
    element.style.setProperty('--progress-value', value);
    element.style.setProperty('--gap-value', PATH_LENGTH - value);
  } catch (error) {
    console.error(`Error updating ring variables for ${selector}:`, error);
  }
}


// DISPLAY PROGRESS RING SCORE [%]
function displayScore(income, expense) {
  //  set income/expense percent score
  const incomeScoreElement = document.querySelector('.ring-score .income');
  const expenseScoreElement = document.querySelector('.ring-score .expense');

  if (!incomeScoreElement || !expenseScoreElement) {
    console.error('Cannot update ring score: .income or .expense element not found');
    return;
  }

  incomeScoreElement.textContent = income;
  expenseScoreElement.textContent = expense;

  // update aria-label ring-score
  document.querySelector('.ring-score').setAttribute(
    'aria-label',
    `Income ${income}%, Expense ${expense}%`
  );
}
  

// UPDATE FINANCIAL FEEDBACK
const positiveFeedback = document.querySelector('.financial-feedback .positive');
const negativeFeedback = document.querySelector('.financial-feedback .negative');
const neutralFeedback = document.querySelector('.financial-feedback .neutral');
const noTransactionsFeedback = document.querySelector('.financial-feedback .no-transactions');

if (incomePercentScore === 0 && expensePercentScore === 0)
  noTransactionsFeedback
    ? noTransactionsFeedback.style.display = 'block'
    : console.error('Not found: .financial-feedback .no-transactions element');
else if (incomePercentScore >= 55 && expensePercentScore <= 45)
  positiveFeedback
    ? positiveFeedback.style.display = 'block'
    : console.error('Not found: .financial-feedback .positive element');
else if (incomePercentScore <= 45 && expensePercentScore >= 55)
  negativeFeedback
    ? negativeFeedback.style.display = 'block'
    : console.error('Not found: .financial-feedback .negative element');
else if ((incomePercentScore > 45 && incomePercentScore < 55) &&
         (expensePercentScore > 45 && expensePercentScore < 55))
  neutralFeedback
    ? neutralFeedback.style.display = 'block'
    : console.error('Not found: .financial-feedback .neutral element');
else
  console.error(`Invalid data. Check income[%]: ${incomePercentScore} and expense[%]: ${expensePercentScore} values.`);


// SHOW INCOME AND EXPENSE TOTAL AMOUNT TRANSACTIONS
function totalTransactionsAmount(income, expense) {
  // income table
  const incomeTotalAmountElement = document.querySelector('#income-summary .total-amount');

  incomeTotalAmountElement.textContent = income;
  incomeTotalAmountElement.parentElement.setAttribute(
    'aria-label',
    `Total income: ${income} PLN`
  );

  // expense table
  const expenseTotalAmountElement = document.querySelector('#expense-summary .total-amount');

  expenseTotalAmountElement.textContent = expense;
  expenseTotalAmountElement.parentElement.setAttribute(
    'aria-label',
    `Total expense: ${expense} PLN`
  );
}

updateRingVariables('.ring.green', greenRingValue);
updateRingVariables('.ring.red', redRingValue);
displayScore(incomePercentScore, expensePercentScore);  // display progress ring financial score
totalTransactionsAmount(totalIncome, totalExpense); // display total balance in tables