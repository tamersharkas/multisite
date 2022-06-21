(function($) {
    'use strict';
    $(document).ready(function() {

        jQuery('.datetimepicker').datetimepicker({
            defaultDate: "",
            dateFormat: "yy-mm-dd",
            numberOfMonths: 1,
            showButtonPanel: true,
            showOn: "button",
            buttonImage: AFW_Ajax.calendar_image,
            buttonImageOnly: true
        });

        var productType = jQuery('#product-type').val();
        if (productType == 'auction') {
            jQuery('.show_if_simple').show();
            jQuery('.inventory_options').show();
            jQuery('.general_options').show();
            jQuery('#inventory_product_data ._manage_stock_field').addClass('hide_if_auction').hide();
            jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('hide_if_auction').hide();
            jQuery('#inventory_product_data ._sold_individually_field').addClass('hide_if_auction').hide();
            jQuery('#inventory_product_data ._stock_field ').addClass('hide_if_auction').hide();
            jQuery('#inventory_product_data ._backorders_field ').parent().addClass('hide_if_auction').hide();
            jQuery('#inventory_product_data ._stock_status_field ').addClass('hide_if_auction').hide();
            jQuery('.options_group.pricing ').addClass('hide_if_auction').hide();
        } else {
            jQuery('#Auction.postbox').hide();
            jQuery('#Automatic_relist_auction.postbox').hide();
        }
        jQuery('#product-type').on('change', function() {
            if (jQuery(this).val() == 'auction') {
                jQuery('.show_if_simple').show();
                jQuery('.inventory_options').show();
                jQuery('.general_options').show();
                Jquery('.auction_tab_tab > a').click();
                jQuery('#inventory_product_data ._manage_stock_field').addClass('hide_if_auction').hide();
                jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('hide_if_auction').hide();
                jQuery('#inventory_product_data ._sold_individually_field').addClass('hide_if_auction').hide();
                jQuery('#inventory_product_data ._stock_field ').addClass('hide_if_auction').hide();
                jQuery('#inventory_product_data ._backorders_field ').parent().addClass('hide_if_auction').hide();
                jQuery('#inventory_product_data ._stock_status_field ').addClass('hide_if_auction').hide();
                jQuery('.options_group.pricing ').addClass('hide_if_auction').hide();
                jQuery('#Auction.postbox').show();
                jQuery('#Automatic_relist_auction.postbox').show();
            } else {
                jQuery('#Auction.postbox').hide();
                jQuery('#Automatic_relist_auction.postbox').hide();
            }
        });
        jQuery('label[for="_virtual"]').addClass('show_if_auction');
        jQuery('label[for="_downloadable"]').addClass('show_if_auction');

        var disabledclick = false;

        jQuery('.auction-table .action a:not(.disabled)').on('click', function(event) {


            if (disabledclick) {
                return;
            }

            jQuery('.auction-table .action a').addClass('disabled');
            disabledclick = true;
            var logid = $(this).data('id');
            var postid = $(this).data('postid');
            var curent = $(this);

            jQuery.ajax({
                type: "post",
                url: AFW_Ajax.ajaxurl,
                data: {
                    action: "delete_bid",
                    logid: logid,
                    postid: postid,
                    security: AFW_Ajax.AFW_nonce
                },
                success: function(response) {
                    if (response.action == 'deleted') {
                        curent.parent().parent().addClass('deleted').fadeOut('slow');
                    }

                    if (response.auction_current_bid) {

                        $('.postbox#Auction span.higestbid').html(response.auction_current_bid)
                    }

                    if (response.auction_current_bider) {
                        $('.postbox#Auction span.higestbider').html(response.auction_current_bider)
                    }

                    disabledclick = false;
                    jQuery('.auction-table .action a').removeClass('disabled');


                }
            });
            event.preventDefault();

        });


        jQuery('#Auction .removereserve').on('click', function(event) {
            var postid = $(this).data('postid');
            var curent = $(this);

            jQuery.ajax({
                type: "post",
                url: AFW_Ajax.ajaxurl,
                data: {
                    action: "remove_reserve_price",
                    postid: postid,
                    security: AFW_Ajax.AFW_nonce
                },
                success: function(response) {
                    if (response.error) {
                        curent.after(response.error)
                    } else {
                        if (response.succes) {
                            $('.postbox#Auction .reservefail').html(response.succes)
                        }
                    }
                }

            });
            event.preventDefault();

        });

        jQuery('#wsa-resend-winning-email').on('click', function(event) {
            var product_id = $(this).data('product_id');
            var curent = $(this);
            var $wrapper = $('#Auction');

            $("#resend-status").empty();

            $wrapper.block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });


            jQuery.ajax({
                type: "post",
                url: ajaxurl,
                data: {
                    action: "resend_winning_email",
                    product_id: product_id,
                    security: AFW_Ajax.AFW_nonce
                },
                success: function(response) {
                    if (response.error && jQuery.isEmptyObject(response.error) === false) {

                        $("#resend-status").append('<div class="error notice"><p class"error">' + response.error + '</p></div>');

                    }
                    if (response.succes && jQuery.isEmptyObject(response.succes) === false) {

                        $("#resend-status").append('<div class="updated notice"><p class"updated">' + response.succes + '</p></div>');

                    }
                    $wrapper.unblock();
                }
            });
            event.preventDefault();
        });

        jQuery('#general_product_data #_regular_price').on('keyup', function() {
            jQuery('#auction_tab #_regular_price').val(jQuery(this).val());
        });

        jQuery('#relistauction').on('click', function(event) {
            event.preventDefault();
            jQuery('.relist_auction_dates_fields').toggle();


        });

        if (jQuery('#_auction_proxy:checkbox:checked').length > 0) {
            $('.form-field._auction_sealed_field ').hide();

        }
        if (jQuery('#_auction_extend_enable:checkbox:checked').length > 0) {
            $('.form-field._auction_extend_in_time_field, .form-field._auction_extend_for_time_field').show();

        }
        if (jQuery('#_auction_sealed:checkbox:checked').length > 0) {
            $('.form-field._auction_proxy_field ').hide();

        }

        $("#_auction_proxy").on('change', function() {
            if (this.checked) {
                $('.form-field._auction_sealed_field ').slideUp('fast');
                $('#_auction_sealed').prop('checked', false);

            } else {
                $('.form-field._auction_sealed_field ').slideDown('fast');
            }
        });

        $("#_auction_sealed").on('change', function() {
            if (this.checked) {
                $('.form-field._auction_proxy_field ').slideUp('fast');
                $('#_auction_proxy').prop('checked', false);

            } else {
                $('.form-field._auction_proxy_field ').slideDown('fast');
            }
        });
        $("#_auction_extend_enable").on('change', function() {

            if (this.checked) {
                $('.form-field._auction_extend_in_time_field, .form-field._auction_extend_for_time_field').slideDown('fast');
            } else {
                $('.form-field._auction_extend_in_time_field, .form-field._auction_extend_for_time_field ').slideUp('fast');
            }
        });
        jQuery('.inventory_options').addClass('show_if_auction').show();

        if (typeof $('#auctions_for_woocommerce_dont_mix_shop') !== 'undefined' && $('#auctions_for_woocommerce_dont_mix_shop').length) {

            if ($('#auctions_for_woocommerce_dont_mix_shop').is(':checked')) {
                if (this.checked) {
                    document.getElementById("auctions_for_woocommerce_dont_mix_cat").disabled = false;
                    document.getElementById("auctions_for_woocommerce_dont_mix_tag").disabled = false;
                } else {
                    document.getElementById("auctions_for_woocommerce_dont_mix_cat").disabled = true;
                    document.getElementById("auctions_for_woocommerce_dont_mix_tag").disabled = true;

                }
            }
        }

        $("#auctions_for_woocommerce_dont_mix_shop").on('change', function() {
            if (this.checked) {
                document.getElementById("auctions_for_woocommerce_dont_mix_cat").disabled = false;
                document.getElementById("auctions_for_woocommerce_dont_mix_tag").disabled = false;
            } else {
                document.getElementById("auctions_for_woocommerce_dont_mix_cat").disabled = true;
                document.getElementById("auctions_for_woocommerce_dont_mix_tag").disabled = true;

            }
        });
        if ( $.isFunction($.fn.DataTable) ) {
            $('#Auction .auction-table').DataTable({
                dom: 'lfBrtip',
                "order": [0, 'desc'],
                stateSave: true,
                "pageLength": 20,
                responsive: true,
                "columns": [
                    null,
                    null,
                    null, {
                        "visible": false
                    }, {
                        "visible": false
                    }, {
                        "visible": false
                    }, {
                        "visible": false
                    },
                    null, {
                        "orderable": false
                    },
                ],
                buttons: [
                    'colvis', {
                        extend: 'csv',
                        exportOptions: {
                            columns: 'th:not(:last-child)'
                        }
                    }, {
                        extend: 'excel',
                        exportOptions: {
                            columns: 'th:not(:last-child)'
                        }
                    },

                ],
                "language": {
                    "sEmptyTable": AFW_Ajax.datatable_language.sEmptyTable,
                    "sInfo": AFW_Ajax.datatable_language.sInfo,
                    "sInfoEmpty": AFW_Ajax.datatable_language.sInfoEmpty,
                    "sInfoFiltered": AFW_Ajax.datatable_language.sInfoFiltered,
                    "sLengthMenu": AFW_Ajax.datatable_language.sLengthMenu,
                    "sLoadingRecords": AFW_Ajax.datatable_language.sLoadingRecords,
                    "sProcessing": AFW_Ajax.datatable_language.sProcessing,
                    "sSearch": AFW_Ajax.datatable_language.sSearch,
                    "sZeroRecords": AFW_Ajax.datatable_language.sZeroRecords,
                    "oPaginate": {
                        "sFirst": AFW_Ajax.datatable_language.oPaginate.sFirst,
                        "sLast": AFW_Ajax.datatable_language.oPaginate.sLast,
                        "sNext": AFW_Ajax.datatable_language.oPaginate.sNext,
                        "sPrevious": AFW_Ajax.datatable_language.oPaginate.sPrevious
                    },
                    "oAria": {
                        "sSortAscending": AFW_Ajax.datatable_language.oAria.sSortAscending,
                        "sSortDescending": AFW_Ajax.datatable_language.oAria.sSortDescending,
                    }
                }
            }); 
        };

    });


})(jQuery);