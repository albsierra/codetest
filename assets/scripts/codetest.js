/*Main Javascript File*/
$(function(){
    $('.results-collapse.collapse').on('show.bs.collapse', function(){
        var rowDiv = $(this).parent();
        rowDiv.find(".fa.rotate").addClass("open");
        rowDiv.parent().addClass("selected-row");
    }).on('hide.bs.collapse', function(){
        var rowDiv = $(this).parent();
        rowDiv.find(".fa.rotate").removeClass("open");
        rowDiv.parent().removeClass("selected-row");
    });

    $("#importModal").on("hidden.bs.modal", function() {
        $(this).find('.results-collapse.collapse').collapse("hide");
        $(this).find("input[name='question']").prop("checked", false);
    });

    let $bodyContainer = $('#body_container');
    let $navbar = $('#tsugi_tool_nav_bar');
    let hasNavbar = $navbar.length > 0;
    let navBarHeight = hasNavbar ? 62 : 0;
    
    $bodyContainer.get(0).style.setProperty("--navbarHeight", `${navBarHeight}px`);
});

global.confirmDeleteQuestion = function() {
    return confirm("Are you sure you want to delete this question? This action cannot be undone.");
}

global.confirmDeleteQuestionBlank = function(questionId) {
    if ($("#questionTextInput"+questionId).val().trim().length < 1) {
        return confirm("Saving this question with blank text will delete this question. Are you sure you want to delete this question? This action cannot be undone.");
    } else {
        return true;
    }
}

global.importLtiContexts = function(i = 0, object = 'test') {
    if (object == 'test') {
        $.ajax({
            type: "GET",
            url: "actions/import/ImportLtiContexts.php?page=" + i + "&" + _TSUGI.ajax_session,
            success: function (data) {
                $('.import-body').html(data);
                $('#buttonImport').attr('onclick', "document.getElementById('importForm').submit();");
                
                if ($('#importModal').is(':hidden')) {
                    //If question tab is active, it deactivates and test tab is activated
                    if ($('#li-questions').addClass('active')) {
                        $('#li-test').addClass('active');
                        $('#li-questions').removeClass('active');
                        $('#tab-test').addClass('active in');
                        $('#tab-question').removeClass('active in');
                    }
                    // Display Modal
                    $('#importModal').modal('show');
                }
            },
            error: function (data) {
                console.error(data.responseText);
            }
        });

    } else if (object = "question") {
        $.ajax({
            type: "GET",
            url: "actions/import/ImportLtiContextsQuestions.php?page=" + i + "&" + _TSUGI.ajax_session,
            success: function (data) {
                $('.import-body-questions').html(data);
                $('#buttonImport').attr('onclick', "document.getElementById('importQuestionsForm').submit();");
            },
            error: function (data) {
                console.error(data.responseText);
            }
        });
    }
}

global.updateDate = function(ty) {
    var date = new Date(ty);
    $("#modified").text(formatDate(date));
}

global.importLtiContextsPage = function(i = 0, object='test') {
    var object;
    var body;
    if ($("#tab-test").hasClass("active")) {
        object = "test";
        body = ".import-body";
    } else if ($("#tab-question").hasClass("active")) {
        object = "question";
        body = ".import-body-questions";
    }
    $.ajax({
        type: "GET",
        url: "actions/import/ImportLtiContextsValue.php?page=" + i + "&action=page&object="+object+"&" + _TSUGI.ajax_session,
        success: function (data) {
            $(body).html(data);
        },
        error: function (data) {
            console.error(data.responseText);
        }
    });
}

global.importLtiContextsButtons = function(value) {
    var object;
    var body;
    
    //body is used to replace the correct element
    if ($("#tab-test").hasClass("active")) {
        object = "test";
        body = ".import-body";
    } else if ($("#tab-question").hasClass("active")) {
        object = "question";
        body = ".import-body-questions";
    }

    $.ajax({
        type: "GET",
        url: "actions/import/ImportLtiContextsValue.php?value=" + value + "&action=add&object=" + object + "&" + _TSUGI.ajax_session,
        success: function (data) {
            $(body).html(data);
        },
        error: function (data) {
            console.error(data.responseText);
        }
    });
}

global.deleteTag = function(value) {
    var object;
    var body;
    
    //body is used to replace the correct element
    if ($("#tab-test").hasClass("active")) {
        object = "test";
        body = ".import-body";
    } else if ($("#tab-question").hasClass("active")) {
        object = "question";
        body = ".import-body-questions";
    }

    $.ajax({
        type: "GET",
        url: "actions/import/ImportLtiContextsValue.php?value=" + value + "&action=delete&object=" + object + "&" + _TSUGI.ajax_session,
        success: function (data) {
            $(body).html(data);
        },
        error: function (data) {
            console.error(data.responseText);
        }
    });
}

global.importMains = function(contextId) {
    
    $.ajax({
        type: "GET",
        url: "actions/import/ImportMains.php?contextId=" + contextId + "&" + _TSUGI.ajax_session,
        success: function(data) {
            $('#site' + contextId).html(data);
            $('#site' + contextId + '.collapse').collapse();
        },
        error: function(data) {
            console.error(data.responseText);
        }
    });
}

global.showTestInfo = function(testId) {
    element = $('#divTest' + testId);

    if ($(element).is(':visible')) {
        $(element).hide();
    } else if ($(element).is(':hidden')) {
        $(element).show();
    }

}

global.showQuestions = function(questionId) {
    element = $('#main' + questionId);

    if ($(element).is(':visible')) {
        $(element).hide();
    } else if ($(element).is(':hidden')) {
        $(element).show();
    }
}


global.importQuestions = function(questionId, testId) {
    $.ajax({
        type: "GET",
        url: "actions/import/ImportQuestions.php?" + _TSUGI.ajax_session,
        data: {
            questionId: questionId,
            testId: testId
        },
        success: function (data) {
            $('#main' + questionId).html(data);
        },
        error: function (data) {
            console.error(data.responseText);
        }
    });
}

global.getAnswersFromQuestion = function(questionId) {
    $.ajax({
        type: "GET",
        url: "actions/answers/getAnswersFromQuestion.php?questionId=" + questionId + "&" + _TSUGI.ajax_session,
        success: function (data) {
            $('#responses' + questionId).html(data);
            $('#responses' + questionId + '.collapse').collapse();
        },
        error: function (data) {
            console.error(data.responseText);
        }
    });
}

global.editQuestionText = function(questionId) {
    var questionText = $("#questionText" + questionId);
    questionText.hide();
    $("#questionDeleteAction" + questionId).hide();
    $("#questionEditAction" + questionId).hide();
    $("#questionReorderAction" + questionId).hide();

    var theForm = $("#questionTextForm" + questionId);

    editor = getCKEditor("questionTextInput" + questionId);
    theForm.show();
    theForm.find('#questionTextInput' + questionId).focus()
            .off("keypress").on("keypress", function (e) {
        if (e.which === 13) {
            e.preventDefault();
            if ($('#questionTextInput' + questionId).val().trim() === '') {
                if (confirmDeleteQuestionBlank(questionId)) {
                    // User entered blank question text and wants to delete.
                    deleteQuestion(questionId, true);
                }
            } else {
                // Still has text in question. Save it.
                $.ajax({
                    type: "POST",
                    url: theForm.prop("action"),
                    data: theForm.serialize(),
                    success: function (data) {
                        questionText.text($('#questionTextInput' + questionId).val());
                        questionText.show();
                        $("#questionDeleteAction" + questionId).show();
                        $("#questionEditAction" + questionId).show();
                        $("#questionReorderAction" + questionId).show();
                        $("#questionSaveAction" + questionId).hide();
                        $("#questionCancelAction" + questionId).hide();
                        theForm.hide();
                        $("#flashmessages").html(data.flashmessage);
                        setupAlertHide();
                    }
                });
            }
        }
    });
    $("#questionSaveAction" + questionId).show()
            .off("click").on("click", function (e) {
        updateCKeditorElements();
        if (editor)
            editor.destroy();
        if ($('#questionTextInput' + questionId).val().trim() === '') {
            if (confirmDeleteQuestionBlank(questionId)) {
                // User entered blank question text and wants to delete.
                deleteQuestion(questionId, true);
            }
        } else {
            // Still has text in question. Save it.
            $.ajax({
                type: "POST",
                url: theForm.prop("action"),
                data: theForm.serialize() + '&' + _TSUGI.ajax_session,
                success: function (data) {
                    questionText.html($('#questionTextInput' + questionId).val());
                    questionText.show();
                    $("#questionDeleteAction" + questionId).show();
                    $("#questionEditAction" + questionId).show();
                    $("#questionReorderAction" + questionId).show();
                    $("#questionSaveAction" + questionId).hide();
                    $("#questionCancelAction" + questionId).hide();
                    theForm.hide();
                    $("#flashmessages").html(data.flashmessage);
                    setupAlertHide();
                }
            });
        }
    });

    $("#questionCancelAction" + questionId).show()
            .off("click").on("click", function (e) {
        var theText = $("#questionText" + questionId);
        theText.show();
        theForm.hide();
        $("#questionTextInput" + questionId).val(theText.text());
        $("#questionDeleteAction" + questionId).show();
        $("#questionEditAction" + questionId).show();
        $("#questionReorderAction" + questionId).show();
        $("#questionSaveAction" + questionId).hide();
        $("#questionCancelAction" + questionId).hide();
    });
}

global.editTitleText = function() {
    $("#toolTitle").hide();
    var titleForm = $("#toolTitleForm");
    titleForm.show();
    titleForm.find("#toolTitleInput").focus()
            .off("keypress").on("keypress", function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: titleForm.prop("action"),
                data: titleForm.serialize(),
                success: function (data) {
                    $(".title-text-span").text($("#toolTitleInput").val());
                    if ($("#mainTypeSelect") && $("#mainTypeSelect")[0]) {
                        $(".mainType-text-span").text($("#mainTypeSelect")[0].options[$("#mainTypeSelect")[0].value].label);
                    }
                    var titleText = $("#toolTitle");
                    titleText.show();
                    titleForm.hide();
                    $("#toolTitleCancelLink").hide();
                    $("#toolTitleSaveLink").hide();
                    $("#flashmessages").html(data.flashmessage);
                    setupAlertHide();
                }
            });
        }
    });
    $("#toolTitleSaveLink").show()
            .off("click").on("click", function (e) {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: titleForm.prop("action"),
            data: titleForm.serialize(),
            success: function (data) {
                $(".title-text-span").text($("#toolTitleInput").val());
                if ($("#mainTypeSelect").length > 0) {
                    let value = $("#mainTypeSelect")[0].value;
                    let options = $("#mainTypeSelect")[0].options;
                    $(".mainType-text-span").text(options[value].label);
                }
                var titleText = $("#toolTitle");
                titleText.show();
                titleForm.hide();
                $("#toolTitleCancelLink").hide();
                $("#toolTitleSaveLink").hide();
                $("#flashmessages").html(data.flashmessage);
                setupAlertHide();
            }
        });
    });
    $("#toolTitleCancelLink").show()
            .off("click").on("click", function (e) {
        var titleText = $("#toolTitle");
        titleText.show();
        titleForm.hide();
        $("#toolTitleInput").val($(".title-text-span").text());
        $("#toolTitleCancelLink").hide();
        $("#toolTitleSaveLink").hide();
    });
}

//unused?
global.moveQuestionUp = function(questionId, testId) {
    $.ajax({
        type: "POST",
        url: "actions/ReorderQuestion.php?" + _TSUGI.ajax_session,
        dataType: 'text',
        data: {
            question_id: questionId,
            test_id: testId
        },
        success: function (data) {
            var theQuestionMoved = $("#questionRow" + questionId);
            theQuestionMoved.hide();
            var currentNumber = theQuestionMoved.data("question-number");
            if (currentNumber === 1) {
                // Move to bottom
                $("#newQuestionRow").before(theQuestionMoved);
            } else {
                // Move up one
                theQuestionMoved.prev().before(theQuestionMoved);
            }
            // Fix up question numbers
            var questionNum = 1;
            $(".question-number").each(function () {
                $(this).text(questionNum + ".");
                $(this).parent().data("question-number", questionNum);
                questionNum++;
            });

            theQuestionMoved.fadeIn("fast");

            $("#flashmessages").html(data.flashmessage);
            setupAlertHide();
        }
    });
}

global.answerQuestion = function(questionId, questionNum) {
    var answerForm = $("#answerForm" + questionId);
    $.ajax({
        type: "POST",
        url: answerForm.prop("action"),
        data: answerForm.serialize() + '&questionNum=' + questionNum + '&' + _TSUGI.ajax_session,
        success: function (data) {
            
            //If the answer is not empty and it is the first time it has been answered, the feedback modal opens
            if (data.answer_content) {
                if (!data.exists) {
                    $('#feedbackModal' + questionId).modal('show');
                }
            }
            
            //If the answer is correct, change the hand down to the hand up
            if(data.success){
                $("#answerForm"+questionId).hide();
                $("#answerIcon"+questionId).removeClass('fa-thumbs-down');
                $("#answerIcon"+questionId).addClass('fa-thumbs-up');
                $("#listIcon"+questionId).removeClass('fa-thumbs-down');
                $("#listIcon"+questionId).addClass('fa-thumbs-up');
            }
            var date =new Date();
            $("#answerSavedText").text(data.answerText);
            $("#modified").text(formatDate(date));
            $("#answerText" + questionId).val("");
            $("#flashmessages").html(data.flashmessage);
        },
        error: function (data) {
            alert('ERROR');
        }
    });
}

global.formatDate = function(dateVal) {
    var newDate = new Date(dateVal);
    var sMonth = padValue(newDate.getMonth() + 1);
    var sDay = padValue(newDate.getDate());
    var sYear = newDate.getFullYear();
    var sHour = newDate.getHours();
    var sMinute = padValue(newDate.getMinutes());
    var sAMPM = "AM";
    var iHourCheck = parseInt(sHour);

    if (iHourCheck > 12) {
        sAMPM = "PM";
        sHour = iHourCheck - 12;
    }
    else if (iHourCheck === 0) {
        sHour = "12";
    }
    sHour = padValue(sHour);

    return sMonth + "/" + sDay + "/" + sYear + " | " + sHour + ":" + sMinute + " " + sAMPM;
}

global.padValue = function(value) {
    return (value < 10) ? "0" + value : value;
}

global.setupAlertHide = function() {
    // On load hide any alerts after 3 seconds
    /*setTimeout(function() {
     $(".alert-banner").slideUp();
     }, 3000);*/
}

global.getCKEditor = function(elementName) {
    let editor;
    for (var i in CKEDITOR.instances) {
        if (elementName == CKEDITOR.instances[i].name)
            editor = CKEDITOR.instances[i];
    }
    if (!editor)
        editor = CKEDITOR.replace(elementName);
    return editor;
}

global.updateCKeditorElements = function() {
    for (var i in CKEDITOR.instances) {
        CKEDITOR.instances[i].updateElement();
    }
}

global.resetCKeditorElements = function() {
    for (var i in CKEDITOR.instances) {
        let element = $('#' + i);
        if(element && element[0]) {
            CKEDITOR.instances[i].setData(element[0].value);
        }
    }
}

global.resetForm = function($form) {
    $form.find('input:text, input:password, input:file, textarea').val(''); // agregar select
    $form.find('input:radio, input:checkbox')
        .removeAttr('checked').removeAttr('selected');
    resetCKeditorElements();
}

//This method calls the action that sends the feedback to the repository
global.sendFeedback = function(questionId) {
    var feedbackForm = $("#feedbackForm"+questionId);
    
    //url = actions/SendFeedback.php
    $.ajax({
        type: "POST",
        dataType: "text",
        url: feedbackForm.prop("action"),
        data: feedbackForm.serialize() + '&questionId='+questionId+'&' + _TSUGI.ajax_session,
        success: function(data) {
            $('#feedbackModal'+questionId).modal('hide');
            $('#feedbackForm'+questionId).trigger("reset");
            $("#flashmessages").html(data.flashmessage);
            setupAlertHide();
        },
        error: function(data){
            alert('ERROR');
        }
    });
}

//this method adds the keyword to the search parameters
global.keyword = function() {
    var keyword = $("#keywordText").val();
    if (keyword) {
        var object;
        var body;
        if ($("#tab-test").hasClass("active")) {
            object = "test";
            body = ".import-body";
        } else if ($("#tab-question").hasClass("active")) {
            object = "question";
            body = ".import-body-questions";
        }

        $.ajax({
            type: "GET",
            url: "actions/import/ImportLtiContextsValue.php?value=" + keyword + "&action=add&object=" + object + "&" + _TSUGI.ajax_session,
            success: function (data) {
                $(body).html(data);
            },
            error: function (data) {
                console.error(data.responseText);
            }
        });

    }
}

//this method adds the punctuation to the search parameters
global.score = function() {
    var score = $("#customRange1").val();
         var object;
        var body;
        if ($("#tab-test").hasClass("active")) {
            object = "test";
            body = ".import-body";
        } else if ($("#tab-question").hasClass("active")) {
            object = "question";
            body = ".import-body-questions";
        }
        
        //if the punctuation is 0 the parameter is cleared
        if(score==0){
            score="delete";
        }
    $.ajax({
        type: "GET",
        url: "actions/import/ImportLtiContextsValue.php?value=" + score + "&action=add&object=" + object +"&" + _TSUGI.ajax_session,
        success: function (data) {
            $(body).html(data);
        },
        error: function (data) {
            console.error(data.responseText);
        }
    });
}

global.deleteQuestion = function(questionId, skipconfirm = false) {
    $('#confirm').modal('show')
            .on('click', '#delete', function (e) {
                $.ajax({
                    type: "POST",
                    url: "actions/DeleteQuestion.php?" + _TSUGI.ajax_session,
                    dataType: "text",
                    data: {
                        question_id: questionId,
                    },
                    success: function (data) {
                        $("#questionRow" + questionId).remove();
                        // Fix up question numbers
                        var questionNum = 1;
                        $(".question-number").each(function () {
                            $(this).text(questionNum);
                            $(this).parent().data("question-number", questionNum);
                            questionNum++;
                        });
                        // Fix new question number
                        $("#newQuestionRow").data("question-number", questionNum);
                        $("#newQuestionNumber").text(questionNum + ".");

                        $("#flashmessages").html(data.flashmessage);
                        setupAlertHide();
                    },
                    error: function (data) {
                        alert("error");
                    }
                });
            })
            .on('click', '#cancel', function (e) {
                e.preventDefault();
                $('#confirm' + questionId).modal.model('close');
            });
}

//this method updates the question numbers
global.updateList = function(questionId, oldIndex, newIndex) {
    $.ajax({
        type: "POST",
        url: "actions/ReorderQuestion.php?" + _TSUGI.ajax_session,
        dataType: 'text',
        data: {
            questionId: questionId,
            oldIndex: oldIndex,
            newIndex: newIndex
        },
        success: function (data) {
            var questionNum = 1;
            $(".question-number").each(function () {
                $(this).text(questionNum+".-");
                $(this).parent().data("question-number", questionNum);
                questionNum++;
            });
            $("#flashmessages").html(data.flashmessage);
            setupAlertHide();
        },
        error: function (data) {
            alert(data);
        }
    });
    return false;
}


global.showCreateModal = function() {
    var language = $("#typeSelect").val();

    $.ajax({
        type: "GET",
        url: "actions/newQuestionForm.php?language=" + language + "&" + _TSUGI.ajax_session,
        success: function (data) {
            $('#createBody').html(data);
        },
        error: function (data) {
            console.error(data.responseText);
        }
    });
    $('#createModal').modal('show');
}

global.typeChange = function(){
     var language = $("#typeSelect").val();
 
     $.ajax({
        type: "GET",
        url: "actions/newQuestionForm.php?language=" +language+"&"+ _TSUGI.ajax_session,
        success: function(data) {
            $('#createBody').html(data);
        },
        error: function(data) {
            console.error(data.responseText);
        }
    });
   
}

global.showNewQuestionRow = function() {

    var theForm = $("#questionTextForm-1");
    var language = $("#typeSelect").val();
    var difficulty = $("#difficultySelect").val();
    updateCKeditorElements();
    title = $("#questionTitleText").val();

    if (title == "") {
        $("#questionTitleText").attr("placeholder", "Field Required");
        $("#questionTitleText").addClass("required");
    }  else {
        
        // $('#newQuestionRow').html("");
        $.ajax({
            type: "POST",
            dataType: "json",
            url: theForm.prop("action"),
            data: theForm.serialize() + '&type=' + language + '&difficulty=' +difficulty+'&' + _TSUGI.ajax_session,
            success: function (data) {
                resetForm(theForm);
                location.reload();
            },
            error: function (data) {
                console.error('FAIL');
                console.error(data);
            }
        });
    }
    $("#questionCancelAction-1").off("click").on("click", function (e) {
        $('#newQuestionRow').html("");
        if (editor)
            editor.destroy();
        addQuestionsSection.show();
    });

}

global.showImportQuestion = function() {
    var theForm = $("#importQuestionsForm");

    $('#newQuestionRow').html("");
    $.ajax({
        type: "POST",
        dataType: "json",
        url: theForm.prop("action"),
        data: theForm.serialize() + '&' + _TSUGI.ajax_session,
        success: function (data) {
            resetForm(theForm);
            location.reload();
        },
        error: function (data) {
            console.error(data);
        }
    });
}
