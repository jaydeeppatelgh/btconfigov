define([
    'ko',
    'uiComponent'
], function (ko, Component) {
    'use strict';

    return Component.extend({
        initialize: function () {
            //initialize parent Component
            this._super();
            this.qty = ko.observable(this.defaultQty);
        },

        decreaseQty: function() {
            var newQty = this.qty() - 1;
            if (newQty < this.defaultQty) {
                newQty = this.defaultQty;
            }
            this.qty(newQty);
        },

        increaseQty: function() {
            var newQty = this.qty() + 1;
            this.qty(newQty);
        }

    });
});