"use strict";

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


/**
 * Globally exposed namespacing function.
 * @public
 * @param {string} namespace
 * @return {object} A referene to the object created
 */
function namespace(namespace) {
    var object = window, tokens = namespace.split("."), token;

    while (tokens.length > 0) {
        token = tokens.shift();

        if (typeof object[token] === "undefined") {
            object[token] = {};
        }

        object = object[token];
    }

    return object;
}

/**
 * Globally exposed extending function.
 * @param {object} target
 * @param {hash} source
 * @return {object}
 */
function extend(target, source) {
    target = target || {};
    for (var prop in source) {
        if (typeof source[prop] === 'object') {
            target[prop] = extend(target[prop], source[prop]);
        } else {
            target[prop] = source[prop];
        }
    }
    return target;
}

/**
 * Sets up inheritance of the child object to the objects
 * supplied by the parents object.
 * @param {array} parents
 * @param {object} child
 * @param {hash} properties
 * @return {object} An object with inheritance
 */
function inherit(parents, child, properties) {
    var childPrototype = properties;

    for (var i in parents) {
        var obj = window[parents[i]];
        
        if (!obj) {
            obj = namespace(parents[i]);
            console.log([obj.prototype, parents[i]]);
        } else {
            console.log("did not find " + parents[i]);
        }

        var parentPrototype = Object.create(obj.prototype);
        childPrototype = extend(parentPrototype, childPrototype);
    }

    child.prototype = childPrototype;
    child.prototype.constructor = child;

    return child;
}

/**
 * Globally filters out jQuery elements matching selector
 * from the haystack. This expects javascript objects that
 * have a public "getRootNode" method.
 * @param {string} selector
 * @param {array} haystack
 * @return {array} matches
 */
function filter(selector, haystack) {
    var matches = [];

    haystack.forEach(function (hay) {
        var node = hay.getRootNode();
        if (node && node.is(selector)) {
            matches.push(hay);
        }
    });

    return matches;
}


function debounce(func, threshold, execAsap) {
    var timeout;

    return function debounced() {
        var obj = this, args = arguments;
        function delayed() {
            if (!execAsap)
                func.apply(obj, args);
            timeout = null;
        };

        if (timeout)
            clearTimeout(timeout);
        else if (execAsap)
            func.apply(obj, args);

        timeout = setTimeout(delayed, threshold || 100);
    };
}
