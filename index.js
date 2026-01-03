const sidebarMenu = document.querySelector(".sidebar-nav-menu");

function showSidebar() {
  sidebarMenu.classList.add('active');
}

function hideSidebar() {
  sidebarMenu.classList.remove('active');
}
