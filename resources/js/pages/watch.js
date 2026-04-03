document.addEventListener('DOMContentLoaded', () => {
    const watchRoot = document.querySelector('[data-watch-base-url]');
    const watchBaseUrl = watchRoot?.dataset.watchBaseUrl;

    // ─── Season Dropdown ───────────────────────────────────────────
    const seasonToggle = document.getElementById('season-toggle');
    const seasonMenu = document.getElementById('season-menu');
    const seasonChevron = document.getElementById('season-chevron');

    if (seasonToggle && seasonMenu && seasonChevron && watchBaseUrl) {
        seasonToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const open = !seasonMenu.classList.contains('hidden');
            seasonMenu.classList.toggle('hidden', open);
            seasonChevron.style.transform = open ? '' : 'rotate(180deg)';
        });

        seasonMenu.querySelectorAll('[data-season]').forEach((button) => {
            button.addEventListener('click', () => {
                window.location.href = `${watchBaseUrl}?s=${button.dataset.season}&e=1`;
            });
        });

        document.addEventListener('click', () => {
            seasonMenu.classList.add('hidden');
            seasonChevron.style.transform = '';
        });
    }

    // ─── Player ────────────────────────────────────────────────────
    const playerFrame = document.getElementById('player-frame');
    const playerShell = document.getElementById('player-shell');
    const fullscreenButton = document.getElementById('player-fullscreen-button');
    const sourceButtons = document.querySelectorAll('[data-player-source]');

    if (playerShell && fullscreenButton) {
        const syncFullscreenButton = () => {
            const isFullscreen = Boolean(document.fullscreenElement || document.webkitFullscreenElement);
            fullscreenButton.style.display = isFullscreen ? 'none' : 'inline-flex';
        };

        fullscreenButton.addEventListener('click', async () => {
            try {
                if (playerShell.requestFullscreen) {
                    await playerShell.requestFullscreen();
                } else if (playerShell.webkitRequestFullscreen) {
                    playerShell.webkitRequestFullscreen();
                }
            } finally {
                syncFullscreenButton();
            }
        });

        document.addEventListener('fullscreenchange', syncFullscreenButton);
        document.addEventListener('webkitfullscreenchange', syncFullscreenButton);
        syncFullscreenButton();
    }

    if (playerFrame && sourceButtons.length) {
        sourceButtons.forEach((button) => {
            button.addEventListener('click', () => {
                playerFrame.src = button.dataset.sourceUrl;

                sourceButtons.forEach((sourceButton) => {
                    const active = sourceButton === button;

                    sourceButton.setAttribute('aria-pressed', active ? 'true' : 'false');
                    sourceButton.classList.toggle('border-red-500/40', active);
                    sourceButton.classList.toggle('bg-red-500/12', active);
                    sourceButton.classList.toggle('text-red-300', active);
                    sourceButton.classList.toggle('border-white/[0.08]', !active);
                    sourceButton.classList.toggle('bg-white/[0.03]', !active);
                    sourceButton.classList.toggle('text-white/55', !active);
                    sourceButton.classList.toggle('hover:bg-white/[0.08]', !active);
                    sourceButton.classList.toggle('hover:text-white/80', !active);
                });
            });
        });
    }

    // Favorites toggle is handled globally in app.js

    // ─── Comments ──────────────────────────────────────────────────
    const commentsSection = document.getElementById('comments-section');
    if (!commentsSection) return;

    const titleId = commentsSection.dataset.titleId;
    const commentsList = document.getElementById('comments-list');
    const commentsLoader = document.getElementById('comments-loader');
    const loadMoreBtn = document.getElementById('comments-load-more');
    const commentInput = document.getElementById('comment-input');
    const commentSubmit = document.getElementById('comment-submit');
    const charCount = document.getElementById('comment-char-count');

    let nextPageUrl = `/comments/${titleId}?page=1`;

    // Load comments
    async function loadComments(append = false) {
        if (!nextPageUrl) return;
        commentsLoader.classList.remove('hidden');
        loadMoreBtn.classList.add('hidden');

        try {
            const res = await axios.get(nextPageUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (append) {
                commentsList.insertAdjacentHTML('beforeend', res.data.html);
            } else {
                commentsList.innerHTML = res.data.html;
            }
            nextPageUrl = res.data.next_page;
            if (nextPageUrl) {
                loadMoreBtn.classList.remove('hidden');
            }
            bindCommentActions();
        } catch (e) {
            commentsList.innerHTML = '<p class="text-white/20 text-[13px] py-4">Could not load comments.</p>';
        } finally {
            commentsLoader.classList.add('hidden');
        }
    }

    loadComments();

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', () => loadComments(true));
    }

    // Comment input
    if (commentInput && commentSubmit) {
        commentInput.addEventListener('input', () => {
            const len = commentInput.value.trim().length;
            charCount.textContent = `${commentInput.value.length} / 2000`;
            commentSubmit.disabled = len === 0;
        });

        commentSubmit.addEventListener('click', async () => {
            const content = commentInput.value.trim();
            if (!content) return;

            commentSubmit.disabled = true;
            try {
                const res = await axios.post('/comments', { title_id: titleId, content });
                commentsList.insertAdjacentHTML('afterbegin', res.data.html);
                commentInput.value = '';
                charCount.textContent = '0 / 2000';
                bindCommentActions();
            } catch (e) {
                const msg = e.response?.data?.message || 'Failed to post comment.';
                alert(msg);
            } finally {
                commentSubmit.disabled = false;
            }
        });
    }

    // Bind actions on comment elements
    function bindCommentActions() {
        // Reply buttons
        document.querySelectorAll('.comment-reply-btn').forEach((btn) => {
            if (btn.dataset.bound) return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', () => {
                const commentId = btn.dataset.commentId;
                // Hide all other reply forms
                document.querySelectorAll('.reply-form').forEach(f => f.classList.add('hidden'));
                const form = document.querySelector(`.reply-form[data-reply-to="${commentId}"]`);
                if (form) {
                    form.classList.remove('hidden');
                    form.querySelector('textarea')?.focus();
                }
            });
        });

        // Reply cancel
        document.querySelectorAll('.reply-cancel').forEach((btn) => {
            if (btn.dataset.bound) return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', () => {
                btn.closest('.reply-form')?.classList.add('hidden');
            });
        });

        // Reply input enable submit
        document.querySelectorAll('.reply-input').forEach((input) => {
            if (input.dataset.bound) return;
            input.dataset.bound = '1';
            input.addEventListener('input', () => {
                const submit = input.closest('.reply-form')?.querySelector('.reply-submit');
                if (submit) submit.disabled = input.value.trim().length === 0;
            });
        });

        // Reply submit
        document.querySelectorAll('.reply-submit').forEach((btn) => {
            if (btn.dataset.bound) return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', async () => {
                const form = btn.closest('.reply-form');
                const parentId = form?.dataset.replyTo;
                const input = form?.querySelector('textarea');
                const content = input?.value?.trim();
                if (!content || !parentId) return;

                btn.disabled = true;
                try {
                    const res = await axios.post('/comments', {
                        title_id: titleId,
                        parent_id: parentId,
                        content,
                    });
                    // Insert reply after the parent comment item
                    const parentItem = document.querySelector(`.comment-item[data-comment-id="${parentId}"]`);
                    if (parentItem) {
                        parentItem.insertAdjacentHTML('beforeend', res.data.html);
                    }
                    form.classList.add('hidden');
                    input.value = '';
                    bindCommentActions();
                } catch (e) {
                    const msg = e.response?.data?.message || 'Failed to post reply.';
                    alert(msg);
                } finally {
                    btn.disabled = false;
                }
            });
        });

        // Delete buttons
        document.querySelectorAll('.comment-delete-btn').forEach((btn) => {
            if (btn.dataset.bound) return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', async () => {
                if (!confirm('Delete this comment?')) return;
                const commentId = btn.dataset.commentId;
                try {
                    await axios.delete(`/comments/${commentId}`);
                    const item = document.querySelector(`.comment-item[data-comment-id="${commentId}"]`);
                    if (item) {
                        item.style.opacity = '0';
                        item.style.transition = 'opacity 0.3s ease';
                        setTimeout(() => item.remove(), 300);
                    }
                } catch {
                    alert('Failed to delete comment.');
                }
            });
        });

        // Report buttons
        document.querySelectorAll('.comment-report-btn').forEach((btn) => {
            if (btn.dataset.bound) return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', () => {
                const modal = document.getElementById('report-modal');
                const commentIdInput = document.getElementById('report-comment-id');
                if (modal && commentIdInput) {
                    commentIdInput.value = btn.dataset.commentId;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    // Reset radio
                    modal.querySelectorAll('input[name="report-reason"]').forEach(r => r.checked = false);
                    document.getElementById('report-submit').disabled = true;
                }
            });
        });
    }

    // ─── Report Modal ──────────────────────────────────────────────
    const reportModal = document.getElementById('report-modal');
    const reportCancel = document.getElementById('report-cancel');
    const reportSubmit = document.getElementById('report-submit');

    if (reportModal) {
        // Enable submit when reason selected
        reportModal.querySelectorAll('input[name="report-reason"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                reportSubmit.disabled = false;
            });
        });

        reportCancel?.addEventListener('click', () => {
            reportModal.classList.add('hidden');
            reportModal.classList.remove('flex');
        });

        reportModal.addEventListener('click', (e) => {
            if (e.target === reportModal) {
                reportModal.classList.add('hidden');
                reportModal.classList.remove('flex');
            }
        });

        reportSubmit?.addEventListener('click', async () => {
            const commentId = document.getElementById('report-comment-id')?.value;
            const reason = reportModal.querySelector('input[name="report-reason"]:checked')?.value;
            if (!commentId || !reason) return;

            reportSubmit.disabled = true;
            try {
                await axios.post(`/comments/${commentId}/report`, { reason });
                reportModal.classList.add('hidden');
                reportModal.classList.remove('flex');
                // Show brief feedback
                const btn = document.querySelector(`.comment-report-btn[data-comment-id="${commentId}"]`);
                if (btn) {
                    btn.textContent = 'Reported';
                    btn.classList.add('text-red-400/50');
                    btn.disabled = true;
                }
            } catch (e) {
                const msg = e.response?.data?.error || 'Failed to report.';
                alert(msg);
            } finally {
                reportSubmit.disabled = false;
            }
        });
    }

    // ─── Title View Tracking ───────────────────────────────────────
    if (titleId) {
        axios.post('/track-view', { title_id: titleId }).catch(() => {});
    }
});
