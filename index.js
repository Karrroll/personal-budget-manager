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
const totalIncome = 299;  // TEMPORARY VALUE! Value will be calculated 
const totalExpense = 1346; // TEMPORARY VALUE! Value will be calculated
const totalBalance = totalIncome + totalExpense;

const PATH_LENGTH = 100;

// calculate % of total balance
const incomePercentScore = Math.round((totalIncome / totalBalance) * 100);
const expensePercentScore = 100 - incomePercentScore;

// Convert percentages to semi-ring values => half ring => * 0.5
const greenRingValue = 0.5 * incomePercentScore;
updateRingVeriables('.ring.green', greenRingValue); // Set CSS variables for use in <cirrcle> stroke-dasharray

const redRingValue = 0.5 * expensePercentScore;
updateRingVeriables('.ring.red', redRingValue); // Set CSS variables for use in <cirrcle> stroke-dasharray

function updateRingVeriables(selector, value) {
  document.querySelector(selector).style.setProperty('--progress-value', value);
  document.querySelector(selector).style.setProperty('--gap-value', PATH_LENGTH - value);
}

function displayScore(income, expense) {
  //  set income/expense percent score
  document.querySelector('.ring-score .income').textContent = income;
  document.querySelector('.ring-score .expense').textContent = expense;

  // update aria-label ring-score
  document.querySelector('.ring-score').setAttribute(
    'aria-label',
    `Income ${income}%, Expense ${expense}%`
  );
  
  // update financial feedback
  if (incomePercentScore >= 55)
    document.querySelector('.financial-feedback .positive').style.display = 'block';
  else if (incomePercentScore <= 45)
    document.querySelector('.financial-feedback .negative').style.display = 'block';
  else
    document.querySelector('.financial-feedback .neutral').style.display = 'block';
}

function totalTransactionsAmount(income, expense) {
  // income table
  document.querySelector('#income-summary .total-amount').textContent = income;
  document.querySelector('#income-summary tfoot th').setAttribute(
    'aria-label',
    `Total income: ${income} PLN`
  );

  // expense table
  document.querySelector('#expense-summary .total-amount').textContent = expense;
  document.querySelector('#expense-summary tfoot th').setAttribute(
    'aria-label',
    `Total expense: ${expense} PLN`
  );
}

// Set pathLength="100" on all SVG rings - allows calculate stroke-dasharray as percentages (0-100).
document.querySelectorAll('.ring').forEach(circle => {
  circle.setAttribute('pathLength', PATH_LENGTH);
  circle.style.setProperty('--total-path-length', PATH_LENGTH);
});

displayScore(incomePercentScore, expensePercentScore);  // display progress ring financial score
totalTransactionsAmount(totalIncome, totalExpense); // display total balance in tables