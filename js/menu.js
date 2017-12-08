/* menu.js JavaScript file 
 * This file will consume our Restful Api services for
 * Coffeebuzz
 */

//The root URL for the RESTful Services
//REST:  REpresentational State Tranfer

var apiURL = "http://localhost:8888/InClassCoffeebuzzAPI_2017/";
//var apiURL = "http://localhost:8888/InClassCoffeebuzz_API/";
//var apiURL = "http://advp-files/Coffeebuzz_API/";

var userId = null;
/******************************* EVENTS HANDLERS ******************************/

$(function(){//DOM Ready
    //Session Storage
    //http://www.geekchamp.com/html5-tutorials/18-html5-session-storage
    var storedData = sessionStorage.getItem("user");
    if (storedData) {
      //user is logged in - hide the login link
      userData = JSON.parse(storedData);//converting JSON string to OBJECT
      //console.log(userData);
      //console.log(userData[0].id);
      userId= userData[0].id;// Retrieve the id property from OBJECT
      $(".navbar-nav [data-login-button]").toggle();  
      $(".navbar-nav [data-register-button]").toggle();  
    }else{
        //User is not logged in (hide logout and favorites link
        $(".navbar-nav [data-logout-button]").toggle();
        $(".navbar-nav [data-favorites-button]").toggle();
    }
    
    //Always retrieve categories when application starts
    getCategories(); 
    
});

//Menu Item click event
$(document).on("click", ".menuitem", function(e){
    e.preventDefault();
    var category = $(this).data('identity');
    getProductsByCategory(category); //get all products by category   
});

//Menu id click event
$(document).on("click", ".menuid", function(e){
    e.preventDefault();
    var id = $(this).data('identity');
    var category = $(this).data('category');
    getProductById(id, category); //get a single produdct by id  
});

//Add Favorite Click Event
$(document).on("click", "a.add", function(e){
    e.preventDefault();//stop befault behavior (hyperlinking)
    if(userId){//Check if user is logged in using sessionStorage
        var id = $(this).data('identity');
        $.ajax({
            type: 'post',
            url: apiURL + 'favorites',
            data: {user_id:userId, product_id:id},
            success: function () {
                //show toast notification
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": true,
                    "onclick": null,
                    "showDuration": 300,
                    //"hideDuration": "1000",
                    "timeOut": "1000",
                    "extendedTimeOut": 0,
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut",
                    "onHidden": function() { 
                        document.location = "http://localhost:8888/Coffeebuzz_PHP/favorites.php";
                    }
                  };                    
                  toastr.success('Favorite has been added!', 'Favorites');

                //document.location = "http://localhost:8888/Coffeebuzz_PHP/favorites.php";
                //document.location.reload(); 

            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(textStatus);
            }
        });//End ajax call
    }//End user logged in check

});//End add favorite click

//Remove Favorite Click Event
$(document).on("click", "a.delete", function(e){
    e.preventDefault();
    if(userId){//if user is logged in
      var id = $(this).data('identity');//get id of favorite item
      //console.log('delete favorite: ' + id + ' for user: '+ userid);
      if (confirm('Are you sure you want to remove this favorite?')){
          $.ajax({
              type: 'delete',
              url: apiURL + 'favorites/'+ userId + '/'+ id,
              success: function () {
                  $('#popRemFav').modal('show');
                  $('#popRemFav').on('hidden.bs.modal', function (e) {
                     //document.location.reload(); 
                     document.location = "http://localhost:8888/Coffeebuzz_PHP/favorites.php";
                  });
              },
              error: function () {
                  alert('Error removing favorite!');
              }
          });//End ajax call
      }//End confirm 

    }//End if user logged in

  });//End remove favorite event
  
  
//Search
$("#searchResult").ready(function(){
    //Get URL parameter
    var s = getUrlVars()['s'];
    //alert (s); 
    if (s!==undefined){
       getSearch(s);//do search
    }    
});

//Favorites
$("#favorites").ready(function(){
    //alert('favorites');
    //Check for logged in user first
    if(userId){
        //User is logged in - show favorites  
        //alert('User id: ' + userId);
        getFavorites(userId);     
    }else{
        //User is not logged in clear table and display bootstrap alert
        $("#favorites table").empty();
        $("#favorites").html('<div class="col-lg-12"><div class="alert alert-danger" role="alert">'+
                               '<p>You must be logged in to view this!</p>'+
                              '</div></div>');
    }   
});

// Read a page's GET URL variables and return them as an associative array.
// For search page
function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}
//User Register
//use submit here to process HTML5 validation
$('#frmRegister').submit(function () {
    //alert('register form');
    //1. Retrieve user credentials from input boxed
    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();
    var email = $("#reg_email").val();
    var password1 = $("#password1").val();
    var password2 = $("#password2").val();
    //test 
    //console.log ('firstname: '+ firstname + ' lastname: '+lastname);
    //console.log ('email: '+ email + ' password: '+password);
    
    //2. passwords match
    if(password1!==password2){
        $("#reg_err").html('Your password does not match the confirmed password!');
        return false;
    }    
    //test
    //return false;
    
    //3.Call the API to process the register
    $.ajax({
        type:'post',
        url: apiURL + 'register',
        data:{email:email,password:password2,first_name:firstname,last_name:lastname},
        success:function(data){
            //console.log(data);
            //Check for login success | failure
            if(data.error){
                //register failed - display error message from API in form
                $("#reg_err").html(data.message);
            }else{
                //register success - get activation code
                //var active = JSON.stringify(data.active);
                var active = data.active;
                console.log('active: ' + active);
                //sessionStorage.setItem('user',user);
                //window.location.reload(); 
                
                //Prepare to send email
                var siteURL = "http://localhost:8888/Coffeebuzz_PHP/activate.php";
                var x= "?x="+ encodeURI(email);
                var y = "&y=" + active;
                
                var link = siteURL + x +y;
                console.log(link);
                
            }
        },
        error:function(jqXHR,textStatus,errorThrown){
            alert(textStatus);
        }
        
    });//End of ajax call
    
    return false; // prevent load to another page
    
    
});//End of register

//User Login
$("#login").click(function(){
    //Retrieve user credentials from input boxed
    var email = $("#email").val();
    var password = $("#password").val();
    //console.log ('email: '+ email + ' password: '+password);
    //return false;
    //Call the API to process the login
    $.ajax({
        type:'post',
        url: apiURL + 'login',
        data:{email:email,password:password},
        success:function(data){
            //console.log(data);
            //Check for login success | failure
            if(data.error){
                //login failed - display error message from API in form
                $("#add_err").html(data.message);
            }else{
                //login success - store the user items in sessionStorage
                var user = JSON.stringify(data.items);
                console.log('logged in: ' + user);
                sessionStorage.setItem('user',user);
                window.location.reload();                
            }
        },
        error:function(jqXHR,textStatus,errorThrown){
            alert(textStatus);
        }
        
    });//End of ajax call
    
    return false;
    
});//End of login click event

/*
$("#login").click(function(){	
    var email=$("#email").val();
    var password=$("#password").val();
    console.log('email: ' +email + ' password: '+ password);
    //return false;
        $.ajax({
        type: 'post',
        url: apiURL + 'login',
        data: {email:email, password:password},
        success: function (data) {
           //console.log(data);
           if(data.error){
               //failed
               //alert(data.message);
               $("#add_err").html(data.message);
               //return false;
           }else{
               //success
               //console.log(data.items);                
               //sessionStorage.setItem('user',data.items);
               var user = JSON.stringify(data.items);
               //alert(user);             
               sessionStorage.setItem('user',user);
               window.location.reload();
               //return true;
           }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(textStatus);
        }
    });
    return false;
});
*/

//User logout click
$(".navbar-nav [data-logout-button]").click(function(){
    //Clear session storage
    sessionStorage.clear();
    //window.location.reload();
    //show toast notification
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": 300,
        //"hideDuration": "1000",
        "timeOut": "1000",
        "extendedTimeOut": 0,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "onHidden": function() { 
            window.location.reload();
        }
      };                    
      toastr.success('You have been logged out!', 'Logout');
    //sessionStorage.clear();
    //window.location.reload();
});





/******************************* API FUNCTION CALLS ***************************/

//Get Categories
//http://localhost:8888/InClassCoffeebuzz_API/category
function getCategories(){
    //console.log('getCategories');
    $.ajax({
        type:'GET',
        url: apiURL + 'category',
        dataType:"json",
        success: renderCategoryList
    });

}//end of getCategories function


//Get Products by Category
//http://localhost:8888/InClassCoffeebuzz_API/coffee
//http://localhost:8888/InClassCoffeebuzz_API/muffins
//...
function getProductsByCategory(category){
    $.ajax({
        type:'GET',
        url: apiURL + category, 
        dataType:"json",
        success: function(data){
            renderProductsByCategoryList(data, category);
        }              
    });
}//End of getProductsByCategory function

//Get a single product
//http://localhost:8888/InClassCoffeebuzz_API/coffee/1
function getProductById(id, category) {
  $.ajax({
    type: 'GET',
    url: apiURL + '/' + category + '/' + id,
    dataType: "json",
    success: function(data) {
      renderProductById(data, category);
    }
  });
}//End of getProductById

//Search by item wildcard
//http://localhost:8888/InClassCoffeebuzz_API/search/an
function getSearch(s){
    //console.log('getSearch');
    //var url = apiURL + 'search/' + s;
    //console.log(url);
    $.ajax({
        type:'GET',
        url: apiURL + 'search/' + s,
        dataType:"json",
         success: function(data){
            renderSearchList(data, s);
        }        
    });
}//end of getSearch function

//Retrieve user favorites
//http://localhost:8888/InClassCoffeebuzz_API/favorites/1
function getFavorites(userid){
    //console.log('getSearch');
    //var url = apiURL + 'search/' + s;
    //console.log(url);
    $.ajax({
        type:'GET',
        url: apiURL + 'favorites/' + userid,
        dataType:"json",
        success: function(data){
            renderFavorites(data, userid);
        }  
        //success: renderFavorites     
    });
}//end of getSearch function

/********************************** RENDER HTML  ******************************/

//Show Categories
function renderCategoryList(data){
  //console.log(data.items);
   var list = data.items;
   //console.log(list);
   
   //remove the div within categoryList so that it does not repeat on 
   //refresh
   $("#categoryList div").remove();
   
   //loop each item within list and recreate the HTML dynamically
   $.each(list, function(index,item){
       //console.log('id: '+ item.id + ' category: ' + item.category);
       $("#categoryList").append('<div class="col-md-4 col-sm-6">' +
                	          '<a data-identity="' + item.category + '" href="#" class="thumbnail menuitem">' +
                                    '<img src="img/menu/' + item.category 
                                    +'.jpg" alt="' + item.category + 
                                    '" class="img-responsive menu">' +
                                '</a>'+
                                '<h4>' + item.category + '</h4>' +
                                '</div>');
   });
}

//Show Products by Categories
function renderProductsByCategoryList(data,category){
   var list = data.items;
   //console.log(list);
   //console.log(category);
   var src = "img/menu/"+ category + ".jpg";
   //console.log(src);
   $("#imgMenu").attr("src", src);
   $("#imgMenu").attr("alt", category);
   $("#categoryList div").remove();//clear out old html from this div
   
   //loop the list - creating new HTML 
   $.each(list, function(index,item){
       var link = "<a class='menuid' data-identity='"+ item.id + 
                  "' href='#' data-category='"+ item.category+"'>";
       var img = "<img src='img/menu/" + item.category + 
                 "/" + item.id + ".jpg' alt='" + item.type + "'></a>";
        //<img src='img/menu/coffee/1.jpg' alt='Espresso'>
       //$console.log(link+img);
       $("#categoryList").append('<div class="col-md-4 col-sm-6">'+
                                    '<div class="thumbnail text-center">' +
                                       link+img+
                                       '<h4>'+ item.type+ '</h4>'+
                                    '</div>' +
                                 '</div>');
       
       
   });
}//End of renderProductsByCategoryList function


//Show single product
function renderProductById(data, category) {
    var list = data.items;
    //console.log(list);
    var id = list[0].id;
    var type = list[0].type;
    var img = '<img src="img/menu/'+ category + '/'+id+'.jpg" alt="' + type + '">';
    //console.log(img);
    var desc = "<p>Lorem ipsum dolor sit amet, elit sagittis sociis, risus mauris nulla, ipsum nunc feugiat, fermentum ac hendrerit, massa amet erat fringilla ante volutpat.\n\
          Mi at elementum pulvinar, pellentesque aliquet purus adipiscing egestas.</p>";
        $("#menu").empty();      
        //$("#menu").append('<div class="col-md-4">'+img+'</div><div class="col-md-8"><h3>'+type+'</h3>'+desc+'</div>');
        $("#menu").append('<div class="col-md-4">'+img+'</div><div class="col-md-8"><h3>'+type+'</h3>'+desc+
                          '<div id="favs"><p></p></div>'+
                          '</div>');
        //$("#menu").append('<div id="favs"><p></p></div>');
        if(userId){
            //user is logged in - get favorites for this user
            $.ajax({
                type:'GET',
                url: apiURL + 'favorites/' + userId,
                dataType:"json",
                success: function(data){
                    //console.log(data);
                    var list = data.items;
                    //console.log(list);
                    //console.log('product id: ' +id); 
                    var fav = null;
                    var link = '';
                    if(list.length>0){
                        var favs = [];
                        //alert('in list');
                        $.each(list, function(index,item){
                            favs.push(parseInt(item.id));    
                        });
                        //console.log(favs);
                        //console.log(id);
                        //console.log('menu id: '+id);
                        var fav = $.inArray(parseInt(id),favs);                     
                        //console.log("is favorite: " +fav);
                        
                        if(fav===-1){
                            //add
                            link = '<p><a href="#" data-identity="' + id + '" class="btn btn-warning add" role="button">Add Favorite <span class="glyphicon glyphicon-remove"></span></a></p>';  
                        }else{
                            //delete
                            link = '<p><a href="#" data-identity="' + id + '" class="btn btn-danger delete" role="button">Delete Favorite <span class="glyphicon glyphicon-heart"></span></a></p>';  
                        }
                        
                        
                        //$("#favs p").remove();
                        $("#favs p").append(link);
                                              
                    }else{
                        //alert('no favs');
                        $("#favs p").append('<p><a href="#" data-identity="' + id + '" class="btn btn-warning add" role="button">Add Favorite <span class="glyphicon glyphicon-heart"></span></a></p>');  
                    }

                }                      
            });
        }else{
            //User Not logged in 
            $("#favs p").append('<a data-login-button="" data-toggle="modal" data-target="#login-modal" href="#"><span class="glyphicon glyphicon-log-in"></span> Login to add item to your favorites!</a>');
        }
        
}//End of renderproductById 


//Show Search results
function renderSearchList(data,s){
    //console.log('search results');
    var list = data.items;
    //console.log(list);
    $("#searchResult").append("<p class='text-success'>Search for: "+s.replace('+',' ')+"</p>");
   //loop the list - creating new HTML 
   $.each(list, function(index,item){
        var img = "<img src='img/menu/" + item.category + 
                 "/" + item.id + ".jpg' alt='" + item.type + "'>";
        $("#searchResult").append('<div class="col-sm-6 col-md-4">'+
                                        '<div class="thumbnail">'+ img +
                                            '<div class="caption">'+
                                                '<h3>'+item.type+'</h3>'+
                                                '<p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>');
    });
}//End renderSearchList


//Show user favorites
function renderFavorites(data, userid){
    var list = data.items;
    $.each(list, function(index,item){
    $("#favorites table tbody").append('<tr>'+ 
                                          '<td><img class="fav" src="img/menu/' + item.category + '/' + item.id + '.jpg"/>'+item.type+'</td>'+
                                          
                                          '<td>'+item.category+'</td>'+
                                          '<td><a data-user="' + userid + '" data-identity="' +item.id + '" href="#" class="btn btn-danger delete">'+
                                            '<span class="glyphicon glyphicon-remove"></span> Remove </a>'+
                                          '</td>'+
                                          '</tr>');
    });
}
   
   

//test section
/*
$('#contactForm').submit(function () {
    //console.log('contact form');
    var hasError = false;
    if (!hasError) {
        var formInput = $(this).serialize();
        $.post($(this).attr('action'), formInput, function (data) {           
                //console.log(data);
                $("#data").text(data);
	});        
    }
    return false;
});
*/