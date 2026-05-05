document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('login-form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        if (!username || !password) {
            alert('Please enter username and password.');
            return;
        }

        // Demo authentication: accept any non-empty credentials
        sessionStorage.setItem('carehubUser', username);

        // Redirect back to landing page
        window.location.href = 'carehub_landing.html';
    });
});
