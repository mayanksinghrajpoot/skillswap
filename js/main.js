// Mobile menu functionality
document.addEventListener('DOMContentLoaded', () => {
    // Password strength validation
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', (e) => {
            const password = e.target.value;
            const strength = checkPasswordStrength(password);
            updatePasswordStrengthIndicator(strength);
        });
    }

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!form.checkValidity()) {
                e.preventDefault();
                showFormErrors(form);
            }
        });
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    
    // Length check
    if (password.length >= 8) strength++;
    
    // Contains number
    if (/\d/.test(password)) strength++;
    
    // Contains letter
    if (/[a-zA-Z]/.test(password)) strength++;
    
    // Contains special character
    if (/[!@#$%^&*]/.test(password)) strength++;
    
    return strength;
}

function updatePasswordStrengthIndicator(strength) {
    const indicator = document.getElementById('password-strength');
    if (!indicator) return;

    const messages = [
        'Weak',
        'Fair',
        'Good',
        'Strong'
    ];

    const colors = [
        'bg-red-500',
        'bg-yellow-500',
        'bg-blue-500',
        'bg-green-500'
    ];

    // Remove all color classes
    colors.forEach(color => indicator.classList.remove(color));
    
    // Add appropriate color class
    indicator.classList.add(colors[strength - 1]);
    indicator.textContent = messages[strength - 1];
}

function showFormErrors(form) {
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        if (!input.validity.valid) {
            const errorMessage = getErrorMessage(input);
            showInputError(input, errorMessage);
        }
    });
}

function getErrorMessage(input) {
    if (input.validity.valueMissing) {
        return 'This field is required';
    }
    if (input.validity.typeMismatch) {
        return `Please enter a valid ${input.type}`;
    }
    if (input.validity.tooShort) {
        return `Must be at least ${input.minLength} characters`;
    }
    if (input.validity.tooLong) {
        return `Must be no more than ${input.maxLength} characters`;
    }
    return 'Invalid input';
}

function showInputError(input, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-red-500 text-sm mt-1';
    errorDiv.textContent = message;

    // Remove any existing error message
    const existingError = input.parentNode.querySelector('.text-red-500');
    if (existingError) {
        existingError.remove();
    }

    input.parentNode.appendChild(errorDiv);
    input.classList.add('border-red-500');

    // Remove error message when input is corrected
    input.addEventListener('input', () => {
        if (input.validity.valid) {
            errorDiv.remove();
            input.classList.remove('border-red-500');
        }
    });
}

// Intersection Observer for animations
const animateOnScroll = () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.animate-on-scroll').forEach(element => {
        observer.observe(element);
    });
};

// Initialize animations
document.addEventListener('DOMContentLoaded', animateOnScroll);