define([
    'Magento_Ui/js/form/components/button'
], function (Button) {
    'use strict';

    return Button.extend({
    	defaults: {
            buttonClasses: {},
            elementTmpl: 'Cytracon_UiBuilder/form/element/button'
        },
    })
});