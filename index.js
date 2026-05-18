// ----------- dashboard: modal alert -----------
const modalError = document.getElementById("error-modal");

if(modalError) {
  const modal = new bootstrap.Modal(modalError);
  modal.show();
}

// ----------- overview: category transactions modal -----------
const categoryTransactionsModal = document.getElementById("categoryTransactions");

categoryTransactionsModal.addEventListener("show.bs.modal", function(event) {
  const selectedCategory = event.relatedTarget;   //get access to data-* button attribute

  const categoryId = selectedCategory.dataset.categoryId; //categoryId (JS) = category-id (HTML)
  const categoryName = selectedCategory.textContent.trim(); 
  const startDate = selectedCategory.dataset.categoryStartDate;
  const endDate = selectedCategory.dataset.categoryEndDate;
  const categoryType = selectedCategory.dataset.categoryType;

  // set modal header
  document.getElementById("transactionModalLabel").textContent = categoryName;

  //set modal hidden inputs form values
  document.getElementById("modal-category-id").value = categoryId;
  document.getElementById("start-date-details").value = startDate;
  document.getElementById("end-date-details").value = endDate;
  document.getElementById("category-type").value = categoryType;

  //fetch() POST to PHP file 
  const modalForm = document.getElementById("category-detaials-form");
  const tbody = document.querySelector("#categoryTransactions tbody");

  fetch("overview.php", { //send POST data to overview.php
      method: "POST",
      body: new FormData(modalForm)
  }) //// Use fetch Response object (response) to process server reply
  .then(response => response.text())  //convert response to HTML
  .then(tableContent => {
      tbody.innerHTML = tableContent; //insert HTML into table
  })
  .catch(error => { //show if response fail
      tbody.innerHTML = "<tr><td class='text-center text-danger' colspan='4'>Something went wrong. Try again later</td></tr>";
  });
});

// ----------- sidebar menu action -----------
const sidebarMenu = document.querySelector(".sidebar-nav-menu");

function showSidebar() {
  sidebarMenu.classList.add('active');
}

function hideSidebar() {
  sidebarMenu.classList.remove('active');
}

// ----------- custom date picker actions -----------
const selectPeriod = document.querySelector("#select-period");
const customDatesPicker = document.querySelector("#select-custom-dates");

selectPeriod.addEventListener("change", () => {
  if(selectPeriod.value === "CUSTOM") {
    customDatesPicker.hidden = false;
    selectPeriod.setAttribute("aria-expanded", "true");
  } else {
    customDatesPicker.hidden = true;
    selectPeriod.setAttribute("aria-expanded", "false");
    selectPeriod.form.submit();
  }
});