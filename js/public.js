(function () {
  "use strict";


  function GetURLParameter(sParam) {
   var sPageURL = window.location.search.substring(1);
   var sURLVariables = sPageURL.split('&');
   for (var i = 0; i < sURLVariables.length; i++)
   {
     var sParameterName = sURLVariables[i].split('=');
     if (sParameterName[0] === sParam)
     {
       return sParameterName[1];
     }
   }
 }

 jQuery(function($) {
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
    var selected = false;
    var entries = [];

    var saveEntry = function (entry) {
      if (selected) {
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

    var getResults = function (entryData) {
      var questGroupItems = elem.find('.kwps-question-group');
      var questGroupLen = elem.find('.kwps-question-group').length;

      //  Check if all kwps question group divs are hidden
      if (questGroupItems) {
        for (var i = 0; i < questGroupLen; i++) {
          if (questGroupItems[i].style.cssText === 'display: block;') {
            elem.find('.kwps-outro').hide();
            break;
          } else {
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
      }
    };

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

    /* CHECK WHICH PAGE NEEDS TO BE DISPLAYED */
      // Hidden input field with version id value is 1
      console.log(elem.find('.kwps-page'));
    if (elem.find('.kwps-page').length !== 0) {
      elem.find('.kwps-page').first().show();
      if (elem.find('.kwps-intro-result').length !== 0) {
        getResultsByVersionId(versionId);
      }
    } else {
      elem.html('<div class="id-not-found">ID NOT FOUND!</div>');
    }

    /* CLICK EVENTS */
    elem.find('.kwps-page').on('click', '.kwps-next' , function () {
      var that = $(this);
      var getQuestionName = that.closest('.kwps-page').find('input[type="radio"]').attr('name');
      var oneSelectCheck = elem.find('input[type="radio"][name=' + getQuestionName + ']:checked').val();
      // Check if there are other input type radio buttons within the same version and check if they are filled in as well

      var _kwps_hash = GetURLParameter('kwps_hash');
      if (oneSelectCheck) {
        var data = {
           "post_parent": oneSelectCheck,
           "post_status": "draft",
           "_kwps_sort_order": 0
        };

        if(_kwps_hash) {
          data._kwps_hash = _kwps_hash;
        }

        entries.push(data);
        selected = true;
        saveEntry(entries);
        that.closest('.kwps-page').hide();
        that.closest('.kwps-page').next().show();
      } else {
        selected = false;
        alert('Please select a value per question.');
      }
    });

});

};

$('.kwps-version').pollPlugin();
});

}());
