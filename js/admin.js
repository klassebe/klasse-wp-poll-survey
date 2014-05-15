jQuery(function ($) {


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

  // debug helper
  // usage: {{debug}} or {{debug someValue}}
  // from: @commondream (http://thinkvitamin.com/code/handlebars-js-part-3-tips-and-tricks/)
  Handlebars.registerHelper("debug", function(optionalValue) {
     console.log("Current Context");
     console.log("====================");
     console.log(this);

    if (optionalValue) {
       console.log("Value");
       console.log("====================");
       console.log(optionalValue);
    }
  });

  Handlebars.registerHelper("getColumnCount", function(versions) {
    versions = parseInt(versions.length);
    return versions + 2;
  });

  Handlebars.registerHelper('setIndex', function(value){
    this.index = Number(value + 1);
  });

  Handlebars.registerHelper('subStringStripper', function (html, length){
    var tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    var result = tmp.textContent || tmp.innerText || "";
    return  result.substring(0, length) + " ...";
  })

  Handlebars.registerHelper('sorter', function (index, obj) {
    var size = 0,
        key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    var result;
    if(index == 0) {
      result = '<span class="up passive"></span>'
    } else {
      result = '<span class="up"></span>'
    }
    if (index == size-1) {
      result = result + '<span class="down passive"></span>'
    } else {
      result = result + '<span class="down"></span>'
    }
    return result;
  });

  Handlebars.registerHelper('lastItem', function ( className ,index, obj){
    var size = 0,
        key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return (index == size-1)? className: "";
  });

  Handlebars.registerHelper('selected', function(option, value){
    if (option === value) {
      return ' selected';
    } else {
      return ''
    }
  });

  Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {

    switch (operator) {
      case '==':
        return (v1 == v2) ? options.fn(this) : options.inverse(this);
      case '===':
        return (v1 === v2) ? options.fn(this) : options.inverse(this);
      case '<':
        return (v1 < v2) ? options.fn(this) : options.inverse(this);
      case '<=':
        return (v1 <= v2) ? options.fn(this) : options.inverse(this);
      case '>':
        return (v1 > v2) ? options.fn(this) : options.inverse(this);
      case '>=':
        return (v1 >= v2) ? options.fn(this) : options.inverse(this);
      case '&&':
        return (v1 && v2) ? options.fn(this) : options.inverse(this);
      case '||':
        return (v1 || v2) ? options.fn(this) : options.inverse(this);
      default:
        return options.inverse(this);
    }
  });

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



  var app = {};
  app.url = 'admin-ajax.php?action=';
  app.openAnswer = {
    questionGroup: "",
    question: ""
  };
  app.views = {}

  if(typeof $('#version_template').html() !== 'undefined') {
    app.templates = {
      version: Handlebars.compile($('#version_template').html()),
      edit: Handlebars.compile($('#edit_template').html()),
      question: Handlebars.compile($('#question_template').html()),
      newKwpsTest: Handlebars.compile($('#chooseTestModus_template').html())
  };
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
    idAttribute: 'ID',
    defaults: {
      post_author: 0,
      post_date: "",
      post_title: "",
      post_status: "draft",
      post_modified: "",
      post_parent: 0,
      post_type: "",
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
      postData.post_status = "publish";

      var that = this;
      var model = new KwpsModel(postData);
      model.save({},{
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
        post_status: "publish",
        post_title : "Version " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index
      });
      model.save({},{
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
        post_status: "publish",
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
        post_status: "publish",
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
        post_status: "publish",
        post_content : "answer " + (index+1),
        post_parent : post_parent,
        _kwps_sort_order : index
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
      this.listenTo(this.collection, 'add', this.render);
    },
    events: {
      'click #add-version': 'addVersion',
      'mouseenter td': 'showActions',
      'mouseleave td': 'hideActions',
      'click .toggle-details': 'toggleDetails',
      'click button.add': 'createNew',
      'click span.del': 'deletePostType',
      // 'click .delete-version': 'deleteVersion',
      // 'click .delete-intro': 'deleteIntro',
      'change #post_title': 'changeTitle',
      'change .update-main': 'updateTestCollection'
    },
    cleanup: function() {
      this.undelegateEvents();
      $(this.el).empty();
    },
    render: function () {
      var data = this.prepareData();
      $(this.el).html(app.templates.version(data));
      $('#tabs').tabs();
    },
    prepareData: function() {
      var data = {};

      var mainPost = this.collection.get(GetURLParameter('id'));
      data.title = mainPost.get('post_title');
      data.versions = this.collection.where({post_type: "kwps_version"});
      data.collection = this.collection.findWhere({post_type: "kwps_test_collection"}).toJSON();
      data.testmodus = this.collection.findWhere({ID: data.collection.post_parent}).toJSON();


      for (var i = 0; i < data.versions.length; i++) {
        data.versions[i] = data.versions[i].toJSON();
        var kwpsIntro = this.collection.findWhere({post_type: "kwps_intro", post_parent : data.versions[i].ID});
        if (kwpsIntro !== undefined) {
          data.intro = true;
          data.versions[i].kwpsIntro = kwpsIntro.toJSON();
        }
        var kwpsOutro = this.collection.findWhere({post_type: "kwps_outro", post_parent : data.versions[i].ID});
        if (kwpsOutro !== undefined) {
          data.outro = true;
          data.versions[i].kwpsOutro = kwpsOutro.toJSON();
        }
        if (i === 0) {
          data.versions[i].main = true;
        }
      };


      var questionGroups = this.collection.where({post_type: "kwps_question_group"});
      _.each(questionGroups, function (questionGroup, index, list) {
        questionGroups[index] = questionGroup.toJSON();
      });
      var questionGroupsLength = questionGroups.length;

      questionGroups = _.groupBy(questionGroups, "_kwps_sort_order");
      questionGroups.length = questionGroupsLength;

      for (var g in questionGroups) {
        if (g == app.openAnswer.questionGroup) {
          questionGroups[g].open = true;

          for (var h = 0; h < questionGroups[g].length; h++) {
            var questions = this.collection.where({post_type: "kwps_question", post_parent : questionGroups[g][h].ID});
            _.each(questions, function (question, index, list) {
              questions[index] = question.toJSON();
            });
            questions = _.groupBy(questions, "_kwps_sort_order");

            for (var i in questions) {
              // if sortorder is equal to openAnswer show all answers
              questions.length = questions[i].length;

              if (i == app.openAnswer.question) {
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

      data.questionGroups = questionGroups;
      data.questions = questions;
      data.answers = _.flatten(data.answers);
      data.answers = _.groupBy(data.answers, "_kwps_sort_order");
      data.kwpsUniquenessTypes = kwpsUniquenessTypes;
      data.open = app.openAnswer;
      return data;
    },
    addVersion: function (event) {
      //TODO php function clone main poll en return added posts
      var newVersion = this.model.clone();
      newVersion.unset('versions');
      newVersion.set({
        answers: this.model.get('answers')
      });
      app.test.set('versions', newVersion, {remove: false});
    },
    deleteVersion: function(event) {
      //TODO php function delete poll with(id) and all child posts + child posts of questions
      event.preventDefault();
      var kwpdId = $(event.target).closest('div.actions').data('kwps-id');
      var toDelete = this.model.get('versions').get(kwpdId);
      toDelete.destroy();
    },
    deleteIntro: function (event) {
      event.preventDefault();
      var kwpdId = $(event.target).closest('div.action').data('kwps-id');
      // var kwpdType = $(event.target).closest('div.action').data('kwps-type');
      var toDelete = this.model.get('kwps_intro').get(kwpsId);
      toDelete.destroy();
    },
    deleteOutro: function (event) {
      event.preventDefault();
      var kwpdId = $(event.target).closest('div.action').data('kwps-id');
      var toDelete = this.model.get('kwps_outro').get(kwpsId);
      toDelete.destroy();
    },
    deleteQuestion: function (event) {
      event.preventDefault();
      var kwpdId = $(event.target).closest('div.action').data('kwps-id');
      var toDelete = this.model.get('kwps_question').get(kwpsId);
      toDelete.destroy();
    },
    deleteAnswerOption: function (event) {
      event.preventDefault();
      var kwpdId = $(event.target).closest('div.action').data('kwps-id');
      var toDelete = this.model.get('kwps_answer_option').get(kwpsId);
      toDelete.destroy();
    },
    deletePostType: function(e) {
      e.preventDefault();
      var postType = $(e.currentTarget).data('post-type');
      var kwpsPolls = this.collection.where({post_type: 'kwps_poll'});
      var kwpsPollLen = kwpsPolls.length;
      switch (postType) {
        case 'kwps_intro':
            this.deleteIntro(kwpsPolls[i].id, true);
          break;
        case 'kwps_outro':
            this.deleteOutro(kwpsPolls[i].id, true);
          break;
        case 'kwps_question':
          for(var i = 0; i < kwpsPollLen; i++) {
            this.deleteQuestion(kwpsPolls[i].id, true);
          }
          break;
        case 'kwps_answer_option':
          for(var i =0; i< kwpsPollLen; i++) {
            this.deleteAnswerOption(kwpsPolls[i].id, true);
          }
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
            this.createIntro(kwpsPolls[i].id, true);
          }
          break;
        case 'kwps_outro':
          for(var i = 0; i < kwpsPollLen; i++) {
            this.createOutro(kwpsPolls[i].id, true);
          }
          break;
        case 'kwps_question_group':
          for(var i = 0; i < kwpsPollLen; i++) {
            this.createQuestionGroup(kwpsPolls[i].id, true);
          }
          break;
        case 'kwps_question':
          var openOrder = $(e.currentTarget).data('open-order');

          for(var i = 0; i < kwpsPollLen; i++) {
            this.createQuestion(kwpsPolls[i].id, openOrder, true);
          }
          break;
        case 'kwps_answer_option':
          var sortOrder = $(e.currentTarget).data('sort-order');
          var kwpsAnswerOptions = this.collection.where({post_type: 'kwps_answer_option', _kwps_sort_order: sortOrder});
          break;
        default:
          console.log('no post type was given');
      }
    },
    createIntro: function (post_parent, edit) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_intro",
        post_status: "publish",
        post_content : "intro ",
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
    createOutro: function (post_parent, edit) {
      var that = this;
      var model = new KwpsModel({
        post_type: "kwps_outro",
        post_status: "publish",
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
    createQuestionGroup: function (post_parent, edit) {
      var index = this.collection.where({post_type: 'kwps_question_group', post_parent: post_parent}).length;
      app.kwpsPollsCollection.create({
        post_type: "kwps_question_group",
        post_status: "publish",
        post_title : "Question Group " + index,
        post_parent : post_parent,
        _kwps_sort_order : index
      });
    },
    createQuestion: function (post_parent, open_order, edit) {
      var parent = this.collection.findWhere({post_type: 'kwps_question_group', post_parent: post_parent, _kwps_sort_order: open_order.toString()});
      var index = this.collection.where({post_type: 'kwps_question', post_parent: parent.get('ID')}).length;

      app.kwpsPollsCollection.create({
        post_type: "kwps_question",
        post_status: "publish",
        post_content : "question",
        post_parent : parent.get('ID'),
        _kwps_sort_order : index
      }, {
        success: function (model, response, options) {
          if (edit) {
            app.router.navigate('edit/'+ model.id, {trigger: true});

          }
        }
      });
    },
    createAnswer: function (post_parent, edit) {
      app.kwpsPollsCollection.create({
        post_type: "kwps_answer_option",
        post_status: "publish",
        post_content : "answer ",
        post_parent : post_parent,
        _kwps_sort_order : 0
      },
        {
          success: function (model, response, options) {
            if (edit) {
              app.router.navigate('edit/'+ model.id, {trigger: true});

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
      toggleOnRow = $(event.currentTarget).data('question-row');
      type = $(event.currentTarget).data('type');
      app.openAnswer[type] = (app.openAnswer[type] !== toggleOnRow || app.openAnswer[type] === "")? toggleOnRow:"";
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
        text: this.model.get("post_content")
      };
      $(this.el).html(app.templates.edit(data));
      tinymce.remove();
      tinymce.init({
        selector: "textarea"
      });
    },
    updateData: function(event) {
      event.preventDefault();
      tinymce.triggerSave();
      var value = $(event.target).closest('form').find('textarea').val();
      this.model.save({"post_content": value});

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
    app.kwpsPollsCollection = new Backbone.Collection(kwpsTests, {
      model: KwpsModel
    });
  }
  app.router = new router;
  Backbone.history.start();
  
});
