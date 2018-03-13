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

        $('.btn-validate-quizz').click(function (e) {
            e.preventDefault();

            stepTo(3);
        });

        /**
         * Form page
         */

        $('.step-3 .btn-back').click(function (e) {
            e.preventDefault();

            stepTo(2);
        });

        $('.btn-submit-form').click(function (e) {
            e.preventDefault();

            stepTo(4);
        });

    });

})(jQuery);