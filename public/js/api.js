// API Base URL
const API_BASE = '/ethioskillfactory/api';

// Generic API call function
async function apiCall(endpoint, method = 'GET', data = null) {
    const url = `${API_BASE}/${endpoint}`;
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        }
    };
    
    if (data && (method === 'POST' || method === 'PUT')) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(url, options);
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, message: 'Network error. Please try again.' };
    }
}

// Check if user is logged in
async function checkAuth() {
    const result = await apiCall('auth/check.php');
    return result;
}

// Logout function
async function logout() {
    const result = await apiCall('auth/logout.php', 'POST');
    if (result.success) {
        window.location.href = result.redirect;
    }
    return result;
}