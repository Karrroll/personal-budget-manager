// sidebar menu action
const sidebarMenu = document.querySelector(".sidebar-nav-menu");

function showSidebar() {
  sidebarMenu.classList.add('active');
}

function hideSidebar() {
  sidebarMenu.classList.remove('active');
}

// custom date actions
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