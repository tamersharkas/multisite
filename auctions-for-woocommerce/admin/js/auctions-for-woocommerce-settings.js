(function($) {
    'use strict';
    $(document).ready(function() {

        if (typeof $('#auctions_for_woocommerce_dont_mix_shop') !== 'undefined' && $('#auctions_for_woocommerce_dont_mix_shop').length) {
            if ($('#auctions_for_woocommerce_dont_mix_shop').is(':checked')) {
                document.getElementById("auctions_for_woocommerce_dont_mix_cat").disabled = false;
                document.getElementById("auctions_for_woocommerce_dont_mix_tag").disabled = false;
            } else {
                document.getElementById("auctions_for_woocommerce_dont_mix_cat").disabled = true;
                document.getElementById("auctions_for_woocommerce_dont_mix_tag").disabled = true;


            }
        }

        $("#auctions_for_woocommerce_dont_mix_shop").on('change', function() {
            console.log('t');
            if (this.checked) {
                document.getElementById("auctions_for_woocommerce_dont_mix_cat").disabled = false;
                document.getElementById("auctions_for_woocommerce_dont_mix_tag").disabled = false;
            } else {
                document.getElementById("auctions_for_woocommerce_dont_mix_cat").disabled = true;
                document.getElementById("auctions_for_woocommerce_dont_mix_tag").disabled = true;

            }
        });

    });


})(jQuery);