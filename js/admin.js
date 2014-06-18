jQuery(function ($) {
  var i;
  function GetURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (i = 0; i < sURLVariables.length; i++)
    {
      var sParameterName = sURLVariables[i].split('=');
      if (sParameterName[0] === sParam)
      {
          return sParameterName[1];
      }
    }
  }
  function stringToBoolean(string){
    switch(string.toLowerCase()){
      case "true": case "yes": case "1": return true;
      case "false": case "no": case "0": case null: return false;
      default: return Boolean(string);
    }
  }
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

  /* BACKBONE STUFF */
  var app = {};
  app.translations = kwps_translations;
  app.url = 'admin-ajax.php?action=';
  app.openRow = {
    main_kwps_outro: true,
    main_kwps_intro: true,
    main_kwps_intro_result: true,
    main_kwps_question_group: true,
    main_kwps_result_profile: true,
    kwps_question_group: -1,
    kwps_question: -1,
    kwps_result_profile: -1
  };
  app.views = {};

  app.templates = {
    controlPanel: kwps_admin_templates.control_panel,
    edit: kwps_admin_templates.edit,
    result: kwps_admin_templates.control_panel,
    question: kwps_admin_templates.table,
    newKwpsTest: kwps_admin_templates.choose_testmodus,
  };
  
  // Routing
   var router = Backbone.Router.extend({
    routes: {
      '' : 'home',
      'edit/:id' : 'edit',
      'result/:id' : 'result',
      'edit/question/:id' : 'editQuestion',
      'new/:type/:parentId' : 'new',
      'new': 'newKwpsTest',
      'delete/:id': 'deletePostType'
    },
    home : function () {
      if(!GetURLParameter('id')) {
        window.location = '#new';
        return;
      }

      if(!app.views.index) {
        if (app.kwpsCollection !== undefined) {
          app.views.index = new app.KwpsView({
            collection: app.kwpsCollection
          });
          app.views.index.initialize();
        }
      } else {
        app.views.index.render();
      }
    },
    edit :  function (id) {
      if (app.kwpsCollection !== undefined) {
        app.views.edit = new app.KwpsViewEdit({
          model : app.kwpsCollection.get(id)
        });
      }
    },
    editQuestion : function (id) {
      if (app.kwpsCollection !== undefined) {
        app.views.edit = new app.KwpsViewQuestion({
          model : app.kwpsCollection.get(id)
        });
      }
    },
    newKwpsTest : function () {
      app.kwpsCollection = new Backbone.Collection(kwpsTests,{
        model: KwpsModel
      });
      app.views.newKwpsTest = new app.KwpsViewNewKwpsTest();
    }
  });

  var KwpsModel = Backbone.Model.extend({
    action: {
      create: 'kwps_save',
      update: 'kwps_update',
      delete: 'kwps_delete'
    },

    sync: function(method, model, options) {
      options = options || {};
      options.url = app.url + model.action[method.toLowerCase()] + (model.attributes.post_type).substring(4);

      return Backbone.sync.apply(this, arguments);
    },
    destroy: function() {
      this.set('post_status', 'trash');
      this.save();
    },
    idAttribute: 'ID',
    defaults: {
      post_author: 0,
      post_date: "",
      post_title: "",
      post_status: "draft",
      post_modified: "",
      post_parent: 0,
      post_type: "",
      _kwps_sort_order: 0
    }
  });

  app.KwpsViewNewKwpsTest = Backbone.View.extend({
    el: '#kwps_test',
    initialize: function (options) {
      this.options = options || {};
      this.model = new KwpsModel();
      _.bindAll(this, 'cleanup');
      this.render();
    },
    cleanup: function() {
      this.undelegateEvents();
      $(this.el).empty();
    },
    events: {
      'submit form#create-new-test': 'createKwpsTest'
    },
    render: function() {
      $(this.el).html(app.templates.newKwpsTest({
        kwpsTestModi: kwpsTestModi
      }));
    },
    createKwpsTest : function (e) {
      e.preventDefault();

      var postData = $(e.target).serializeObject();

      postData.post_type = 'kwps_test_collection';
      postData.post_status = "draft";
      postData._kwps_logged_in_user_limit = 'free';
      postData._kwps_logged_out_user_limit = 'free';


      Backbone.Validation.bind(this, {
        valid: function (view, attr, selector) {
          var $el = view.$('[name=' + attr + ']'),
            $group = $el.closest('.form-group');

          $group.removeClass('has-error');
          $group.find('.help-block').html('').addClass('hidden');
        },
        invalid: function (view, attr, error, selector) {
          var $el = view.$('[name=' + attr + ']'),
            $group = $el.closest('td');

          $group.addClass('has-error');
          $group.find('.help-block').html(error).removeClass('hidden');
        }
      });


      this.model.validation = {
        post_title: {
          required: true,
          msg: kwps_translations['Name is required']
        },
        post_parent: {
          required: true,
          min: 1,
          msg: kwps_translations['Type is required']
        }
      };

      var that = this;
      this.model.set(postData);
      if(this.model.isValid(true)) {
        this.model.save({}, {
          wait: true,
          success: function (model) {
            app.kwpsCollection.add(model);
            for (i = 0; i < 1; i++) {
              that.createVersion(model.get('ID'), i);
            }
            var url = window.location.pathname + window.location.search + "&action=edit&id=" + model.get('ID');
            window.history.pushState(model.get('ID'), "Edit", url);
            app.router.navigate('', {trigger: true});
          }
        });
      }
    },
    createVersion: function (post_parent, index) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_version",
        post_status: "draft",
        post_title : kwps_translations.Version + " " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index
      });
      model.save({},{
        wait: true,
        success: function (model) {
          app.kwpsCollection.add(model);
          for (i = 0; i < 1; i++) {
            that.createIntroResult(model.get('ID'), i);
            that.createQuestionGroup(model.get('ID'), i);
            that.createOutro(model.get('ID'), i);
          }
        }
      });
    },
    createIntroResult: function (post_parent, index) {
      app.kwpsCollection.create({
        post_type: "kwps_intro_result",
        post_status: "draft",
        post_content : kwps_translations['Intro result'],
        post_parent : post_parent,
        _kwps_sort_order : "0"
      }, {
        wait: true,
        success: function (model, response, options) {
          app.kwpsCollection.add(model);
        }
      });
    },
    createQuestionGroup: function (post_parent, index) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_question_group",
        post_status: "draft",
        post_title : kwps_translations["Question Group"] + " " + (index+1),
        post_content : kwps_translations["Question Group"] + " " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index
      });
      model.save({},{
        success: function (model) {
          app.openRow.main_kwps_question_group = true;
          app.openRow.kwps_question_group = 0;
          app.kwpsCollection.add(model);
          for (i = 0; i < 1; i++) {
            that.createQuestion(model.get('ID'), i, model.get('post_type'));
          }
        }
      });
    },
    createQuestion: function (post_parent, index, post_type) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_question",
        post_status: "draft",
        post_content : kwps_translations.Question + " " + (index + 1),
        post_parent : post_parent,
        _kwps_sort_order : index
      });
      model.save({},{
        success: function (model) {
          app.openRow.main_kwps_question = true;
          app.openRow.kwps_question = 0;
          app.kwpsCollection.add(model);
          for (i = 0; i < 2; i++) {
            that.createAnswer(model.get('ID'), i, post_type);
          }
        }
      });
    },
    createAnswer: function (post_parent, index) {
      var model = new KwpsModel({
        post_type: "kwps_answer_option",
        post_status: "draft",
        post_content : kwps_translations["Answer Option"] + " " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index,
        _kwps_answer_option_value : 0
      });
      model.save({},{
        success: function (model) {
          app.kwpsCollection.add(model);
        }
      });
    },
    createOutro: function (post_parent) {
      var that = this;
      app.kwpsCollection.create({
        post_type: "kwps_outro",
        post_status: "draft",
        post_content : kwps_translations.Outro,
        post_parent : post_parent,
        _kwps_sort_order : "0"
      });
    }
  });

  app.KwpsView = Backbone.View.extend({
    el: '#kwps_test',
    initialize: function () {
      //_.bindAll(this, 'cleanup');
      this.validateVersion();
      this.render();
      this.listenTo(this.collection, 'add remove', this.render);
      this.listenTo(this.collection, 'sync', this.validateVersion);
      $(this.el).ajaxStart( function () {
        $('.spinner').show();
        console.log('start ajax');
      });
      $(this.el).ajaxStop( function () {
        $('.spinner').hide();
        console.log('all ajax stopped');
      });
    },
    events: {
      'click .delete-version': 'deleteVersion',
      'mouseenter td': 'showActions',
      'mouseleave td': 'hideActions',
      'mouseenter th': 'showActions',
      'mouseleave th': 'hideActions',
      'click .toggle-details': 'toggleDetails',
      'click button.add': 'createNew',
      'click span.del': 'deletePostType',
      'change #post_title': 'updateTestCollection',
      'change .update-main': 'updateTestCollection',
      'change .update-version-post-title': 'updateVersionPostTitle',
      'click .move-action:not(.disabled)': 'moveItem',
      'click .make-live': 'makeLive',
      'click .clear-entries': 'clearEntries'
    },
    cleanup: function() {
      this.undelegateEvents();
      $(this.el).empty();
    },
    render: function () {
      var data = this.prepareData();
      $(this.el).html(app.templates.controlPanel(data));
      $('#tabs').tabs();
    },
    prepareData: function() {
      var testCollection = this.collection.findWhere({post_type: "kwps_test_collection"});
      var testmodus = this.collection.findWhere({ID: testCollection.get('post_parent')});
      var y;
      //Get versions
      var versions = _.sortBy(_.invoke(this.collection.where({post_type: "kwps_version"}), 'toJSON'),'_kwps_sort_order');

      versions.forEach(function(version) {
        version.isLive = (version.post_status !== "draft");
        version.editable = !version.isLive;
        if(version.isLive && version.conversion_rate_percentage) {
          version.conversion_rate_percentage = version.conversion_rate.toPrecision(4) * 100;
        } else {
          version.conversion_rate_percentage = 0;
        }
      });

      //Get intro's
      var intros = [];
      for (i = 0; i < versions.length; i++) {
        y = this.collection.findWhere({post_type: "kwps_intro", post_parent : versions[i].ID});
        if (y === undefined) {
          break;
        }
        y.set('editable', !versions[i].isLive);
        intros[i] = y.toJSON();
      }


      //Get intro results
      var introResults = [];
      for (i = 0; i < versions.length; i++) {
        var introResult = this.collection.findWhere({post_type: "kwps_intro_result", post_parent : versions[i].ID});
        if (introResult === undefined) {
          break;
        }
        introResult.set('editable', !versions[i].isLive);
        introResults[i] = introResult.toJSON();
      }


      //Get outro's
      var outros = [];
      for (i = 0; i < versions.length; i++) {
        var outro = this.collection.findWhere({post_type: "kwps_outro", post_parent : versions[i].ID});
        if (outro === undefined) {
          break;
        }
        outro.set('editable', !versions[i].isLive);
        outros[i] = outro.toJSON();
      }


      //Get questionGroups if questionGroups are open
      var qGroups = [];
      if (app.openRow.main_kwps_question_group) {
        for (i = 0; i < versions.length; i++) {
          var qGrJson = _.invoke(this.collection.where({post_type: "kwps_question_group", post_parent : versions[i].ID}), 'toJSON');
          var sortedQuestionGroupPerVersion = _.sortBy(qGrJson, "_kwps_sort_order");
          qGroups.push(sortedQuestionGroupPerVersion);
        }
      }

      var sortedQGroups = _.groupBy(_.flatten(qGroups,true),"_kwps_sort_order");

      var allqGroups = _.invoke(this.collection.where({post_type: "kwps_question_group"}), 'toJSON');

      var sortedAllQGroups =_.groupBy(_.flatten(allqGroups,true),"_kwps_sort_order");

      //Get questions if a questiongroup is open
      var qu = [];
      if (app.openRow.kwps_question_group >= 0) {
        for (i = 0; i < versions.length; i++) {
          var questionGroupId = this.collection.findWhere({post_type: "kwps_question_group", post_parent : versions[i].ID, _kwps_sort_order: app.openRow.kwps_question_group});
          if (questionGroupId !== undefined) {
            var quJson = _.invoke(this.collection.where({post_type: "kwps_question", post_parent : questionGroupId.id}), 'toJSON');
            var sortedQuestionsPerVersion = _.sortBy(quJson, "_kwps_sort_order");
            qu.push(sortedQuestionsPerVersion);
          } else {
            app.openRow.kwps_question_group = -1;
            app.openRow.kwps_question = -1;
          }
        }
      }

      var sortedQu = _.groupBy(_.flatten(qu,true),"_kwps_sort_order");

      //Get Result Profiles
      var resultProfiles = [];
      if(_.contains(testmodus.get('_kwps_allowed_output_types'), 'result-profile')) {
        for(i = 0; i < versions.length; i++) {
          var resultProfilesJson = _.invoke(this.collection.where({post_type: "kwps_result_profile", post_parent : versions[i].ID}), 'toJSON');
          var sortedResultProfilesJsonPerVersion = _.sortBy(resultProfilesJson, "_kwps_sort_order");
          resultProfiles.push(sortedResultProfilesJsonPerVersion);
        }
      }

      var sortedResultProfiles = _.groupBy(_.flatten(resultProfiles,true),"_kwps_sort_order");

      var allResultProfiles = _.invoke(this.collection.where({post_type: "kwps_result_profile"}), 'toJSON');

      var sortedAllResultProfiles =_.groupBy(_.flatten(allResultProfiles,true),"_kwps_sort_order");

      //Get Answers if a question is open
      var ans = [];
      if (app.openRow.kwps_question >= 0) {
        for (i = 0; i < versions.length; i++) {
          var openQuestionGroup = this.collection.findWhere({post_type: "kwps_question_group", post_parent : versions[i].ID, _kwps_sort_order: app.openRow.kwps_question_group});
          var openQuestion = this.collection.findWhere({post_type: "kwps_question", post_parent : openQuestionGroup.id, _kwps_sort_order: app.openRow.kwps_question});
          var ansJson = _.invoke(this.collection.where({post_type: "kwps_answer_option", post_parent: openQuestion.id}), 'toJSON');
          var sortedAnswersPerVersion = _.sortBy(ansJson, "_kwps_sort_order");
          ans.push(sortedAnswersPerVersion);
        }
      }

      var sortedAns = _.groupBy(_.flatten(ans,true),"_kwps_sort_order");

      var data = {
        kwpsUniquenessTypes: kwpsUniquenessTypes
      };

      var mainPost = this.collection.get(GetURLParameter('id'));
      data.title = mainPost.get('post_title');      
      
      if (versions.length >1) {
        for (i = versions.length - 1; i >=1; i--) {
          versions[i].deleteVersion = true;
        }
      }
      data.versions = versions;
      data.collection = testCollection.toJSON();
      data.testmodus = testmodus.toJSON();
      
      data.table = [];

      // TITLE INTRO
      data.table.push({
        colSpan : versions.length +1,
        title: "Intro",
        postType: "kwps_intro",
        mainTitle: true,
        add: (intros.length <= 0 && !_.some(versions, function(version) {return version.isLive;})),
        hasMore: (intros.length > 0),
        addText: 'Display Intro',
        opened: app.openRow.main_kwps_intro,
        amount: intros.length/ versions.length,
        maxAmount: 1,
        description: 'This introduction is shown when someone fills out the test for the first time.'
      });

      // INTRO
      if (intros.length > 0 && intros.length === versions.length && app.openRow.main_kwps_intro) {
        data.table.push({
          sorterArrows : false,
          postType: 'kwps_intro',
          deletable : !_.some(versions, function(version) {return version.isLive;}),
          hasMore: false,
          hasAmount: false,
          editable: !_.some(versions, function(version) {return version.isLive;}),
          versions: intros,
          mainRow: true,
          sortOrder: 0
        });
      }

      // TITLE INTRO RESULT
      data.table.push({
        colSpan : versions.length +1,
        title: "Intro Result",
        postType: "kwps_intro_result",
        mainTitle: true,
        add: (introResults.length <= 0 && !_.some(versions, function(version) {return version.isLive;})),
        hasMore: (introResults.length > 0),
        addText: 'Add Intro Result',
        opened: app.openRow.main_kwps_intro_result,
        amount: introResults.length/ versions.length,
        maxAmount: 1,
        description: 'For people who have already completed the test.'
      });

      // INTRO RESULT
      if (introResults.length > 0 && introResults.length === versions.length && app.openRow.main_kwps_intro_result) {
        data.table.push({
          sorterArrows : false,
          postType: 'kwps_intro_result',
          deletable : false,
          hasMore: false,
          hasAmount: false,
          editable: !_.some(versions, function(version) {return version.isLive;}),
          versions: introResults,
          mainRow: true,
          sortOrder: 0
        });
      }

      // TITLE QUESTION GROUP
      data.table.push({
        colSpan : versions.length +1,
        title: "Question pages",
        postType: "kwps_question_group",
        mainTitle: true,
        add: (allqGroups && (testmodus.get('_kwps_max_question_groups') < 0 || testmodus.get('_kwps_max_question_groups') > _.size(sortedAllQGroups)) && !_.some(versions, function(version) {return version.isLive;})),
        hasMore: (_.size(sortedAllQGroups) > 0),
        addText: 'Add question page',
        opened: app.openRow.main_kwps_question_group,
        amount: _.size(sortedAllQGroups),
        maxAmount: (testmodus.get('_kwps_max_question_groups') > 0)? testmodus.get('_kwps_max_question_groups') : "&infin;",
        description: "Here you can combine questions into a page."
      });

      if ( _.size(sortedAllQGroups) > 0 && app.openRow.main_kwps_question_group) {
        for (var sortOrderQG in sortedQGroups) {
          sortOrderQG = parseInt(sortOrderQG);

          _.each(sortedQGroups[sortOrderQG], function(questionGroup) {
            var parentVersion = this.collection.findWhere({ID: questionGroup.post_parent});
            questionGroup.editable = (parentVersion.get('post_status') !== 'publish');
          }, this);

          // QUESTION GROUP
          data.table.push({
            first: (sortOrderQG === 0),
            last: (sortOrderQG === allqGroups.length/versions.length-1),
            sorterArrows : (allqGroups.length/ versions.length > 1),
            postType: "kwps_question_group",
            deletable: (_.some(versions, function(version) {return version.isLive;}) || _.size(sortedQGroups)<2)? false:true, 
            hasMore: true,
            hasAmount: false,
            hasOpened: (app.openRow.kwps_question_group === sortOrderQG),
            versions: sortedQGroups[sortOrderQG],
            mainRow: true,
            sortOrder: sortOrderQG,
            number: parseInt(sortOrderQG) +1
            //amountOfSiblings : this.collection.where({post_type: "kwps_question", post_parent: qGroups[0][sortOrderQG].ID}).length
          });

          if(app.openRow.kwps_question_group === sortOrderQG) {

            // TITLE QUESTION
            data.table.push({
              questionTitle: true,
              title: "Questions",
              postType: "kwps_question",
              questionGroupSortOrder : sortOrderQG,
              addText: "Add question",
              colSpan : versions.length +1,
              add: (testmodus.get('_kwps_max_questions_per_question_group') < 0 || testmodus.get('_kwps_max_questions_per_question_group') > _.size(sortedQu).toString() && !_.some(versions, function(version) {return version.isLive;})),
              amount: _.size(sortedQu),
              maxAmount: (testmodus.get('_kwps_max_questions_per_question_group') > 0)? testmodus.get('_kwps_max_questions_per_question_group') : "&infin;",
              description: "This is a question."
            });
            
            for (var sortOrderQ in sortedQu) {
              sortOrderQ = parseInt(sortOrderQ);
              _.each(sortedQu[sortOrderQ], function(question) {
                var parentQuestionGroup = this.collection.findWhere({ID: question.post_parent});
                var parentVersion = this.collection.findWhere({ID: parentQuestionGroup.get("post_parent")});
                question.editable = (parentVersion.get('post_status') !== 'publish');
              }, this);

              // QUESTION
              data.table.push({
                sorterArrows : (_.size(sortedQu) > 1),
                first: (parseInt(sortOrderQ) === 0),
                last: (parseInt(sortOrderQ) === _.size(sortedQu)-1),
                versions: sortedQu[sortOrderQ],
                question: true,
                postType: "kwps_question",
                deletable: (_.some(versions, function(version) {return version.isLive;}) || _.size(sortedQu)<2)? false:true, 
                sortOrder: sortOrderQ,
                number: parseInt(sortOrderQ) +1,
                //amountOfSiblings : this.collection.where({post_type: "kwps_answer_option", post_parent : qu[0].ID}).length,
                hasOpened: (app.openRow.kwps_question === sortOrderQ)
              });

              if (app.openRow.kwps_question >= 0 && sortOrderQ === app.openRow.kwps_question) {

                // TITLE ANSWER
                data.table.push({
                  answerTitle: true,
                  title: "Answers",
                  postType: "kwps_answer_option",
                  addText: "Add answer",
                  questionSortOrder: sortOrderQ,
                  colSpan : versions.length +1,
                  add: ((testmodus.get('_kwps_max_answer_options_per_question') < 0 || testmodus.get('_kwps_max_answer_options_per_question') > _.size(sortedAns).toString()) && !_.some(versions, function(version) {return version.isLive;})),
                  amount: _.size(sortedAns),
                  maxAmount: (testmodus.get('_kwps_max_answer_options_per_question') > 0)? testmodus.get('_kwps_max_answer_options_per_question') : "&infin;",
                  description: "This is an answer."
                });

                for (var sortOrderA in sortedAns) {
                  sortOrderA = parseInt(sortOrderA);
                  var value;

                  _.each(sortedAns[sortOrderA], function(answer) {
                    var parentQuestion = this.collection.findWhere({ID: answer.post_parent});
                    var parentQuestionGroup = this.collection.findWhere({ID: parentQuestion.get("post_parent")});
                    var parentVersion = this.collection.findWhere({ID: parentQuestionGroup.get("post_parent")});
                    answer.editable = (parentVersion.get('post_status') !== 'publish');

                    if(typeof value === 'undefined') {
                      value = answer._kwps_answer_option_value;
                    }

                  }, this);

                  // ANSWER
                  data.table.push({
                    answer: true,
                    sorterArrows : (_.size(sortedAns) > 1),
                    first: (parseInt(sortOrderA) === 0),
                    last: (parseInt(sortOrderA) === _.size(sortedAns)-1),
                    deletable: (_.some(versions, function(version) {return version.isLive;}) || _.size(sortedAns)<3)? false:true, 
                    sortOrder : sortOrderA,
                    number: parseInt(sortOrderA) +1,
                    versions : sortedAns[sortOrderA],
                    postType: 'kwps_answer_option',
                    showValue: (stringToBoolean(testmodus.get('_kwps_answer_options_require_value'))),
                    value: value
                  });
                }
              }
            }
          }
        }
      }

      // TITLE RESULT PROFILE
      if(_.contains(testmodus.get('_kwps_allowed_output_types'), 'result-profile')) {
        data.table.push({
          colSpan: versions.length + 1,
          title: 'Result Profiles',
          postType: "kwps_result_profile",
          mainTitle: true,
          add: (allResultProfiles && !_.some(versions, function (version) {
            return version.isLive;
          })),
          hasMore: (_.size(sortedAllResultProfiles) > 0),
          addText: 'Add result profile',
          opened: app.openRow.main_kwps_result_profile,
          amount: _.size(sortedAllResultProfiles),
          maxAmount: "&infin;",
          description: "This is one of the results a participant get when he finishes the test."
        });
      }

      if ( _.size(sortedAllResultProfiles) > 0 && app.openRow.main_kwps_result_profile) {
        for (var sortOrderRP in sortedResultProfiles) {

          _.each(sortedResultProfiles[sortOrderRP], function (resultProfile) {
            var parentVersion = this.collection.findWhere({ID: resultProfile.post_parent});
            resultProfile.editable = (parentVersion.get('post_status') !== 'publish');
          }, this);
          
          // RESULT PROFILE
          data.table.push({
            first: (parseInt(sortOrderRP) === 0),
            last: (parseInt(sortOrderRP) === allResultProfiles.length/ versions.length-1),
            sorterArrows : (allResultProfiles.length/ versions.length > 1),
            postType: "kwps_result_profile",
            deletable: (_.some(versions, function(version) {return version.isLive;}) || _.size(sortedResultProfiles)<2)? false:true, 
            hasMore: false,
            hasAmount: false,
            hasOpened: (app.openRow.kwps_result_profile === sortOrderRP),
            min: String(_.min(sortedResultProfiles[sortOrderRP], function (v) {return v._kwps_min_value})._kwps_min_value),
            max: String(_.max(sortedResultProfiles[sortOrderRP], function (v) {return v._kwps_max_value})._kwps_max_value),
            versions: sortedResultProfiles[sortOrderRP],
            mainRow: true,
            sortOrder: sortOrderRP
            //amountOfSiblings : this.collection.where({post_type: "kwps_question", post_parent: qGroups[0][sortOrderQG].ID}).length
          });
        }
      }

      // TITLE OUTRO
      data.table.push({
        colSpan : data.versions.length +1,
        title: "Outro",
        postType: "kwps_outro",
        mainTitle: true,
        add: (outros.length <= 0 && !_.some(versions, function(version) {return version.isLive;})),
        hasMore: (outros.length > 0),
        addText: 'Add outro',
        opened: app.openRow.main_kwps_outro,
        amount: outros.length/ versions.length,
        maxAmount: 1,
        description: "You see this page at the end of the test, it contains the participants result."
      });

      //OUTRO
      if (outros.length > 0 && outros.length === versions.length && app.openRow.main_kwps_outro) {
        data.table.push({
          sorterArrows : false,
          postType: 'kwps_outro',
          deletable : false,
          hasMore: false,
          hasAmount: false,
          versions: outros,
          mainRow: true,
          sortOrder: 0
        });
      }

      return data;
    },
    deleteVersion: function(versionId) {
      var toDelete = this.collection.get(versionId);
      toDelete.destroy();
      this.collection.remove(toDelete);
      this.render();
    },
    deleteRow: function(postType, sortOrder) {
      if(app.openRow[postType] === sortOrder) {
        app.openRow[postType] = -1;
      }

      var parentPostType = this.getParent(postType);
      var parentPostTypeSortOrder = app.openRow[parentPostType];
      var whereAttributes = {post_type: parentPostType};

      if(parentPostTypeSortOrder) {
        whereAttributes._kwps_sort_order = parentPostTypeSortOrder;
      }

      var parentPosts = this.collection.where(whereAttributes);
      var postsToMove = [];
      var postsToDelete = this.collection.where({post_type: postType, _kwps_sort_order: sortOrder});
      for (i = 0; i < postsToDelete.length; i++) {
        postsToDelete[i].destroy();
      }
      this.collection.remove(postsToDelete);

      parentPosts.forEach(function(parentPost) {
        var allRemainingPostsInParent = this.collection.where({post_type: postType, post_parent: parentPost.get('ID')});

        var newPosts = _.filter(allRemainingPostsInParent, function(post) {
          return post.get('_kwps_sort_order') > sortOrder;
        });
        postsToMove = _.union(postsToMove, newPosts);

      }, this);

      var that = this;
      postsToMove.forEach(function(postToMove) {
        var currentSortOrder = postToMove.get('_kwps_sort_order');
        var newSortOrder = currentSortOrder - 1;
        postToMove.set('_kwps_sort_order', newSortOrder);
        postToMove.save();
        that.render();
      });
    },
    deletePostType: function(e) {
      e.preventDefault();
      var postType = $(e.currentTarget).closest('tr').data('post-type');
      switch (postType) {
        case 'kwps_version':
          var versionId = $(e.currentTarget).data('version-id');
          this.deleteVersion(versionId);
          break;
        case 'kwps_answer_option':
        case 'kwps_question':
        case 'kwps_question_group':
        case 'kwps_intro':
        case 'kwps_outro':
        case 'kwps_result_profile':
          var sortOrder = $(e.currentTarget).closest('tr').data('sort-order');
          this.deleteRow(postType, sortOrder);
          break;
        default:
          console.log('no post type was given', postType);
      }
    },
    createNew: function (e) {
      e.preventDefault();
      var postType = $(e.currentTarget).closest('tr').data('post-type');
      var kwpsPolls = this.collection.where({post_type: 'kwps_version'});
      // get the id of the post parent(main version)
      var kwpsPollLen = kwpsPolls.length;
      var that = this;
      var sortOrder;
      switch (postType) {
        case 'main_kwps_intro':
        case 'kwps_intro':
          for(i = 0; i < kwpsPollLen; i++) {
            this.createIntro(kwpsPolls[i].id);
          }
          break;
        case 'main_kwps_intro_result':
        case 'kwps_intro_result':
          for(i = 0; i < kwpsPollLen; i++) {
            this.createIntroResult(kwpsPolls[i].id);
          }
          break;
        case 'main_kwps_outro':
        case 'kwps_outro':
          for(i = 0; i < kwpsPollLen; i++) {
            this.createOutro(kwpsPolls[i].id);
          }
          break;
        case 'main_kwps_question_group':
        case 'kwps_question_group':
          sortOrder = _.max(_.invoke(this.collection.where({post_type: 'kwps_question_group'}),"toJSON"), function (a) {return a._kwps_sort_order;});
          sortOrder = (sortOrder === -Infinity || sortOrder === Infinity)? 0: parseInt(sortOrder._kwps_sort_order)+1;
          for(i = 0; i < kwpsPollLen; i++) {
            this.createQuestionGroup(kwpsPolls[i].id, sortOrder, function(newQuestionGroup) {
              that.createQuestion(newQuestionGroup.get('ID'), 0, function(newQuestion) {
                for (i = 0; i < 2; i++) {
                  that.createAnswer(newQuestion.get('ID'), i, function(newAnswer) {
                    console.log('answer created: ' + newAnswer.id);
                  });
                }
              });
            });
          }
          break;
        case 'main_kwps_result_profile':
        case 'kwps_result_profile':
          sortOrder = _.max(_.invoke(this.collection.where({post_type: 'kwps_result_profile'}),"toJSON"), function (a) {return a._kwps_sort_order;});
          sortOrder = (sortOrder === -Infinity || sortOrder === Infinity)? 0: parseInt(sortOrder._kwps_sort_order)+1;
          for(i = 0; i < kwpsPollLen; i++) {
            this.createResultProfile(kwpsPolls[i].id, sortOrder);
          }
          break;
        case 'kwps_question':
          var sortOrderOfQuestionGroup = $(e.currentTarget).closest('tr').data('sort-order');
          var versionsOfOpenedQuestionGroup = this.collection.where({post_type: 'kwps_question_group', _kwps_sort_order: sortOrderOfQuestionGroup});
          var highestSortOrder = 0;
          for (i = versionsOfOpenedQuestionGroup.length - 1; i >= 0; i--) {
            var connectedQuestionsToOpenedQuestionGroup = _.invoke(this.collection.where({post_type: "kwps_question", post_parent: versionsOfOpenedQuestionGroup[i].id}), 'toJSON');
            highestSortOrder = Math.max (parseInt(_.max( connectedQuestionsToOpenedQuestionGroup ,function (model) {return parseInt(model._kwps_sort_order);})._kwps_sort_order),highestSortOrder);
          }
          highestSortOrder = (isNaN(highestSortOrder))? 0:highestSortOrder;
          for (i = versionsOfOpenedQuestionGroup.length - 1; i >= 0; i--) {
            this.createQuestion(versionsOfOpenedQuestionGroup[i].id , highestSortOrder +1, function (newQuestion) {
              for (i = 0; i < 2; i++) {
                that.createAnswer(newQuestion.get('ID'), i);
              }
            });
          }
          break;
        case 'kwps_answer_option':
          sortOrder = $(e.currentTarget).closest('tr').data('sort-order');
          for(i = 0; i < kwpsPollLen; i++) {
            var questionGroups = this.collection.where({post_type: 'kwps_question_group', post_parent: kwpsPolls[i].id, _kwps_sort_order: app.openRow.kwps_question_group});
            for(var j = 0; j < questionGroups.length; j++) {
              var questions = this.collection.where({post_type: 'kwps_question', post_parent: questionGroups[j].id, _kwps_sort_order: sortOrder});
              for(var k = 0; k < questions.length; k++) {
                var index = this.collection.where({post_type: 'kwps_answer_option', post_parent: questions[k].id}).length;
                this.createAnswer(questions[k].id, index);
              }
            }
          }
          break;
        case 'kwps_version':
          this.createVersion(kwpsPolls[kwpsPollLen-1], kwpsPollLen);
          break;
        default:
          console.log('no post type was given');
      }
    },
    createVersion: function (previousVersion, index) {
      var that = this;
      var versionData = {
        post_type: "kwps_version",
        post_status: "draft",
        post_title : kwps_translations['Copy of'] + " " + previousVersion.get('post_title'),
        post_parent : previousVersion.get('post_parent'),
        _kwps_sort_order : index
      };

      app.kwpsCollection.create(
        versionData,
        {
          success: function(newVersion, response, options) {
            that.createIntro(newVersion.get('ID'), that.getExistingObject(previousVersion.get('ID'), 'kwps_intro', true));
            that.createIntroResult(newVersion.get('ID'), that.getExistingObject(previousVersion.get('ID'), 'kwps_intro_result', true));
            that.createOutro(newVersion.get('ID'), that.getExistingObject(previousVersion.get('ID'), 'kwps_outro', true));

            var resultProfiles = that.getExistingObject(previousVersion.get('ID'), 'kwps_result_profile', false);
            for (i = 0; i < resultProfiles.length; i++) {
              var resultProfilesOriginal = resultProfiles[i];
              that.createResultProfile(newVersion.get('ID'), resultProfilesOriginal);
            }

            var questionGroups = that.getExistingObject(previousVersion.get('ID'), 'kwps_question_group', false);
            for (i = 0; i < questionGroups.length; i++) {
              var questionGroupOriginal = questionGroups[i];
              that.createQuestionGroup(newVersion.get('ID'), questionGroupOriginal, function(newQuestionGroup) {
                var questionsInGroup = that.getExistingObject(questionGroupOriginal.get('ID'), 'kwps_question', false);

                for (i = 0; i < questionsInGroup.length; i++) {
                  var questionOriginal = questionsInGroup[i];

                  that.createQuestion(newQuestionGroup.get('ID'), questionOriginal, function(newQuestion) {
                    var answersInQuestion = that.getExistingObject(questionOriginal.get('ID'), 'kwps_answer_option', false);

                    for (i = 0; i < answersInQuestion.length; i++) {
                      var answersInQuestionOriginal = answersInQuestion[i];
                      that.createAnswer(newQuestion.get('ID'), answersInQuestionOriginal, function(newAnswer) {
                        console.log('answer created: ' + newAnswer.id);
                      });
                    }
                  });
                }
              });
            }

          }
        }
      );
    },
    createIntro: function (post_parent, data) {
      if(typeof data === 'boolean') {
        return;
      }

      var that = this,
        introData = {
          post_type: "kwps_intro",
          post_status: "draft",
          post_content : kwps_translations.Intro,
          post_parent : post_parent,
          _kwps_sort_order : 0
        };

      if(data) {
        introData.post_content = data.get('post_content');
      }

      this.collection.create(
        introData,
        {
          wait: true
        }
      );
    },
    createIntroResult: function (post_parent, data) {
      if(typeof data === 'boolean') {
        return;
      }

      var that = this,
        introResultData = {
          post_type: "kwps_intro_result",
          post_status: "draft",
          post_content : kwps_translations['Intro result'],
          post_parent : post_parent,
          _kwps_sort_order : 0
        };

      if(data) {
        introResultData.post_content = data.get('post_content');
      }

      this.collection.create(
        introResultData,
        {
          wait: true
        }
      );
    },
    createOutro: function (post_parent, data) {
      if(typeof data === 'boolean') {
        return;
      }

      var that = this,
        outroData = {
          post_type: "kwps_outro",
          post_status: "draft",
          post_content : kwps_translations.Outro,
          post_parent : post_parent,
          _kwps_sort_order : 0
        };

      if(data) {
        outroData.post_content = data.get('post_content');
      }

      this.collection.create(
        outroData,
        {
          wait: true
        }
      );
    },
    createResultProfile: function (post_parent, data, cb) {
      var resultProfileData = {};
      console.log(data);
      /* Check which data you are getting and link with the correct menu order so there is no overlapping */
      resultProfileData._kwps_min_value = (data.attributes._kwps_min_value)? data.attributes._kwps_min_value : 0;
      resultProfileData._kwps_max_value = (data.attributes._kwps_max_value)? data.attributes._kwps_max_value : 0;
      
      resultProfileData = {
        post_type: "kwps_result_profile",
        post_status: "draft",
        post_title : "",
        post_content : "",
        post_parent : post_parent,
        _kwps_sort_order : 0,
        _kwps_min_value: 0,
        _kwps_max_value: 0
        // _kwps_min_value: data.attributes._kwps_min_value || 0,
        // _kwps_max_value: data.attributes._kwps_max_value || 0
      };

      if(typeof data === 'object') {
        resultProfileData.post_title = data.get('post_title');
        resultProfileData.post_content = data.get('post_content');
        resultProfileData._kwps_sort_order = data.get('_kwps_sort_order');
      } else {
        resultProfileData._kwps_sort_order = data;
        resultProfileData.post_title = kwps_translations["Result profile"] + " " + (data + 1);
        resultProfileData.post_content = kwps_translations["Result profile"] + " " + (data + 1);
      }

      this.collection.create(resultProfileData, {
        wait: true,
        success: function(model, response, options) {
          if(cb) {
            cb(model);
          }
        }
      });
    },
    createQuestionGroup: function (post_parent, data, cb) {
      var questionGroupData = {
        post_type: "kwps_question_group",
        post_status: "draft",
        post_title : "",
        post_content : "",
        post_parent : post_parent,
        _kwps_sort_order : 0,
        _kwps_min_value: 0,
        _kwps_max_value: 0
      };

      if(typeof data === 'object') {
        questionGroupData.post_title = data.get('post_title');
        questionGroupData.post_content = data.get('post_content');
        questionGroupData._kwps_sort_order = data.get('_kwps_sort_order');
      } else {
        questionGroupData._kwps_sort_order = data;
        questionGroupData.post_title = kwps_translations["Question Group"] + " " + (data + 1);
        questionGroupData.post_content = kwps_translations["Question Group"] + " " + (data + 1);
      }

      this.collection.create(questionGroupData, {
        wait: true,
        success: function(model, response, options) {
          if(cb) {
            cb(model);
          }
        }
      });
    },
    createQuestion: function (post_parent, data, cb) {
      var questionData = {
        post_type: "kwps_question",
        post_status: "draft",
        post_title : "",
        post_content : "",
        post_parent : post_parent,
        _kwps_sort_order : 0,
        _kwps_min_value: 0,
        _kwps_max_value: 0
      };

      if(typeof data === 'object') {
        questionData.post_title = data.get('post_title');
        questionData.post_content = data.get('post_content');
        questionData._kwps_sort_order = data.get('_kwps_sort_order');
      } else {
        questionData._kwps_sort_order = data;
        questionData.post_title = kwps_translations.Question + " " + (data + 1);
        questionData.post_content = kwps_translations.Question + " " + (data + 1);
      }

      this.collection.create(questionData, {
        wait: true,
        success: function(model, response, options) {
          if(cb) {
            cb(model);
          }
        }
      });
    },
    createAnswer: function (post_parent, data, cb) {
      var answerData = {
        post_type: "kwps_answer_option",
        post_status: "draft",
        post_title : "",
        post_content : "",
        post_parent : post_parent,
        _kwps_sort_order : 0,
        _kwps_min_value: 0,
        _kwps_max_value: 0,
        _kwps_answer_option_value: 0
      };

      if(typeof data === 'object') {
        answerData.post_title = data.get('post_title');
        answerData.post_content = data.get('post_content');
        answerData._kwps_sort_order = data.get('_kwps_sort_order');
      } else {
        answerData._kwps_sort_order = data;
        answerData.post_title = kwps_translations["Answer Option"] + " " + (data + 1);
        answerData.post_content = kwps_translations["Answer Option"] + " " + (data + 1);
      }

      this.collection.create(answerData, {
        wait: true,
        success: function(model, response, options) {
          if(cb) {
            cb(model);
          }
        }
      });
    },
    showActions: function(event) {
      $(event.target).find(".actions").show();
    },
    hideActions: function(event) {
      $(event.target).find(".actions").hide();
    },
    toggleDetails: function(event) {
      var postType = $(event.currentTarget).closest('tr').data('post-type');
      var sortOrder = $(event.currentTarget).closest('tr').data('sort-order');
      switch (postType) {
        case "main_kwps_intro" :
        case "main_kwps_intro_result" :
        case "main_kwps_outro" :
        case "main_kwps_question_group" :
          app.openRow[postType] = !app.openRow[postType];
          break;
        case "kwps_question" :
          app.openRow[postType] = (app.openRow[postType] === sortOrder)? -1 : sortOrder;
          break;
        case "kwps_question_group" :
          if (app.openRow.kwps_question_group === sortOrder) {
            app.openRow.kwps_question = -1;
            app.openRow.kwps_question_group = -1;
          } else {
            app.openRow.kwps_question_group = sortOrder;
          }
          break;
        default:
          console.log('no post type was given', postType);
      }
      this.render();
    },
    preview: function(event) {
    },
    edit: function(event) {

      var kwpsAttribute = $(event.target).closest('div.actions').data('kwps-attribute');
      var kwpsId = $(event.target).closest('div.actions').data('kwps-id');

      if(typeof kwpsId === 'undefined') {
        var editView = new app.KwpsViewEdit({model: app.test, attribute: kwpsAttribute});
      }
    },
    updateTestCollection: function(event) {
      var testCollection = this.collection.findWhere({post_type: "kwps_test_collection"});
      var attribute = $(event.target).attr("name");
      var value = $(event.target).val();

      if(value === "on") {
        value = 1;
      }
      testCollection.set(attribute, value);
      testCollection.save();
    },
    updateVersionPostTitle: function(event) {
      var attribute = $(event.target).attr("name");
      var value = $(event.target).val();
      var versionId = $(event.currentTarget).closest('th').data('version-id');
      var version = this.collection.get(versionId);
      version.set(attribute, value);
      version.save();
    },
    moveItem: function(event) {
      var currentSortOrder = $(event.currentTarget).closest('tr').data('sort-order');
      var newSortOrder;
      if($(event.currentTarget).hasClass('up')) {
        newSortOrder = currentSortOrder-1;
      } else {
        newSortOrder = currentSortOrder+1;
      }

      var postType = $(event.currentTarget).closest('tr').data('post-type');
      var toMove = [],
        toCorrect = [];
      if(postType === 'kwps_question' || postType === 'kwps_answer_option') {

        var parentPostType = this.getParent(postType);
        var parentPostTypeSortOrder = app.openRow[parentPostType];
        var parentPosts = this.collection.where({post_type: parentPostType, _kwps_sort_order: parentPostTypeSortOrder});

        parentPosts.forEach(function(parentPost) {
          toMove = _.union(toMove, this.collection.where({post_type: postType, _kwps_sort_order: currentSortOrder, post_parent: parentPost.get('ID')}));
          toCorrect = _.union(toCorrect, this.collection.where({post_type: postType, _kwps_sort_order: newSortOrder, post_parent: parentPost.get('ID')}));
        }, this);

      } else {
        toMove = this.collection.where({post_type: postType, _kwps_sort_order: currentSortOrder});
        toCorrect = this.collection.where({post_type: postType, _kwps_sort_order: newSortOrder});
      }


      toMove.forEach(function(post) {
        post.set('_kwps_sort_order', newSortOrder);
        post.save();
      });

      toCorrect.forEach(function(post) {
        post.set('_kwps_sort_order', currentSortOrder);
        post.save();
      });

      this.render();
    },
    getParent: function(postType) {
      var parentPostType;
      switch (postType) {
        case 'kwps_version':
          parentPostType = 'kwps_collection';
          break;
        case 'kwps_question_group':
        case 'kwps_intro':
        case 'kwps_outro':
        case 'kwps_result_profile':
          parentPostType = 'kwps_version';
          break;
        case 'kwps_question':
          parentPostType = 'kwps_question_group';
          break;
        case 'kwps_answer_option':
          parentPostType = 'kwps_question';
          break;
        default:
          console.log('no post type was given', postType);
      }

      return parentPostType;
    },
    makeLive: function(event) {
      event.preventDefault();
      var versionId = $(event.currentTarget).closest('th').data('version-id');
      var version = this.collection.findWhere({ID: versionId});
      var that = this;
      $.ajax({
        type: 'POST',
        data: JSON.stringify(version.toJSON()),
        url: app.url + 'kwps_validate_version',
        contentType: "application/json; charset=utf-8",
        dataType: 'json'
      })
        .done(function(request, status, error) {
          version.set('post_status', 'publish');
          version.save({
            wait: true,
            error: function(version, resp, options)  {
              console.log(resp);
            },
            success: function() {
              that.render();
            }
          });
        })
        .fail(function() {
          alert(kwps_translations['Errors occurred. Please check below for more information.']);
        });


    },
    clearEntries: function(event) {
      event.preventDefault();
      if(confirm(kwps_translations['This will delete all entries. Are you sure?'])) {
        var id = $(event.currentTarget).closest('th').data('post-id');
        $.post(
          app.url + 'kwps_delete_entries_from_version',JSON.stringify(
          {
            post_parent: id
          }),
          function(data) {
            console.log('deleted: ' + data.count);
          }
        );
      }
    },
    getExistingObject: function(post_parent, post_type, single) {
      if(typeof single === 'undefined') {
        single = true;
      }
      var query = {post_type: post_type, post_parent: post_parent},
        result;

      if(single) {
        result = this.collection.findWhere(query);
      } else {
        result = this.collection.where(query);
      }

      return (typeof result === 'undefined')? false : result;
    },
    validateVersion: function(event) {
      var versions = this.collection.where({post_type: 'kwps_version'}),
        that = this;
      _.each(versions, function(version) {
        $.ajax({
          type: 'POST',
          data: JSON.stringify(version.toJSON()),
          url: app.url + 'kwps_validate_version',
          contentType: "application/json; charset=utf-8",
          dataType: 'json'
        })
          .done(function(response, status, xhr) {
            version.set('validation', response);
            that.render();
          });
      }, this);
    }
  });

  app.KwpsViewResult = Backbone.View.extend({
    el: '#tabs-results',
    initialize: function() {
      this.render();
    },
    render: function() {
      $(this.el).html(app.templates.result(this.model));
    }
  });

  app.KwpsViewEdit = Backbone.View.extend({
    el: '#kwps_test',

    initialize: function (options) {
      this.options = options || {};
      _.bindAll(this, 'cleanup');
      this.render();

    },
    cleanup: function() {
      this.undelegateEvents();
      $(this.el).empty();
    },
    events: {
      'click button#update': 'updateData',
      'click a#add-media-button': 'addMedia',
      'click a#add-result-button': 'addResult',
      'click td.savesend input.button': 'insertIntoEditor',
      'click button#add-result-to-editor': 'insertChartIntoEditor'
    },
    render: function() {
      var testCollection = app.kwpsCollection.findWhere({post_type: "kwps_test_collection"});
      var testmodus = app.kwpsCollection.findWhere({ID: testCollection.get('post_parent')});

      var data =  this.model.toJSON();
      data.attribute = this.options.attribute;
      data.label = this.model.get('post_type');
      data.addResults = (this.model.get('post_type') === "kwps_outro" || this.model.get('post_type') === "kwps_intro_result");
      data.min_max = (this.model.get('post_type') === 'kwps_result_profile' && _.contains(testmodus.get('_kwps_allowed_output_types'), 'result-profile'));
      data.title = (this.model.get('post_type') === 'kwps_result_profile');
      data.showValue = (testmodus.get('_kwps_answer_options_require_value') && this.model.get('post_type') === 'kwps_answer_option');
      data._kwps_answer_option_value = this.model.get("_kwps_answer_option_value");
      data.parentStack = this.getParentStack();

      $(this.el).html(app.templates.edit(data));
      tinymce.remove();
      tinymce.init({
        menubar: false,
        visual: true,
        statusbar: false,
        relative_urls: false,
        selector: "textarea",
        plugins: "code link hr paste lists table textcolor wordcount charmap image code",
        toolbar: ["bold italic strikethrough bullist numlist blockquote hr alignleft aligncenter alignright link unlink", 
                  "formatselect underline alignjustify forecolor backcolor paste removeformat charmap outdent indent undo redo | code"]
      });
    },
    /* BEGIN RESULT INPUT */
    addResult: function () {
      var testCollection = app.kwpsCollection.findWhere({post_type: "kwps_test_collection"});
      var testmodus = app.kwpsCollection.findWhere({ID: testCollection.get('post_parent')});
      var data = this.model.toJSON();
      var output ='';
      var allowedTypes = testmodus.attributes._kwps_allowed_output_types;

      tb_show('','../wp-content/plugins/klasse-wp-poll-survey/includes/show_charts.php?type=image&amp;TB_iframe=true');

        $.ajax({
            url: app.url + 'kwps_get_result_page',
            context: document.body
        })
            .done(function(request, status, error) {
                $('iframe').contents().find('#kwps-result-page').append(request);
            })
            .fail(function() {
                alert(kwps_translations['Errors occurred. Please check below for more information.']);
            });

      $.each(allowedTypes, function (key, value) {
        output +=   '<div id="' + value + '" class="media-item left"><label><h4>' + value.charAt(0).toUpperCase() + value.slice(1).split('-').join(' ') + '</h4><input type="radio" name="results" value="' + value + '"><img class="thumbnail" src="images/' + value + '.png" alt="' + value + '" height="128" width="128"></label></div>';
      });

      var selectedResult;
      var timer = setInterval( function () {

        $('iframe').contents().find('#charts').append(output);

        $('iframe').contents().find('input:radio').on('click', function () {
            selectedResult = $(this).next().attr('alt');
        });
        $('iframe').contents().find('#add-result-to-editor').on('click', function () {
          if (selectedResult) {
            $('iframe').contents().find('#tinymce').append('[kwps_result result='+ selectedResult + ']');
            tb_remove();
          } else {
            alert('Please select a result view to import');
          }
        });
        if ($('iframe').contents().find('#charts').length > 0) {
          clearInterval(timer);
        }
      }, 100);

      return false;
    },
    insertChartIntoEditor: function (html) {
      $('iframe', window.parent.document).contents().find('#tinymce').append('<div class="kwps-chart">Hello</div>');
      self.parent.tb_remove();
    },
    /* END RESULT INPUT */

    /* BEGIN MEDIA UPLOAD */
    addMedia: function () {
      tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
      return false;
    },
    insertIntoEditor: function (html) {
      var imgUrl = $('img',html).attr('src');
      var imgTitle = imgUrl.split("/").pop();
      $('iframe').contents().find('#tinymce').append('<img class="img img-' + imgTitle + '" src="' + imgUrl + '" alt="">');
      tb_remove();
    },
    /* END MEDIA UPLOAD */
    updateData: function(event) {
      event.preventDefault();
      tinymce.triggerSave();

      var data = $('#update-model').serializeObject();

      if(data._kwps_min_value) {
        data._kwps_min_value = parseInt(data._kwps_min_value);
      }
      if(data._kwps_max_value) {
        data._kwps_max_value = parseInt(data._kwps_max_value);
      }

      this.model.set(data);

      var that = this;
      this.model.save(data, {
        success: function() {
          that.cleanup();
          window.location = '#';
        },
        error: function(model, response) {
          var errors = response.responseJSON;

          model.set('success', errors.success);
          _.each(errors.data, function(error) {
            model.set('error_' + error.field, error.message);
          });
          that.render();
        }
      });
    },
    getParentStack: function(){
      var parentStack= {};
      var getParent = function (id) {
        var parent = app.kwpsCollection.get(id);
        if (parent !== undefined) {
          parent = parent.toJSON();
          parentStack[parent.post_type] = parent;
          if (parent.post_type !== 'kwps_version') {
            getParent(parent.post_parent);
          } 
        }
      };
      var parentId = this.model.get('post_parent');
      getParent(parentId);

      return parentStack;
    }
  });

  app.KwpsViewQuestion = Backbone.View.extend({
    el: '#kwps_test',
    initialize: function (options) {
      this.options = options || {};
      
      _.bindAll(this, 'cleanup');
      this.render();
    },
    cleanup: function() {
      this.undelegateEvents();
      $(this.el).empty();
    },
    events: {
      'click button#update': 'updateData'
    },
    render: function() {
      var answers = app.kwpsCollection.where({post_type : "kwps_answer_option", post_parent : this.model.id});
      answers = _.each(answers, function (answer){
          return answer.toJSON();
      });
      var data = {
        question: this.model.toJSON(),
        answers: answers
      };
      $(this.el).html(app.templates.question(data));
      tinymce.remove();
    },
    updateData: function(event) {
      event.preventDefault();
      var value = $(event.target).closest('form').find('textarea').val();

      this.model.save("post_content", value);

      this.cleanup();
      // app.views.index.render();
      window.location = '#';
    }
  });

  if (typeof kwpsTests !== 'undefined') {
    kwpsTests.push(kwpsTestModi[0]);
    app.kwpsCollection = new Backbone.Collection(kwpsTests, {
      model: KwpsModel
    });
  }
  app.router = new router();
  Backbone.history.start();

});
