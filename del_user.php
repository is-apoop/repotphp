<html>
<head>
    <title>Add User</title>
    <link href="styles/style.css" rel="stylesheet" type="text/css"/>
	<script src="script/jquery-2.1.3.min.js" type="text/javascript"></script>  
</head>

<?php
	$link = mysqli_connect("localhost","root","Nikitos9305","report_php");

	if(!empty($_POST['ok'])) 
	{	
		// first delete the records marked for deletion. Why? Because we don't want to process them in the code below
		if( !empty($_POST['delete_ids']) and is_array($_POST['delete_ids'])) {
			// you can optimize below into a single query, but let's keep it simple and clear for now:
			foreach($_POST['delete_ids'] as $id) {
				$sql1 = "DELETE FROM users WHERE user_id=$id";
				$link->query($sql1);

				$sql2 = "DELETE * FROM reports WHERE user_id=$id";
				$link->query($sql2);
			}
		}
	}
?>

<body>
	<div id="container">
	    <div id="body">
	    	<a href="admin_index.php">Back to admin menu</a>

	    	<form method="post">
	    		<ul>
		    	<?php 
		    	$sql ="SELECT * FROM users WHERE isadmin=0 ORDER BY surname";
				$result = $link->query($sql);

			    while($user = mysqli_fetch_array($result)):?>
			    	<li>
			    		<input type="checkbox" name="delete_ids[]" value="<?=$user['user_id']?>">
			    		<?php echo $user['surname']." ".$user['name'];?>	
			    	</li>
			    <?php endwhile;?>
                </ul>
                <p><input type="submit" name="ok" value="Delete Checked" onclick = "return confirm('Are you sure you want to delete this?')"></p>
		    </form>
		</div>
	</div>

<script>
function alertUser() {
	var txt;
    var r = confirm("All records of user will be erased !!!");
    if (r == true) {
        txt = "You pressed OK!";
    } else {
        txt = "You pressed Cancel!";
    }
}
</script>

</body>
</html>