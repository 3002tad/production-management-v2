/*!
 * Bootstrap Select v1.13.18 (https://developer.snapappointments.com/bootstrap-select)
 * Copyright 2012-2020 SnapAppointments
 * Licensed under MIT (https://github.com/snapappointments/bootstrap-select/blob/master/LICENSE)
 */

(function($) {
    'use strict';

    $.fn.selectpicker = function(options) {
        return this.each(function() {
            var $this = $(this);
            var data = $this.data('selectpicker');
            
            if (!data) {
                data = new SelectPicker(this, options);
                $this.data('selectpicker', data);
            } else if (typeof options === 'string') {
                data[options].call(data);
            }
        });
    };

    var SelectPicker = function(element, options) {
        this.$element = $(element);
        this.options = $.extend({}, SelectPicker.DEFAULTS, options);
    };

    SelectPicker.DEFAULTS = {
        noneSelectedText: 'Nothing selected',
        noneResultsText: 'No results matched {0}',
        countSelectedText: '{0} of {1} selected',
        maxOptionsText: ['Limit reached ({n} selected)', 'Group limit reached ({n} selected)'],
        multipleSeparator: ', ',
        style: '',
        size: 'auto',
        title: null,
        selectedTextFormat: 'values',
        width: false,
        container: false,
        hideDisabled: false,
        showSubtext: false,
        showTick: false,
        showContent: true,
        dropupAuto: true,
        header: false,
        liveSearch: false,
        actionsBox: false,
        doneButton: false,
        doneButtonText: 'Close',
        maxOptions: false,
        deselectAllText: 'Deselect all',
        selectAllText: 'Select all',
        selectOnTab: false,
        dropdownAlignRight: false,
        virtualScroll: 'auto',
        mobileNoneSelectedText: 'Nothing selected'
    };

})(jQuery);
