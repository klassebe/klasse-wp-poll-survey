/* kwps admin */ 
// Backbone.Validation v0.7.1
//
// Copyright (c) 2011-2012 Thomas Pedersen
// Distributed under MIT License
//
// Documentation and full license available at:
// http://thedersen.com/projects/backbone-validation
Backbone.Validation = (function(_){
  'use strict';

  // Default options
  // ---------------

  var defaultOptions = {
    forceUpdate: false,
    selector: 'name',
    labelFormatter: 'sentenceCase',
    valid: Function.prototype,
    invalid: Function.prototype
  };


  // Helper functions
  // ----------------

  // Formatting functions used for formatting error messages
  var formatFunctions = {
    // Uses the configured label formatter to format the attribute name
    // to make it more readable for the user
    formatLabel: function(attrName, model) {
      return defaultLabelFormatters[defaultOptions.labelFormatter](attrName, model);
    },

    // Replaces nummeric placeholders like {0} in a string with arguments
    // passed to the function
    format: function() {
      var args = Array.prototype.slice.call(arguments),
          text = args.shift();
      return text.replace(/\{(\d+)\}/g, function(match, number) {
        return typeof args[number] !== 'undefined' ? args[number] : match;
      });
    }
  };

  // Flattens an object
  // eg:
  //
  //     var o = {
  //       address: {
  //         street: 'Street',
  //         zip: 1234
  //       }
  //     };
  //
  // becomes:
  //
  //     var o = {
  //       'address.street': 'Street',
  //       'address.zip': 1234
  //     };
  var flatten = function (obj, into, prefix) {
    into = into || {};
    prefix = prefix || '';

    _.each(obj, function(val, key) {
      if(obj.hasOwnProperty(key)) {
        if (val && typeof val === 'object' && !(val instanceof Date || val instanceof RegExp)) {
          flatten(val, into, prefix + key + '.');
        }
        else {
          into[prefix + key] = val;
        }
      }
    });

    return into;
  };

  // Validation
  // ----------

  var Validation = (function(){

    // Returns an object with undefined properties for all
    // attributes on the model that has defined one or more
    // validation rules.
    var getValidatedAttrs = function(model) {
      return _.reduce(_.keys(model.validation || {}), function(memo, key) {
        memo[key] = void 0;
        return memo;
      }, {});
    };

    // Looks on the model for validations for a specified
    // attribute. Returns an array of any validators defined,
    // or an empty array if none is defined.
    var getValidators = function(model, attr) {
      var attrValidationSet = model.validation ? model.validation[attr] || {} : {};

      // If the validator is a function or a string, wrap it in a function validator
      if (_.isFunction(attrValidationSet) || _.isString(attrValidationSet)) {
        attrValidationSet = {
          fn: attrValidationSet
        };
      }

      // Stick the validator object into an array
      if(!_.isArray(attrValidationSet)) {
        attrValidationSet = [attrValidationSet];
      }

      // Reduces the array of validators into a new array with objects
      // with a validation method to call, the value to validate against
      // and the specified error message, if any
      return _.reduce(attrValidationSet, function(memo, attrValidation) {
        _.each(_.without(_.keys(attrValidation), 'msg'), function(validator) {
          memo.push({
            fn: defaultValidators[validator],
            val: attrValidation[validator],
            msg: attrValidation.msg
          });
        });
        return memo;
      }, []);
    };

    // Validates an attribute against all validators defined
    // for that attribute. If one or more errors are found,
    // the first error message is returned.
    // If the attribute is valid, an empty string is returned.
    var validateAttr = function(model, attr, value, computed) {
      // Reduces the array of validators to an error message by
      // applying all the validators and returning the first error
      // message, if any.
      return _.reduce(getValidators(model, attr), function(memo, validator){
        // Pass the format functions plus the default
        // validators as the context to the validator
        var ctx = _.extend({}, formatFunctions, defaultValidators),
            result = validator.fn.call(ctx, value, attr, validator.val, model, computed);

        if(result === false || memo === false) {
          return false;
        }
        if (result && !memo) {
          return validator.msg || result;
        }
        return memo;
      }, '');
    };

    // Loops through the model's attributes and validates them all.
    // Returns and object containing names of invalid attributes
    // as well as error messages.
    var validateModel = function(model, attrs) {
      var error,
          invalidAttrs = {},
          isValid = true,
          computed = _.clone(attrs),
          flattened = flatten(attrs);

      _.each(flattened, function(val, attr) {
        error = validateAttr(model, attr, val, computed);
        if (error) {
          invalidAttrs[attr] = error;
          isValid = false;
        }
      });

      return {
        invalidAttrs: invalidAttrs,
        isValid: isValid
      };
    };

    // Contains the methods that are mixed in on the model when binding
    var mixin = function(view, options) {
      return {

        // Check whether or not a value passes validation
        // without updating the model
        preValidate: function(attr, value) {
          return validateAttr(this, attr, value, _.extend({}, this.attributes));
        },

        // Check to see if an attribute, an array of attributes or the
        // entire model is valid. Passing true will force a validation
        // of the model.
        isValid: function(option) {
          var flattened = flatten(this.attributes);

          if(_.isString(option)){
            return !validateAttr(this, option, flattened[option], _.extend({}, this.attributes));
          }
          if(_.isArray(option)){
            return _.reduce(option, function(memo, attr) {
              return memo && !validateAttr(this, attr, flattened[attr], _.extend({}, this.attributes));
            }, true, this);
          }
          if(option === true) {
            this.validate();
          }
          return this.validation ? this._isValid : true;
        },

        // This is called by Backbone when it needs to perform validation.
        // You can call it manually without any parameters to validate the
        // entire model.
        validate: function(attrs, setOptions){
          var model = this,
              validateAll = !attrs,
              opt = _.extend({}, options, setOptions),
              validatedAttrs = getValidatedAttrs(model),
              allAttrs = _.extend({}, validatedAttrs, model.attributes, attrs),
              changedAttrs = flatten(attrs || allAttrs),

              result = validateModel(model, allAttrs);

          model._isValid = result.isValid;

          // After validation is performed, loop through all changed attributes
          // and call the valid callbacks so the view is updated.
          _.each(validatedAttrs, function(val, attr){
            var invalid = result.invalidAttrs.hasOwnProperty(attr);
            if(!invalid){
              opt.valid(view, attr, opt.selector);
            }
          });

          // After validation is performed, loop through all changed attributes
          // and call the invalid callback so the view is updated.
          _.each(validatedAttrs, function(val, attr){
            var invalid = result.invalidAttrs.hasOwnProperty(attr),
                changed = changedAttrs.hasOwnProperty(attr);

            if(invalid && (changed || validateAll)){
              opt.invalid(view, attr, result.invalidAttrs[attr], opt.selector);
            }
          });

          // Trigger validated events.
          // Need to defer this so the model is actually updated before
          // the event is triggered.
          _.defer(function() {
            model.trigger('validated', model._isValid, model, result.invalidAttrs);
            model.trigger('validated:' + (model._isValid ? 'valid' : 'invalid'), model, result.invalidAttrs);
          });

          // Return any error messages to Backbone, unless the forceUpdate flag is set.
          // Then we do not return anything and fools Backbone to believe the validation was
          // a success. That way Backbone will update the model regardless.
          if (!opt.forceUpdate && _.intersection(_.keys(result.invalidAttrs), _.keys(changedAttrs)).length > 0) {
            return result.invalidAttrs;
          }
        }
      };
    };

    // Helper to mix in validation on a model
    var bindModel = function(view, model, options) {
      _.extend(model, mixin(view, options));
    };

    // Removes the methods added to a model
    var unbindModel = function(model) {
      delete model.validate;
      delete model.preValidate;
      delete model.isValid;
    };

    // Mix in validation on a model whenever a model is
    // added to a collection
    var collectionAdd = function(model) {
      bindModel(this.view, model, this.options);
    };

    // Remove validation from a model whenever a model is
    // removed from a collection
    var collectionRemove = function(model) {
      unbindModel(model);
    };

    // Returns the public methods on Backbone.Validation
    return {

      // Current version of the library
      version: '0.7.1',

      // Called to configure the default options
      configure: function(options) {
        _.extend(defaultOptions, options);
      },

      // Hooks up validation on a view with a model
      // or collection
      bind: function(view, options) {
        var model = view.model,
            collection = view.collection;

        options = _.extend({}, defaultOptions, defaultCallbacks, options);

        if(typeof model === 'undefined' && typeof collection === 'undefined'){
          throw 'Before you execute the binding your view must have a model or a collection.\n' +
                'See http://thedersen.com/projects/backbone-validation/#using-form-model-validation for more information.';
        }

        if(model) {
          bindModel(view, model, options);
        }
        else if(collection) {
          collection.each(function(model){
            bindModel(view, model, options);
          });
          collection.bind('add', collectionAdd, {view: view, options: options});
          collection.bind('remove', collectionRemove);
        }
      },

      // Removes validation from a view with a model
      // or collection
      unbind: function(view) {
        var model = view.model,
            collection = view.collection;

        if(model) {
          unbindModel(view.model);
        }
        if(collection) {
          collection.each(function(model){
            unbindModel(model);
          });
          collection.unbind('add', collectionAdd);
          collection.unbind('remove', collectionRemove);
        }
      },

      // Used to extend the Backbone.Model.prototype
      // with validation
      mixin: mixin(null, defaultOptions)
    };
  }());


  // Callbacks
  // ---------

  var defaultCallbacks = Validation.callbacks = {

    // Gets called when a previously invalid field in the
    // view becomes valid. Removes any error message.
    // Should be overridden with custom functionality.
    valid: function(view, attr, selector) {
      view.$('[' + selector + '~="' + attr + '"]')
          .removeClass('invalid')
          .removeAttr('data-error');
    },

    // Gets called when a field in the view becomes invalid.
    // Adds a error message.
    // Should be overridden with custom functionality.
    invalid: function(view, attr, error, selector) {
      view.$('[' + selector + '~="' + attr + '"]')
          .addClass('invalid')
          .attr('data-error', error);
    }
  };


  // Patterns
  // --------

  var defaultPatterns = Validation.patterns = {
    // Matches any digit(s) (i.e. 0-9)
    digits: /^\d+$/,

    // Matched any number (e.g. 100.000)
    number: /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/,

    // Matches a valid email address (e.g. mail@example.com)
    email: /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i,

    // Mathes any valid url (e.g. http://www.xample.com)
    url: /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i
  };


  // Error messages
  // --------------

  // Error message for the build in validators.
  // {x} gets swapped out with arguments form the validator.
  var defaultMessages = Validation.messages = {
    required: '{0} is required',
    acceptance: '{0} must be accepted',
    min: '{0} must be greater than or equal to {1}',
    max: '{0} must be less than or equal to {1}',
    range: '{0} must be between {1} and {2}',
    length: '{0} must be {1} characters',
    minLength: '{0} must be at least {1} characters',
    maxLength: '{0} must be at most {1} characters',
    rangeLength: '{0} must be between {1} and {2} characters',
    oneOf: '{0} must be one of: {1}',
    equalTo: '{0} must be the same as {1}',
    pattern: '{0} must be a valid {1}'
  };

  // Label formatters
  // ----------------

  // Label formatters are used to convert the attribute name
  // to a more human friendly label when using the built in
  // error messages.
  // Configure which one to use with a call to
  //
  //     Backbone.Validation.configure({
  //       labelFormatter: 'label'
  //     });
  var defaultLabelFormatters = Validation.labelFormatters = {

    // Returns the attribute name with applying any formatting
    none: function(attrName) {
      return attrName;
    },

    // Converts attributeName or attribute_name to Attribute name
    sentenceCase: function(attrName) {
      return attrName.replace(/(?:^\w|[A-Z]|\b\w)/g, function(match, index) {
        return index === 0 ? match.toUpperCase() : ' ' + match.toLowerCase();
      }).replace('_', ' ');
    },

    // Looks for a label configured on the model and returns it
    //
    //      var Model = Backbone.Model.extend({
    //        validation: {
    //          someAttribute: {
    //            required: true
    //          }
    //        },
    //
    //        labels: {
    //          someAttribute: 'Custom label'
    //        }
    //      });
    label: function(attrName, model) {
      return (model.labels && model.labels[attrName]) || defaultLabelFormatters.sentenceCase(attrName, model);
    }
  };


  // Built in validators
  // -------------------

  var defaultValidators = Validation.validators = (function(){
    // Use native trim when defined
    var trim = String.prototype.trim ?
      function(text) {
        return text === null ? '' : String.prototype.trim.call(text);
      } :
      function(text) {
        var trimLeft = /^\s+/,
            trimRight = /\s+$/;

        return text === null ? '' : text.toString().replace(trimLeft, '').replace(trimRight, '');
      };

    // Determines whether or not a value is a number
    var isNumber = function(value){
      return _.isNumber(value) || (_.isString(value) && value.match(defaultPatterns.number));
    };

    // Determines whether or not not a value is empty
    var hasValue = function(value) {
      return !(_.isNull(value) || _.isUndefined(value) || (_.isString(value) && trim(value) === ''));
    };

    return {
      // Function validator
      // Lets you implement a custom function used for validation
      fn: function(value, attr, fn, model, computed) {
        if(_.isString(fn)){
          fn = model[fn];
        }
        return fn.call(model, value, attr, computed);
      },

      // Required validator
      // Validates if the attribute is required or not
      required: function(value, attr, required, model, computed) {
        var isRequired = _.isFunction(required) ? required.call(model, value, attr, computed) : required;
        if(!isRequired && !hasValue(value)) {
          return false; // overrides all other validators
        }
        if (isRequired && !hasValue(value)) {
          return this.format(defaultMessages.required, this.formatLabel(attr, model));
        }
      },

      // Acceptance validator
      // Validates that something has to be accepted, e.g. terms of use
      // `true` or 'true' are valid
      acceptance: function(value, attr, accept, model) {
        if(value !== 'true' && (!_.isBoolean(value) || value === false)) {
          return this.format(defaultMessages.acceptance, this.formatLabel(attr, model));
        }
      },

      // Min validator
      // Validates that the value has to be a number and equal to or greater than
      // the min value specified
      min: function(value, attr, minValue, model) {
        if (!isNumber(value) || value < minValue) {
          return this.format(defaultMessages.min, this.formatLabel(attr, model), minValue);
        }
      },

      // Max validator
      // Validates that the value has to be a number and equal to or less than
      // the max value specified
      max: function(value, attr, maxValue, model) {
        if (!isNumber(value) || value > maxValue) {
          return this.format(defaultMessages.max, this.formatLabel(attr, model), maxValue);
        }
      },

      // Range validator
      // Validates that the value has to be a number and equal to or between
      // the two numbers specified
      range: function(value, attr, range, model) {
        if(!isNumber(value) || value < range[0] || value > range[1]) {
          return this.format(defaultMessages.range, this.formatLabel(attr, model), range[0], range[1]);
        }
      },

      // Length validator
      // Validates that the value has to be a string with length equal to
      // the length value specified
      length: function(value, attr, length, model) {
        if (!hasValue(value) || trim(value).length !== length) {
          return this.format(defaultMessages.length, this.formatLabel(attr, model), length);
        }
      },

      // Min length validator
      // Validates that the value has to be a string with length equal to or greater than
      // the min length value specified
      minLength: function(value, attr, minLength, model) {
        if (!hasValue(value) || trim(value).length < minLength) {
          return this.format(defaultMessages.minLength, this.formatLabel(attr, model), minLength);
        }
      },

      // Max length validator
      // Validates that the value has to be a string with length equal to or less than
      // the max length value specified
      maxLength: function(value, attr, maxLength, model) {
        if (!hasValue(value) || trim(value).length > maxLength) {
          return this.format(defaultMessages.maxLength, this.formatLabel(attr, model), maxLength);
        }
      },

      // Range length validator
      // Validates that the value has to be a string and equal to or between
      // the two numbers specified
      rangeLength: function(value, attr, range, model) {
        if(!hasValue(value) || trim(value).length < range[0] || trim(value).length > range[1]) {
          return this.format(defaultMessages.rangeLength, this.formatLabel(attr, model), range[0], range[1]);
        }
      },

      // One of validator
      // Validates that the value has to be equal to one of the elements in
      // the specified array. Case sensitive matching
      oneOf: function(value, attr, values, model) {
        if(!_.include(values, value)){
          return this.format(defaultMessages.oneOf, this.formatLabel(attr, model), values.join(', '));
        }
      },

      // Equal to validator
      // Validates that the value has to be equal to the value of the attribute
      // with the name specified
      equalTo: function(value, attr, equalTo, model, computed) {
        if(value !== computed[equalTo]) {
          return this.format(defaultMessages.equalTo, this.formatLabel(attr, model), this.formatLabel(equalTo, model));
        }
      },

      // Pattern validator
      // Validates that the value has to match the pattern specified.
      // Can be a regular expression or the name of one of the built in patterns
      pattern: function(value, attr, pattern, model) {
        if (!hasValue(value) || !value.toString().match(defaultPatterns[pattern] || pattern)) {
          return this.format(defaultMessages.pattern, this.formatLabel(attr, model), pattern);
        }
      }
    };
  }());

  return Validation;
}(_));
/*!

 handlebars v1.3.0

Copyright (C) 2011 by Yehuda Katz

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

@license
*/
/* exported Handlebars */
var Handlebars = (function() {
// handlebars/safe-string.js
var __module3__ = (function() {
  "use strict";
  var __exports__;
  // Build out our basic SafeString type
  function SafeString(string) {
    this.string = string;
  }

  SafeString.prototype.toString = function() {
    return "" + this.string;
  };

  __exports__ = SafeString;
  return __exports__;
})();

// handlebars/utils.js
var __module2__ = (function(__dependency1__) {
  "use strict";
  var __exports__ = {};
  /*jshint -W004 */
  var SafeString = __dependency1__;

  var escape = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#x27;",
    "`": "&#x60;"
  };

  var badChars = /[&<>"'`]/g;
  var possible = /[&<>"'`]/;

  function escapeChar(chr) {
    return escape[chr] || "&amp;";
  }

  function extend(obj, value) {
    for(var key in value) {
      if(Object.prototype.hasOwnProperty.call(value, key)) {
        obj[key] = value[key];
      }
    }
  }

  __exports__.extend = extend;var toString = Object.prototype.toString;
  __exports__.toString = toString;
  // Sourced from lodash
  // https://github.com/bestiejs/lodash/blob/master/LICENSE.txt
  var isFunction = function(value) {
    return typeof value === 'function';
  };
  // fallback for older versions of Chrome and Safari
  if (isFunction(/x/)) {
    isFunction = function(value) {
      return typeof value === 'function' && toString.call(value) === '[object Function]';
    };
  }
  var isFunction;
  __exports__.isFunction = isFunction;
  var isArray = Array.isArray || function(value) {
    return (value && typeof value === 'object') ? toString.call(value) === '[object Array]' : false;
  };
  __exports__.isArray = isArray;

  function escapeExpression(string) {
    // don't escape SafeStrings, since they're already safe
    if (string instanceof SafeString) {
      return string.toString();
    } else if (!string && string !== 0) {
      return "";
    }

    // Force a string conversion as this will be done by the append regardless and
    // the regex test will do this transparently behind the scenes, causing issues if
    // an object's to string has escaped characters in it.
    string = "" + string;

    if(!possible.test(string)) { return string; }
    return string.replace(badChars, escapeChar);
  }

  __exports__.escapeExpression = escapeExpression;function isEmpty(value) {
    if (!value && value !== 0) {
      return true;
    } else if (isArray(value) && value.length === 0) {
      return true;
    } else {
      return false;
    }
  }

  __exports__.isEmpty = isEmpty;
  return __exports__;
})(__module3__);

// handlebars/exception.js
var __module4__ = (function() {
  "use strict";
  var __exports__;

  var errorProps = ['description', 'fileName', 'lineNumber', 'message', 'name', 'number', 'stack'];

  function Exception(message, node) {
    var line;
    if (node && node.firstLine) {
      line = node.firstLine;

      message += ' - ' + line + ':' + node.firstColumn;
    }

    var tmp = Error.prototype.constructor.call(this, message);

    // Unfortunately errors are not enumerable in Chrome (at least), so `for prop in tmp` doesn't work.
    for (var idx = 0; idx < errorProps.length; idx++) {
      this[errorProps[idx]] = tmp[errorProps[idx]];
    }

    if (line) {
      this.lineNumber = line;
      this.column = node.firstColumn;
    }
  }

  Exception.prototype = new Error();

  __exports__ = Exception;
  return __exports__;
})();

// handlebars/base.js
var __module1__ = (function(__dependency1__, __dependency2__) {
  "use strict";
  var __exports__ = {};
  var Utils = __dependency1__;
  var Exception = __dependency2__;

  var VERSION = "1.3.0";
  __exports__.VERSION = VERSION;var COMPILER_REVISION = 4;
  __exports__.COMPILER_REVISION = COMPILER_REVISION;
  var REVISION_CHANGES = {
    1: '<= 1.0.rc.2', // 1.0.rc.2 is actually rev2 but doesn't report it
    2: '== 1.0.0-rc.3',
    3: '== 1.0.0-rc.4',
    4: '>= 1.0.0'
  };
  __exports__.REVISION_CHANGES = REVISION_CHANGES;
  var isArray = Utils.isArray,
      isFunction = Utils.isFunction,
      toString = Utils.toString,
      objectType = '[object Object]';

  function HandlebarsEnvironment(helpers, partials) {
    this.helpers = helpers || {};
    this.partials = partials || {};

    registerDefaultHelpers(this);
  }

  __exports__.HandlebarsEnvironment = HandlebarsEnvironment;HandlebarsEnvironment.prototype = {
    constructor: HandlebarsEnvironment,

    logger: logger,
    log: log,

    registerHelper: function(name, fn, inverse) {
      if (toString.call(name) === objectType) {
        if (inverse || fn) { throw new Exception('Arg not supported with multiple helpers'); }
        Utils.extend(this.helpers, name);
      } else {
        if (inverse) { fn.not = inverse; }
        this.helpers[name] = fn;
      }
    },

    registerPartial: function(name, str) {
      if (toString.call(name) === objectType) {
        Utils.extend(this.partials,  name);
      } else {
        this.partials[name] = str;
      }
    }
  };

  function registerDefaultHelpers(instance) {
    instance.registerHelper('helperMissing', function(arg) {
      if(arguments.length === 2) {
        return undefined;
      } else {
        throw new Exception("Missing helper: '" + arg + "'");
      }
    });

    instance.registerHelper('blockHelperMissing', function(context, options) {
      var inverse = options.inverse || function() {}, fn = options.fn;

      if (isFunction(context)) { context = context.call(this); }

      if(context === true) {
        return fn(this);
      } else if(context === false || context == null) {
        return inverse(this);
      } else if (isArray(context)) {
        if(context.length > 0) {
          return instance.helpers.each(context, options);
        } else {
          return inverse(this);
        }
      } else {
        return fn(context);
      }
    });

    instance.registerHelper('each', function(context, options) {
      var fn = options.fn, inverse = options.inverse;
      var i = 0, ret = "", data;

      if (isFunction(context)) { context = context.call(this); }

      if (options.data) {
        data = createFrame(options.data);
      }

      if(context && typeof context === 'object') {
        if (isArray(context)) {
          for(var j = context.length; i<j; i++) {
            if (data) {
              data.index = i;
              data.first = (i === 0);
              data.last  = (i === (context.length-1));
            }
            ret = ret + fn(context[i], { data: data });
          }
        } else {
          for(var key in context) {
            if(context.hasOwnProperty(key)) {
              if(data) { 
                data.key = key; 
                data.index = i;
                data.first = (i === 0);
              }
              ret = ret + fn(context[key], {data: data});
              i++;
            }
          }
        }
      }

      if(i === 0){
        ret = inverse(this);
      }

      return ret;
    });

    instance.registerHelper('if', function(conditional, options) {
      if (isFunction(conditional)) { conditional = conditional.call(this); }

      // Default behavior is to render the positive path if the value is truthy and not empty.
      // The `includeZero` option may be set to treat the condtional as purely not empty based on the
      // behavior of isEmpty. Effectively this determines if 0 is handled by the positive path or negative.
      if ((!options.hash.includeZero && !conditional) || Utils.isEmpty(conditional)) {
        return options.inverse(this);
      } else {
        return options.fn(this);
      }
    });

    instance.registerHelper('unless', function(conditional, options) {
      return instance.helpers['if'].call(this, conditional, {fn: options.inverse, inverse: options.fn, hash: options.hash});
    });

    instance.registerHelper('with', function(context, options) {
      if (isFunction(context)) { context = context.call(this); }

      if (!Utils.isEmpty(context)) return options.fn(context);
    });

    instance.registerHelper('log', function(context, options) {
      var level = options.data && options.data.level != null ? parseInt(options.data.level, 10) : 1;
      instance.log(level, context);
    });
  }

  var logger = {
    methodMap: { 0: 'debug', 1: 'info', 2: 'warn', 3: 'error' },

    // State enum
    DEBUG: 0,
    INFO: 1,
    WARN: 2,
    ERROR: 3,
    level: 3,

    // can be overridden in the host environment
    log: function(level, obj) {
      if (logger.level <= level) {
        var method = logger.methodMap[level];
        if (typeof console !== 'undefined' && console[method]) {
          console[method].call(console, obj);
        }
      }
    }
  };
  __exports__.logger = logger;
  function log(level, obj) { logger.log(level, obj); }

  __exports__.log = log;var createFrame = function(object) {
    var obj = {};
    Utils.extend(obj, object);
    return obj;
  };
  __exports__.createFrame = createFrame;
  return __exports__;
})(__module2__, __module4__);

// handlebars/runtime.js
var __module5__ = (function(__dependency1__, __dependency2__, __dependency3__) {
  "use strict";
  var __exports__ = {};
  var Utils = __dependency1__;
  var Exception = __dependency2__;
  var COMPILER_REVISION = __dependency3__.COMPILER_REVISION;
  var REVISION_CHANGES = __dependency3__.REVISION_CHANGES;

  function checkRevision(compilerInfo) {
    var compilerRevision = compilerInfo && compilerInfo[0] || 1,
        currentRevision = COMPILER_REVISION;

    if (compilerRevision !== currentRevision) {
      if (compilerRevision < currentRevision) {
        var runtimeVersions = REVISION_CHANGES[currentRevision],
            compilerVersions = REVISION_CHANGES[compilerRevision];
        throw new Exception("Template was precompiled with an older version of Handlebars than the current runtime. "+
              "Please update your precompiler to a newer version ("+runtimeVersions+") or downgrade your runtime to an older version ("+compilerVersions+").");
      } else {
        // Use the embedded version info since the runtime doesn't know about this revision yet
        throw new Exception("Template was precompiled with a newer version of Handlebars than the current runtime. "+
              "Please update your runtime to a newer version ("+compilerInfo[1]+").");
      }
    }
  }

  __exports__.checkRevision = checkRevision;// TODO: Remove this line and break up compilePartial

  function template(templateSpec, env) {
    if (!env) {
      throw new Exception("No environment passed to template");
    }

    // Note: Using env.VM references rather than local var references throughout this section to allow
    // for external users to override these as psuedo-supported APIs.
    var invokePartialWrapper = function(partial, name, context, helpers, partials, data) {
      var result = env.VM.invokePartial.apply(this, arguments);
      if (result != null) { return result; }

      if (env.compile) {
        var options = { helpers: helpers, partials: partials, data: data };
        partials[name] = env.compile(partial, { data: data !== undefined }, env);
        return partials[name](context, options);
      } else {
        throw new Exception("The partial " + name + " could not be compiled when running in runtime-only mode");
      }
    };

    // Just add water
    var container = {
      escapeExpression: Utils.escapeExpression,
      invokePartial: invokePartialWrapper,
      programs: [],
      program: function(i, fn, data) {
        var programWrapper = this.programs[i];
        if(data) {
          programWrapper = program(i, fn, data);
        } else if (!programWrapper) {
          programWrapper = this.programs[i] = program(i, fn);
        }
        return programWrapper;
      },
      merge: function(param, common) {
        var ret = param || common;

        if (param && common && (param !== common)) {
          ret = {};
          Utils.extend(ret, common);
          Utils.extend(ret, param);
        }
        return ret;
      },
      programWithDepth: env.VM.programWithDepth,
      noop: env.VM.noop,
      compilerInfo: null
    };

    return function(context, options) {
      options = options || {};
      var namespace = options.partial ? options : env,
          helpers,
          partials;

      if (!options.partial) {
        helpers = options.helpers;
        partials = options.partials;
      }
      var result = templateSpec.call(
            container,
            namespace, context,
            helpers,
            partials,
            options.data);

      if (!options.partial) {
        env.VM.checkRevision(container.compilerInfo);
      }

      return result;
    };
  }

  __exports__.template = template;function programWithDepth(i, fn, data /*, $depth */) {
    var args = Array.prototype.slice.call(arguments, 3);

    var prog = function(context, options) {
      options = options || {};

      return fn.apply(this, [context, options.data || data].concat(args));
    };
    prog.program = i;
    prog.depth = args.length;
    return prog;
  }

  __exports__.programWithDepth = programWithDepth;function program(i, fn, data) {
    var prog = function(context, options) {
      options = options || {};

      return fn(context, options.data || data);
    };
    prog.program = i;
    prog.depth = 0;
    return prog;
  }

  __exports__.program = program;function invokePartial(partial, name, context, helpers, partials, data) {
    var options = { partial: true, helpers: helpers, partials: partials, data: data };

    if(partial === undefined) {
      throw new Exception("The partial " + name + " could not be found");
    } else if(partial instanceof Function) {
      return partial(context, options);
    }
  }

  __exports__.invokePartial = invokePartial;function noop() { return ""; }

  __exports__.noop = noop;
  return __exports__;
})(__module2__, __module4__, __module1__);

// handlebars.runtime.js
var __module0__ = (function(__dependency1__, __dependency2__, __dependency3__, __dependency4__, __dependency5__) {
  "use strict";
  var __exports__;
  /*globals Handlebars: true */
  var base = __dependency1__;

  // Each of these augment the Handlebars object. No need to setup here.
  // (This is done to easily share code between commonjs and browse envs)
  var SafeString = __dependency2__;
  var Exception = __dependency3__;
  var Utils = __dependency4__;
  var runtime = __dependency5__;

  // For compatibility and usage outside of module systems, make the Handlebars object a namespace
  var create = function() {
    var hb = new base.HandlebarsEnvironment();

    Utils.extend(hb, base);
    hb.SafeString = SafeString;
    hb.Exception = Exception;
    hb.Utils = Utils;

    hb.VM = runtime;
    hb.template = function(spec) {
      return runtime.template(spec, hb);
    };

    return hb;
  };

  var Handlebars = create();
  Handlebars.create = create;

  __exports__ = Handlebars;
  return __exports__;
})(__module1__, __module3__, __module4__, __module2__, __module5__);

  return __module0__;
})();

this["kwps_admin_templates"] = this["kwps_admin_templates"] || {};

this["kwps_admin_templates"]["add_result"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, self=this;

function program1(depth0,data) {
  
  
  return "\n	<div id=\"bar-chart\" class=\"media-item left\">\n		<label>\n			<h4>Bar Chart</h4>\n			<input type=\"radio\" name=\"results\" value=\"bar_chart\">\n			<img class=\"thumbnail\" src=\"images/bar_chart.png\" alt=\"bar-chart-per-question\" height=\"128\" width=\"128\">\n		</label>\n	</div>\n";
  }

function program3(depth0,data) {
  
  
  return "\n	<div id=\"pie-chart\" class=\"media-item left\">\n		<label>\n			<h4>Pie Chart</h4>\n			<input type=\"radio\" name=\"results\" value=\"pie_chart\">\n			<img class=\"thumbnail\" src=\"images/pie_chart.png\" alt=\"pie-chart-per-question\" height=\"128\" width=\"160\">\n		</label>\n	</div>\n";
  }

function program5(depth0,data) {
  
  
  return "\n	<div id=\"stacked-bar-chart\" class=\"media-item left\">\n		<label>\n			<h4>Stacked Bar Chart</h4>\n			<input type=\"radio\" name=\"results\" value=\"stacked_bar_chart\">\n			<img class=\"thumbnail\" src=\"images/stacked_bar_chart.png\" alt=\"stacked-bar-chart-per-question\" height=\"128\" width=\"128\">\n		</label>\n	</div>\n";
  }

function program7(depth0,data) {
  
  
  return "\n	<div id=\"quiz-respons\" class=\"media-item left\">\n		<label>\n			<h4>Quiz Respons</h4>\n			<input type=\"radio\" name=\"results\" value=\"quiz_respons\">\n			<img class=\"thumbnail\" src=\"images/stacked_bar_chart.png\" alt=\"quiz-respons\" height=\"128\" width=\"128\">\n		</label>\n	</div>\n";
  }

function program9(depth0,data) {
  
  
  return "\n	<div id=\"result-profile\" class=\"media-item left\">\n		<label>\n			<h4>Result Profile</h4>\n			<input type=\"radio\" name=\"results\" value=\"result_profile\">\n			<img class=\"thumbnail\" src=\"images/stacked_bar_chart.png\" alt=\"result-profile\" height=\"128\" width=\"128\">\n		</label>\n	</div>\n";
  }

  stack1 = helpers['if'].call(depth0, (depth0 && depth0.allowedBar), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.allowedPie), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.allowedStackedBar), {hash:{},inverse:self.noop,fn:self.program(5, program5, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.allowedQuizRespons), {hash:{},inverse:self.noop,fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.typePersTest), {hash:{},inverse:self.noop,fn:self.program(9, program9, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n";
  return buffer;
  });

this["kwps_admin_templates"]["choose_testmodus"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, options, functionType="function", escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n						<label for=\"kwpsTestModi_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" >\n							<input type=\"radio\" value=\"";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" name=\"post_parent\" id=\"kwpsTestModi_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n							<span>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.post_title), options) : helperMissing.call(depth0, "t", (depth0 && depth0.post_title), options)))
    + "</span>\n							<p>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.post_content), options) : helperMissing.call(depth0, "t", (depth0 && depth0.post_content), options)))
    + "</p>\n						</label><br>\n					";
  return buffer;
  }

  buffer += "<h2>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Create new test", options) : helperMissing.call(depth0, "t", "Create new test", options)))
    + "</h2>\n<form id=\"create-new-test\">\n<table class=\"form-table\">\n	<tbody>\n		<tr>\n			<th>\n				<label for=\"post_title\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Test Title", options) : helperMissing.call(depth0, "t", "Test Title", options)))
    + "</label>\n			</th>\n			<td>\n				<input name=\"post_title\" id=\"post_title\" class=\"regular-text\"><span class=\"help-block hidden\"></span>\n				<p class=\"description\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "This will be the title of your test.", options) : helperMissing.call(depth0, "t", "This will be the title of your test.", options)))
    + "</p>\n			</td>\n		</tr>\n		<tr>\n			<th>\n				<label for=\"kwpsTestModi\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Test modus", options) : helperMissing.call(depth0, "t", "Test modus", options)))
    + "</label>\n			</th>\n			<td>\n				<fieldset>\n					<legend class=\"screen-reader-text\">\n						<span>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Test modi", options) : helperMissing.call(depth0, "t", "Test modi", options)))
    + "</span>\n					</legend>\n					";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.kwpsTestModi), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n				</fieldset>\n				<span class=\"help-block hidden\"></span>\n				<p class=\"description\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Select the type of test you want to create.", options) : helperMissing.call(depth0, "t", "Select the type of test you want to create.", options)))
    + "</p>\n			</td>\n		</tr>\n	</tbody>\n</table>\n<p class=\"submit\">\n	<button type=\"submit\" class=\"button button-primary\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Create", options) : helperMissing.call(depth0, "t", "Create", options)))
    + "</button>\n</p>\n</form>\n";
  return buffer;
  });

this["kwps_admin_templates"]["control_panel"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); partials = this.merge(partials, Handlebars.partials); data = data || {};
  var buffer = "", stack1, helper, options, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, functionType="function", self=this;


  buffer += "<h2>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Control panel", options) : helperMissing.call(depth0, "t", "Control panel", options)))
    + ": "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, ((stack1 = (depth0 && depth0.testmodus)),stack1 == null || stack1 === false ? stack1 : stack1.post_title), options) : helperMissing.call(depth0, "t", ((stack1 = (depth0 && depth0.testmodus)),stack1 == null || stack1 === false ? stack1 : stack1.post_title), options)))
    + "</h2>\n<div class=\"test-input\">\n    <input type=\"text\" name=\"post_title\" id=\"post_title\" value=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.collection)),stack1 == null || stack1 === false ? stack1 : stack1.post_title)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" placeholder=\""
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "New Test", options) : helperMissing.call(depth0, "t", "New Test", options)))
    + "\"/>\n</div>\n\n<div id=\"tabs\">\n    <ul>\n        <li><a href=\"#tabs-add\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Builder", options) : helperMissing.call(depth0, "t", "Builder", options)))
    + "</a></li>\n        <li><a href=\"#tabs-settings\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Settings", options) : helperMissing.call(depth0, "t", "Settings", options)))
    + "</a></li>\n    </ul>\n    <div id=\"tabs-add\">\n        ";
  stack1 = self.invokePartial(partials.controlTest, 'controlTest', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </div>\n    <div id=\"tabs-settings\">\n        ";
  stack1 = self.invokePartial(partials.settings, 'settings', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </div>\n</div>";
  return buffer;
  });

this["kwps_admin_templates"]["control_test"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); partials = this.merge(partials, Handlebars.partials); data = data || {};
  var buffer = "", stack1, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.mainTitle), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.mainRow), {hash:{},inverse:self.noop,fn:self.program(4, program4, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.questionTitle), {hash:{},inverse:self.noop,fn:self.program(6, program6, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.question), {hash:{},inverse:self.noop,fn:self.program(8, program8, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.answerTitle), {hash:{},inverse:self.noop,fn:self.program(10, program10, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.answer), {hash:{},inverse:self.noop,fn:self.program(12, program12, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    ";
  stack1 = self.invokePartial(partials.controlTestMainTitleRow, 'controlTestMainTitleRow', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  return buffer;
  }

function program4(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    ";
  stack1 = self.invokePartial(partials.controlTestMainRow, 'controlTestMainRow', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  return buffer;
  }

function program6(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    ";
  stack1 = self.invokePartial(partials.controlTestQuestionTitleRow, 'controlTestQuestionTitleRow', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  return buffer;
  }

function program8(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    ";
  stack1 = self.invokePartial(partials.controlTestQuestionRow, 'controlTestQuestionRow', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  return buffer;
  }

function program10(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    ";
  stack1 = self.invokePartial(partials.controlTestAnswerTitleRow, 'controlTestAnswerTitleRow', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  return buffer;
  }

function program12(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    ";
  stack1 = self.invokePartial(partials.controlTestAnswerRow, 'controlTestAnswerRow', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  return buffer;
  }

  buffer += "<div>\n    <div>\n        <table id=\"matrix\" class=\"wp-list-table widefat fixed\" style=\"table-layout:fixed;\">\n            \n\n            ";
  stack1 = self.invokePartial(partials.controlTestTopRow, 'controlTestTopRow', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.table), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  stack1 = self.invokePartial(partials.controlTestBottomRow, 'controlTestBottomRow', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </table>\n    </div>\n</div>";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_answer_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, self=this, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, functionType="function";

function program1(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            <div class=\"move\">\n                <span class=\"move-action up ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.first), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\"></span>\n                <span class=\"move-action down ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.last), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\"></span>\n            </div>\n        ";
  return buffer;
  }
function program2(depth0,data) {
  
  
  return "disabled";
  }

function program4(depth0,data) {
  
  
  return "\n            <a class=\"delete-answer-option\">\n                <span class=\"del\">\n                    <span data-code=\"f182\" class=\"dashicons dashicons-trash\"></span>\n                </span>\n            </a>\n            ";
  }

function program6(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n            <div class=\"label label-info\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Value", options) : helperMissing.call(depth0, "t", "Value", options)))
    + ": ";
  if (helper = helpers.value) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.value); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n        ";
  return buffer;
  }

function program8(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n    <td id=\"_kwps_answer_option_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"post-title page-title column-title\">\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.editable), {hash:{},inverse:self.program(11, program11, data),fn:self.program(9, program9, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n    </td>\n    ";
  return buffer;
  }
function program9(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n            <strong>\n                <a class=\"row-title\" href=\"#edit/";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"\n                   title=\""
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Edit", options) : helperMissing.call(depth0, "t", "Edit", options)))
    + "\">"
    + escapeExpression((helper = helpers.subStringStripper || (depth0 && depth0.subStringStripper),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.post_content), 120, options) : helperMissing.call(depth0, "subStringStripper", (depth0 && depth0.post_content), 120, options)))
    + "</a>\n            </strong>\n\n            <div class=\"actions\" style=\"display: none\"><a href=\"#edit/";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Edit", options) : helperMissing.call(depth0, "t", "Edit", options)))
    + "</a></div>\n        ";
  return buffer;
  }

function program11(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n            <strong>\n                "
    + escapeExpression((helper = helpers.subStringStripper || (depth0 && depth0.subStringStripper),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.post_content), 120, options) : helperMissing.call(depth0, "subStringStripper", (depth0 && depth0.post_content), 120, options)))
    + "\n            </strong>\n        ";
  return buffer;
  }

  buffer += "<tr class=\"post-1 type-post status-publish format-standard hentry category-uncategorized iedit author-self level-0\" style=\"background:#FFF8E7\" data-post-type=\"";
  if (helper = helpers.postType) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.postType); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-sort-order=\"";
  if (helper = helpers.sortOrder) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.sortOrder); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n    <td class=\"column-action\" style=\"border: none; padding: 0px;height: 60px;\">\n        <div style=\"width:30px; height: 60px; float:left;\">\n            <svg x=\"0px\" y=\"0px\" viewBox=\"0 0 30 60\">\n                <rect x=\"0\" y=\"0\" fill=\"#FFFFFF\" width=\"20\" height=\"60\"></rect>\n                <rect x=\"20\" y=\"0\" fill=\"#FFBA00\" width=\"10\" height=\"60\"></rect>\n            </svg>\n        </div>\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.sorterArrows), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        <div class=\"action\" style=\"padding: 10px; float:left;\">\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.deletable), {hash:{},inverse:self.noop,fn:self.program(4, program4, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </div>\n        <div class=\"label\">";
  if (helper = helpers.number) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.number); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.showValue), {hash:{},inverse:self.noop,fn:self.program(6, program6, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </td>\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.versions), {hash:{},inverse:self.noop,fn:self.program(8, program8, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    <td>\n        &nbsp;\n    </td>\n</tr>\n";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_answer_title_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, options, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, functionType="function", self=this;

function program1(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n            <button class=\"button add\">\n                <span class=\"dashicons dashicons-plus\"></span>\n                "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.addText), options) : helperMissing.call(depth0, "t", (depth0 && depth0.addText), options)))
    + "\n            </button>\n            ";
  return buffer;
  }

function program3(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += " / ";
  if (helper = helpers.maxAmount) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.maxAmount); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  return buffer;
  }

  buffer += "<tr class=\"post-1 type-post title status-publish format-standard hentry category-uncategorized iedit author-self level-0\" style=\"background:#FFB500\" data-post-type=\"";
  if (helper = helpers.postType) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.postType); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-sort-order=\"";
  if (helper = helpers.questionSortOrder) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.questionSortOrder); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n    <th class=\"no-delete row-title\" colspan=\"";
  if (helper = helpers.colSpan) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.colSpan); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" style=\" border-top: #FFBA00; padding: 0px;height: 42px;\">\n        <div style=\"width:20px; height: 42px; float:left;\">\n            <svg x=\"0px\" y=\"0px\" viewBox=\"0 0 20 42\">\n                <rect x=\"0\" y=\"0\" fill=\"#FFFFFF\" width=\"10\" height=\"42\"></rect>\n                <rect x=\"10\" y=\"0\" fill=\"#1E8CBE\" width=\"10\" height=\"42\"></rect>\n            </svg>\n        </div>\n        <div style=\"padding: 10px; float:left;\">\n            "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.title), options) : helperMissing.call(depth0, "t", (depth0 && depth0.title), options)))
    + "\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.add), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </div> \n    </th>\n    <td style=\"height: 20px;\"><span class=\"label label-inverse\">";
  if (helper = helpers.amount) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.amount); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1);
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.maxAmount), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</span></td>\n    \n</tr>";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_bottom_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, functionType="function", self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n        <th style=\"border-top: 1px solid #D6D6D6;\" class=\"column-title bottom\" data-post-id=\"";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n            <div class=\"column-tab\">\n                "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Shortcode", options) : helperMissing.call(depth0, "t", "Shortcode", options)))
    + ": <code>[kwps_version id=";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "]</code>\n                    <div>\n                        <h3>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Results", options) : helperMissing.call(depth0, "t", "Results", options)))
    + ": </h3>\n                        <ul>\n                            <li>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "View count", options) : helperMissing.call(depth0, "t", "View count", options)))
    + ": ";
  if (helper = helpers._kwps_view_count) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0._kwps_view_count); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</li>\n                            <li>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Conversion Rate", options) : helperMissing.call(depth0, "t", "Conversion Rate", options)))
    + ": ";
  if (helper = helpers.conversion_rate_percentage) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.conversion_rate_percentage); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "%</li>\n                            <li>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Total Participants", options) : helperMissing.call(depth0, "t", "Total Participants", options)))
    + ": ";
  if (helper = helpers.total_participants) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.total_participants); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</li>\n                        </ul>\n                    </div>\n                    ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.validation)),stack1 == null || stack1 === false ? stack1 : stack1.success), {hash:{},inverse:self.program(4, program4, data),fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers.unless.call(depth0, (depth0 && depth0.isLive), {hash:{},inverse:self.noop,fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            </div>\n        </th>\n    ";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n                    <div>\n                        "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Ready to Publish!", options) : helperMissing.call(depth0, "t", "Ready to Publish!", options)))
    + "\n                    </div>\n                    ";
  return buffer;
  }

function program4(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n                    <div>\n                        <h3>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Errors", options) : helperMissing.call(depth0, "t", "Errors", options)))
    + ": </h3>\n                        <ul>\n                            ";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.validation)),stack1 == null || stack1 === false ? stack1 : stack1.data), {hash:{},inverse:self.noop,fn:self.program(5, program5, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                        </ul>\n                    </div>\n                    ";
  return buffer;
  }
function program5(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n                                <li>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.field), options) : helperMissing.call(depth0, "t", (depth0 && depth0.field), options)))
    + ": ";
  if (helper = helpers.message) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.message); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</li>\n                            ";
  return buffer;
  }

function program7(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n                    <span><a href=\"#\" class=\"clear-entries\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Clear entries", options) : helperMissing.call(depth0, "t", "Clear entries", options)))
    + "</a></span>\n                ";
  return buffer;
  }

  buffer += "<tr>\n    <th style=\"border-top: 1px solid #D6D6D6;\">&nbsp;</th>\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.versions), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    <td style=\"border-top: 1px solid #D6D6D6;\">&nbsp;</td>\n</tr>";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_main_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, self=this, functionType="function", escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        <div class=\"move\">\n            <span class=\"move-action up ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.first), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\"></span>\n            <span class=\"move-action down ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.last), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\"></span>\n        </div>\n        ";
  return buffer;
  }
function program2(depth0,data) {
  
  
  return "disabled";
  }

function program4(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.hasOpened), {hash:{},inverse:self.program(7, program7, data),fn:self.program(5, program5, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  return buffer;
  }
function program5(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <span data-code=\"f140\" class=\"dashicons dashicons-arrow-down toggle-details\" data-type=\"question\" data-question-row=\""
    + escapeExpression(((stack1 = (data == null || data === false ? data : data.index)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\"></span>\n                ";
  return buffer;
  }

function program7(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <span data-code=\"f140\" class=\"dashicons dashicons-arrow-right toggle-details\" data-type=\"question\" data-question-row=\""
    + escapeExpression(((stack1 = (data == null || data === false ? data : data.index)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\"></span>\n                ";
  return buffer;
  }

function program9(depth0,data) {
  
  
  return "\n            <a class=\"delete-intro\">\n                <span class=\"del\">\n                    <span data-code=\"f182\" class=\"dashicons dashicons-trash\"></span>\n                </span>\n            </a>\n            ";
  }

function program11(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n            <div class=\"label\">";
  if (helper = helpers.number) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.number); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n        ";
  return buffer;
  }

function program13(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.max), {hash:{},inverse:self.noop,fn:self.program(14, program14, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  return buffer;
  }
function program14(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n                <div class=\"label label-info\">\n                    Result: ";
  if (helper = helpers.min) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.min); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + " - ";
  if (helper = helpers.max) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.max); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\n                </div>\n            ";
  return buffer;
  }

function program16(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n        <td id=\"_kwps_intro_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"post-title page-title column-title\">\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.editable), {hash:{},inverse:self.program(19, program19, data),fn:self.program(17, program17, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </td>\n    ";
  return buffer;
  }
function program17(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n            <strong>\n                <a class=\"row-title\" href=\"#edit/";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" title=\""
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Edit", options) : helperMissing.call(depth0, "t", "Edit", options)))
    + "\">\n                   "
    + escapeExpression((helper = helpers.subStringStripper || (depth0 && depth0.subStringStripper),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.post_content), 120, options) : helperMissing.call(depth0, "subStringStripper", (depth0 && depth0.post_content), 120, options)))
    + "\n               </a>\n            </strong>\n\n            <div class=\"actions\" style=\"display: none\"><a href=\"#edit/";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Edit", options) : helperMissing.call(depth0, "t", "Edit", options)))
    + "</a></div>\n            ";
  return buffer;
  }

function program19(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n                <strong>\n                    "
    + escapeExpression((helper = helpers.subStringStripper || (depth0 && depth0.subStringStripper),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.post_content), 120, options) : helperMissing.call(depth0, "subStringStripper", (depth0 && depth0.post_content), 120, options)))
    + "\n                </strong>\n            ";
  return buffer;
  }

  buffer += "\n<tr class=\"type-post status-publish format-standard hentry category-uncategorized iedit author-self level-0\" data-post-type=\"";
  if (helper = helpers.postType) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.postType); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-sort-order=\"";
  if (helper = helpers.sortOrder) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.sortOrder); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n    <td class=\"column-action\" style=\"border: none; padding: 0px;height: 60px;\">\n        <div style=\"width:10px; height: 60px; float:left;\">\n            <svg x=\"0px\" y=\"0px\" viewBox=\"0 0 10 60\">\n                <rect x=\"0\" y=\"0\" fill=\"#555555\" width=\"10\" height=\"60\"></rect>\n            </svg>\n        </div>\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.sorterArrows), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        <div class=\"action\" style=\"padding: 10px; float:left;\">\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.hasMore), {hash:{},inverse:self.noop,fn:self.program(4, program4, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.deletable), {hash:{},inverse:self.noop,fn:self.program(9, program9, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </div>\n\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.number), {hash:{},inverse:self.noop,fn:self.program(11, program11, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.min), {hash:{},inverse:self.noop,fn:self.program(13, program13, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        \n    </td>\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.versions), {hash:{},inverse:self.noop,fn:self.program(16, program16, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    \n    <td>";
  if (helper = helpers.amountOfSiblings) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.amountOfSiblings); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</td>\n</tr>";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_main_title_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, options, self=this, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, functionType="function";

function program1(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        	";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.opened), {hash:{},inverse:self.program(4, program4, data),fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  return buffer;
  }
function program2(depth0,data) {
  
  
  return "\n            	<span class=\"dashicons dashicons-arrow-down toggle-details\"></span>\n        	";
  }

function program4(depth0,data) {
  
  
  return "\n            	<span class=\"dashicons dashicons-arrow-right toggle-details\"></span>\n        	";
  }

function program6(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n        <button class=\"button add\">\n            <span data-code=\"f132\" class=\"dashicons dashicons-plus\"></span>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.addText), options) : helperMissing.call(depth0, "t", (depth0 && depth0.addText), options)))
    + "\n        </button>\n        ";
  return buffer;
  }

function program8(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += " / ";
  if (helper = helpers.maxAmount) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.maxAmount); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  return buffer;
  }

  buffer += "<tr class=\"title\" data-post-type=\"main_";
  if (helper = helpers.postType) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.postType); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n    <th class=\"row-title\" colspan=\"";
  if (helper = helpers.colSpan) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.colSpan); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.hasMore), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.title), options) : helperMissing.call(depth0, "t", (depth0 && depth0.title), options)))
    + "\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.add), {hash:{},inverse:self.noop,fn:self.program(6, program6, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        <span class=\"row-title-description\"><span class=\"dashicons dashicons-info\"></span> "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.description), options) : helperMissing.call(depth0, "t", (depth0 && depth0.description), options)))
    + "</span>\n    </th>\n    <th class=\"row-title\"><span class=\"label\">";
  if (helper = helpers.amount) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.amount); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1);
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.maxAmount), {hash:{},inverse:self.noop,fn:self.program(8, program8, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</span></th>\n</tr>";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_question_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, self=this, functionType="function", escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            <div class=\"move\">\n                <span class=\"move-action up ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.first), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\"></span>\n                <span class=\"move-action down ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.last), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\"></span>\n            </div>\n        ";
  return buffer;
  }
function program2(depth0,data) {
  
  
  return "disabled";
  }

function program4(depth0,data) {
  
  
  return "\n                <span class=\"dashicons dashicons-arrow-down toggle-details\"></span>\n            ";
  }

function program6(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n                <span class=\"dashicons dashicons-arrow-right toggle-details\" data-type=\"question\" data-sort-order=\"";
  if (helper = helpers.sortOrder) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.sortOrder); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"></span>\n            ";
  return buffer;
  }

function program8(depth0,data) {
  
  
  return "\n            <a class=\"delete-question\">\n                <span class=\"del\" data-post-type=\"kwps_qsdlfkj\">\n                    <span data-code=\"f182\" class=\"dashicons dashicons-trash\"></span>\n                </span>\n            </a>\n            ";
  }

function program10(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n    <td id=\"_kwps_question_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"post-title page-title column-title\">\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.editable), {hash:{},inverse:self.program(13, program13, data),fn:self.program(11, program11, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </td>\n    ";
  return buffer;
  }
function program11(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n        <strong>\n            <a class=\"row-title\" href=\"#edit/";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"\n               title=\""
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Edit", options) : helperMissing.call(depth0, "t", "Edit", options)))
    + " “";
  if (helper = helpers.post_content) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.post_content); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "”\">"
    + escapeExpression((helper = helpers.subStringStripper || (depth0 && depth0.subStringStripper),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.post_content), 120, options) : helperMissing.call(depth0, "subStringStripper", (depth0 && depth0.post_content), 120, options)))
    + "</a>\n        </strong>\n\n        <div class=\"actions\" style=\"display: none\"><a href=\"#edit/";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Edit", options) : helperMissing.call(depth0, "t", "Edit", options)))
    + "</a></div>\n        ";
  return buffer;
  }

function program13(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n            <strong>\n                "
    + escapeExpression((helper = helpers.subStringStripper || (depth0 && depth0.subStringStripper),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.post_content), 120, options) : helperMissing.call(depth0, "subStringStripper", (depth0 && depth0.post_content), 120, options)))
    + "\n            </strong>\n        ";
  return buffer;
  }

  buffer += "<tr class=\"post-1 type-post status-publish format-standard hentry category-uncategorized iedit author-self level-0\" style=\"background: #DAEFF8;\" data-post-type=\"kwps_question\" data-sort-order=\"";
  if (helper = helpers.sortOrder) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.sortOrder); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n    <td class=\"column-action\" style=\"border: none; padding: 0px;height: 60px;\">\n        <div style=\"width:20px; height: 60px; float:left;\">\n            <svg x=\"0px\" y=\"0px\" viewBox=\"0 0 20 60\">\n                <rect x=\"0\" y=\"0\" fill=\"#FFFFFF\" width=\"10\" height=\"60\"></rect>\n                <rect x=\"10\" y=\"0\" fill=\"#1E8CBE\" width=\"10\" height=\"60\"></rect>\n            </svg>\n        </div>\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.sorterArrows), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        <div class=\"action\" style=\"padding: 10px; float:left;\">\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.hasOpened), {hash:{},inverse:self.program(6, program6, data),fn:self.program(4, program4, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.deletable), {hash:{},inverse:self.noop,fn:self.program(8, program8, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </div>\n        <div class=\"label\">";
  if (helper = helpers.number) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.number); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</div>\n    </td>\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.versions), {hash:{},inverse:self.noop,fn:self.program(10, program10, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    <td>";
  if (helper = helpers.amountOfSiblings) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.amountOfSiblings); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</td>\n</tr>\n";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_question_title_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, options, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, functionType="function", self=this;

function program1(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n                <button class=\"button add\">\n                    <span data-code=\"f132\" class=\"dashicons dashicons-plus\"></span>\n                    "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.addText), options) : helperMissing.call(depth0, "t", (depth0 && depth0.addText), options)))
    + "\n                </button>\n            ";
  return buffer;
  }

function program3(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += " / ";
  if (helper = helpers.maxAmount) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.maxAmount); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  return buffer;
  }

  buffer += " <tr class=\"post-1 type-post title status-publish format-standard hentry category-uncategorized iedit author-self level-0\" style=\"background: #1E8CBE;\" data-post-type=\"";
  if (helper = helpers.postType) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.postType); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-sort-order=\"";
  if (helper = helpers.questionGroupSortOrder) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.questionGroupSortOrder); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n    <th class=\"no-delete row-title\" colspan=\"";
  if (helper = helpers.colSpan) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.colSpan); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" style=\"border-top: none ;padding: 0px;height: 40px;\">\n        <div style=\"width:10px; height: 42px; float:left;\">\n            <svg x=\"0px\" y=\"0px\" viewBox=\"0 0 10 42\">\n                 <rect x=\"0\" y=\"0\" fill=\"#555555\" width=\"10\" height=\"42\"></rect>\n            </svg>\n        </div>\n        <div style=\"padding: 10px; float:left; color:white;\">\n            "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.title), options) : helperMissing.call(depth0, "t", (depth0 && depth0.title), options)))
    + "\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.add), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </div>  \n    </th>\n    <td style=\"height: 20px;\"><span class=\"label label-inverse\">";
  if (helper = helpers.amount) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.amount); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1);
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.maxAmount), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</span></td>\n</tr>";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_top_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n        <th class=\" column-title top\" data-version-id=\"";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n            <div class=\"column-tab\">\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.editable), {hash:{},inverse:self.program(5, program5, data),fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.isLive), {hash:{},inverse:self.program(9, program9, data),fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            </div>\n        </th>\n    ";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n                <input type=\"text\" class=\"update-version-post-title\" name=\"post_title\" value=\"";
  if (helper = helpers.post_title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.post_title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.deleteVersion), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  return buffer;
  }
function program3(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n                    <span class=\"del\" data-version-id=\"";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n                    <span data-code=\"f182\" class=\"dashicons dashicons-trash\"></span>\n                </span>\n                    ";
  return buffer;
  }

function program5(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n                ";
  if (helper = helpers.post_title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.post_title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\n                ";
  return buffer;
  }

function program7(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n                    <span class=\"label label-warning\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Live!", options) : helperMissing.call(depth0, "t", "Live!", options)))
    + "</span>\n                    <button class=\"make make-locked button button-primary\"> <span class=\"dashicons dashicons-lock\"></span> "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Lock", options) : helperMissing.call(depth0, "t", "Lock", options)))
    + "</button>\n                ";
  return buffer;
  }

function program9(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n                    <button class=\"make make-live button button-primary\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Make live", options) : helperMissing.call(depth0, "t", "Make live", options)))
    + "</button>\n                ";
  return buffer;
  }

  buffer += "<tr data-post-type=\"kwps_version\">\n    <th class=\"no-delete column-action\">&nbsp;</th>\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.versions), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    <td class=\"column-title\" style=\"width:105px;\">\n        <div class=\"column-tab\">\n            <button class=\"add button\" data-post-type=\"kwps_version\">\n                <span data-code=\"f132\" class=\"dashicons dashicons-plus\"></span> Version\n            </button>\n        </div>\n    </td>\n</tr>";
  return buffer;
  });

this["kwps_admin_templates"]["edit"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, options, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, functionType="function", self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n            <div class=\"form-group\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Title", options) : helperMissing.call(depth0, "t", "Title", options)))
    + ": <input type=\"text\" name=\"post_title\" id=\"kwps-question-group-title\" value=\"";
  if (helper = helpers.post_title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.post_title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"><span class=\"help-block hidden\"></span></div>\n        ";
  return buffer;
  }

function program3(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n            <div class=\"form-group\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Value", options) : helperMissing.call(depth0, "t", "Value", options)))
    + ": <input type=\"text\" name=\"_kwps_answer_option_value\" id=\"kwps-answer-option-value\" value=\"";
  if (helper = helpers._kwps_answer_option_value) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0._kwps_answer_option_value); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"><span class=\"help-block hidden\"></span></div>\n        ";
  return buffer;
  }

function program5(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n            <div class=\"form-group\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Min value", options) : helperMissing.call(depth0, "t", "Min value", options)))
    + ": <input type=\"text\" name=\"_kwps_min_value\" id=\"kwps-answer-option-value\" value=\"";
  if (helper = helpers._kwps_min_value) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0._kwps_min_value); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"><span class=\"help-block hidden\"></span></div>\n            <div class=\"form-group\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Max value", options) : helperMissing.call(depth0, "t", "Max value", options)))
    + ": <input type=\"text\" name=\"_kwps_max_value\" id=\"kwps-answer-option-value\" value=\"";
  if (helper = helpers._kwps_max_value) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0._kwps_max_value); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"><span class=\"help-block hidden\"></span></div>\n        ";
  return buffer;
  }

function program7(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n        <button id=\"add-result-button\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Add results", options) : helperMissing.call(depth0, "t", "Add results", options)))
    + "</button>\n        ";
  return buffer;
  }

  buffer += "<h2>";
  if (helper = helpers.label) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.label); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</h2>\n<div>\n    <ul id=\"errors\">\n\n    </ul>\n</div>\n<div>  \n	<form id=\"update-model\">\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.post_title), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.showValue), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.min_max), {hash:{},inverse:self.noop,fn:self.program(5, program5, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n        <br>\n        <button id=\"add-media-button\"><span class=\"add-media-icon\"></span>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Add media", options) : helperMissing.call(depth0, "t", "Add media", options)))
    + "</button>\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.addResults), {hash:{},inverse:self.noop,fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        <div class=\"form-group\">\n            <div id=\"editor-tiny\">\n                <textarea name='post_content' rows=\"20\">";
  if (helper = helpers.post_content) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.post_content); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</textarea>\n            </div>\n            <span class=\"help-block hidden\"></span>\n        </div>\n		<button id=\"update\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Update", options) : helperMissing.call(depth0, "t", "Update", options)))
    + "</button>\n	</form>\n</div>\n";
  return buffer;
  });

this["kwps_admin_templates"]["settings"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, options, functionType="function", escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing, self=this;

function program1(depth0,data,depth1) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n                        <option value=\"";
  if (helper = helpers['function']) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0['function']); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"\n                        "
    + escapeExpression((helper = helpers.selected || (depth0 && depth0.selected),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0['function']), ((stack1 = (depth1 && depth1.collection)),stack1 == null || stack1 === false ? stack1 : stack1._kwps_logged_in_user_limit), options) : helperMissing.call(depth0, "selected", (depth0 && depth0['function']), ((stack1 = (depth1 && depth1.collection)),stack1 == null || stack1 === false ? stack1 : stack1._kwps_logged_in_user_limit), options)))
    + " >"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.label), options) : helperMissing.call(depth0, "t", (depth0 && depth0.label), options)))
    + "</option>\n                        ";
  return buffer;
  }

function program3(depth0,data,depth1) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n                        <option value=\"";
  if (helper = helpers['function']) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0['function']); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"\n                        "
    + escapeExpression((helper = helpers.selected || (depth0 && depth0.selected),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0['function']), ((stack1 = (depth1 && depth1.collection)),stack1 == null || stack1 === false ? stack1 : stack1._kwps_logged_out_user_limit), options) : helperMissing.call(depth0, "selected", (depth0 && depth0['function']), ((stack1 = (depth1 && depth1.collection)),stack1 == null || stack1 === false ? stack1 : stack1._kwps_logged_out_user_limit), options)))
    + " >"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.label), options) : helperMissing.call(depth0, "t", (depth0 && depth0.label), options)))
    + "</option>\n                        ";
  return buffer;
  }

  buffer += "<div class=\"ui-tabs-panel-content-wrapper\">\n    <h2>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Limit entries", options) : helperMissing.call(depth0, "t", "Limit entries", options)))
    + "</h2>\n    <p>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Here you can set, who can fill in a test.", options) : helperMissing.call(depth0, "t", "Here you can set, who can fill in a test.", options)))
    + "</p>\n    <table class=\"form-table\">\n        <tbody>\n            <tr>\n                <th>\n                    <label for=\"kwps_logged_in_user_limit\">\n                        "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Logged in user", options) : helperMissing.call(depth0, "t", "Logged in user", options)))
    + "\n                    </label>\n                </th>\n                <td>\n                    <select id=\"kwps_logged_in_user_limit\" name=\"_kwps_logged_in_user_limit\" class=\"update-main\">\n                        ";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.kwpsUniquenessTypes)),stack1 == null || stack1 === false ? stack1 : stack1.logged_in), {hash:{},inverse:self.noop,fn:self.programWithDepth(1, program1, data, depth0),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                    </select>\n                    <p class=\"description\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "How many times can logged in users fill in the test", options) : helperMissing.call(depth0, "t", "How many times can logged in users fill in the test", options)))
    + "</p>\n                </td>\n            </tr>\n            <tr>\n                <th>\n                    <label for=\"kwps_logged_out_user_limit\">\n                        "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Anonymous user", options) : helperMissing.call(depth0, "t", "Anonymous user", options)))
    + "\n                    </label>\n                </th>\n                <td>\n                    <select id=\"kwps_logged_out_user_limit\" name=\"_kwps_logged_out_user_limit\" class=\"update-main\">\n                        ";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.kwpsUniquenessTypes)),stack1 == null || stack1 === false ? stack1 : stack1.logged_out), {hash:{},inverse:self.noop,fn:self.programWithDepth(3, program3, data, depth0),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                    </select>\n                    <p class=\"description\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "How many times can anonymous users fill in the test", options) : helperMissing.call(depth0, "t", "How many times can anonymous users fill in the test", options)))
    + "</p>\n                </td>\n            </tr>\n        </tbody>\n    </table>\n</div>";
  return buffer;
  });
  Handlebars.registerPartial('controlTest', kwps_admin_templates.control_test);
  Handlebars.registerPartial('controlResults', kwps_admin_templates.control_results);
  Handlebars.registerPartial('manageEntries', kwps_admin_templates.manage_entries);
  Handlebars.registerPartial('settings', kwps_admin_templates.settings);
  Handlebars.registerPartial('controlTestTopRow', kwps_admin_templates.control_test_top_row);
  Handlebars.registerPartial('controlTestMainTitleRow', kwps_admin_templates.control_test_main_title_row);
  Handlebars.registerPartial('controlTestMainRow', kwps_admin_templates.control_test_main_row);
  Handlebars.registerPartial('controlTestQuestionTitleRow', kwps_admin_templates.control_test_question_title_row);
  Handlebars.registerPartial('controlTestQuestionRow', kwps_admin_templates.control_test_question_row);
  Handlebars.registerPartial('controlTestAnswerTitleRow', kwps_admin_templates.control_test_answer_title_row);
  Handlebars.registerPartial('controlTestAnswerRow', kwps_admin_templates.control_test_answer_row);
  Handlebars.registerPartial('controlTestBottomRow', kwps_admin_templates.control_test_bottom_row);

  //translate helper
  Handlebars.registerHelper("t", function(key) {
    if (kwps_translations !== undefined) {
      return (kwps_translations[key] !== undefined)? kwps_translations[key] : key;
    } else {
      return key;
    }
  });

  // debug helper
  // usage: {{debug}} or {{debug someValue}}
  // from: @commondream (http://thinkvitamin.com/code/handlebars-js-part-3-tips-and-tricks/)
  Handlebars.registerHelper("debug", function(optionalValue) {
    console.log("Current Context");
    console.log("====================");
    console.log(this);
    if (optionalValue) {
     console.log("Value");
     console.log("====================");
     console.log(optionalValue);
    }
  });

  Handlebars.registerHelper('subStringStripper', function (html, length){
    var tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    var result = tmp.textContent || tmp.innerText || "";
    var substrResult = result.substring(0, length);
    return  (substrResult.length <= length)? substrResult : substrResult + "...";
  });

  Handlebars.registerHelper('sorter', function (index, obj) {
    var size = 0,
        key,
        result;
    for (key in obj) {
        if(obj.hasOwnProperty(key)) { size++ }
    }
    if(index === 0) {
      result = '<span class="up passive"></span>';
    } else {
      result = '<span class="up"></span>';
    }
    if (index === size-1) {
      result = result + '<span class="down passive"></span>';
    } else {
      result = result + '<span class="down"></span>';
    }
    return result;
  });


  Handlebars.registerHelper('selected', function(option, value){
    if (option === value) {
      return ' selected';
    } else {
      return '';
    }
  });
jQuery(function ($) {
  var i;
  function GetURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (i = 0; i < sURLVariables.length; i++)
    {
      var sParameterName = sURLVariables[i].split('=');
      if (sParameterName[0] === sParam)
      {
          return sParameterName[1];
      }
    }
  }
  function stringToBoolean(string){
    switch(string.toLowerCase()){
      case "true": case "yes": case "1": return true;
      case "false": case "no": case "0": case null: return false;
      default: return Boolean(string);
    }
  }
  $.fn.serializeObject = function(){
    var obj = {};

    $.each( this.serializeArray(), function(i,o){
      var n = o.name,
        v = o.value;

      obj[n] = obj[n] === undefined ? v
        : $.isArray( obj[n] ) ? obj[n].concat( v )
        : [ obj[n], v ];
    });

    return obj;
  };


  _.extend(Backbone.Validation.callbacks, {
    valid: function (view, attr, selector) {
      var $el = view.$('[name=' + attr + ']'),
        $group = $el.closest('.form-group');

      $group.removeClass('has-error');
      $group.find('.help-block').html('').addClass('hidden');
    },
    invalid: function (view, attr, error, selector) {
      console.log('hierin');
      var $el = view.$('[name=' + attr + ']'),
        $group = $el.closest('.form-group');

      $group.addClass('has-error');
      $group.find('.help-block').html(error).removeClass('hidden');
    }
  });

  /* BACKBONE STUFF */
  var app = {};
  app.translations = kwps_translations;
  app.url = 'admin-ajax.php?action=';
  app.openRow = {
    main_kwps_outro: true,
    main_kwps_intro: true,
    main_kwps_intro_result: true,
    main_kwps_question_group: true,
    main_kwps_result_profile: true,
    kwps_question_group: -1,
    kwps_question: -1,
    kwps_result_profile: -1
  };
  app.views = {};

  app.templates = {
    controlPanel: kwps_admin_templates.control_panel,
    edit: kwps_admin_templates.edit,
    result: kwps_admin_templates.control_panel,
    question: kwps_admin_templates.table,
    newKwpsTest: kwps_admin_templates.choose_testmodus,
    resultPage: kwps_admin_templates.add_result
  };
  
  // Routing
   var router = Backbone.Router.extend({
    routes: {
      '' : 'home',
      'edit/:id' : 'edit',
      'result/:id' : 'result',
      'edit/question/:id' : 'editQuestion',
      'new/:type/:parentId' : 'new',
      'new': 'newKwpsTest',
      'delete/:id': 'deletePostType'
    },
    home : function () {
      // console.log("ROUTING TO: home")
      if(!GetURLParameter('id')) {
        window.location = '#new';
        return;
      }

      if(!app.views.index) {
        if (app.kwpsPollsCollection !== undefined) {
          app.views.index = new app.KwpsView({
            collection: app.kwpsPollsCollection
          });
          app.views.index.initialize();
          //app.views.result = new app.KwpsViewResult({model: {data: 'een beetje data'}});
        }
      } else {
        app.views.index.render();
      }
    },
    edit :  function (id) {
      // console.log("ROUTING TO: edit");
      // controleren of er nog een edit view in steekt en alle events unbinden
      if (app.kwpsPollsCollection !== undefined) {
        app.views.edit = new app.KwpsViewEdit({
          model : app.kwpsPollsCollection.get(id)
        });
      }
    },
    editQuestion : function (id) {
      // console.log("ROUTING TO: editQuestion");
      if (app.kwpsPollsCollection !== undefined) {
        app.views.edit = new app.KwpsViewQuestion({
          model : app.kwpsPollsCollection.get(id)
        });
      }
    },
    newKwpsTest : function () {
      // toon keuze options tussen verschillende testmodi
      // Nu vooral één knop met de keuze poll en invulveld
      // indien opties worden de opties getoond en kan men test aanmaken
      //console.log("ROUTING TO: newKwpsTest");

      app.kwpsPollsCollection = new Backbone.Collection(kwpsTests,{
        model: KwpsModel
      });
      app.views.newKwpsTest = new app.KwpsViewNewKwpsTest();
    }
  });

  var KwpsModel = Backbone.Model.extend({
    action: {
      create: 'kwps_save',
      update: 'kwps_update',
      delete: 'kwps_delete'
    },

    sync: function(method, model, options) {
      options = options || {};
      options.url = app.url + model.action[method.toLowerCase()] + (model.attributes.post_type).substring(4);

      return Backbone.sync.apply(this, arguments);
    },
    destroy: function() {
      this.set('post_status', 'trash');
      this.save();
    },
    idAttribute: 'ID',
    defaults: {
      post_author: 0,
      post_date: "",
      post_title: "",
      post_status: "draft",
      post_modified: "",
      post_parent: 0,
      post_type: "",
      _kwps_sort_order: 0
    }
  });

  app.KwpsViewNewKwpsTest = Backbone.View.extend({
    el: '#kwps_test',
    initialize: function (options) {
      this.options = options || {};
      this.model = new KwpsModel();
      _.bindAll(this, 'cleanup');
      this.render();
    },
    cleanup: function() {
      this.undelegateEvents();
      $(this.el).empty();
    },
    events: {
      'submit form#create-new-test': 'createKwpsTest'
    },
    render: function() {
      $(this.el).html(app.templates.newKwpsTest({
        kwpsTestModi: kwpsTestModi
      }));
    },
    createKwpsTest : function (e) {
      e.preventDefault();

      var postData = $(e.target).serializeObject();

      postData.post_type = 'kwps_test_collection';
      postData.post_status = "draft";
      postData._kwps_logged_in_user_limit = 'free';
      postData._kwps_logged_out_user_limit = 'free';

      Backbone.Validation.bind(this, {
        valid: function (view, attr, selector) {
          var $el = view.$('[name=' + attr + ']'),
            $group = $el.closest('.form-group');

          $group.removeClass('has-error');
          $group.find('.help-block').html('').addClass('hidden');
        },
        invalid: function (view, attr, error, selector) {
          var $el = view.$('[name=' + attr + ']'),
            $group = $el.closest('.form-group');
          $group.addClass('has-error');
          $group.find('.help-block').html(error).removeClass('hidden');
        }
      });


      this.model.validation = {
        post_title: {
          required: true,
          msg: kwps_translations['Name is required']
        },
        post_parent: {
          required: true,
          min: 1,
          msg: kwps_translations['Type is required']
        }
      };


      var that = this;
      this.model.set(postData);
      if(this.model.isValid(true)) {
        this.model.save({}, {
          wait: true,
          success: function (model) {
            app.kwpsPollsCollection.add(model);
            for (i = 0; i < 1; i++) {
              that.createVersion(model.get('ID'), i);
            }
            var url = window.location.pathname + window.location.search + "&action=edit&id=" + model.get('ID');
            window.history.pushState(model.get('ID'), "Edit", url);
            app.router.navigate('', {trigger: true});
          }
        });
      }
    },
    createVersion: function (post_parent, index) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_version",
        post_status: "draft",
        post_title : kwps_translations.Version + " " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index
      });
      model.save({},{
        wait: true,
        success: function (model) {
          app.kwpsPollsCollection.add(model);
          for (i = 0; i < 1; i++) {
            that.createIntroResult(model.get('ID'), i);
            that.createQuestionGroup(model.get('ID'), i);
            that.createOutro(model.get('ID'), i);
          }
        }
      });
    },
    createIntroResult: function (post_parent, index) {
      app.kwpsPollsCollection.create({
        post_type: "kwps_intro_result",
        post_status: "draft",
        post_content : kwps_translations['Intro result'],
        post_parent : post_parent,
        _kwps_sort_order : "0"
      }, {
        wait: true,
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
        }
      });
    },
    createQuestionGroup: function (post_parent, index) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_question_group",
        post_status: "draft",
        post_title : kwps_translations["Question Group"] + " " + (index+1),
        post_content : kwps_translations["Question Group"] + " " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index
      });
      model.save({},{
        success: function (model) {
          app.openRow.main_kwps_question_group = true;
          app.openRow.kwps_question_group = 0;
          app.kwpsPollsCollection.add(model);
          for (i = 0; i < 1; i++) {
            that.createQuestion(model.get('ID'), i, model.get('post_type'));
          }
        }
      });
    },
    createQuestion: function (post_parent, index, post_type) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_question",
        post_status: "draft",
        post_content : kwps_translations.Question + " " + (index + 1),
        post_parent : post_parent,
        _kwps_sort_order : index
      });
      model.save({},{
        success: function (model) {
          app.openRow.main_kwps_question = true;
          app.openRow.kwps_question = 0;
          app.kwpsPollsCollection.add(model);
          for (i = 0; i < 2; i++) {
            that.createAnswer(model.get('ID'), i, post_type);
          }
        }
      });
    },
    createAnswer: function (post_parent, index) {
      var model = new KwpsModel({
        post_type: "kwps_answer_option",
        post_status: "draft",
        post_content : kwps_translations["Answer Option"] + " " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index,
        _kwps_answer_option_value : 0
      });
      model.save({},{
        success: function (model) {
          app.kwpsPollsCollection.add(model);
        }
      });
    },
    createOutro: function (post_parent) {
      var that = this;
      app.kwpsPollsCollection.create({
        post_type: "kwps_outro",
        post_status: "draft",
        post_content : kwps_translations.Outro,
        post_parent : post_parent,
        _kwps_sort_order : "0"
      });
    }
  });

  app.KwpsView = Backbone.View.extend({
    el: '#kwps_test',
    initialize: function () {
      //_.bindAll(this, 'cleanup');
      this.validateVersion();
      this.render();
      this.listenTo(this.collection, 'add remove', this.render);
      this.listenTo(this.collection, 'sync', this.validateVersion);
    },
    events: {
      'click .delete-version': 'deleteVersion',
      'mouseenter td': 'showActions',
      'mouseleave td': 'hideActions',
      'mouseenter th': 'showActions',
      'mouseleave th': 'hideActions',
      'click .toggle-details': 'toggleDetails',
      'click button.add': 'createNew',
      'click span.del': 'deletePostType',
      'change #post_title': 'updateTestCollection',
      'change .update-main': 'updateTestCollection',
      'change .update-version-post-title': 'updateVersionPostTitle',
      'click .move-action:not(.disabled)': 'moveItem',
      'click .make-live': 'makeLive',
      'click .clear-entries': 'clearEntries'
    },
    cleanup: function() {
      this.undelegateEvents();
      $(this.el).empty();
    },
    render: function () {
      var data = this.prepareData();
      $(this.el).html(app.templates.controlPanel(data));
      $('#tabs').tabs();
    },
    prepareData: function() {
      var testCollection = this.collection.findWhere({post_type: "kwps_test_collection"});
      var testmodus = this.collection.findWhere({ID: testCollection.get('post_parent')});
      var y;
      //Get versions
      var versions = _.sortBy(_.invoke(this.collection.where({post_type: "kwps_version"}), 'toJSON'),'_kwps_sort_order');

      versions.forEach(function(version) {
        version.isLive = (version.post_status !== "draft");
        version.editable = !version.isLive;
        if(version.isLive && version.conversion_rate_percentage) {
          version.conversion_rate_percentage = version.conversion_rate.toPrecision(4) * 100;
        } else {
          version.conversion_rate_percentage = 0;
        }
      });

      //Get intro's
      var intros = [];
      for (i = 0; i < versions.length; i++) {
        y = this.collection.findWhere({post_type: "kwps_intro", post_parent : versions[i].ID});
        if (y === undefined) {
          break;
        }
        y.set('editable', !versions[i].isLive);
        intros[i] = y.toJSON();
      }


      //Get intro results
      var introResults = [];
      for (i = 0; i < versions.length; i++) {
        var introResult = this.collection.findWhere({post_type: "kwps_intro_result", post_parent : versions[i].ID});
        if (introResult === undefined) {
          break;
        }
        introResult.set('editable', !versions[i].isLive);
        introResults[i] = introResult.toJSON();
      }


      //Get outro's
      var outros = [];
      for (i = 0; i < versions.length; i++) {
        var outro = this.collection.findWhere({post_type: "kwps_outro", post_parent : versions[i].ID});
        if (outro === undefined) {
          break;
        }
        outro.set('editable', !versions[i].isLive);
        outros[i] = outro.toJSON();
      }


      //Get questionGroups if questionGroups are open
      var qGroups = [];
      if (app.openRow.main_kwps_question_group) {
        for (i = 0; i < versions.length; i++) {
          var qGrJson = _.invoke(this.collection.where({post_type: "kwps_question_group", post_parent : versions[i].ID}), 'toJSON');
          var sortedQuestionGroupPerVersion = _.sortBy(qGrJson, "_kwps_sort_order");
          qGroups.push(sortedQuestionGroupPerVersion);
        }
      }

      var sortedQGroups = _.groupBy(_.flatten(qGroups,true),"_kwps_sort_order");

      var allqGroups = _.invoke(this.collection.where({post_type: "kwps_question_group"}), 'toJSON');

      var sortedAllQGroups =_.groupBy(_.flatten(allqGroups,true),"_kwps_sort_order");

      //Get questions if a questiongroup is open
      var qu = [];
      if (app.openRow.kwps_question_group >= 0) {
        for (i = 0; i < versions.length; i++) {
          var questionGroupId = this.collection.findWhere({post_type: "kwps_question_group", post_parent : versions[i].ID, _kwps_sort_order: app.openRow.kwps_question_group});
          if (questionGroupId !== undefined) {
            var quJson = _.invoke(this.collection.where({post_type: "kwps_question", post_parent : questionGroupId.id}), 'toJSON');
            var sortedQuestionsPerVersion = _.sortBy(quJson, "_kwps_sort_order");
            qu.push(sortedQuestionsPerVersion);
          } else {
            app.openRow.kwps_question_group = -1;
            app.openRow.kwps_question = -1;
          }
        }
      }

      var sortedQu = _.groupBy(_.flatten(qu,true),"_kwps_sort_order");

      //Get Result Profiles
      var resultProfiles = [];
      if(_.contains(testmodus.get('_kwps_allowed_output_types'), 'result-profile')) {
        for(i = 0; i < versions.length; i++) {
          var resultProfilesJson = _.invoke(this.collection.where({post_type: "kwps_result_profile", post_parent : versions[i].ID}), 'toJSON');
          var sortedResultProfilesJsonPerVersion = _.sortBy(resultProfilesJson, "_kwps_sort_order");
          resultProfiles.push(sortedResultProfilesJsonPerVersion);
        }
      }

      var sortedResultProfiles = _.groupBy(_.flatten(resultProfiles,true),"_kwps_sort_order");

      var allResultProfiles = _.invoke(this.collection.where({post_type: "kwps_result_profile"}), 'toJSON');

      var sortedAllResultProfiles =_.groupBy(_.flatten(allResultProfiles,true),"_kwps_sort_order");

      //Get Answers if a question is open
      var ans = [];
      if (app.openRow.kwps_question >= 0) {
        for (i = 0; i < versions.length; i++) {
          var openQuestionGroup = this.collection.findWhere({post_type: "kwps_question_group", post_parent : versions[i].ID, _kwps_sort_order: app.openRow.kwps_question_group});
          var openQuestion = this.collection.findWhere({post_type: "kwps_question", post_parent : openQuestionGroup.id, _kwps_sort_order: app.openRow.kwps_question});
          var ansJson = _.invoke(this.collection.where({post_type: "kwps_answer_option", post_parent: openQuestion.id}), 'toJSON');
          var sortedAnswersPerVersion = _.sortBy(ansJson, "_kwps_sort_order");
          ans.push(sortedAnswersPerVersion);
        }
      }

      var sortedAns = _.groupBy(_.flatten(ans,true),"_kwps_sort_order");

      var data = {
        kwpsUniquenessTypes: kwpsUniquenessTypes
      };

      var mainPost = this.collection.get(GetURLParameter('id'));
      data.title = mainPost.get('post_title');      
      
      if (versions.length >1) {
        for (i = versions.length - 1; i >=1; i--) {
          versions[i].deleteVersion = true;
        }
      }
      data.versions = versions;
      data.collection = testCollection.toJSON();
      data.testmodus = testmodus.toJSON();
      
      data.table = [];

      // TITLE INTRO
      data.table.push({
        colSpan : versions.length +1,
        title: "Intro",
        postType: "kwps_intro",
        mainTitle: true,
        add: (intros.length <= 0 && !_.some(versions, function(version) {return version.isLive;})),
        hasMore: (intros.length > 0),
        addText: 'Display Intro',
        opened: app.openRow.main_kwps_intro,
        amount: intros.length/ versions.length,
        maxAmount: 1,
        description: 'This introduction is shown when someone fills out the test for the first time.'
      });

      // INTRO
      if (intros.length > 0 && intros.length === versions.length && app.openRow.main_kwps_intro) {
        data.table.push({
          sorterArrows : false,
          postType: 'kwps_intro',
          deletable : !_.some(versions, function(version) {return version.isLive;}),
          hasMore: false,
          hasAmount: false,
          editable: !_.some(versions, function(version) {return version.isLive;}),
          versions: intros,
          mainRow: true,
          sortOrder: 0
        });
      }

      // TITLE INTRO RESULT
      data.table.push({
        colSpan : versions.length +1,
        title: "Intro Result",
        postType: "kwps_intro_result",
        mainTitle: true,
        add: (introResults.length <= 0 && !_.some(versions, function(version) {return version.isLive;})),
        hasMore: (introResults.length > 0),
        addText: 'Add Intro Result',
        opened: app.openRow.main_kwps_intro_result,
        amount: introResults.length/ versions.length,
        maxAmount: 1,
        description: 'For people who have already completed the test.'
      });

      // INTRO RESULT
      if (introResults.length > 0 && introResults.length === versions.length && app.openRow.main_kwps_intro_result) {
        data.table.push({
          sorterArrows : false,
          postType: 'kwps_intro_result',
          deletable : false,
          hasMore: false,
          hasAmount: false,
          editable: !_.some(versions, function(version) {return version.isLive;}),
          versions: introResults,
          mainRow: true,
          sortOrder: 0
        });
      }

      // TITLE QUESTION GROUP
      data.table.push({
        colSpan : versions.length +1,
        title: "Question pages",
        postType: "kwps_question_group",
        mainTitle: true,
        add: (allqGroups && (testmodus.get('_kwps_max_question_groups') < 0 || testmodus.get('_kwps_max_question_groups') > _.size(sortedAllQGroups)) && !_.some(versions, function(version) {return version.isLive;})),
        hasMore: (_.size(sortedAllQGroups) > 0),
        addText: 'Add question page',
        opened: app.openRow.main_kwps_question_group,
        amount: _.size(sortedAllQGroups),
        maxAmount: (testmodus.get('_kwps_max_question_groups') > 0)? testmodus.get('_kwps_max_question_groups') : "&infin;",
        description: "Here you can combine questions into a page."
      });

      if ( _.size(sortedAllQGroups) > 0 && app.openRow.main_kwps_question_group) {
        for (var sortOrderQG in sortedQGroups) {
          sortOrderQG = parseInt(sortOrderQG);

          _.each(sortedQGroups[sortOrderQG], function(questionGroup) {
            var parentVersion = this.collection.findWhere({ID: questionGroup.post_parent});
            questionGroup.editable = (parentVersion.get('post_status') !== 'publish');
          }, this);

          // QUESTION GROUP
          data.table.push({
            first: (sortOrderQG === 0),
            last: (sortOrderQG === allqGroups.length/versions.length-1),
            sorterArrows : (allqGroups.length/ versions.length > 1),
            postType: "kwps_question_group",
            deletable: (_.some(versions, function(version) {return version.isLive;}) || _.size(sortedQGroups)<2)? false:true, 
            hasMore: true,
            hasAmount: false,
            hasOpened: (app.openRow.kwps_question_group === sortOrderQG),
            versions: sortedQGroups[sortOrderQG],
            mainRow: true,
            sortOrder: sortOrderQG,
            number: parseInt(sortOrderQG) +1
            //amountOfSiblings : this.collection.where({post_type: "kwps_question", post_parent: qGroups[0][sortOrderQG].ID}).length
          });

          if(app.openRow.kwps_question_group === sortOrderQG) {

            // TITLE QUESTION
            data.table.push({
              questionTitle: true,
              title: "Questions",
              postType: "kwps_question",
              questionGroupSortOrder : sortOrderQG,
              addText: "Add question",
              colSpan : versions.length +1,
              add: (testmodus.get('_kwps_max_questions_per_question_group') < 0 || testmodus.get('_kwps_max_questions_per_question_group') > _.size(sortedQu).toString() && !_.some(versions, function(version) {return version.isLive;})),
              amount: _.size(sortedQu),
              maxAmount: (testmodus.get('_kwps_max_questions_per_question_group') > 0)? testmodus.get('_kwps_max_questions_per_question_group') : "&infin;",
              description: "This is a question."
            });
            
            for (var sortOrderQ in sortedQu) {
              sortOrderQ = parseInt(sortOrderQ);
              _.each(sortedQu[sortOrderQ], function(question) {
                var parentQuestionGroup = this.collection.findWhere({ID: question.post_parent});
                var parentVersion = this.collection.findWhere({ID: parentQuestionGroup.get("post_parent")});
                question.editable = (parentVersion.get('post_status') !== 'publish');
              }, this);

              // QUESTION
              data.table.push({
                sorterArrows : (_.size(sortedQu) > 1),
                first: (parseInt(sortOrderQ) === 0),
                last: (parseInt(sortOrderQ) === _.size(sortedQu)-1),
                versions: sortedQu[sortOrderQ],
                question: true,
                postType: "kwps_question",
                deletable: (_.some(versions, function(version) {return version.isLive;}) || _.size(sortedQu)<2)? false:true, 
                sortOrder: sortOrderQ,
                number: parseInt(sortOrderQ) +1,
                //amountOfSiblings : this.collection.where({post_type: "kwps_answer_option", post_parent : qu[0].ID}).length,
                hasOpened: (app.openRow.kwps_question === sortOrderQ)
              });

              if (app.openRow.kwps_question >= 0 && sortOrderQ === app.openRow.kwps_question) {

                // TITLE ANSWER
                data.table.push({
                  answerTitle: true,
                  title: "Answers",
                  postType: "kwps_answer_option",
                  addText: "Add answer",
                  questionSortOrder: sortOrderQ,
                  colSpan : versions.length +1,
                  add: ((testmodus.get('_kwps_max_answer_options_per_question') < 0 || testmodus.get('_kwps_max_answer_options_per_question') > _.size(sortedAns).toString()) && !_.some(versions, function(version) {return version.isLive;})),
                  amount: _.size(sortedAns),
                  maxAmount: (testmodus.get('_kwps_max_answer_options_per_question') > 0)? testmodus.get('_kwps_max_answer_options_per_question') : "&infin;",
                  description: "This is an answer."
                });

                for (var sortOrderA in sortedAns) {
                  sortOrderA = parseInt(sortOrderA);
                  var value;

                  _.each(sortedAns[sortOrderA], function(answer) {
                    var parentQuestion = this.collection.findWhere({ID: answer.post_parent});
                    var parentQuestionGroup = this.collection.findWhere({ID: parentQuestion.get("post_parent")});
                    var parentVersion = this.collection.findWhere({ID: parentQuestionGroup.get("post_parent")});
                    answer.editable = (parentVersion.get('post_status') !== 'publish');

                    if(typeof value === 'undefined') {
                      value = answer._kwps_answer_option_value;
                    }

                  }, this);

                  // ANSWER
                  data.table.push({
                    answer: true,
                    sorterArrows : (_.size(sortedAns) > 1),
                    first: (parseInt(sortOrderA) === 0),
                    last: (parseInt(sortOrderA) === _.size(sortedAns)-1),
                    deletable: (_.some(versions, function(version) {return version.isLive;}) || _.size(sortedAns)<3)? false:true, 
                    sortOrder : sortOrderA,
                    number: parseInt(sortOrderA) +1,
                    versions : sortedAns[sortOrderA],
                    postType: 'kwps_answer_option',
                    showValue: (stringToBoolean(testmodus.get('_kwps_answer_options_require_value'))),
                    value: value
                  });
                }
              }
            }
          }
        }
      }

      // TITLE RESULT PROFILE
      if(_.contains(testmodus.get('_kwps_allowed_output_types'), 'result-profile')) {
        data.table.push({
          colSpan: versions.length + 1,
          title: 'Result Profiles',
          postType: "kwps_result_profile",
          mainTitle: true,
          add: (allResultProfiles && !_.some(versions, function (version) {
            return version.isLive;
          })),
          hasMore: (_.size(sortedAllResultProfiles) > 0),
          addText: 'Add result profile',
          opened: app.openRow.main_kwps_result_profile,
          amount: _.size(sortedAllResultProfiles),
          maxAmount: "&infin;",
          description: "This is one of the results a participant get when he finishes the test."
        });
      }

      if ( _.size(sortedAllResultProfiles) > 0 && app.openRow.main_kwps_result_profile) {
        for (var sortOrderRP in sortedResultProfiles) {

          _.each(sortedResultProfiles[sortOrderRP], function (resultProfile) {
            var parentVersion = this.collection.findWhere({ID: resultProfile.post_parent});
            resultProfile.editable = (parentVersion.get('post_status') !== 'publish');
          }, this);
          
          // RESULT PROFILE
          data.table.push({
            first: (parseInt(sortOrderRP) === 0),
            last: (parseInt(sortOrderRP) === allResultProfiles.length/ versions.length-1),
            sorterArrows : (allResultProfiles.length/ versions.length > 1),
            postType: "kwps_result_profile",
            deletable: (_.some(versions, function(version) {return version.isLive;}) || _.size(sortedResultProfiles)<2)? false:true, 
            hasMore: false,
            hasAmount: false,
            hasOpened: (app.openRow.kwps_result_profile === sortOrderRP),
            min: String(_.min(sortedResultProfiles[sortOrderRP], function (v) {return v._kwps_min_value})._kwps_min_value),
            max: String(_.max(sortedResultProfiles[sortOrderRP], function (v) {return v._kwps_max_value})._kwps_max_value),
            versions: sortedResultProfiles[sortOrderRP],
            mainRow: true,
            sortOrder: sortOrderRP
            //amountOfSiblings : this.collection.where({post_type: "kwps_question", post_parent: qGroups[0][sortOrderQG].ID}).length
          });
        }
      }

      // TITLE OUTRO
      data.table.push({
        colSpan : data.versions.length +1,
        title: "Outro",
        postType: "kwps_outro",
        mainTitle: true,
        add: (outros.length <= 0 && !_.some(versions, function(version) {return version.isLive;})),
        hasMore: (outros.length > 0),
        addText: 'Add outro',
        opened: app.openRow.main_kwps_outro,
        amount: outros.length/ versions.length,
        maxAmount: 1,
        description: "You see this page at the end of the test, it contains the participants result."
      });

      //OUTRO
      if (outros.length > 0 && outros.length === versions.length && app.openRow.main_kwps_outro) {
        data.table.push({
          sorterArrows : false,
          postType: 'kwps_outro',
          deletable : false,
          hasMore: false,
          hasAmount: false,
          versions: outros,
          mainRow: true,
          sortOrder: 0
        });
      }

      return data;
    },
    deleteVersion: function(versionId) {
      var toDelete = this.collection.get(versionId);
      toDelete.destroy();
      this.collection.remove(toDelete);
      this.render();
    },
    deleteRow: function(postType, sortOrder) {
      if(app.openRow[postType] === sortOrder) {
        app.openRow[postType] = -1;
      }

      var parentPostType = this.getParent(postType);
      var parentPostTypeSortOrder = app.openRow[parentPostType];
      var whereAttributes = {post_type: parentPostType};

      if(parentPostTypeSortOrder) {
        whereAttributes._kwps_sort_order = parentPostTypeSortOrder;
      }

      var parentPosts = this.collection.where(whereAttributes);
      var postsToMove = [];
      var postsToDelete = this.collection.where({post_type: postType, _kwps_sort_order: sortOrder});
      for (i = 0; i < postsToDelete.length; i++) {
        postsToDelete[i].destroy();
      }
      this.collection.remove(postsToDelete);

      parentPosts.forEach(function(parentPost) {
        var allRemainingPostsInParent = this.collection.where({post_type: postType, post_parent: parentPost.get('ID')});

        var newPosts = _.filter(allRemainingPostsInParent, function(post) {
          return post.get('_kwps_sort_order') > sortOrder;
        });
        postsToMove = _.union(postsToMove, newPosts);

      }, this);

      var that = this;
      postsToMove.forEach(function(postToMove) {
        var currentSortOrder = postToMove.get('_kwps_sort_order');
        var newSortOrder = currentSortOrder - 1;
        postToMove.set('_kwps_sort_order', newSortOrder);
        postToMove.save();
        that.render();
      });
    },
    deletePostType: function(e) {
      e.preventDefault();
      var postType = $(e.currentTarget).closest('tr').data('post-type');
      switch (postType) {
        case 'kwps_version':
          var versionId = $(e.currentTarget).data('version-id');
          this.deleteVersion(versionId);
          break;
        case 'kwps_answer_option':
        case 'kwps_question':
        case 'kwps_question_group':
        case 'kwps_intro':
        case 'kwps_outro':
        case 'kwps_result_profile':
          var sortOrder = $(e.currentTarget).closest('tr').data('sort-order');
          this.deleteRow(postType, sortOrder);
          break;
        default:
          console.log('no post type was given', postType);
      }
    },
    createNew: function (e) {
      e.preventDefault();
      var postType = $(e.currentTarget).closest('tr').data('post-type');
      var kwpsPolls = this.collection.where({post_type: 'kwps_version'});
      // get the id of the post parent(main version)
      var kwpsPollLen = kwpsPolls.length;
      var that = this;
      var sortOrder;
      switch (postType) {
        case 'main_kwps_intro':
        case 'kwps_intro':
          for(i = 0; i < kwpsPollLen; i++) {
            this.createIntro(kwpsPolls[i].id);
          }
          break;
        case 'main_kwps_intro_result':
        case 'kwps_intro_result':
          for(i = 0; i < kwpsPollLen; i++) {
            this.createIntroResult(kwpsPolls[i].id);
          }
          break;
        case 'main_kwps_outro':
        case 'kwps_outro':
          for(i = 0; i < kwpsPollLen; i++) {
            this.createOutro(kwpsPolls[i].id);
          }
          break;
        case 'main_kwps_question_group':
        case 'kwps_question_group':
          sortOrder = _.max(_.invoke(this.collection.where({post_type: 'kwps_question_group'}),"toJSON"), function (a) {return a._kwps_sort_order;});
          sortOrder = (sortOrder === -Infinity || sortOrder === Infinity)? 0: parseInt(sortOrder._kwps_sort_order)+1;
          for(i = 0; i < kwpsPollLen; i++) {
            this.createQuestionGroup(kwpsPolls[i].id, i, sortOrder);
          }
          break;
        case 'main_kwps_result_profile':
        case 'kwps_result_profile':
          sortOrder = _.max(_.invoke(this.collection.where({post_type: 'kwps_result_profile'}),"toJSON"), function (a) {return a._kwps_sort_order;});
          sortOrder = (sortOrder === -Infinity || sortOrder === Infinity)? 0: parseInt(sortOrder._kwps_sort_order)+1;
          for(i = 0; i < kwpsPollLen; i++) {
            this.createResultProfile(kwpsPolls[i].id, sortOrder);
          }
          break;
        case 'kwps_question':
          var sortOrderOfQuestionGroup = $(e.currentTarget).closest('tr').data('sort-order');
          var versionsOfOpenedQuestionGroup = this.collection.where({post_type: 'kwps_question_group', _kwps_sort_order: sortOrderOfQuestionGroup});
          var highestSortOrder = 0;
          for (i = versionsOfOpenedQuestionGroup.length - 1; i >= 0; i--) {
            var connectedQuestionsToOpenedQuestionGroup = _.invoke(this.collection.where({post_type: "kwps_question", post_parent: versionsOfOpenedQuestionGroup[i].id}), 'toJSON');
            highestSortOrder = Math.max (parseInt(_.max( connectedQuestionsToOpenedQuestionGroup ,function (model) {return parseInt(model._kwps_sort_order);})._kwps_sort_order),highestSortOrder);
          }
          highestSortOrder = (isNaN(highestSortOrder))? 0:highestSortOrder;
          for (i = versionsOfOpenedQuestionGroup.length - 1; i >= 0; i--) {
            this.createQuestion(versionsOfOpenedQuestionGroup[i].id , highestSortOrder +1, function (newQuestion) {
              for (i = 0; i < 2; i++) {
                that.createAnswer(newQuestion.get('ID'), i);
              }
            });
          }
          break;
        case 'kwps_answer_option':
          sortOrder = $(e.currentTarget).closest('tr').data('sort-order');
          for(i = 0; i < kwpsPollLen; i++) {
            var questionGroups = this.collection.where({post_type: 'kwps_question_group', post_parent: kwpsPolls[i].id, _kwps_sort_order: app.openRow.kwps_question_group});
            for(var j = 0; j < questionGroups.length; j++) {
              var questions = this.collection.where({post_type: 'kwps_question', post_parent: questionGroups[j].id, _kwps_sort_order: sortOrder});
              for(var k = 0; k < questions.length; k++) {
                var index = this.collection.where({post_type: 'kwps_answer_option', post_parent: questions[k].id}).length;
                this.createAnswer(questions[k].id, index);
              }
            }
          }
          break;
        case 'kwps_version':
          this.createVersion(kwpsPolls[kwpsPollLen-1], kwpsPollLen);
          break;
        default:
          console.log('no post type was given');
      }
    },
    createVersion: function (previousVersion, index) {
      var that = this;
      var versionData = {
        post_type: "kwps_version",
        post_status: "draft",
        post_title : kwps_translations['Copy of'] + " " + previousVersion.get('post_title'),
        post_parent : previousVersion.get('post_parent'),
        _kwps_sort_order : index
      };

      app.kwpsPollsCollection.create(
        versionData,
        {
          success: function(newVersion, response, options) {
            that.createIntro(newVersion.get('ID'), that.getExistingObject(previousVersion.get('ID'), 'kwps_intro', true));
            that.createIntroResult(newVersion.get('ID'), that.getExistingObject(previousVersion.get('ID'), 'kwps_intro_result', true));
            that.createOutro(newVersion.get('ID'), that.getExistingObject(previousVersion.get('ID'), 'kwps_outro', true));

            var resultProfiles = that.getExistingObject(previousVersion.get('ID'), 'kwps_result_profile', false);
            for (i = 0; i < resultProfiles.length; i++) {
              var resultProfilesOriginal = resultProfiles[i];
              that.createResultProfile(newVersion.get('ID'), resultProfilesOriginal);
            }

            var questionGroups = that.getExistingObject(previousVersion.get('ID'), 'kwps_question_group', false);
            for (i = 0; i < questionGroups.length; i++) {
              var questionGroupOriginal = questionGroups[i];
              that.createQuestionGroup(newVersion.get('ID'), questionGroupOriginal, function(newQuestionGroup) {
                var questionsInGroup = that.getExistingObject(questionGroupOriginal.get('ID'), 'kwps_question', false);

                for (i = 0; i < questionsInGroup.length; i++) {
                  var questionOriginal = questionsInGroup[i];

                  that.createQuestion(newQuestionGroup.get('ID'), questionOriginal, function(newQuestion) {
                    var answersInQuestion = that.getExistingObject(questionOriginal.get('ID'), 'kwps_answer_option', false);

                    for (i = 0; i < answersInQuestion.length; i++) {
                      var answersInQuestionOriginal = answersInQuestion[i];
                      that.createAnswer(newQuestion.get('ID'), answersInQuestionOriginal, function(newAnswer) {
                        console.log('answer created: ' + newAnswer.id);
                      });
                    }
                  });
                }
              });
            }

          }
        }
      );
    },
    createIntro: function (post_parent, data) {
      if(typeof data === 'boolean') {
        return;
      }

      var that = this,
        introData = {
          post_type: "kwps_intro",
          post_status: "draft",
          post_content : kwps_translations.Intro,
          post_parent : post_parent,
          _kwps_sort_order : 0
        };

      if(data) {
        introData.post_content = data.get('post_content');
      }

      this.collection.create(
        introData,
        {
          wait: true
        }
      );
    },
    createIntroResult: function (post_parent, data) {
      if(typeof data === 'boolean') {
        return;
      }

      var that = this,
        introResultData = {
          post_type: "kwps_intro_result",
          post_status: "draft",
          post_content : kwps_translations['Intro result'],
          post_parent : post_parent,
          _kwps_sort_order : 0
        };

      if(data) {
        introResultData.post_content = data.get('post_content');
      }

      this.collection.create(
        introResultData,
        {
          wait: true
        }
      );
    },
    createOutro: function (post_parent, data) {
      if(typeof data === 'boolean') {
        return;
      }

      var that = this,
        outroData = {
          post_type: "kwps_outro",
          post_status: "draft",
          post_content : kwps_translations.Outro,
          post_parent : post_parent,
          _kwps_sort_order : 0
        };

      if(data) {
        outroData.post_content = data.get('post_content');
      }

      this.collection.create(
        outroData,
        {
          wait: true
        }
      );
    },
    createResultProfile: function (post_parent, data, cb) {
      var resultProfileData = {
        post_type: "kwps_result_profile",
        post_status: "draft",
        post_title : "",
        post_content : "",
        post_parent : post_parent,
        _kwps_sort_order : 0,
        _kwps_min_value: 0,
        _kwps_max_value: 0
      };

      if(typeof data === 'object') {
        resultProfileData.post_title = data.get('post_title');
        resultProfileData.post_content = data.get('post_content');
        resultProfileData._kwps_sort_order = data.get('_kwps_sort_order');
      } else {
        resultProfileData._kwps_sort_order = data;
        resultProfileData.post_title = kwps_translations["Result profile"] + " " + (data + 1);
        resultProfileData.post_content = kwps_translations["Result profile"] + " " + (data + 1);
      }

      this.collection.create(resultProfileData, {
        wait: true,
        success: function(model, response, options) {
          if(cb) {
            cb(model);
          }
        }
      });
    },
    createQuestionGroup: function (post_parent, data, cb) {
      var questionGroupData = {
        post_type: "kwps_question_group",
        post_status: "draft",
        post_title : "",
        post_content : "",
        post_parent : post_parent,
        _kwps_sort_order : 0,
        _kwps_min_value: 0,
        _kwps_max_value: 0
      };

      if(typeof data === 'object') {
        questionGroupData.post_title = data.get('post_title');
        questionGroupData.post_content = data.get('post_content');
        questionGroupData._kwps_sort_order = data.get('_kwps_sort_order');
      } else {
        questionGroupData._kwps_sort_order = data;
        questionGroupData.post_title = kwps_translations["Question Group"] + " " + (data + 1);
        questionGroupData.post_content = kwps_translations["Question Group"] + " " + (data + 1);
      }

      this.collection.create(questionGroupData, {
        wait: true,
        success: function(model, response, options) {
          if(cb) {
            cb(model);
          }
        }
      });
    },
    createQuestion: function (post_parent, data, cb) {
      var questionData = {
        post_type: "kwps_question",
        post_status: "draft",
        post_title : "",
        post_content : "",
        post_parent : post_parent,
        _kwps_sort_order : 0,
        _kwps_min_value: 0,
        _kwps_max_value: 0
      };

      if(typeof data === 'object') {
        questionData.post_title = data.get('post_title');
        questionData.post_content = data.get('post_content');
        questionData._kwps_sort_order = data.get('_kwps_sort_order');
      } else {
        questionData._kwps_sort_order = data;
        questionData.post_title = kwps_translations.Question + " " + (data + 1);
        questionData.post_content = kwps_translations.Question + " " + (data + 1);
      }

      this.collection.create(questionData, {
        wait: true,
        success: function(model, response, options) {
          if(cb) {
            cb(model);
          }
        }
      });
    },
    createAnswer: function (post_parent, data, cb) {
      var answerData = {
        post_type: "kwps_answer_option",
        post_status: "draft",
        post_title : "",
        post_content : "",
        post_parent : post_parent,
        _kwps_sort_order : 0,
        _kwps_min_value: 0,
        _kwps_max_value: 0,
        _kwps_answer_option_value: 0
      };

      if(typeof data === 'object') {
        answerData.post_title = data.get('post_title');
        answerData.post_content = data.get('post_content');
        answerData._kwps_sort_order = data.get('_kwps_sort_order');
      } else {
        answerData._kwps_sort_order = data;
        answerData.post_title = kwps_translations["Answer Option"] + " " + (data + 1);
        answerData.post_content = kwps_translations["Answer Option"] + " " + (data + 1);
      }

      this.collection.create(answerData, {
        wait: true,
        success: function(model, response, options) {
          if(cb) {
            cb(model);
          }
        }
      });
    },
    showActions: function(event) {
      $(event.target).find(".actions").show();
    },
    hideActions: function(event) {
      $(event.target).find(".actions").hide();
    },
    toggleDetails: function(event) {
      var postType = $(event.currentTarget).closest('tr').data('post-type');
      var sortOrder = $(event.currentTarget).closest('tr').data('sort-order');
      switch (postType) {
        case "main_kwps_intro" :
        case "main_kwps_intro_result" :
        case "main_kwps_outro" :
        case "main_kwps_question_group" :
          app.openRow[postType] = !app.openRow[postType];
          break;
        case "kwps_question" :
          app.openRow[postType] = (app.openRow[postType] === sortOrder)? -1 : sortOrder;
          break;
        case "kwps_question_group" :
          if (app.openRow.kwps_question_group === sortOrder) {
            app.openRow.kwps_question = -1;
            app.openRow.kwps_question_group = -1;
          } else {
            app.openRow.kwps_question_group = sortOrder;
          }
          break;
        default:
          console.log('no post type was given', postType);
      }
      this.render();
    },
    preview: function(event) {
    },
    edit: function(event) {

      var kwpsAttribute = $(event.target).closest('div.actions').data('kwps-attribute');
      var kwpsId = $(event.target).closest('div.actions').data('kwps-id');

      if(typeof kwpsId === 'undefined') {
        var editView = new app.KwpsViewEdit({model: app.test, attribute: kwpsAttribute});
      }
    },
    updateTestCollection: function(event) {
      var testCollection = this.collection.findWhere({post_type: "kwps_test_collection"});
      var attribute = $(event.target).attr("name");
      var value = $(event.target).val();

      if(value === "on") {
        value = 1;
      }
      testCollection.set(attribute, value);
      testCollection.save();
    },
    updateVersionPostTitle: function(event) {
      var attribute = $(event.target).attr("name");
      var value = $(event.target).val();
      var versionId = $(event.currentTarget).closest('th').data('version-id');
      var version = this.collection.get(versionId);
      version.set(attribute, value);
      version.save();
    },
    moveItem: function(event) {
      var currentSortOrder = $(event.currentTarget).closest('tr').data('sort-order');
      var newSortOrder;
      if($(event.currentTarget).hasClass('up')) {
        newSortOrder = currentSortOrder-1;
      } else {
        newSortOrder = currentSortOrder+1;
      }

      var postType = $(event.currentTarget).closest('tr').data('post-type');
      var toMove = [],
        toCorrect = [];
      if(postType === 'kwps_question' || postType === 'kwps_answer_option') {

        var parentPostType = this.getParent(postType);
        var parentPostTypeSortOrder = app.openRow[parentPostType];
        var parentPosts = this.collection.where({post_type: parentPostType, _kwps_sort_order: parentPostTypeSortOrder});

        parentPosts.forEach(function(parentPost) {
          toMove = _.union(toMove, this.collection.where({post_type: postType, _kwps_sort_order: currentSortOrder, post_parent: parentPost.get('ID')}));
          toCorrect = _.union(toCorrect, this.collection.where({post_type: postType, _kwps_sort_order: newSortOrder, post_parent: parentPost.get('ID')}));
        }, this);

      } else {
        toMove = this.collection.where({post_type: postType, _kwps_sort_order: currentSortOrder});
        toCorrect = this.collection.where({post_type: postType, _kwps_sort_order: newSortOrder});
      }


      toMove.forEach(function(post) {
        post.set('_kwps_sort_order', newSortOrder);
        post.save();
      });

      toCorrect.forEach(function(post) {
        post.set('_kwps_sort_order', currentSortOrder);
        post.save();
      });

      this.render();
    },
    getParent: function(postType) {
      var parentPostType;
      switch (postType) {
        case 'kwps_version':
          parentPostType = 'kwps_collection';
          break;
        case 'kwps_question_group':
        case 'kwps_intro':
        case 'kwps_outro':
        case 'kwps_result_profile':
          parentPostType = 'kwps_version';
          break;
        case 'kwps_question':
          parentPostType = 'kwps_question_group';
          break;
        case 'kwps_answer_option':
          parentPostType = 'kwps_question';
          break;
        default:
          console.log('no post type was given', postType);
      }

      return parentPostType;
    },
    makeLive: function(event) {
      event.preventDefault();
      var versionId = $(event.currentTarget).closest('th').data('version-id');
      var version = this.collection.findWhere({ID: versionId});
      var that = this;
      $.ajax({
        type: 'POST',
        data: JSON.stringify(version.toJSON()),
        url: app.url + 'kwps_validate_version',
        contentType: "application/json; charset=utf-8",
        dataType: 'json'
      })
        .done(function(request, status, error) {
          version.set('post_status', 'publish');
          version.save({
            wait: true,
            error: function(version, resp, options)  {
              console.log(resp);
            },
            success: function() {
              that.render();
            }
          });
        })
        .fail(function() {
          alert(kwps_translations['Errors occurred. Please check below for more information.']);
        });


    },
    clearEntries: function(event) {
      event.preventDefault();
      if(confirm(kwps_translations['This will delete all entries. Are you sure?'])) {
        var id = $(event.currentTarget).closest('th').data('post-id');
        $.post(
          app.url + 'kwps_delete_entries_from_version',JSON.stringify(
          {
            post_parent: id
          }),
          function(data) {
            console.log('deleted: ' + data.count);
          }
        );
      }
    },
    getExistingObject: function(post_parent, post_type, single) {
      if(typeof single === 'undefined') {
        single = true;
      }
      var query = {post_type: post_type, post_parent: post_parent},
        result;

      if(single) {
        result = this.collection.findWhere(query);
      } else {
        result = this.collection.where(query);
      }

      return (typeof result === 'undefined')? false : result;
    },
    validateVersion: function(event) {
      var versions = this.collection.where({post_type: 'kwps_version'}),
        that = this;
      _.each(versions, function(version) {
        $.ajax({
          type: 'POST',
          data: JSON.stringify(version.toJSON()),
          url: app.url + 'kwps_validate_version',
          contentType: "application/json; charset=utf-8",
          dataType: 'json'
        })
          .fail(function(request, status, error) {
            version.set('validation', request.responseJSON);
            that.render();
          })
          .done(function(request, status, error) {
            version.set('validation', request.responseJSON);
            that.render();
          });
      }, this);
    }
  });

  app.KwpsViewResult = Backbone.View.extend({
    el: '#tabs-results',
    initialize: function() {
      this.render();
    },
    render: function() {
      $(this.el).html(app.templates.result(this.model));
    }
  });

  app.KwpsViewAddResult = Backbone.View.extend({
    el: '#extra-test',
    initialize: function() {
      this.render();
    },
    render: function() {
      // console.log(this.model);
      // console.log(app.templates.add_result(this.model));
      $(this.el).html(app.templates.add_result(this.model));
    }
  });

  app.KwpsViewEdit = Backbone.View.extend({
    el: '#kwps_test',

    initialize: function (options) {
      this.options = options || {};
      _.bindAll(this, 'cleanup');
      this.render();
    },
    cleanup: function() {
      this.undelegateEvents();
      $(this.el).empty();
    },
    events: {
      'click button#update': 'updateData',
      'click button#add-media-button': 'addMedia',
      'click button#add-result-button': 'addResult',
      'click td.savesend input.button': 'insertIntoEditor',
      'click button#add-result-to-editor': 'insertChartIntoEditor'
    },
    render: function() {
      var testCollection = app.kwpsPollsCollection.findWhere({post_type: "kwps_test_collection"});
      var testmodus = app.kwpsPollsCollection.findWhere({ID: testCollection.get('post_parent')});


      Backbone.Validation.bind(this, {
        valid: function (view, attr, selector) {
          var $el = view.$('[name=' + attr + ']'),
            $group = $el.closest('.form-group');

          $group.removeClass('has-error');
          $group.find('.help-block').html('').addClass('hidden');
        },
        invalid: function (view, attr, error, selector) {
          var $el = view.$('[name=' + attr + ']'),
            $group = $el.closest('.form-group');
          $group.addClass('has-error');
          $group.find('.help-block').html(error).removeClass('hidden');
        }
      });

      var data =  this.model.toJSON();
      data.attribute = this.options.attribute;
      data.label = kwps_translations[this.options.attribute];
      data.addResults = (this.model.get('post_type') === "kwps_outro" || this.model.get('post_type') === "kwps_intro_result");
      data.min_max = (this.model.get('post_type') === 'kwps_result_profile' && _.contains(testmodus.get('_kwps_allowed_output_types'), 'result-profile'));
      data.showValue = (testmodus.get('_kwps_answer_options_require_value') && this.model.get('post_type') === 'kwps_answer_option');
      data._kwps_answer_option_value = this.model.get("_kwps_answer_option_value");

      var validation = {
        post_content: {
          required: true
        },
        post_parent: {
          required: true
        },
        _kwps_sort_order: {
          required: true,
            min: 0
        }
      };

      if(data.post_title) {
        validation.post_title = {
          required: true,
          msg: kwps_translations['Title is required']
        };
      }

      /* jshint ignore:start */
      if(this.model.get('post_type') === 'kwps_outro' || this.model.get('post_type') === 'kwps_intro_result') {
        validation.post_content = [
          {
            required: true
          },
          {
            pattern: '\\[kwps_result\\ .*\\]',
            msg: kwps_translations['You must add a result to the text']
          }
        ];
      }
      /* jshint ignore:end */

      if(data.min_max) {
        validation._kwps_min_value = {
          required: true,
          min: 0,
          msg: kwps_translations['Min value is required']
        };
        validation._kwps_max_value = {
          required: true,
          min: 0,
          msg: kwps_translations['Max value is required']
        };
      }

      if(data._kwps_answer_option_value) {
        validation._kwps_answer_option_value = {
          required: true,
          msg: kwps_translations['Value is required']
        };
      }

      this.model.validation = validation;

      $(this.el).html(app.templates.edit(data));
      tinymce.remove();
      tinymce.init({
        menubar: false,
        visual: true,
        statusbar: false,
        relative_urls: false,
        selector: "textarea",
        plugins: "code link hr paste lists table textcolor wordcount charmap image code",
        toolbar: ["bold italic strikethrough bullist numlist blockquote hr alignleft aligncenter alignright link unlink", 
                  "formatselect underline alignjustify forecolor backcolor paste removeformat charmap outdent indent undo redo | code"]
      });
    },
    /* BEGIN RESULT INPUT */
    addResult: function () {
      var testCollection = app.kwpsPollsCollection.findWhere({post_type: "kwps_test_collection"});
      var testmodus = app.kwpsPollsCollection.findWhere({ID: testCollection.get('post_parent')});
      var data = this.model.toJSON();
      var output ='';
      var allowedTypes = testmodus.attributes._kwps_allowed_output_types;

      tb_show('','../wp-content/plugins/klasse-wp-poll-survey/includes/show_charts.php?type=image&amp;TB_iframe=true');

      $.each(allowedTypes, function (key, value) {
        output +=   '<div id="' + value + '" class="media-item left"><label><h4>' + value.charAt(0).toUpperCase() + value.slice(1).split('-').join(' ') + '</h4><input type="radio" name="results" value="' + value + '"><img class="thumbnail" src="images/' + value + '.png" alt="' + value + '" height="128" width="128"></label></div>';
      });

      var selectedResult;
      var timer = setInterval( function () {

        $('iframe').contents().find('#charts').append(output);

        $('iframe').contents().find('input:radio').on('click', function () {
            selectedResult = $(this).next().attr('alt');
        });
        $('iframe').contents().find('#add-result-to-editor').on('click', function () {
          if (selectedResult) {
            $('iframe').contents().find('#tinymce').append('[kwps_result result='+ selectedResult + ']');
            tb_remove();
          } else {
            alert('Please select a result view to import');
          }
        });
        if ($('iframe').contents().find('#charts').length > 0) {
          clearInterval(timer);
        }
      }, 100);
      

      
      return false;
    },
    insertChartIntoEditor: function (html) {
      $('iframe', window.parent.document).contents().find('#tinymce').append('<div class="kwps-chart">Hello</div>');
      self.parent.tb_remove();
    },
    /* END RESULT INPUT */

    /* BEGIN MEDIA UPLOAD */
    addMedia: function () {
      tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
      return false;
    },
    insertIntoEditor: function (html) {
      var imgUrl = $('img',html).attr('src');
      var imgTitle = imgUrl.split("/").pop();
      $('iframe').contents().find('#tinymce').append('<img class="img img-' + imgTitle + '" src="' + imgUrl + '" alt="">');
      tb_remove();
    },
    /* END MEDIA UPLOAD */
    updateData: function(event) {
      event.preventDefault();
      tinymce.triggerSave();

      var data = $('#update-model').serializeObject();

      if(data._kwps_min_value) {
        data._kwps_min_value = parseInt(data._kwps_min_value);
      }
      if(data._kwps_max_value) {
        data._kwps_max_value = parseInt(data._kwps_max_value);
      }

      this.model.set(data);

      if(this.model.isValid(true)) {
        var that = this;
        this.model.save(data, {
          success: function() {
            that.cleanup();
            window.location = '#';
          }
        });
      }
    }
  });

  app.KwpsViewQuestion = Backbone.View.extend({
    el: '#kwps_test',
    initialize: function (options) {
      this.options = options || {};
      
      _.bindAll(this, 'cleanup');
      this.render();
    },
    cleanup: function() {
      this.undelegateEvents();
      $(this.el).empty();
    },
    events: {
      'click button#update': 'updateData'
    },
    render: function() {
      var answers = app.kwpsPollsCollection.where({post_type : "kwps_answer_option", post_parent : this.model.id});
      answers = _.each(answers, function (answer){
          return answer.toJSON();
      });
      var data = {
        question: this.model.toJSON(),
        answers: answers
      };
      $(this.el).html(app.templates.question(data));
      tinymce.remove();
    },
    updateData: function(event) {
      event.preventDefault();
      var value = $(event.target).closest('form').find('textarea').val();

      this.model.save("post_content", value);

      this.cleanup();
      // app.views.index.render();
      window.location = '#';
    }
  });

  if (typeof kwpsTests !== 'undefined') {
    kwpsTests.push(kwpsTestModi[0]);
    app.kwpsPollsCollection = new Backbone.Collection(kwpsTests, {
      model: KwpsModel
    });
  }
  app.router = new router();
  Backbone.history.start();

});
