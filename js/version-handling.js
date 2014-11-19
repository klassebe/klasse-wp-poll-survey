jQuery(function($) {
    $('#create_answer_option').click(function(event) {
        var current = $(event.currentTarget);

        var questionGroupIndex = current.attr("questionGroupIndex");
        var questionIndex = current.attr("questionIndex");

        var divToClone = current.parent().children('div.kwps-answer-option:visible').last();
        var clonedDiv = divToClone.clone();

        var kwpsSortOrderField = clonedDiv.children("input[name*='_kwps_sort_order']");

        var newAnswerOptionIndex = parseInt(kwpsSortOrderField.attr("value")) + 1;
        newAnswerOptionIndex = newAnswerOptionIndex.toString();

        var clonedId = clonedDiv.attr("id");
        clonedDiv.attr("id", clonedId.slice(0, -1) + newAnswerOptionIndex);

        var sectionTitleNumer = clonedDiv.children("h3").children("span");
        sectionTitleNumer.html(newAnswerOptionIndex);
        console.log(sectionTitleNumer.html());

        var namePrefix = "question_groups[" + questionGroupIndex +
            "][questions][" + questionIndex +"][answer_options][" + newAnswerOptionIndex +"]";

        var idField = clonedDiv.children("input[name*='ID']");
        idField.attr("value", "");
        idField.attr("name", namePrefix + "[ID]");


        kwpsSortOrderField.attr("value", newAnswerOptionIndex);
        kwpsSortOrderField.attr("name", namePrefix + "[_kwps_sort_order]");

        var postStatusField = clonedDiv.children("input[name*='post_status']");
        postStatusField.attr("name", namePrefix + "[post_status]");

        var postContentField = clonedDiv.children("input[name*='post_content']");
        postContentField.attr("name", namePrefix + "[post_content]");

        clonedDiv.insertAfter('#' +divToClone.attr("id"));
    });

});