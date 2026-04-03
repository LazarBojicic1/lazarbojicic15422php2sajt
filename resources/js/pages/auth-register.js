document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('register-form');
    const btn = document.getElementById('register-btn');
    const nameEl = document.getElementById('name');
    const emailEl = document.getElementById('email');
    const passwordEl = document.getElementById('password');
    const confirmEl = document.getElementById('password_confirmation');
    const avatarEl = document.getElementById('avatar');
    const nameFb = document.getElementById('name-feedback');
    const emailFb = document.getElementById('email-feedback');
    const confirmFb = document.getElementById('confirm-feedback');
    const avatarFb = document.getElementById('avatar-feedback');
    const pwReqs = document.getElementById('pw-requirements');
    const avatarPreview = document.getElementById('avatar-img');
    const avatarPlaceholder = document.getElementById('avatar-placeholder');

    if (!form || !btn || !nameEl || !emailEl || !passwordEl || !confirmEl || !avatarEl) {
        return;
    }

    const emailRegex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
    const nameRegex = /^[a-zA-Z0-9_]+$/;
    const allowedAvatarTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    const maxAvatarSize = 2 * 1024 * 1024;
    const state = { name: false, email: false, password: false, confirm: false, avatar: true };
    const touched = new Set();

    const pwChecks = {
        length: document.getElementById('pw-length'),
        upper: document.getElementById('pw-upper'),
        lower: document.getElementById('pw-lower'),
        number: document.getElementById('pw-number'),
        special: document.getElementById('pw-special'),
    };

    function showFeedback(el, valid, msg) {
        if (!el) {
            return;
        }

        el.classList.remove('hidden');
        el.textContent = msg;
        el.className = el.className.replace(/text-(emerald|red)-\d+\/?\d*/g, '');

        if (valid) {
            el.classList.add('text-emerald-400/70');
        } else {
            el.classList.add('text-red-400/70');
        }
    }

    function hideFeedback(el) {
        if (el) {
            el.classList.add('hidden');
        }
    }

    function setBorder(input, valid) {
        input.classList.remove('border-emerald-500/30', 'border-red-500/30', 'border-white/[0.06]');

        if (valid === null) {
            input.classList.add('border-white/[0.06]');
        } else if (valid) {
            input.classList.add('border-emerald-500/30');
        } else {
            input.classList.add('border-red-500/30');
        }
    }

    function updateBtn() {
        const allValid = state.name && state.email && state.password && state.confirm && state.avatar;
        btn.disabled = !allValid;

        if (allValid) {
            btn.classList.remove('bg-red-600/40', 'text-white/40', 'cursor-not-allowed');
            btn.classList.add('bg-red-600', 'hover:bg-red-500', 'text-white', 'cursor-pointer');
        } else {
            btn.classList.remove('bg-red-600', 'hover:bg-red-500', 'text-white', 'cursor-pointer');
            btn.classList.add('bg-red-600/40', 'text-white/40', 'cursor-not-allowed');
        }
    }

    function setPwCheck(el, pass) {
        if (!el) {
            return;
        }

        const dot = el.querySelector('.pw-dot');

        if (pass) {
            el.classList.remove('text-white/25');
            el.classList.add('text-emerald-400/70');
            dot?.classList.remove('bg-white/20');
            dot?.classList.add('bg-emerald-400');
        } else {
            el.classList.remove('text-emerald-400/70');
            el.classList.add('text-white/25');
            dot?.classList.remove('bg-emerald-400');
            dot?.classList.add('bg-white/20');
        }
    }

    function validateConfirm() {
        const value = confirmEl.value;

        if (value.length === 0) {
            state.confirm = false;
            showFeedback(confirmFb, false, 'Please confirm your password');
            setBorder(confirmEl, false);
        } else if (value !== passwordEl.value) {
            state.confirm = false;
            showFeedback(confirmFb, false, 'Passwords do not match');
            setBorder(confirmEl, false);
        } else {
            state.confirm = true;
            showFeedback(confirmFb, true, 'Passwords match');
            setBorder(confirmEl, true);
        }
    }

    nameEl.addEventListener('input', () => {
        touched.add('name');
        const value = nameEl.value;

        if (value.length === 0) {
            state.name = false;
            showFeedback(nameFb, false, 'Nickname is required');
            setBorder(nameEl, false);
        } else if (value.length < 4) {
            state.name = false;
            showFeedback(nameFb, false, `${value.length}/4 characters minimum`);
            setBorder(nameEl, false);
        } else if (value.length > 16) {
            state.name = false;
            showFeedback(nameFb, false, 'Maximum 16 characters');
            setBorder(nameEl, false);
        } else if (!nameRegex.test(value)) {
            state.name = false;
            showFeedback(nameFb, false, 'Only letters, numbers, and underscores allowed');
            setBorder(nameEl, false);
        } else {
            state.name = true;
            showFeedback(nameFb, true, 'Looks good!');
            setBorder(nameEl, true);
        }

        updateBtn();
    });

    emailEl.addEventListener('input', () => {
        touched.add('email');
        const value = emailEl.value.trim();

        if (value.length === 0) {
            state.email = false;
            showFeedback(emailFb, false, 'Email is required');
            setBorder(emailEl, false);
        } else if (!emailRegex.test(value)) {
            state.email = false;
            showFeedback(emailFb, false, 'Enter a valid email address');
            setBorder(emailEl, false);
        } else {
            state.email = true;
            showFeedback(emailFb, true, 'Valid email');
            setBorder(emailEl, true);
        }

        updateBtn();
    });

    passwordEl.addEventListener('focus', () => {
        pwReqs?.classList.remove('hidden');
    });

    passwordEl.addEventListener('input', () => {
        touched.add('password');
        pwReqs?.classList.remove('hidden');

        const value = passwordEl.value;
        const checks = {
            length: value.length >= 6 && value.length <= 30,
            upper: /[A-Z]/.test(value),
            lower: /[a-z]/.test(value),
            number: /\d/.test(value),
            special: /[^a-zA-Z\d]/.test(value),
        };

        setPwCheck(pwChecks.length, checks.length);
        setPwCheck(pwChecks.upper, checks.upper);
        setPwCheck(pwChecks.lower, checks.lower);
        setPwCheck(pwChecks.number, checks.number);
        setPwCheck(pwChecks.special, checks.special);

        state.password = Object.values(checks).every(Boolean);
        setBorder(passwordEl, value.length === 0 ? null : state.password);

        if (touched.has('confirm')) {
            validateConfirm();
        }

        updateBtn();
    });

    confirmEl.addEventListener('input', () => {
        touched.add('confirm');
        validateConfirm();
        updateBtn();
    });

    avatarEl.addEventListener('change', () => {
        const file = avatarEl.files[0];

        if (!file) {
            state.avatar = true;
            hideFeedback(avatarFb);
            avatarPreview?.classList.add('hidden');
            avatarPlaceholder?.classList.remove('hidden');
            updateBtn();
            return;
        }

        if (!allowedAvatarTypes.includes(file.type)) {
            state.avatar = false;
            showFeedback(avatarFb, false, 'Only JPG, PNG, GIF, or WebP allowed');
            avatarPreview?.classList.add('hidden');
            avatarPlaceholder?.classList.remove('hidden');
            avatarEl.value = '';
            updateBtn();
            return;
        }

        if (file.size > maxAvatarSize) {
            state.avatar = false;
            showFeedback(avatarFb, false, 'Image must be smaller than 2MB');
            avatarPreview?.classList.add('hidden');
            avatarPlaceholder?.classList.remove('hidden');
            avatarEl.value = '';
            updateBtn();
            return;
        }

        state.avatar = true;
        hideFeedback(avatarFb);

        const reader = new FileReader();
        reader.onload = (event) => {
            if (avatarPreview) {
                avatarPreview.src = event.target?.result ?? '';
                avatarPreview.classList.remove('hidden');
            }

            avatarPlaceholder?.classList.add('hidden');
        };
        reader.readAsDataURL(file);
        updateBtn();
    });

    form.addEventListener('submit', (event) => {
        touched.add('name');
        touched.add('email');
        touched.add('password');
        touched.add('confirm');

        nameEl.dispatchEvent(new Event('input'));
        emailEl.dispatchEvent(new Event('input'));
        passwordEl.dispatchEvent(new Event('input'));
        confirmEl.dispatchEvent(new Event('input'));

        if (!state.name || !state.email || !state.password || !state.confirm || !state.avatar) {
            event.preventDefault();
        }
    });

    if (nameEl.value) {
        nameEl.dispatchEvent(new Event('input'));
    }

    if (emailEl.value) {
        emailEl.dispatchEvent(new Event('input'));
    }
});
