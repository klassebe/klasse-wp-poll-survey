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
  };

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
    kwps_outro: true,
    kwps_intro: true,
    kwps_question_group: true,
    questionGroup: 0,
    question: 0
  };
  app.views = {}

  app.templates = {
    controlPanel: kwps_admin_templates.control_panel,
    edit: kwps_admin_templates.edit,
    question: kwps_admin_templates.table,
    newKwpsTest: kwps_admin_templates.choose_testmodus
  }
  
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

  KwpsModel = Backbone.Model.extend({
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
    initialize: function() {
      //this.bind("add", this.changeHandler);
    },
    changeHandler: function() {
      this.save({silent: true},{error : function (model, response, options) {
        console.log(this);
      }, success : function (model, response, options) {
        console.log(this)
      }});
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
      post_type: ""
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
      postData.post_type = 'kwps_test_collection';
      postData.post_status = "draft";

      var that = this;
      var model = new KwpsModel(postData);
      model.save({},{
        wait: true,
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
          for (var i = 0; i < 1; i++) {
            that.createVersion(model.get('ID'), i);
          };
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
        _kwps_sort_order : index
      });
      model.save({},{
        wait: true,
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
          for (var i = 0; i < 1; i++) {
            that.createQuestionGroup(model.get('ID'), i);
          };
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
        _kwps_sort_order : index
      });
      model.save({},{
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
          for (var i = 0; i < 1; i++) {
            that.createQuestion(model.get('ID'), i, model.get('post_type'));
          };
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
        _kwps_sort_order : index
      });
      model.save({},{
        success: function (model, response, options) {
          app.kwpsPollsCollection.add(model);
          for (var i = 0; i < 2; i++) {
            that.createAnswer(model.get('ID'), i, post_type);
          };
        }
      });
    },
    createAnswer: function (post_parent, index, post_type) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_answer_option",
        post_status: "draft",
        post_content : "answer " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index,
        _kwps_answer_option_value : 'value...'
      });
      model.save({},{
        success: function (model, response, options) {
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
      'change .update-post-title': 'updatePostTitle'
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
      var data = {};
      var privData = {};
      privData.intro = [];
      privData.outro = [];

      var mainPost = this.collection.get(GetURLParameter('id'));
      data.title = mainPost.get('post_title');
      data.versions = this.collection.where({post_type: "kwps_version"});
      
      privData.amountOfVersions = data.versions.length;
      privData.amountOfQuestionPages = this.collection.where({post_type: "kwps_question_group"}).length;



      data.collection = this.collection.findWhere({post_type: "kwps_test_collection"}).toJSON();

      data.testmodus = this.collection.findWhere({ID: data.collection.post_parent}).toJSON();

      for (var i = 0; i < data.versions.length; i++) {
        data.versions[i] = data.versions[i].toJSON();
        var kwpsIntro = this.collection.findWhere({post_type: "kwps_intro", post_parent : data.versions[i].ID});
        if (kwpsIntro !== undefined) {
          data.intro = true;
          data.versions[i].kwpsIntro = kwpsIntro.toJSON();
          privData.intro[i]= kwpsIntro.toJSON();
        }
        var kwpsOutro = this.collection.findWhere({post_type: "kwps_outro", post_parent : data.versions[i].ID});
        if (kwpsOutro !== undefined) {
          data.outro = true;
          data.versions[i].kwpsOutro = kwpsOutro.toJSON();
          privData.outro[i] = kwpsOutro.toJSON();
        }
        if (i === 0) {
          data.versions[i].main = true;
        }
        if (data.versions.length > 1) {
          data.versions[i].deleteVersion = true;
        }
      };

      var questionGroups = this.collection.where({post_type: "kwps_question_group"});
      for (var i = questionGroups.length - 1; i >= 0; i--) {
        questionGroups[i] = questionGroups[i].toJSON();
      };
      questionGroups = _.toArray(_.groupBy(questionGroups, "_kwps_sort_order"));

      for (var g = questionGroups.length - 1; g >= 0; g--) {
        if (g == app.openRow.questionGroup) {
          questionGroups[g].open = true;

          for (var h = 0; h < questionGroups[g].length; h++) {
            var questions = this.collection.where({post_type: "kwps_question", post_parent : questionGroups[g][h].ID});
            for (var i = questions.length - 1; i >= 0; i--) {
              questions[i] = questions[i].toJSON();
            };
            questions = _.groupBy(questions, "_kwps_sort_order");

            for (var i in questions) {
              // if sortorder is equal to openRow show all answers
              //questions.length = questions[i].length;

              if (i == app.openRow.question) {
                questions[i].open = true;
                data.answers = [];

                for (var j = 0; j < questions[i].length; j++) {
                  var answers = this.collection.where({post_type: "kwps_answer_option", post_parent : questions[i][j].ID});
                  _.each(answers, function (answer, index, list) {
                    answers[index] = answer.toJSON();
                  });

                  data.answers.push(answers);
                };
              }
            };
          }
        }
      }

      privData.questionGroupsLength = questionGroups.length;

      data.questionGroups = questionGroups;
      data.questions = questions;
      data.answers = _.flatten(data.answers);
      data.answers = _.groupBy(data.answers, "_kwps_sort_order");
      data.kwpsUniquenessTypes = kwpsUniquenessTypes;
      data.open = app.openRow;



      data.table = [];
      data.table.push({
        colSpan : data.versions.length +1,
        title: "Intro",
        postType: "kwps_intro",
        mainTitle: true,
        add: (this.collection.where({post_type: "kwps_intro"}).length > 0)? false:true,
        hasMore: (this.collection.where({post_type: "kwps_intro"}).length > 0)? true:false,
        addText: 'Add Intro',
        opened: app.openRow.kwps_intro,
        amount: privData.intro.length/ privData.amountOfVersions
      });
      if (this.collection.where({post_type: "kwps_intro"}).length > 0 && privData.intro.length == privData.amountOfVersions && app.openRow.kwps_intro) {
        data.table.push({
          sorterArrows : false,
          postType: 'kwps_intro',
          deletable : true,
          hasMore: false,
          hasAmount: false,
          editable: true, //TODO look if the test is published or not.
          versions: privData.intro,
          mainRow: true,
          sortOrder: 0
        })
      };
      data.table.push({
        colSpan : data.versions.length +1,
        title: "Question pages",
        postType: "kwps_question_group",
        mainTitle: true,
        add: (data.testmodus._kwps_max_question_groups <= (privData.amountOfQuestionPages/ privData.amountOfVersions))? false:true,
        hasMore: (privData.amountOfQuestionPages/ privData.amountOfVersions > 0)? true:false,
        addText: 'Add question page',
        opened: app.openRow.kwps_question_group,
        amount: privData.amountOfQuestionPages/ privData.amountOfVersions
      });
      if (privData.questionGroupsLength > 0 && app.openRow.kwps_question_group) {
        for (var key in data.questionGroups) {
          console.log('key',key);
          console.log(_.flatten(data.questionGroups[key]));
          data.table.push({
            sorterArrows : (data.questionGroups.length > 1)? true : false,
            postType: data.questionGroups[key][0].post_type,
            deletable : true,
            hasMore: (this.collection.where({post_type: "kwps_question", post_parent : data.questionGroups[key][data.questionGroups.length - 1].ID}).length > 0)? true : false,
            hasAmount: false,
            hasOpened: (app.openRow.questionGroup == key)? true : false,
            editable: true, //TODO look if the test is published or not.
            versions: data.questionGroups[key],
            mainRow: true,
            sortOrder: key,
            amountOfSiblings : this.collection.where({post_type: "kwps_question", post_parent : data.questionGroups[key][data.questionGroups.length - 1].ID}).length
          })
          if(app.openRow.questionGroup == key) {
            privData.questions = [];
            data.table.push({
              questionTitle: true,
              title: "Questions",
              postType: "kwps_question",
              addText: "Add question",
              colSpan : data.versions.length +1
            })
            for (var i = data.questionGroups[key].length - 1; i >= 0; i--) {
              console.log(data.questionGroups[key][i].ID);
              privData.questions = privData.questions.concat(this.collection.where({post_type: "kwps_question", post_parent : data.questionGroups[key][i].ID}));
            };
            console.log(privData.questions);
            for (var i = privData.questions.length - 1; i >= 0; i--) {
              privData.questions[i] = privData.questions[i].toJSON();
            };
            console.log(privData.questions);
            privData.questions = _.toArray(_.groupBy(privData.questions, "_kwps_sort_order"));
            console.log(privData.questions);
            for (var i = 0; i < privData.questions.length; i++) {
              console.log(privData.questions[i]);
              console.log(privData.questions[i][0]);
              data.table.push({
                versions: privData.questions[i],
                question: true,
                postType: privData.questions[i][0].post_type,
                sortOrder: i+1,
                amountOfSiblings : this.collection.where({post_type: "kwps_answer_option", post_parent : privData.questions[i][0].ID}).length
              })
              if (app.openRow.question >= 0 && i == app.openRow.question) {
                privData.answers = [];
                data.table.push({
                  answerTitle: true,
                  title: "Answers",
                  postType: "kwps_answer_option",
                  addText: "Add answer",
                  questionSortOrder: i,
                  colSpan : data.versions.length +1
                })
                for (var j = privData.questions[i].length - 1; j >= 0; j--) {
                  privData.answers = privData.answers.concat(this.collection.where({post_type: "kwps_answer_option", post_parent : privData.questions[i][j].ID}));
                };
                console.log('answers: ',privData.answers);
                for (var j = privData.answers.length - 1; j >= 0; j--) {
                  privData.answers[j] = privData.answers[j].toJSON();
                };
                console.log('answers: ',privData.answers);
                privData.answers = _.toArray(_.groupBy(privData.answers, "_kwps_sort_order"));
                console.log('answers: ',privData.answers);
                for (var j = 0; j < privData.answers.length; j++) {
                  data.table.push({
                    answer: true,
                    sortOrder : j+1,
                    versions : privData.answers[j]
                  })
                };
              }
            };
          }
        };
        console.log(data.table);
      };
      data.table.push({
        colSpan : data.versions.length +1,
        title: "Outro",
        postType: "kwps_outro",
        mainTitle: true,
        add: (this.collection.where({post_type: "kwps_outro"}).length > 0)? false:true,
        hasMore: (this.collection.where({post_type: "kwps_outro"}).length > 0)? true:false,
        addText: 'Add outro',
        opened: app.openRow.kwps_outro,
        amount: privData.outro.length/ privData.amountOfVersions
      });
      console.log(privData.outro);
      if (this.collection.where({post_type: "kwps_outro"}).length > 0 && privData.outro.length == privData.amountOfVersions && app.openRow.kwps_outro) {
        data.table.push({
          sorterArrows : false,
          postType: 'kwps_outro',
          deletable : true,
          hasMore: false,
          hasAmount: false,
          editable: true, //TODO look if the test is published or not.
          versions: privData.outro,
          mainRow: true,
          sortOrder: 0
        })
      };

      return data;
    },
    deleteVersion: function(event) {
      //TODO php function delete poll with(id) and all child posts + child posts of questions
      event.preventDefault();
      var versionId = $(event.target).data('version-id');
      var toDelete = this.collection.get(versionId);
      toDelete.destroy();
      this.collection.remove(toDelete);
    },
    deleteUnique: function (postType) {
      var postToDelete = this.collection.findWhere({post_type: postType});
      postToDelete.destroy();
      this.collection.remove(postToDelete);
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
      var type = $(e.currentTarget).data('type');
      var kwpsPolls = this.collection.where({post_type: 'kwps_poll'});
      var kwpsPollLen = kwpsPolls.length;
      switch (type) {
        case 'unique':
            var postType = $(e.currentTarget).data('post-type');
            this.deleteUnique(postType);
          break;
        case 'row':
            var sortOrder = $(e.currentTarget).data('sort-order');     
            var postType = $(e.currentTarget).data('post-type');
            this.deleteRow(postType, sortOrder);
          break;
        default:
          console.log('no post type was given');
      }
    },
    createNew: function (e) {
      e.preventDefault();
      var postType = $(e.currentTarget).data('post-type');
      var kwpsPolls = this.collection.where({post_type: 'kwps_version'});
      // get the id of the post parent(main version)
      var kwpsPollLen = kwpsPolls.length;
      switch (postType) {
        case 'kwps_intro':
          for(var i = 0; i < kwpsPollLen; i++) {
            this.createIntro(kwpsPolls[i].id);
          }
          break;
        case 'kwps_outro':
          for(var i = 0; i < kwpsPollLen; i++) {
            this.createOutro(kwpsPolls[i].id);
          }
          break;
        case 'kwps_question_group':
          for(var i = 0; i < kwpsPollLen; i++) {
            this.createQuestionGroup(kwpsPolls[i].id);
          }
          break;
        case 'kwps_question':
          for(var i = 0; i < kwpsPollLen; i++) {
            var parent = this.collection.findWhere({post_type: 'kwps_question_group', post_parent: kwpsPolls[i].id});
            this.createQuestion(parent.get('ID'));
          }
          break;
        case 'kwps_answer_option':
          var sortOrder = $(e.currentTarget).data('sort-order');
          for(var i = 0; i < kwpsPollLen; i++) {
            var questionGroups = this.collection.where({post_type: 'kwps_question_group', post_parent: kwpsPolls[i].id});
            for(var j = 0; j < questionGroups.length; j++) {
              var questions = this.collection.where({post_type: 'kwps_question', post_parent: questionGroups[j].id, _kwps_sort_order: sortOrder.toString()});
              for(var k = 0; k < questions.length; k++) {
                this.createAnswer(questions[k].id);
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
        post_title : "Version " + index,
        post_content : "Version " + index,
        post_parent : previousVersion.get('post_parent'),
        _kwps_sort_order : index
      },
        {
          success: function (newVersion, response, options) {
            that.createIntro(newVersion.get('ID'), false);
            that.createOutro(newVersion.get('ID'), false);
            var questionGroups = that.collection.where({post_type: 'kwps_question_group', post_parent: previousVersion.get('ID')});  

            for (var i = 0; i < questionGroups.length; i++) {
              var questionGroupOriginal = questionGroups[i];

              that.createQuestionGroup(newVersion.get('ID'), function(newQuestionGroup) {
                var questionsInGroup = that.collection.where({post_type: 'kwps_question', post_parent: questionGroupOriginal.id});  

                for (var i = 0; i < questionsInGroup.length; i++) {
                  var questionOriginal = questionsInGroup[i];

                  that.createQuestion(newQuestionGroup.get('ID'), function(newQuestion) {
                    var answersInQuestion = that.collection.where({post_type: 'kwps_answer_option', post_parent: questionOriginal.id});  

                    for (var i = 0; i < answersInQuestion.length; i++) {
                      that.createAnswer(newQuestion.get('ID'), function(newAnswer) {
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
        _kwps_sort_order : 0
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
    createQuestionGroup: function (post_parent, cb) {
      var index = this.collection.where({post_type: 'kwps_question_group', post_parent: post_parent}).length;
      this.collection.create({
        post_type: "kwps_question_group",
        post_status: "draft",
        post_title : "Question Group " + index,
        post_parent : post_parent,
        _kwps_sort_order : index
      }, {
        wait: true,
        success: function(model, response, options) {
          if(cb) {
            cb(model);
          }
        }
      });
    },
    createQuestion: function (post_parent, cb) {
      var index = this.collection.where({post_type: 'kwps_question', post_parent: post_parent}).length;
      app.kwpsPollsCollection.create({
        post_type: "kwps_question",
        post_status: "draft",
        post_content : "question",
        post_parent : post_parent,
        _kwps_sort_order : index
      }, {
        wait: true,
        success: function (model, response, options) {
          if(cb) {
            cb(model);
          }        
        }
      });
    },
    createAnswer: function (post_parent, cb) {
      var index = this.collection.where({post_type: 'kwps_answer_option', post_parent: post_parent}).length;
      app.kwpsPollsCollection.create({
        post_type: "kwps_answer_option",
        post_status: "draft",
        post_content : "answer ",
        post_parent : post_parent,
        _kwps_sort_order : index,
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
      var type = $(event.currentTarget).data('type');
      console.log('type: ', type);
      switch (type) {
        case "kwps_intro" :
          app.openRow[type] = (app.openRow[type])? false: true;
        break;
        case "kwps_outro" :
          app.openRow[type] = (app.openRow[type])? false: true;
        break;
        case "question" :
          var sortOrder = $(event.currentTarget).data('sort-order');
          app.openRow[type] = (app.openRow[type] == sortOrder)? -1 : sortOrder;
        break;
        case "kwps_question_group" :
          app.openRow[type] = (app.openRow[type])? false: true;
        break;
      }
      //toggleOnRow = $(event.currentTarget).data('question-row');
      
      //app.openRow[type] = (app.openRow[type] !== toggleOnRow || app.openRow[type] === "")? toggleOnRow:"";
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
      'click button#update': 'updateData'
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
      /* MEDIA UPLOAD */
      $('#add-media-button').on('click', function() {
        tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
        return false;
      });
      window.send_to_editor = function(html) {
        var imgUrl = $('img',html).attr('src');
        var imgTitle = url.split("/").pop();
        $('iframe').contents().find('#tinymce').append('<img class="img img-' + imgTitle + '" src="' + imgUrl + '" alt="">');
        tb_remove();
      };
    },
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
