


$(document).ready(function() {
   
    $("#signinForm").submit(function(e) {
        e.preventDefault();
        const email = $("#email").val();
        const password = $("#password").val();
        const emailerrormsg = $("#emailerrormsg");
        const passworderrormsg = $("#passworderrormsg");


        console.log(email , password);

        if (!email || !password) {
            console.log("email")
            if (!email) {
                emailerrormsg.text("Please enter email address");
            }
            if (!password) {
                passworderrormsg.text("Enter password");
            }
            return ;
        }
        
        if(email === "") {
            emailerrormsg.text("Please enter your email address");
            $("#email").focus();
            return;
        }
    
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          emailerrormsg.text("Please enter a valid email address.");
          $("#email").focus();
          return;
        }
    
        if (password === "") {
          passworderrormsg.text("Please enter your password.");
          $("#password").focus();
          return;
        }

        $.ajax({
            url : "../api/auth/signin.php",
            method: "POST",
            data : {email : email , password : password},
            success : function(res) {
                const response = typeof res === 'string' ? JSON.parse(res) : res;
                if (response.success){
                    window.location.href = response.redirectUrl;
                }else {
                    passworderrormsg.text(response.message);
                }
            },
            error : function (error){
                const response = error.responseJSON;
                passworderrormsg.text(
                    response?.message || "Unable to sign in."
                );
            }
        });

        $("#email").on("input", function () {
            $("#emailerrormsg").text("");
        });

        $("#password").on("input", function () {
            $("#passworderrormsg").text("");
        });

        
    })

})