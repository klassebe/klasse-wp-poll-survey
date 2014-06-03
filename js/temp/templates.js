this["kwps_admin_templates"] = this["kwps_admin_templates"] || {};

this["kwps_admin_templates"]["choose_testmodus"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, options, functionType="function", escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n\n            <label for=\"kwpsTestModi_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"><input type=\"radio\" value=\"";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" name=\"post_parent\" id=\"kwpsTestModi_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\"> ";
  if (helper = helpers.post_title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.post_title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</label>\n		";
  return buffer;
  }

  buffer += "<form id=\"create-new-test\">\n	<label>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Name", options) : helperMissing.call(depth0, "t", "Name", options)))
    + "</label>\n    <div class=\"form-group\">\n	<input name=\"post_title\"><span class=\"help-block hidden\"></span>\n    </div>\n    <div class=\"form-group\">\n	<ul>\n		";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.kwpsTestModi), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n	</ul>            <span class=\"help-block hidden\"></span>\n\n    </div>\n	<div>\n		<button type=\"submit\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Create", options) : helperMissing.call(depth0, "t", "Create", options)))
    + "</button>\n	</div>\n</form>\n";
  return buffer;
  });

this["kwps_admin_templates"]["control_panel"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); partials = this.merge(partials, Handlebars.partials); data = data || {};
  var buffer = "", stack1, helper, options, helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, functionType="function", self=this;


  buffer += "<div id=\"icon-tests\" class=\"icon32\"><br/>\n</div>\n<h2>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Poll & Survey Control panel", options) : helperMissing.call(depth0, "t", "Poll & Survey Control panel", options)))
    + "</h2>\n\n<div class=\"test-input\">\n    <input type=\"text\" name=\"post_title\" id=\"post_title\" value=\""
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
  
  
  return "\n            <a class=\"delete-answer-option\">\n                <span class=\"del\">\n                    <span data-code=\"f182\" class=\"dashicons dashicons-trash\"></span>\n                </span>\n            </a>\n            ";
  }

function program6(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n    <td id=\"_kwps_answer_option_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"post-title page-title column-title\">\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.editable), {hash:{},inverse:self.program(9, program9, data),fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n    </td>\n    ";
  return buffer;
  }
function program7(depth0,data) {
  
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

function program9(depth0,data) {
  
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
  buffer += "\n            ";
  if (helper = helpers.number) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.number); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\n        </div>\n    </td>\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.versions), {hash:{},inverse:self.noop,fn:self.program(6, program6, data),data:data});
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
  buffer += "\n        </div> \n    </th>\n    <th class=\"no-delete row-title\" style=\"height:20px;\">";
  if (helper = helpers.number) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.number); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</th>\n</tr>";
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
    + ": [kwps_version id=";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "]\n            </div>\n\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.isLive), {hash:{},inverse:self.program(4, program4, data),fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </th>\n    ";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n                <div>\n                    <h3>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Results", options) : helperMissing.call(depth0, "t", "Results", options)))
    + ": </h3>\n                    <ul>\n                        <li>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "View count", options) : helperMissing.call(depth0, "t", "View count", options)))
    + ": ";
  if (helper = helpers._kwps_view_count) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0._kwps_view_count); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</li>\n                        <li>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Conversion Rate", options) : helperMissing.call(depth0, "t", "Conversion Rate", options)))
    + ": ";
  if (helper = helpers.conversion_rate_percentage) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.conversion_rate_percentage); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "%</li>\n                        <li>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Total Participants", options) : helperMissing.call(depth0, "t", "Total Participants", options)))
    + ": ";
  if (helper = helpers.total_participants) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.total_participants); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</li>\n                    </ul>\n                </div>\n            ";
  return buffer;
  }

function program4(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n                <span><a href=\"#\" class=\"clear-entries\">"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Clear entries", options) : helperMissing.call(depth0, "t", "Clear entries", options)))
    + "</a></span>\n            ";
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
  buffer += "\n        #: ";
  if (helper = helpers.number) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.number); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\n        ";
  return buffer;
  }

function program13(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n        <td id=\"_kwps_intro_";
  if (helper = helpers.ID) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.ID); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"post-title page-title column-title\">\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.editable), {hash:{},inverse:self.program(16, program16, data),fn:self.program(14, program14, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        </td>\n    ";
  return buffer;
  }
function program14(depth0,data) {
  
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

function program16(depth0,data) {
  
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
  buffer += "\n        </div>\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.number), {hash:{},inverse:self.noop,fn:self.program(11, program11, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        \n    </td>\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.versions), {hash:{},inverse:self.noop,fn:self.program(13, program13, data),data:data});
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
  buffer += "\n            ";
  if (helper = helpers.number) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.number); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\n        </div>\n\n    </td>\n    ";
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
  buffer += "\n        </div>  \n    </th>\n    <th class=\"no-delete row-title\" style=\"height:20px; border-top: none;\"></th>\n</tr>";
  return buffer;
  });

this["kwps_admin_templates"]["control_test_top_row"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this, helperMissing=helpers.helperMissing;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n        <th class=\" column-title\" data-version-id=\"";
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
  buffer += "\n                <input class=\"update-version-post-title\" name=\"post_title\" value=\"";
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
  
  
  return "\n                    Live!\n                ";
  }

function program9(depth0,data) {
  
  var buffer = "", helper, options;
  buffer += "\n                    <button class=\"make-live\"><span class=\"dashicons dashicons-rss\"></span>"
    + escapeExpression((helper = helpers.t || (depth0 && depth0.t),options={hash:{},data:data},helper ? helper.call(depth0, "Make live", options) : helperMissing.call(depth0, "t", "Make live", options)))
    + "</button>\n                ";
  return buffer;
  }

  buffer += "<tr data-post-type=\"kwps_version\">\n    <th class=\"no-delete column-action\">&nbsp;</th>\n    ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.versions), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    <td class=\"column-title\" style=\"width:85px;\">\n        <div class=\"column-tab\">\n            <button class=\"add button\" data-post-type=\"kwps_version\">\n                <span data-code=\"f132\" class=\"dashicons dashicons-plus\"></span>\n            </button>\n        </div>\n    </td>\n</tr>";
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
  stack1 = helpers['if'].call(depth0, (depth0 && depth0._kwps_answer_option_value), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
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