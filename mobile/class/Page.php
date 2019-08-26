<?php
class Page {

	public function __construct(){}
	function nav(){
		global $page;
		//var_dump($page);
		$html = "<div class=\"nav_position\"><div class='box'><a href='/'>首页</a>";
			$i = 1;
			$len = count($page);
			foreach ($page as $key => $value) {

				$html .= "<span class='arrow'>></span>";
				$html .= $i==$len ? "<span class='current'>".$value["name"]."</span>" : "<a href='".$value["url"]."'>".$value["name"]."</a>";
				$i = $i+1;
			}
			//$html .= "sfadasfasdfas";
		$html .= "</div></div>";

		return $html;
	}
}
?>