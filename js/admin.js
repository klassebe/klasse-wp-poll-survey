jQuery(function ($) {



  var app = {};

  app.QuestionCollection = Backbone.Collection.extend({
    model: app.TestModel
  });

  app.TestModel = Backbone.Model.extend({
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
      versions: new app.QuestionCollection()
    },
    parse: function (response) {
      console.log('hierin');
      response.versions = new app.QuestionCollection();
      return response;
    }
  });
  app.test = new app.TestModel();


  app.TestView = Backbone.View.extend({
    el: '#kwps_test',
    initialize: function () {
      this.inputPostTitle = $('#post_title');
      this.listenTo(this.model, 'all', this.render);

      app.test.set(testData);
    },
    events: {
      "click #add-version": "addVersion"
    },
    render: function () {
      this.inputPostTitle.val(this.model.get('post_title'));
      this.renderParentMatrix();
      console.log(app.test.get('versions').length);
    },
    renderParentMatrix: function() {
      var template = Handlebars.compile($('#version_template').html());
      $(this.el).html(template(this.model.toJSON()));
      $('#tabs').tabs();

    },
    addVersion: function (event) {
      console.log(event);
      var version = new app.TestModel();
      app.test.get('versions').push(version);
    }
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
  "_kwps_view_count": "0",
  "versions": [
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
  ]
}
