document.addEventListener('DOMContentLoaded', () => {
    const wall = document.getElementById('messageWall');
    const popup = document.getElementById('messagePopup');

    if (!wall || !popup) {
        return;
    }

    const state = {
        lastId: 0,
        seenIds: new Set(),
        pollTimer: null,
        shuffleTimer: null,
        isPaused: false,
    };

    const getColumnCount = () => {
        return window.innerWidth >= 1280 ? 5 : 4;
    };

    const applyWallLayout = () => {
        const count = wall.children.length;
        const columns = getColumnCount();

        wall.style.gridTemplateColumns = `repeat(${columns}, minmax(0, 1fr))`;
        wall.style.columnGap = count > 40 ? '12px' : '14px';
        wall.style.rowGap = count > 40 ? '8px' : count > 20 ? '10px' : count > 10 ? '12px' : '14px';

        const rowOverlap = count > 40 ? 12 : count > 20 ? 8 : count > 10 ? 4 : 0;

        [...wall.children].forEach((card, index) => {
            const row = Math.floor(index / columns);
            const idValue = Number(card.dataset.messageId || 0);
            const cardHeight = count > 40 ? 120 : count > 20 ? 128 : count > 10 ? 138 : 148;
            const driftX = ((idValue % 7) - 3) * 6;
            const driftY = ((idValue % 5) - 2) * 4;
            const duration = 10 + (idValue % 6) * 1.5;

            card.style.minHeight = `${cardHeight}px`;
            card.style.height = `${cardHeight}px`;
            card.style.marginTop = row > 0 && count > 10 ? `${-rowOverlap}px` : '0px';
            card.style.zIndex = String(10 + row + (index % columns));
            card.style.setProperty('--drift-x', `${driftX}px`);
            card.style.setProperty('--drift-y', `${driftY}px`);
            card.style.setProperty('--drift-duration', `${duration}s`);
            card.classList.toggle('message-card--foreground', count > 20 && index % 9 === 0);
            card.classList.toggle('message-card--background', count > 40 && index % 3 === 0);
        });
    };

    const createMessageCard = (message) => {
        const card = document.createElement('article');
        card.className = 'message-card';
        card.setAttribute('data-message-id', String(message.id));

        const float = document.createElement('div');
        float.className = 'message-card__float';

        const inner = document.createElement('div');
        inner.className = 'message-card__inner';

        const header = document.createElement('div');
        header.className = 'message-card__header';

        const name = document.createElement('strong');
        name.textContent = message.name;

        const company = document.createElement('span');
        company.textContent = message.company || 'Guest';
        company.className = 'message-card__company';

        header.append(name, company);

        const body = document.createElement('p');
        body.className = 'message-card__body';
        body.textContent = message.message;

        const time = document.createElement('time');
        time.className = 'message-card__time';
        time.textContent = new Date(message.created_at).toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit',
        });

        inner.append(header, body, time);
        float.appendChild(inner);
        card.appendChild(float);
        return card;
    };

    const renderPopup = (message) => {
        popup.innerHTML = '';

        const title = document.createElement('div');
        title.className = 'message-popup__title';
        title.textContent = message.name;

        const company = document.createElement('div');
        company.className = 'message-popup__company';
        company.textContent = message.company || 'Guest';

        const body = document.createElement('div');
        body.className = 'message-popup__body';
        body.textContent = message.message;

        popup.append(title, company, body);
        popup.classList.remove('is-hiding', 'd-none');
        popup.classList.add('is-visible');

        window.clearTimeout(renderPopup.hideTimer);
        renderPopup.hideTimer = window.setTimeout(() => {
            popup.classList.remove('is-visible');
            popup.classList.add('is-hiding');
            window.setTimeout(() => {
                popup.classList.add('d-none');
                popup.classList.remove('is-hiding');
            }, 450);
        }, 5000);
    };

    const appendMessage = (message, isPopup = false) => {
        const id = String(message.id);

        if (state.seenIds.has(id)) {
            return;
        }

        state.seenIds.add(id);

        const card = createMessageCard(message);
        wall.appendChild(card);
        applyWallLayout();

        if (isPopup) {
            renderPopup(message);
        }
    };

    const normalizeMessages = (items) => [...items].sort((a, b) => Number(a.id) - Number(b.id));

    const shuffleCards = () => {
        if (wall.children.length < 6 || document.hidden) {
            return;
        }

        const cards = [...wall.children].sort(() => Math.random() - 0.5).slice(0, Math.min(4, wall.children.length));

        cards.forEach((card, index) => {
            const currentX = Number(card.style.getPropertyValue('--drift-x').replace('px', '')) || 0;
            const currentY = Number(card.style.getPropertyValue('--drift-y').replace('px', '')) || 0;
            const nextX = Math.max(-10, Math.min(10, currentX + (index % 2 === 0 ? 4 : -4)));
            const nextY = Math.max(-8, Math.min(8, currentY + (index % 3 === 0 ? 3 : -3)));

            card.style.setProperty('--drift-x', `${nextX}px`);
            card.style.setProperty('--drift-y', `${nextY}px`);
        });
    };

    const fetchMessages = async () => {
        if (state.isPaused) {
            return;
        }

        try {
            const response = await fetch(`/api/messages?last_id=${state.lastId}`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Unable to load messages');
            }

            const payload = await response.json();
            const messages = normalizeMessages(payload.messages ?? []);

            messages.forEach((message) => {
                if (Number(message.id) > state.lastId) {
                    state.lastId = Number(message.id);
                }

                if (!state.seenIds.has(String(message.id))) {
                    appendMessage(message, true);
                }
            });
        } catch (error) {
            console.warn('Message polling failed:', error);
        }
    };

    const startPolling = () => {
        if (state.pollTimer) {
            clearInterval(state.pollTimer);
        }

        state.pollTimer = window.setInterval(fetchMessages, 2500);
    };

    const startShuffle = () => {
        if (state.shuffleTimer) {
            clearInterval(state.shuffleTimer);
        }

        state.shuffleTimer = window.setInterval(shuffleCards, 12000);
    };

    document.addEventListener('visibilitychange', () => {
        state.isPaused = document.hidden;

        if (state.isPaused) {
            clearInterval(state.pollTimer);
            clearInterval(state.shuffleTimer);
        } else {
            startPolling();
            startShuffle();
            fetchMessages();
        }
    });

    window.addEventListener('resize', applyWallLayout, { passive: true });

    fetchMessages();
    applyWallLayout();
    startPolling();
    startShuffle();
});
