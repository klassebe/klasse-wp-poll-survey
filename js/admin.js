jQuery(function ($) {



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
      _kwps_question: ""
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
    }]
  });

  VersionCollection = Backbone.Collection.extend({
    model: TestModel
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
      this.renderParentMatrix();
    },
    renderParentMatrix: function() {
      var template = Handlebars.compile($('#version_template').html());
      $(this.el).html(template(this.model.toJSON()));
      $('#tabs').tabs();

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
       conbso
    }
  });
  app.test = new TestModel(testData);
  $.each(versions, function(index, version) {
    version.fromTest = app.test;
    app.index = new TestModel(version);
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
  "_kwps_answers": [
    "antwoord optie 1",
    "antwoord optie 2"
  ],
  "_kwps_view_count": "0"
}

var versions = [
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
    "_kwps_answers": [
      "antwoord optie 1",
      "antwoord optie 2"
    ],
    "_kwps_view_count": "0"
  },
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
    "_kwps_answers": [
      "antwoord optie 1",
      "antwoord optie 2"
    ],
    "_kwps_view_count": "0"
  }
];
