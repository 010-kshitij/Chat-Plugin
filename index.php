<?php die("Forbidden"); ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
<style>
	.chat-window {
		width: 250px;
		border: 1px solid black;
	}
	
	.send-chat {
		background: blue;
		padding: 10px;
		margin: 0;
	}
	
	.chat-area {
		height: 200px;
		overflow-Y: scroll;
	}
	
	.info-bar {
		cursor: pointer;
		margin: 0;
		background: skyblue;
		padding: 5px;
		border-bottom: 1px solid black;
	}
	
	.info-bar h5 {
		margin: 0;
	}
	
	.sender {
		background: yellow;
		padding: 10px;
		margin: 10px;
	}
	
	.sender-arrow {
		display: inline-block;
		border: 10px solid yellow;
		border-top-color: transparent;
		border-left-color: transparent;
		border-bottom-color: transparent;
		width: 0;
		height: 0;
		position: relative;
		left: -30px;
	}
	
	ul#chat {
		list-style-type : none;
		position: absolute;
		bottom: 0;
		padding: 0;
		margin: 0;
	}
	
	ul#chat li {
		list-style-type : none;
		display: inline-block;
		padding: 0 5px;
	}
	
	.close-chat {
		float: right;
	}
</style>

<script
  src="http://code.jquery.com/jquery-2.2.4.min.js"
  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
  crossorigin="anonymous"></script>
  
<script>
	function ChatWindow(chatHeading = 'Chat') {
		// Misc var
		this.scrolled = true;
		
		// Elements
		this.chatArea;
		
		// Meta  Data
		this.chatHeading = chatHeading;
		this.show = function() {
			// Main Chat Window.
			var chatWindow = document.createElement('div');
			chatWindow.className = "chat-window";
			
			// Info Bar.
			var infoBar = document.createElement('div');
			infoBar.className = "info-bar";
			chatWindow.appendChild(infoBar);
			
			// Chat Window heading.
			var infoBarH5 = document.createElement('h5');
			infoBarH5.innerHTML = this.chatHeading
			infoBar.appendChild(infoBarH5);
			
			// Chat Window Close icon.
			var collapseIcon = document.createElement('span');
			collapseIcon.className = "close-chat";
			collapseIcon.innerHTML = '<i class="fa fa-window-close"></i>';
			infoBarH5.appendChild(collapseIcon);
			
			// Chat Area.
			this.chatArea = document.createElement('div');
			this.chatArea.className = "chat-area";
			chatWindow.appendChild(this.chatArea);
			
			// Chat Type Area to send messages.
			var chatType = document.createElement('div');
			chatType.className = "chat-type";
			chatWindow.appendChild(chatType);
			
			// Chat Type Area Form.
			var chatTypeForm = document.createElement('form');
			chatTypeForm.className = "send-chat";
			chatType.appendChild(chatTypeForm);
			
			// Chat Type Area Form Input text
			var chatTypeFormInputText = document.createElement('input');
			chatTypeFormInputText.type = "text";
			chatTypeFormInputText.name = "message";
			chatTypeFormInputText.placeholder = "Enter Message...";
			chatTypeForm.appendChild(chatTypeFormInputText);
			
			// Chat Type Area Form Input Submit
			var chatTypeFormInputSubmit = document.createElement('input');
			chatTypeFormInputSubmit.type = "submit";
			chatTypeFormInputSubmit.name = "submit-message";
			chatTypeFormInputSubmit.value = "Send";
			chatTypeForm.appendChild(chatTypeFormInputSubmit);
			
			// <li> element for ul#chat
			var chatWindowLi = document.createElement('li');
			chatWindowLi.appendChild(chatWindow);
			
			$("#chat").append(chatWindowLi);
		};
		this.loop = function() {
				setInterval(function(){
				$.ajax({
					url: "check-message.php",
					type: "GET",
					success: function(response) {
						response = JSON.parse(response);
						for(let i = 0; i < response.length; i++) {
							this.chatArea.append("<h6 class='sender'><span class='sender-arrow'></span>" + response[i].message + "</h6>");
							$.ajax({
								url: "change-message-status.php",
								type: "POST",
								data: {"sno" : response[i].sno},
								success: function() {
									scrolled = true;
								}
							})
						}
					}
				})
				if(scrolled) {
					$(".chat-area").animate({ scrollTop: $(this).height() }, "slow");
					scrolled = false;
					return false;
				}
			}, 1000);
		}
	}
</script>

<ul id="chat"></ul>

<script>
	var chat = new ChatWindow('Window 1');
	chat.show();
	
	var chat1 = new ChatWindow('Window 2');
	chat1.show();
	
	var chat2 = new ChatWindow('Window 3');
	chat2.show();
	
	var chat3 = new ChatWindow('Window 4');
	chat3.show();
	
	var chat4 = new ChatWindow('Window 5');
	chat4.show();
</script>

<script>
	$(document).ready(function() {
		var scrolled = true; // Scroll to bottom check.
		// Checking message arrival after every 1 sec.
		setInterval(function(){
			$.ajax({
				url: "check-message.php",
				type: "GET",
				success: function(response) {
					response = JSON.parse(response);
					for(let i = 0; i < response.length; i++) {
						$(".chat-area").append("<h6 class='sender'><span class='sender-arrow'></span>" + response[i].message + "</h6>");
						$.ajax({
							url: "change-message-status.php",
							type: "POST",
							data: {"sno" : response[i].sno},
							success: function() {
								scrolled = true;
							}
						})
					}
				}
			})
			if(scrolled) {
				$(".chat-area").animate({ scrollTop: $(this).height() }, "slow");
				scrolled = false;
				return false;
			}
		}, 1000);
		
		// Send Message on submit.
		$(".send-chat").submit(function(event){
			event.preventDefault();
			var message = $(this).children("input[type=text]").val();
			$.ajax({
				url: "send-message.php",
				data: {"message" : message},
				type: "POST",
				success: function() {
					scrolled = true;
				}
			})
			if(scrolled) {
				$(".chat-area").animate({ scrollTop: $(this).height() }, "slow");
				scrolled = false;
				return false;
			}
		});
		
		// Collapse Chat Window on clicking info bar
		$(".info-bar").click(function() {
			var parent = $(this).parent();
			var chatArea = parent.children(".chat-area");
			var chatType = parent.children(".chat-type");
			
			chatArea.toggle();
			chatType.toggle();
		});
	});
</script>