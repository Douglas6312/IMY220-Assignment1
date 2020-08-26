<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;	
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Douglas van Reeuwyk">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res))
				{
				    $UserID = $row['user_id'];

					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form action='login.php' method='post' enctype='multipart/form-data'>
								<div class='form-group'>
                                    <input type='hidden' name='loginEmail' value='".$_POST["loginEmail"]."'>
                                    <input type='hidden' name='loginPass' value='".$_POST["loginPass"]."'>
									<input type='file' class='form-control' name='picToUpload' id='picToUpload' /><br/>
									<input type='submit' class='btn btn-secondary' value='Upload Image' name='submit' />
								</div>
						  	</form>";
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>

        <?php

            if(isset($_POST["submit"]) && $_FILES['picToUpload']['name'] !== "")
            {
                $isValid = true;

                $target_dir = "gallery/";
                $uploadFile = $_FILES["picToUpload"];
                $target_file = $target_dir . basename($uploadFile["name"]);


                $check = getimagesize($_FILES["picToUpload"]["tmp_name"]);
                if($check === false  && $isValid)
                {
                    $isValid = false;
                    echo 	'<div class="alert alert-danger mt-3" role="alert">
                                Image file is not what you think it is ;)
                            </div>';
                }

                $allowFileExtensions = array("jpg", "jpeg");
                $filename = $_FILES['picToUpload']['name'];
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                if (!in_array($imageFileType, $allowFileExtensions) && $isValid)
                {
                    $isValid = false;
                    echo 	'<div class="alert alert-danger mt-3" role="alert">
                                Selected Image file type is invalid
                            </div>';
                }

                if ($_FILES["picToUpload"]["size"] > 1024000 && $isValid)
                {
                    $isValid = false;
                    echo 	'<div class="alert alert-danger mt-3" role="alert">
                                Selected Image is too large
                            </div>';
                }

                if ($isValid == true)
                {
                    if (move_uploaded_file($_FILES["picToUpload"]["tmp_name"], $target_file) === false)
                    {
                        echo 	'<div class="alert alert-danger mt-3" role="alert">
                                There was an Error Uploading your Selected Image
                            </div>';
                    }
                    else
                    {
                        $queryInsert = "INSERT INTO tbgallery (user_id,filename) VALUES ('$UserID','$target_file')";
                        if(mysqli_query($mysqli, $queryInsert) === false)
                        {
                            echo 	'<div class="alert alert-danger mt-3" role="alert">
                                        The Image File could NOT be Added to the Database !!!
                                    </div>';
                        }
                    }
                }
            }
        ?>

        <h3>Image Gallery</h3>
        <div class='container'>
            <div class='row imageGallery'>
                <?php
                $query = "SELECT * FROM tbgallery WHERE user_id = '$UserID'";
                $results = $mysqli->query($query);
                if ($results->num_rows > 0)
                {
                    while($row = $results->fetch_assoc())
                    {
                        echo "<div class='col-3' style='background-image: url(".$row["filename"].")'></div>";
                    }
                }
                else
                {
                    echo 	'<div class="alert alert-warning mt-3" role="alert">
                            You have not uploaded anything to your gallery yet
                        </div>';
                }
                mysqli_close($mysqli);
                ?>
            </div>
        </div>

	</div>
</body>
</html>