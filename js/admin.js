jQuery(function ($) {
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
    versions = parseInt(versions);
    return versions + 2;
  });

  Handlebars.registerHelper('setIndex', function(value){
    this.index = Number(value + 1); //I needed human readable index, not zero based
  });

  var app = {};


  app.AnswerModel = Backbone.AssociatedModel.extend({

  });

  TestModel = Backbone.AssociatedModel.extend({
    defaults: {
      ID: '',
      post_author: 0,
      post_date: "",
      post_title: "",
      post_status: "publish",
      post_modified: "",
      post_parent: 0,
      post_type: "kwps_poll",
      _kwps_intro: "",
      _kwps_outro: "",
      _kwps_question: "",
      open: false,
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
    }]
  });


  VersionCollection = Backbone.Collection.extend({
    model: TestModel
  });

  AnswerCollection = Backbone.Collection.extend({
    model: app.AnswerModel
  });

  app.TestView = Backbone.View.extend({
    el: '#kwps_test',
    initialize: function () {
      this.inputPostTitle = $('#post_title');
      this.render();
      _.bindAll(this, 'render');
      this.model.bind('change', this.render);
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
      'click .add-answer': 'addAnswer'
    },
    render: function () {
      this.inputPostTitle.val(this.model.get('post_title'));
      var template = Handlebars.compile($('#version_template').html());
      var data = this.model.toJSON();
      data.table = this.prepareTable();
      $(this.el).html(template(data));
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
    addAnswer: function(event) {
      var answer = new app.AnswerModel();
      app.test.get('answers').add(answer);

      this.model.get('versions').each(function(version) {
        var newAnswer = new app.AnswerModel();
        version.get('answers').add(newAnswer);
      });

      this.render();
    },
    showActions: function(event) {
      $(event.target).find(".actions").show();
    },
    hideActions: function(event) {
      $(event.target).find(".actions").hide();
    },
    toggleDetails: function(event) {
      this.model.set('open', true);
    }
  });
  app.test = new TestModel(testData);

  app.view = new app.TestView({model: app.test});
});

var testData =
{
  ID: "18",
  post_author: 1,
  post_date: "2014-03-24 16:01:35",
  post_title: "Dit is een poll",
  post_status: "publish",
  post_modified: "2014-03-25 9:14:36",
  post_parent: 0,
  post_type: "kwps_poll",
  _kwps_intro: "Dit is een intro",
  _kwps_outro: "Dit is een outro",
  _kwps_question: "Hier staat de vraag",
  _kwps_view_count: "0",
  versions: [
    {
      "ID": "19",
      "post_author": 1,
      "post_date": "2014-03-24 16:01:35",
      "post_title": "Dit is een poll",
      "post_status": "publish",
      "post_modified": "2014-03-25 9:14:36",
      "post_parent": 0,
      "post_type": "kwps_poll",
      "_kwps_intro": "Dit is een intro",
      "_kwps_outro": "Dit is een outro",
      "_kwps_question": "Hier staat de vraag",
      "_kwps_view_count": "0",
      answers: [
        {
          "post_id": 19,
          "answer_option": "Sure"
        },
        {
          "post_id": 19,
          "answer_option": "Maybe"
        }
      ]
    },
    {
      "ID": "20",
      "post_author": 1,
      "post_date": "2014-03-24 16:01:35",
      "post_title": "Dit is een poll",
      "post_status": "publish",
      "post_modified": "2014-03-25 9:14:36",
      "post_parent": 0,
      "post_type": "kwps_poll",
      "_kwps_intro": "Dit is een intro",
      "_kwps_outro": "Dit is een outro",
      "_kwps_question": "Hier staat de vraag",
      "_kwps_view_count": "0",
      answers: [
        {
          "post_id": 20,
          "answer_option": "Ok my way or the highway"
        },
        {
          "post_id": 20,
          "answer_option": "Highway please"
        }
      ]
    }
  ],
  answers: [
    {
      "post_id": 18,
      "answer_option": "Yes"
    },
    {
      "post_id": 18,
      "answer_option": "No"
    }
  ]
};
