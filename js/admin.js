jQuery(function ($) {

  $('#tabs').tabs();

  function GetURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
      var sParameterName = sURLVariables[i].split('=');
      if (sParameterName[0] == sParam) 
      {
          return sParameterName[1];
      }
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
  app.url = 'admin-ajax.php?action=';
  app.openRow = {
    main_kwps_outro: false,
    main_kwps_intro: false,
    main_kwps_question_group: false,
    kwps_question_group: -1,
    kwps_question: -1
  };
  app.views = {};

  app.templates = {
    controlPanel: kwps_admin_templates.control_panel,
    edit: kwps_admin_templates.edit,
    question: kwps_admin_templates.table,
    newKwpsTest: kwps_admin_templates.choose_testmodus
  };
  
  // Routing
   var router = Backbone.Router.extend({
    routes: {
      '' : 'home',
      'edit/:id' : 'edit',
      'edit/question/:id' : 'editQuestion',
      'new/:type/:parentId' : 'new',
      'new': 'newKwpsTest',
      'delete/:id': 'deletePostType'
    },
    home : function () {
      // console.log("ROUTING TO: home")
      if (app.kwpsPollsCollection !== undefined) {
        app.views.index = new app.KwpsView({
          collection: app.kwpsPollsCollection
        });
        app.views.index.initialize();
      } 
    },
    edit :  function (id) {
      // console.log("ROUTING TO: edit");
      // controleren of er nog een edit view in steekt en alle events unbinden
      if (app.kwpsPollsCollection !== undefined) {
        app.views.edit = new app.KwpsViewEdit({
          model : app.kwpsPollsCollection.get(id)
        });
      }
    },
    editQuestion : function (id) {
      // console.log("ROUTING TO: editQuestion");
      if (app.kwpsPollsCollection !== undefined) {
        app.views.edit = new app.KwpsViewQuestion({
          model : app.kwpsPollsCollection.get(id)
        })
      }
    },
    newKwpsTest : function () {
      // toon keuze options tussen verschillende testmodi
      // Nu vooral één knop met de keuze poll en invulveld
      // indien opties worden de opties getoond en kan men test aanmaken
      //console.log("ROUTING TO: newKwpsTest");

      app.kwpsPollsCollection = new Backbone.Collection(kwpsTests,{
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

      if(!postData.post_title || !postData.post_parent) {
        alert('Please fill in all fields');
        return;
      }

      postData.post_type = 'kwps_test_collection';
      postData.post_status = "draft";
      postData._kwps_logged_in_user_limit = 'free';
      postData._kwps_logged_out_user_limit = 'free';

      var that = this;
      var model = new KwpsModel(postData);
      model.save({},{
        wait: true,
        success: function (model) {
          app.kwpsPollsCollection.add(model);
          for (var i = 0; i < 1; i++) {
            that.createVersion(model.get('ID'), i);
          }
          var url = window.location.pathname + window.location.search + "\&action=edit\&id=" + model.get('ID');
          window.history.pushState( model.get('ID') , "Edit" , url);
          app.router.navigate('', {trigger: true});
        }
      });
    },
    createVersion: function (post_parent, index) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_version",
        post_status: "draft",
        post_title : "Version " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index.toString()
      });
      model.save({},{
        wait: true,
        success: function (model) {
          app.kwpsPollsCollection.add(model);
          for (var i = 0; i < 1; i++) {
            that.createQuestionGroup(model.get('ID'), i);
          }
        }
      });
    },
    createQuestionGroup: function (post_parent, index) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_question_group",
        post_status: "draft",
        post_title : "Question Group " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index.toString()
      });
      model.save({},{
        success: function (model) {
          app.kwpsPollsCollection.add(model);
          for (var i = 0; i < 1; i++) {
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
        post_content : "question " + (index),
        post_parent : post_parent,
        _kwps_sort_order : index.toString()
      });
      model.save({},{
        success: function (model) {
          app.kwpsPollsCollection.add(model);
          for (var i = 0; i < 2; i++) {
            that.createAnswer(model.get('ID'), i, post_type);
          }
        }
      });
    },
    createAnswer: function (post_parent, index) {
      var model = new KwpsModel({
        post_type: "kwps_answer_option",
        post_status: "draft",
        post_content : "answer " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index.toString(),
        _kwps_answer_option_value : 'value...'
      });
      model.save({},{
        success: function (model) {
          app.kwpsPollsCollection.add(model);
        }
      });
    }
  });

  app.KwpsView = Backbone.View.extend({
    el: '#kwps_test',
    initialize: function () {
      //_.bindAll(this, 'cleanup');
      this.render();
      this.listenTo(this.collection, 'add remove', this.render);
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
      'change #post_title': 'changeTitle',
      'change .update-main': 'updateTestCollection',
      'change .update-post-title': 'updatePostTitle',
      'click .move-action:not(.disabled)': 'moveItem'
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

      //Get versions
      var versions = _.sortBy(_.invoke(this.collection.where({post_type: "kwps_version"}), 'toJSON'),'_kwps_sort_order');

      //Get intro's
      var intros = [];
      for (var i = 0; i < versions.length; i++) {
        var y = this.collection.findWhere({post_type: "kwps_intro", post_parent : versions[i].ID});
        if (y == undefined) {
          break;
        }
        intros[i] = y.toJSON();
      };


      //Get outro's
      var outros = [];
      for (var i = 0; i < versions.length; i++) {
        var y = this.collection.findWhere({post_type: "kwps_outro", post_parent : versions[i].ID});
        if (y == undefined) {
          break;
        }
        outros[i] = y.toJSON();
      };


      //Get questionGroups if questionGroups are open
      var qGroups = [];
      if (app.openRow.main_kwps_question_group) {
        for (var i = 0; i < versions.length; i++) {
          var qGrJson = _.invoke(this.collection.where({post_type: "kwps_question_group", post_parent : versions[i].ID}), 'toJSON');
          var sortedQuestionGroupPerVersion = _.sortBy(qGrJson, "_kwps_sort_order");
          qGroups.push(sortedQuestionGroupPerVersion);
        };
      };

      var sortedQGroups = _.groupBy(_.flatten(qGroups,true),"_kwps_sort_order");

      var allqGroups = _.invoke(this.collection.where({post_type: "kwps_question_group"}), 'toJSON');

      var sortedAllQGroups =_.groupBy(_.flatten(allqGroups,true),"_kwps_sort_order");

      //Get questions if a questiongroup is open
      var qu = [];
      if (app.openRow.kwps_question_group >= 0) {
        for (var i = 0; i < versions.length; i++) {
          var questionGroupId = this.collection.findWhere({post_type: "kwps_question_group", post_parent : versions[i].ID, _kwps_sort_order: app.openRow.kwps_question_group.toString()});
          if (questionGroupId != undefined) {
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

      //Get Answers if a question is open
      var ans = [];
      if (app.openRow.kwps_question >= 0) {
        for (var i = 0; i < versions.length; i++) {
          var openQuestionGroup = this.collection.findWhere({post_type: "kwps_question_group", post_parent : versions[i].ID, _kwps_sort_order: app.openRow.kwps_question_group.toString()});
          var openQuestion = this.collection.findWhere({post_type: "kwps_question", post_parent : openQuestionGroup.id, _kwps_sort_order: app.openRow.kwps_question.toString()});
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
        for (var i = versions.length - 1; i >=1; i--) {
          versions[i].deleteVersion = true
        };
      }
      data.versions = versions;
      
      data.table = [];

      // TITLE INTRO
      data.table.push({
        colSpan : versions.length +1,
        title: "Intro",
        postType: "kwps_intro",
        mainTitle: true,
        add: (intros.length <= 0),
        hasMore: (intros.length > 0),
        addText: 'Add Intro',
        opened: app.openRow.main_kwps_intro,
        amount: intros.length/ versions.length
      });

      // INTRO
      if (intros.length > 0 && intros.length == versions.length && app.openRow.main_kwps_intro) {
        data.table.push({
          sorterArrows : false,
          postType: 'kwps_intro',
          deletable : true,
          hasMore: false,
          hasAmount: false,
          editable: true, //TODO look if the test is published or not.
          versions: intros,
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
        add: (allqGroups && testmodus.get('_kwps_max_question_groups') <= _.size(sortedAllQGroups))? false:true,
        hasMore: (_.size(sortedAllQGroups) > 0),
        addText: 'Add question page',
        opened: app.openRow.main_kwps_question_group,
        amount: _.size(sortedAllQGroups)
      });

      if ( _.size(sortedAllQGroups) > 0 && app.openRow.main_kwps_question_group) {
        for (var sortOrderQG in sortedQGroups) {

          // QUESTION GROUP
          data.table.push({
            first: (sortOrderQG == '0'),
            last: (sortOrderQG == allqGroups.length/ versions.length-1),
            sorterArrows : (allqGroups.length/ versions.length > 1),
            postType: "kwps_question_group",
            deletable : true,
            hasMore: true,
            hasAmount: false,
            hasOpened: (app.openRow.kwps_question_group == sortOrderQG),
            editable: true, //TODO look if the test is published or not.
            versions: sortedQGroups[sortOrderQG],
            mainRow: true,
            sortOrder: sortOrderQG,
            number: parseInt(sortOrderQG) +1,
            //amountOfSiblings : this.collection.where({post_type: "kwps_question", post_parent: qGroups[0][sortOrderQG].ID}).length
          });


          if(app.openRow.kwps_question_group == sortOrderQG) {

            // TITLE QUESTION
            data.table.push({
              questionTitle: true,
              title: "Questions",
              postType: "kwps_question",
              questionGroupSortOrder : sortOrderQG,
              addText: "Add question",
              colSpan : versions.length +1,
              add: (testmodus.get('_kwps_max_questions_per_question_group') > _.size(sortedQu))
            });
            
            for (var sortOrderQ in sortedQu) {
              data.table.push({
                versions: sortedQu[sortOrderQ],
                question: true,
                postType: "kwps_question",
                sortOrder: sortOrderQ,
                number: parseInt(sortOrderQ) +1,
                //amountOfSiblings : this.collection.where({post_type: "kwps_answer_option", post_parent : qu[0].ID}).length,
                hasOpened: (app.openRow.kwps_question == sortOrderQ)
              });

              if (app.openRow.kwps_question >= 0 && sortOrderQ == app.openRow.kwps_question) {

                data.table.push({
                  answerTitle: true,
                  title: "Answers",
                  postType: "kwps_answer_option",
                  addText: "Add answer",
                  questionSortOrder: sortOrderQ,
                  colSpan : versions.length +1
                });

                for (var sortOrderA in sortedAns) {
                  data.table.push({
                    answer: true,
                    sorterArrows: true,
                    first: (sortOrderA == 0),
                    last: (sortOrderA == _.size(sortedAns)-1),
                    sortOrder : sortOrderA,
                    number: parseInt(sortOrderA) +1,
                    versions : sortedAns[sortOrderA],
                    postType: 'kwps_answer_option'
                  });
                }
              }
            }
          }
        }
      }

      data.table.push({
        colSpan : data.versions.length +1,
        title: "Outro",
        postType: "kwps_outro",
        mainTitle: true,
        add: (outros.length <= 0),
        hasMore: (outros.length > 0),
        addText: 'Add outro',
        opened: app.openRow.main_kwps_outro,
        amount: outros.length/ versions.length
      });

      if (outros.length > 0 && outros.length == versions.length && app.openRow.main_kwps_outro) {
        data.table.push({
          sorterArrows : false,
          postType: 'kwps_outro',
          deletable : true,
          hasMore: false,
          hasAmount: false,
          editable: true, //TODO look if the test is published or not.
          versions: outros,
          mainRow: true,
          sortOrder: 0
        })
      };

      return data;
    },
    deleteVersion: function(versionId) {
      var toDelete = this.collection.get(versionId);
      toDelete.destroy();
      this.collection.remove(toDelete);
      this.render();
    },
    deleteRow: function(postType, sortOrder) {
      console.log(postType);
      console.log(sortOrder);
      var postsToDelete = this.collection.where({post_type: postType, _kwps_sort_order: sortOrder.toString()});

      for (var i = 0; i < postsToDelete.length; i++) {
        postsToDelete[i].destroy();
      }
      this.collection.remove(postsToDelete);
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
      switch (postType) {
        case 'main_kwps_intro':
        case 'kwps_intro':
          for(var i = 0; i < kwpsPollLen; i++) {
            this.createIntro(kwpsPolls[i].id);
          }
          break;
        case 'main_kwps_outro':
        case 'kwps_outro':
          for(var i = 0; i < kwpsPollLen; i++) {
            this.createOutro(kwpsPolls[i].id);
          }
          break;
        case 'main_kwps_question_group':
        case 'kwps_question_group':
          var sortOrder = _.max(_.invoke(this.collection.where({post_type: 'kwps_question_group'}),"toJSON"), function (a) {return a._kwps_sort_order});
          sortOrder = (sortOrder == -Infinity || sortOrder == Infinity)? 0: parseInt(sortOrder._kwps_sort_order)+1;
          for(var i = 0; i < kwpsPollLen; i++) {
            this.createQuestionGroup(kwpsPolls[i].id, i, sortOrder);
          }
          break;
        case 'kwps_question':
          var sortOrderOfQuestionGroup = $(e.currentTarget).closest('tr').data('sort-order');
          var versionsOfOpenedQuestionGroup = this.collection.where({post_type: 'kwps_question_group', _kwps_sort_order: sortOrderOfQuestionGroup.toString()});
          var highestSortOrder = 0;
          for (var i = versionsOfOpenedQuestionGroup.length - 1; i >= 0; i--) {
            var connectedQuestionsToOpenedQuestionGroup = _.invoke(this.collection.where({post_type: "kwps_question", post_parent: versionsOfOpenedQuestionGroup[i].id}), 'toJSON');
            var highestSortOrder = Math.max (parseInt(_.max( connectedQuestionsToOpenedQuestionGroup ,function (model) {return parseInt(model._kwps_sort_order);})._kwps_sort_order),highestSortOrder);
          };
          highestSortOrder = (isNaN(highestSortOrder))? 0:highestSortOrder;
          for (var i = versionsOfOpenedQuestionGroup.length - 1; i >= 0; i--) {
            this.createQuestion(versionsOfOpenedQuestionGroup[i].id , highestSortOrder +1, function (newQuestion) {
              for (var i = 0; i < 2; i++) {
                that.createAnswer(newQuestion.get('ID'), i, function(newAnswer) {
                  console.log('answer created: ' + newAnswer.id);
                });
              }
            });
          }
          break;
        case 'kwps_answer_option':
          var sortOrder = $(e.currentTarget).closest('tr').data('sort-order');
          for(var i = 0; i < kwpsPollLen; i++) {
            var questionGroups = this.collection.where({post_type: 'kwps_question_group', post_parent: kwpsPolls[i].id});
            for(var j = 0; j < questionGroups.length; j++) {
              var questions = this.collection.where({post_type: 'kwps_question', post_parent: questionGroups[j].id, _kwps_sort_order: sortOrder.toString()});
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
      app.kwpsPollsCollection.create({
        post_type: "kwps_version",
        post_status: "draft",
        post_title : "Version " + (index+1),
        post_content : "Version " + (index+1),
        post_parent : previousVersion.get('post_parent'),
        _kwps_sort_order : index.toString()
      },
        {
          success: function (newVersion, response, options) {
            that.createIntro(newVersion.get('ID'), false);
            that.createOutro(newVersion.get('ID'), false);
            var questionGroups = that.collection.where({post_type: 'kwps_question_group', post_parent: previousVersion.get('ID')});  

            for (var i = 0; i < questionGroups.length; i++) {
              var questionGroupOriginal = questionGroups[i];
              that.createQuestionGroup(newVersion.get('ID'),i, questionGroupOriginal.get('_kwps_sort_order'), function(newQuestionGroup) {
                var questionsInGroup = that.collection.where({post_type: 'kwps_question', post_parent: questionGroupOriginal.id});  

                for (var i = 0; i < questionsInGroup.length; i++) {
                  var questionOriginal = questionsInGroup[i];

                  that.createQuestion(newQuestionGroup.get('ID'), questionGroupOriginal.get('_kwps_sort_order'), function(newQuestion) {
                    var answersInQuestion = that.collection.where({post_type: 'kwps_answer_option', post_parent: questionOriginal.id});  

                    for (var i = 0; i < answersInQuestion.length; i++) {
                      var answersInQuestionOriginal = answersInQuestion[i];
                      that.createAnswer(newQuestion.get('ID'), answersInQuestionOriginal.get('_kwps_sort_order'), function(newAnswer) {
                        console.log('answer created: ' + newAnswer.id);
                      });
                    }
                  });
                }
              });
            }
          }
        });
    },
    createIntro: function (post_parent, edit) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_intro",
        post_status: "draft",
        post_content : "intro ",
        post_parent : post_parent,
        _kwps_sort_order : "0"
      });
      model.save({},{
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
          if (edit) {
            app.router.navigate('edit/'+ model.id, {trigger: true});

          }
        }
      });
    },
    createOutro: function (post_parent, edit) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_outro",
        post_status: "draft",
        post_content : "outro ",
        post_parent : post_parent,
        _kwps_sort_order : "0"
      });
      model.save({},{
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
          if (edit) {
            app.router.navigate('edit/'+ model.id, {trigger: true});

          }
        }
      });
    },
    createQuestionGroup: function (post_parent, index, sortOrder, cb) {
      console.log(sortOrder)
      this.collection.create({
        post_type: "kwps_question_group",
        post_status: "draft",
        post_title : "Question Group " + (parseInt(sortOrder) + 1),
        post_parent : post_parent,
        _kwps_sort_order : sortOrder.toString()
      }, {
        wait: true,
        success: function(model, response, options) {
          if(cb) {
            cb(model);
          }
        }
      });
    },
    createQuestion: function (post_parent, index, cb) {
      app.kwpsPollsCollection.create({
        post_type: "kwps_question",
        post_status: "draft",
        post_content : "question" + (index + 1),
        post_parent : post_parent,
        _kwps_sort_order : index.toString()
      }, {
        wait: true,
        success: function (model, response, options) {
          if(cb) {
            cb(model);
          }        
        },
        error: function() {
          console.log('error in de vraag');
        }
      });
    },
    createAnswer: function (post_parent, index, cb) {
      app.kwpsPollsCollection.create({
        post_type: "kwps_answer_option",
        post_status: "draft",
        post_content : "answer ",
        post_parent : post_parent,
        _kwps_sort_order : index.toString(),
        _kwps_answer_option_value : "value ..."
      },
        {
          wait: true,
          success: function (model, response, options) {
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
      switch (postType) {
        case "main_kwps_intro" :
          app.openRow[postType] = !app.openRow[postType];
        break;
        case "main_kwps_outro" :
          app.openRow[postType] = !app.openRow[postType];
        break;
        case "kwps_question" :
          var sortOrder = $(event.currentTarget).closest('tr').data('sort-order');
          app.openRow[postType] = (app.openRow[postType] == sortOrder)? -1 : sortOrder;
        break;
        case "kwps_question_group" :
          var sortOrder = $(event.currentTarget).closest('tr').data('sort-order');
          app.openRow[postType] = (app.openRow[postType] == sortOrder)? -1 : sortOrder;
        break;
        case "main_kwps_question_group" :
          app.openRow[postType] = !app.openRow[postType];
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
        new app.KwpsViewEdit({model: app.test, attribute: kwpsAttribute});
      }
    },
    changeTitle: function(event) {
      this.model.set('post_title', $(event.target).val());
    },
    updateTestCollection: function(event) {
      var mainPost = this.collection.get(GetURLParameter('id'));
      var attribute = $(event.target).attr("name");
      var value = $(event.target).val();

      if(value === "on") {
        value = 1;
      }

      mainPost.set(attribute, value);
      mainPost.save();
    },
    updatePostTitle: function(event) {
      var attribute = $(event.target).attr("name");
      var value = $(event.target).val();
      var ID = $(event.target).data('id');
      var post = this.collection.get(ID);
      post.set(attribute, value);
      post.save();
    },
    moveItem: function(event) {
      var currentSortOrder = $(event.currentTarget).closest('tr').data('sort-order');
      if($(event.currentTarget).hasClass('up')) {
        var newSortOrder = currentSortOrder-1;
      } else {
        var newSortOrder = currentSortOrder+1;
      }
      var postType = $(event.currentTarget).closest('tr').data('post-type');

      var toMove = this.collection.where({post_type: postType, _kwps_sort_order: currentSortOrder.toString()});
      var toCorrect = this.collection.where({post_type: postType, _kwps_sort_order: newSortOrder.toString()});

      toMove.forEach(function(post) {
        post.set('_kwps_sort_order', newSortOrder.toString());
        post.save();
      });

      toCorrect.forEach(function(post) {
        post.set('_kwps_sort_order', currentSortOrder.toString());
        post.save();
      });

      this.render();
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
      'click button#add-media-button': 'addMedia',
      'click button#add-result-button': 'addResult',
      'click td.savesend input.button': 'insertIntoEditor',
      'click button#add-result-to-editor': 'insertChartIntoEditor'
    },
    render: function() {
      var data = {
        attribute: this.options.attribute,
        label: kwps_translations[this.options.attribute],
        text: this.model.get("post_content"),
        title: this.model.get("post_title"),
        answer_option_value: this.model.get("_kwps_answer_option_value")
      };
      $(this.el).html(app.templates.edit(data));
      tinymce.remove();
      tinymce.init({
        menubar: false,
        visual: true,
        selector: "textarea",
        plugins: "code link hr paste lists table textcolor wordcount charmap",
        toolbar: ["bold italic strikethrough bullist numlist blockquote hr alignleft aligncenter alignright link unlink", 
                  "formatselect underline alignjustify forecolor backcolor paste removeformat charmap outdent indent undo redo | code"]
      });
    },
    /* BEGIN RESULT INPUT */
    addResult: function () {
      console.log('add chart');
      $('iframe').contents().find('#tinymce').append('<div class="kwps-chart">Chart will be here</div>');
      // tb_show('','../wp-content/plugins/klasse-wp-poll-survey/includes/show-charts.php?type=image&amp;TB_iframe=true');
      return false;
    },
    insertChartIntoEditor: function (html) {
      console.log('you clicked to add result to editor');
      $('iframe').contents().find('#tinymce').append('<div class="kwps-chart">Hello</div>');
      tb_remove();
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
      var type, title, content, value;
      event.preventDefault();
      tinymce.triggerSave();
      content = $(event.target).closest('form').find('textarea').val();
      type = this.model.get("post_type");

      if (type === 'kwps_question_group') {
        title = $(event.target).closest('form').find('input[name=qg-title]').val();
      } else if (type === 'kwps_answer_option') {
        value = $(event.target).closest('form').find('input[name=ao-value]').val();
        if (!value) {
          value = 'value...';
        }
      }

      this.model.save({
        "post_content": content,
        "post_title" : title,
        "_kwps_answer_option_value" : value
      });

      this.cleanup();
      window.location = '#';
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
      var answers = app.kwpsPollsCollection.where({post_type : "kwps_answer_option", post_parent : this.model.id});
      answers = _.each(answers, function (answer){
          return answer.toJSON();
      })
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
    app.kwpsPollsCollection = new Backbone.Collection(kwpsTests, {
      model: KwpsModel
    });
  }
  app.router = new router;
  Backbone.history.start();

});
