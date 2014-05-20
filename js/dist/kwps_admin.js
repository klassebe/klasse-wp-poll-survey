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

this["kwps_admin_templates"]["choose_testmodus"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n		<li>\n			<input type=\"radio\" value=\"";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" name=\"post_parent\" id=\"kwpsTestModi_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"><label\n				for=\"kwpsTestModi_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">";
  if (helper = helpers.post_title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.post_title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</label>\n		</li>\n		";
  return buffer;
  }

  buffer += "<form id=\"create-new-test\">\n	<label>Test name</label>\n	<input name=\"post_title\">\n	<ul>\n		";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.kwpsTestModi), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n	</ul>\n	<div>\n		<button type=\"submit\">Create</button>\n	</div>\n</form>\n";
  return buffer;
  });

this["kwps_admin_templates"]["control_panel"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); partials = this.merge(partials, Handlebars.partials); data = data || {};
  var buffer = "", stack1, self=this;


  buffer += "<div id=\"icon-tests\" class=\"icon32\"><br/>\n</div>\n<h2>Test control panel</h2>\n\n<div class=\"test-input\">\n    <input type=\"text\" name=\"post_title\" id=\"post_title\" value=\"Wa tekst\" placeholder=\"<?php _e( 'New Test' ) ?>\"/>\n</div>\n\n<div id=\"tabs\">\n    <ul>\n        <li><a href=\"#tabs-add\">Test builder</a></li>\n        <li><a href=\"#tabs-results\">Control results</a></li>\n        <li><a href=\"#tabs-entries\">Manage entries</a></li>\n        <li><a href=\"#tabs-settings\">Settings</a></li>\n    </ul>\n    <div id=\"tabs-add\">\n        ";
  stack1 = self.invokePartial(partials.controlTest, 'controlTest', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </div>\n    <div id=\"tabs-results\">\n        ";
  stack1 = self.invokePartial(partials.controlResults, 'controlResults', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </div>\n    <div id=\"tabs-entries\">\n        ";
  stack1 = self.invokePartial(partials.manageEntries, 'manageEntries', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </div>\n    <div id=\"tabs-settings\">\n        ";
  stack1 = self.invokePartial(partials.settings, 'settings', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </div>\n</div>";
  return buffer;
  });

this["kwps_admin_templates"]["control_results"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus id\n            nunc. Duis scelerisque molestie turpis. Sed fringilla, massa eget luctus malesuada, metus eros molestie\n            lectus, ut tempus eros massa ut dolor. Aenean aliquet fringilla sem. Suspendisse sed ligula in ligula\n            suscipit aliquam. Praesent in eros vestibulum mi adipiscing adipiscing. Morbi facilisis. Curabitur\n            ornare consequat nunc. Aenean vel metus. Ut posuere viverra nulla. Aliquam erat volutpat. Pellentesque\n            convallis. Maecenas feugiat, tellus pellentesque pretium posuere, felis lorem euismod felis, eu ornare\n            leo nisi vel felis. Mauris consectetur tortor et purus.</p>";
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
  buffer += "\n\n\n            \n\n            \n\n            \n\n            ";
  stack1 = self.invokePartial(partials.controlTestMainTitleRow, 'controlTestMainTitleRow', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n            ";
  stack1 = self.invokePartial(partials.controlTestMainRow, 'controlTestMainRow', depth0, helpers, partials, data);
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </table>\n    </div>\n</div>";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_answer_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, options, functionType="function", escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing;


  buffer += "<tr class=\"post-1 type-post status-publish format-standard hentry category-uncategorized iedit author-self level-0\" style=\"background:#FFF8E7\">\n    <td class=\"column-action\" style=\"border: none; padding: 0px;height: 60px;\">\n        <div style=\"width:30px; height: 60px; float:left;\">\n            <svg x=\"0px\" y=\"0px\" viewBox=\"0 0 30 60\">\n                <rect x=\"0\" y=\"0\" fill=\"#FFFFFF\" width=\"20\" height=\"60\"></rect>\n                <rect x=\"20\" y=\"0\" fill=\"#FFBA00\" width=\"10\" height=\"60\"></rect>\n            </svg>\n        </div>\n        <div class=\"move\">\n            <span class=\"up passive\"></span>\n            <span class=\"down\"></span>\n        </div>\n        <div class=\"action\" style=\"padding: 10px; float:left;\">\n            <a class=\"delete-intro\">\n                <span class=\"del\" data-post-type=\"kwps_intro\">\n                    <span data-code=\"f182\" class=\"dashicons dashicons-trash\"></span>\n                </span>\n            </a>\n            1\n        </div>\n    </td>\n    <td id=\"_kwps_intro_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"post-title page-title column-title\">\n        <strong>\n            <a class=\"row-title\" href=\"#edit/"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.kwpsIntro)),stack1 == null || stack1 === false ? stack1 : stack1.ID)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\"\n               title=\"Edit “”\">mldfkqùdsfgjlmùdsfgùmfdkg</a>\n        </strong>\n\n        <div class=\"actions\" style=\"display: none\"><a href=\"#edit/"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.kwpsIntro)),stack1 == null || stack1 === false ? stack1 : stack1.ID)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\">edit</a></div>\n    </td>\n    <td id=\"_kwps_intro_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"post-title page-title column-title\">\n        <strong>\n            <a class=\"row-title\" href=\"#edit/"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.kwpsIntro)),stack1 == null || stack1 === false ? stack1 : stack1.ID)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\"\n               title=\"Edit “"
    + escapeExpression((helper = helpers.subStringStripper || (depth0 && depth0.subStringStripper),options={hash:{},data:data},helper ? helper.call(depth0, ((stack1 = (depth0 && depth0.kwpsIntro)),stack1 == null || stack1 === false ? stack1 : stack1.post_content), 100, options) : helperMissing.call(depth0, "subStringStripper", ((stack1 = (depth0 && depth0.kwpsIntro)),stack1 == null || stack1 === false ? stack1 : stack1.post_content), 100, options)))
    + "”\">subStringStripper\n                kwpsIntro.post_content 100</a>\n        </strong>\n\n        <div class=\"actions\" style=\"display: none\"><a href=\"#edit/"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.kwpsIntro)),stack1 == null || stack1 === false ? stack1 : stack1.ID)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\">edit</a></div>\n    </td>\n    <td></td>\n</tr>";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_answer_title_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<tr class=\"post-1 type-post title status-publish format-standard hentry category-uncategorized iedit author-self level-0\" style=\"background:#FFB500\">\n                <th class=\"no-delete row-title\" colspan=\"3\" style=\" border-top: #FFBA00; padding: 0px;height: 42px;\">\n                    <div style=\"width:20px; height: 42px; float:left;\">\n                        <svg x=\"0px\" y=\"0px\" viewBox=\"0 0 20 42\">\n                            <rect x=\"0\" y=\"0\" fill=\"#FFFFFF\" width=\"10\" height=\"42\"></rect>\n                            <rect x=\"10\" y=\"0\" fill=\"#1E8CBE\" width=\"10\" height=\"42\"></rect>\n                        </svg>\n                    </div>\n                    <div style=\"padding: 10px; float:left;\">\n                        Answer options\n                        <button class=\"button add\" data-post-type=\"kwps_intro\">\n                            <span data-code=\"f132\" class=\"dashicons dashicons-plus\"></span>\n                            add answers\n                        </button>\n                    </div> \n                </th>\n                <th class=\"no-delete row-title\" style=\"height:20px;\">3</th>\n            </tr>";
  });

this["kwps_admin_templates"]["control_test_main_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  
  return "\n        <div class=\"move\">\n            <span class=\"up passive\"></span>\n            <span class=\"down\"></span>\n        </div>\n        ";
  }

function program3(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.hasOpened), {hash:{},inverse:self.program(6, program6, data),fn:self.program(4, program4, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  return buffer;
  }
function program4(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <span data-code=\"f140\" class=\"dashicons dashicons-arrow-down toggle-details\" data-type=\"question\" data-question-row=\""
    + escapeExpression(((stack1 = (data == null || data === false ? data : data.index)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\"></span>\n                ";
  return buffer;
  }

function program6(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <span data-code=\"f140\" class=\"dashicons dashicons-arrow-right toggle-details\" data-type=\"question\" data-question-row=\""
    + escapeExpression(((stack1 = (data == null || data === false ? data : data.index)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\"></span>\n                ";
  return buffer;
  }

function program8(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n            <a class=\"delete-intro\">\n                <span class=\"del\" data-post-type=\"";
  if (helper = helpers.postType) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.postType); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" data-type=\"row\" data-sort-order=\"";
  if (helper = helpers.sortOrder) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.sortOrder); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n                    <span data-code=\"f182\" class=\"dashicons dashicons-trash\"></span>\n                </span>\n            </a>\n            ";
  return buffer;
  }

function program10(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n        <td id=\"_kwps_intro_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"post-title page-title column-title\">\n            <strong>\n                <a class=\"row-title\" href=\"#edit/";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"\n                   title=\"Edit “”\">";
  if (helper = helpers.post_content) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.post_content); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</a>\n            </strong>\n\n            <div class=\"actions\" style=\"display: none\"><a href=\"#edit/";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">edit</a></div>\n        </td>\n    ";
  return buffer;
  }

  buffer += "\n<tr class=\"type-post status-publish format-standard hentry category-uncategorized iedit author-self level-0\">\n    <td class=\"column-action\" style=\"border: none; padding: 0px;height: 60px;\">\n        <div style=\"width:10px; height: 60px; float:left;\">\n            <svg x=\"0px\" y=\"0px\" viewBox=\"0 0 10 60\">\n                <rect x=\"0\" y=\"0\" fill=\"#555555\" width=\"10\" height=\"60\"></rect>\n            </svg>\n        </div>\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.sorterArrows), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        <div class=\"action\" style=\"padding: 10px; float:left;\">\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.hasMore), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.deletable), {hash:{},inverse:self.noop,fn:self.program(8, program8, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </div>\n    </td>\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.versions), {hash:{},inverse:self.noop,fn:self.program(10, program10, data),data:data});
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
  var buffer = "", stack1, helper, options, functionType="function", escapeExpression=this.escapeExpression, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n        	";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.opened), {hash:{},inverse:self.program(2, program2, data),fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        ";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n            	<span class=\"dashicons dashicons-arrow-down toggle-details\" data-type=\"question\" data-question-row=\""
    + escapeExpression(((stack1 = (data == null || data === false ? data : data.index)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\"></span>\n        	";
  return buffer;
  }

function program4(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n        <button class=\"button add\" data-post-type=\"kwps_intro\">\n            <span data-code=\"f132\" class=\"dashicons dashicons-plus\"></span>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.addText), options) : helperMissing.call(depth0, "t", (depth0 && depth0.addText), options)))
    + "\n        </button>\n        ";
  return buffer;
  }

  buffer += "<tr class=\"title\">\n    <th class=\"row-title\" colspan=\"";
  if (helper = helpers.colSpan) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.colSpan); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.hasMore), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.title), options) : helperMissing.call(depth0, "t", (depth0 && depth0.title), options)))
    + "\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.add), {hash:{},inverse:self.noop,fn:self.program(4, program4, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </th>\n    <th class=\"row-title\">";
  if (helper = helpers.amount) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.amount); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</th>\n</tr>";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_question_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n                <td id=\"_kwps_intro_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"post-title page-title column-title\">\n                    <strong>\n                        <a class=\"row-title\" href=\"#edit/";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"\n                           title=\"Edit “";
  if (helper = helpers.post_content) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.post_content); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "”\">";
  if (helper = helpers.post_content) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.post_content); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</a>\n                    </strong>\n\n                    <div class=\"actions\" style=\"display: none\"><a href=\"#edit/";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">edit</a></div>\n                </td>\n                ";
  return buffer;
  }

  buffer += "<tr class=\"post-1 type-post status-publish format-standard hentry category-uncategorized iedit author-self level-0\" style=\"background: #DAEFF8;\">\n                <td class=\"column-action\" style=\"border: none; padding: 0px;height: 60px;\">\n                    <div style=\"width:20px; height: 60px; float:left;\">\n                        <svg x=\"0px\" y=\"0px\" viewBox=\"0 0 20 60\">\n                            <rect x=\"0\" y=\"0\" fill=\"#FFFFFF\" width=\"10\" height=\"60\"></rect>\n                            <rect x=\"10\" y=\"0\" fill=\"#1E8CBE\" width=\"10\" height=\"60\"></rect>\n                        </svg>\n                    </div>\n                    <div class=\"move\">\n                        <span class=\"up passive\"></span>\n                        <span class=\"down\"></span>\n                    </div>\n                    <div class=\"action\" style=\"padding: 10px; float:left;\">\n                        <span data-code=\"f140\" class=\"dashicons dashicons-arrow-down toggle-details\" data-type=\"question\" data-question-row=\"";
  if (helper = helpers.sortOrder) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.sortOrder); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"></span>\n\n                        <a class=\"delete-intro\">\n                            <span class=\"del\" data-post-type=\"kwps_qsdlfkj\">\n                                <span data-code=\"f182\" class=\"dashicons dashicons-trash\"></span>\n                            </span>\n                        </a>\n                        ";
  if (helper = helpers.sortOrder) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.sortOrder); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\n                    </div>\n                    \n                </td>\n                ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.versions), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                <td>";
  if (helper = helpers.amountOfSiblings) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.amountOfSiblings); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</td>\n            </tr>\n            ";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_question_title_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, options, functionType="function", escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing;


  buffer += " <tr class=\"post-1 type-post title status-publish format-standard hentry category-uncategorized iedit author-self level-0\" style=\"background: #1E8CBE;\">\n    <th class=\"no-delete row-title\" colspan=\"";
  if (helper = helpers.colSpan) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.colSpan); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" style=\"border-top: none ;padding: 0px;height: 40px;\">\n        <div style=\"width:10px; height: 42px; float:left;\">\n            <svg x=\"0px\" y=\"0px\" viewBox=\"0 0 10 42\">\n                 <rect x=\"0\" y=\"0\" fill=\"#555555\" width=\"10\" height=\"42\"></rect>\n            </svg>\n        </div>\n        <div style=\"padding: 10px; float:left; color:white;\">\n            "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.title), options) : helperMissing.call(depth0, "t", (depth0 && depth0.title), options)))
    + "\n            <button class=\"button add\" data-post-type=\"kwps_intro\">\n                <span data-code=\"f132\" class=\"dashicons dashicons-plus\"></span>\n                "
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0.addText), options) : helperMissing.call(depth0, "t", (depth0 && depth0.addText), options)))
    + "\n            </button>\n        </div>  \n    </th>\n    <th class=\"no-delete row-title\" style=\"height:20px; border-top: none;\"></th>\n</tr>";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_top_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n        <th class=\" column-title\">\n            <div class=\"column-tab\">\n                <input name=\"";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" value=\"";
  if (helper = helpers.post_title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.post_title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.deleteVersion), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            </div>\n        </th>\n    ";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n                    <span class=\"del\" data-post-type=\"kwps_version\" data-type=\"row\" data-sort-order=\"";
  if (helper = helpers._kwps_sort_order) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0._kwps_sort_order); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n                        <span data-code=\"f182\" class=\"dashicons dashicons-trash\"></span>\n                    </span>\n                ";
  return buffer;
  }

  buffer += "<tr>\n    <th class=\"no-delete column-action\">&nbsp;</th>\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.versions), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    <td class=\"column-title\" style=\"width:85px;\">\n        <div class=\"column-tab\">\n            <button class=\"add button\" data-post-type=\"kwps_version\">\n                <span data-code=\"f132\" class=\"dashicons dashicons-plus\"></span>\n            </button>\n        </div>\n    </td>\n</tr>";
  return buffer;
  });

this["kwps_admin_templates"]["manage_entries"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<p>EEEEE Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus\n                id nunc. Duis scelerisque molestie turpis. Sed fringilla, massa eget luctus malesuada, metus eros\n                molestie lectus, ut tempus eros massa ut dolor. Aenean aliquet fringilla sem. Suspendisse sed ligula in\n                ligula suscipit aliquam. Praesent in eros vestibulum mi adipiscing adipiscing. Morbi facilisis.\n                Curabitur ornare consequat nunc. Aenean vel metus. Ut posuere viverra nulla. Aliquam erat volutpat.\n                Pellentesque convallis. Maecenas feugiat, tellus pellentesque pretium posuere, felis lorem euismod\n                felis, eu ornare leo nisi vel felis. Mauris consectetur tortor et purus.</p>";
  });

this["kwps_admin_templates"]["nogietanders"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, functionType="function", escapeExpression=this.escapeExpression;


  if (helper = helpers.dot) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.dot); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\ndiv ver ";
  if (helper = helpers.lkjd) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.lkjd); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1);
  return buffer;
  });

this["kwps_admin_templates"]["settings"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing, self=this;

function program1(depth0,data,depth1) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n            <option value=\"";
  if (helper = helpers['function']) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0['function']); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"\n            "
    + escapeExpression((helper = helpers.selected || (depth0 && depth0.selected),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0['function']), ((stack1 = (depth1 && depth1.collection)),stack1 == null || stack1 === false ? stack1 : stack1._kwps_logged_in_user_limit), options) : helperMissing.call(depth0, "selected", (depth0 && depth0['function']), ((stack1 = (depth1 && depth1.collection)),stack1 == null || stack1 === false ? stack1 : stack1._kwps_logged_in_user_limit), options)))
    + " >";
  if (helper = helpers.label) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.label); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</option>\n            ";
  return buffer;
  }

function program3(depth0,data,depth1) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n            <option value=\"";
  if (helper = helpers['function']) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0['function']); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"\n            "
    + escapeExpression((helper = helpers.selected || (depth0 && depth0.selected),options={hash:{},data:data},helper ? helper.call(depth0, (depth0 && depth0['function']), ((stack1 = (depth1 && depth1.collection)),stack1 == null || stack1 === false ? stack1 : stack1._kwps_logged_out_user_limit), options) : helperMissing.call(depth0, "selected", (depth0 && depth0['function']), ((stack1 = (depth1 && depth1.collection)),stack1 == null || stack1 === false ? stack1 : stack1._kwps_logged_out_user_limit), options)))
    + " >";
  if (helper = helpers.label) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.label); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</option>\n            ";
  return buffer;
  }

  buffer += "<div>\n    <h2>Limit entries</h2>\n\n    <div>\n        <label for=\"kwps_logged_in_user_limit\">Logged in user</label>\n        <select id=\"kwps_logged_in_user_limit\" name=\"_kwps_logged_in_user_limit\" class=\"update-main\">\n            ";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.kwpsUniquenessTypes)),stack1 == null || stack1 === false ? stack1 : stack1.logged_in), {hash:{},inverse:self.noop,fn:self.programWithDepth(1, program1, data, depth0),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </select>\n    </div>\n    <div>\n        <label for=\"kwps_logged_out_user_limit\">Logged out user</label>\n        <select id=\"kwps_logged_out_user_limit\" name=\"_kwps_logged_out_user_limit\" class=\"update-main\">\n            ";
  stack1 = helpers.each.call(depth0, ((stack1 = (depth0 && depth0.kwpsUniquenessTypes)),stack1 == null || stack1 === false ? stack1 : stack1.logged_out), {hash:{},inverse:self.noop,fn:self.programWithDepth(3, program3, data, depth0),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </select>\n    </div>\n</div>\n";
  return buffer;
  });

this["kwps_admin_templates"]["table"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "";


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

  //translate helper
  Handlebars.registerHelper("t", function(key) {
    if (kwps_translations != undefined) {
      return (kwps_translations[key] != undefined)? kwps_translations[key] : key;
    } else {
      return key
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

  Handlebars.registerHelper("getColumnCount", function(versions) {
    versions = parseInt(versions.length);
    return versions + 2;
  });

  Handlebars.registerHelper('setIndex', function(value){
    this.index = Number(value + 1);
  });

  Handlebars.registerHelper('subStringStripper', function (html, length){
    var tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    var result = tmp.textContent || tmp.innerText || "";
    return  result.substring(0, length) + " ...";
  })

  Handlebars.registerHelper('sorter', function (index, obj) {
    var size = 0,
        key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    var result;
    if(index == 0) {
      result = '<span class="up passive"></span>'
    } else {
      result = '<span class="up"></span>'
    }
    if (index == size-1) {
      result = result + '<span class="down passive"></span>'
    } else {
      result = result + '<span class="down"></span>'
    }
    return result;
  });

  Handlebars.registerHelper('lastItem', function ( className ,index, obj){
    var size = 0,
        key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return (index == size-1)? className: "";
  });

  Handlebars.registerHelper('selected', function(option, value){
    if (option === value) {
      return ' selected';
    } else {
      return ''
    }
  });

  Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {

    switch (operator) {
      case '==':
        return (v1 == v2) ? options.fn(this) : options.inverse(this);
      case '===':
        return (v1 === v2) ? options.fn(this) : options.inverse(this);
      case '<':
        return (v1 < v2) ? options.fn(this) : options.inverse(this);
      case '<=':
        return (v1 <= v2) ? options.fn(this) : options.inverse(this);
      case '>':
        return (v1 > v2) ? options.fn(this) : options.inverse(this);
      case '>=':
        return (v1 >= v2) ? options.fn(this) : options.inverse(this);
      case '&&':
        return (v1 && v2) ? options.fn(this) : options.inverse(this);
      case '||':
        return (v1 || v2) ? options.fn(this) : options.inverse(this);
      default:
        return options.inverse(this);
    }
  });

  Handlebars.registerHelper('ifLength', function(obj, max, options) {
    var size = 0,
      key;
    for (key in obj) {
      if (obj.hasOwnProperty(key)) size++;
    }

    return (size < max || max < 0) ? options.fn(this) : options.inverse(this);
  });
jQuery(function ($) {
  $('#tabs').tabs();

  function GetURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
      var sParameterName = sURLVariables[i].split('=');
      if (sParameterName[0] == sParam) 
      {
          return sParameterName[1];
      }
    }
  };

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


  /* BACKBONE STUFF */
  var app = {};
  app.url = 'admin-ajax.php?action=';
  app.openAnswer = {
    questionGroup: 0,
    question: 0
  };
  app.views = {}

  app.templates = {
    controlPanel: kwps_admin_templates.control_panel,
    edit: kwps_admin_templates.table,
    question: kwps_admin_templates.table,
    newKwpsTest: kwps_admin_templates.choose_testmodus
  }
  
  // Routing
   var router = Backbone.Router.extend({
    routes: {
      '' : 'home',
      'edit/:id' : 'edit',
      'edit/question/:id' : 'editQuestion',
      'new/:type/:parentId' : 'new',
      'new': 'newKwpsTest',
      'delete/:id': 'deletePostType'
    },
    home : function () {
      // console.log("ROUTING TO: home")
      if (app.kwpsPollsCollection !== undefined) {
        app.views.index = new app.KwpsView({
          collection: app.kwpsPollsCollection
        });
        app.views.index.initialize();
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
        })
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

  KwpsModel = Backbone.Model.extend({
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
    initialize: function() {
      //this.bind("add", this.changeHandler);
    },
    changeHandler: function() {
      this.save({silent: true},{error : function (model, response, options) {
        console.log(this);
      }, success : function (model, response, options) {
        console.log(this)
      }});
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
    }
  });

  app.KwpsViewNewKwpsTest = Backbone.View.extend({
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

      var that = this;
      var model = new KwpsModel(postData);
      model.save({},{
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
          for (var i = 0; i < 1; i++) {
            that.createVersion(model.get('ID'), i);
          };
          var url = window.location.pathname + window.location.search + "\&action=edit\&id=" + model.get('ID');
          window.history.pushState( model.get('ID') , "Edit" , url);
          app.router.navigate('', {trigger: true});
        }
      });
    },
    createVersion: function (post_parent, index) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_version",
        post_status: "draft",
        post_title : "Version " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index
      });
      model.save({},{
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
          for (var i = 0; i < 1; i++) {
            that.createQuestionGroup(model.get('ID'), i);
          };
        }
      });
    },
    createQuestionGroup: function (post_parent, index) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_question_group",
        post_status: "draft",
        post_title : "Question Group " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index
      });
      model.save({},{
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
          for (var i = 0; i < 1; i++) {
            that.createQuestion(model.get('ID'), i, model.get('post_type'));
          };
        }
      });
    },
    createQuestion: function (post_parent, index, post_type) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_question",
        post_status: "draft",
        post_content : "question " + (index),
        post_parent : post_parent,
        _kwps_sort_order : index
      });
      model.save({},{
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
          for (var i = 0; i < 2; i++) {
            that.createAnswer(model.get('ID'), i, post_type);
          };
        }
      });
    },
    createAnswer: function (post_parent, index, post_type) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_answer_option",
        post_status: "draft",
        post_content : "answer " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index,
        _kwps_answer_option_value : 'value...'
      });
      model.save({},{
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
        }
      });
    }
  });

  app.KwpsView = Backbone.View.extend({
    el: '#kwps_test',
    initialize: function () {
      //_.bindAll(this, 'cleanup');
      this.render();
      this.listenTo(this.collection, 'add remove', this.render);
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
      'change #post_title': 'changeTitle',
      'change .update-main': 'updateTestCollection',
      'change .update-post-title': 'updatePostTitle'
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
      var data = {};
      var privData = {};
      privData.intro = [];

      var mainPost = this.collection.get(GetURLParameter('id'));
      data.title = mainPost.get('post_title');
      data.versions = this.collection.where({post_type: "kwps_version"});
      
      privData.amountOfVersions = data.versions.length;
      privData.amountOfQuestionPages = this.collection.where({post_type: "kwps_question_group"}).length;



      data.collection = this.collection.findWhere({post_type: "kwps_test_collection"}).toJSON();

      data.testmodus = this.collection.findWhere({ID: data.collection.post_parent}).toJSON();

      for (var i = 0; i < data.versions.length; i++) {
        data.versions[i] = data.versions[i].toJSON();
        var kwpsIntro = this.collection.findWhere({post_type: "kwps_intro", post_parent : data.versions[i].ID});
        if (kwpsIntro !== undefined) {
          data.intro = true;
          data.versions[i].kwpsIntro = kwpsIntro.toJSON();
          privData.intro[i]= kwpsIntro.toJSON();
        }
        var kwpsOutro = this.collection.findWhere({post_type: "kwps_outro", post_parent : data.versions[i].ID});
        if (kwpsOutro !== undefined) {
          data.outro = true;
          data.versions[i].kwpsOutro = kwpsOutro.toJSON();
        }
        if (i === 0) {
          data.versions[i].main = true;
        }
        if (data.versions.length > 1) {
          data.versions[i].deleteVersion = true;
        }
      };

      var questionGroups = this.collection.where({post_type: "kwps_question_group"});
      for (var i = questionGroups.length - 1; i >= 0; i--) {
        questionGroups[i] = questionGroups[i].toJSON();
      };
      questionGroups = _.toArray(_.groupBy(questionGroups, "_kwps_sort_order"));

      for (var g = questionGroups.length - 1; g >= 0; g--) {
        if (g == app.openAnswer.questionGroup) {
          questionGroups[g].open = true;

          for (var h = 0; h < questionGroups[g].length; h++) {
            var questions = this.collection.where({post_type: "kwps_question", post_parent : questionGroups[g][h].ID});
            for (var i = questions.length - 1; i >= 0; i--) {
              questions[i] = questions[i].toJSON();
            };
            questions = _.groupBy(questions, "_kwps_sort_order");

            for (var i in questions) {
              // if sortorder is equal to openAnswer show all answers
              //questions.length = questions[i].length;

              if (i == app.openAnswer.question) {
                questions[i].open = true;
                data.answers = [];

                for (var j = 0; j < questions[i].length; j++) {
                  var answers = this.collection.where({post_type: "kwps_answer_option", post_parent : questions[i][j].ID});
                  _.each(answers, function (answer, index, list) {
                    answers[index] = answer.toJSON();
                  });

                  data.answers.push(answers);
                };
              }
            };
          }
        }
      }

      privData.questionGroupsLength = questionGroups.length;

      data.questionGroups = questionGroups;
      data.questions = questions;
      data.answers = _.flatten(data.answers);
      data.answers = _.groupBy(data.answers, "_kwps_sort_order");
      data.kwpsUniquenessTypes = kwpsUniquenessTypes;
      data.open = app.openAnswer;


      data.table = [];
      data.table.push({
        colSpan : data.versions.length +1,
        title: "Intro",
        post_type: "kwps_intro",
        mainTitle: true,
        add: (this.collection.where({post_type: "kwps_intro"}).length > 0)? false:true,
        hasMore: (this.collection.where({post_type: "kwps_intro"}).length > 0)? true:false,
        addText: 'Add Intro',
        opened: true,
        amount: "" 
      });
      if (privData.intro.length == privData.amountOfVersions) {
        data.table.push({
          sorterArrows : false,
          postType: 'kwps_intro',
          deletable : true,
          hasMore: false,
          hasAmount: false,
          editable: true, //TODO look if the test is published or not.
          versions: privData.intro,
          mainRow: true,
          sortOrder: privData.intro[privData.intro.length-1]._kwps_sort_order
        })
      };
      data.table.push({
        colSpan : data.versions.length +1,
        title: "Question pages",
        post_type: "kwps_question_group",
        mainTitle: true,
        add: (data.testmodus._kwps_max_question_groups <= (privData.amountOfQuestionPages/ privData.amountOfVersions))? false:true,
        hasMore: (privData.amountOfQuestionPages/ privData.amountOfVersions > 0)? true:false,
        addText: 'Add question page',
        opened: true,
        amount: privData.amountOfQuestionPages/ privData.amountOfVersions
      });
      if (privData.questionGroupsLength > 0) {
        for (var key in data.questionGroups) {
          console.log('key',key);
          data.table.push({
            sorterArrows : (data.questionGroups.length > 1)? true : false,
            postType: data.questionGroups[key][0].post_type,
            deletable : true,
            hasMore: (this.collection.where({post_type: "kwps_question", post_parent : data.questionGroups[key][data.questionGroups.length - 1].ID}).length > 0)? true : false,
            hasAmount: false,
            hasOpened: (app.openAnswer.questionGroup == key)? true : false,
            editable: true, //TODO look if the test is published or not.
            versions: data.questionGroups[key],
            mainRow: true,
            sortOrder: data.questionGroups[key][0]._kwps_sort_order,
            amountOfSiblings : this.collection.where({post_type: "kwps_question", post_parent : data.questionGroups[key][data.questionGroups.length - 1].ID}).length
          })
          if(app.openAnswer.questionGroup == key) {
            privData.questions = [];
            data.table.push({
              questionTitle: true,
              title: "Questions",
              addText: "Add question",
              colSpan : data.versions.length +1,
            })
            for (var i = data.questionGroups[key].length - 1; i >= 0; i--) {
              console.log(data.questionGroups[key][i].ID);
              privData.questions = privData.questions.concat(this.collection.where({post_type: "kwps_question", post_parent : data.questionGroups[key][i].ID}));
            };
            console.log(privData.questions);
            for (var i = privData.questions.length - 1; i >= 0; i--) {
              privData.questions[i] = privData.questions[i].toJSON();
            };
            console.log(privData.questions);
            privData.questions = _.toArray(_.groupBy(privData.questions, "_kwps_sort_order"));
            console.log(privData.questions);
            for (var i = 0; i < privData.questions.length; i++) {
              data.table.push({
                versions: privData.questions[i],
                question: true,
                postType: privData.questions[i][0].post_type,
                sortOrder: privData.questions[i][0]._kwps_sort_order,
                amountOfSiblings : this.collection.where({post_type: "kwps_answer_option", post_parent : privData.questions[i][0].ID}).length
              })
              if (i == app.openAnswer.question) {
                privData.answers = [];
                data.table.push({
                  answerTitle: true,
                  title: "Questions",
                  addText: "Add question",
                  colSpan : data.versions.length +1,
                })
                for (var j = privData.questions[i].length - 1; j >= 0; j--) {
                  privData.answers = privData.answers.concat(this.collection.where({post_type: "kwps_answer_option", post_parent : privData.questions[i][j].ID}));
                };
                console.log('answers: ',privData.answers);
                for (var j = privData.answers.length - 1; j >= 0; j--) {
                  privData.answers[j] = privData.answers[j].toJSON();
                };
                console.log('answers: ',privData.answers);
                privData.answers = _.toArray(_.groupBy(privData.answers, "_kwps_sort_order"));
                console.log('answers: ',privData.answers);
                for (var j = 0; j < privData.answers.length; j++) {
                  data.table.push({
                    answer: true,
                    
                  })
                };
              }
            };
          }
        };
        console.log(data.table);
      };
      return data;
    },
    deleteVersion: function(event) {
      //TODO php function delete poll with(id) and all child posts + child posts of questions
      event.preventDefault();
      var versionId = $(event.target).data('version-id');
      var toDelete = this.collection.get(versionId);
      toDelete.destroy();
      this.collection.remove(toDelete);
    },
    deleteUnique: function (postType) {
      var postToDelete = this.collection.findWhere({post_type: postType});
      postToDelete.destroy();
      this.collection.remove(postToDelete);
    },
    deleteRow: function(postType, sortOrder) {
      console.log(postType);
      console.log(sortOrder);
      var postsToDelete = this.collection.where({post_type: postType, _kwps_sort_order: sortOrder.toString()});

      for (var i = 0; i < postsToDelete.length; i++) {
        postsToDelete[i].destroy();
      }
      this.collection.remove(postsToDelete);
    },
    deletePostType: function(e) {
      e.preventDefault();
      var type = $(e.currentTarget).data('type');
      var kwpsPolls = this.collection.where({post_type: 'kwps_poll'});
      var kwpsPollLen = kwpsPolls.length;
      switch (type) {
        case 'unique':
            var postType = $(e.currentTarget).data('post-type');
            this.deleteUnique(postType);
          break;
        case 'row':
            var sortOrder = $(e.currentTarget).data('sort-order');     
            var postType = $(e.currentTarget).data('post-type');
            this.deleteRow(postType, sortOrder);
          break;
        default:
          console.log('no post type was given');
      }
    },
    createNew: function (e) {
      e.preventDefault();
      var postType = $(e.currentTarget).data('post-type');
      var kwpsPolls = this.collection.where({post_type: 'kwps_version'});
      // get the id of the post parent(main version)
      var kwpsPollLen = kwpsPolls.length;
      switch (postType) {
        case 'kwps_intro':
          for(var i = 0; i < kwpsPollLen; i++) {
            this.createIntro(kwpsPolls[i].id);
          }
          break;
        case 'kwps_outro':
          for(var i = 0; i < kwpsPollLen; i++) {
            this.createOutro(kwpsPolls[i].id);
          }
          break;
        case 'kwps_question_group':
          for(var i = 0; i < kwpsPollLen; i++) {
            this.createQuestionGroup(kwpsPolls[i].id);
          }
          break;
        case 'kwps_question':
          for(var i = 0; i < kwpsPollLen; i++) {
            var parent = this.collection.findWhere({post_type: 'kwps_question_group', post_parent: kwpsPolls[i].id});
            this.createQuestion(parent.get('ID'));
          }
          break;
        case 'kwps_answer_option':
          var sortOrder = $(e.currentTarget).data('sort-order');
          for(var i = 0; i < kwpsPollLen; i++) {
            var questionGroups = this.collection.where({post_type: 'kwps_question_group', post_parent: kwpsPolls[i].id});
            for(var j = 0; j < questionGroups.length; j++) {
              var questions = this.collection.where({post_type: 'kwps_question', post_parent: questionGroups[j].id, _kwps_sort_order: sortOrder.toString()});
              for(var k = 0; k < questions.length; k++) {
                this.createAnswer(questions[k].id);
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
      app.kwpsPollsCollection.create({
        post_type: "kwps_version",
        post_status: "draft",
        post_title : "Version " + index,
        post_content : "Version " + index,
        post_parent : previousVersion.get('post_parent'),
        _kwps_sort_order : index
      },
        {
          success: function (newVersion, response, options) {
            that.createIntro(newVersion.get('ID'), false);
            that.createOutro(newVersion.get('ID'), false);
            var questionGroups = that.collection.where({post_type: 'kwps_question_group', post_parent: previousVersion.get('ID')});  

            for (var i = 0; i < questionGroups.length; i++) {
              var questionGroupOriginal = questionGroups[i];

              that.createQuestionGroup(newVersion.get('ID'), function(newQuestionGroup) {
                var questionsInGroup = that.collection.where({post_type: 'kwps_question', post_parent: questionGroupOriginal.id});  

                for (var i = 0; i < questionsInGroup.length; i++) {
                  var questionOriginal = questionsInGroup[i];

                  that.createQuestion(newQuestionGroup.get('ID'), function(newQuestion) {
                    var answersInQuestion = that.collection.where({post_type: 'kwps_answer_option', post_parent: questionOriginal.id});  

                    for (var i = 0; i < answersInQuestion.length; i++) {
                      that.createAnswer(newQuestion.get('ID'), function(newAnswer) {
                        console.log('answer created: ' + newAnswer.id);
                      });
                    }
                  });
                }
              });
            }
          }
        });
    },
    createIntro: function (post_parent, edit) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_intro",
        post_status: "draft",
        post_content : "intro ",
        post_parent : post_parent,
        _kwps_sort_order : "0"
      });
      model.save({},{
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
          if (edit) {
            app.router.navigate('edit/'+ model.id, {trigger: true});

          }
        }
      });
    },
    createOutro: function (post_parent, edit) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_outro",
        post_status: "draft",
        post_content : "outro ",
        post_parent : post_parent,
        _kwps_sort_order : 0
      });
      model.save({},{
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
          if (edit) {
            app.router.navigate('edit/'+ model.id, {trigger: true});

          }
        }
      });
    },
    createQuestionGroup: function (post_parent, cb) {
      var index = this.collection.where({post_type: 'kwps_question_group', post_parent: post_parent}).length;
      app.kwpsPollsCollection.create({
        post_type: "kwps_question_group",
        post_status: "draft",
        post_title : "Question Group " + index,
        post_parent : post_parent,
        _kwps_sort_order : index
      }, {
        success: function(model, response, options) {
          if(cb) {
            cb(model);
          }
        }
      });
    },
    createQuestion: function (post_parent, cb) {
      var index = this.collection.where({post_type: 'kwps_question', post_parent: post_parent}).length;
      app.kwpsPollsCollection.create({
        post_type: "kwps_question",
        post_status: "draft",
        post_content : "question",
        post_parent : post_parent,
        _kwps_sort_order : index
      }, {
        success: function (model, response, options) {
          if(cb) {
            cb(model);
          }        
        }
      });
    },
    createAnswer: function (post_parent, cb) {
      var index = this.collection.where({post_type: 'kwps_answer_option', post_parent: post_parent}).length;
      app.kwpsPollsCollection.create({
        post_type: "kwps_answer_option",
        post_status: "draft",
        post_content : "answer ",
        post_parent : post_parent,
        _kwps_sort_order : index,
        _kwps_answer_option_value : "value ..."
      },
        {
          success: function (model, response, options) {
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
      toggleOnRow = $(event.currentTarget).data('question-row');
      type = $(event.currentTarget).data('type');
      app.openAnswer[type] = (app.openAnswer[type] !== toggleOnRow || app.openAnswer[type] === "")? toggleOnRow:"";
      this.render();
    },
    preview: function(event) {
    },
    edit: function(event) {

      var kwpsAttribute = $(event.target).closest('div.actions').data('kwps-attribute');
      var kwpsId = $(event.target).closest('div.actions').data('kwps-id');

      if(typeof kwpsId === 'undefined') {
        new app.KwpsViewEdit({model: app.test, attribute: kwpsAttribute});
      }
    },
    changeTitle: function(event) {
      this.model.set('post_title', $(event.target).val());
    },
    updateTestCollection: function(event) {
      var mainPost = this.collection.get(GetURLParameter('id'));
      var attribute = $(event.target).attr("name");
      var value = $(event.target).val();

      if(value === "on") {
        value = 1;
      }

      mainPost.set(attribute, value);
      mainPost.save();
    },
    updatePostTitle: function(event) {
      var attribute = $(event.target).attr("name");
      var value = $(event.target).val();
      var ID = $(event.target).data('id');
      var post = this.collection.get(ID);
      post.set(attribute, value);
      post.save();
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
      'click button#update': 'updateData'
    },
    render: function() {
      var data = {
        attribute: this.options.attribute,
        label: kwps_translations[this.options.attribute],
        text: this.model.get("post_content"),
        title: this.model.get("post_title"),
        answer_option_value: this.model.get("_kwps_answer_option_value")
      };
      $(this.el).html(app.templates.edit(data));
      tinymce.remove();
      tinymce.init({
        menubar: false,
        visual: true,
        selector: "textarea",
        plugins: "code link hr paste lists table textcolor wordcount charmap",
        toolbar: ["bold italic strikethrough bullist numlist blockquote hr alignleft aligncenter alignright link unlink", 
                  "formatselect underline alignjustify forecolor backcolor paste removeformat charmap outdent indent undo redo | code"]
      });
      /* MEDIA UPLOAD */
      $('#add-media-button').on('click', function() {
        tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
        return false;
      });
      window.send_to_editor = function(html) {
        var imgUrl = $('img',html).attr('src');
        var imgTitle = url.split("/").pop();
        $('iframe').contents().find('#tinymce').append('<img class="img img-' + imgTitle + '" src="' + imgUrl + '" alt="">');
        tb_remove();
      };
    },
    updateData: function(event) {
      var type, title, content, value;
      event.preventDefault();
      tinymce.triggerSave();
      content = $(event.target).closest('form').find('textarea').val();
      type = this.model.get("post_type");

      if (type === 'kwps_question_group') {
        title = $(event.target).closest('form').find('input[name=qg-title]').val();
      } else if (type === 'kwps_answer_option') {
        value = $(event.target).closest('form').find('input[name=ao-value]').val();
        if (!value) {
          value = 'value...';
        }
      }

      this.model.save({
        "post_content": content,
        "post_title" : title,
        "_kwps_answer_option_value" : value
      });

      this.cleanup();
      window.location = '#';
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
      })
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
  app.router = new router;
  Backbone.history.start();

});
