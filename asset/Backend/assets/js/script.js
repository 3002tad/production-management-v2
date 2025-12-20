/**
 * Custom Project Scripts
 * Production Management System v2
 */

(function($) {
    'use strict';

    // Initialize selectors and components
    $(document).ready(function() {
        // Initialize selectpicker if available
        if ($.fn.selectpicker) {
            $('.selectpicker').selectpicker();
        }

        // Initialize tagsinput if available
        if ($.fn.tagsinput) {
            $('.tagsinput').tagsinput();
        }

        // Initialize file input
        if ($.fn.fileinput) {
            $('.fileinput').fileinput();
        }

        // Initialize validation if available
        if ($.fn.validate) {
            $('form').validate({
                errorClass: 'is-invalid',
                validClass: 'is-valid',
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                }
            });
        }

        // Handle dynamic content
        if (typeof Arrive !== 'undefined') {
            Arrive.on('.dynamic-content', function() {
                console.log('Dynamic content loaded');
                
                // Re-initialize components for dynamic content
                if ($.fn.selectpicker) {
                    $('.selectpicker').selectpicker('refresh');
                }
            });
        }

        // Initialize tooltips and popovers if Bootstrap is available
        if (typeof bootstrap !== 'undefined') {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
                new bootstrap.Tooltip(el);
            });

            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function(el) {
                new bootstrap.Popover(el);
            });
        }

        // Handle form submissions
        $('form').on('submit', function(e) {
            if ($.fn.validate) {
                if (!$(this).valid()) {
                    e.preventDefault();
                    return false;
                }
            }
        });

        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
            }
        });
    });

    // Helper functions
    window.scriptUtils = {
        // Show toast/alert message
        showMessage: function(message, type) {
            type = type || 'info';
            var alertClass = 'alert alert-' + type;
            var alert = $('<div class="' + alertClass + ' alert-dismissible fade show" role="alert">' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
            
            $('body').prepend(alert);
            setTimeout(function() {
                alert.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },

        // Format date
        formatDate: function(date, format) {
            format = format || 'DD/MM/YYYY';
            var d = new Date(date);
            return format.replace('DD', ('0' + d.getDate()).slice(-2))
                         .replace('MM', ('0' + (d.getMonth() + 1)).slice(-2))
                         .replace('YYYY', d.getFullYear());
        },

        // AJAX helper
        ajax: function(url, options) {
            return $.ajax($.extend({}, {
                url: url,
                type: 'GET',
                dataType: 'json'
            }, options));
        }
    };

})(jQuery);
