/**
 * Multi-Auth System JavaScript
 *
 * This file contains client-side functionality for the authentication system
 */

$(document).ready(function() {
    // Auto-dismiss alert messages after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Form validation helpers
    window.validateForm = function(formData, rules) {
        const errors = {};

        for (let field in rules) {
            const rule = rules[field];
            const value = formData[field];

            // Required validation
            if (rule.required && !value) {
                errors[field] = rule.requiredMsg || `${field} is required`;
                continue;
            }

            // Email validation
            if (rule.email && value && !isValidEmail(value)) {
                errors[field] = rule.emailMsg || 'Invalid email format';
            }

            // Min length validation
            if (rule.minLength && value && value.length < rule.minLength) {
                errors[field] = rule.minLengthMsg || `${field} must be at least ${rule.minLength} characters`;
            }

            // Max length validation
            if (rule.maxLength && value && value.length > rule.maxLength) {
                errors[field] = rule.maxLengthMsg || `${field} must be at most ${rule.maxLength} characters`;
            }

            // Password match validation
            if (rule.match && value !== formData[rule.match]) {
                errors[field] = rule.matchMsg || `${field} does not match`;
            }
        }

        return errors;
    };

    window.isValidEmail = function(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    };

    // Confirm delete dialogs
    $('.delete-confirm').on('click', function(e) {
        e.preventDefault();
        const message = $(this).data('message') || 'Are you sure you want to delete this item?';
        if (confirm(message)) {
            $(this).closest('form').submit();
        }
    });

    // Toggle switch handlers
    $('.form-check-input').on('change', function() {
        const target = $(this).data('target');
        if (target) {
            $(target).submit();
        }
    });

    // Password strength indicator
    $('#password').on('input', function() {
        const password = $(this).val();
        let strength = 0;

        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;

        const strengthBar = $('#password-strength');
        const colors = ['#dc3545', '#ffc107', '#17a2b8', '#28a745', '#20c997'];
        const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];

        if (password.length > 0) {
            strengthBar.css({
                'width': (strength * 20) + '%',
                'background-color': colors[strength - 1] || colors[0]
            });
            strengthBar.closest('.password-strength').find('.strength-text').text(texts[strength - 1] || texts[0]);
        } else {
            strengthBar.css({ 'width': '0%' });
            strengthBar.closest('.password-strength').find('.strength-text').text('');
        }
    });

    // Select all/deselect all for permissions
    $('#select-all-permissions').on('change', function() {
        const checked = $(this).prop('checked');
        $('.permission-checkbox').prop('checked', checked);
    });

    $('#select-all-resource').on('change', function() {
        const resource = $(this).data('resource');
        const checked = $(this).prop('checked');
        $(`.permission-checkbox[data-resource="${resource}"]`).prop('checked', checked);
    });

    // Form submission with loading state
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        if (submitBtn.length && !submitBtn.prop('disabled')) {
            submitBtn.addClass('loading').prop('disabled', true);
        }
    });

    // Live search/filter
    const searchInputs = $('.search-input');
    searchInputs.on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const targetTable = $(this).data('target');
        $(targetTable).find('tbody tr').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(searchTerm));
        });
    });

    // Modal handlers
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0]?.reset();
        $(this).find('.alert').remove();
    });

    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Popover initialization
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Data table export buttons (if DT init is needed)
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.datatable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "_MENU_ records per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }

    // AJAX form submission support
    $('form[data-ajax="true"]').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const url = $form.attr('action');
        const method = $form.attr('method') || 'POST';
        const formData = new FormData(this);

        // Send AJAX request
        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('input[name="<?= csrf_token() ?>"]').val()
            },
            success: function(response) {
                handleAjaxSuccess($form, response);
            },
            error: function(xhr) {
                handleAjaxError($form, xhr);
            }
        });
    });

    window.handleAjaxSuccess = function($form, response) {
        if (response.success) {
            if (response.redirect) {
                window.location.href = response.redirect;
            } else {
                // Show success message and optionally refresh page
                showAlert('success', response.message || 'Operation completed successfully');
                if (response.reload) {
                    setTimeout(() => location.reload(), 1000);
                }
            }
        } else {
            showAjaxErrors($form, response.errors || {general: response.message});
        }
    };

    window.handleAjaxError = function($form, xhr) {
        let message = 'An error occurred. Please try again.';
        if (xhr.status === 422) {
            const errors = xhr.responseJSON?.errors || {};
            showAjaxErrors($form, errors);
            return;
        } else if (xhr.status === 403) {
            message = 'You do not have permission to perform this action.';
        } else if (xhr.status === 404) {
            message = 'Resource not found.';
        } else if (xhr.status >= 500) {
            message = 'Server error. Please contact administrator.';
        }
        showAlert('danger', message);
    };

    window.showAlert = function(type, message) {
        const alertClass = `alert-${type}`;
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        // Remove existing alerts
        $('.alert').remove();

        // Insert new alert at the top of main container
        $('main').prepend(alertHtml);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $('.alert').fadeOut('slow');
        }, 5000);
    };

    window.showAjaxErrors = function($form, errors) {
        // Clear previous errors
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();

        // Show new errors
        for (let field in errors) {
            const $input = $form.find(`[name="${field}"]`);
            if ($input.length) {
                $input.addClass('is-invalid');
                const feedback = `<div class="invalid-feedback">${errors[field]}</div>`;
                $input.after(feedback);
            } else {
                // General error
                showAlert('danger', errors[field]);
            }
        }
    };

    // Confirm modals
    window.showConfirmModal = function(title, message, confirmCallback) {
        const modalHtml = `
            <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">${message}</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmModalAction">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const $modal = $(modalHtml);
        $('body').append($modal);
        const modal = new bootstrap.Modal($modal[0]);
        modal.show();

        $modal.find('#confirmModalAction').on('click', function() {
            confirmCallback();
            modal.hide();
        });

        $modal.on('hidden.bs.modal', function() {
            $(this).remove();
        });
    };

    // Initialize tooltips and popovers
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Permission selection
    $('.permission-group-header').on('click', function() {
        const $group = $(this).next('.permission-group-body');
        $group.toggleClass('d-none');
        $(this).find('i').toggleClass('bi-chevron-down bi-chevron-right');
    });
});
