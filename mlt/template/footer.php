<script>
    $(document).ready(function() {

        $('.mltbutton').click(function(){
            $('textarea#query_content').text($(this).parent().parent().find('.doccontent').text());
            //$('select[name=field').val('content').change();
            $('textarea#query_title').text($(this).parent().parent().find('h3 >span').text().trim());
            //	$('select[name=field').val('title').change();

            $('#send').click();
            return false;
        });

        $(':radio').change(function() {

            $("#faceted").trigger('submit');

        });
        $('select[name=ranker]').change(function(){
            if($(this).val() =='expr') {
                $('input[name=rankerexpr]').addClass('d-block').removeClass('d-none');
            }else{
                $('input[name=rankerexpr]').removeClass('d-block').addClass('d-none');
            }
        });
        $(':reset').click(function(){

            location.search ='';
        });
        $('.reset_facet').click(function(){
            $('input[name^='+$(this).attr('data-target')+']').removeAttr('checked');
            $("#faceted").trigger('submit');
            return false;

        });
    });
</script>
