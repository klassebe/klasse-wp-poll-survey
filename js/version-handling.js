jQuery(document).ready(function($) {
  $('form').areYouSure({'addRemoveFieldsMarksDirty':true});

  $('#version-save').click(versionSave);

  $(document).on('click','.kwps-create-item', createItem);

  $(document).on('click','.kwps-remove-item', removeItem);


  function createItem (event) {
    event.preventDefault();
    var divToClone = $(this).prevAll(':visible:first');
    var clonedDiv = divToClone.clone();
    clonedDiv.children("input[name='ID']").val('');
    clonedDiv.insertAfter(divToClone);
    $('form').trigger('rescan.areYouSure');
  }

  function removeItem (event) {
    event.preventDefault();

    var divToHide = $(this).parent().closest('div');

    if( divToHide.children("input[name='ID']").val() === '' ) {
        divToHide.remove();
    } else {
        divToHide.children("input[name='post_status']").val('trash');
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
