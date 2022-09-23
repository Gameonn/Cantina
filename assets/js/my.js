    var globalId= 1;
    var levelCount=1;
    
    function myfunction()
    {
 $(document).ajaxStop($.unblockUI); 
    $('#submit-it').unbind().click(function(){
    var tempArray= [];

    $('.main-con .field').each(function(i,ob) {

    temp=[];
    

    if($(ob).attr('style') != 'display: none;'){

    temp[0]= $(ob).attr('level');
    temp[1]= $(ob).attr('field-id');
    temp[2]= $(ob).find('.field-name').text();
    temp[3]= $(ob).attr('has-child');
    temp[4]= $(ob).attr('is-item');
    temp[5]= $(ob).attr('parent-id');
    }
    
    
    if(temp[0] != undefined){
    tempArray.push(JSON.stringify(temp, true));
    }
    
    

    });
    console.log(tempArray);


    $.post("data.php",
    {
    data: tempArray
    },
    
    function(data){

    console.log(data);

    $('body').append(data);


    }    
    );
    });   

    $(".field .close").unbind().click(function(){

    var level= $(this).parents('.field').attr('level');
    var field= $(this).parents('.field').attr('field-id');
    var fields= [field];
    var cat_id=$(this).parents('.field').attr('category_id');
    if(level>=2){
    var subcat_id=$(this).parents('.field').attr('subcategory_id');
    var fselect='.main-con [subcategory_id=' + subcat_id + ']';
    }
    else{ 
    var subcat_id=0;
    var fselect='.main-con [category_id=' + cat_id + ']';
    }
    removeEntry(cat_id,subcat_id,level,fselect);
    //deleteField(fields,true);
    myfunction();
    
    });   

    $(".add-cat").unbind().click(function(){
    
    var level= parseInt($(this).attr('level'));
    //alert(level);
    var levelSelect= '[level=' + level + ']';
    if(level==1)
    var categ=0;
    else
    var categoryId=$(this).parents('.level-main').find('.parent').attr('cat-id');
    var subcategoryId=$(this).parents('.level-main').find('.parent').attr('subcat-id');
    if(parentSet(level) == false){
    return false;
    }
    var vid= $(this).parents('.main-con').attr('vid');
    var value= $(levelSelect + " [type=text]").val();
     value = value.replace(/(<([^>]+)>)/ig,"");
     
    var isItem= $(levelSelect + " .is-item").attr('is-item');

    var childFlag= "no";

    if(level == 1){
    parentId=0;
    var isItem="no";
    }
    else{
    parentId= $(levelSelect + " .parent").attr('parent-id');
    parentChildCheck(parentId);
    }

    if(value== ""){
    alert('name can\'t be empty');  
    }
    else{
    //Empty the value
    $(levelSelect + " [type=text]").val('');
    var target= levelSelect + " .items-in .nav";

    addField(target,value,level,childFlag,parentId,isItem,globalId++,vid,categoryId,subcategoryId)
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

    myfunction();

    })

    $('.field').unbind().click(function(){

    var fieldName= $(this).find('.field-name').text();
    
    var fieldId= $(this).attr('field-id');

   var has_item= $(this).attr('has-item');
   
    var catId= $(this).attr('category_id');
    var subcatId= $(this).attr('subcategory_id');
   
    var level= parseInt($(this).attr('level'));
    var nextLevel = level + 1;
    
    var fields= [subcatId];
     var levelSelect= '[level=' + level + ']';
     var nextlevelSelect='[level=' + nextLevel + ']';
     

    // Add active class to current field only
    $(levelSelect +' .field').removeClass('active');
    $(this).addClass('active');

    //alert(has_item);
    
if(has_item=='yes'){
alert('A menu item has been created. You cannot add a level if a menu item has already been created');
}    
    
else 
{
	var isNext= $('#level-' + nextLevel);
    //Next level is available or not, add if not
    if (isNext.length == 0) { 

    //Add new level
    if(level<6)
    addLevel(nextLevel,fieldName,fieldId,catId,subcatId);
    }

    else{
    //Be parent of next level
    if(level<6)
    var isNext= $('#level-' + nextLevel + ' .parent').html(fieldName).attr('parent-id', fieldId).attr('cat-id', catId ).attr('subcat-id', subcatId );
    }



    var event='get-subcategory';
    var postlevel=level+2;
    var nlevel=level+3;
    var plevel=level+4;
    var klevel=level+5;
    if(level==1)
    var pid=0;
    else
    var pid=subcatId;
    
    $.blockUI({ message: ('<h4><img src="../uploads/ajax-loader.gif" /> Please wait...</h4>' ),
    		css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: .5, 
            color: '#fff' 
        }}
    ); 
       $.post("functions.php",
    {
    event: event,
    parent_id: pid,
    menucategory_id: catId
    },
    
    function(data){
   
    
    console.log(data);
  if(data[0]=='0'){
   $('#level-'+nextLevel+' .items-in ul').empty(); 
        $('#level-'+postlevel).remove(); 
          $('#level-'+nlevel).remove(); 
         $('#level-'+plevel).remove(); 
          $('#level-'+klevel).remove(); 
       }
       else{
        $('#level-'+nextLevel+' .items-in ul').empty(); 
        $('#level-'+postlevel).remove(); 
          $('#level-'+nlevel).remove(); 
           $('#level-'+plevel).remove(); 
            $('#level-'+klevel).remove();    
    $.each(data, function(index,value) {
	value.count= (value.count==0) ? "no":"yes";
	//alert(value.count);
            var field= "<li class='field' category_id='"+ value.menucategory_id +"' subcategory_id='"+ value.id +"' level='"+ nextLevel +"' has-item='"+ value.count +"' has-child='no' parent-id='"+ value.parent_id +"' field-id='"+ value.id +"' is-item='no'><a href='#'><span class='field-name'>" + value.name + "</span><span class='close'>x</span></a></li>";
            
          
    $('#level-'+nextLevel+' .items-in ul').append(field).fadeIn(1000);

     });
     
     myfunction();
     }
    },"json"
    
    );


}
    myfunction();
    
    });

    }
    function parentSet(level){
    if(level != 1)
    {
    var levelSelect= '[level=' + level + ']';
    
    var lastLevel= level-1;
    
    var newlevelSelect= '[level=' + lastLevel + ']';
    
    var isSet= $(newlevelSelect + " .field.active");
    //alert(isSet.length);
    //var isSet= $('.main-con [field-id=' + parentId + '][style="display: none;"]');
    //alert(isSet.length);      
    
    var parentId= $(levelSelect + " .parent").attr('parent-id');

    var parentisItem= $('.main-con [field-id=' + parentId + ']').attr('is-item');

    if(parentisItem == 'yes'){
    alert('Parent can\'t be an item!');
    return false;
    }

    if (isSet.length == 0) {
    alert('Please select a parent');
    return false;
    }
    else{
    return true;
    }



    }
    else{
    return true;
    }
    }

    function deleteField(fields,showAlert){

    showAlert = typeof showAlert !== 'undefined' ? showAlert : false;

    fields.forEach(function(field) {

    var fieldSelect= '.main-con [field-id=' + field + ']';

    var hasChild= $(fieldSelect).attr('has-child');

    if(hasChild == 'yes')
    {   
    $(fieldSelect).fadeOut(1000);

    var tempArray= [];
    //get child
    $('.main-con [parent-id=' + field + ']').each(function(i,obj) {

    var field= $(obj).attr('field-id');
    tempArray.push(field);
    });

    if(showAlert){
    var con= confirm('This Field has childs they will also be deleted, want to continue?');

    if(con){
    deleteField(tempArray);
    }
    else{
    return; 
    }
    }
    else{
    deleteField(tempArray);
    }
    
    }
    else{
    $(fieldSelect).fadeOut(1000);
    }
    });     
    }

    function highlightField(fields,level){
 	//alert(fields);
 	  var nextLevel = level + 1;
 	 var child= $('#level-' + nextLevel + ' .field').attr('parent_id', fields);
 	 //alert(child);
         fields.forEach(function(field) {      
	var fieldSelect= $('.main-con [subcategory_id=' + field + ']'); 
   // alert(fieldSelect.length);
    if(fieldSelect.length == 1){
    //alert(fieldSelect.length);
    }
     //var f= $(fieldSelect).attr('parent_id');
     //var field=[f];
     $(fieldSelect).addClass('active');
   
     //highlightField(field);
    
    });
    }

 function itemCheck(level){

    if(level != 1){

    var levelSelect= '[level=' + level + ']';

    var parentId= $(levelSelect + " .parent").attr('parent-id');

    var isItem= $(levelSelect + " .is-item").attr('is-item');

    if(isItem == "yes"){

    var checkChild= $(".main-con [parent-id='" + parentId + "'][is-item='no']");

    if (checkChild.length != 0) {
    
    if(checkChild.attr('style') != 'display: none;'){
    alert('Parent has subcategory(s), so you can\'t add an item!');
    return false; 
    }
    else{
    return true;
    }   
    }
    else
    return true;

    }
    else{

    var checkChild= $(".main-con [parent-id='" + parentId + "'][is-item='yes']");

    if (checkChild.length != 0) {

    if(checkChild.attr('style') != 'display: none;'){
    alert('Parent has item(s), so you can\'t add an sub category!');
    return false; 
    }
    else{
    return true;
    }
    }
    else
    return true;
    }
    }

    return true;
    }

    function removeEntry(category_Id,subcategory_Id,level,fselect){
    if(level==1)
    var event='remove-category';
    else
    var event='remove-sub-category';

    
    $.post("functions.php",
    {
    event: event,
    subcategory_id: subcategory_Id,
    menucategory_id: category_Id
    },
    
    function(data){
    
    var option=data;
    
    console.log(data);

    //$('body').append(catid);
    if(option==1)
    $(fselect).remove();
    else
    alert("First Delete Child Elements Or Items");
    
    } 
    );
    }


    function addField(target,name,level,childFlag,parentId,isItem,fieldId,vid,categoryId,subcategoryId){
    if(level==1)
    var event='add-category';
    else
    var event='sub-category';
    
    if(level==2)
    var pid=0;
    else
    var pid=subcategoryId;

    $.post("functions.php",
    {
    event: event,
    name: name,
    venue_id: vid,
    parent_id: pid,
    menucategory_id: categoryId
    },
    
    function(data){
    var catid=data;
    var subcatid=data;
    
    console.log(data);

    //var hidden_field="<input type='hidden' name='category_id' value="+data+">";
    if(level==1)
    $('[field-id=' + fieldId + ']').attr('category_id', data);
    else
    $('[field-id=' + fieldId + ']').attr('category_id', categoryId);
    
    if(level>=2)
    $('[field-id=' + fieldId + ']').attr('subcategory_id', data);
    else
    $('[field-id=' + fieldId + ']').attr('subcategory_id', 0);
    }
    
    );
    
    if(itemCheck(level)){
    //Build the item to add
    var field= "<li class='field' category_id='0' subcategory_id='0' level='"+ level +"' has-item='no' has-child="+ childFlag +" parent-id="+ parentId +" field-id='"+ fieldId +"' is-item='"+ isItem +"'><a href='#'><span class='field-name'>" + name + "</span><span class='close'>x</span></a></li>";
    $(target).append(field).fadeIn(1000);
    }
    
    }

    function addLevel(level,parent, parentId,catId,subcatId){
    
    var newLevel= "<div class='col-md-3 well level-main' id='level-" + level + "' id='level-" + level + "' level='" + level + "'>Level "+ level +"<div class='parent' cat-id='" + catId + "' subcat-id='" + subcatId + "' parent-id='" + parentId + "' >"+ parent +"</div><div class='input-group'><span class='input-group-addon is-item' level='"+ level +"' is-item='no'></span><input type='text' class='form-control'><span class='input-group-btn'><button class='add-cat btn btn-default' level='" + level + "' type='button'>Go!</button></span></div><div class='items-in'><ul class='nav nav-pills nav-stacked'></ul></div></div>";
    if(level<=6)
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


    
    /*
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
    
 
  