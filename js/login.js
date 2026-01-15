// Login page functionality
document.addEventListener('DOMContentLoaded', function() {
    setupFormToggle();
    setupPasswordToggle();
    setupLoginForm();
    setupSignupForm();
    setupTermsModal();
});

function setupFormToggle() {
    const loginBtn = document.getElementById('loginBtn');
    const signupBtn = document.getElementById('signupBtn');
    const formContainer = document.getElementById('formContainer');
    
    loginBtn.addEventListener('click', () => {
        formContainer.style.transform = 'translateX(0%)';
        loginBtn.classList.add('active');
        signupBtn.classList.remove('active');
    });
    
    signupBtn.addEventListener('click', () => {
        formContainer.style.transform = 'translateX(-50%)';
        signupBtn.classList.add('active');
        loginBtn.classList.remove('active');
    });
}

function setupPasswordToggle() {
    document.querySelectorAll('.toggle-password').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const inputId = this.getAttribute('data-toggle');
            const input = document.getElementById(inputId);
            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            this.src = isPassword
                ? 'https://cdn-icons-png.flaticon.com/512/159/159604.png'
                : 'https://cdn-icons-png.flaticon.com/512/565/565655.png';
        });
    });
}

function setupLoginForm() {
    const form = document.getElementById('loginFormElement');
    const messageEl = document.getElementById('loginMessage');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const login_id = document.getElementById('login_id').value.trim();
        const password = document.getElementById('login_password').value;
        
        if (!login_id || !password) {
            showMessage(messageEl, 'Please fill in all fields', 'error');
            return;
        }
        
        // Get reCAPTCHA token
        const recaptchaToken = grecaptcha.getResponse(getRecaptchaIndex('loginRecaptcha'));
        if (!recaptchaToken) {
            showMessage(messageEl, 'Please complete the reCAPTCHA', 'error');
            return;
        }
        
        try {
            const response = await fetch('api/client-login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({
                    login_id: login_id,
                    login_password: password,
                    recaptcha_token: recaptchaToken
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Store user info in localStorage
                localStorage.setItem('client_logged_in', 'true');
                localStorage.setItem('client_username', login_id);
                
                showMessage(messageEl, data.message, 'success');
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 1500);
            } else {
                showMessage(messageEl, data.message, 'error');
                grecaptcha.reset(getRecaptchaIndex('loginRecaptcha'));
            }
        } catch (error) {
            showMessage(messageEl, 'An error occurred. Please try again.', 'error');
            console.error('Login error:', error);
        }
    });
}

function setupSignupForm() {
    const form = document.getElementById('signupFormElement');
    const messageEl = document.getElementById('signupMessage');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const username = document.getElementById('signup_username').value.trim();
        const first_name = document.getElementById('signup_first_name').value.trim();
        const last_name = document.getElementById('signup_last_name').value.trim();
        const email = document.getElementById('signup_email').value.trim();
        const contact_number = document.getElementById('signup_contact_number').value.trim();
        const password = document.getElementById('signup_password').value;
        const confirm_password = document.getElementById('signup_confirm_password').value;
        const agreeTerms = document.getElementById('agreeTerms').checked;
        
        if (!username || !first_name || !last_name || !email || !contact_number || !password || !confirm_password) {
            showMessage(messageEl, 'All fields are required', 'error');
            return;
        }
        
        if (!agreeTerms) {
            showMessage(messageEl, 'You must agree to the Terms & Privacy Policy', 'error');
            return;
        }
        
        if (password !== confirm_password) {
            showMessage(messageEl, 'Passwords do not match', 'error');
            return;
        }
        
        // Get reCAPTCHA token
        const recaptchaToken = grecaptcha.getResponse(getRecaptchaIndex('signupRecaptcha'));
        if (!recaptchaToken) {
            showMessage(messageEl, 'Please complete the reCAPTCHA', 'error');
            return;
        }
        
        try {
            const response = await fetch('api/client-signup.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({
                    username: username,
                    first_name: first_name,
                    last_name: last_name,
                    email: email,
                    contact_number: contact_number,
                    password: password,
                    confirm_password: confirm_password,
                    recaptcha_token: recaptchaToken
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage(messageEl, data.message, 'success');
                form.reset();
                grecaptcha.reset(getRecaptchaIndex('signupRecaptcha'));
                
                // Switch to login form
                setTimeout(() => {
                    document.getElementById('loginBtn').click();
                }, 1500);
            } else {
                showMessage(messageEl, data.message, 'error');
                grecaptcha.reset(getRecaptchaIndex('signupRecaptcha'));
            }
        } catch (error) {
            showMessage(messageEl, 'An error occurred. Please try again.', 'error');
            console.error('Signup error:', error);
        }
    });
}

function setupTermsModal() {
    const termsLink = document.getElementById('termsLink');
    const termsModal = document.getElementById('termsModal');
    const closeButton = document.querySelector('.close-button');
    
    termsLink.addEventListener('click', function(e) {
        e.preventDefault();
        termsModal.style.display = 'flex';
    });
    
    closeButton.addEventListener('click', function() {
        termsModal.style.display = 'none';
    });
    
    window.addEventListener('click', function(e) {
        if (e.target === termsModal) {
            termsModal.style.display = 'none';
        }
    });
}

function showMessage(element, message, type) {
    element.textContent = message;
    element.className = 'message ' + type;
    element.style.color = type === 'error' ? 'red' : 'green';
}

function getRecaptchaIndex(elementId) {
    // This is a simplified approach. In production, you might need a more robust solution
    const element = document.getElementById(elementId);
    const index = Array.from(document.querySelectorAll('.g-recaptcha')).indexOf(element);
    return index >= 0 ? index : 0;
}
