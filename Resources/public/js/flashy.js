"use strict";

var flashy = (function() {
    var Flashy = function() {
        var flashy = this,

            container,
            template,

            defaultDelay = 2800,

            selPrefix          = 'lexty-',
            selContainerId     = selPrefix + 'flashy-container',
            selContainerClass  = selPrefix + 'flashy-container',
            selTemplateId      = selPrefix + 'flashy-template',
            selFlashyClass     = selPrefix + 'flashy',
            selMessageClass    = selPrefix + 'flashy-message',
            selTypeClassPrefix = selPrefix + 'flashy-type-';


        container = document.getElementById(selContainerId) || createContainer();

        var tplScript = document.getElementById(selTemplateId);
        template = tplScript ? tplScript.firstChild : createTemplate();

        flashy.types = {
            success: 'success',
            info   : 'info'
        };

        /**
         * @param {object} options
         * @param {number} [options.delay]
         */
        flashy.setOptions = function(options) {
            if (!options) return;

            if (options.hasOwnProperty('delay')) defaultDelay = options.delay;
        };

        /**
         * @param {string} message
         * @param {string} type
         * @param {number} [delay]
         */
        flashy.add = function(message, type, delay) {
            delay || (delay = defaultDelay);

            var newFlashy = createFlashy(type, message);
            container.appendChild(newFlashy);
            fadeIn(newFlashy, function() {
                setTimeout(function() {
                    fadeOut(newFlashy, function() {
                        container.removeChild(newFlashy);
                    });
                }, delay);
            });
            return newFlashy;
        };

        /**
         * @param {Array} data
         */
        flashy.render = function(data) {
            var len = data.length;
            for (var i = 0; i < len; i++) {
                flashy.add(data[i].message, data[i].type, data[i].delay);
            }
        };

        function createContainer() {
            var container = document.createElement('div');
            container.className = selContainerClass;
            document.body.insertBefore(container, document.body.firstChild);
            return container;
        }

        function createTemplate() {
            var tpl = document.createElement('div');
            tpl.className += ' ' + selFlashyClass;
            var msg = document.createElement('span');
            msg.className += ' ' + selMessageClass;
            tpl.appendChild(msg);
            return tpl;
        }

        function createFlashy(type, message) {
            //var wrapper = document.createElement('div');
            //wrapper.innerHTML = template.innerHTML;
            var newFlashy = template.cloneNode(true);
            newFlashy.className += ' ' + selTypeClassPrefix + type;
            newFlashy.style.opacity = 0;
            var msg = newFlashy.getElementsByClassName(selMessageClass)[0];
            msg.innerHTML = message;
            return newFlashy;
        }

        function fadeOut(element, callback, duration) {
            fade('out', element, callback, duration)
        }

        function fadeIn(element, callback, duration) {
            fade('in', element, callback, duration)
        }

        function fade(type, element, callback, duration) {
            duration || (duration = 300);
            var op    = 'in' === type ? 0 : 1;
            var tick = 50;
            var steps = duration / tick;
            var step = 1 / steps;

            var timer = setInterval(function () {
                if (('in' === type && op >= (1 - step)) || ('in' !== type && op <= step)) {
                    clearInterval(timer);
                    if ('in' !== type) element.style.display = 'none';
                    if (callback) {
                        callback();
                    }
                }
                element.style.opacity = op;
                element.style.filter = 'alpha(opacity=' + op * 100 + ")";
                if ('in' === type) {
                    op += step;
                } else {
                    op -= step;
                }
            }, tick);
        }
    };

    return new Flashy();
}());

if (window._lexty_flashy_data) {
    flashy.render(window._lexty_flashy_data);
}