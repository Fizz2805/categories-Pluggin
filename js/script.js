// alert("connected")
//it is sending date to load_products function
/* when we will click on read more, it will fetch its category id then this id will be sent to Php using ajax. 
in php function it will get that category id, get the slug, based on slug show products and send data back to 
js(here). Then here it will print products in success */

jQuery(document).ready(function($){

    $(".single_category a").on("click", function(e){
        e.preventDefault()
        var mycategoryID = $(this).attr("category_id")
        //alert(mycategoryID)
        
        $.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        data: {
            action: 'load_products_by_category',
            nonce: ajax_object.nonce,
            category_id: mycategoryID
        },
    // jab ajex call data fetch kar ka la ayay tu ya function run karna. jo response hota wo sucess ma receive 
    //hota aur phir usay print
        success: function(response){
          // console.log(response)
           //products-container is products div iD in custom template. here we are appending
          $("#products-container").html(response.data);
          //.products-main was hidden we will show it when vlivk on read more
          $(".products-main").css("display", "block");
          $(".categories").css("display", "none");
        }
    
        });
        });

        $("#back-button").on("click", function(){
            $(".products-main").css("display", "none");
            $(".categories").css("display", "block");
        })
});


