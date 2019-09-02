/*jshint browser:true jquery:true*/
define([
    "jquery",
    "ko",
    'mage/storage'
], function($, ko, storage){
    "use strict";

    $.widget('mage.licensePlateExtendedSearch', {
            options: {
                searchManufacturer: '[data-role=licenseplate-manufacturer]',
                searchModel: '[data-role=licenseplate-model]',
                searchMotor: '[data-role=licenseplate-motor]',
                searchGeneration: '[data-role=licenseplate-generation]',
                searchConstructionPeriod: '[data-role=licenseplate-construction-period]',
                searchModelId: '#licenseplate-search-model-id',
                errorMessagesBlock: '#licenseplate-search-messages',
                updateUrl: null,
                submitUrl: null
            },
            _create: function () {
                this.searchForm = $(this.options.form);
                this.manufacture = $(this.options.searchManufacturer);
                this.model = $(this.options.searchModel);
                this.motor = $(this.options.searchMotor);
                this.generation = $(this.options.searchGeneration);
                this.constructionPeriod = $(this.options.searchConstructionPeriod);
                this.model.attr('disabled', 'disabled');
                this.motor.attr('disabled', 'disabled');
                this.generation.attr('disabled', 'disabled');
                this.constructionPeriod.attr('disabled', 'disabled');

                _.bindAll(this, '_onPropertyChange', '_onSubmit');
                if ((/msie|trident|edge/i).test(navigator.userAgent)) {
                    this.element.on('change input', this._onPropertyChange);
                } else {
                    this.element.on('input propertychange', this._onPropertyChange);
                }


                this.searchForm.on('submit', $.proxy(function() {
                    this._onSubmit();
                }, this));
            },
            _onSubmit: function (e) {
                if (!$(this.options.searchModelId).val()) {
                    e.preventDefault();
                }
                this.searchForm.attr('action', this.options.submitUrl + 'model_id/' + $(this.options.searchModelId).val());
            },
            _onPropertyChange: function (e) {
                var _this = this;
                $(this.options.errorMessagesBlock).html('');
                var nextField = $(this.options.form + ' :input:eq(' + ($(this.options.form + ' :input').index(e.target) + 1) + ')'); //$(e.target).next('select'); //
                if ($(nextField).length && $(e.target).val()) {
                    $(nextField).html('');
                    $(this.options.form + ' :input:gt(' + ($(this.options.form + ' :input').index(e.target)) + ')').each(function() {
                            $(this).html('');
                        }
                    );

                    $.ajax({
                        type: "POST",
                        url: this.options.updateUrl,
                        data: $(this.searchForm).serialize(),
                        success: function(data) {
                            if (data.errors) {
                                $.each(data.errors, function(el) {
                                    $(_this.options.errorMessagesBlock).append(this)
                                        .append('<br />');
                                });
                                return;
                            }
                            var field = _this.searchForm.find('[name='+data.field+']');
                            if (Object.keys(data.options).length > 1) {
                                $(field).append($("<option disabled selected></option>")
                                    .text(data.caption));
                                $.each(data.options, function(key, value) {
                                    if (typeof  value === 'object') {
                                        $(field).append($("<option></option>")
                                            .attr("value", value.model_id)
                                            .text(value.period));
                                    } else {
                                        $(field).append($("<option></option>")
                                            .attr("value",key)
                                            .text(value));
                                    }
                                });
                                field.removeAttr('disabled');

                            } else {
                                $.each(data.options, function(key, value) {
                                    if (typeof  value === 'object') {
                                        $(field).append($("<option selected></option>")
                                            .attr("value", value.model_id)
                                            .text(value.period));
                                    } else {
                                        $(field).append($("<option selected></option>")
                                            .attr("value",key)
                                            .text(value));
                                    }
                                });
                                field.removeAttr('disabled');
                                if ((/msie|trident|edge/i).test(navigator.userAgent)) {
                                    $(field).trigger('change');
                                } else {
                                    $(field).trigger('propertychange');
                                }
                                return this;

                            }

                        }
                    });
                }

                if (!$(nextField).length) {
                    $(this.options.searchModelId).val($(e.target).val());
                    this.submitForm();

                }

            },
            submitForm: function() {
                if ($(this.options.form).validation() &&
                    $(this.options.form).validation('isValid')
                    ) {
                    $(this.options.form).submit();
                }
            }
        }
    );

    return $.mage.licensePlateExtendedSearch;
});