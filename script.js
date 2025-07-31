function toggleForm() {
    const title = document.getElementById("form-title");
    const button = document.querySelector("button");
    const toggle = document.getElementById("toggle-text");
    const alertBox = document.getElementById("alert");
  
    if (title.innerText === "Login") {
      title.innerText = "Sign Up";
      button.innerText = "Sign Up";
      toggle.innerHTML = 'Already have an account? <a href="#" onclick="toggleForm()">Login</a>';
    } else {
      title.innerText = "Login";
      button.innerText = "Login";
      toggle.innerHTML = 'Don’t have an account? <a href="#" onclick="toggleForm()">Sign Up</a>';
    }
  
    alertBox.innerText = "";
  }
  
  function showSuccess() {
    const alertBox = document.getElementById("alert");
    alertBox.innerText = "✅ Success!";
  }
  