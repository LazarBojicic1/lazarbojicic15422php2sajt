document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('title-request-form');
    const titleEl = document.getElementById('requested_title');
    const messageEl = document.getElementById('request_message');
    const submitBtn = document.getElementById('title-request-submit');
    const titleFb = document.getElementById('requested-title-feedback');
    const messageFb = document.getElementById('request-message-feedback');

    if (!form || !titleEl || !messageEl || !submitBtn) {
        return;
    }

    const titleRegex = /^(?=.{2,150}$)[\p{L}\p{N}][\p{L}\p{N}\s\-':&.,!?()\/+#]*$/u;
    const messageRegex = /^(?=.{0,1000}$)[\p{L}\p{N}\s\-':&.,!?()\/+#"\n\r]*$/u;
    const state = {
        title: false,
        message: true,
    };

    function showFeedback(el, valid, message) {
        if (!el) {
            return;
        }

        el.classList.remove('hidden', 'text-emerald-400/70', 'text-red-400/70');
        el.textContent = message;
        el.classList.add(valid ? 'text-emerald-400/70' : 'text-red-400/70');
    }

    function hideFeedback(el) {
        el?.classList.add('hidden');
    }

    function setBorder(input, valid) {
        input.classList.remove('border-emerald-500/30', 'border-red-500/30', 'border-white/[0.08]');

        if (valid === null) {
            input.classList.add('border-white/[0.08]');
        } else if (valid) {
            input.classList.add('border-emerald-500/30');
        } else {
            input.classList.add('border-red-500/30');
        }
    }

    function updateSubmitState() {
        const isValid = state.title && state.message;

        submitBtn.disabled = !isValid;
        submitBtn.classList.toggle('bg-red-600', isValid);
        submitBtn.classList.toggle('hover:bg-red-500', isValid);
        submitBtn.classList.toggle('text-white', isValid);
        submitBtn.classList.toggle('bg-red-600/40', !isValid);
        submitBtn.classList.toggle('text-white/40', !isValid);
        submitBtn.classList.toggle('cursor-not-allowed', !isValid);
    }

    function validateTitle() {
        const value = titleEl.value.trim();

        if (value.length === 0) {
            state.title = false;
            showFeedback(titleFb, false, 'Requested title is required.');
            setBorder(titleEl, false);
            return;
        }

        if (!titleRegex.test(value)) {
            state.title = false;
            showFeedback(titleFb, false, 'Use letters, numbers, spaces, and common punctuation only.');
            setBorder(titleEl, false);
            return;
        }

        state.title = true;
        showFeedback(titleFb, true, 'Looks good.');
        setBorder(titleEl, true);
    }

    function validateMessage() {
        const value = messageEl.value.trim();

        if (value.length === 0) {
            state.message = true;
            hideFeedback(messageFb);
            setBorder(messageEl, null);
            return;
        }

        if (!messageRegex.test(value)) {
            state.message = false;
            showFeedback(messageFb, false, 'Notes can only use letters, numbers, spaces, and common punctuation.');
            setBorder(messageEl, false);
            return;
        }

        state.message = true;
        showFeedback(messageFb, true, 'Notes format looks good.');
        setBorder(messageEl, true);
    }

    titleEl.addEventListener('input', () => {
        validateTitle();
        updateSubmitState();
    });

    messageEl.addEventListener('input', () => {
        validateMessage();
        updateSubmitState();
    });

    form.addEventListener('submit', (event) => {
        validateTitle();
        validateMessage();
        updateSubmitState();

        if (!state.title || !state.message) {
            event.preventDefault();
        }
    });

    if (titleEl.value.trim() !== '') {
        validateTitle();
    } else {
        setBorder(titleEl, null);
    }

    if (messageEl.value.trim() !== '') {
        validateMessage();
    } else {
        setBorder(messageEl, null);
    }

    updateSubmitState();
});
