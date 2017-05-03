<?php
class html {
	private $head;
	private $body;
	private $foot;
	
	function __construct($title, $container = 'container-fluid') {
		$this->head = '<!DOCTYPE html>'.
						'<html lang="en">'.
						'<head>'.
						'<meta charset="utf-8">'.
						'<meta http-equiv="X-UA-Compatible" content="IE=edge">'.
						'<meta name="viewport" content="width=device-width, initial-scale=1">'.
						'<title>'.$title.'</title>'.
						'<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>'.
						'<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">'.
						'<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">'.
						'<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>'.
						'<script src="js/bootstrap.js"></script>'.
						'</head><body><div class="'.$container.'">';
		$this->body = "";
		$this->foot = "</div></body></html>";
	}
	
	function appendBody($input) {
		$this->body .= $input;
	}
	
	function toHTML() {
		return $this->head . $this->body . $this->foot;
	}
}

class navBar {
	private $bar;
	private $items;
	private $active;
	
	function __construct($active) {
		$this->active = $active;
		$this->bar = <<<EOT
	<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">NetMan</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
EOT;
		$this->addItem("index.php", "Home");
		$this->addItem("downhosts.php", "Down Hosts");
	}
	
	function addItem($fileName, $name) {
		$this->items[] = array($fileName, $name);
	}
	
	function toHTML() {
		$output = '';
		foreach($this->items as $item) {
			$output .= '<li';
			if($this->active == $item[0]) $output .= ' class="active"';
			$output .= '><a href="'.$item[0].'">'.$item[1].'</a></li>';
		}
		return $this->bar.$output."</ul></div><!--/.nav-collapse --></div></nav><br><br><br>";
	}
}

class gridRow {
	private $output;
	
	function __construct($cols, $data) {
		$this->output = '<div class="row">';
		foreach($cols as $index => $colSize) {
			$this->output .= '<div class="col-md-'.$colSize.'">'.$data[$index].'</div>';
		}
		$this->output .= '</div>';
	}
	
	function toHTML() {
		return $this->output;
	}
}

class gridRowBuilder {
	private $cols;
	private $colSize;
	private $output;
	private $colDataArray;
	
	function __construct($cols) {
		$this->cols = $cols;
		$this->colSize = 12/$cols;
		$this->output = '';
		$this->colDataArray = array();
	}
	
	function addCol($input) {		
		$this->colDataArray[] = $input;
		// If we have enough data to build a row, do it
		if($this->cols == sizeof($this->colDataArray)) {
			$colSizeArray = array_pad(array(), $this->cols, $this->colSize);
			$row = new gridRow($colSizeArray, $this->colDataArray);
			$this->output .= $row->toHTML();
			$this->colDataArray = array();
		}
	}
	
	function toHTML() {
		// If there is data waiting to be output...
		while(sizeof($this->colDataArray) > 0) {
			$this->addCol('');
		}
		return $this->output;
	}
}

class panel {
	private $heading;
	private $body;
	private $type;
	
	function __construct($heading, $body, $type = "primary") {
		$this->heading = $heading;
		$this->body = $body;
		$this->type = $type;
	}
	
	function append($input) {
		$this->body .= $input;
	}
	
	function toHTML() {
		$output = "<div class=\"panel panel-".$this->type."\">";
		$output .= "<div class=\"panel-heading\"><h3 class=\"panel-title\">".$this->heading."</h3></div>";
		$output .= "<div class=\"panel-body\">".$this->body."</div></div>";
		return $output;
	}
}

class table {
	private $headers;
	private $input;
	
	function __construct($input = array()) {
		$this->input = $input;
		return $this->toHTML();
	}
	
	function addRow($input) {
		$this->input[] = $input;
	}
	
	function headers($input) {
		$output = "<thead><tr>";
		foreach($input as $value) {
			$output .= '<th>'.$value.'</th>';
		}
		$output .= "</tr></thead>\n";
		$this->headers = $output;
	}
	
	function toHTML() {
		$even = false;
		$output = "<table class=\"table table-striped\">";
		$output .= $this->headers;
		foreach($this->input as $key => $tableRow) {
			$output .= "<tr>";
			foreach($tableRow as $rowKey => $rowData) {
				$output .= "<td style=\"padding: 2px 20px 2px 2px;\">".$rowData."</td>";
			}
			$output .= "</tr>\n";
			$even = !$even;
		}
		$output .= "</table>";
		return $output;
	}
}

class modal {
	private $id;
	
	function __construct($id) {
		$this->id = $id;
	}
	function toHTML() {
		?>
		<!-- Modal -->
		<div id="<?php echo $this->id; ?>" class="modal fade" role="dialog">
		  <div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Modal Header</h4>
			  </div>
			  <div class="modal-body">
				<p>Some text in the modal.</p>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			  </div>
			</div>

		  </div>
		</div>
		<?php
	}
}

class form {
	private $output;
	
	function __construct() {
		$this->output = '<form class="form-inline"> ';
	}
	
	function addText($id, $placeholder, $value = '', $label = ' ', $size = '') {
		$this->output .= $label.'<input type="text" id="'.$id.'" placeholder="'.$placeholder.'" size="" value="'.$value.'" class="form-control">';
	}
	
	function addButton($title, $onClick) {
		$this->output .= '<button type="button" onclick="'.$onClick.'" class="btn btn-primary">'.$title.'</button>';
	}
	
	function toHTML() {
		return $this->output.'</form>';
	}
}
?>