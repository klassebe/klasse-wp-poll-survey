jQuery(function ($) {
  // debug helper
  // usage: {{debug}} or {{debug someValue}}
  // from: @commondream (http://thinkvitamin.com/code/handlebars-js-part-3-tips-and-tricks/)
  
  Handlebars.registerHelper("debug", function(optionalValue) {
    // console.log("Current Context");
    // console.log("====================");
    // console.log(this);

    if (optionalValue) {
      // console.log("Value");
      // console.log("====================");
      // console.log(optionalValue);
    }
  });

  Handlebars.registerHelper("getColumnCount", function(versions) {
    versions = parseInt(versions.length);
    return versions + 1;
  });

  Handlebars.registerHelper('setIndex', function(value){
    this.index = Number(value + 1);
  });

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
  //TODO: set back to empty after developing
  app.openAnswer = 0;
  app.views = {}

  if(typeof $('#version_template').html() !== 'undefined') {
    app.templates = {
      version: Handlebars.compile($('#version_template').html()),
      edit: Handlebars.compile($('#edit_template').html()),
      question: Handlebars.compile($('#question_template').html())
  };
  }
  
  // Routing
   var router = Backbone.Router.extend({
    routes: {
      '' : 'home',
      'edit/:id' : 'edit',
      'edit/question/:id' : 'editQuestion',
      'new/:type/:parentId' : 'new'
    },
    home : function () {
      // console.log("ROUTING TO: home");
      if (app.kwpsPollsCollection !== undefined) {
        app.views.index = new app.KwpsView({collection: app.kwpsPollsCollection});
      } 
      app.views.index.initialize();
    },
    edit :  function (id) {
      // console.log("ROUTING TO: edit");
      // controleren of er nog een edit view in steekt en alle events unbinden
      app.views.edit = new app.KwpsViewEdit({
        action : "edit",
        model : app.kwpsPollsCollection.get(id)
      });
    },
    editQuestion : function (id) {
      // console.log("ROUTING TO: editQuestion");
      app.views.edit = new app.KwpsViewQuestion({
        model : app.kwpsPollsCollection.get(id)
      })
    },
    new : function (type, parentId) {
      // console.log("ROUTING TO: new");
      app.views.edit = new app.KwpsViewEdit({
        type:type, 
        id : id,
        action : "new"
      });
    }
  });

  KwpsModel = Backbone.Model.extend({
    methodToURL: {
      'read': '/user/get',
      'create': '/user/create',
      'update': '/user/update',
      'delete': '/wp-admin/admin-ajax.php?action=kwps_version_delete'
    },
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
      this.bind("change", this.changeHandler);
    },
    changeHandler: function() {
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
    }
  }); 

  app.KwpsView = Backbone.View.extend({
    el: '#kwps_test',
    initialize: function () {
      //_.bindAll(this, 'cleanup');
      this.render();
      this.listenTo(this.collection, 'change', this.render);
    },
    events: {
      'click #add-version': 'addVersion',
      'mouseenter td': 'showActions',
      'mouseleave td': 'hideActions',
      'click .toggle-details': 'toggleDetails',
      'click .add-answer': 'addAnswer',
      'click #add-question': 'addQuestion',
      'click .delete-version': 'deleteVersion',
      'click .preview': 'preview',
      'click .edit': 'edit',
      'change #post_title': 'changeTitle'
    },
    cleanup: function() {
      this.undelegateEvents();
      $(this.el).empty();
    },
    render: function () {
      //$('#post_title').val(this.model.get('post_title'));
      var data = this.prepareData();
      $(this.el).html(app.templates.version(data));
      $('#tabs').tabs();
    },
    prepareData: function() {
      var data = {};

      var mainPost = this.collection.get(GetURLParameter('id'));
      data.title = mainPost.get('post_title');
      data.versions = this.collection.where({post_type: "kwps_poll"});
      for (var i = 0; i < data.versions.length; i++) {
        data.versions[i] = data.versions[i].toJSON();
        data.versions[i].kwpsIntro = this.collection.findWhere({post_type: "kwps_intro", post_parent : data.versions[i].ID});
        data.versions[i].kwpsIntro = (data.versions[i].kwpsIntro !== undefined)? data.versions[i].kwpsIntro.toJSON(): {};
        data.versions[i].kwpsOutro = this.collection.findWhere({post_type: "kwps_outro", post_parent : data.versions[i].ID});
        data.versions[i].kwpsOutro = (data.versions[i].kwpsOutro !== undefined)? data.versions[i].kwpsOutro.toJSON(): {};
        if (i === 0) {
          data.versions[i].main = true;
        }
      };
      var questions = this.collection.where({post_type: "kwps_question"});
      _.each(questions, function (question, index, list) {
        questions[index] = question.toJSON();
      });
      questions = _.groupBy(questions, "_kwps_sort_order");

      console.log(questions);
      for (var i in questions) {
        // if sortorder is equal to openAnswer show all answers
        if (i == app.openAnswer) {
                  console.log(i);
          questions[i].open = true;
          data.answers = [];

          console.log(questions[i]);
          for (var j = 0; j < questions[i].length; j++) {
            console.log(questions[i][j].ID);
            var answers = this.collection.where({post_type: "kwps_answer_option", post_parent : questions[i][j].ID});
            console.log(answers);
            _.each(answers, function (answer, index, list) {
              answers[index] = answer.toJSON();
            });

            data.answers.push(answers);
          };
        }
      };
      data.answers = _.flatten(data.answers);
      data.answers = _.groupBy(data.answers, "_kwps_sort_order");
      data.questions = questions;
      // console.log(data);
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
      //TODO php function delete poll with(id) en all child posts + child posts of questions
      event.preventDefault();
      var kwpdId = $(event.target).closest('div.actions').data('kwps-id');
      var toDelete = this.model.get('versions').get(kwpdId);
      toDelete.destroy();
    },
    addAnswer: function(event) {
      var answer = new app.AnswerModel();
      app.test.get('answers').add(answer);

      this.model.get('versions').each(function(version) {
        var newAnswer = new app.AnswerModel();
        version.get('answers').add(newAnswer);
      });

      this.render();
    },
    addQuestion: function(event) {
      // will be done by routing

      // var question = new app.QuestionModel();
      // question.set('post_parent', app.test.get('ID'));
      // app.test.get('questions').add(question);
      // console.log(question);
      // console.log(app.test);
      // /**/$(this.el).html(app.templates.iframe(data));
      new app.KwpsViewQuestion();
    },
    showActions: function(event) {
      $(event.target).find(".actions").show();
    },
    hideActions: function(event) {
      $(event.target).find(".actions").hide();
    },
    toggleDetails: function(event) {
      toggleOnRow = $(event.currentTarget).data('question-row');
      console.log(toggleOnRow);
      app.openAnswer = (app.openAnswer !== toggleOnRow || app.openAnswer === "")? toggleOnRow:"";
      this.render();
    },
    preview: function(event) {
      // console.log(event);
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
      console.log(this.model);
      var answers = app.kwpsPollsCollection.where({post_type : "kwps_answer_option", post_parent : this.model.id});
      // console.log(answers);
      answers = _.each(answers, function (answer){
          return answer.toJSON();
      })
      var data = {
        question: this.model.toJSON(),
        answers: answers
      };
      // console.log(data);
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

  if (typeof kwpsPolls !== 'undefined') {
    app.kwpsPollsCollection = new Backbone.Collection(kwpsPolls, {
      model: KwpsModel
    });
    app.router = new router;
    Backbone.history.start();
  }
  
  
});
