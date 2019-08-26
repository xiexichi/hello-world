$(function(){
   InitCart()
});

function InitCart(){
    //$.cookie('cart', '', {expires: 365});
    var options = [{ "url": "/ajax/get.cart.php", "type":"GET", "dataType":"json"}]
    Load(options, function(json){
        if(json.status=="success"){
           $("#menu #navcart").addClass("cart")
            $("#producttool #pagecart").addClass("cartadded")
        }
    },function(){})
}
