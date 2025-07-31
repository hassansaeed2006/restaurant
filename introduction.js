
function toggleDropdown() {
    const menu = document.getElementById("dropdownMenu");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
  }
  
  function signOut() {
    localStorage.setItem("isLoggedIn", "false"); 
    window.location.href = "login.html";
  }
  