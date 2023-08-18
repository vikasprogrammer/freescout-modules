/**
 * Module's JavaScript.
 */

function initAutoSignature()
{
	$(document).ready(function() {
		
		summernoteInit('.saved-reply-text', {minHeight: 250, insertVar: true});

		// Update Auto Signature
		$('.saved-reply-save').click(function(e) {
			var saved_reply_id = $(this).attr('data-saved_reply_id');
			var name = $('#saved-reply-'+saved_reply_id+' :input[name="name"]').val();
			var button = $(this);
	    	button.button('loading');
			fsAjax({
					action: 'update',
					saved_reply_id: saved_reply_id,
					name: name,
					text: $('#saved-reply-'+saved_reply_id+' :input[name="text"]').val()
				}, 
				laroute.route('mailboxes.auto_signature.ajax'), 
				function(response) {
					if (typeof(response.status) != "undefined" && response.status == 'success' &&
						typeof(response.msg_success) != "undefined")
					{
						showFloatingAlert('success', response.msg_success);
						$('#saved-reply-'+saved_reply_id+' .panel-title a:first span:first').text(name);
					} else {
						showAjaxError(response);
					}
					button.button('reset');
					loaderHide();
				}
			);
		});

		// Delete Auto Signature
		$(".sr-delete-trigger").click(function(e){
			var button = $(this);

			showModalConfirm(Lang.get("messages.confirm_delete_saved_reply"), 'sr-delete-ok', {
				on_show: function(modal) {
					var saved_reply_id = button.attr('data-saved_reply_id');
					modal.children().find('.sr-delete-ok:first').click(function(e) {
						button.button('loading');
						modal.modal('hide');
						fsAjax(
							{
								action: 'delete',
								saved_reply_id: saved_reply_id
							}, 
							laroute.route('mailboxes.auto_signature.ajax'), 
							function(response) {
								showAjaxResult(response);
								button.button('reset');
								$('#saved-reply-'+saved_reply_id).remove();
							}
						);
					});
				}
			}, Lang.get("messages.delete"));
			e.preventDefault();
		});

		if ($('#saved-replies-index').length) {
			sortable('#saved-replies-index', {
			    handle: '.handle',
			    //forcePlaceholderSize: true 
			})[0].addEventListener('sortupdate', function(e) {
			    // ui.item contains the current dragged element.
			    var saved_replies = [];
			    $('#saved-replies-index > .panel').each(function(idx, el){
				    saved_replies.push($(this).attr('data-saved-reply-id'));
				});
				fsAjax({
						action: 'update_sort_order',
						saved_replies: saved_replies,
					}, 
					laroute.route('mailboxes.auto_signature.ajax'), 
					function(response) {
						showAjaxResult(response);
					}
				);
			});
		}

	});
}

// Create saved reply
function initNewAutoSignature(jmodal)
{
	$(document).ready(function(){
		// Show text
		summernoteInit('.modal-dialog .new-saved-reply-editor:visible:first textarea:first', {minHeight: 250, insertVar: true});

		// Process save
		$('.modal-content .new-saved-reply-save:first').click(function(e) {
			var button = $(this);
	    	button.button('loading');
	    	var name = $(this).parents('.modal-content:first').children().find(':input[name="name"]').val();
	    	var text = $(this).parents('.modal-content:first').children().find(':input[name="text"]').val();
			fsAjax({
					action: 'create',
					mailbox_id: getGlobalAttr('mailbox_id'),
					from_reply: getGlobalAttr('conversation_id'),
					name: name,
					text: text
				}, 
				laroute.route('mailboxes.auto_signature.ajax'), 
				function(response) {
					if (typeof(response.status) != "undefined" && response.status == 'success' &&
						typeof(response.id) != "undefined" && response.id)
					{

						if (typeof(response.msg_success) != "undefined" && response.msg_success) {
							// Show alert (in conversation)
							jmodal.modal('hide');
							showFloatingAlert('success', response.msg_success);
							loaderHide();

							// Add newly created saved reply to the list
							var li_html = '<li><a href="#" data-value="'+response.id+'">'+htmlEscape(name)+'</a></li>';
							$('.form-reply:first:visible .dropdown-saved-replies:first').children('li:last').prev().before(li_html);
						} else {
							// Reload page (in saved replies list)
							window.location.href = '';
						}
					} else {
						showAjaxError(response);
						loaderHide();
						button.button('reset');
					}
				}
			);
		});
	});
}

// Display modal and show reply text
function showSaveThisReply(jmodal)
{
	// Show text
	$('.modal-dialog .new-saved-reply-editor:visible:first textarea[name="text"]:first').val(getReplyBody());
	initNewAutoSignature(jmodal);
}