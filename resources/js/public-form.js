import $ from 'jquery';
import 'select2/dist/css/select2.min.css';

window.jQuery = $;
window.$ = $;

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('guestBookForm');

    if (! form) {
        return;
    }

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

    const toggleManualCompanyField = () => {
        const selectedValue = companySelect.value;
        const shouldShow = selectedValue === 'other';

        if (manualCompanyField) {
            manualCompanyField.classList.toggle('is-visible', shouldShow);
            manualCompanyField.hidden = !shouldShow;

            if (shouldShow) {
                setTimeout(() => {
                    companyOtherInput?.focus();
                }, 120);
            }
        }

        if (! shouldShow && companyOtherInput) {
            companyOtherInput.value = '';
            const otherError = document.querySelector('[data-error-for="other_company"]');
            if (otherError) {
                otherError.textContent = '';
            }
        }
    };

    companySelect.addEventListener('change', toggleManualCompanyField);

    const initSelect2 = async () => {
        if (! companySelect) {
            return;
        }

        try {
            await import('select2');
        } catch (error) {
            console.error('Select2 failed to load:', error);
            return;
        }

        if (typeof $.fn.select2 !== 'function') {
            return;
        }

        $(companySelect).select2({
            width: '100%',
            placeholder: 'Search or select a company',
            allowClear: true,
            language: {
                noResults: () => 'No companies found',
            },
        });

        $(companySelect).off('change.toggleManualCompanyField').on('change.toggleManualCompanyField', toggleManualCompanyField);
        $(companySelect).off('select2:select.select2ManualCompany select2:unselect.select2ManualCompany').on('select2:select.select2ManualCompany select2:unselect.select2ManualCompany', function () {
            toggleManualCompanyField();
        });
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

            if (! Array.from(companySelect.options).some((option) => option.value === 'other')) {
                const otherOption = document.createElement('option');
                otherOption.value = 'other';
                otherOption.textContent = 'Other';
                companySelect.appendChild(otherOption);
            }

            if ($(companySelect).hasClass('select2-hidden-accessible')) {
                $(companySelect).val('').trigger('change.select2');
            }
        } catch (error) {
            companySelect.innerHTML = '<option value="">No active companies available</option>';
            if (! Array.from(companySelect.options).some((option) => option.value === 'other')) {
                const otherOption = document.createElement('option');
                otherOption.value = 'other';
                otherOption.textContent = 'Other';
                companySelect.appendChild(otherOption);
            }
            setAlert('danger', 'No active companies are available right now. Please try again later.');
        } finally {
            initSelect2();
            toggleManualCompanyField();
        }
    };

    const syncMessageCounter = () => {
        const length = messageInput.value.length;
        if (messageCounter) {
            messageCounter.textContent = `${length} / 2000`;
        }
    };

    const ensureLongMessageScroll = () => {
        const isOverflowing = messageInput.scrollHeight > messageInput.clientHeight + 6;

        if (isOverflowing) {
            messageInput.scrollTop = messageInput.scrollHeight;

            const formBottom = form.getBoundingClientRect().bottom;
            const viewportHeight = window.innerHeight;

            if (viewportHeight > 0 && formBottom > viewportHeight - 180) {
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth',
                });
            }
        }
    };

    messageInput.addEventListener('input', () => {
        syncMessageCounter();
        ensureLongMessageScroll();
    });

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
    initSelect2();
    fetchActiveCompanies();
});
