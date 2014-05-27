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