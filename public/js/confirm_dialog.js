// We define a new jQuery function named 'confirm' that will create a dialog window
(function($){

    $.confirm = function(params){

        if($('#confirmOverlay').length){
            // A confirm is already shown on the page:
            return false;
        }

        var buttonHTML = '';
        $.each(params.buttons,function(name,obj){

            // Generating the markup for the buttons:

            //buttonHTML += '<a href="#" class="'+obj['class']+'">'+name+'<span></span></a>';
            buttonHTML += "<button type='button' class='"+obj['class']+"'><span class='glyphicon "+obj['glyph']+"'></span> "+obj['label']+"</button>";

            if(!obj.action){
                obj.action = function(){};
            }
        });

        if (!(typeof params.width === 'undefined')) {
            d_width = params.width;
            d_left_margin = Math.floor(d_width/2);      
            extra_style = ' style="width: "'+d_width+'px; margin-left: '+d_left_margin+'px"';
        } else {
            extra_style = '';
            }

        var markup = [
            '<div id="confirmOverlay">',
            '<div id="confirmBox"'+extra_style+'>',
            '<div style="text-align:right">',
            '<span class="glyphicon glyphicon-remove confirm-x"></span>',
            '</div>',
            '<h1 style="margin-top: 0px">',params.title,'</h1>',
            '<div id="confirmButtons">',
            buttonHTML,
            '</div></div></div>'
        ].join('');

        $(markup).hide().appendTo('body').fadeIn().center();

        $('#confirmBox h1').after(params.message);

        var buttons = $('#confirmBox .btn'),
            i = 0;

        $.each(params.buttons,function(name,obj){
            buttons.eq(i++).click(function(){

                // Calling the action attribute when a
                // click occurs, and hiding the confirm.

                obj.action();
                $.confirm.hide();
                return false;
            });
        });
        
        // Hide the dialog when x is pressed
        $('#confirmBox span.confirm-x').click(function(){
            $.confirm.hide();
        });
        
    }

    $.confirm.hide = function(){
        $('#confirmOverlay').fadeOut(function(){
            $(this).remove();
        });
    }

})(jQuery);

// A generic confirm dialog function 
// Defines a certain type of confirm dialog using the 'confirm' function defined above 
// We define what buttons there will be and what action will be taken for each button
// The parameters of the function include the dialog window title and the dialog's body
function assign_role_dialog(theTitle,theMessage){

    $.confirm({
            'title'	: theTitle,
            'message'	: theMessage,
            'buttons'	: {
                'Assign'	: {
                    'label'     : lang_array.assign,  
                    'class'	: 'btn btn-primary',
                    'glyph'     : 'glyphicon-check',
                    'action': function(){   // Τί πρέπει να γίνει αν επιλεχθεί το 'Yes'
			$('form#roleForm').submit();
                    }
                },
                'Cancel'	: {
                    'label'     : lang_array.cancel_role_assignment,
                    'class'	: 'btn btn-danger',
                    'action': function(){}  // Τί πρέπει να γίνει αν επιλεχθεί το 'No'
                }
            }
        });
}

// A dialog function for closing a ticket
function close_ticket_by_handler_dialog(ticket_id){

    $.confirm({
            'title'	: lang_array.close_ticket,
            'message'	: "<p style='margin:10px'>"+lang_array.close_reason+"<textarea id='reason' style='margin-top:5px; width:100%'></textarea></p>",
            'buttons'	: {
                'Canceled'	: {
                    'label'     : lang_array.cancel,
                    'class'	: 'btn btn-danger',
                    'glyph'     : 'glyphicon-remove',
                    'action': function(){   
                        block_reason = $('#reason').val();
			cancelTicket(ticket_id,block_reason);
                    }
                },
                'Rejected'	: {
                    'label'     : lang_array.reject,
                    'class'	: 'btn btn-danger',
                    'glyph'     : 'glyphicon-trash',
                    'action': function(){
                        block_reason = $('#reason').val();
                        rejectTicket(ticket_id,block_reason);
                    }  
                },
                'Served'	: {
                    'label'    : lang_array.served,
                    'class'	: 'btn btn-success',
                    'glyph'     : 'glyphicon-ok',
                    'action': function(){
                        block_reason = $('#reason').val();
                        serveTicket(ticket_id,block_reason);
                    }  
                },
                'Do Nothing'	: {
                    'label'     : lang_array.do_nothing,
                    'class'	: 'btn btn-default',
                    'action': function(){}  // Τί πρέπει να γίνει αν επιλεχθεί το 'No'
                }
            }
        });
}

// A dialog function for closing a blocked ticket
function close_blocked_ticket_by_handler_dialog(ticket_id){

    $.confirm({
            'title'	: lang_array.close_ticket,
            'message'	: "<p style='margin:10px'>"+lang_array.close_reason+"<textarea id='reason' style='margin-top:5px; width:100%'></textarea></p>",
            'buttons'	: {
                'Canceled'	: {
                    'label'     : lang_array.cancel,
                    'class'	: 'btn btn-danger',
                    'glyph'     : 'glyphicon-remove',
                    'action': function(){   
                        block_reason = $('#reason').val();
			cancelTicket(ticket_id,block_reason);
                    }
                },
                'Rejected'	: {
                    'label'     : lang_array.reject,
                    'class'	: 'btn btn-danger',
                    'glyph'     : 'glyphicon-trash',
                    'action': function(){
                        block_reason = $('#reason').val();
                        rejectTicket(ticket_id,block_reason);
                    }  
                },
                'Do Nothing'	: {
                    'label'     : lang_array.do_nothing,
                    'class'	: 'btn btn-default',
                    'action': function(){}  // Τί πρέπει να γίνει αν επιλεχθεί το 'No'
                }
            }
        });
}

// A dialog function for closing a ticket
function close_new_ticket_by_handler_dialog(ticket_id){

    $.confirm({
            'title'	: lang_array.close_ticket,
            'message'	: "<p style='margin:10px'>"+lang_array.close_reason+"<textarea id='reason' style='margin-top:5px; width:100%'></textarea></p>",
            'buttons'	: {
                "Cancelled"	: {
                    'label'     : lang_array.cancel,
                    'class'	: 'btn btn-danger',
                    'glyph'     : 'glyphicon-remove',
                    'action': function(){   
                        block_reason = $('#reason').val();
			cancelTicket(ticket_id,block_reason);
                    }
                },
                'Rejected'	: {
                    'label'     : lang_array.reject,
                    'class'	: 'btn btn-danger',
                    'glyph'     : 'glyphicon-trash',
                    'action': function(){
                        block_reason = $('#reason').val();
                        rejectTicket(ticket_id,block_reason);
                    }  
                },
                'Do Nothing'	: {
                    'label'     : lang_array.do_nothing,
                    'class'	: 'btn btn-default',
                    'action': function(){}  // Τί πρέπει να γίνει αν επιλεχθεί το 'No'
                }
            }
        });
}

// A dialog function for closing a ticket
function close_ticket_by_owner_dialog(ticket_id){

    $.confirm({
            'title'	: lang_array.close_ticket,
            'message'	: "<p style='margin:10px'>"+lang_array.close_reason+"<textarea id='reason' style='margin-top:5px; width:100%'></textarea></p>",
            'buttons'	: {
                'Cancelled'	: {
                    'label'     : lang_array.cancel,
                    'class'	: 'btn btn-danger',
                    'glyph'     : 'glyphicon-remove',
                    'action': function(){   
                        block_reason = $('#reason').val();
			cancelTicket(ticket_id,block_reason);
                    } 
                },
                'Do nothing'	: {
                    'label'     : lang_array.do_nothing,
                    'class'	: 'btn btn-default',
                    'action': function(){}  
                }
            },
            'width'     : '600',            
        });
}

// A dialog function for blocking a ticket
function block_ticket_dialog(ticket_id){

    $.confirm({
            'title'	: lang_array.set_blocked,
            'message'	: "<p style='margin:10px'>"+lang_array.blocked_reason+"<textarea id='reason' style='margin-top:5px; width:100%'></textarea></p>",
            'buttons'	: {
                'Set as Blocked'	: {
                    'label'     : lang_array.set_as_blocked,
                    'class'	: 'btn btn-danger',
                    'glyph'     : 'glyphicon-pause',
                    'action': function(){   
                        block_reason = $('#reason').val();
			blockTicket(ticket_id,block_reason);
                    }
                },
                'Do nothing'	: {
                    'label'     : lang_array.do_nothing,
                    'class'	: 'btn btn-default',
                    'action': function(){}  
                }
            }
        });
}

// A dialog function for assigning a ticket
function assign_ticket_dialog(ticket_id,teamHandlers){

    selectHtml = "<select id='userForRole' name='userForRole' style='margin:8px auto'>";
    $.each(teamHandlers, function(i, user) {
        selectHtml = selectHtml+"<option value='"+user.id+"'>"+user.username+" ("+user.email+")"+"</option>";
    });
    selectHtml = selectHtml+"</select>"

    $.confirm({
            'title'	: lang_array.assign_to_user,
            'message'	: "<p style='margin:10px'>"+lang_array.select_user+"<br>"+selectHtml+"</p>",
            'buttons'	: {
                'assign'	: {
                    'label'     : lang_array.assign,
                    'class'	: 'btn btn-primary',
                    'glyph'     : 'glyphicon-user',
                    'action': function(){
                        uid = $('#userForRole').find(":selected").attr('value');
			assignTicket(ticket_id,uid);
                    }
                },
                'Do nothing'	: {
                    'label'     : lang_array.do_nothing,
                    'class'	: 'btn btn-default',
                    'action': function(){}  
                }
            }
        });
}

// A dialog function for re-assigning a ticket
function reassign_ticket_dialog(ticket_id,teamHandlers){

    selectHtml = "<select id='userForRole' name='userForRole' style='margin:8px auto'>";
    $.each(teamHandlers, function(i, user) {
        selectHtml = selectHtml+"<option value='"+user.id+"'>"+user.username+" ("+user.email+")"+"</option>";
    });
    selectHtml = selectHtml+"</select>"

    $.confirm({
            'title'	: lang_array.assign_to_user,
            'message'	: "<p style='margin:10px'>"+lang_array.select_user+"<br>"+selectHtml+"</p>",
            'buttons'	: {
                'assign'	: {
                    'label'     : lang_array.assign,
                    'class'	: 'btn btn-primary',
                    'glyph'     : 'glyphicon-user',
                    'action': function(){
                        uid = $('#userForRole').find(":selected").attr('value');
			reassignTicket(ticket_id,uid);
                    }
                },
                'Do nothing'	: {
                    'label'     : lang_array.do_nothing,
                    'class'	: 'btn btn-default',
                    'action': function(){}  
                }
            }
        });
}