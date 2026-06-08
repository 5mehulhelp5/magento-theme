define([], function () {
    'use strict';

    return function (Payment) {
        return Payment.extend({
            defaults: {
                template: 'Forever_Onestep/payment'
            }
        });
    }
});
