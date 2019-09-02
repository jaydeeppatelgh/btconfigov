/*jshint browser:true jquery:true*/
define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    /**
     * Check whether the incoming string is not empty or if doesn't consist of spaces.
     *
     * @param {String} value - Value to check.
     * @returns {Boolean}
     */
    function isEmpty(value) {
        return (value.length === 0) || (value == null) || /^\s+$/.test(value);
    }

    $.widget('mage.licensePlateSearch', {
        options: {
            autocomplete: 'off',
            minSearchLength: 8,
            submitBtn: 'button[type="submit"]',
            searchLabel: '[data-role=mini-licenseplate-search-label]',
            isExpandable: null
        },

        _create: function () {
            this.searchForm = $(this.options.formSelector);
            this.submitBtn = this.searchForm.find(this.options.submitBtn)[0];
            this.searchLabel = $(this.options.searchLabel);
            this.isExpandable = this.options.isExpandable;

            _.bindAll(this, '_onKeyUp', '_onPropertyChange', '_onSubmit');

            this.submitBtn.disabled = true;

            this.element.attr('autocomplete', this.options.autocomplete);



            this.searchLabel.on('click', function (e) {
                // allow input to lose its' focus when clicking on label
                if (this.isExpandable && this.isActive()) {
                    e.preventDefault();
                }
            }.bind(this));

            this.element.on('focus', this.setActiveState.bind(this, true));
            this.element.on('keyup', this._onKeyUp);
            this.element.on('input propertychange', this._onPropertyChange);

            this.searchForm.on('submit', $.proxy(function() {
                this._onSubmit();
            }, this));
        },

        /**
         * Checks if search field is active.
         *
         * @returns {Boolean}
         */
        isActive: function () {
            return this.searchLabel.hasClass('active');
        },

        isDigit: function (c) {
            return /^\d$/.test(c);
        },

        isLetter: function (c) {
            return /^[a-zA-Z]$/.test(c);
        },

        /**
         * Sets state of the search field to provided value.
         *
         * @param {Boolean} isActive
         */
        setActiveState: function (isActive) {
            this.searchLabel.toggleClass('active', isActive);

            if (this.isExpandable) {
                this.element.attr('aria-expanded', isActive);
            }
        },

        /**
         * Executes when the search box is submitted. Sets the search input field to the
         * value of the selected item.
         * @private
         * @param {Event} e - The submit event
         */
        _onSubmit: function (e) {
            var value = this.element.val();

            if (isEmpty(value)) {
                e.preventDefault();
            }
        },

        /**
         * Executes when keys are pressed in the search input field. Performs specific actions
         * depending on which keys are pressed.
         * @private
         * @param {Event} e - The key down event
         * @return {Boolean} Default return type for any unhandled keys
         */
        _onKeyUp: function (e) {
            var keyCode = e.keyCode || e.which;
            var  value = this.element.val();

            switch (keyCode) {
                case $.ui.keyCode.ENTER:
                case $.ui.keyCode.BACKSPACE:
                case $.ui.keyCode.DELETE:
                    break;
                default:
                    if (value.length <= parseInt(this.options.minSearchLength, 10)) {
                        var kt = value.replace(/\-/g,'');
                        var newkt = '';
                        var kt2 = '';
                        for(var i=0;(i<kt.length&&i<6);i++) {
                            var c = kt.charAt(i);
                            if (this.isDigit(c) || this.isLetter(c)) {
                                kt2 += c;
                                newkt += c;
                                if (kt2.length == 2)newkt += '-';
                                if (kt2.length == 4)newkt += '-';
                                if (kt2.length == 6) {
                                    if (this.isDigit(kt2.charAt(4)) != this.isDigit(kt2.charAt(5))) {
                                        newkt = kt2.charAt(0) + kt2.charAt(1) + '-' + kt2.charAt(2) + kt2.charAt(3) + kt2.charAt(4) + '-' + kt2.charAt(5);
                                    } else if (this.isDigit(kt2.charAt(0)) != this.isDigit(kt2.charAt(1))) {
                                        newkt = kt2.charAt(0) + '-' + kt2.charAt(1) + kt2.charAt(2) + kt2.charAt(3) + '-' + kt2.charAt(4) + kt2.charAt(5);
                                    } else {
                                        newkt = kt2.charAt(0) + kt2.charAt(1) + '-' + kt2.charAt(2) + kt2.charAt(3) + '-' + kt2.charAt(4) + kt2.charAt(5);
                                    }
                                }
                            }
                        }
                        this.element.val(newkt);
                    }
            }
        },


        /**
         * Executes when the value of the search input field changes.
         * @private
         */
        _onPropertyChange: function () {
            var  value = this.element.val();
            this.submitBtn.disabled = isEmpty(value);
            if (value.length < parseInt(this.options.minSearchLength, 10)) {
                this.submitBtn.disabled = true;
            }
        }
    });

    return $.mage.licensePlateSearch;
});