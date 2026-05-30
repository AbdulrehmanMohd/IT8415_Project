
function validateRegister() {
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    const errorDiv = document.getElementById('js-error');

    if (username.length < 3) {
        errorDiv.innerHTML = '<div class="alert alert-danger">Username must be at least 3 characters.</div>';
        return false;
    }
    if (!email.includes('@') || !email.includes('.')) {
        errorDiv.innerHTML = '<div class="alert alert-danger">Please enter a valid email address.</div>';
        return false;
    }
    if (password.length < 6) {
        errorDiv.innerHTML = '<div class="alert alert-danger">Password must be at least 6 characters.</div>';
        return false;
    }
    if (password !== confirm) {
        errorDiv.innerHTML = '<div class="alert alert-danger">Passwords do not match.</div>';
        return false;
    }
    return true;
}

function validateLogin() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('js-error');

    if (!email.includes('@')) {
        errorDiv.innerHTML = '<div class="alert alert-danger">Please enter a valid email.</div>';
        return false;
    }
    if (password.length < 1) {
        errorDiv.innerHTML = '<div class="alert alert-danger">Please enter your password.</div>';
        return false;
    }
    return true;
}

function validateProduct() {
    const name = document.getElementById('name').value.trim();
    const price = document.getElementById('price').value;
    const stock = document.getElementById('stock').value;
    const errorDiv = document.getElementById('js-error');

    if (name.length < 2) {
        errorDiv.innerHTML = '<div class="alert alert-danger">Product name must be at least 2 characters.</div>';
        return false;
    }
    if (parseFloat(price) <= 0) {
        errorDiv.innerHTML = '<div class="alert alert-danger">Price must be greater than 0.</div>';
        return false;
    }
    if (parseInt(stock) < 0) {
        errorDiv.innerHTML = '<div class="alert alert-danger">Stock cannot be negative.</div>';
        return false;
    }
    return true;
}