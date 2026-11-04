document.addEventListener('DOMContentLoaded', () => {
    const wall = document.getElementById('messageWall');
    const popup = document.getElementById('messagePopup');

    if (! wall || ! popup) {
        return;
    }

    const state = {
        lastId: 0,
        seenIds: new Set(),
        pollTimer: null,
        isPaused: false,
    };

    const createMessageCard = (message, isPopup = false) => {
        const card = document.createElement('article');
        card.className = `message-card ${isPopup ? 'message-card--popup' : ''}`;
        card.setAttribute('data-message-id', String(message.id));

        const header = document.createElement('div');
        header.className = 'message-card__header';

        const name = document.createElement('strong');
        name.textContent = message.name;

        const company = document.createElement('span');
        company.textContent = message.company;
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

        card.append(header, body, time);
        return card;
    };

    const renderPopup = (message) => {
        popup.innerHTML = '';

        const title = document.createElement('div');
        title.className = 'message-popup__title';
        title.textContent = message.name;

        const company = document.createElement('div');
        company.className = 'message-popup__company';
        company.textContent = message.company;

        const body = document.createElement('div');
        body.className = 'message-popup__body';
        body.textContent = message.message;

        popup.append(title, company, body);
        popup.classList.remove('d-none');
        popup.classList.add('is-visible');

        window.clearTimeout(renderPopup.hideTimer);
        renderPopup.hideTimer = window.setTimeout(() => {
            popup.classList.remove('is-visible');
            popup.classList.add('is-hiding');
            window.setTimeout(() => {
                popup.classList.add('d-none');
                popup.classList.remove('is-hiding');
            }, 500);
        }, 5000);
    };

    const appendMessage = (message, isPopup = false) => {
        const id = String(message.id);

        if (state.seenIds.has(id)) {
            return;
        }

        state.seenIds.add(id);

        const card = createMessageCard(message, isPopup);
        card.classList.add('is-visible');
        wall.appendChild(card);

        if (isPopup) {
            renderPopup(message);
        }
    };

    const normalizeMessages = (items) => {
        return [...items].sort((a, b) => Number(a.id) - Number(b.id));
    };

    const fetchMessages = async () => {
        if (state.isPaused) {
            return;
        }

        try {
            const response = await fetch(`/api/messages?last_id=${state.lastId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (! response.ok) {
                throw new Error('Unable to load messages');
            }

            const payload = await response.json();
            const messages = normalizeMessages(payload.messages ?? []);

            messages.forEach((message) => {
                if (Number(message.id) > state.lastId) {
                    state.lastId = Number(message.id);
                }

                if (! state.seenIds.has(String(message.id))) {
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

    document.addEventListener('visibilitychange', () => {
        state.isPaused = document.hidden;

        if (state.isPaused) {
            clearInterval(state.pollTimer);
        } else {
            startPolling();
            fetchMessages();
        }
    });

    fetchMessages();
    startPolling();
});
