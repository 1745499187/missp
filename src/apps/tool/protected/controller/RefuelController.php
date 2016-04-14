<?php

class RefuelController extends CController {
	protected $layout = 'index';
	
	public function indexAction() {
		$output = array();
		
		$this->render('refuel/index', $output);
	}
	
	public function showAction() {
		//oxy::instance()->init();
		$result = oxy::DB()->query("select (@rownum:=@rownum+1) as ROWID, a.* from refuel_log a, (select (@rownum :=0) ) b order by a.refuel_date");
		$rowNum = oxy::DB()->num_rows($result);
		
		$rows = array();
		while($row = oxy::DB()->fetch_assoc($result)) {
			$rows[$row['ROWID']] = $row;
		}
		
		$rowsDisp = array();
		$mileSum = 0;
		$amountSum = 0;
		$priceSum = 0;
		$quantitySum = 0;
		foreach(range(1, count($rows)) as $i) {
			if(! isset($rows[$i])) {
				continue;
			}
			$row = $rows[$i];
			$rowDisp = $row;
			switch ($row['FULL_REFUEL']) {
				case 0:
					$rowDisp['FULL_REFUEL'] = '否';
					break;
				case 1:
					$rowDisp['FULL_REFUEL'] = '是';
					break;
			}
			switch ($row['ROAD_CONDITION']) {
				case 0:
					$rowDisp['ROAD_CONDITION'] = '不清楚';
					break;
				case 1:
					$rowDisp['ROAD_CONDITION'] = '高速';
					break;
				case 2:
					$rowDisp['ROAD_CONDITION'] = '通畅';
					break;
				case 3:
					$rowDisp['ROAD_CONDITION'] = '一般';
					break;
				case 4:
					$rowDisp['ROAD_CONDITION'] = '拥堵';
					break;
				case 5:
					$rowDisp['ROAD_CONDITION'] = '十分拥堵';
					break;
			}
			$rowDisp['QUANTITY'] = round($row['AMOUNT'] / $row['PRICE'], 2);
			
			$deltaMile = 'N/A';
			$fuelConsume = 'N/A';
			if(isset($rows[$i - 1])) {
				$rowPrev = $rows[$i - 1];
				$deltaMile = $row['MILEAGE'] - $rowPrev['MILEAGE'];
				
				if($rowPrev['FULL_REFUEL'] == 1 && $row['FULL_REFUEL'] == 1) {
					$fuelConsume = round(($rowDisp['QUANTITY']) / ($deltaMile / 100), 2);
				}
			}
			$rowDisp['DELTA_MILE'] = $deltaMile;
			$rowDisp['FUEL_CONSUME'] = $fuelConsume;
			
			$rowsDisp[$i] = $rowDisp;
			
			if($mileSum < $row['MILEAGE']) {
				$mileSum = $row['MILEAGE'];
			}
			$quantitySum += $rowDisp['QUANTITY'];
			$priceSum += $rowDisp['PRICE'];
			$amountSum += $rowDisp['AMOUNT'];
		}
		
		$output = array();
		$output['rows'] = $rowsDisp;
		$output['total_mile'] = $mileSum;
		$output['total_amount'] = $amountSum;
		$output['total_quantity'] = $quantitySum;
		$output['avg_price'] = round($priceSum / count($rows), 2);
		$output['avg_fuel_consume'] = round($quantitySum / ($mileSum / 100), 2);

		$this->render('refuel/show', $output);
	}
	
	public function addAction() {
// 		$output = array();
		
// 		$this->render('refuel/index', $output);
		$this->responseJson($_POST);
	}
	
	public function modifyAction() {
		$output = array();
	
		$this->render('refuel/index', $output);
	}
	
	public function deleteAction() {
		$output = array();
		if(! isset($_REQUEST['id'])) {
			$output['respCode'] = -1;
			$output['respMsg'] = '参数丢失';
		}
		else {
			$itemId = $_REQUEST['id'];
			
			if(true) {
				$output['respCode'] = 0;
				$output['respMsg'] = $itemId;
			}
			else {
				$output['respCode'] = -1;
				$output['respMsg'] = '内部错误';
			}
		}
		$this->responseJson($output);
	}
}