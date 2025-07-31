document.getElementById('signupForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const form = this;
  const username = form.username.value.trim();
  const email = form.email.value.trim();
  const password = form.password.value;
  const confirmPassword = form.confirm_password.value;
  const alertDiv = document.getElementById('alert');
  const submitBtn = form.querySelector('button[type="submit"]');

  // Disable submit button and show loading state
  submitBtn.disabled = true;
  submitBtn.textContent = 'Creating Account...';
  alertDiv.textContent = '';
  alertDiv.style.display = 'none';

  // Validation
  if (!username || !email || !password || !confirmPassword) {
    showError('Please fill in all fields');
    resetForm();
    return;
  }

  if (username.length < 3) {
    showError('Username must be at least 3 characters long');
    resetForm();
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showError('Please enter a valid email address');
    resetForm();
    return;
  }

  if (password.length < 8) {
    showError('Password must be at least 8 characters long');
    resetForm();
    return;
  }

  if (password !== confirmPassword) {
    showError('Passwords do not match');
    resetForm();
    return;
  }

  const formData = new FormData();
  formData.append('username', username);
  formData.append('email', email);
  formData.append('password', password);
  formData.append('confirm_password', confirmPassword);

  fetch('signup.php', {
    method: 'POST',
    body: formData,
    credentials: 'same-origin'
  })
    .then(response => {
      if (!response.ok) {
        if (response.status === 404) {
          throw new Error('Signup service not found');
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
        if (data.redirect) {
          setTimeout(() => {
            window.location.href = data.redirect;
          }, 2000);
        }
      } else {
        throw new Error(data.message || 'Signup failed');
      }
    })
    .catch(error => {
      console.error('Signup error:', error);
      showError(error.message || 'An error occurred. Please try again.');
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
  submitBtn.textContent = 'Sign Up';
}
