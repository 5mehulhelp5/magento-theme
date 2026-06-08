require(
    [
        'Magento_Ui/js/lib/validation/validator',
        'jquery',
        'mage/translate'
], function(validator, $){
        validator.addRule(
            'blogurl-validation',
            function (value) {
                return !(/[^a-z^A-Z^0-9\.\-]/g.test(value));
            },
            $.mage.__('not allowed the url only allowed string.')
        );
});