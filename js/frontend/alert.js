if ('undefined' === typeof Alerts) {
    window.Alerts = function() {
        var mustache = '<div class="messageBox">' +
            '<div class="messageTitle"><i class="fa {{icon}}" style="color:{{iconColor}}"></i> <span>{{title}}</span></div>' +
            '<div class="messageContent">{{{content}}}</div>' +
            '<div class="messageAction">' +
            '{{#actions}}' +
                '{{{.}}}' +
            '{{/actions}}' +
            '</div>' +
        '</div>';

        var _result;

        function init() {

            jQuery.blockUI.defaults = {
                // message displayed when blocking (use null for no message)
                message:  null,

                title: null,        // title string; only used when theme == true
                draggable: false,    // only used when theme == true (requires jquery-ui.js to be loaded)

                theme: false, // set to true to use with jQuery UI themes

                // styles for the message when blocking; if you wish to disable
                // these and use an external stylesheet then do this in your code:
                // jQuery.blockUI.defaults.css = {};
                css: {
                    padding:        '15px',
                    margin:         0,
                    width:          '100%',
                    top:            '35%',
                    left:           '0%',
                    textAlign:      'center',
                    color:          '#fff',
                    border:         'none',
                    backgroundColor:'rgba(0,0,0,0.8)',
                    cursor:         'default',
                    zIndex:         '10011',
                },

                // minimal style set used when themes are used
                themedCSS: {
                    width:  '30%',
                    top:    '40%',
                    left:   '35%'
                },

                // styles for the overlay
                overlayCSS:  {
                    backgroundColor: '#000',
                    opacity:         0.6,
                    cursor:          'default',
                    zIndex:          '10000',
                },

                // style to replace wait cursor before unblocking to correct issue
                // of lingering wait cursor
                cursorReset: 'default',

                // styles applied when using jQuery.growlUI
                growlCSS: {
                    width:    '350px',
                    top:      '10px',
                    left:     '',
                    right:    '10px',
                    border:   'none',
                    padding:  '5px',
                    opacity:   0.6,
                    cursor:    null,
                    color:    '#fff',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius':    '10px'
                },

                // IE issues: 'about:blank' fails on HTTPS and javascript:false is s-l-o-w
                // (hat tip to Jorge H. N. de Vasconcelos)
                iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank',

                // force usage of iframe in non-IE browsers (handy for blocking applets)
                forceIframe: false,

                // z-index for the blocking overlay
                baseZ: 1000,

                // set these to true to have the message automatically centered
                centerX: true, // <-- only effects element blocking (page block controlled via css above)
                centerY: true,

                // allow body element to be stetched in ie6; this makes blocking look better
                // on "short" pages.  disable if you wish to prevent changes to the body height
                allowBodyStretch: true,

                // enable if you want key and mouse events to be disabled for content that is blocked
                bindEvents: true,

                // be default blockUI will supress tab navigation from leaving blocking content
                // (if bindEvents is true)
                constrainTabKey: true,

                // fadeIn time in millis; set to 0 to disable fadeIn on block
                fadeIn:  200,

                // fadeOut time in millis; set to 0 to disable fadeOut on unblock
                fadeOut:  400,

                // time in millis to wait before auto-unblocking; set to 0 to disable auto-unblock
                timeout: 0,

                // disable if you don't want to show the overlay
                showOverlay: true,

                // if true, focus will be placed in the first available input field when
                // page blocking
                focusInput: true,

                // suppresses the use of overlay styles on FF/Linux (due to performance issues with opacity)
                // no longer needed in 2012
                // applyPlatformOpacityRules: true,

                // callback method invoked when fadeIn has completed and blocking message is visible
                onBlock: null,

                // callback method invoked when unblocking has completed; the callback is
                // passed the element that has been unblocked (which is the window object for page
                // blocks) and the options that were passed to the unblock call:
                //   onUnblock(element, options)
                onUnblock: null,

                // don't ask; if you really must know: http://groups.google.com/group/jquery-en/browse_thread/thread/36640a8730503595/2f6a79a77a78e493#2f6a79a77a78e493
                quirksmodeOffsetHack: 4,

                // class name of the message block
                blockMsgClass: 'messageBox',

                // if it is already blocked, then ignore it (don't unblock and reblock)
                ignoreIfBlocked: false
            };

            Mustache.parse(mustache);
        }

        /**
         *
         */
        function playSound(id) {
            Alerts.sounds = Alerts.sounds || {};
            if (typeof Alerts.sounds[id] === 'undefined') {
                var audioElement = document.createElement("audio");
                audioElement.setAttribute("src", id);
                $.get();
                audioElement.addEventListener("load", function () {
                    audioElement.play()
                }, true);
                audioElement.pause();
                audioElement.play();
                Alerts.sounds[id] = audioElement;
            } else {
                Alerts.sounds[id].play();
            }
        }

        /**
         * Generic alert method
         * @param  options
         * @returns null
         */
        function alert(options) {
            options = jQuery.extend({
                icon : 'fa-warning',
                iconColor : '#000',
                title : '',
                content : '',
                sound  : Alerts.SOUND_CHIME,
                callback: null,
                actions: [ Alerts.ACTION_OK ],
                /* callbacks */

                /* events */
                onBlock : function() {
                    jQuery('.messageBox [data-toggle=Alerts#close]').click(Alerts.close).focus();
                },
                onUnblock: function() {
                    if (options['callback']) {
                        options['callback'](Alerts._result);
                    }
                }
            }, options);


            if (options.sound) {
                playSound(options.sound);
            }

            var undefined;
            Alerts._result = undefined;
            jQuery.blockUI({
                message: Mustache.render(mustache, options),
                onBlock: options.onBlock,
                onUnblock: options.onUnblock
            });
        }

        /**
         * Convenience function for displaying hard error alerts
         * @param Array options
         */
        function error(options) {
            options = jQuery.extend({
                title : 'Error',
                content : '',
                icon : 'fa-warning',
                iconColor : '#c00',
                actions : [ Alerts.ACTION_OK ],
                onBlock : function() {
                    jQuery('.messageBox [data-toggle=Alerts#close]').click(Alerts.close).focus();
                },
            }, options);
            this.alert(options);
        }

        /**
         * Convenience function for displaying loading messages
         * @param Array options
         */
        function loading(options) {
            options = jQuery.extend({
                icon : 'fa-spin fa-gear',
                iconColor : 'rgb(0, 125, 196)',
                title : 'Loading',
                content : 'Please wait...',
                sound : null,
                actions: null,
                onBlock : null,
                onUnblock: null
            }, options);
            this.alert(options);
        }

        /**
         * Convenience function for displaying light warning messages
         * @param Array options
         */
        function warn(options) {
            options = jQuery.extend({
                title : 'Warning',
                content : '',
                icon : 'fa-warning',
                iconColor : '#EC4C00',
                actions: [ Alerts.ACTION_OK ],
                onBlock : function() {
                    jQuery('.messageBox [data-toggle=Alerts#close]').click(Alerts.close).focus();
                }
            }, options);
            this.alert(options);
        }

        /**
         * Dialog for prompting confirmation from the user
         * @param Array options
         */
        function confirm(options) {
            options = jQuery.extend({
                title : '',
                content : '',
                callback: null,
            }, options);

            options['icon'] = 'fa-question-circle';
            options['iconColor'] = '#006A7E';
            options['actions'] = [ Alerts.ACTION_YES, Alerts.ACTION_NO ];
            options['onBlock'] = function() {
                jQuery('.messageBox [data-toggle=Alerts#close]').click(function() {
                    Alerts.close();
                    if ($(this).data('action') == 'yes') {
                        Alerts._result = true;
                    } else {
                        Alerts._result = false;
                    }
                });
                jQuery('.messageBox [data-action=yes]').focus();
            };
            this.alert(options);
        }

        /**
         *
         */
        function close() {
            jQuery.unblockUI();
        }

        init();
        return {
            SOUND_CHIME: 'js/frontend/sounds/chime.mp3',
            ACTION_OK: '<button class="btn" data-toggle="Alerts#close" data-action="ok"><i class="fa fa-check-circle"></i> OK</button> ',
            ACTION_YES: '<button class="btn" data-toggle="Alerts#close" data-action="yes"><i class="fa fa-check" style="color:green"></i> Yes</button> ',
            ACTION_NO: '<button class="btn" data-toggle="Alerts#close" data-action="no"><i class="fa fa-times" style="color:red"></i> No</button> ',
            sounds: {}, // sound cache
            alert: alert,
            close: close,
            confirm: confirm,
            error: error,
            loading: loading,
            warn: warn,
        };
    }();
}