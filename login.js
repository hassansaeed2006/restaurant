document.getElementById('loginForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const form = this;
  const email = form.email.value.trim();
  const password = form.password.value;
  const alertDiv = document.getElementById('alert');
  const submitBtn = form.querySelector('button[type="submit"]');

  // Disable the submit button and show loading state
  submitBtn.disabled = true;
  submitBtn.textContent = 'Logging in...';
  alertDiv.textContent = '';
  alertDiv.style.display = 'none';

  // Basic validation
  if (!email || !password) {
    showError('Please fill in all fields');
    resetForm();
    return;
  }

  // Email format validation
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showError('Please enter a valid email address');
    resetForm();
    return;
  }

  // Create form data
  const formData = new FormData();
  formData.append('email', email);
  formData.append('password', password);

  // Send request
  fetch('login.php', {
    method: 'POST',
    body: formData,
    credentials: 'same-origin'
  })
    .then(response => {
      if (!response.ok) {
        if (response.status === 404) {
          throw new Error('Login service not found');
        }
        return response.json().then(err => {
          throw new Error(err.message || 'Server error');
        });
      }
      return response.json();
    })
    .then(data => {
      if (data.status === 'success') {
        showSuccess(data.message);
        form.reset();
        setTimeout(() => {
          window.location.href = data.redirect || 'introduction.php';
        }, 1000);
      } else {
        throw new Error(data.message || 'Login failed');
      }
    })
    .catch(error => {
      console.error('Login error:', error);
      showError(error.message || 'Connection error. Please try again.');
    })
    .finally(() => {
      resetForm();
    });
});

function showError(message) {
  const alertDiv = document.getElementById('alert');
  alertDiv.textContent = message;
  alertDiv.style.color = '#dc3545';
  alertDiv.style.display = 'block';
  alertDiv.style.padding = '10px';
  alertDiv.style.marginBottom = '15px';
  alertDiv.style.borderRadius = '4px';
  alertDiv.style.backgroundColor = '#f8d7da';
  alertDiv.style.border = '1px solid #f5c6cb';
}

function showSuccess(message) {
  const alertDiv = document.getElementById('alert');
  alertDiv.textContent = message;
  alertDiv.style.color = '#28a745';
  alertDiv.style.display = 'block';
  alertDiv.style.padding = '10px';
  alertDiv.style.marginBottom = '15px';
  alertDiv.style.borderRadius = '4px';
  alertDiv.style.backgroundColor = '#d4edda';
  alertDiv.style.border = '1px solid #c3e6cb';
}

function resetForm() {
  const submitBtn = document.querySelector('button[type="submit"]');
  submitBtn.disabled = false;
  submitBtn.textContent = 'Login';
}
