	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<!-- Title -->
				<title><?php echo($this->getPage()->getTitle()) ?></title>
				<!-- CSS -->
				<?php echo($this->getPage()->getStylesheets()) ?>
				<!-- JavaScript -->
				<?php echo($this->getPage()->getJavascripts()) ?>
			</head>
			<body>
				<!-- Show View -->
				<?php $this->renderView() ?>
			</body>
	</html>
