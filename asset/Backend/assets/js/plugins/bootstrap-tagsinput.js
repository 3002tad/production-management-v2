/*!
 * bootstrap-tagsinput v0.9.0
 * https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/
 */

(function($) {
    "use strict";

    var TagsInput = function(element, options) {
        this.element = $(element);
        this.options = $.extend({}, TagsInput.defaults, options);
        this.init();
    };

    TagsInput.prototype = {
        init: function() {
            var self = this;
            
            this.element.on('change', function() {
                self.parseTags();
            });
            
            this.parseTags();
        },

        parseTags: function() {
            var self = this;
            var value = this.element.val();
            
            if (value) {
                var tags = value.split(this.options.tagDelimiter || ',');
                this.element.data('tags', tags);
            }
        },

        addTag: function(tag) {
            var tags = this.element.data('tags') || [];
            if (tags.indexOf(tag) === -1) {
                tags.push(tag);
                this.element.data('tags', tags);
                this.element.val(tags.join(this.options.tagDelimiter || ','));
            }
        },

        removeTag: function(tag) {
            var tags = this.element.data('tags') || [];
            var index = tags.indexOf(tag);
            if (index !== -1) {
                tags.splice(index, 1);
                this.element.data('tags', tags);
                this.element.val(tags.join(this.options.tagDelimiter || ','));
            }
        }
    };

    TagsInput.defaults = {
        tagDelimiter: ',',
        allowDuplicates: false
    };

    $.fn.tagsinput = function(option) {
        return this.each(function() {
            var $this = $(this);
            var data = $this.data('tagsinput');
            var options = typeof option === 'object' && option;

            if (!data) {
                data = new TagsInput(this, options);
                $this.data('tagsinput', data);
            }
            if (typeof option === 'string') {
                data[option].apply(data, Array.prototype.slice.call(arguments, 1));
            }
        });
    };

})(jQuery);
