jQuery(document).ready(function(a){function b(b){b.preventDefault();var c=a(this).prevAll(":visible:first"),d=c.clone();d.find("input[name='ID']").remove();var e=d.find('input[name="_kwps_min_value"]'),f=d.find('input[name="_kwps_max_value"]'),g=parseInt(f.prop("value"));e.prop("value",g+1),f.prop("value",g+10),d.find(".kwps-result_profile_head_title").text("Result profile "+(g+1)+" - "+(g+10)),d.insertAfter(c),a("#version-save").click()}function c(b){b.preventDefault();var c=a(this).parent().closest("div");""===c.find("input[name='ID']").val()?c.remove():(c.find("input[name='post_status']").val("trash"),c.hide()),a("#version-save").click()}function d(b){b.preventDefault();var c=a(this).parent().closest("div"),d=c.next();c.insertAfter(d),a("#version-save").click()}function e(b){b.preventDefault();var c=a(this).parent().closest("div"),d=c.prev();c.insertBefore(d),a("#version-save").click()}function f(b,c){var d;d=b?this:a("#"+c).find("span.kwps-collapse").first(),a(d).closest("div").children("div").is(":visible")?(a(d).removeClass(function(b,c){var d=c.split(" "),e=[];return a.each(d,function(a,b){b.match("^dashicons-")&&e.push(b)}),e.join(" ")}).addClass("dashicons-arrow-right"),b&&o(d,"closed")):(a(d).removeClass(function(b,c){var d=c.split(" "),e=[];return a.each(d,function(a,b){b.match("^dashicons-")&&e.push(b)}),e.join(" ")}).addClass("dashicons-arrow-down"),b&&o(d,"open")),a(d).closest("div").children("div").toggle()}function g(b){b.preventDefault();var c=a(this).closest(".kwps-content");c.children(".kwps-content-view").hide(),c.children(".kwps-content-editor").show()}function h(b){b.preventDefault();var c,d=a(b.target).closest("div").find("textarea");if(c="none"!==d.css("display")?d[0].value:a(b.target).closest("div").find("iframe").contents().find("#tinymce")[0].innerHTML,c!==k){var e=a(this).closest(".kwps-content");e.parent().children('[name="post_content"]').val(c),e.find(".kwps-content-view-content").html(c),e.children(".kwps-content-view").show(),e.children(".kwps-content-editor").hide(),c=k}}function i(){var b={},c=a(this).parent();c.children("div.kwps").each(function(){var c=a(this);if(c.hasClass("kwps-single")){var d=c.attr("id").split("-")[1],e=c.find("input.kwps-single_input, textarea.kwps-single_input");e.each(function(){var c=a(this),e=c.attr("name");"version"===d?b[e]=c.val():(b[d]||(b[d]={}),b[d][e]=c.val())})}else c.hasClass("kwps-multi")&&(c.hasClass("kwps-question_groups")&&(b.question_groups=[]),c.hasClass("kwps-result_profiles")&&(b.result_profiles=[]),c.children("div.inside").children("div").each(function(d){var e={_kwps_new_sort_order:d},f=a(this).find("input.kwps-question_group_input, textarea.kwps-question_group_input");f.each(function(){var b=a(this),c=b.attr("name");e[c]=b.val()}),a(this).children(".inside").children(".kwps").each(function(){e.questions||(e.questions=[]),a(this).find(".kwps-question").each(function(b){var c={_kwps_new_sort_order:b};a(this).find("input.kwps-question_input, textarea.kwps-question_input").each(function(){var b=a(this),d=b.attr("name");c[d]=b.val()}),a(this).find(".kwps-answer_options").each(function(){c.answer_options||(c.answer_options=[]),a(this).children(".inside").find("div.kwps-answer_option").each(function(b){var d={_kwps_new_sort_order:b};a(this).find("input.kwps-answer_input, textarea.kwps-answer_input").each(function(){var b=a(this),c=b.attr("name");d[c]=b.val()}),c.answer_options.push(d)})}),e.questions.push(c)})}),c.hasClass("kwps-question_groups")&&b.question_groups.push(e),c.hasClass("kwps-result_profiles")&&b.result_profiles.push(e)}))});var d=a("<input>").attr("type","hidden").attr("name","formattedData").val(JSON.stringify(b));a("form").append(a(d))}function j(){a(".kwps-action").remove(),a(".kwps-remove-item").attr("disabled",!0),a(".kwps-question_groups").each(function(){var b=a(this).find(".kwps-question_group:visible").length;b>1&&a(this).find(".kwps-question_group").children("h3").children(".kwps-remove-item").removeAttr("disabled"),a(this).find(".kwps-question_group:visible").each(function(c){b-1>c&&a(this).find("h3:first").append("<a href='' class='kwps-move-down kwps-action button'><span class='dashicons dashicons-arrow-down'></span></a>")}),a(this).find(".kwps-question_group:visible").each(function(b){b>0&&a(this).find("h3:first").append("<a href='' class='kwps-move-up kwps-action button'><span class='dashicons dashicons-arrow-up'></span></a>")})}),a(".kwps-questions").each(function(){var b=a(this).find(".kwps-question:visible").length;b>1&&a(this).find(".kwps-question").children("h3").children(".kwps-remove-item").removeAttr("disabled"),a(this).find(".kwps-question:visible").each(function(c){b-1>c&&a(this).find("h3:first").append("<a href='' class='kwps-move-down kwps-action button'><span class='dashicons dashicons-arrow-down'></span></a>")}),a(this).find(".kwps-question:visible").each(function(b){b>0&&a(this).find("h3:first").append("<a href='' class='kwps-move-up kwps-action button'><span class='dashicons dashicons-arrow-up'></span></a>")})}),a(".kwps-answer_options").each(function(){var b=a(this).find(".kwps-answer_option:visible").length;b>2&&a(this).find(".kwps-answer_option").children("h3").children(".kwps-remove-item").removeAttr("disabled"),a(this).find(".kwps-answer_option:visible").each(function(c){b-1>c&&a(this).find("h3:first").append("<a href='' class='kwps-move-down kwps-action button'><span class='dashicons dashicons-arrow-down'></span></a>")}),a(this).find(".kwps-answer_option:visible").each(function(b){b>0&&a(this).find("h3:first").append("<a href='' class='kwps-move-up kwps-action button'><span class='dashicons dashicons-arrow-up'></span></a>")})}),a(".kwps-result_profiles").each(function(){var b=a(this).find(".kwps-result_profile:visible");b.length>2&&a(this).find(".kwps-result_profile").children("h3").children(".kwps-remove-item").removeAttr("disabled"),b.each(function(b,c){var d=a(c).find('input[name="_kwps_min_value"]').prop("value"),e=a(c).find('input[name="_kwps_max_value"]').prop("value");a(c).find(".kwps-result_profile_head_title").text("Result profile "+d+" - "+e)})}),a(".kwps-create-item").attr("disabled","disabled"),a(".kwps-create-item").each(function(){var b,c=a(this).data("kwps-max"),d="_kwps_max_"+c,e=testModus[d];"question_groups"===c?(e>0&&a(".kwps-question_group").length<e||0>e)&&a(this).removeAttr("disabled"):"questions_per_question_group"===c?(b=a(this).parent().children(".kwps-question").length,(e>0&&b>e||0>e)&&a(this).removeAttr("disabled")):"answer_options_per_question"===c?(b=a(this).parent().children(".kwps-answer_option").length,(e>0&&b>e||0>e)&&a(this).removeAttr("disabled")):"result_profiles"===c&&a(this).removeAttr("disabled")})}var k="";j(),a("form").areYouSure({addRemoveFieldsMarksDirty:!0}),a("h3.collapsables").closest("div").children("div").toggle(),a("#version-save").click(i),a(document).on("click",".kwps-create-item",b),a(document).on("click",".kwps-remove-item",c),a(document).on("click",".kwps-move-down",d),a(document).on("click",".kwps-move-up",e),a(document).on("click",".kwps-collapse",f),a(document).on("click",".kwps-content-edit",g),a(document).on("click",".kwps-content-editor-save",h);var l=a(".kwps-add-result-button.outro-result-button").detach();a("#wp-outro-media-buttons").append(l),l=a(".kwps-add-result-button.intro-result-button").detach(),a("#wp-post_content_intro_result-media-buttons").after(l),a(".kwps-add-result-button").on("click",function(b){var c;c="intro"===a(b.currentTarget).prop("class").split(" ")[2].split("-")[0]?"intro":"outro";var d,e="";d="kwps_coll_outro"===testModus.post_type?testModus._kwps_allowed_output_types_test_collection:testModus._kwps_allowed_output_types,tb_show("",WPURLS.siteurl+"/wp-content/plugins/klasse-wp-poll-survey/classes/show-charts.php?type=image&amp;TB_iframe=true");var f=a.ajax({url:"admin-ajax.php?action=kwps_get_result_page",context:document.body});f.done(function(b){a("iframe").contents().find("#kwps-result-page").append(b)}),f.fail(function(){alert(kwps_translations["Errors occurred. Please check below for more information."])}),a.each(d,function(a,b){e+='<div id="'+b+'" class="media-item left"><label><h4>'+b.charAt(0).toUpperCase()+b.slice(1).split("-").join(" ")+'</h4><input type="radio" name="results" value="'+b+'"><img class="thumbnail" src="images/'+b+'.png" alt="'+b+'" height="128" width="128"></label></div>'});var g,h=setInterval(function(){m("#charts").append(e),m("input:radio").hide(),m("input:radio").on("click",function(){m(".selected").removeClass(),a(this).next().addClass("selected"),g=a(this).next().attr("alt")}),m("#add-result-to-editor").on("click",function(){if(g){var b;"intro"===c?(a("#post_content_intro_result_ifr").contents().find("#tinymce").append("[kwps_result result="+g+"]"),b=a("#wp-post_content_intro_result-editor-container textarea")):(a("#outro_ifr").contents().find("#tinymce").append("[kwps_result result="+g+"]"),b=a("#wp-outro-editor-container textarea"));var d=b[0].value+"[kwps_result result="+g+"]";a(b).prop("value",d),tb_remove()}else alert("Please select a result view to import")}),m("#charts").length>0&&clearInterval(h)},100);return!1});var m=function(b){return a("iframe").contents().find(b)},n=function(){var b=a("#kwps-version");b="collapseStatusVersionID"+b.find('input[name="ID"]').attr("value")+"post_parent"+b.find('input[name="post_parent"]').attr("value");var c=JSON.parse(localStorage[b]||"{}");for(var d in c)"open"===c[d]&&f(!1,d)};n();var o=function(b,c){var d=a("#kwps-version");d="collapseStatusVersionID"+d.find('input[name="ID"]').attr("value")+"post_parent"+d.find('input[name="post_parent"]').attr("value");var e=JSON.parse(localStorage[d]||"{}"),f=a(b).closest("div").attr("id"),g=c;e[f]=g,localStorage.setItem(d,JSON.stringify(e))}}),function(a){a.fn.areYouSure=function(b){var c=a.extend({message:"You have unsaved changes!",dirtyClass:"dirty",change:null,silent:!1,addRemoveFieldsMarksDirty:!1,fieldEvents:"change keyup propertychange input",fieldSelector:":input:not(input[type=submit]):not(input[type=button])"},b),d=function(b){if(b.hasClass("ays-ignore")||b.hasClass("aysIgnore")||b.attr("data-ays-ignore")||void 0===b.attr("name"))return null;if(b.is(":disabled"))return"ays-disabled";var c,d=b.attr("type");switch(b.is("select")&&(d="select"),d){case"checkbox":case"radio":c=b.is(":checked");break;case"select":c="",b.find("option").each(function(){var b=a(this);b.is(":selected")&&(c+=b.val())});break;default:c=b.val()}return c},e=function(a){a.data("ays-orig",d(a))},f=function(b){var e=function(a){var b=a.data("ays-orig");return void 0===b?!1:d(a)!=b},f=a(this).is("form")?a(this):a(this).parents("form");if(e(a(b.target)))return void h(f,!0);if($fields=f.find(c.fieldSelector),c.addRemoveFieldsMarksDirty){var g=f.data("ays-orig-field-count");if(g!=$fields.length)return void h(f,!0)}var i=!1;$fields.each(function(){return $field=a(this),e($field)?(i=!0,!1):void 0}),h(f,i)},g=function(b){var d=b.find(c.fieldSelector);a(d).each(function(){e(a(this))}),a(d).unbind(c.fieldEvents,f),a(d).bind(c.fieldEvents,f),b.data("ays-orig-field-count",a(d).length),h(b,!1)},h=function(a,b){var d=b!=a.hasClass(c.dirtyClass);a.toggleClass(c.dirtyClass,b),d&&(c.change&&c.change.call(a,a),b&&a.trigger("dirty.areYouSure",[a]),b||a.trigger("clean.areYouSure",[a]),a.trigger("change.areYouSure",[a]))},i=function(){var b=a(this),d=b.find(c.fieldSelector);a(d).each(function(){var b=a(this);b.data("ays-orig")||(e(b),b.bind(c.fieldEvents,f))}),b.trigger("checkform.areYouSure")},j=function(){g(a(this))};return c.silent||window.aysUnloadSet||(window.aysUnloadSet=!0,a(window).bind("beforeunload",function(){if($dirtyForms=a("form").filter("."+c.dirtyClass),0!=$dirtyForms.length){if(navigator.userAgent.toLowerCase().match(/msie|chrome/)){if(window.aysHasPrompted)return;window.aysHasPrompted=!0,window.setTimeout(function(){window.aysHasPrompted=!1},900)}return c.message}})),this.each(function(){if(a(this).is("form")){var b=a(this);b.submit(function(){b.removeClass(c.dirtyClass)}),b.bind("reset",function(){h(b,!1)}),b.bind("rescan.areYouSure",i),b.bind("reinitialize.areYouSure",j),b.bind("checkform.areYouSure",f),g(b)}})}}(jQuery);