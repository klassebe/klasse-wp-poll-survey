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

    $(document).on('click','.kwps-create-item', createItem);

    $(document).on('click','.kwps-remove-answer_option', removeItem);
//    $('.kwps-remove-answer-option').on('click','button.kwps-remove-answer-option', removeAnswerOptionFunction);


  function createItem (event) {
    event.preventDefault();
    var divToClone = $(this).prevAll(':visible:first');
    var clonedDiv = divToClone.clone();
    clonedDiv.insertAfter(divToClone);
    $('form').trigger('rescan.areYouSure');
  }

  function removeItem (event) {

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

      /**
       * Used for version, intro, outro
       */
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

        /**
         * Used for question_groups and nested
         */
      } else if(div.hasClass('kwps-multi')) {
        formData.question_groups = [];

        /**
         * Loop over question_groups
         */
        div.children('div').each(function(i) {
          var inputData = {};
          var inputs = $(this).children('input');

          /**
           * Loop over question_groups inputs
           */
          inputs.each(function(questionGroupsInputsI) {
            var input = $(this);
            var inputName = input.attr('name');
            var value = input.val();

            if(inputName === '_kwps_sort_order') {
              value = questionGroupsInputsI;
            }

            inputData[inputName] = value;
          });

          $(this).children('.kwps').each(function(j) {
            if(!inputData.questions) {
              inputData.questions = [];
            }

            /**
             * Loop over questions
             */
            $(this).children('div').each(function(k) {
              var questionData = {};

              /**
               * Loop over questions inputs
               */
              $(this).children('input').each(function(questionInputsI) {
                var input = $(this);
                var inputName = input.attr('name');
                var value = input.val();

                if(inputName === '_kwps_sort_order') {
                  value = questionInputsI;
                }

                questionData[inputName] = value;
              });

              $(this).children('.kwps').each(function() {
                if(!questionData.answer_options) {
                  questionData.answer_options = [];
                }

                /**
                 * Loop over answer_options
                 */
                $(this).children('div').each(function(i) {
                  var answerOptionData = {};

                  /**
                   * Loop over answer_options inputs
                   */
                  $(this).children('input').each(function(answerOptionInputsI) {
                    var input = $(this);
                    var inputName = input.attr('name');
                    var value = input.val();

                    if(inputName === '_kwps_sort_order') {
                      value = answerOptionInputsI;
                    }

                    answerOptionData[inputName] = input.val();
                  });
                  questionData.answer_options.push(answerOptionData);
                });
              });
              inputData.questions.push(questionData);
            });
          });

          formData.question_groups.push(inputData);

        });
      }
    });

    var input = $("<input>").attr('type', 'hidden').attr('name', 'formattedData').val(JSON.stringify(formData));
    $('form').append($(input));
  }
});
