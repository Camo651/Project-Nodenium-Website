

<?php include '../Metadata.php'; ?>

<title>Login</title>
<link rel="stylesheet" href="https://projectnodenium.com/Gallery.css" media="screen">
  <body class="u-body">
    <script type="text/javascript">
        function toggleSignUp(){
            var x = document.getElementById("signUpInfo");
            if(x.style.display === "none"){
                x.style.display = "block";
            }else{
                x.style.display = "none";
            }
        }
    </script>
    <section class="u-align-center u-clearfix u-grey-90 u-section-2" id="contentHolder" style="background-image: url(https://projectnodenium.com/Profiles/Content/camo_1.png);">
        <div id="modal">
            <p id="loginError" style="display:none;"></p>
            <form id="loginForm" action="Account" method="post">
                <h1>Login</h1>
                <input id="formUsername" type="text" name="username" placeholder="Username" required>
                <input id="formPassword"type="password" name="password" placeholder="Password" required>
                <p><input type="checkbox" name="remember" value="remember">Remember Me</p>
                <input type="submit" value="Login">
            </form>
            <button id="signUp" onclick="toggleSignUp()">Help</button>
            <p id="signUpInfo" style="display:none;">DM Camo if you need to set up your account, forget your password, or need any help. You must me a Nodenium member to create an account.</p>
        </div>
    </section>
    
    

    <?php
        session_start();
        if (isset($_POST['error'])) {
                echo "  <script> 
                        document.getElementById('loginError').style.display = 'block'; 
                        document.getElementById('loginError').innerHTML = '" . $_POST['error'] . "'; 
                    </script>";
                unset($_POST['error']);
        }
    ?>
    <style>
        #contentHolder{
            background-color: #1a1a1a;
            height: 100vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #modal{
            background-color: #1a1a1a;
            border-radius: 5px;
            padding: 20px;
            width: 400px;
            height: fit-content;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            filter: drop-shadow(0px 0px 10px rgba(0,0,0,1));
        }
        #loginForm{
            background-color: #1a1a1a;
            border-radius: 5px;
            padding: 20px;
            width: 400px;
            height: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        #loginForm input{
            background-color: #1a1a1a;
            border: 1px solid #ffffff;
            border-radius: 5px;
            padding: 10px;
            margin: 10px;
            width: 300px;
            color: #ffffff;
        }
        #loginForm input[type="submit"]{
            background-color: #1a1a1a;
            border: 1px solid #ffffff;
            border-radius: 5px;
            padding: 10px;
            margin: 10px;
            width: 300px;
            color: #ffffff;
            cursor: pointer;
        }
        #loginForm input[type='checkbox']{
            background-color: #1a1a1a;
            border: 1px solid #ffffff;
            border-radius: 5px;
            padding: 5px;
            margin: 5px;
            width: 15px;
            color: #ffffff;
            cursor: pointer;
        }
        #loginForm p{
            color: #ffffff;
            margin: 2px;
        }
        #loginForm input[type="submit"]:hover{
            background-color: #ffffff;
            color: #1a1a1a;
        }
        #signUp{
            background-color: #1a1a1a;
            border: 1px solid #ffffff;
            border-radius: 5px;
            padding: 10px;
            margin: 10px;
            width: 150px;
            color: #ffffff;
            cursor: pointer;
        }
        #signUp:hover{
            background-color: #ffffff;
            color: #1a1a1a;
        }
        #signUpInfo{
            color: #ffffff;
            font-size: 18px;
            margin-top: 20px;
        }
        #loginError{
            color: lightcoral;
            font-size: 18px;
            margin-top: 20px;
        }

    </style>
  </body>
</html>
