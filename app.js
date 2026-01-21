;(function ($) {
    var AppUtil = {};

    AppUtil.resetSelect = function ($select, placeholder) {
        $select.empty();
        var $opt = $('<option></option>');
        $opt.val('');
        $opt.text(placeholder);
        $select.append($opt);
    };

    AppUtil.loadOptions = function (options) {
        var url = options.url;
        var $select = options.$select;
        var placeholder = options.placeholder || '';
        var selectedId = options.selectedId || null;
        var nextLoader = options.nextLoader;
        AppUtil.resetSelect($select, placeholder);
        $.getJSON(url, function (data) {
            $.each(data, function (idx, item) {
                var $opt = $('<option></option>');
                $opt.val(item.id);
                $opt.text(item.name);
                $select.append($opt);
            });
            if (selectedId) {
                $select.val(String(selectedId));
            }
            if (typeof nextLoader === 'function') {
                nextLoader();
            }
        });
    };

    AppUtil.setupFocusNavigation = function ($form) {
        var $focusable = $form.find('input.form-control, select.form-select');
        $focusable.each(function (index, el) {
            var $el = $(el);
            $el.on('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var nextIndex = index + 1;
                    if (nextIndex < $focusable.length) {
                        $focusable.eq(nextIndex).focus();
                    } else {
                        $form.trigger('submit');
                    }
                }
            });
            if (el.tagName === 'SELECT') {
                $el.on('change', function () {
                    var nextIndex = index + 1;
                    if (nextIndex < $focusable.length) {
                        $focusable.eq(nextIndex).focus();
                    }
                });
            }
        });
    };

    AppUtil.setupSelectSearch = function (inputId, selectId, options) {
        var opts = options || {};
        var $input = $('#' + inputId);
        var $select = $('#' + selectId);
        if ($input.length === 0 || $select.length === 0) {
            return;
        }
        var allOptions = [];
        $select.find('option').each(function () {
            var $opt = $(this);
            allOptions.push({
                value: $opt.val(),
                text: $opt.text(),
                barcode: $opt.attr('data-barcode') || '',
                price: $opt.attr('data-price') || '',
                disabled: $opt.prop('disabled')
            });
        });
        $input.on('input', function () {
            var term = $input.val().toLowerCase();
            var selectedValue = $select.val();
            $select.empty();
            $.each(allOptions, function (index, optData) {
                if (index === 0) {
                    var $placeholderOpt = $('<option></option>');
                    $placeholderOpt.val(optData.value);
                    $placeholderOpt.text(optData.text);
                    $placeholderOpt.prop('disabled', optData.disabled);
                    $select.append($placeholderOpt);
                    return;
                }
                var matchText = optData.text.toLowerCase().indexOf(term) !== -1;
                var matchBarcode = optData.barcode.toLowerCase().indexOf(term) !== -1;
                if (term === '' || matchText || matchBarcode) {
                    var $opt = $('<option></option>');
                    $opt.val(optData.value);
                    $opt.text(optData.text);
                    if (optData.barcode) {
                        $opt.attr('data-barcode', optData.barcode);
                    }
                    if (optData.price) {
                        $opt.attr('data-price', optData.price);
                    }
                    $select.append($opt);
                }
            });
            var hasSelected = false;
            $select.find('option').each(function (idx, el) {
                if (el.value === selectedValue) {
                    $select.prop('selectedIndex', idx);
                    hasSelected = true;
                    return false;
                }
            });
            if (!hasSelected && $select[0].options.length > 0) {
                $select.prop('selectedIndex', 0);
            }
            if (typeof opts.onAfterRebuild === 'function') {
                opts.onAfterRebuild($input, $select);
            }
        });
    };

    AppUtil.debounce = function (fn, delay) {
        var timeoutId;
        return function () {
            var context = this;
            var args = arguments;
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function () {
                fn.apply(context, args);
            }, delay);
        };
    };

    AppUtil.setupAjaxIncrementalSelect = function (config) {
        if (!config) {
            return;
        }
        var $input = $(config.inputSelector);
        var $select = $(config.selectSelector);
        if ($input.length === 0 || $select.length === 0) {
            return;
        }
        var ajaxUrl = config.ajaxUrl;
        var ajaxParams = config.ajaxParams;
        var optionBuilder = config.optionBuilder;
        var onAfterUpdate = config.onAfterUpdate;
        var maxResults = typeof config.maxResults === 'number' ? config.maxResults : 10;
        var delay = typeof config.delay === 'number' ? config.delay : 300;
        var placeholderText = config.placeholderOptionText || '';

        function resetOptions() {
            $select.empty();
            if (placeholderText) {
                var $placeholderOpt = $('<option></option>');
                $placeholderOpt.val('');
                $placeholderOpt.text(placeholderText);
                $select.append($placeholderOpt);
            }
        }

        function handleResults(data) {
            resetOptions();
            var list = Array.isArray(data) ? data : [];
            var count = 0;
            $.each(list, function (index, item) {
                if (count >= maxResults) {
                    return false;
                }
                if (typeof optionBuilder === 'function') {
                    var $opt = optionBuilder(item);
                    if ($opt && $opt.length) {
                        $select.append($opt);
                        count++;
                    }
                }
            });
            if (typeof onAfterUpdate === 'function') {
                onAfterUpdate($select, list);
            }
        }

        function executeSearch() {
            var term = $input.val();
            if (!term) {
                handleResults([]);
                return;
            }
            $.getJSON(ajaxUrl, ajaxParams(term)).done(function (data) {
                handleResults(data);
            });
        }

        resetOptions();
        $input.on('keyup', AppUtil.debounce(executeSearch, delay));
    };

    AppUtil.setupDatePickers = function () {
        if (typeof flatpickr === 'undefined') {
            return;
        }
        var dateInputs = document.querySelectorAll('input.date-input');
        if (!dateInputs.length) {
            return;
        }

        function normalizeDateValue(value) {
            if (!value) {
                return '';
            }
            var trimmed = value.trim();
            if (trimmed === '') {
                return '';
            }
            var digits = trimmed.replace(/\D/g, '');
            if (digits.length === 8) {
                var d = digits.slice(0, 2);
                var m = digits.slice(2, 4);
                var y = digits.slice(4);
                return d + '-' + m + '-' + y;
            }
            return trimmed;
        }

        dateInputs.forEach(function (input) {
            var calendarButtonSelector = input.getAttribute('data-calendar-button');
            var fp = flatpickr(input, {
                dateFormat: 'd-m-Y',
                allowInput: true,
                locale: flatpickr.l10ns.id || 'id',
                ariaDateFormat: 'd F Y',
                disableMobile: false,
                onOpen: function () {
                    input.classList.remove('is-invalid');
                    var errorEl = input.nextElementSibling;
                    if (errorEl && errorEl.classList.contains('invalid-feedback')) {
                        errorEl.textContent = '';
                    }
                },
                onChange: function () {
                    input.classList.remove('is-invalid');
                    var errorEl = input.nextElementSibling;
                    if (errorEl && errorEl.classList.contains('invalid-feedback')) {
                        errorEl.textContent = '';
                    }
                }
            });

            input.addEventListener('blur', function () {
                var normalized = normalizeDateValue(input.value);
                if (normalized !== input.value) {
                    input.value = normalized;
                }
                if (normalized === '') {
                    input.classList.remove('is-invalid');
                    return;
                }
                var parsed = flatpickr.parseDate(normalized, 'd-m-Y');
                if (!parsed) {
                    input.classList.add('is-invalid');
                    var errorEl = input.nextElementSibling;
                    if (errorEl && errorEl.classList.contains('invalid-feedback')) {
                        errorEl.textContent = 'Tanggal tidak valid, gunakan format dd-mm-yyyy.';
                    }
                    return;
                }
                fp.setDate(parsed, false, 'd-m-Y');
                input.value = flatpickr.formatDate(parsed, 'd-m-Y');
            });

            if (calendarButtonSelector) {
                var button = document.querySelector(calendarButtonSelector);
                if (button) {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        fp.open();
                    });
                }
            }
        });
    };

    AppUtil.parseCurrencyIndo = function (value) {
        if (value === null || typeof value === 'undefined') {
            return NaN;
        }
        if (typeof value === 'number') {
            if (!isFinite(value)) {
                return NaN;
            }
            return value;
        }
        var str = String(value).trim();
        if (str === '') {
            return NaN;
        }
        str = str.replace(/[^\d.,-]/g, '');
        var negative = false;
        if (str.charAt(0) === '-') {
            negative = true;
            str = str.slice(1);
        }
        var commaIndex = str.lastIndexOf(',');
        var dotIndex = str.lastIndexOf('.');
        if (commaIndex > dotIndex) {
            str = str.replace(/\./g, '');
            str = str.replace(',', '.');
        } else if (dotIndex > commaIndex) {
            str = str.replace(/,/g, '');
        } else {
            str = str.replace(/[.,]/g, '');
        }
        var num = parseFloat(str);
        if (!isFinite(num)) {
            return NaN;
        }
        return negative ? -num : num;
    };

    AppUtil.formatCurrencyIndo = function (value) {
        if (value === null || typeof value === 'undefined') {
            return '';
        }
        var num = typeof value === 'number' ? value : AppUtil.parseCurrencyIndo(value);
        if (!isFinite(num)) {
            return '';
        }
        return num.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    AppUtil.setCurrencyInputValue = function (input, numeric) {
        if (!input) {
            return;
        }
        var el = input;
        if (typeof input === 'string') {
            el = document.querySelector(input);
        } else if (input.jquery) {
            el = input.get(0);
        }
        if (!el) {
            return;
        }
        var num = typeof numeric === 'number' ? numeric : AppUtil.parseCurrencyIndo(numeric);
        if (!isFinite(num)) {
            el.value = '';
            var hiddenSelectorInvalid = el.getAttribute('data-currency-hidden');
            if (hiddenSelectorInvalid) {
                var hiddenInvalid = document.querySelector(hiddenSelectorInvalid);
                if (hiddenInvalid) {
                    hiddenInvalid.value = '';
                }
            }
            return;
        }
        var hiddenSelector = el.getAttribute('data-currency-hidden');
        if (hiddenSelector) {
            var hidden = document.querySelector(hiddenSelector);
            if (hidden) {
                hidden.value = num.toFixed(2);
            }
        }
        el.value = AppUtil.formatCurrencyIndo(num);
    };

    AppUtil.initCurrencyInputs = function () {
        var inputs = document.querySelectorAll('input.currency-input');
        if (!inputs.length) {
            return;
        }
        inputs.forEach(function (input) {
            var hiddenSelector = input.getAttribute('data-currency-hidden');
            var hidden = hiddenSelector ? document.querySelector(hiddenSelector) : null;
            var initial = '';
            if (hidden && hidden.value) {
                initial = hidden.value;
            } else if (input.value) {
                initial = input.value;
            }
            if (initial !== '') {
                var initialNum = AppUtil.parseCurrencyIndo(initial);
                if (isFinite(initialNum)) {
                    if (hidden) {
                        hidden.value = initialNum.toFixed(2);
                    }
                    input.value = AppUtil.formatCurrencyIndo(initialNum);
                }
            }
            input.addEventListener('blur', function () {
                var val = input.value;
                if (!val) {
                    if (hidden) {
                        hidden.value = '';
                    }
                    return;
                }
                var n = AppUtil.parseCurrencyIndo(val);
                if (!isFinite(n) || n < 0) {
                    if (hidden) {
                        hidden.value = '';
                    }
                    return;
                }
                if (hidden) {
                    hidden.value = n.toFixed(2);
                }
                input.value = AppUtil.formatCurrencyIndo(n);
            });
        });
    };

    AppUtil.showToast = function (message, options) {
        if (!message) {
            return;
        }
        var opts = options || {};
        var type = opts.type || 'info';
        var delay = typeof opts.delay === 'number' ? opts.delay : 3000;
        var containerId = 'app-toast-container';
        var container = document.getElementById(containerId);
        if (!container) {
            container = document.createElement('div');
            container.id = containerId;
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1080';
            document.body.appendChild(container);
        }
        var contextClass = 'text-bg-info';
        if (type === 'success') {
            contextClass = 'text-bg-success';
        } else if (type === 'error' || type === 'danger') {
            contextClass = 'text-bg-danger';
        } else if (type === 'warning') {
            contextClass = 'text-bg-warning';
        } else if (type === 'secondary') {
            contextClass = 'text-bg-secondary';
        }
        var toastEl = document.createElement('div');
        toastEl.className = 'toast align-items-center ' + contextClass + ' border-0';
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        var inner = document.createElement('div');
        inner.className = 'd-flex';
        var body = document.createElement('div');
        body.className = 'toast-body';
        body.textContent = message;
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn-close btn-close-white me-2 m-auto';
        button.setAttribute('data-bs-dismiss', 'toast');
        button.setAttribute('aria-label', 'Close');
        inner.appendChild(body);
        inner.appendChild(button);
        toastEl.appendChild(inner);
        container.appendChild(toastEl);
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            var toast = new bootstrap.Toast(toastEl, { delay: delay });
            toast.show();
        }
    };

    AppUtil.handleStatusToggle = function (config) {
        if (!config || !config.buttonSelector || !config.url || !config.idDataAttribute || !config.idParamName) {
            return;
        }
        var buttonSelector = config.buttonSelector;
        var confirmMessage = config.confirmMessage || '';
        var url = config.url;
        var idDataAttr = config.idDataAttribute;
        var idParamName = config.idParamName;
        var defaultErrorMessage = config.defaultErrorMessage || 'Gagal mengubah status.';
        var updateUI = typeof config.updateUI === 'function' ? config.updateUI : null;

        $(document).on('click', buttonSelector, function () {
            var $btn = $(this);
            if (confirmMessage) {
                if (!window.confirm(confirmMessage)) {
                    return;
                }
            }
            var id = $btn.data(idDataAttr);
            var currentStatus = parseInt(String($btn.data('current-status') || '0'), 10);
            if (!id) {
                return;
            }
            $btn.prop('disabled', true);
            var payload = {
                action: 'toggle',
                ajax: '1'
            };
            payload[idParamName] = id;
            $.post(url, payload, function (data) {
                var resp = data;
                if (typeof resp !== 'object') {
                    try {
                        resp = JSON.parse(data);
                    } catch (e) {
                        resp = null;
                    }
                }
                if (!resp || resp.success !== true) {
                    var msg = resp && resp.message ? resp.message : defaultErrorMessage;
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(msg, { type: 'error' });
                    } else {
                        window.alert(msg);
                    }
                    return;
                }
                var successMsg = resp && resp.message ? resp.message : '';
                if (successMsg && typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(successMsg, { type: 'success' });
                }
                var newStatus = typeof resp.new_status !== 'undefined'
                    ? parseInt(String(resp.new_status), 10)
                    : (currentStatus === 1 ? 0 : 1);
                if (newStatus !== 0 && newStatus !== 1) {
                    newStatus = currentStatus === 1 ? 0 : 1;
                }
                $btn.data('current-status', newStatus);
                if (updateUI) {
                    updateUI($btn, newStatus, currentStatus, resp);
                }
            }, 'json').fail(function () {
                var msg = 'Gagal terhubung ke server.';
                if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(msg, { type: 'error' });
                } else {
                    window.alert(msg);
                }
            }).always(function () {
                $btn.prop('disabled', false);
            });
        });
    };

    AppUtil.handleDeleteAction = function (config) {
        if (!config || !config.buttonSelector || !config.url) {
            return;
        }
        var buttonSelector = config.buttonSelector;
        var confirmMessage = config.confirmMessage || 'Apakah Anda yakin ingin menghapus data ini?';
        var url = config.url;
        var idDataAttr = config.idDataAttribute || null;
        var idParamName = config.idParamName || null;
        var actionName = config.actionName || 'delete';
        var defaultErrorMessage = config.defaultErrorMessage || 'Gagal menghapus data.';
        var successFallbackMessage = config.successMessage || '';
        var extraParams = config.extraParams || null;
        var onSuccess = typeof config.onSuccess === 'function' ? config.onSuccess : null;

        $(document).on('click', buttonSelector, function (e) {
            e.preventDefault();
            var $btn = $(this);
            if (confirmMessage) {
                if (!window.confirm(confirmMessage)) {
                    return;
                }
            }
            var payload = {
                action: actionName,
                ajax: '1'
            };
            if (idDataAttr && idParamName) {
                var id = $btn.data(idDataAttr);
                if (!id) {
                    return;
                }
                payload[idParamName] = id;
            }
            if (extraParams && typeof extraParams === 'object') {
                for (var key in extraParams) {
                    if (Object.prototype.hasOwnProperty.call(extraParams, key)) {
                        payload[key] = extraParams[key];
                    }
                }
            }
            $btn.prop('disabled', true);
            $.post(url, payload, function (data) {
                var resp = data;
                if (typeof resp !== 'object') {
                    try {
                        resp = JSON.parse(data);
                    } catch (e) {
                        resp = null;
                    }
                }
                if (!resp || resp.success !== true) {
                    var msg = resp && resp.message ? resp.message : defaultErrorMessage;
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(msg, { type: 'error' });
                    } else {
                        window.alert(msg);
                    }
                    return;
                }
                var successMsg = resp && resp.message ? resp.message : successFallbackMessage;
                if (successMsg && typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(successMsg, { type: 'success' });
                }
                if (onSuccess) {
                    onSuccess($btn, resp);
                }
            }, 'json').fail(function () {
                var msg = 'Gagal terhubung ke server.';
                if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(msg, { type: 'error' });
                } else {
                    window.alert(msg);
                }
            }).always(function () {
                $btn.prop('disabled', false);
            });
        });
    };

    AppUtil.handleLargeForm = function (config) {
        if (!config) {
            return;
        }
        var $form = $(config.formSelector);
        if ($form.length === 0) {
            return;
        }
        var ajaxUrl = config.ajaxUrl || $form.attr('action') || window.location.href;
        var onSuccess = config.onSuccess;
        var onError = config.onError;
        var parseJson = config.parseJson === true;
        var beforeSubmit = config.beforeSubmit;

        $form.on('submit', function (e) {
            e.preventDefault();
            if (typeof beforeSubmit === 'function') {
                var ok = beforeSubmit($form);
                if (ok === false) {
                    return;
                }
            }
            var $submitButton = $form.find('button[type="submit"]').first();
            if ($submitButton.length) {
                $submitButton.prop('disabled', true);
            }
            var formData = $form.serialize();
            $.post(ajaxUrl, formData, function (data) {
                if (parseJson) {
                    var resp = data;
                    if (typeof resp !== 'object') {
                        try {
                            resp = JSON.parse(data);
                        } catch (err) {
                            resp = null;
                        }
                    }
                    if (resp && resp.success) {
                        if (typeof onSuccess === 'function') {
                            onSuccess(resp);
                        }
                    } else if (typeof onError === 'function') {
                        var msg = resp && (resp.message || resp.error) ? (resp.message || resp.error) : 'Gagal mengirim data';
                        onError(msg, resp);
                    }
                } else if (typeof onSuccess === 'function') {
                    onSuccess(data);
                }
            }).fail(function () {
                if (typeof onError === 'function') {
                    onError('Gagal terhubung ke server');
                }
            }).always(function () {
                if ($submitButton.length) {
                    $submitButton.prop('disabled', false);
                }
            });
        });
    };

    AppUtil.initThemeToggle = function () {
        var $html = $('html');
        if ($html.length === 0) {
            return;
        }
        var storedTheme = null;
        try {
            storedTheme = localStorage.getItem('app_theme');
        } catch (e) {
            storedTheme = null;
        }
        var currentTheme = storedTheme === 'dark' ? 'dark' : 'light';
        function applyTheme(theme) {
            currentTheme = theme === 'dark' ? 'dark' : 'light';
            $html.attr('data-bs-theme', currentTheme);
            try {
                localStorage.setItem('app_theme', currentTheme);
            } catch (e) {
            }
            updateButtons();
        }
        function updateButtons() {
            var label = currentTheme === 'dark' ? 'Tema: Gelap' : 'Tema: Terang';
            $('[data-theme-toggle]').each(function () {
                var $btn = $(this);
                if ($btn.is('button') || $btn.is('a')) {
                    $btn.text(label);
                } else {
                    $btn.val(label);
                }
            });
        }
        applyTheme(currentTheme);
        $(document).on('click', '[data-theme-toggle]', function (e) {
            e.preventDefault();
            var nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);
        });
    };

    $(function () {
        if (typeof AppUtil.setupDatePickers === 'function') {
            AppUtil.setupDatePickers();
        }
        if (typeof AppUtil.initCurrencyInputs === 'function') {
            AppUtil.initCurrencyInputs();
        }
        if (typeof AppUtil.initThemeToggle === 'function') {
            AppUtil.initThemeToggle();
        }
    });

    window.AppUtil = AppUtil;
})(jQuery);
