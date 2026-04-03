import './bootstrap';

// Navbar scroll behavior
const navbar = document.getElementById('navbar');
if (navbar) {
    const onScroll = () => {
        navbar.classList.toggle('navbar-scrolled', window.scrollY > 30);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

// Mobile navigation
(() => {
    const toggle = document.getElementById('mobile-nav-toggle');
    const panel = document.getElementById('mobile-nav-panel');
    const overlay = document.getElementById('mobile-nav-overlay');
    if (!toggle || !panel || !overlay) return;

    const setOpen = (open) => {
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        panel.classList.toggle('hidden', !open);
        overlay.classList.toggle('hidden', !open);
        document.body.classList.toggle('overflow-hidden', open);
    };

    toggle.addEventListener('click', () => {
        const open = toggle.getAttribute('aria-expanded') === 'true';
        setOpen(!open);
    });

    overlay.addEventListener('click', () => setOpen(false));

    panel.querySelectorAll('a, button[type="submit"]').forEach((element) => {
        element.addEventListener('click', () => setOpen(false));
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            setOpen(false);
        }
    });
})();

// Desktop user menu
(() => {
    const menu = document.getElementById('user-menu');
    const toggle = document.getElementById('user-menu-toggle');
    const panel = document.getElementById('user-menu-panel');
    const chevron = document.getElementById('user-menu-chevron');

    if (!menu || !toggle || !panel) {
        return;
    }

    const setOpen = (open) => {
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        panel.classList.toggle('hidden', !open);

        if (chevron) {
            chevron.style.transform = open ? 'rotate(180deg)' : '';
        }
    };

    toggle.addEventListener('click', (event) => {
        event.stopPropagation();
        setOpen(toggle.getAttribute('aria-expanded') !== 'true');
    });

    document.addEventListener('click', (event) => {
        if (!menu.contains(event.target)) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });

    panel.querySelectorAll('a, button[type="submit"]').forEach((element) => {
        element.addEventListener('click', () => setOpen(false));
    });
})();

// ─── Search Bar ──────────────────────────────────────────────
(() => {
    const wrapper = document.getElementById('search-wrapper');
    const toggle  = document.getElementById('search-toggle');
    const panel   = document.getElementById('search-panel');
    const input   = document.getElementById('search-input');
    const results = document.getElementById('search-results');
    if (!wrapper || !toggle || !panel || !input || !results) return;

    let debounceTimer = null;
    let activeRequest = null;
    let isOpen = false;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function openSearch() {
        isOpen = true;
        toggle.classList.add('hidden');
        panel.classList.remove('hidden');
        input.focus();
    }

    function closeSearch() {
        isOpen = false;
        toggle.classList.remove('hidden');
        panel.classList.add('hidden');
        results.classList.add('hidden');
        results.innerHTML = '';
        input.value = '';
    }

    function logSelection(selectedTitleId = null) {
        const query = input.value.trim();

        if (query.length < 2) {
            return;
        }

        fetch('/search/log', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                q: query,
                selected_title_id: selectedTitleId,
            }),
            keepalive: true,
        }).catch(() => {});
    }

    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        openSearch();
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (isOpen && !wrapper.contains(e.target)) closeSearch();
    });

    // Close on Escape
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeSearch();
            return;
        }
        // Keyboard navigation within results
        const items = results.querySelectorAll('[data-search-item]');
        if (!items.length) return;

        let active = results.querySelector('.search-active');
        let idx = active ? [...items].indexOf(active) : -1;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (active) active.classList.remove('search-active', 'bg-white/[0.06]');
            idx = (idx + 1) % items.length;
            items[idx].classList.add('search-active', 'bg-white/[0.06]');
            items[idx].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (active) active.classList.remove('search-active', 'bg-white/[0.06]');
            idx = idx <= 0 ? items.length - 1 : idx - 1;
            items[idx].classList.add('search-active', 'bg-white/[0.06]');
            items[idx].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            if (active) {
                e.preventDefault();
                const link = active.querySelector('a');
                if (link) {
                    logSelection(Number.parseInt(link.dataset.titleId, 10) || null);
                    window.location.href = link.href;
                }
            }
        }
    });

    results.addEventListener('click', (event) => {
        const link = event.target.closest('a[data-search-link]');

        if (!link) {
            return;
        }

        logSelection(Number.parseInt(link.dataset.titleId, 10) || null);
    });

    // Debounced search
    input.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const q = input.value.trim();

        if (q.length < 2) {
            results.classList.add('hidden');
            results.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(async () => {
            activeRequest?.abort();
            activeRequest = new AbortController();

            try {
                const res = await axios.get('/search/suggest', {
                    params: { q },
                    signal: activeRequest.signal,
                });
                const items = res.data.results;

                if (!items.length) {
                    results.innerHTML = '<div class="px-4 py-6 text-center text-[13px] text-white/30">No results found</div>';
                    results.classList.remove('hidden');
                    return;
                }

                results.innerHTML = items.map(item => `
                    <div data-search-item class="transition-colors">
                        <a href="/watch/${encodeURIComponent(item.slug)}" data-search-link data-title-id="${item.id}" class="flex items-center gap-3 px-3 py-2.5 hover:bg-white/[0.06] transition">
                            <div class="w-9 h-[54px] flex-none rounded overflow-hidden bg-white/[0.04]">
                                ${item.poster
                                    ? `<img src="${item.poster}" alt="" class="w-full h-full object-cover" loading="lazy">`
                                    : `<div class="w-full h-full flex items-center justify-center text-white/10"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg></div>`
                                }
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] font-medium text-white/90 truncate">${escapeHtml(item.name)}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[11px] text-white/30">${escapeHtml(item.type)}</span>
                                    ${item.year ? `<span class="text-[11px] text-white/25">${escapeHtml(item.year)}</span>` : ''}
                                    ${item.rating ? `<span class="text-[11px] text-yellow-400/70 flex items-center gap-0.5"><svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>${escapeHtml(item.rating)}</span>` : ''}
                                </div>
                            </div>
                        </a>
                    </div>
                `).join('');
                results.classList.remove('hidden');
            } catch (error) {
                if (error.name === 'CanceledError' || error.name === 'AbortError') {
                    return;
                }

                results.classList.add('hidden');
            }
        }, 400);
    });
})();

// Title row horizontal scrolling
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

    document.querySelectorAll('.row-scroll-left').forEach(btn => {
        btn.addEventListener('click', () => {
            const row = btn.parentElement.querySelector('.title-row');
            if (row) row.scrollBy({ left: -row.clientWidth * 0.75, behavior: 'smooth' });
        });
    });

    document.querySelectorAll('.row-scroll-right').forEach(btn => {
        btn.addEventListener('click', () => {
            const row = btn.parentElement.querySelector('.title-row');
            if (row) row.scrollBy({ left: row.clientWidth * 0.75, behavior: 'smooth' });
        });
    });

    // Auto-hide flash messages
    const flash = document.getElementById('flash-message');
    if (flash) {
        setTimeout(() => {
            flash.style.opacity = '0';
            flash.style.transition = 'opacity 0.5s ease';
            setTimeout(() => flash.remove(), 500);
        }, 3000);
    }

    // ─── Custom Filter Dropdowns ──────────────────────────────────
    // Replicates the season-dropdown pattern for filter forms
    document.querySelectorAll('[data-dropdown]').forEach((dropdown) => {
        const toggle = dropdown.querySelector('[data-dropdown-toggle]');
        const menu = dropdown.querySelector('[data-dropdown-menu]');
        const chevron = dropdown.querySelector('[data-dropdown-chevron]');
        const label = dropdown.querySelector('[data-dropdown-label]');
        const hiddenInput = dropdown.querySelector('input[type="hidden"]');
        const submitOnChange = dropdown.dataset.dropdownSubmit !== 'false';
        if (!toggle || !menu) return;

        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            // Close all other dropdowns first
            document.querySelectorAll('[data-dropdown-menu]').forEach((other) => {
                if (other !== menu) {
                    other.classList.add('hidden');
                    const otherChevron = other.closest('[data-dropdown]')?.querySelector('[data-dropdown-chevron]');
                    if (otherChevron) otherChevron.style.transform = '';
                }
            });
            const isOpen = !menu.classList.contains('hidden');
            menu.classList.toggle('hidden', isOpen);
            if (chevron) chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
        });

        menu.querySelectorAll('[data-dropdown-option]').forEach((option) => {
            option.addEventListener('click', () => {
                const value = option.dataset.value;
                if (hiddenInput) hiddenInput.value = value;
                if (label) {
                    label.textContent = option.textContent.trim();
                    label.classList.toggle('text-white/40', value === '');
                    label.classList.toggle('text-white', value !== '');
                }
                menu.querySelectorAll('[data-dropdown-option]').forEach((item) => {
                    const active = item === option;
                    item.classList.toggle('bg-red-500/15', active);
                    item.classList.toggle('text-red-400', active);
                    item.classList.toggle('font-semibold', active);
                    item.classList.toggle('text-white/60', !active);
                    item.classList.toggle('hover:bg-white/[0.06]', !active);
                    item.classList.toggle('hover:text-white/90', !active);
                });
                menu.classList.add('hidden');
                if (chevron) chevron.style.transform = '';
                if (submitOnChange) {
                    const form = dropdown.closest('form');
                    if (form) form.submit();
                }
            });
        });
    });

    // Close all custom dropdowns on outside click
    document.addEventListener('click', () => {
        document.querySelectorAll('[data-dropdown-menu]').forEach((menu) => {
            menu.classList.add('hidden');
            const chevron = menu.closest('[data-dropdown]')?.querySelector('[data-dropdown-chevron]');
            if (chevron) chevron.style.transform = '';
        });
    });

    document.addEventListener('click', (event) => {
        const link = event.target.closest('[data-search-result-link]');
        if (!link) return;

        const titleId = Number.parseInt(link.dataset.titleId, 10);
        if (!titleId) return;

        fetch('/search/log', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                search_log_id: Number.parseInt(link.dataset.searchLogId, 10) || null,
                selected_title_id: titleId,
                q: link.dataset.searchQuery || null,
            }),
            keepalive: true,
        }).catch(() => {});
    });

    // ─── Global Favorite Toggle ────────────────────────────────────
    // Works on every page: home title-rows, movies grid, series grid, watch page
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-favorite-toggle]');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const titleId = btn.dataset.titleId;
        if (!titleId) return;

        try {
            const res = await axios.post('/favorites/toggle', { title_id: titleId });
            const added = res.data.status === 'added';

            // Update visual state
            const svg = btn.querySelector('svg');
            if (svg) svg.setAttribute('fill', added ? 'currentColor' : 'none');

            // Toggle classes for card-style buttons (small, on poster)
            if (btn.classList.contains('favorite-btn')) {
                btn.classList.toggle('bg-red-600/90', added);
                btn.classList.toggle('text-white', added);
                btn.classList.toggle('!opacity-100', added);
                btn.classList.toggle('fav-active', added);
                btn.classList.toggle('bg-black/60', !added);
                btn.classList.toggle('backdrop-blur', !added);
                btn.classList.toggle('text-white/70', !added);
            }
            // Toggle classes for watch-page style button (large, round)
            else {
                btn.classList.toggle('bg-red-600/20', added);
                btn.classList.toggle('text-red-400', added);
                btn.classList.toggle('bg-white/[0.06]', !added);
                btn.classList.toggle('text-white/40', !added);
            }

            btn.title = added ? 'Remove from My List' : 'Add to My List';

            // Sync all other buttons for the same title on the page
            document.querySelectorAll(`[data-favorite-toggle][data-title-id="${titleId}"]`).forEach((other) => {
                if (other === btn) return;
                const otherSvg = other.querySelector('svg');
                if (otherSvg) otherSvg.setAttribute('fill', added ? 'currentColor' : 'none');

                if (other.classList.contains('favorite-btn')) {
                    other.classList.toggle('bg-red-600/90', added);
                    other.classList.toggle('text-white', added);
                    other.classList.toggle('!opacity-100', added);
                    other.classList.toggle('fav-active', added);
                    other.classList.toggle('bg-black/60', !added);
                    other.classList.toggle('backdrop-blur', !added);
                    other.classList.toggle('text-white/70', !added);
                } else {
                    other.classList.toggle('bg-red-600/20', added);
                    other.classList.toggle('text-red-400', added);
                    other.classList.toggle('bg-white/[0.06]', !added);
                    other.classList.toggle('text-white/40', !added);
                }
                other.title = added ? 'Remove from My List' : 'Add to My List';
            });
        } catch {
            window.location.href = '/login';
        }
    });
});
