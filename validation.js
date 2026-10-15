function validateRegisterForm() {
    let firstName = document.getElementById("first_name").value.trim();
    let lastName = document.getElementById("last_name").value.trim();
    let email = document.getElementById("email").value.trim();
    let phone = document.getElementById("phone").value.trim();
    let address = document.getElementById("address").value.trim();
    let password = document.getElementById("password").value;

    if (!firstName || !lastName || !email || !phone || !address || !password) {
        alert("Please fill all fields.");
        return false;
    }
    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return false;
    }
    return true;
}

function validateLoginForm() {
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value;
    if (!email || !password) {
        alert("Email and password are required.");
        return false;
    }
    return true;
}

function validateWalletPayment() {
    const provider = document.getElementById('wallet_provider');
    const account = document.getElementById('wallet_account');
    const pin = document.getElementById('wallet_pin');

    if (!provider || provider.value.trim() === '') {
        alert('Please choose a wallet provider.');
        return false;
    }
    if (!account || account.value.trim() === '') {
        alert('Please enter your wallet email or phone.');
        return false;
    }
    if (!pin || pin.value.trim().length < 4) {
        alert('Please enter a valid wallet PIN.');
        return false;
    }
    return true;
}
