define([
    'Magento_Ui/js/grid/columns/select'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html'
        },
        getLabel: function (record) {
            var label = this._super(record);

            if (label !== '') {
                return '<span class="tooltip-severity ' + record.status + '"><span>' + label + '</span></span>';
            }

            return label;
        }
    });
});

