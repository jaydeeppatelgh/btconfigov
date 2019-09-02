define(
    [
        'jquery',
        'Magento_Catalog/js/catalog-add-to-cart'
    ],
    function ($) {

        'use strict';

        $.widget('borntechies.catalogAddToCartValidated', $.mage.catalogAddToCart, {
            _bindSubmit: function() {
                var self = this;
                this.element.mage('validation');
                this.element.on('submit', function(e) {
                    e.preventDefault();
                    if(self.element.valid()) {
                        self.submitForm($(this));
                    }
                });
            }
        });

        return $.borntechies.catalogAddToCartValidated;
    }
);