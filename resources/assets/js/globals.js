"use strict";

/**
 * Globally exposed namespacing function.
 * @param {string} namespace
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
 * @param {array} parent prototypes
 * @param {hash} children
 */
function extend(parents, child) {
    for (var i in parents) {
        for(var k in parents[i].prototype) {
            child[i] = parents[i].prototype[k];
        }
    }

    return child;
}


/**
 * Globally filters out jQuery elements matching selector
 * from the haystack
 * @param {*} selector
 * @param {*} haystack
 */
function filter(selector, haystack) {
    var matches = [],
        i = -1;

    while (++i < haystack.length) {
        if (haystack[i].element && haystack[i].element.is('selector')) {
            matches.push(haystack[i]);
        }
    }

    return matches;
}
