jQuery(document).ready(function($) {
  $('form').areYouSure({'addRemoveFieldsMarksDirty':true});

    $('.kwps-create-question').click(function(event) {
        var current = $(event.currentTarget);

        var questionGroupIndex = current.attr("questionGroupIndex");
        var questionIndex = current.attr("questionIndex");

        var divToClone = current.parent().children('div.kwps-question:visible').last();
        var clonedDiv = divToClone.clone();

        var kwpsSortOrderField = clonedDiv.children("input[name*='_kwps_sort_order']");

        var newQuestionIndex = parseInt(kwpsSortOrderField.attr("value")) + 1;
        newQuestionIndex = newQuestionIndex.toString();

        var clonedId = clonedDiv.attr("id");
        clonedDiv.attr("id", clonedId.slice(0, -1) + newQuestionIndex);

        var sectionTitleNumer = clonedDiv.children("h3").children("span");
        sectionTitleNumer.html(newQuestionIndex);

        var namePrefix = "question_groups[" + questionGroupIndex +
            "][questions][" + newQuestionIndex +"]";

        clonedDiv.attr("questionIndex", newQuestionIndex );

        var idField = clonedDiv.children("input[name*='ID']");
        idField.attr("value", "");
        idField.attr("name", namePrefix + "[ID]");


        kwpsSortOrderField.attr("value", newQuestionIndex);
        kwpsSortOrderField.attr("name", namePrefix + "[_kwps_sort_order]");

        var postStatusField = clonedDiv.children("input[name*='post_status']");
        postStatusField.attr("name", namePrefix + "[post_status]");

        var postContentField = clonedDiv.children("input[name*='post_content']");
        postContentField.attr("name", namePrefix + "[post_content]");

        var answerOptionsDiv = clonedDiv.children('#kwps-question-group-question-answer-options');


        clonedDiv.insertAfter('#' +divToClone.attr("id"));
      $('form').trigger('rescan.areYouSure');
    });

  $('#version-save').click(versionSave);

    $(document).on('click','.kwps-create-answer_option', createAnswerOption);

    $(document).on('click','.kwps-remove-answer_option', removeAnswerOptionFunction);
//    $('.kwps-remove-answer-option').on('click','button.kwps-remove-answer-option', removeAnswerOptionFunction);


  function createAnswerOption (event) {
    var current = jQuery(event.currentTarget);

      var questionGroupIndex = current.attr("questionGroupIndex");
      var questionIndex = current.attr("questionIndex");

      var divToClone = current.parent().children('div.kwps-answer-option:visible').last();
      var clonedDiv = divToClone.clone();

      var kwpsSortOrderField = clonedDiv.children("input[name*='_kwps_sort_order']");

      var newAnswerOptionIndex = parseInt(kwpsSortOrderField.attr("value")) + 1;
      newAnswerOptionIndex = newAnswerOptionIndex.toString();

      var clonedId = clonedDiv.attr("id");
      clonedDiv.attr("id", clonedId.slice(0, -1) + newAnswerOptionIndex);

      var sectionTitleNumer = clonedDiv.children("h3").children("span");
      sectionTitleNumer.html(newAnswerOptionIndex);

      var namePrefix = "question_groups[" + questionGroupIndex +
          "][questions][" + questionIndex +"][answer_options][" + newAnswerOptionIndex +"]";

      var idField = clonedDiv.children("input[name*='ID']");
      idField.attr("value", "");
      idField.attr("name", namePrefix + "[ID]");


      kwpsSortOrderField.attr("value", newAnswerOptionIndex);
      kwpsSortOrderField.attr("name", namePrefix + "[_kwps_sort_order]");

      var postStatusField = clonedDiv.children("input[name*='post_status']");
      postStatusField.attr("name", namePrefix + "[post_status]");

      var postContentField = clonedDiv.children("input[name*='post_content']");
      postContentField.attr("name", namePrefix + "[post_content]");

  //        var removeButton = clonedDiv.children('.kwps-remove-answer-option').last();
  //        console.log(removeButton);

      clonedDiv.insertAfter('#' +divToClone.attr("id"));
    $('form').trigger('rescan.areYouSure');
  }

  function removeAnswerOptionFunction (event) {

    var current = jQuery(event.currentTarget);

      var divToHide = current.parent().closest('div');

      var id = divToHide.children("input[name*='ID']").attr("value");

      if( id == '' ) {
          divToHide.remove();
      } else {
          var postStatusField = divToHide.children("input[name*='post_status']");
          postStatusField.attr("value", "trash");

          var questionGroupIndex = divToHide.parent().attr("questiongroupindex");
          var questionIndex = divToHide.parent().attr("questionindex");

          var kwpsSortOrderField = divToHide.children("input[name*='_kwps_sort_order']");

          divToHide.hide();
      }
    $('form').trigger('rescan.areYouSure');
  }

  function versionSave(event) {
    var formData = {};

    var form = $(this).parent();

    form.children('div.kwps').each(function(i) {
      var div = $(this);

      if(div.hasClass('kwps-single')) {
        var attribute = div.attr('id').split('-')[1];
        var data = div.children('input');
        data.each(function(j) {
          var input = $(this);
          var name = input.attr('name');

          if(attribute === 'version') {
            formData[name] = input.val();
          } else {
            if(!formData[attribute]) {
              formData[attribute] = {};
            }
            formData[attribute][name] = input.val();
          }
        });
      } else {

      }
    });

    //var inputs = form.children('#kwps-version').eq(0).children('input');
  }
});
