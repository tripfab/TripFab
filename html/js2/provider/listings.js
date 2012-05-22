$(document).ready(function() {    
    
    $.address.change(function(adr){
        
        $('.loading').show();

        if(adr.pathNames.length == 0) {
            document.location.hash = "/all/";
            return;
        }
        
        $data = adr.parameters;
        $data.get = adr.pathNames[0];
        
        $.ajax({
            url:'/ajax/getpartnerlistings',
            dataType:'html',
            data:$data,
            success:function(res){
                $('.resultsholder').html(res);
                refreshActions();
            }, error: function(){
                $('.loading').hide();
                showError('Something went wrong. Please try again later');
            }
        });
    });
    
    function refreshActions(){
        
        $('a.lb').fancybox({
            padding:0,
            overlayColor:'#fff',
            centerOnScroll:1,
            overlayOpacity:0.7,
            showCloseButton:0
        });
        
        $('.preferences .btn-10').click(function() {
            $pref = $(this).parents('.preferences');
            $('ul', $pref).toggleClass('show');
            return false
        });
        
        $('.lbox .btn-2, .lbox .btn-11').click(function(){
            $(this).parents('form').submit();
        });
        
        $('.lstng.inactive').each(function(){
            $this = $(this);
            $ul   = $('.right ul', this);
            $id   = $this.data('id');
            $.ajax({
                url:'/ajax/activate',
                data:{
                    listing:$id
                },
                type:'post',
                success:function(response){
                    if(response.success == 1){
                        $msg = $('<div></div>');
                        $msg.addClass(response.status);
                        $msg.append('<div><a href="/provider/listings/activate/'+response.id+'"><span>'+response.text+'</span></a></div>');
                        $('span', $msg).addClass(response.missing);
                        $('.listing'+response.id+' .right > ul').before($msg);
                    }
                }
            });
        });
        
        $('select[name=sort]').change(function(){
            data = {};
            if($('option:selected', this).data('sort') != "created")
                data.sort = $('option:selected', this).data('sort');
            if($('option:selected', this).data('order') != "")
                data.order = $('option:selected', this).data('order');
            if($.address.parameter('page'))
                data.page = $.address.parameter('page');
            
            $.address.queryString($.param(data));
        });
        
        $('.resultsholder .paging li a').click(function(){
            if($(this).hasClass('active'))
                return false;
            
            data = {};
            $parent = $(this).parents('li');
            if($parent.hasClass('prev')) {
                if($.address.parameter('page'))
                    $page = $.address.parameter('page');
                else
                    $page = 1;
                $page = parseInt($page) - 1;
                if($page != 1) 
                    data.page = $page;
            } else if($parent.hasClass('next')) {
                if($.address.parameter('page'))
                    $page = $.address.parameter('page');
                else
                    $page = 1;
                $page = parseInt($page) + 1;
                data.page = $page;
            } else {
                $page = $(this).attr('rel');
                if($page != 1)
                    data.page = $page;
            }
            
            if($.address.parameter('sort'))
                data.sort = $.address.parameter('sort');
            if($.address.parameter('order'))
                data.order = $.address.parameter('order');
            
            $.address.queryString($.param(data));            
            
            return false;
        });
        
        $('.loading').hide();
    }
});