document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('login-form');
    const btn = document.getElementById('login-btn');
    const emailEl = document.getElementById('email');
    const passwordEl = document.getElementById('password');
    const emailFb = document.getElementById('email-feedback');
    const passwordFb = document.getElementById('password-feedback');

    if (!form || !btn || !emailEl || !passwordEl || !emailFb || !passwordFb) {
        return;
    }

    const emailRegex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
    const state = { email: false, password: false };

    function showFeedback(el, valid, msg) {
        el.classList.remove('hidden');
        el.textContent = msg;
        el.className = el.className.replace(/text-(emerald|red)-\d+\/?\d*/g, '');
        el.classList.add(valid ? 'text-emerald-400/70' : 'text-red-400/70');
    }

    function setBorder(input, valid) {
        input.classList.remove('border-emerald-500/30', 'border-red-500/30', 'border-white/[0.06]');

        if (valid === null) {
            input.classList.add('border-white/[0.06]');
        } else {
            input.classList.add(valid ? 'border-emerald-500/30' : 'border-red-500/30');
        }
    }

    function updateBtn() {
        const ok = state.email && state.password;
        btn.disabled = !ok;

        if (ok) {
            btn.classList.remove('bg-red-600/40', 'text-white/40', 'cursor-not-allowed');
            btn.classList.add('bg-red-600', 'hover:bg-red-500', 'text-white', 'cursor-pointer');
        } else {
            btn.classList.remove('bg-red-600', 'hover:bg-red-500', 'text-white', 'cursor-pointer');
            btn.classList.add('bg-red-600/40', 'text-white/40', 'cursor-not-allowed');
        }
    }

    emailEl.addEventListener('input', () => {
        const value = emailEl.value.trim();

        if (!value) {
            state.email = false;
            showFeedback(emailFb, false, 'Email is required');
            setBorder(emailEl, false);
        } else if (!emailRegex.test(value)) {
            state.email = false;
            showFeedback(emailFb, false, 'Enter a valid email address');
            setBorder(emailEl, false);
        } else {
            state.email = true;
            emailFb.classList.add('hidden');
            setBorder(emailEl, true);
        }

        updateBtn();
    });

    passwordEl.addEventListener('input', () => {
        const value = passwordEl.value;

        if (!value) {
            state.password = false;
            showFeedback(passwordFb, false, 'Password is required');
            setBorder(passwordEl, false);
        } else {
            state.password = true;
            passwordFb.classList.add('hidden');
            setBorder(passwordEl, true);
        }

        updateBtn();
    });

    form.addEventListener('submit', (event) => {
        emailEl.dispatchEvent(new Event('input'));
        passwordEl.dispatchEvent(new Event('input'));

        if (!state.email || !state.password) {
            event.preventDefault();
        }
    });

    if (emailEl.value) {
        emailEl.dispatchEvent(new Event('input'));
    }
});
