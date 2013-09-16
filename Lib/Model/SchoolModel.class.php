<?php
class SchoolModel extends Model {
	public function getList() {
		$result = $this->order('align')->field('align', true)->select();
		if($result) {
			$school = array();
			foreach($result as $v) {
				$school[$v['schoolid']] = $v['name'];
			}
			return $school;
		}
		return array();
	}
}
?>
