// API endpoints
const API_ENDPOINTS = {
    SIGNUP: '/api/signup.php',
    LOGIN: '/api/login.php',
    CONTACT: '/api/contact.php',
    NEWSLETTER: '/api/newsletter.php'
};

// Session management
const SESSION_TOKEN_KEY = 'skillswap_session';

function getSessionToken() {
    return localStorage.getItem(SESSION_TOKEN_KEY);
}

function setSessionToken(token) {
    localStorage.setItem(SESSION_TOKEN_KEY, token);
}

function clearSessionToken() {
    localStorage.removeItem(SESSION_TOKEN_KEY);
}

// API calls
async function signup(userData) {
    try {
        const response = await fetch(API_ENDPOINTS.SIGNUP, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Signup failed');
        }

        // Store session token
        if (data.sessionToken) {
            setSessionToken(data.sessionToken);
        }

        return data;
    } catch (error) {
        console.error('Signup error:', error);
        throw error;
    }
}

async function login(credentials) {
    try {
        const response = await fetch(API_ENDPOINTS.LOGIN, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(credentials)
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Login failed');
        }

        // Store session token
        if (data.sessionToken) {
            setSessionToken(data.sessionToken);
        }

        return data;
    } catch (error) {
        console.error('Login error:', error);
        throw error;
    }
}

async function submitContactForm(formData) {
    try {
        const response = await fetch(API_ENDPOINTS.CONTACT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Failed to send message');
        }

        return data;
    } catch (error) {
        console.error('Contact form error:', error);
        throw error;
    }
}

async function subscribeNewsletter(email) {
    try {
        const response = await fetch(API_ENDPOINTS.NEWSLETTER, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Newsletter subscription failed');
        }

        return data;
    } catch (error) {
        console.error('Newsletter subscription error:', error);
        throw error;
    }
}

// Form handling
document.addEventListener('DOMContentLoaded', () => {
    // Signup form
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            try {
                const formData = {
                    fullName: signupForm.querySelector('#fullName').value,
                    email: signupForm.querySelector('#email').value,
                    password: signupForm.querySelector('#password').value,
                    userType: document.querySelector('input[name="profile-type"]:checked').value,
                    skills: Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
                        .map(checkbox => checkbox.value)
                };

                const confirmPassword = signupForm.querySelector('#confirmPassword').value;
                if (formData.password !== confirmPassword) {
                    throw new Error('Passwords do not match');
                }

                const result = await signup(formData);
                showNotification('Account created successfully!', 'success');
                
                // Redirect to home page after successful signup
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 1500);
            } catch (error) {
                showNotification(error.message, 'error');
            }
        });
    }

    // Contact form
    const contactForm = document.querySelector('#contact form');
    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            try {
                const formData = {
                    name: contactForm.querySelector('#name').value,
                    email: contactForm.querySelector('#email').value,
                    message: contactForm.querySelector('#message').value
                };

                await submitContactForm(formData);
                showNotification('Message sent successfully!', 'success');
                contactForm.reset();
            } catch (error) {
                showNotification(error.message, 'error');
            }
        });
    }

    // Newsletter form
    const newsletterForm = document.querySelector('.newsletter form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            try {
                const email = newsletterForm.querySelector('input[type="email"]').value;
                await subscribeNewsletter(email);
                showNotification('Successfully subscribed to newsletter!', 'success');
                newsletterForm.reset();
            } catch (error) {
                showNotification(error.message, 'error');
            }
        });
    }
});

// Utility functions
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded shadow-lg ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}