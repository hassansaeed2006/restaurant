// Check if user is logged in via session and fetch profile data
fetch('profile_operations.php?action=check_session')
    .then(response => response.json())
    .then(data => {
        if (!data.logged_in) {
            window.location.href = "login.html";
        } else {
            // Fetch user profile data
            return fetch('profile_operations.php?action=get_profile');
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById("profileName").textContent = data.user.username;
            document.getElementById("profileEmail").textContent = data.user.email;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.location.href = "login.html";
    });

function signOut() {
    fetch('profile_operations.php?action=logout')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = "login.html";
            } else {
                console.error('Error signing out');
                // Optionally show an alert here if needed, but showAlert is removed
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Optionally show an alert here if needed, but showAlert is removed
        });
}
