$(function(){
    
    var listingId = $('#listingId').val();
    
    $('a.lb').fancybox({
        padding:0,
        overlayColor:'#fff',
        centerOnScroll:1,
        overlayOpacity:0.7,
        showCloseButton:0
    });
    
    $("#btn-send-request").click(function(){
        $.ajax({ 
            type: "POST", 
            url: "/ajax/requestinfo/", 
            data: $("#request-info").serialize() ,
            success: function(json, status){
                $.fancybox.close();
                if(json=="success"){
                    showAlert("Notification has been sent successfully");
                }
            }, 
            error:function(jqXHR, textStatus, errorThrown){ 
                alert(textStatus);
            }
        });
    });


    $("#btn-cancel-327").click(function(){
        $.fancybox.close();
    });

    $("#btn-cancel-326").click(function(){
        $.fancybox.close();
    });

    $("#btn-cancel").click(function(){
        $.fancybox.close();
    });
    $("#add-form-325").click(function(){
        addFormElement();
    });

    $("#btn-activate-326").click(function(){
        changeListingStatus(listingId, 1);
    });

    $("#btn-deactivate-327").click(function(){
        changeListingStatus(listingId, 0);
    });

    function changeListingStatus(listingId, action){
        $.ajax({ 
            type: "POST", 
            url: "/ajax/listingstatus/", 
            data: 'id=' + listingId + '&act=' + action ,
            success: function(json, status){
                $.fancybox.close();
                if(json=="success"){
                    showAlert("Listing has been updated successfully");
                    if(action==1){
                        $("#act-btn-342").hide(); 
                        $("#deact-btn-342").show(); 
                    }
                    else{
                        $("#act-btn-342").show(); 
                        $("#deact-btn-342").hide(); 
                    }
                }
            }, 
            error:function(jqXHR, textStatus, errorThrown){ 
                alert(textStatus);
            }
        });
    }
    function removeFormElement(elm){
        var d = document.getElementById('field-container-r325');
        d.removeChild(elm.parentNode);
    }

    function addFormElement(){
        var html = '<div class="removed">';
        html += '<a href="javascript://" class="remove" name="delete" onclick="removeFormElement(this)">remove</a>';
        html += '<input type="text" name="info[]" value="" placeholder="What\'s pending?" />';
        html += '<div class="clear"></div>';
        html += '</div>';

        $('#field-container-r325').append(html);
    }

});