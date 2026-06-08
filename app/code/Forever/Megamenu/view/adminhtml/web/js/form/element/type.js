define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function ($, _, uiRegistry, select) {
    'use strict';
    return select.extend({

        initialize: function () {
            var status = this._super().initialValue;
            this.fieldDepend(status);
            return this;
        },


        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            this.fieldDepend(value);
            return this._super();
        },

        /**
         * Update field dependency
         *
         * @param {String} value
         */
        fieldDepend: function (value) {
            setTimeout(function () {
                var custcateurl = uiRegistry.get('index = custcateurl');
                var image = uiRegistry.get('index = image');
                var catename = uiRegistry.get('index = catename');
                                
                var catid = uiRegistry.get('index = cat_id');
                var right = uiRegistry.get('index = rightcontent');
                var left = uiRegistry.get('index = leftcontent');

                console.log(value);
                // for home page silder
                if (value == "1") {
                    catid.show();
                    right.show();
                    left.show();
                    custcateurl.hide();
                    image.hide();
                    catename.hide();
                    catename.clear();
                    custcateurl.clear();
                    image.clear();
                } else {
                    custcateurl.show();
                    image.show();
                    catename.show();
                    catid.hide();
                    right.hide();
                    left.hide();
                    catid.clear();
                    right.clear();
                    left.clear();
                }
            }, 500);
            return this;
        }
    });
});