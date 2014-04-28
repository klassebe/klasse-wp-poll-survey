jQuery(function ($) {
  // debug helper
  // usage: {{debug}} or {{debug someValue}}
  // from: @commondream (http://thinkvitamin.com/code/handlebars-js-part-3-tips-and-tricks/)
  Backbone.history.start();
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

  var app = {};
  app.url = 'admin-ajax.php?action=';

  if(typeof $('#version_template').html() !== 'undefined') {
    app.templates = {
      version: Handlebars.compile($('#version_template').html()),
      edit: Handlebars.compile($('#edit_template').html()),
      iframe: Handlebars.compile($('#iframe_template').html())
  };
  }
  

  app.AnswerModel = Backbone.AssociatedModel.extend({

  });

  KwpsModel = Backbone.AssociatedModel.extend({
    methodToURL: {
      'read': '/user/get',
      'create': '/user/create',
      'update': '/user/update',
      'delete': '/wp-admin/admin-ajax.php?action=kwps_version_delete'
    },
    action: {
      create: 'kwps_save_poll',
      update: 'kwps_update_poll',
      delete: 'kwps_delete_poll'
    },

    sync: function(method, model, options) {
      options = options || {};
      options.url = app.url + model.action[method.toLowerCase()]

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
      post_type: "kwps_poll",
      _kwps_intro: "",
      _kwps_outro: "",
      open: false,
      questions: [],
      versions: [],
      answers: []
    },
    relations: [{
      type: Backbone.Many,
      key: 'versions',
      relatedModel: this,
      collectionType: 'VersionCollection'
    },
    {
      type: Backbone.Many,
      key: 'answers',
      relatedModel: app.AnswerModel,
      collectionType: 'AnswerCollection'
    },
    {
      type: Backbone.Many,
      key: 'questions',
      relatedModel: app.QuestionModel,
      collectionType: 'QuestionCollection'
    }]
  });

  app.QuestionModel = Backbone.AssociatedModel.extend({

    action: {
      create: 'kwps_save_question',
      update: 'kwps_update_question',
      delete: 'kwps_delete_question'
    },

    sync: function (method, model, options) {
      options = options || {};
      options.url = app.url + model.action[method.toLowerCase()];

      return Backbone.sync.apply(this, arguments);
    },
    initialize: function () {
      this.bind("change", this.changeHandler);
    },
    changeHandler: function () {
      this.save();
    },
    idAttribute: 'ID',
    defaults: {
      post_title: "",
      post_status: "draft",
      post_parent: null,
      test: null
    }

  });

  VersionCollection = Backbone.Collection.extend({
    model: KwpsModel
  });

  AnswerCollection = Backbone.Collection.extend({
    model: app.AnswerModel
  });

  QuestionCollection = Backbone.Collection.extend({
    model: app.QuestionModel
  });

  app.KwpsView = Backbone.View.extend({
    el: '#kwps_test',
    initialize: function () {
      _.bindAll(this, 'cleanup');
      this.render();
      this.listenTo(this.model, 'change', this.render);
      this.model
        .on('add:answers', this.render)
        .on('change:answers', this.render)
        .on('add:versions', this.render)
        .on('change:versions', this.render)
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
      $('#post_title').val(this.model.get('post_title'));
      var data = this.model.toJSON();
      data.table = this.prepareTable();
      $(this.el).html(app.templates.version(data));
      $('#tabs').tabs();
    },
    prepareTable: function() {
      var tableData = new Array();
      this.model.get('answers').each(function(answer) {
        var row = new Array(answer.toJSON());
        tableData.push(row);
      });

      var versions = this.model.get('versions');
      versions.each(function(version) {
        var index = versions.indexOf(version);
        var answers = version.get('answers');
        answers.each(function(answer) {
          var indexAnswer = answers.indexOf(answer);
          tableData[indexAnswer].push(answer.toJSON());
        });
      });
      return tableData;
    },
    addVersion: function (event) {
      var newVersion = this.model.clone();
      newVersion.unset('versions');
      newVersion.set({
        answers: this.model.get('answers')
      });
      app.test.set('versions', newVersion, {remove: false});
    },
    deleteVersion: function(event) {
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
      this.model.set('open', true);
    },
    preview: function(event) {
      console.log(event);
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
        text: this.model.get(this.options.attribute)
      };
      $(this.el).html(app.templates.edit(data));
    },
    updateData: function(event) {
      event.preventDefault();

      var value = $(event.target).closest('form').find('textarea').val();

      this.model.set(this.options.attribute, value);

      app.view.render();
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
      // $('#load-data').load('/post-new.php?post_type=kwps_question #post-body-content');
      $(this.el).html(app.templates.iframe());
      // $('#load-data').load('post-new.php?post_type=kwps_question #post-body-content');
      // $('#load-data').load('post-new.php?post_type=kwps_question #wpbody-content');
      $('#load-data').load('post-new.php?post_type=kwps_question #wp-content-wrap');
    },
    updateData: function(event) {
      app.view.render();
    }
  });


  if (typeof parentPost !== 'undefined') {
    app.test = new KwpsModel(parentPost);
  }
  console.log(app.test);
  if (app.test !== undefined) {
    app.view = new app.KwpsView({model: app.test});
  }
  
});
