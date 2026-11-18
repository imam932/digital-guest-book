document.addEventListener('DOMContentLoaded', () => {
    const wall = document.getElementById('messageWall');
    const popup = document.getElementById('messagePopup');
    if (!wall || !popup) return;

    const state = {
        lastId: 0,
        seenIds: new Set(),
        pollTimer: null,
        shuffleTimer: null,
        popupTimer: null,
        popupExitTimer: null,
        isPaused: false,
        initialLoad: true,
    };

    const getColumnCount = () => {
        if (window.innerWidth >= 1280) return 5;
        if (window.innerWidth >= 900) return 4;
        if (window.innerWidth >= 640) return 3;
        return 2;
    };

    const isNearBottom = () => wall.scrollHeight - wall.scrollTop - wall.clientHeight < 160;

    const applyWallLayout = () => {
        wall.style.gridTemplateColumns = `repeat(${getColumnCount()}, minmax(0, 1fr))`;
    };

    const createMessageCard = (message) => {
        const card = document.createElement('article');
        card.className = 'message-card';
        card.dataset.messageId = String(message.id);

        const header = document.createElement('header');
        header.className = 'message-card__header';

        const name = document.createElement('strong');
        name.className = 'message-card__name';
        name.textContent = message.name;

        const company = document.createElement('span');
        company.className = 'message-card__company';
        company.textContent = message.company || 'Unknown';

        const body = document.createElement('p');
        body.className = 'message-card__body';
        body.textContent = message.message;

        const time = document.createElement('time');
        time.className = 'message-card__time';
        time.textContent = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        header.append(name, company);
        card.append(header, body, time);
        return card;
    };

    const hidePopup = () => {
        window.clearTimeout(state.popupTimer);
        window.clearTimeout(state.popupExitTimer);
        popup.classList.remove('is-visible');
        popup.classList.add('is-hiding');
        state.popupExitTimer = window.setTimeout(() => {
            popup.classList.add('d-none');
            popup.classList.remove('is-hiding');
        }, 450);
    };

    const renderPopup = (message) => {
        window.clearTimeout(state.popupTimer);
        window.clearTimeout(state.popupExitTimer);
        popup.innerHTML = '';

        const name = document.createElement('div');
        name.className = 'message-popup__title';
        name.textContent = message.name;

        const company = document.createElement('div');
        company.className = 'message-popup__company';
        company.textContent = message.company || 'Unknown';

        const body = document.createElement('div');
        body.className = 'message-popup__body';
        body.textContent = message.message;

        popup.append(name, company, body);
        popup.classList.remove('is-hiding', 'd-none');
        void popup.offsetWidth;
        popup.classList.add('is-visible');

        state.popupTimer = window.setTimeout(hidePopup, 5000);
    };

    const appendMessage = (message, showPopup) => {
        const id = String(message.id);
        if (state.seenIds.has(id)) return;

        const wasNearBottom = isNearBottom();
        state.seenIds.add(id);
        wall.appendChild(createMessageCard(message));
        applyWallLayout();

        if (showPopup) renderPopup(message);
        if (wasNearBottom) wall.scrollTo({ top: wall.scrollHeight, behavior: 'smooth' });
    };

    const fetchMessages = async () => {
        if (state.isPaused) return;

        try {
            const response = await fetch(`/api/messages?last_id=${state.lastId}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!response.ok) throw new Error('Unable to load messages');

            const payload = await response.json();
            const messages = [...(payload.messages ?? [])].sort((a, b) => Number(a.id) - Number(b.id));

            messages.forEach((message) => appendMessage(message, !state.initialLoad));

            if (payload.last_id) state.lastId = Math.max(state.lastId, Number(payload.last_id));

            if (state.initialLoad) {
                state.initialLoad = false;
                wall.scrollTop = wall.scrollHeight;
            }
        } catch (error) {
            console.warn('Message polling failed:', error);
        }
    };

    const startPolling = () => {
        if (state.pollTimer) clearInterval(state.pollTimer);
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

    window.addEventListener('resize', applyWallLayout, { passive: true });
    applyWallLayout();
    fetchMessages();
    startPolling();
});
