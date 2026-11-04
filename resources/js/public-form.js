document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('guestBookForm');

    if (! form) {
        return;
    }

    const companySelect = document.getElementById('company_id');
    const guestNameInput = document.getElementById('guest_name');
    const messageInput = document.getElementById('message');
    const alertBox = document.getElementById('guestBookAlert');
    const submitButton = document.getElementById('submitGuestBook');
    const messageCounter = document.getElementById('messageCounter');

    const setAlert = (type, message) => {
        alertBox.className = `alert alert-${type} mt-3`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');
    };

    const clearAlerts = () => {
        alertBox.className = 'alert d-none';
        alertBox.textContent = '';
        document.querySelectorAll('[data-error-for]').forEach((node) => {
            node.textContent = '';
        });
    };

    const setLoadingState = (isLoading) => {
        submitButton.disabled = isLoading;
        submitButton.querySelector('.submit-text').textContent = isLoading ? 'Submitting...' : 'Submit message';
        const spinner = submitButton.querySelector('.spinner-border');
        if (spinner) {
            spinner.classList.toggle('d-none', !isLoading);
        }
    };

    const fetchActiveCompanies = async () => {
        try {
            const response = await fetch('/api/companies/active', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (! response.ok) {
                throw new Error('Unable to load companies.');
            }

            const companies = await response.json();
            companySelect.innerHTML = '<option value="">Select an active company</option>';

            companies.forEach((company) => {
                const option = document.createElement('option');
                option.value = company.id;
                option.textContent = company.name;
                companySelect.appendChild(option);
            });
        } catch (error) {
            companySelect.innerHTML = '<option value="">No active companies available</option>';
            setAlert('danger', 'No active companies are available right now. Please try again later.');
        }
    };

    const syncMessageCounter = () => {
        const length = messageInput.value.length;
        if (messageCounter) {
            messageCounter.textContent = `${length} / 2000`;
        }
    };

    messageInput.addEventListener('input', syncMessageCounter);

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearAlerts();

        const formData = new FormData(form);
        const payload = {
            guest_name: guestNameInput.value.trim(),
            company_id: companySelect.value,
            message: messageInput.value.trim(),
            _token: formData.get('_token'),
        };

        try {
            setLoadingState(true);

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();

            if (! response.ok) {
                const errors = result.errors ?? {};

                Object.entries(errors).forEach(([field, messages]) => {
                    const fieldError = document.querySelector(`[data-error-for="${field}"]`);
                    if (fieldError) {
                        fieldError.textContent = messages[0];
                    }
                });

                const message = result.message || 'Please correct the highlighted fields and try again.';
                setAlert('danger', message);
                return;
            }

            form.reset();
            syncMessageCounter();
            setAlert('success', result.message || 'Your message was submitted successfully.');
        } catch (error) {
            setAlert('danger', error.message || 'Something went wrong while submitting your message.');
        } finally {
            setLoadingState(false);
        }
    });

    syncMessageCounter();
    fetchActiveCompanies();
});
