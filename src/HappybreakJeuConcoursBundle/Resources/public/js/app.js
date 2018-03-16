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

            if ($('form.form-quizz').valid())
                stepTo(3);
        });

        /**
         * Form page
         */

        $('.step-3 .btn-back').click(function (e) {
            e.preventDefault();

            stepTo(2);
        });

        $('form.form-register #birthday').datepicker({
            language: 'fr',
            endDate: new Date()
        });

        $.validator.addMethod(
            'frenchDate',
            function (value, element) {
                return value.match(/^\d\d?\/\d\d?\/\d\d\d\d$/);
            },
            "Merci de renseigner une date au format jj/mm/aaaa."
        );

        $('form.form-register').validate({
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
                    frenchDate: true
                },
                email: {
                    required: true,
                    email: true
                },
                phone: {
                    required: true
                    // regex: /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/
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

                        if (result.registration)
                            $('input[name="registration"]').val(result.registration);

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