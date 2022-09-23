    var globalId= 1;
    var levelCount=1;

    function myfunction()
    {

    $(".field .close").unbind().click(function(){

        var level= $(this).parents('.field').attr('level');
        var field= $(this).parents('.field').attr('field-id');

        deleteField(field);
        console.log(field);
        alert(field);
    });   

    $(".add-cat").unbind().click(function(){

        var level= $(this).attr('level');
        var levelSelect= '[level=' + level + ']';

        var value= $(levelSelect + " [type=text]").val();

        var isItem= $(levelSelect + " .is-item").attr('is-item');

        var childFlag= "no";

        if(level == 1){
            parentId=0;
            var isItem= "no";
        }
        else{
            parentId= $(levelSelect + " .parent").attr('level-parent-id');
        }

        alert(parentId);
        parentChildCheck(parentId);

        if(value== ""){
            alert('name can\'t be empty');  
        }
        else{
            //Empty the value
            $(levelSelect + " [type=text]").val('');
            var target= levelSelect + " .items-in .nav";

            addField(target,value,level,childFlag,parentId,isItem,globalId++)
        }  

        myfunction();

    });


    $('.is-item').unbind().click(function(){

        var level= $(this).attr('level');

        if(level != 1){

            $(this).toggleClass('active');

            var isItem= $(this).attr('is-item');

            if(isItem == "no"){
                $(this).attr('is-item', 'yes');
            }
            else{
                $(this).attr('is-item', 'no');
            }


        }

    })

    $('.field').unbind().click(function(){

        var fieldName= $(this).text();

        var fieldId= $(this).attr('field-id');

        var level= parseInt($(this).attr('level'));
        var nextLevel = level + 1;

        var levelSelect= '[level=' + level + ']';

        var parentId= $(this).attr('parent-id');

        parentChildCheck(parentId);

        // Add active class to current field only
        $(levelSelect +' .field').removeClass('active');
        $(this).addClass('active');

        var isNext= $('#level-' + nextLevel);

        //Next level is available or not, add if not
        if (isNext.length == 0) { 

            //Add new level
            addLevel(nextLevel,fieldName,fieldId);
        }

        else{
            //Be parent of next level
            var isNext= $('#level-' + nextLevel + ' .parent').html(fieldName).attr('level-parent-id', fieldId);
        }

        myfunction();
        
    });

}

    function deleteField(field){

        var fieldSelect= '.main-con [field-id=' + field + ']';

        var hasChild= $(fieldSelect).attr('has-child');

        if(hasChild == 'yes')
        {
            var con= confirm('This Field has childs they will also be deleted, want to continue?');

            if(con){
                deleteFieldChilds(field);
            }
            else{
                return; 
            }
        }
        else{
            $(fieldSelect).fadeOut(1000);
        }
    }

    function deleteFieldChilds(field){

        var fieldSelect= '.main-con [field-id=' + field + ']';

        var parentId= $('.main-con [field-id=' + field + ']').attr('parent-id');

        var hasChild= $(fieldSelect).attr('has-child');

        if(hasChild == 'yes')
        {

            $('.main-con [parent-id=' + field + ']').fadeOut(1000);

            deleteFieldChilds(parentId);
  
        }
        else{
            $(fieldSelect).fadeOut(1000);
        }
    }

    function itemCheck(level){

        if(level != 1){

            var levelSelect= '[level=' + level + ']';

            var parentId= $(levelSelect + " .parent").attr('level-parent-id');

            var isItem= $(levelSelect + " .is-item").attr('is-item');

            if(isItem == "yes"){

                var checkChild= $(".main-con [parent-id='" + parentId + "'][is-item='no']");

                if (checkChild.length != 0) {
                    
                    alert('Parent has subcategory(s), so you can\'t add an item!');

                    return false;
                }
                else
                    return true;

            }
            else{

                var checkChild= $(".main-con [parent-id='" + parentId + "'][is-item='yes']");

                if (checkChild.length != 0) {
                    
                    alert('Parent has item(s), so you can\'t add an sub category!');

                    return false;
                }
                else
                    return true;
            }

            

        }

        return true;
    }


    function addField(target,name,level,childFlag,parentId,isItem,fieldId){

        if(itemCheck(level)){
            //Build the item to add
            var field= "<li class='field' level='"+ level +"' has-child="+ childFlag +" parent-id="+ parentId +" field-id='"+ fieldId +"' is-item='"+ isItem +"'><a href='#'>" + name + "<span class='close'>x</span></a></li>";
            $(target).append(field).fadeIn(1000);
        }
    }

    function addLevel(level,parent, parentId){
        var newLevel= "<div class='col-md-3 well level-main' id='level-" + level + "' level='" + level + "'><div class='parent' level-parent-id='" + parentId + "' >"+ parent +"</div><div class='input-group'><span class='input-group-addon is-item' level='"+ level +"' is-item='no'>@</span><input type='text' class='form-control'><span class='input-group-btn'><button class='add-cat btn btn-default' level='" + level + "' type='button'>Go!</button></span></div><div class='items-in'><ul class='nav nav-pills nav-stacked'></ul></div></div>";

        $('.main-con').append(newLevel).fadeIn(1000);
        levelCount++;
    }

    function parentChildCheck(parentId){

        var hasChild= $('.main-con [parent-id=' + parentId + ']');

        //Next level is available or not, add if not
        if (hasChild.length == 0) {
            
            $('.main-con [field-id=' + parentId + ']').attr('has-child', 'no');
        }
        else{
            $('.main-con [field-id=' + parentId + ']').attr('has-child', 'yes');
        }


        /*$('.level-main').each(function(i,obj) {
            var name= $('.level-main').attr('id');

            alert(name);

            $(obj).find('.field').each(function(k,ob){

                var getIt= $(ob).text();
                alert(getIt);
            });

        });

        $.get("data.php",
                {
                    data: "data"
                },
            
                    function(data){

                        console.log(data);


                        }
                    
 
                    
                ); */

    }

    

    myfunction();

