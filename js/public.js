(function () {
  "use strict";


  var getURLParameter = function (sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName;
    for (var i = 0, l = sURLVariables.length; i < l; i++) {
      sParameterName = sURLVariables[i].split('=');
      if (sParameterName[0] === sParam) {
        return sParameterName[1];
      }
    }
  };

 jQuery(function($) {
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

   $.fn.classList = function() {return this.attr('class').split(/\s+/);};

   $('.kwps-page').hide();

  $.fn.pollPlugin = function ( options ) {
   return this.each( function ( ) {
    var elem = $( this );

    /* GENERAL AJAX ADMIN URL WITH ACTION KEYWORD INCLUDED */
    var urlAjaxToAdmin = $('.admin-url').val() + "admin-ajax.php?action=";

    /* AVAILABLE SERVER ACTIONS */
    var actionSaveEntry = "kwps_save_entry";
    var actionGetVersionResultByEntryId = "kwps_get_result_of_version_by_entry_id";
    var actionGetVersionResult = "kwps_get_result_of_version";

    var urlGetDataForChart = urlAjaxToAdmin + actionGetVersionResult;
    var urlGetDataForChartByEntryId = urlAjaxToAdmin + actionGetVersionResultByEntryId;
    var actionGetProfileResult = "kwps_get_result_profile";

    /* VERSION ID */
    var versionId = elem.find('.kwps-version-id').val();

    /* OBJECT THAT WILL BE PASSED THROUGHOUT THE ACTION CALLS */
    var entryData = {
      ID : '',
      output_type : ''
    };
    /* OTHER SCOPE VARS */
    var selectChecked;
    var entries = [];

    // Save an entry, this works for all entries
    var saveEntry = function (entry) {
      if (selectChecked) {
        elem.find('.kwps-page').hide();

        /* FIRST AJAX CALL TO SAVE ENTRY */
        $.ajax({
          type: "POST",
          url: urlAjaxToAdmin + actionSaveEntry,
          data: JSON.stringify(entry),
          contentType: "application/json; charset=utf-8",
          dataType: "json",
          success: function(data) {
            entryData.ID = data[0].ID;
            // After entry is saved get results from DB
            getResults(entryData);
          },
          failure: function (errMsg) {
            alert(errMsg);
          }
        });
      } else {
        elem.find('.kwps-page').hide();
        $(this).closest('.kwps-question-group').show();
        alert('Please select an answer for every question!');
      }
    };

    // Used when a new entry was entered
    var getResults = function (entryData) {
      var questionGroupItems = elem.find('.kwps-question-group');
      var questionGroupLen = elem.find('.kwps-question-group').length;
      var allQuestionGroupsAreHidden = false;

      //  Only get all data when end of page is shown (outro or intro result)
      if (questionGroupItems) {
        for (var i = 0; i < questionGroupLen; i++) {
          if (questionGroupItems[i].style.cssText === 'display: block;') {
            elem.find('.kwps-outro').hide();
            allQuestionGroupsAreHidden = false;
            break;
          } else {
            allQuestionGroupsAreHidden = true;
          }
        }
        if (allQuestionGroupsAreHidden) {
          // Start doing the requests per div class and set it as output type
          $.each(elem.find('.kwps-result'), function (key, value) {
            entryData.output_type = value.classList[1];
            if (entryData.output_type.match('chart')) {
              getData(entryData, urlGetDataForChartByEntryId);
            } else {
              getRawData(entryData);
            }
          });
          elem.find('.kwps-outro').show();
        }
      }
    };

    // Used for intro result, because there is no entry given
    var getResultsByVersionId = function (versionId) {
      entryData.ID = versionId;
      // Start doing the requests per div class and set it as output type
      $.each(elem.find('.kwps-result'), function (key, value) {
        entryData.output_type = value.classList[1];
        if (entryData.output_type.match('chart')) {
          getData(entryData, urlGetDataForChart);
        } else {
          getRawData(entryData);
        }
      });
    };

    var getData = function (versionData, urlGetDataForChart) {
        $.ajax({
          type: "POST",
          url: urlGetDataForChart,
          data: JSON.stringify(versionData),
          contentType: "application/json; charset=utf-8",
          dataType: "json",
          success: function (data) {
              elem.find('.'+entryData.output_type).highcharts(data);
            },
            async: false
          });
      elem.find('.kwps-intro').hide();
    };

    var getRawData = function (entryData) {
      $.ajax({
        type: "POST",
        url: urlAjaxToAdmin + actionGetProfileResult,
        data: JSON.stringify(entryData),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (data) {
          if (data[0] && data[0].message) {
            elem.find('.'+entryData.output_type).html(data[0].message);
          } else {
            elem.find('.'+entryData.output_type).html(data.post_content);
          }
        },
        async: false
      });
        elem.find('.kwps-intro').hide();
    };

    var outputQuizRespons = function (data, answer) {
      elem.find('.kwps-result.quiz-respons').html(data.respons);
      if (answer) {
        elem.find('.kwps-result.quiz-respons').html(data.answer);
      }
    };
    // TODO: When Median scores are necessary make this function
    var outputMedianScore = function () {
      // elem.find('.kwps-result');
    };

    /* DISPLAY THE FIRST PAGE AND IF NECESSARY PERFORM RESULT CALL */
    if (elem.find('.kwps-page').length !== 0) {
      elem.find('.kwps-page').first().show();
      if (elem.find('.kwps-intro-result').length !== 0) {
        getResultsByVersionId(versionId);
      }
    } else {
      elem.html('<hr><div class="id-not-found">UNABLE TO DISPLAY TEST!<br><small>Possible causes: The test is not Live yet or an error with the ID value.</small></div>');
    }

    /* CLICK EVENTS */
    elem.find('.kwps-page').on('click', '.kwps-next' , function () {
      var that = $(this);
      var _kwps_group = getURLParameter('_kwps_group');
      // This is for the intro page, has no radio buttons but needs to go to next page
      var noRadioButtonsOnPage = that.closest('.kwps-page').find('input[type="radio"]').length === 0;

      if (noRadioButtonsOnPage) {
        that.closest('.kwps-page').hide();
        that.closest('.kwps-page').next().show();
      } else {
        // Get the name of the radio buttons that are being displayed now
        var answers = that.closest('.kwps-page').find('input[type="radio"]');
        // Look if the page has more different input radio buttons within current group
        // Put all possible answer name values in an array
        var answerNames = [];
        $.each(answers, function (key, value) {
          if (answerNames.indexOf(value.name) === -1) {
            answerNames.push(value.name);
          }
        });
        // Check if there are unselected values and compare with array
        $.each(answerNames, function (key, value) {
          selectChecked = elem.find('input[type="radio"][name=' + value + ']:checked').val();
          if (!selectChecked) {
            return !1;
          }
        });

        if (selectChecked) {
          var data = {
             "post_parent": selectChecked,
             "post_status": "draft",
             "_kwps_sort_order": 0
          };

          if(_kwps_group) {
            data._kwps_group = _kwps_group;
          }

          entries.push(data);
          saveEntry(entries);

          that.closest('.kwps-page').hide();
          that.closest('.kwps-page').next().show();
        } else {
          alert('Please select an answer per question.');
        }
      }
    });

});

};


$.fn.groupingPlugin = function ( options ) {
  var elem = $( this );

  /* GENERAL AJAX ADMIN URL WITH ACTION KEYWORD INCLUDED */
  var urlAjaxToAdmin = $('.admin-url').val() + "admin-ajax.php?action=";

  /* AVAILABLE SERVER ACTIONS */
  var actionSaveEntry = "kwps_save_result_group";

  var saveGroupName = function(data) {
    console.log(data.serializeObject());
    $.ajax({
      type: "POST",
      url: urlAjaxToAdmin + actionSaveEntry,
      data: JSON.stringify(data.serializeObject()),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      success: function(data) {
        console.log('saved', data);
      },
      failure: function (errMsg) {
        alert(errMsg);
      }
    });
  };

  /* DISPLAY THE FIRST PAGE AND IF NECESSARY PERFORM RESULT CALL */
  if (elem.find('.kwps-page').length !== 0) {
    elem.find('.kwps-page').first().show();
  } else {
    elem.html('<hr><div class="id-not-found">UNABLE TO DISPLAY TEST!<br><small>Possible causes: The test is not Live yet or an error with the ID value.</small></div>');
  }

  /* Store name and show next page */
  elem.find('.kwps-page').on('click', '.kwps-next' , function () {
    if($('#kwps-result-group').val() !== '') {
      var data = elem.find("input").not(':input[type=button], :input[type=submit], :input[type=reset]');
      saveGroupName(data);
      $(this).closest('.kwps-page').hide();
      $(this).closest('.kwps-page').next().show();
    } else {
      console.log('enter data');
    }
  });
};

$.fn.groupResultPlugin = function ( options ) {
  var elem = $(this);

  /* GENERAL AJAX ADMIN URL WITH ACTION KEYWORD INCLUDED */
  var urlAjaxToAdmin = $('.admin-url').val() + "admin-ajax.php?action=";

  /* AVAILABLE SERVER ACTIONS */
  var actionGetCollectionResult = "kwps_get_result_of_test_collection";

  var urlGetDataForChart = urlAjaxToAdmin + actionGetCollectionResult;

  var getData = function (element, urlGetDataForChart) {

    var classList = element.classList();
    var searchClass;

    $.each(classList, function(index, value) {
      if(value.indexOf("grouped") >=0) {
        searchClass = value;
      }
    });


    var requestData = {
      _kwps_result_hash: getURLParameter('_kwps_result_hash'),
      ID: getURLParameter('test_collection'),
      output_type: searchClass
    };

    $.ajax({
      type: "POST",
      url: urlGetDataForChart,
      data: JSON.stringify(requestData),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      success: function (data) {
        element.highcharts(data);
      },
      async: false
    });
    elem.find('.kwps-intro').hide();
  };

  $.each(elem.find('.kwps-result'), function (key, value) {
    if($(value).attr('class').indexOf('grouped')) {
      getData($(value), urlGetDataForChart);
    }
  });

};

$('.kwps-version').pollPlugin();
$('.kwps-test-collection').groupingPlugin();
$('.kwps-coll-outro').groupResultPlugin();



});

}());
