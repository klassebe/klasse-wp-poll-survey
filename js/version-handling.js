jQuery(document).ready(function($) {
  var prevContent = '';

  updateUi();
  $('form').areYouSure({'addRemoveFieldsMarksDirty':true});

  $('#version-save').click(versionSave);

  $(document).on('click','.kwps-create-item', createItem);
  $(document).on('click','.kwps-remove-item', removeItem);
  $(document).on('click','.kwps-move-down', moveDown);
  $(document).on('click','.kwps-move-up', moveUp);
  $(document).on('click','.kwps-content-edit', showEditor);
  $(document).on('click','.kwps-content-editor-save', updateValues);


  function createItem (event) {
    event.preventDefault();
    var divToClone = $(this).prevAll(':visible:first');
    var clonedDiv = divToClone.clone();
    clonedDiv.children("input[name='ID']").val('');
    clonedDiv.insertAfter(divToClone);
    $('#version-save').click();
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
    $('#version-save').click();
  }

  function moveDown(event) {
    event.preventDefault();

    var divToMove = $(this).parent().closest('div');
    var divToSwitch = $(this).parent().closest('div').next();
    divToMove.insertAfter(divToSwitch);

    updateUi();
  }

  function moveUp(event) {
    event.preventDefault();

    var divToMove = $(this).parent().closest('div');
    var divToSwitch = $(this).parent().closest('div').prev();
    divToMove.insertBefore(divToSwitch);

    updateUi();
  }

  function showEditor(event) {
    event.preventDefault();
    $(this).closest('.kwps-content').children('.kwps-content-view').hide();
    $(this).closest('.kwps-content').children('.kwps-content-editor').show();
  }

  function updateValues(event) {
    event.preventDefault();

    var content = tinyMCE.activeEditor.getContent();

    if(content !== prevContent) {
      var contentItem = $(this).closest('.kwps-content');
      contentItem.parent().children('[name="post_content"]').val(content);
      contentItem.find('.kwps-content-view-content').html(content);
      contentItem.children('.kwps-content-view').show();
      contentItem.children('.kwps-content-editor').hide();
      content = prevContent;
    }
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
        var data = div.children('input, textarea');
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
        div.children('div').each(function(questionGroupsI) {
          var inputData = {
            _kwps_sort_order: questionGroupsI
          };
          var inputs = $(this).children('input, textarea');

          /**
           * Loop over question_groups inputs
           */
          inputs.each(function() {
            var input = $(this);
            var inputName = input.attr('name');
            inputData[inputName] = input.val();
          });

          $(this).children('.kwps').each(function(j) {
            if(!inputData.questions) {
              inputData.questions = [];
            }

            /**
             * Loop over questions
             */
            $(this).children('div').each(function(questionsI) {
              var questionData = {
                _kwps_sort_order: questionsI
              };

              /**
               * Loop over questions inputs
               */
              $(this).children('input, textarea').each(function() {
                var input = $(this);
                var inputName = input.attr('name');
                questionData[inputName] = input.val();
              });

              $(this).children('.kwps').each(function() {
                if(!questionData.answer_options) {
                  questionData.answer_options = [];
                }

                /**
                 * Loop over answer_options
                 */
                $(this).children('div').each(function(answerOptionI) {
                  var answerOptionData = {
                    _kwps_sort_order: answerOptionI
                  };

                  /**
                   * Loop over answer_options inputs
                   */
                  $(this).children('input, textarea').each(function() {
                    var input = $(this);
                    var inputName = input.attr('name');

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

  function updateUi() {
    //Clean-up UI before adding
    $('.kwps-action').remove();


    $('.kwps-question_groups').each(function(i) {
      var questionGroupsCount = $(this).children('.kwps-question_group:visible').length;

      if(questionGroupsCount > 1) {
        $(this).children('.kwps-question_group').children('h3').append("<button class=\"kwps-remove-item kwps-action\">remove</button>");


        $(this).children('.kwps-question_group:visible').each(function(questionGroupI) {
          if(questionGroupI < questionGroupsCount-1) {
            $(this).children('h3').append("<a href=\'\' class='kwps-move-down kwps-action'>Down</a>");
          }
        });

        $(this).children('.kwps-question_group:visible').each(function(questionGroupI) {
          if(questionGroupI > 0) {
            $(this).children('h3').append("<a href=\'\' class='kwps-move-up kwps-action'>Up</a>");
          }
        });

      }

    });

    $('.kwps-questions').each(function() {
      var questionsCount = $(this).children('.kwps-question:visible').length;

      if(questionsCount > 1) {
        $(this).children('.kwps-question').children('h3').append("<button class=\"kwps-remove-item kwps-action\">remove</button>");

        $(this).children('.kwps-question:visible').each(function(questionI) {
          if(questionI < questionsCount-1) {
            $(this).children('h3').append("<a href=\'\' class='kwps-move-down kwps-action'>Down</a>");
          }
        });

        $(this).children('.kwps-question:visible').each(function(questionI) {
          if(questionI > 0) {
            $(this).children('h3').append("<a href=\'\' class='kwps-move-up kwps-action'>Up</a>");
          }
        });
      }
    });


    $('.kwps-answer_options').each(function() {
      var answerOptionCount = $(this).children('.kwps-answer_option:visible').length;

      if(answerOptionCount > 2) {
        $(this).children('.kwps-answer_option').children('h3').append("<button class=\"kwps-remove-item kwps-action\">remove</button>");

        $(this).children('.kwps-answer_option:visible').each(function(answerOptionI) {
          if(answerOptionI < answerOptionCount-1) {
            $(this).children('h3').append("<a href=\'\' class='kwps-move-down kwps-action'>Down</a>");
          }
        });

        $(this).children('.kwps-answer_option:visible').each(function(answerOptionI) {
          if(answerOptionI > 0) {
            $(this).children('h3').append("<a href=\'\' class='kwps-move-up kwps-action'>Up</a>");
          }
        });
      }
    });
  }
});
