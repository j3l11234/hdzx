<?php
class RoomModel extends Model {
	public function types() {
		$types = array();
		foreach($this->group('type')->field('type')->select() as $item) {
			$types[] = $item['type'];
		}
		return $types;
	}
}
?>
