/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

(function ($) {

    $(document).ready(function () {

        $(document).ajaxError(function (event, xhr, settings) {
            if (xhr.statusText === 'abort')
                return;

            $('#modal-flash-error .content').text('Une erreur inattendue est survenue.');
            $('#modal-flash-error').modal('show');
        });

        $(document).ajaxSuccess(function (event, xhr, settings) {

            if (xhr.responseJSON && xhr.responseJSON.error) {
                if (xhr.responseJSON.details && xhr.responseJSON.details.length) {
                    $('#modal-flash-error .content').html('<h5>' + xhr.responseJSON.error + '</h5>' + (xhr.responseJSON.details).replace(/(?:\r\n|\r|\n)/g, '<br>'));
                } else {
                    $('#modal-flash-error .content').text(xhr.responseJSON.error);
                }
                $('#modal-flash-error').modal('show');
            }
        });

        /**
         * Declarations
         */

        var stepTo = function (stepIndex) {
            if (stepIndex < 1 || stepIndex > 4)
                return;

            $('.step').hide();
            $('.step-' + stepIndex).show();
        };

        /**
         * Welcome page
         */

        $('.btn-start-quizz').click(function (e) {
            e.preventDefault();

            stepTo(2);
        });

        /**
         * Quizz page
         */

        $('.step-2 .btn-back').click(function (e) {
            e.preventDefault();

            stepTo(1);
        });

        // Shortcut to form step if quizz has saved state and is valid
        if (typeof isQuizzStateDefined !== 'undefined' && isQuizzStateDefined) {
            if ($('form.form-quizz').valid()) {
                stepTo(3);
            } else {
                stepTo(2); // Skip 1st step anyway
            }
        }

        $('form.form-quizz').validate({
            errorPlacement: function () {
            },
            showErrors: function (errorMap, errorList) {
                $('.form-quizz-error').hide();
                if (errorList.length)
                    $('.form-quizz-error').show();

                this.defaultShowErrors();
            }
        });

        $('form.form-quizz input').each(function () {
            $(this).rules('add', {
                required: true
            });
        });

        $('.btn-validate-quizz').click(function (e) {
            e.preventDefault();
            var form = $('form.form-quizz');
            var btn = this;

            if (form.valid()) {
                $(btn).button('loading');
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function (result) {
                        if (result && result.error)
                            return;

                        stepTo(3);
                    },
                    complete: function () {
                        $(btn).button('reset');
                    }
                });
            }
        });

        /**
         * Form page
         */

        $('.step-3 .btn-back').click(function (e) {
            e.preventDefault();

            stepTo(2);
        });

        $.validator.addMethod(
            'frenchDate',
            function (value, element) {
                return value.match(/^\d\d\d\d\-\d\d\-\d\d$/);
            },
            "Merci de renseigner une date au format jj/mm/aaaa." // Weirdly display format != value format for type date field
        );

        String.prototype.capitalize = function () {
            return this.charAt(0).toUpperCase() + this.slice(1).toLowerCase();
        };

        $('form.form-register #first_name, form.form-register #last_name').change(function (e) {
            $(this).val($(this).val().capitalize());
        });

        $('form.form-register #email').change(function (e) {
            $(this).val($(this).val().toLowerCase());
        });

        $('form.form-register #phone').change(function (e) {
            $(this).val($(this).val().split(/(\d{2})/).filter(function (value) {
                return !/^\s*$/.test(value);
            }).join(' '));
        });

        $('#birthday-picker').birthdayPicker({
            sizeClass: 'form-control',
            dateFormat: 'littleEndian',
            monthFormat: 'number',
            defaultDate: $('form.form-register #birthday').val(),
            callback: function (dateString) {
                $('form.form-register #birthday').val(dateString);
            }
        });

        $.validator.addMethod(
            'phoneNumber',
            function (value, element) {
                return /^\d{2}(?:[\s.-]*\d{2}){4}$/.test(value);
            },
            "Merci de renseigner un numéro de téléphone valide ex. 06 01 02 03 04"
        );

        $.validator.addMethod(
            'phoneNumberCountryCode',
            function (value, element) {
                return /^\+?[0-9]{1,3}$/.test(value);
            },
            "Merci de renseigner un numéro de téléphone valide ex. 06 01 02 03 04"
        );

        $('form.form-register').validate({
            ignore: [], // Hidden elements
            rules: {
                civility: {
                    required: true
                },
                last_name: {
                    required: true
                },
                first_name: {
                    required: true
                },
                birthday: {
                    required: true,
                    frenchDate: true
                },
                email: {
                    required: true,
                    email: true
                },
                phone_country_code: {
                    required: true,
                    phoneNumberCountryCode: true,
                    minlength: 3,
                    maxlength: 4
                },
                phone: {
                    required: true,
                    phoneNumber: true,
                    minlength: 14,
                    maxlength: 14
                },
                'g-recaptcha-response': {
                    required: true
                },
                cgv: {
                    required: true
                }
            },
            onfocusout: false,
            onkeyup: false,
            errorClass: 'has-error',
            validClass: '',
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').addClass(errorClass).removeClass(validClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass(errorClass).addClass(validClass);
            },
            errorPlacement: function () {
            },
            showErrors: function (errorMap, errorList) {
                $('.form-register-error').hide();
                if (errorList.length)
                    $('.form-register-error').show();

                this.defaultShowErrors();
            }
        });

        $('.btn-submit-form').click(function (e) {
            e.preventDefault();
            var btn = this;

            var form = $('form.form-register');
            if (form.valid()) {
                $(btn).button('loading');
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: $('form.form-quizz, form.form-register').serialize(),
                    dataType: 'json',
                    success: function (result) {
                        if (result && result.error)
                            return;

                        // Save created registration id
                        if (result.registration)
                            $('input[name="registration"]').val(result.registration);

                        // Put the first name in next page title
                        $('.congrats .text-yellow').text($('form.form-register #first_name').val());

                        stepTo(4);
                    },
                    complete: function () {
                        $(btn).button('reset');
                    }
                });
            }

        });

        /**
         * Share page
         */

        $('form.form-share-email').validate({
            rules: {
                'share-email': {
                    required: true,
                    email: true
                }
            },
            onfocusout: false,
            onkeyup: false,
            errorClass: 'has-error',
            validClass: '',
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').addClass(errorClass).removeClass(validClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.form-group').removeClass(errorClass).addClass(validClass);
            },
            errorPlacement: function () {
            },
            showErrors: function (errorMap, errorList) {
                $('.form-share-email-error').hide();
                if (errorList.length)
                    $('.form-share-email-error').show();

                this.defaultShowErrors();
            }
        });

        $('.btn-share-email').click(function (e) {
            e.preventDefault();
            var btn = this;
            var form = $('form.form-share-email');

            if (form.valid()) {
                $(btn).button('loading');
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function (result) {
                        if (result && result.error)
                            return;

                        $('#modal-flash-success .content').html('Invitation envoyée !');
                        $('#modal-flash-success').modal('show');
                    },
                    complete: function () {
                        $(btn).button('reset');
                    }
                });
            }

        });
    });

})(jQuery);