// Handle login form
if (document.getElementById('loginForm')) {
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        const result = await apiCall('auth/login.php', 'POST', { email, password });
        
        if (result.success) {
            // Store user data in localStorage
            localStorage.setItem('user', JSON.stringify(result.data.user));
            // Redirect to dashboard
            window.location.href = result.redirect;
        } else {
            showAlert(result.message, 'error');
        }
    });
}

// Handle register form
if (document.getElementById('registerForm')) {
    document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const full_name = document.getElementById('full_name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const password = document.getElementById('password').value;
        const confirm_password = document.getElementById('confirm_password').value;
        
        const result = await apiCall('auth/register.php', 'POST', {
            full_name, email, phone, password, confirm_password
        });
        
        if (result.success) {
            showAlert(result.message, 'success');
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 2000);
        } else {
            showAlert(result.message, 'error');
        }
    });
}

// Helper function to show alerts
function showAlert(message, type) {
    const alertDiv = document.getElementById('alertMessage');
    if (alertDiv) {
        alertDiv.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
            alertDiv.innerHTML = '';
        }, 5000);
    } else {
        alert(message);
    }
}

// Check auth status on protected pages
async function requireAuth() {
    const auth = await checkAuth();
    if (!auth.success) {
        window.location.href = '/ethioskillfactory/public/login.html';
    }
    return auth;
}

// Update navigation based on login status
async function updateNavigation() {
    const auth = await checkAuth();
    const nav = document.querySelector('.nav-links');
    if (nav && auth.success) {
        // Update nav for logged in user
        console.log('User logged in:', auth.data.user);
    }
}