
$(document).ready(function () {
  
  $("#form").submit(function(e){
    e.preventDefault();
    console.log("HI");
    
    const accountType = $("#accountType").val().trim();
    const fullName = $("#fullName").val().trim();
    const email = $("#email").val().trim();
    const phone = $("#phone").val();
    const password = $("#password").val();

    

    $.ajax({
      url : "../api/auth/signup.php",
      method : "POST",
      data : {phone:phone , accountType : accountType , fullName:fullName , email:email , password : password},
      success : function(res) {
        const response = typeof res === 'string' ? JSON.parse(res): res;
        if (response.success) {
          window.location.href = './signin.php';
        }else {
          console.log(response.message);
          $("#emailError").text(response.message);
        }
      },
      error : function () {

      }
    })
  })

})