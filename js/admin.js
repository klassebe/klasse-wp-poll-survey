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
    console.log(this);
    this.index = Number(value + 1); //I needed human readable index, not zero based
  });

  var app = {};


  TestModel = Backbone.RelationalModel.extend({
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
      open: false
    },
    relations: [{
      type: Backbone.HasMany,
      key: 'versions',
      relatedModel: 'TestModel',
      collectionType: 'VersionCollection',
      reverseRelation: {
        key: 'fromTest',
        includeInJSON: true
      }
    },
    {
      type: Backbone.HasMany,
      key: 'answers',
      relatedModel: 'AnswerModel',
      collectionType: 'AnswerCollection',
      reverseRelation: {
        key: 'fromTest',
        includeInJSON: true
      }
    }]
  });

  AnswerModel = Backbone.RelationalModel.extend({

  });

  VersionCollection = Backbone.Collection.extend({
    model: TestModel
  });

  AnswerCollection = Backbone.Collection.extend({
    model: AnswerModel
  });

  app.TestView = Backbone.View.extend({
    el: '#kwps_test',
    initialize: function () {
      this.inputPostTitle = $('#post_title');
      this.render();
      _.bindAll(this, 'render');
      this.model.bind('change', this.render);
    },
    events: {
      'click #add-version': 'addVersion',
      'mouseenter td': 'showActions',
      'mouseleave td': 'hideActions',
      'click .toggle-details': 'toggleDetails'
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
      var newVersion = new TestModel(testData);
      app.test.set('versions', newVersion, {remove: false});
    },
    showActions: function(event) {
      console.log(event.target);
      $(event.target).find(".actions").show();
    },
    hideActions: function(event) {
      $(event.target).find(".actions").hide();
    },
    toggleDetails: function(event) {
      this.model.set('open', true);
       console.log(this.model);
    }
  });
  app.test = new TestModel(testData);

  $.each(versions, function(index, version) {
    version.fromTest = app.test;
    app.index = new TestModel(version);
  });

  $.each(answers, function(index, answer) {
    if(answer.post_id == app.test.get('ID')) {
      answer.fromTest = app.test;
      app.index = new AnswerModel(answer);
    }
  });

  $.each(app.test.get('versions').models, function(index, version) {
    var post_id = version.get('ID');
    $.each(answers, function(index, answer) {
      if(answer.post_id == post_id) {
        answer.fromTest = version;
        app.index = new AnswerModel(answer);
      }
    });
  });

  app.view = new app.TestView({model: app.test});
});

var testData =
{
  "ID": "18",
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
  "_kwps_view_count": "0"
}

var versions = [
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
    "_kwps_view_count": "0"
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
    "_kwps_view_count": "0"
  }
];

var answers = [
  {
    "post_id": 18,
    "answer_option": "Yes"
  },
  {
    "post_id": 18,
    "answer_option": "No"
  },
  {
    "post_id": 19,
    "answer_option": "Sure"
  },
  {
    "post_id": 19,
    "answer_option": "Maybe"
  },
  {
    "post_id": 20,
    "answer_option": "Ok my way or the highway"
  },
  {
    "post_id": 20,
    "answer_option": "Highway please"
  }
]
