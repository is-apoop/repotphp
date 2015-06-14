<?php
require_once 'includes/global.inc.php';
require_once 'includes/functions.inc.php';
?>

<html>
<head>
    <title>Admin Homepage</title>
    <link href="styles/style.css" rel="stylesheet" type="text/css"/>
	<script src="script/jquery-2.1.3.min.js" type="text/javascript"></script>  
</head>

<?php
	
	$link = mysqli_connect("localhost","root","Nikitos9305","report_php");


	//first delete reports if any exist
	if(!empty($_POST['ok'])) 
	{	
		// first delete the records marked for deletion. Why? Because we don't want to process them in the code below
		if( !empty($_POST['delete_ids']) and is_array($_POST['delete_ids'])) {
			// you can optimize below into a single query, but let's keep it simple and clear for now:
			foreach($_POST['delete_ids'] as $id) {
				$sql = "DELETE FROM reports WHERE report_id=$id";
				$link->query($sql);
			}
		}
	}
?>

<body>
	<div id="container">

	    <div id="header">
			Hello, <?php echo $user->name; ?>. You are logged in. <a href="logout.php">Logout</a> <br/>

	    	<a class="topmenuitem" href="add_user.php">Добавить пользователся</a>
	    	<a class="topmenuitem" href="del_user.php">Удалить пользователся</a>
	    	<a class="topmenuitem" href="admin_index.php?show-weekly=1">Мой отчет</a>
	    	<a class="topmenuitem" href="admin_index.php?download-xlc=1">Скачать отчет</a>
	    </div>

	    <div id="body">

		    <div id="sidebar">
		    	<ul>
			    	<?php 
							
					$link = mysqli_connect("localhost","root","Nikitos9305","report_php");

					$sql ="SELECT * FROM users WHERE isadmin=0 ORDER BY surname";
					$result = $link->query($sql);

					while($user = mysqli_fetch_array($result)): ?>
						<li>
							<a class ="name" href="admin_index.php?user-id=<?=$user['user_id']?>"> <?php echo $user['surname']." ".$user['name']; ?> </a>
						</li>
					<?php endwhile;?>
		        </ul>
		    </div>

		    <div id="content">	

		    	<?php if(isset($_GET['user-id'])): ?>
			    	<form method="post" name="post">
				    	<?php
			    		$user_id_to_display = $_GET['user-id'];
			    		$sql ="SELECT * FROM reports WHERE user_id = '$user_id_to_display' ORDER BY rep_date DESC";
						$result = $link->query($sql);

			    		while($report = mysqli_fetch_array($result)): ?>
				    		<ul id="oldRow<?=$report['report_id']?>">		
				    		<li>Task:<textarea rows="1" cols="15" readonly><?=$report['task']?></textarea></li>
				    		<li>Time:<textarea rows="1" cols="10" readonly><?=$report['time']?></textarea></li>
				    		<li>Comment:<textarea rows="1" cols="30" readonly><?=$report['comment']?></textarea></li>
				    		<li>Day:<textarea rows="1" cols="2" readonly><?=$report['day']?></textarea></li>
				    		<li>Date:<textarea rows="1" cols="10" readonly><?=$report['rep_date']?></textarea></li>
				    		<li>
				    			On vacation:
				    			<?php 
				    				if($report['on_vacation'] == 1) echo 'yes';
				    				else echo 'no ';
				    			?>
				    		</li>
				    		<li>Check to delete <input type="checkbox" name="delete_ids[]" value="<?=$report['report_id']?>"></li>
				    		</ul>
				    	<?php endwhile;?>

				    	<p><input type="submit" name="ok" value="Save Changes"></p>
		    		</form>
	    		<?php endif;?>



	    		<?php
	    			if(isset($_GET['download-xlc']))
	    			{
	    				$objPHPExcel = new PHPExcel();
						// устанавливаем метаданные
						$objPHPExcel->getProperties()->setCreator("PHP")
						                ->setLastModifiedBy("Алексей")
						                ->setTitle("Office 2007 XLSX Тестируем")
						                ->setSubject("Office 2007 XLSX Тестируем")
						                ->setDescription("Тестовый файл Office 2007 XLSX, сгенерированный PHPExcel.")
						                ->setKeywords("office 2007 openxml php")
						                ->setCategory("Тестовый файл");
						

						// и начинаем заполнять таблицу
						$sql = "SELECT* FROM users";
						$result = $link->query($sql);
						
						$this_week_monday_date = date('Y-m-d',strtotime('monday this week'));
						$this_week_friday_date = date('Y-m-d',strtotime('friday this week'));


						$style_header = array(
						    'fill' => array(
						        'type' => PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb'=>'E1E0F7'),
						    )
						    
						);



						$userNumber=0;
						while($user = mysqli_fetch_array($result)) {

            			    $objWorkSheet = $objPHPExcel->createSheet($userNumber);
            			    //$sheet = $objPHPExcel->getActiveSheet();
            			    $objWorkSheet->setTitle($user['name'].' '.$user['surname']);

            			    $objWorkSheet->setCellValue('A1', 'Задание')
            			    	->setCellValue('B1', 'Описание')
            			    	->setCellValue('C1', 'Время выполнения')
            			    	->setCellValue('D1', 'Дата')
            			    	->setCellValue('E1', 'День')
            			    	->setCellValue('F1', 'Отпуск');

            			    $objWorkSheet->freezePane('A2');

            			    //now add reports
            			    $id = $user['user_id'];

            			    $sql2 = "SELECT * FROM reports WHERE user_id = $id AND rep_date>='$this_week_monday_date' AND rep_date<='$this_week_friday_date' ORDER BY rep_date";
            			    $result2 = $link->query($sql2);
            			    $reportNumber = 2;
            			   
            			    while($report = mysqli_fetch_array($result2)) {

            			    	if($report['day'] == 'MO' || $report['day'] == 'WE' || $report['day'] == 'FR')
            			    	{
            			    		$objWorkSheet->getStyle('A'.$reportNumber.':F'.$reportNumber)->applyFromArray( $style_header );
            			    		$day_changed = false;
            			    	}

            			    	$objWorkSheet->setCellValue('A'.$reportNumber, $report['task'])
            			    	->setCellValue('B'.$reportNumber, $report['comment'])
            			    	->setCellValue('C'.$reportNumber, $report['time'])
            			    	->setCellValue('D'.$reportNumber, $report['rep_date'])
            			    	->setCellValue('E'.$reportNumber, $report['day'])
            			    	->setCellValue('F'.$reportNumber, $report['on_vacation']);

            			    	$reportNumber++;
            			    }

            			    $objWorkSheet->getColumnDimension("A")->setWidth(30);
            			    $objWorkSheet->getColumnDimension("B")->setWidth(60);
            			    $objWorkSheet->getColumnDimension("C")->setWidth(20);
            			    $objWorkSheet->getColumnDimension("D")->setWidth(20);
            			    $objWorkSheet->getColumnDimension("E")->setWidth(20);

            			    $userNumber++;
						}

            			//сохраняем файл на сервере
            			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
						$objWriter->save('MyExcel.xlsx');
	    				
		    			// Выводим HTTP-заголовки
						header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
						header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
						header ( "Cache-Control: no-cache, must-revalidate" );
						header ( "Pragma: no-cache" );
						header ( "Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" );
						header ( "Content-Disposition: attachment; filename=MyExcel.xlsx" );

						// Выводим содержимое файла в браузер
						ob_clean();
						flush();
						readfile("MyExcel.xlsx");
					}
	    		?>


	    		<?php if(isset($_GET['show-weekly'])): ?>
	    				
	    		<?php endif;?>

		    </div>

		</div>
	</div>


</body>
</html>