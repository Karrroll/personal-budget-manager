// ----------- modal alert -----------
document.addEventListener("DOMContentLoaded", function () {
    const modalEl = document.getElementById('error-modal');

    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
});

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
  if(selectPeriod.value === "CUSTOM") {
    customDatesPicker.hidden = false;
    selectPeriod.setAttribute("aria-expanded", "true");
  } else {
    customDatesPicker.hidden = true;
    selectPeriod.setAttribute("aria-expanded", "false");
    selectPeriod.form.submit();
  }
});