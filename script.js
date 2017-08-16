function ChatWindow(chatHeading, sender_id, sender_type, receiver_id, receiver_type) {
    // Instance Variable
    var instance = this;

    // Meta Data
    instance.chatHeading = chatHeading;
    instance.sender_id = sender_id;
    instance.sender_type = sender_type;
    instance.receiver_id = receiver_id;
    instance.receiver_type = receiver_type;

    // Misc var
    instance.scrolled = true;

    // Elements
    // Main Chat Window.
    instance.chatWindow = document.createElement('div');
    instance.chatWindow.className = "chat-window";

    // Info Bar.
    instance.infoBar = document.createElement('div');
    instance.infoBar.className = "info-bar";
    instance.chatWindow.appendChild(instance.infoBar);

    // Chat Window heading.
    instance.infoBarH5 = document.createElement('h5');
    instance.infoBarH5.innerHTML = instance.chatHeading;
    instance.infoBar.appendChild(instance.infoBarH5);

    // Chat Window Close icon.
    instance.closeIcon = document.createElement('span');
    instance.closeIcon.className = "close-chat";
    instance.closeIcon.innerHTML = '<i class="fa fa-window-close"></i>';
    instance.infoBarH5.appendChild(instance.closeIcon);

    // Chat Area.
    instance.chatArea = document.createElement('div');
    instance.chatArea.className = "chat-area";
    instance.chatWindow.appendChild(instance.chatArea);

    // Chat Type Area to send messages.
    instance.chatType = document.createElement('div');
    instance.chatType.className = "chat-type";
    instance.chatWindow.appendChild(instance.chatType);

    // Chat Type Area Form.
    instance.chatTypeForm = document.createElement('form');
    instance.chatTypeForm.className = "send-chat";
    instance.chatType.appendChild(instance.chatTypeForm);

    // Chat Type Area Form Input text
    instance.chatTypeFormInputText = document.createElement('input');
    instance.chatTypeFormInputText.type = "text";
    instance.chatTypeFormInputText.autocomplete = "off";
    instance.chatTypeFormInputText.name = "message";
    instance.chatTypeFormInputText.placeholder = "Enter Message...";
    instance.chatTypeForm.appendChild(instance.chatTypeFormInputText);

    // Chat Type Area Form Input Submit
    instance.chatTypeFormInputSubmit = document.createElement('input');
    instance.chatTypeFormInputSubmit.type = "submit";
    instance.chatTypeFormInputSubmit.name = "submit-message";
    instance.chatTypeFormInputSubmit.value = "Send";
    instance.chatTypeForm.appendChild(instance.chatTypeFormInputSubmit);

    // <li> element for ul#chat
    instance.chatWindowLi = document.createElement('li');
    instance.chatWindowLi.id = "chat" + receiver_id;
    instance.chatWindowLi.appendChild(instance.chatWindow);

    $("#chat").append(instance.chatWindowLi);


    // Methods
    // Get previous Messages.
    instance.listPreviousMessages = function () {
        $.ajax({
            url: "../chat/check-previous-message.php",
            type: "POST",
            data: {"receiver_id": instance.sender_id, "receiver_type": instance.sender_type, "sender_id": instance.receiver_id, "sender_type": instance.receiver_type},
            success: function (response) {
                response = JSON.parse(response);
                for (var i = 0; i < response.length; i++) {
                    if((response[i].receiver_id == instance.receiver_id) && (response[i].receiver_type == instance.receiver_type)) {
                        $(instance.chatArea).append("<h6 class='sender'><span class='sender-arrow'></span>" + response[i].message + "</h6>");
                    }
                    else {
                        $(instance.chatArea).append("<h6 class='receiver'><span class='receiver-arrow'></span>" + response[i].message + "</h6>");
                    }
                    $(instance.chatWindowLi).show();
                    $(instance.chatArea).scrollTop(function() { return this.scrollHeight; });
                    instance.scrolled = true;
                }
            }
        })
        if (instance.scrolled == false) {
            // $(instance.chatArea).animate({scrollTop: $(this).height()}, "slow");
            $(instance.chatArea).scrollTop(function() { return this.scrollHeight; });
            $(instance.chatTypeFormInputText).val("");
            instance.scrolled = false;
            return false;
        }
    }

    // Check for Message arrival.
    instance.loop = function () {
        setInterval(function () {
            $.ajax({
                url: "../chat/check-message.php",
                type: "POST",
                data: {"receiver_id": instance.sender_id, "receiver_type": instance.sender_type, "sender_id": instance.receiver_id, "sender_type": instance.receiver_type},
                success: function (response) {
                    response = JSON.parse(response);
                    for (var i = 0; i < response.length; i++) {
                        $(instance.chatArea).append("<h6 class='receiver'><span class='receiver-arrow'></span>" + response[i].message + "</h6>");
                        $(instance.chatWindowLi).show();
                        $(instance.chatArea).scrollTop(function() { return this.scrollHeight; });
                        $.ajax({
                            url: "../chat/change-message-status.php",
                            type: "POST",
                            data: {"sno": response[i].sno},
                            success: function (response) {
                                console.log(response);
                                instance.scrolled = true;
                            }
                        })
                    }
                }
            })
            if (instance.scrolled == false) {
                $(instance.chatArea).animate({scrollTop: $(this).height()}, "slow");
                $(instance.chatTypeFormInputText).val("");
                instance.scrolled = false;
                return false;
            }
        }, 1000);
    }

    // Send Message on submit.
    $(instance.chatTypeForm).submit(function (event) {
        event.preventDefault();
        var message = $(this).children("input[type=text]").val();
        if(message != '') {
            $.ajax({
                url: "../chat/send-message.php",
                data: {"message": message, "sender_id": instance.sender_id, "sender_type": instance.sender_type, "receiver_id": instance.receiver_id, "receiver_type": instance.receiver_type},
                type: "POST",
                success: function (response) {
                    $(instance.chatArea).append("<h6 class='sender'><span class='sender-arrow'></span>" + response + "</h6>");
                    $(instance.chatTypeFormInputText).val("");
                    $(instance.chatArea).scrollTop(function() { return this.scrollHeight; });
                    instance.scrolled = true;
                }
            })
        }
        if (instance.scrolled == false) {
            $(instance.chatArea).animate({scrollTop: $(this).height()}, "slow");
            instance.scrolled = false;
            return false;
        }
    });

    // Collapse Chat Window on clicking info bar
    $(instance.infoBar).click(function () {
        $(instance.chatArea).toggle();
        $(instance.chatType).toggle();
    });

    // Destroy chat window on closing.
    $(instance.closeIcon).click(function () {
        $(instance.chatWindowLi).hide();
    });
}
