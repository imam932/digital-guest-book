import $ from 'jquery';
import select2Factory from 'select2/dist/js/select2.full.js';
import 'select2/dist/css/select2.min.css';

window.jQuery = $;
window.$ = $;

if (typeof select2Factory === 'function') {
    select2Factory(window, $);
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('guestBookForm');
    if (! form) return;

    const companySelect = document.getElementById('company_id');
    const companyOtherInput = document.getElementById('other_company');
    const manualCompanyField = document.getElementById('manualCompanyField');
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
        document.querySelectorAll('[data-error-for]').forEach((node) => { node.textContent = ''; });
    };

    const setLoadingState = (isLoading) => {
        submitButton.disabled = isLoading;
        submitButton.querySelector('.submit-text').textContent = isLoading ? 'Submitting...' : 'Submit message';
        submitButton.querySelector('.spinner-border')?.classList.toggle('d-none', ! isLoading);
    };

    const toggleManualCompanyField = () => {
        const shouldShow = companySelect.value === 'other';
        if (manualCompanyField) {
            manualCompanyField.hidden = ! shouldShow;
            manualCompanyField.classList.toggle('is-visible', shouldShow);
        }
        if (companyOtherInput) {
            companyOtherInput.required = shouldShow;
            if (! shouldShow) companyOtherInput.value = '';
            else window.setTimeout(() => companyOtherInput.focus(), 120);
        }
        if (! shouldShow) {
            const otherError = document.querySelector('[data-error-for="other_company"]');
            if (otherError) otherError.textContent = '';
        }
    };

    const initSelect2 = () => {
        if (typeof $.fn.select2 !== 'function') {
            console.error('Select2 is not available.');
            return;
        }

        if (! $(companySelect).hasClass('select2-hidden-accessible')) {
            $(companySelect).select2({
                width: '100%',
                placeholder: 'Search or select a company',
                allowClear: true,
                language: { noResults: () => 'No companies found' },
            });
        }

        $(companySelect).off('change.toggleManualCompany select2:select.toggleManualCompany select2:unselect.toggleManualCompany');
        $(companySelect).on('change.toggleManualCompany select2:select.toggleManualCompany select2:unselect.toggleManualCompany', toggleManualCompanyField);
    };

    const fetchActiveCompanies = async () => {
        try {
            const response = await fetch('/api/companies/active', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (! response.ok) throw new Error('Unable to load companies.');

            const companies = await response.json();
            companySelect.innerHTML = '';

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Select an active company';
            companySelect.appendChild(placeholder);

            const otherOption = document.createElement('option');
            otherOption.value = 'other';
            otherOption.textContent = 'Others';
            companySelect.appendChild(otherOption);

            companies.forEach((company) => {
                const option = document.createElement('option');
                option.value = company.id;
                option.textContent = company.name;
                companySelect.appendChild(option);
            });

            if ($(companySelect).hasClass('select2-hidden-accessible')) {
                $(companySelect).val('').trigger('change.select2');
            }
        } catch (error) {
            companySelect.innerHTML = '<option value="">No active companies available</option><option value="other">Others</option>';
            setAlert('danger', 'No active companies are available right now. Please try again later.');
        } finally {
            initSelect2();
            toggleManualCompanyField();
        }
    };

    const syncMessageCounter = () => {
        if (messageCounter) messageCounter.textContent = `${messageInput.value.length} / 2000`;
    };

    messageInput.addEventListener('input', () => {
        syncMessageCounter();
        if (messageInput.scrollHeight > messageInput.clientHeight + 6) {
            messageInput.scrollTop = messageInput.scrollHeight;
        }
    });

    companySelect.addEventListener('change', toggleManualCompanyField);

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearAlerts();

        const selectedCompany = companySelect.value;
        const payload = {
            guest_name: guestNameInput.value.trim(),
            company_id: selectedCompany,
            other_company: selectedCompany === 'other' ? companyOtherInput.value.trim() : null,
            message: messageInput.value.trim(),
            _token: form.querySelector('input[name="_token"]').value,
        };

        try {
            setLoadingState(true);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': payload._token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });
            const result = await response.json();

            if (! response.ok) {
                Object.entries(result.errors ?? {}).forEach(([field, messages]) => {
                    const fieldError = document.querySelector(`[data-error-for="${field}"]`);
                    if (fieldError) fieldError.textContent = messages[0];
                });
                setAlert('danger', result.message || 'Please correct the highlighted fields and try again.');
                return;
            }

            form.reset();
            if ($(companySelect).hasClass('select2-hidden-accessible')) {
                $(companySelect).val('').trigger('change.select2');
            }
            companyOtherInput.value = '';
            toggleManualCompanyField();
            syncMessageCounter();
            setAlert('success', result.message || 'Your message was submitted successfully.');
        } catch (error) {
            setAlert('danger', error.message || 'Something went wrong while submitting your message.');
        } finally {
            setLoadingState(false);
        }
    });

    syncMessageCounter();
    toggleManualCompanyField();
    fetchActiveCompanies();
});
