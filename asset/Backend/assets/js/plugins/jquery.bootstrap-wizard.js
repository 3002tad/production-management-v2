/**
 * Bootstrap Wizard - A simple wizard/stepping form that only uses bootstrap components and css
 * https://github.com/VinceG/twitter-bootstrap-wizard
 * 
 * Licensed under the MIT License (https://github.com/VinceG/twitter-bootstrap-wizard/blob/master/LICENSE)
 */

(function($) {
    var bootstrapWizard = function(element, options) {
        this.$element = $(element);
        this.options = $.extend({}, $.fn.bootstrapWizard.defaults, options);
        this.currentIndex = 0;
        this.numSteps = this.$element.find('.nav-tabs [role="tab"]').length;
        this.init();
    };

    bootstrapWizard.prototype = {
        init: function() {
            var that = this;
            
            // Handle tab clicks
            this.$element.find('.nav-tabs [role="tab"]').on('click', function(e) {
                e.preventDefault();
                that.selectTab($(this).data('tab-index') || that.$element.find('.nav-tabs [role="tab"]').index($(this)));
            });

            // First tab
            this.$element.find('.nav-tabs [role="tab"]:first').addClass('active');
            this.$element.find('.tab-content .tab-pane:first').addClass('active');
        },

        selectTab: function(index) {
            if (index < 0 || index >= this.numSteps) return false;
            
            this.currentIndex = index;
            
            this.$element.find('.nav-tabs [role="tab"]').removeClass('active');
            this.$element.find('.nav-tabs [role="tab"]').eq(index).addClass('active');
            
            this.$element.find('.tab-content .tab-pane').removeClass('active');
            this.$element.find('.tab-content .tab-pane').eq(index).addClass('active');
            
            return true;
        },

        nextTab: function() {
            return this.selectTab(this.currentIndex + 1);
        },

        previousTab: function() {
            return this.selectTab(this.currentIndex - 1);
        }
    };

    $.fn.bootstrapWizard = function(option) {
        return this.each(function() {
            var $this = $(this);
            var data = $this.data('bootstrapWizard');
            var options = typeof option === 'object' && option;

            if (!data) {
                data = new bootstrapWizard(this, options);
                $this.data('bootstrapWizard', data);
            }
            if (typeof option === 'string') {
                data[option].call(data);
            }
        });
    };

    $.fn.bootstrapWizard.defaults = {
        onNext: null,
        onPrevious: null,
        onTabClick: null,
        onTabShow: null
    };

})(jQuery);
