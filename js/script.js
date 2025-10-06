// login.html y register.html
const passwordInput = document.getElementById("password");
const strengthBar = document.getElementById("strengthBar");
const strengthText = document.getElementById("strengthText");

passwordInput.addEventListener("input", () => {
    const password = passwordInput.value;
    let strength = 0;

    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    switch (strength) {
        case 0:
            strengthBar.style.width = "0";
            strengthText.textContent = "";
            break;
        case 1:
            strengthBar.style.width = "25%";
            strengthBar.style.backgroundColor = "#ff4d4d";
            strengthText.style.color = "#ff4d4d";
            strengthText.textContent = "Poco segura";
            break;
        case 2:
            strengthBar.style.width = "50%";
            strengthBar.style.backgroundColor = "#ff944d";
            strengthText.style.color = "#ff944d";
            strengthText.textContent = "Medio segura";
            break;
        case 3:
            strengthBar.style.width = "75%";
            strengthBar.style.backgroundColor = "#ffff66";
            strengthText.style.color = "#ffff66";
            strengthText.textContent = "Segura";
            break;
        case 4:
            strengthBar.style.width = "100%";
            strengthBar.style.backgroundColor = "#1ece1eff";
            strengthText.style.color = "#1ece1eff";
            strengthText.textContent = "Muy segura";
            break;
    }
});

