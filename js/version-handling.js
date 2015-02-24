/**
Version Handling
- Takes care of all the dom manimulations on the Version edit page
- It parses the form in a stacked json format on add/remove and save
- Restore collapse states after page refreshes (states are stored in LocalStorage)
- Disables add and remove buttons as first level of validation
*/

jQuery(document).ready(function($) {
  var prevContent = '';

  updateUi();

  $('form').areYouSure({'addRemoveFieldsMarksDirty':true});
  $('h3.collapsables').closest('div').children('div').toggle();

  $('#version-save').click(versionSave);

  $(document).on('click','.kwps-create-item', createItem);
  $(document).on('click','.kwps-remove-item', removeItem);
  $(document).on('click','.kwps-move-down', moveDown);
  $(document).on('click','.kwps-move-up', moveUp);
  $(document).on('click','.kwps-collapse', collapse);
  $(document).on('click','.kwps-content-edit', showEditor);
  $(document).on('click','.kwps-content-editor-save', updateValues);


	var button = $('.kwps-add-result-button.outro-result-button').detach();
	$('#wp-outro-media-buttons').append(button);

	button = $('.kwps-add-result-button.intro-result-button').detach();
	$('#wp-post_content_intro_result-media-buttons').after(button);

	$('.kwps-add-result-button').on('click', function(event) {
		var buttonClass;
		if ( $(event.currentTarget).prop('class').split(' ')[2].split('-')[0] === 'intro' ) {
			buttonClass = 'intro';
		} else {
			buttonClass = 'outro';
		}
		var output ='';
		var allowedTypes;

		if(testModus.post_type === 'kwps_coll_outro') {
			allowedTypes = testModus._kwps_allowed_output_types_test_collection;
		} else {
			allowedTypes = testModus._kwps_allowed_output_types;
		}

		tb_show('', WPURLS.siteurl + '/wp-content/plugins/klasse-wp-poll-survey/classes/show-charts.php?type=image&amp;TB_iframe=true');

		var request = $.ajax({
										url: 'admin-ajax.php?action=kwps_get_result_page',
										context: document.body
									});
		request.done(function(request, status, error) {
				$('iframe').contents().find('#kwps-result-page').append(request);
		});
		request.fail(function() {
				alert(kwps_translations['Errors occurred. Please check below for more information.']);
		});

		$.each(allowedTypes, function (key, value) {
			output +=	 '<div id="' + value + '" class="media-item left"><label><h4>' + value.charAt(0).toUpperCase() + value.slice(1).split('-').join(' ') + '</h4><input type="radio" name="results" value="' + value + '"><img class="thumbnail" src="images/' + value + '.png" alt="' + value + '" height="128" width="128"></label></div>';
		});

		var selectedResult;
		// Check the iframe if the content is already loaded
		var timer = setInterval( function () {

			findInIFrame('#charts').append(output);

			findInIFrame('input:radio').hide();

			findInIFrame('input:radio').on('click', function () {
				findInIFrame('.selected').removeClass();
				$(this).next().addClass('selected');
				selectedResult = $(this).next().attr('alt');
			});

			findInIFrame('#add-result-to-editor').on('click', function () {
				if (selectedResult) {
					var textarea;
					if ( buttonClass === 'intro' ) {
						$('#post_content_intro_result_ifr').contents().find('#tinymce').append('[kwps_result result='+ selectedResult + ']');
						textarea = $('#wp-post_content_intro_result-editor-container textarea');
					} else {
						$('#outro_ifr').contents().find('#tinymce').append('[kwps_result result='+ selectedResult + ']');
						textarea = $('#wp-outro-editor-container textarea');
					}
					var newText = textarea[0].value + '[kwps_result result=' + selectedResult + ']';
					$(textarea).prop('value', newText);
					tb_remove();
				} else {
					alert('Please select a result view to import');
				}
			});

			if (findInIFrame('#charts').length > 0) {
				clearInterval(timer);
			}
		}, 100); //end timer

			return false;
	});

  var findInIFrame = function (element) {
    return $('iframe').contents().find(element);
  };


  var openCollapse = function () {
    var objectName = $('#kwps-version');
    var objectNameId = objectName.find('input[name="ID"]').attr('value');
    objectName = 'collapseStatusVersionID' + objectNameId + 'post_parent' + objectName.find('input[name="post_parent"]').attr('value');
    var objectData = JSON.parse(localStorage[objectName]||'{}');
    for ( var key in objectData ) {
      if ( objectData[key] === 'open' ) {
        collapse(false, key);
      }
    }
  };

  openCollapse();

  function createItem (event) {
    event.preventDefault();
    var divToClone = $(this).prevAll(':visible:first');
    var clonedDiv = divToClone.clone();
    clonedDiv.find("input[name='ID']").remove();
    if ( clonedDiv.hasClass('kwps-result_profile') ) {
      var min = clonedDiv.find('input[name="_kwps_min_value"]');
      var max = clonedDiv.find('input[name="_kwps_max_value"]');
      var maxValue = parseInt(max.prop('value'));
      min.prop('value', maxValue+1);
      max.prop('value', maxValue+10);
      clonedDiv.find('.kwps-result_profile_head_title').text('Result profile ' + (maxValue+1) + ' - ' + (maxValue+10));
    }
    var sortOrderInput = clonedDiv.find('input[name="_kwps_sort_order"]');
    sortOrderInput.val(parseInt(sortOrderInput.val())+1);
    clonedDiv.insertAfter(divToClone);
    $('#version-save').click();
  }

  function removeItem (event) {
    event.preventDefault();

    var divToHide = $(this).parent().closest('div');

    if( divToHide.find("input[name='ID']").val() === '' ) {
        divToHide.remove();
    } else {
        divToHide.find("input[name='post_status']").val('trash');
        divToHide.hide();
    }
    $('#version-save').click();
  }

  function moveDown(event) {
    event.preventDefault();

    var divToMove = $(this).parent().closest('div');
    var divToSwitch = divToMove.next();
    divToMove.insertAfter(divToSwitch);

    // updateUi();
    $('#version-save').click();
  }

  function moveUp(event) {
    event.preventDefault();

    var divToMove = $(this).parent().closest('div');
    var divToSwitch = divToMove.prev();
    divToMove.insertBefore(divToSwitch);

    // updateUi();
    $('#version-save').click();
  }

  function collapse(event, id) {
    var span;
    if ( event ) {
      span = this;
    } else {
      span = $('#'+id).find('span.kwps-collapse').first();
    }

    if($(span).closest('div').children('div').is(':visible')) {
      //Open > Close
      $(span).removeClass(function(i, className) {
        //dashicons-[.\S]*
        var classes = className.split(" ");
        var classesToRemove = [];

        $.each(classes, function(index, classItem) {
          if(classItem.match('^dashicons-')) {
            classesToRemove.push(classItem);
          }
        });

        return classesToRemove.join(' ');
      }).addClass('dashicons-arrow-right');

     if ( event ) {
       updateLocalStorage(span, 'closed');
     }

    } else {
      //Close > Open
      $(span).removeClass(function(i, className) {
        //dashicons-[.\S]*
        var classes = className.split(" ");
        var classesToRemove = [];

        $.each(classes, function(index, classItem) {
          if(classItem.match('^dashicons-')) {
            classesToRemove.push(classItem);
          }
        });

        return classesToRemove.join(' ');
      }).addClass('dashicons-arrow-down');

      if ( event ) {
        updateLocalStorage(span, 'open');
      }

    }
    $(span).closest('div').children('div').toggle();
  }

  var updateLocalStorage = function (target, status) {
    var objectName = $('#kwps-version');
    var objectNameId = objectName.find('input[name="ID"]').attr('value');
    if ( objectNameId ) {
      objectName = 'collapseStatusVersionID' + objectNameId + 'post_parent' + objectName.find('input[name="post_parent"]').attr('value');

      var objectData = JSON.parse(localStorage[objectName]||'{}');
      var collapseID = $(target).closest('div').attr('id');
      var collapseStatus = status;
      objectData[collapseID] = collapseStatus;
      localStorage.setItem(objectName, JSON.stringify(objectData));
    }
  };

  function showEditor(event) {
    event.preventDefault();
    var kwpsContent = $(this).closest('.kwps-content');
    kwpsContent.children('.kwps-content-view').hide();
    kwpsContent.children('.kwps-content-editor').show();
  }

  function updateValues(event) {
    event.preventDefault();

    var content,
        textarea = $(event.target).closest('div').find('textarea');

    if ( textarea.css('display') !== 'none' ) {
      content = textarea[0].value;
    } else {
      content =  $(event.target).closest('div').find('iframe').contents().find('#tinymce')[0].innerHTML;
    }

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
        var data = div.find('input.kwps-single_input, textarea.kwps-single_input');
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
          if(div.hasClass('kwps-question_groups')) {
            formData.question_groups = [];
          }
          
          if(div.hasClass('kwps-result_profiles')) {
            formData.result_profiles = [];
          }

        /**
         * Loop over question_groups
         */
        div.children('div.inside').children('div').each(function(questionGroupsI) {
          var inputData = {
            _kwps_new_sort_order: questionGroupsI
          };
          var inputs = $(this).find('input.kwps-question_group_input, textarea.kwps-question_group_input');

          /**
           * Loop over question_groups inputs
           */
          inputs.each(function() {
            var input = $(this);
            var inputName = input.attr('name');
            inputData[inputName] = input.val();
          });

          $(this).children('.inside').children('.kwps').each(function(j) {
            if(!inputData.questions) {
              inputData.questions = [];
            }

            /**
             * Loop over questions
             */
            $(this).find('.kwps-question').each(function(questionsI) {
              var questionData = {
                _kwps_new_sort_order: questionsI
              };

              /**
               * Loop over questions inputs
               */
              $(this).find('input.kwps-question_input, textarea.kwps-question_input').each(function() {
                var input = $(this);
                var inputName = input.attr('name');
                questionData[inputName] = input.val();
              });

              $(this).find('.kwps-answer_options').each(function() {
                if(!questionData.answer_options) {
                  questionData.answer_options = [];
                }

                /**
                 * Loop over answer_options
                 */
                $(this).children('.inside').find('div.kwps-answer_option').each(function(answerOptionI) {
                  var answerOptionData = {
                    _kwps_new_sort_order: answerOptionI
                  };

                  /**
                   * Loop over answer_options inputs
                   */
                  $(this).find('input.kwps-answer_input, textarea.kwps-answer_input').each(function() {
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
          
          if(div.hasClass('kwps-question_groups')) {
            formData.question_groups.push(inputData);
          }
          
          if(div.hasClass('kwps-result_profiles')) {
            formData.result_profiles.push(inputData);
          }

        });
      }
    });

    var input = $("<input>").attr('type', 'hidden').attr('name', 'formattedData').val(JSON.stringify(formData));
    $('form').append($(input));
  }

  function updateUi() {
    //Clean-up UI before adding
    $('.kwps-action').remove();
    $('.kwps-remove-item').attr('disabled', true);


    $('.kwps-question_groups').each(function(i) {
      var questionGroupsCount = $(this).find('.kwps-question_group:visible').length;

      if(questionGroupsCount > 1) {
        $(this).find('.kwps-question_group').children('h3').children('.kwps-remove-item').removeAttr('disabled');
      }


      $(this).find('.kwps-question_group:visible').each(function(questionGroupI) {
        if(questionGroupI < questionGroupsCount-1) {
          $(this).find('h3:first').append("<a href=\'\' class='kwps-move-down kwps-action button'><span class='dashicons dashicons-arrow-down'></span></a>");
        }
      });

      $(this).find('.kwps-question_group:visible').each(function(questionGroupI) {
        if(questionGroupI > 0) {
          $(this).find('h3:first').append("<a href=\'\' class='kwps-move-up kwps-action button'><span class='dashicons dashicons-arrow-up'></span></a>");
        }
      });

    });

    $('.kwps-questions').each(function() {
      var questionsCount = $(this).find('.kwps-question:visible').length;

      if(questionsCount > 1) {
        $(this).find('.kwps-question').children('h3').children('.kwps-remove-item').removeAttr('disabled');
      }

      $(this).find('.kwps-question:visible').each(function(questionI) {
        if(questionI < questionsCount-1) {
          $(this).find('h3:first').append("<a href=\'\' class='kwps-move-down kwps-action button'><span class='dashicons dashicons-arrow-down'></span></a>");
        }
      });

      $(this).find('.kwps-question:visible').each(function(questionI) {
        if(questionI > 0) {
          $(this).find('h3:first').append("<a href=\'\' class='kwps-move-up kwps-action button'><span class='dashicons dashicons-arrow-up'></span></a>");
        }
      });
    });


    $('.kwps-answer_options').each(function() {
      var answerOptionCount = $(this).find('.kwps-answer_option:visible').length;

      if(answerOptionCount > 2) {
        $(this).find('.kwps-answer_option').children('h3').children('.kwps-remove-item').removeAttr('disabled');
      }

      $(this).find('.kwps-answer_option:visible').each(function(answerOptionI) {
        if(answerOptionI < answerOptionCount-1) {
          $(this).find('h3:first').append("<a href=\'\' class='kwps-move-down kwps-action button'><span class='dashicons dashicons-arrow-down'></span></a>");
        }
      });

      $(this).find('.kwps-answer_option:visible').each(function(answerOptionI) {
        if(answerOptionI > 0) {
          $(this).find('h3:first').append("<a href=\'\' class='kwps-move-up kwps-action button'><span class='dashicons dashicons-arrow-up'></span></a>");
        }
      });
    });

    $('.kwps-result_profiles').each(function() {
      var resultProfileCount = $(this).find('.kwps-result_profile:visible');

      if(resultProfileCount.length > 2) {
        $(this).find('.kwps-result_profile').children('h3').children('.kwps-remove-item').removeAttr('disabled');
      }

      resultProfileCount.each(function (index, element) {
        var min = $(element).find('input[name="_kwps_min_value"]').prop('value');
        var max = $(element).find('input[name="_kwps_max_value"]').prop('value');
        $(element).find('.kwps-result_profile_head_title').text('Result profile ' + min + ' - ' + max);
      });

    });

    $('.kwps-create-item').attr('disabled','disabled');

    $('.kwps-create-item').each(function() {
      var name = $(this).data('kwps-max');
      var fullName = '_kwps_max_' + name;
      var max = testModus[fullName];
      var count;

      if(name === "question_groups") {
        if((max > 0 && $('.kwps-question_group').length < max) || max < 0) {
          $(this).removeAttr('disabled');
        }
      } else if(name === "questions_per_question_group") {
        count = $(this).parent().children('.kwps-question').length;
        if((max > 0 && max < count) || max < 0) {
          $(this).removeAttr('disabled');
        }
      } else if(name === "answer_options_per_question") {
        count = $(this).parent().children('.kwps-answer_option').length;
        if((max > 0 && max < count) || max < 0) {
          $(this).removeAttr('disabled');
        }
      } else if (name === "result_profiles" ) {
        $(this).removeAttr('disabled');
      }
    });
  }
});
