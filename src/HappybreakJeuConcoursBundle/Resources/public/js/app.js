/**
 * Coffee & Brackets software studio
 * @author Mohamed KRISTOU <krisstwo@gmail.com>.
 */

(function ($) {

    $(document).ready(function () {

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
            startDate: new Date()
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
                gender: {
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
                phone: {
                    required: true,
                    regex: /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/
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

            if($('form.form-register').valid())
                stepTo(4);
        });

    });

})(jQuery);