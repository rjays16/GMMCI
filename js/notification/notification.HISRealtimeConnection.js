'use strict';

/**
 * Namespace
 * Please see http://autobahn.ws/js/reference_wampv1.html
 */
if ('object' !== typeof his) {
    var his = {};
    his.websocket = {
        models: {}
    };
} else {
    if ('object' !== typeof his.websocket) {
        his.websocket = {
            models: {}
        };
    } else {
        if ('object' !== typeof his.websocket.models) {
            his.websocket.models = {

            };
        }
    }
}


his.websocket.models.Subscription = function(topic, callback) 
{
    this.topic = topic;
    this.callback = callback;
};
his.websocket.HISRealtimeConnection = function(uri) 
{
    var _uri = uri,
        _realtimeConnection = this,
        _subscriptions = [],
        _events = {};

    this.session = null;

    var _subcribeAll = function() 
    {
        var _parent = this;
        if(_subscriptions.length > 0) {
            _subscriptions.forEach(function(val) {
                if(val instanceof his.websocket.models.Subscription) {
                    _parent.session.subscribe(val.topic, val.callback);
                }
            });
        }
    }

    var _executeEventCallbacks = function(event_name, args) 
    {
        var _arguments = [].slice.call(arguments, 0);;
        _arguments.splice(0, 1);
        var _parent = this;
        if(_events[event_name] || false) {
            _events[event_name].forEach(function(callback) {
                callback.apply(_parent, _arguments);
            });
        }
    };

    /**
     * Custom by jolly
     * @param  {[type]}   event_name [description]
     * @param  {Function} callback   [description]
     * @return {[type]}              [description]
     */
    this.on = function(event_name, callback) 
    {
        if(!_events[event_name] || false) {
            _events[event_name] = [];
        }
        _events[event_name].push(callback);
    };

    /**
     * A copy of session.subscribe function.
     * The difference. You can stack subscribing without minding the session.
     */
    this.subscribe = function(topic, callback) 
    {
        _subscriptions.push(
            new his.websocket.models.Subscription(topic, callback)
        );
        if(this.isConnected()) {
            this.session.subscribe(topic, callback);
        }
    };

    this.isConnected = function() 
    {
        return this.session || false;
    };

    this.connect = function() 
    {
        var _parent = this;
        return new ab.connect(_uri,
            function(session) {
                var _args = [].slice.call(arguments, 0);
                _args.unshift('connect');
                _executeEventCallbacks.apply(_parent, _args);
            },
            function(code, reason, detail) {
                var _args = [].slice.call(arguments, 0);
                _args.unshift('hangUp');
                _executeEventCallbacks.apply(_parent, _args);
            },
            // The session options
            {
                'maxRetries': 60,
                'retryDelay': 2000
            }
        );
    };
    /**
     * On open connection
     * @param  {[type]} session [description]
     * @return {[type]}         [description]
     */
    this.on('connect', function(session) 
    {
        this.session = session;
        _subcribeAll.apply(this);
        console.warn('WebSocket connection opened');
    });
    /**
     * On hang-up connection
     * @return {[type]} [description]
     */
    this.on('hangUp', function(code, reason, detail) 
    {
        this.session = null;
        console.warn('WebSocket connection closed');
    });
    return this;
};

his.frames = function(name){
    var _name = name;
    var _frame = document.getElementById(_name);

    this.getFrame = function(){
        return _frame;
    }

    this.changeFrame = function(name){
        _name = name;
    }
};