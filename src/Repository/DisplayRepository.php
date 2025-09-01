<?php
 
namespace App\Repository;

use App\Entity\Module;	

 
 
/**
 * Index repository
 */
class DisplayRepository  
{


	protected $db;
	
	
	 public function __construct($db)
    {
    	$ini_array = parse_ini_file("../.gwatch", true);
    	
        $this->db = $db;
        $this->dbprefics = $ini_array['gwatch']['my_sch_prefix'];
        $this->dbmanagemant = $ini_array['gwatch']['my_sch_mgr'];
    }
	
 	protected function module($module){
		
		return $this->dbprefics . trim($module);
	
	}
    
	public function ReadColumnInfo($module)
	{
	
 		$sql = "
 		SELECT 
 			max(col) as num 
 		FROM 
 			".$this->module( $module ).".col;";
 		$stmt = $this->db->prepare($sql);
 		$stmt->execute();
 		$result = $stmt->fetch(\PDO::FETCH_ASSOC); 
 		
 		$colInfo["NumDataColumns"] = $result['num'];
	
 		$sql = "
 		SELECT 
 			* 
 		FROM 
 			".$this->module( $module ).".`col` 
 		ORDER BY 
 			col;";

 		$stmt = $this->db->prepare($sql);
 		$stmt->execute();
 		 

 		$i = 0;
		$gr = 0;
		$test = ''; 
		$subs = 3;
		
		$ms = 0;
		while ($rowch  = $stmt->fetch(\PDO::FETCH_ASSOC))
		{
		
	 		//$colInfo["NumDataColumns"] ++;
			$stat = $bg = '';
			if (isset($rowch['statname'])) $stat = $rowch['statname'];
			$prvTest = $test;
			$test = substr($rowch["test"], 0, $subs);
			$rowch["test"] = trim($rowch["test"]);
			$col = $rowch["col"];
			if(isset($rowch["bg"]))$bg =  $rowch["bg"];
			if ($prvTest != $test)
			{
				if ($prvTest != '')
				{
					$colInfo[$i] = array(
						"Name" => "$prvTest",
						"DataColumn" => - 1,
						"StatName" => "$stat",
						"GroupIndex" => $gr,
						"IdColumn" => $ms,
						"bg" => $bg
					);
					$gr++;
					$i++;
					$ms++;
				}

				$colInfo[$i] = array(
					"Name" => "$test",
					"DataColumn" => - 1,
					"StatName" => "$stat",
					"GroupIndex" => $gr,
					"IdColumn" => $ms,
						"bg" => $bg
				);
				$i++;
				$ms++;
			}

			$colInfo[$i] = array(
				"Name" => $rowch["test"],
				"DataColumn" => $col,
				"StatName" => "$stat",
				"GroupIndex" => $gr,
				"IdColumn" => $ms,
						"bg" => $bg
			);
			$i++;
			$ms++;
		}

		$colInfo[$i] = array(
			"Name" => "$test",
			"DataColumn" => - 1,
			"StatName" => "$stat",
			"GroupIndex" => $gr,
						"IdColumn" => $ms,
						"bg" => $bg
		);
		$ms++;
		$i++;
		$col++;
		$colInfo[$i] = array(
			"Name" => "Genes",
			"DataColumn" => $col,
			"StatName" => "$stat",
			"GroupIndex" => $gr,
					"IdColumn" => $ms,
						"bg" => $bg
		);
		$ms++;
		$i++;

		$colInfo[$i] = array(
			"Name" => "$test",
			"DataColumn" => - 1,
			"StatName" => "$stat",
			"GroupIndex" => $gr,
						"IdColumn" => $ms,
						"bg" => $bg
		);
		$ms++;
		$i++;
		$col++;
	
/*			$colInfo[$i] = array(
			"Name" => "Effect",
			"DataColumn" => $col,
			"StatName" => "$stat",
			"GroupIndex" => $gr,
					"IdColumn" => $ms,
						"bg" => $bg
		);
		$ms++;
		$i++;

*/
		$colInfo["NumColumns"] = $i;
		return $colInfo;
	}

 
 
	public function getSnpAndPatientsNumber($module){
		if($module)
		{
			$q = "
				SELECT 
					count(*) as snp
				FROM 
					". $this->module($module) .".ind 
				 ";
		
		
			$stmt = $this->db->prepare($q);
			$stmt->execute();
			$rowch = $stmt->fetch(\PDO::FETCH_ASSOC);
			$md['snp'] = $rowch['snp'];

			$check = "SHOW TABLES FROM ".$this->module( $module )." LIKE 'phenotypes'";
			$stmt = $this->db->prepare($check);
			$stmt->execute();
			$flag = $stmt->fetch(\PDO::FETCH_ASSOC);
			
			 
			
			if($flag != 0){
				$q = "
					SELECT 
						count(*) as patients
					FROM 
						". $this->module($module) .".phenotypes 
					 ";
	
	
				$stmt = $this->db->prepare($q);
				$stmt->execute();
				$rowch = $stmt->fetch(\PDO::FETCH_ASSOC);			
				$md['patients'] = $rowch['patients'];
			
			
			}
			else{ 
				$md['patients'] = false;
			}
			return $md;
		}
		else
			return false;

	}
	
 
 
 
 
	public function getModuleDescription($module){
		if($module)
		{
			$q = "
				SELECT 
					title, 
					description 
				FROM 
					". $this->dbmanagemant .".module 
				WHERE 
					name = '$module';";
		
		
			$stmt = $this->db->prepare($q);
			$stmt->execute();
	 
			$rowch = $stmt->fetch(\PDO::FETCH_ASSOC);
			$md['title'] = $rowch['title'];
			$md['description'] = $rowch['description'];
			return $md;
		}
		else
			return false;

	}
	

	public function ReadRowCount($module, $chr)
	{	


    	$stmt = $this->db->prepare("SELECT chrlen FROM ".$this->module( $module ).".chrsupp WHERE chr = $chr");
    	$stmt->execute();
    	return $stmt->fetchColumn();
 	 
		 

	}

	public function ReadRowData($module, $chr, $numDataColumns, $rowCount, $rowDataStart, $rowDataCount, $returnHeight, $heightScale)
	{

		 
	
		$sql = "SELECT * FROM ".$this->module( $module ).".`col` ORDER BY col;";
 		$stmt = $this->db->prepare($sql);
 		$stmt->execute();

		while ($md  = $stmt->fetch(\PDO::FETCH_ASSOC))
		{
		if(isset($md['islog']) && $md['islog']){
			$n = $md['col'];
			$colslog[$n] = $md['islog'];
		
			}
	
		}
	
		$currentRowHeader = 0;
		$trackBlock = array();
		$rowDataEnd = $rowDataStart + $rowDataCount;
		$dataBlock["NumColumns"] = $numDataColumns;
		$dataBlock["NumRows"] = $rowDataCount;
						
		$q="
		SELECT 
			c.chr, c.chrname, nrow, a.alias, p.pos, maf as maf, 1 AS marker, 
				(SELECT  
					g.`gene`  
				FROM   
					GWATCH.genes g 
				WHERE 
					g.posstart < p.pos 
				AND 
					g.posend > p.pos 
				AND 
					g.chr = $chr
				AND
					g.build = 38
				LIMIT 1
				) as gene
		 FROM 
		 	".$this->module( $module ).".ind r 
		 JOIN 
		 	".$this->module( $module ).".alias a USING ( ind ) 
		 JOIN 
		 	".$this->module( $module ).".chr c USING ( chr ) 
		 JOIN 
		 	".$this->module( $module ).".pos p USING ( ind ) 
		 JOIN 
		 	".$this->module( $module ).".maf m USING ( ind ) 


		 WHERE 
		 	r.chr = $chr 
		 AND 
		 	r.nrow >=$rowDataStart 
		 AND 
		 	r.nrow <=$rowDataEnd 
		 AND 
		 	r.nrow >0 
		 ORDER BY 
		 	r.nrow;";
 		$stmt = $this->db->prepare($q);
 		$stmt->execute();
		@$i = 0;
		
		while ($rowch = $stmt->fetch(\PDO::FETCH_ASSOC))
		{
			if(isset($rowch["effect"] ))$effect = $rowch["effect"] ; else $effect = '';
			$dataBlock["Rows"][$i] = array(
				"marker" => $rowch["marker"],
				"Name" => $rowch["alias"],
				"nrow" => $rowch["nrow"],
				"Coords" => $rowch["pos"],
				"MAF" => round($rowch["maf"], 5),
				"Gene" => $rowch["gene"] ,
				"Effect" => $effect  
			);
		
			$trackBlock[$i] = $rowch["gene"];
			$effectBlock[$i] = $effect;
	 
		
			$i++;
		}
		$pv = '';
		if ($returnHeight) 
			$pv = " AND pval <= 4 AND pval > 0 "; 
			$q = "
			SELECT 
				nrow, col,  pval,ratio , v.v_ind
			FROM 
				".$this->module( $module ).".ind i
			JOIN 
				".$this->module( $module ).".v_ind v using(ind)
			JOIN  
				".$this->module( $module ).".pval using(v_ind)
			JOIN  
				".$this->module( $module ).".ratio using(v_ind)
			WHERE 
				i.chr = $chr 
			AND 
				nrow >= $rowDataStart 
			AND 
				nrow <= $rowDataEnd  
				$pv
			ORDER BY 
				v.v_ind";
		
	
		@$j = 0;
		$cols = $dataBlock["NumColumns"] + 1;
		
		$stmt = $this->db->prepare($q);
 		$stmt->execute();
		$i = 0;
		$colslog = false;
		while ($rowch = $stmt->fetch(\PDO::FETCH_ASSOC))
		{
		
			if( $rowch['nrow'] <= 200 )$rowch['nrow']--;
			@$row = $rowch['nrow'] - $rowDataStart;
			
			
			
			@$col = $rowch['col'];
			if ($returnHeight)
			{
			
				$height = (round(log10($rowch["pval"]) , 3) + 2) * -$heightScale;
			 
				if($colslog){
					if($colslog[$col] == 1)
					$height = (round($rowch["pval"], 3) + 2) * -$heightScale;
			
				}
			 
				if ($height < 0.0) continue;
				$dataBlock[$row][$col] = array(
					$height,
					$rowch["ratio"]
				);
			}
			else
			{
				if ($rowch["pval"] > 0) $dataBlock[$row][$col] = array(
			
					-log10($rowch["pval"]) ,
					$rowch["ratio"]
				);
				else $dataBlock[$row][$col] = array(-1, -1
				);
			}
	 
			//print_r($dataBlock);
		
		
			@$j++;
		
		}
		 
			//add track - genes
			$prevvalue = false;
			$color = -1;
			foreach($trackBlock as $key => $value){
	
				if($value != ''){	
			
				if($value != $prevvalue)
					$color = -$color;
			
					$dataBlock[$key][$cols] = array(0, $color);		
		
				 $j++;
				$prevvalue = $value;
				}
		
			}
		
			//add track - effects 
			/*
			$color = -1;
			$newcols = $cols + 1;
			foreach($effectBlock as $key => $va){
	
				if($va != null){	
			
				if($va != $prevva)
					$color = -$color;
			
					$dataBlock[$key][$newcols] = array(10, $color);		
		
				 $j++;
				$prevva = $va;
				}
		
			}
		
		 */


		// read the row data.
		// Skip any rows that aren't relevant to what we're looking for.
		// we still need to read them but we don't need to
		// store them

		$dataBlock["NumDataPoints"] = $j+$i;

		$level = 1; //treshhold for search replication

		foreach($dataBlock as $k => $r){
	
			if(is_numeric($k)){
			
				foreach($r as $key => $value){
		 
					if(isset( $columns[$key])){

						$master = $columns[$key];
						$slave = $key;

						if((!$dataBlock[$k][$master][0]) || ($dataBlock[$k][$master][0] < $level)){
				 
							unset( $dataBlock[$k][$slave]);

							$dataBlock["NumDataPoints"]--;
					
			
						}
					
					}
	
				}
			
			}
		
		}
 
		return $dataBlock;
	
	}
	public function getEyePos( $eyeposParam )
	{
		 
		if ( isset( $eyeposParam ) )
		{
			$eyeposParam = str_replace( ' ', '', $eyeposParam );
			// check it matches the right format
	
			if ( preg_match( "/^\[[\d\.\-]+\,[\d\.\-]+\,[\d\.\-]+\]$/", $eyeposParam ) )
			{
				// echo the parsed eye pos
				return $eyeposParam;
				 
			}

		}

		// use the default
		return "[0, 40, 70]";
	}

	public function getDistance( $distanceParam )
	{
		 
		if ( isset( $distanceParam ) )
		{
			$distance = floatval( $distanceParam );
			if ( $distance < 5 )
			{
				$distance = 5;
			}
	
			return $distance;
	
			 
		}

		return "80";
	}

	public function getAngleX( $anglexParam )
	{
		 
		if ( isset( $anglexParam ) )
		{
			$anglex = floatval( $anglexParam );
			$halfPi = pi() * 0.5;
			if ( $anglex < - $halfPi )
			{
				$anglex = -$halfPi;
			}

			if ( $anglex > $halfPi )
			{
				$anglex  = $halfPi;
			}
	
			return $anglex;
	
			 
		}
 
		return "0.0";
	}

	public function getOffsetX( $offsetxParam )
	{
		 
		if ( isset( $offsetxParam ) )
		{
			$offsetx = floatval( $offsetxParam );
			return $offsetx;
	
			 
		}
		return "0.0";
	}
		
	
	public function getCurrentRow($urlRow)
	{
		 
		if ( isset( $urlRow ) )
		{
			return $urlRow;
		}
		else
		{
			return "0";
		}

	}

	public function getCurrentURL()
	{
		return (!empty($_SERVER['HTTPS'])) ? "http://".$_SERVER['SERVER_NAME'].'/display.php' : "http://".$_SERVER['SERVER_NAME'].'/display.php';
		//return (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
	}

	public function ReturnError($error)
	{
		$errorMsg["Error"] = $error;
		echo json_encode($errorMsg);
		exit(0);
	}
		
		
	public function getTreshhold( $threshold ){


		$ts = array(1,2,3,4);
		if(in_array($threshold, $ts)) 
			return exp(-($_GET['threshold'] + 0) * log10(10));
		else 
			return exp(-(2 + 0) * log10(10)); 


	}
		
	public function GetBuildAndPlatform($module){
		if($module)
		{
			$q = "
				SELECT 
					build, 
					platform 
				FROM 
					". $this->dbmanagemant .".project 
				WHERE 
					name = '$module';";
		
		
		//	$stmt = $this->db->prepare($q);
		//	$stmt->execute();
	 
			//$rowch = $stmt->fetch(\PDO::FETCH_ASSOC);
			$md['build'] = 37;//$rowch['build'];
			$md['platform'] = ''; //$rowch['platform'];
			return $md;
		}
		else
			return false;

	}
	
		public function GetSearchType($SearchTerm){
	
		if(is_numeric($SearchTerm) && $SearchTerm > 0){
		
			if($SearchTerm <= 20){
			
				 
				return 3;  // log(p)
		
			}
			elseif(preg_match('/^\+?\d+$/', $SearchTerm) && $SearchTerm > 20){
			
				 
				return 1;  // coordinates
		
			}
			else {
			
			 
				return -1;
			}
		}
		else{
			if(preg_match('/^rs\d+$/', $SearchTerm) || preg_match('/^kgp\d+$/', $SearchTerm) || $SearchTerm == -1)
		 
				return 0; //SNP
			
			else{
				 
				return 2; // GENE
			
			}
	
	
		}
		}
		public function addSpace($var, $space, $count){
	
			$adprobel = '';
	
			$commas = substr_count($var, ',');
	
			for($i=0;$i<$commas;$i++){
	
				$adprobel = "$adprobel"."&nbsp;";
	
			}
	
			$ad = ($count - strlen($var)) * strlen($space) + strlen($var);
	
			return str_pad($var,$ad,$space)."$adprobel";

		}
		
		public function createCompareLinks($module, $chr, $pos){
		

			
			$q = "SELECT link as compare FROM ". $this->dbmanagemant .".module WHERE name =  '$module'";
			$stmt = $this->db->prepare($q);
			$stmt->execute();
			$md = $stmt->fetch(\PDO::FETCH_ASSOC);
			if( $md['compare'] ){
 			
 				$names = explode(',', $md['compare']);
 				
					foreach($names as $key=>$value){
					
					$value = trim($value);
				
					$query = "SELECT i.ind FROM Module_" . $value . ".ind i JOIN Module_" . $value . ".pos p USING(ind) WHERE i.chr = ".$chr." AND p.pos = $pos";
					$stmt = $this->db->prepare($query);
					$stmt->execute();
					$ms = $stmt->fetch(\PDO::FETCH_ASSOC);
				
				
				
				
					if( $ms['ind'] != '' ){
				 
						$ind = $ms['ind']; 

					}	

					else{

						$query = "SELECT i.ind FROM Module_" . $value  . ".ind  i JOIN Module_" . $value . ".pos p USING(ind) WHERE i.chr = ".$chr." AND ( p.pos < $pos OR  p.pos > $pos) ORDER BY p.pos DESC LIMIT 1";
						$stmt = $this->db->prepare($query);
						$stmt->execute();
						$ms = $stmt->fetch(\PDO::FETCH_ASSOC);
			 
						$ind = $ms['ind']; 

					}
				
								
					if($ind) {
					
						$query1 = "SELECT chr, nrow FROM ". $this->module($value) .".ind WHERE ind = $ind";
						$stmt = $this->db->prepare($query1);
						$stmt->execute();
						$mi = $stmt->fetch(\PDO::FETCH_ASSOC);
					
						$data[$key]['chr'] = $mi['chr'];
						$data[$key]['nrow'] = $mi['nrow'];
						$data[$key]['module'] = $value;
					
					}
					
				}	
							
			}
		//print_r($data);
			if($data)
				return $data;
			else 
				return false;
		
		}
		
		
		
		public function getChrDescription($module){
			
			$q = "SELECT * FROM ".$this->module( $module ).".chrsupp;";
			$stmt = $this->db->prepare($q);
			$stmt->execute();
			@$i = 0;
			while ($chrres = $stmt->fetch(\PDO::FETCH_ASSOC)){
				$nchr = $chrres['chr'];
				$chrsupp[$nchr] = $chrres['chroff'];
			}
			return $chrsupp;

		}
		public function search($module, $chr, $searchTerm, $searchType, $type){
		
		$found = 0;
		$result = Array();
		$results = "";
		$j=0;

	 	$chrsupp = $this->getChrDescription($module);
		
		if ( $searchType == 0) //SNP
		{	
			if($type == 1)$chrselect = " i.chr = $chr AND "; 
			else $chrselect = " ";
 	
			$q = "
			SELECT 
				* 
			FROM 
				".$this->module( $module ).".alias 
			JOIN 
				".$this->module( $module ).".ind i using(ind) 
			JOIN 
				".$this->module( $module ).".pos USING(ind) 
			WHERE 
				$chrselect alias LIKE '%$searchTerm%' 
			AND 
				i.nrow > 0 
			ORDER BY 
				cast(chr as UNSIGNED), nrow;";
			$stmt = $this->db->prepare($q);
			$stmt->execute();
			
			if($stmt->rowCount() == 0)
			{
				return FALSE;				
			}
			while ($rowch = $stmt->fetch(\PDO::FETCH_ASSOC))
			{
	 
				$curchr=$rowch['chr'] + 0;
				$result[$j][ 0 ] = $this->addSpace($rowch['alias'] , '&nbsp;&nbsp;', 13).$this->addSpace($rowch['chr'], '&nbsp;&nbsp;', 6).$this->addCommas($rowch['pos']);
				$result[$j][ 1 ] = $rowch["ind"] - $chrsupp[$curchr];
				$result[$j][ 2 ] = $rowch["chr"];

				$j++;
			} 
		}
		else if ( ( $searchType == 1 ) && ( is_numeric( $searchTerm ) ) ) //position
		{
	
 			$q = "
 			SELECT 
 				* 
 			FROM 
 				".$this->module( $module ).".alias 
 			JOIN 
 				".$this->module( $module ).".ind i using(ind) 
 			JOIN 
 				".$this->module( $module ).".pos p using(ind) 
 			WHERE  
 				i.chr = $chr 
 			AND 
 				p.pos = $searchTerm 
 			AND 
 				i.nrow > 0 
 			ORDER BY  pos;"; 
			
			$stmt = $this->db->prepare($q);
			$stmt->execute();
			if($rowch = $stmt->fetch(\PDO::FETCH_ASSOC))
			{
		
		
				$result[0][ 0 ] = $this->addSpace($rowch['alias'], '&nbsp;&nbsp;', 13).$this->addCommas($rowch['pos']);
				$result[0][ 1 ] = $rowch["nrow"];

	 
			} 
			else 
			{
 				$q = "
 				SELECT 
 					* 
 				FROM 
 					".$this->module( $module ).".alias 
 				JOIN 
	 				".$this->module( $module ).".ind i using(ind) 
	 			JOIN 
	 				".$this->module( $module ).".pos using(ind) 
	 			WHERE  
	 				i.chr = $chr 
	 			AND 
	 				pos > $searchTerm 
	 			AND 
	 				i.nrow > 0 
	 			ORDER BY 
	 				pos 
	 			LIMIT 5;"; 
				$stmt = $this->db->prepare($q);
				$stmt->execute();
				while($rowch1 = $stmt->fetch(\PDO::FETCH_ASSOC))
				{
			 
					$result[$j][ 0 ] = $this->addSpace($rowch1['alias'], '&nbsp;&nbsp;', 13).$this->addCommas($rowch1['pos']);
					$result[$j][ 1 ] = $rowch1["nrow"];

					$j++;
				} 
				$q = "
				SELECT 
					* 
				FROM 
					".$this->module( $module ).".alias 
				JOIN 
					".$this->module( $module ).".ind i using(ind) 
				JOIN 
					".$this->module( $module ).".pos using(ind) 
				WHERE  
					i.chr = $chr 
				AND 
					pos < $searchTerm 
				AND 
					i.nrow > 0 
				ORDER BY  
					pos DESC 
				LIMIT 5;"; 
				$stmt = $this->db->prepare($q);
				$stmt->execute();
				while($rowch2 = $stmt->fetch(\PDO::FETCH_ASSOC))
				{
			 
					$result[$j][ 0 ] = $this->addSpace($rowch2['alias'], '&nbsp;&nbsp;', 13).$this->addCommas($rowch2['pos']);
					$result[$j][ 1 ] = $rowch2["nrow"];

					$j++;
				} 
			}
			 

		}
		else if ( $searchType == 2 ) //GENES
		{	

			if($type == 1)$chrselect = "AND   chr = '$chr' ";
				else $chrselect = " ";

 
 

		 	$q = "
			SELECT 
				gene, chr, posstart,  posend 
			FROM 
				". $this->dbmanagemant .".genes
			WHERE 
				gene LIKE '%$searchTerm%' $chrselect
			ORDER BY 
				cast(chr as UNSIGNED), posstart;"; 
			
			
			$stmt = $this->db->prepare($q);
			$stmt->execute();
			@$i = 0;
				 
			while ($genes = $stmt->fetch(\PDO::FETCH_ASSOC))
			{
  			 
			//	if($type != 0){
		
 					$chrselect1 = "AND   chr = $genes[chr] "; 
		 
			//	}

				$q = "
				SELECT 
					chr, nrow, alias , pos
				FROM 
					".$this->module( $module ).".ind 
				JOIN 
					".$this->module( $module ).".alias USING(ind) 
				JOIN 
					".$this->module( $module ).".pos USING(ind) 
				WHERE  
					pos >= $genes[posstart] 
				AND 
					pos <= $genes[posend]   
				$chrselect1 
				AND  
					nrow > 0 
				ORDER BY 
					cast(chr as UNSIGNED), nrow;"; 
				$stmt1 = $this->db->prepare($q);
				$stmt1->execute();
				while ($rowgen = $stmt1->fetch(\PDO::FETCH_ASSOC))
				{
		
					$result[$j][ 0 ] = $this->addSpace($rowgen['alias'], '&nbsp;&nbsp;', 13).$this->addSpace($rowgen['chr'], '&nbsp;&nbsp;', 6).$this->addSpace($this->addCommas($rowgen['pos']), '&nbsp;&nbsp;', 13)."$genes[gene]";
					$result[$j][ 1 ] = $rowgen["nrow"];
					$result[$j][ 2 ] = $rowgen["chr"];

					$j++;
				}
			} 
		}

		else if ( $searchType == 3 )//log(p)
		{	
		 
			$searchTerm=exp(-($searchTerm + 0) * log10(10));
	
			if($type == 1)$chrselect = " chr = $chr AND ";
			else $chrselect = " ";

	
	
		
			$q = "
			SELECT 
				pval, v_ind, chr, alias, pos , nrow, ind
			FROM 
				".$this->module( $module ).".pval
			JOIN 
				".$this->module( $module ).".v_ind USING ( v_ind )
			JOIN 
				".$this->module( $module ).".ind USING ( ind )
			JOIN 
				".$this->module( $module ).".alias USING(ind)
			JOIN 
				".$this->module( $module ).".pos USING(ind)
			WHERE 
				$chrselect pval < $searchTerm
			AND 
				pval > 0
			ORDER BY 
				pval ASC
			LIMIT 100
			"; 
			$stmt = $this->db->prepare($q);
			$stmt->execute();
	
	
			if($stmt->rowCount() == 0)
			{
				return FALSE;
			}
 
	 
			while ($rowch = $stmt->fetch(\PDO::FETCH_ASSOC))
			{
	 
			   $curchr=$rowch['chr'] + 0;
	   
 
				$result[$j][ 0 ] = $this->addSpace($rowch['alias'], '&nbsp;&nbsp;', 13).$this->addSpace($rowch['chr'], '&nbsp;&nbsp;', 4).$this->addSpace($this->addCommas($rowch['pos']), '&nbsp;&nbsp;', 13)."  -log(P)=". -round(log10($rowch['pval']),5);
				$result[$j][ 1 ] = $rowch["nrow"];// - $chrsupp[$curchr];
				$result[$j][ 2 ] = $rowch['chr'];

			$j++;
			}  
			

			
		}

	
			return $result;
		
		}
		
	// Removed polarization methods - not needed for new schema
	
	public function isPolarization($module)
	{
		return false; // Always false for new schema
	}
	
	
		// Removed getPolarizationData method - not needed for new schema	
		
		
		
		
		
		
		
		
			
		
		private function addCommas($nStr)
		{
			$new = '';
			$i=strlen($nStr.'');
			$nStr = str_split( $nStr.'');
			for($i = sizeof($nStr)-1; $i >= 0; $i--)
			{
				
				 
				
				$new = $nStr[$i]."$new";
				if((sizeof($nStr)-$i)%3 == 0 && sizeof($nStr) != $i && $i != 0)$new = ','.$new;
			 
			}
			
		return $new;
		}
		
		
	private function addRect($dom, $parent, $left, $color)
	{
		$box = $dom->createElement("use", null);
		$box->setAttribute("xlink:href", "#".$color);
		$box->setAttribute("x", $left);
		$parent->appendChild($box);
	}

	private function addDefBox($dom, $parent, $id, $color)
	{
		$box = $dom->createElement( 'rect', null);
		
		$box->setAttribute('id', $id);
		$box->setAttribute('width', 10);
		$box->setAttribute('height', 10);
		$box->setAttribute('fill', $color);
		$box->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');

		$parent->appendChild($box);
	}

	private function getColorPValue($logP)
	{

		// if ( !isset( $logP ) ) // missing data

		if ($logP == - 1) // missing data
		{
			return 'w';
		}

		if ($logP == 0)
		{
			return 'g';
		}

		if ($logP < - 5)
		{
			return 'bl';
		}

		if ($logP < - 4)
		{
			return 'b';
		}

		if ($logP < - 3)
		{
			return 'r';
		}

		if ($logP < - 2)
		{
			return 'o';
		}

		if ($logP < - 1.3)
		{
			return 'y';
		}

		return 'g';
	}

	public function generateGWASArray($request)
	{
		$reportRow = $request->query->get('row');
		if (!isset($reportRow))
		{
			$this->ReturnError("No row specified");
		}
	
	
	 
		$chr = $request->query->get('chr');
		if (!isset($chr))
		{
			$this->ReturnError("No chromosome specified");
		}

		$fileName = trim($request->query->get('module'));
		if (!$fileName)
		{
			$this->ReturnError("No module specified");
		}

		$file = $fileName;

		if (!$file)
		{
			$this->ReturnError("Could not open table.");
		}
		
		$stageMask = $request->query->get('stageMask');
 

		$BuildAndPlatform = $this->GetBuildAndPlatform($fileName);
		$colInfo = $this->ReadColumnInfo($file, $stageMask);
	//print_r($colInfo);
		$numColumns = $colInfo["NumColumns"];
		$numDataColumns = $colInfo["NumDataColumns"];
		$rowCount = $this->ReadRowCount($file,$chr);
		$rowDataStart = $reportRow - 50;
		if ($rowDataStart < 0) $rowDataStart = 0;
		$rowDataCount = 100;

		// false means return the data as logP

		$dataBlock = $this->ReadRowData($file, $chr, $numDataColumns, $rowCount, $rowDataStart, $rowDataCount, false, 1.0);

		//echo json_encode($dataBlock);

		$topOfBlocks = 200;
		$leftOfBlocks = 250;
		
		$dom = new \DOMDocument('1.0', 'utf-8');

		$dom->formatOutput = true; // TODO, remove this to save bandwidth later

		$svg = $dom->createElement( 'svg',  null);
		
		$svg->setAttribute('width', '29.7cm');
		$svg->setAttribute('height', '21.0cm');
		$svg->setAttribute('preserveAspectRatio', 'xMinYMin meet');
		$svg->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
		$svg->setAttribute('xmlns:xlink', "http://www.w3.org/1999/xlink");
		$svg->setAttribute('version', '1.1');
		
		
		$defs = $dom->createElement('defs');
		$this->addDefBox($dom, $defs, 'r', 'red');
		$this->addDefBox($dom, $defs, 'w', 'white');
		$this->addDefBox($dom, $defs, 'g', 'lightgrey');
		$this->addDefBox($dom, $defs, 'y', '#f7f5ca');
		$this->addDefBox($dom, $defs, 'o', 'orange');
		$this->addDefBox($dom, $defs, 'b', 'blue');
		$this->addDefBox($dom, $defs, 'bl', 'black');
		$svg->appendChild($defs);
		$style = $dom->createElement('style', 'text { font-size: 8pt; font-family: Arial, sans-serif }');
		$svg->appendChild($style);
	
		$rows = $dataBlock["Rows"];
		$numRows = count($rows);
	
		$relativeRow = $reportRow - $rowDataStart;
		for ($i = 0; $i < $numRows; ++$i)
		{
			$rowName = $rows[$numRows - $i - 1]["Name"];
			$maf = sprintf("%01.5f", $rows[$numRows - $i - 1]["MAF"]);
			$text = $dom->createElement( 'text', $rowName);
			
			$text->setAttribute("x", 5);
			$text->setAttribute("y", $topOfBlocks + 8 + $i * 10);
			
			if ($i == $relativeRow)
			{
				$reportRowName = $rowName;
			}

			$svg->appendChild($text);
			$text = $dom->createElement('text', $maf );
			$text->setAttribute("x", $leftOfBlocks / 2 + 20);
			$text->setAttribute("y", $topOfBlocks + 8 + $i * 10);
			$text->setAttribute('text-anchor', 'end');
			
			
			$svg->appendChild($text);
			$text = $dom->createElement( 'text', number_format($rows[$numRows - $i - 1]["Coords"]) );
			$text->setAttribute("x", $leftOfBlocks - 20);
			$text->setAttribute("y", $topOfBlocks + 8 + $i * 10);
			$text->setAttribute('text-anchor', 'end');

			$svg->appendChild($text);
		}

		$title = $dom->createElement( 'title', "Report for Module $fileName - SNP $reportRowName");
			$title->setAttribute('x', 10);
			$title->setAttribute('y', 18);
			$title->setAttribute('style', 'font-size:30pt');
		
		$svg->appendChild($title);
		
		$text = $dom->createElement( "text", "2D Snapshot for SNP $reportRowName");
			$text->setAttribute('x', 10);
			$text->setAttribute('y', 18);
			$text->setAttribute('style', 'font-size:11pt');
		$svg->appendChild($text);



		$cornerBox = $dom->createElement( "text", null );
		$cornerBox->setAttribute('x', 10);
		$cornerBox->setAttribute('y', 25);
		$cornerBox->setAttribute('style', 'font-size:10pt');
		$svg->appendChild($text);		
		
		
		
		$text = $dom->createElement( "tspan", "Date:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		if($BuildAndPlatform['build'] !=''){
			$text = $dom->createElement("tspan", "Build:");
			$text->setAttribute('x', 10);
			$text->setAttribute('dy', '1.2em');
			$cornerBox->appendChild($text);
		 
		}
		if(trim($BuildAndPlatform['platform']) !=''){
			$text = $dom->createElement("tspan", "Platform:");
			
			$cornerBox->appendChild($text);
			$text->setAttribute('x', 10);
			$text->setAttribute('dy', '1.2em');
		}
		$text = $dom->createElement("tspan", "Chr:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text );
		$text = $dom->createElement("tspan", "From:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		$dom->createElement("tspan", "To:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		$svg->appendChild($cornerBox);
		
		$cornerBox = $dom->createElement("text", null);
		$cornerBox->setAttribute('x', '85');
		$cornerBox->setAttribute('y', '25');
		$cornerBox->setAttribute('style', 'font-size:10pt');
		$text = $dom->createElement("tspan", date("Y-m-d"));
		$text->setAttribute('x', 85);
		$text->setAttribute('dy', '1.2em');
		
		$cornerBox->appendChild($text);
		
		$text = $dom->createElement("tspan", "$BuildAndPlatform[build]");
		$text->setAttribute('x', 85);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		$text = $dom->createElement("tspan", "$BuildAndPlatform[platform]");
		$text->setAttribute('x', 85);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		$text = $dom->createElement("tspan", "$chr");
		$text->setAttribute('x', 85);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		$text = $dom->createElement("tspan", number_format($rows[0]["Coords"]));
		$text->setAttribute('x', 85);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		$text = $dom->createElement("tspan");
		$text->setAttribute('x', 85);
		$text->setAttribute('dy', '1.2em');
		$cornerBox->appendChild($text);
		
		$svg->appendChild($cornerBox);
		$colIndex = 0;
		$gap = false;
		$colHeadingPos = $topOfBlocks - 10;
		

		for ($i = 0; $i < $numColumns; ++$i)
 
		{
			$dataColumn = $colInfo[$i]["DataColumn"];
			if ($dataColumn == - 1)
			{
				if ($gap == false)
				{
					$gap = true;
					$dataColumns[$colIndex] = $dataColumn;
					++$colIndex;
				}

				continue;
			}

			$dataColumns[$colIndex] = $dataColumn;
			$offset = $leftOfBlocks + $colIndex * 10 + 10;
			$gap = false;
 
			$text = $dom->createElement( 'text', $colInfo[$i]["Name"]);
			$text->setAttribute("transform", "translate( $offset, $colHeadingPos ) rotate( -80 ) ");
			$text->setAttribute('style', 'font-size:7pt');

			$svg->appendChild($text);
			++$colIndex;
		} 
		
		for ($row = 0; $row < $numRows; ++$row)
		{
			$top = $topOfBlocks + $row * 10;
			$group = $dom->createElement( 'g', null );
			$group->setAttribute('transform', "translate(0,$top)");
			// if($rowDataStart == 0) $ro = $row + 1;
			// else $ro = $row;

			$currentRowData = $dataBlock[$numRows - $row - 1];
		 
			for ($i = 0; $i < $colIndex; ++$i)
		 
			{
				$dc = $dataColumns[$i];
				if ($dc == - 1)
				{
					continue;
				}
 				if(isset($currentRowData[$dc][0]))
						 $logP = $currentRowData[$dc][0];
				$this->addRect($dom, $group, $leftOfBlocks + $i * 10, $this->getColorPValue(-$logP));
				
				//echo  $this->getColorPValue(-$logP) .'<br>';
		 
			}

			$svg->appendChild($group);
		}

	$maxX = $leftOfBlocks + $numColumns * 10 + 30;
	$maxY = $topOfBlocks + $numRows * 10 + 80;
	$top = $topOfBlocks + $numRows * 10 + 20;
	$group = $dom->createElement('g', null);
		$group->setAttribute('transform', "translate(0,$top)");
 
	$labels = array(
		'' => 'P-value',
		'g' => '>0.05',
		'y' => '0.01 - 0.05',
		'o' => '0.001 - 0.01',
		'r' => '0.0001 - 0.001',
		'b' => '0.00001 - 0.0001',
		'bl' => '<0.00001',
		'w' => 'Missing'
	);
	$labelIdx = 0;
	foreach($labels as $c => $d)
	{
		if ($c != '')
		{
			$this->addRect($dom, $group, $leftOfBlocks + $labelIdx * 180, $c);
		}

		$xPos = $leftOfBlocks + 15 + $labelIdx * 180;
		$text = $dom->createElement('text', $d); 
			$text->setAttribute('x', $xPos);
			$text->setAttribute('style', 'font-size:12pt');
			$text->setAttribute('y', 10);
		
		$maxX = max($maxX, $xPos + 180);
		$group->appendChild($text);
		++$labelIdx;
	}

	$svg->appendChild($group);
	$rect = $dom->createElement("rect", null);
		$rect->setAttribute('x', 0);
		$rect->setAttribute('y', 0);
		$rect->setAttribute('width', $leftOfBlocks);
		$rect->setAttribute('height', $topOfBlocks - 5);
		$rect->setAttribute('fill', 'none');
		$rect->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
	$svg->appendChild($rect);
	
	$rect = $dom->createElement("rect", null);
		$rect->setAttribute('x', $leftOfBlocks);
		$rect->setAttribute('y', 0);
		$rect->setAttribute('width', $maxX - $leftOfBlocks);
		$rect->setAttribute('height', $topOfBlocks - 5);
		$rect->setAttribute('fill', 'none');
		$rect->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
	$svg->appendChild($rect);

	$rect = $dom->createElement("rect", null);
		$rect->setAttribute('x', 0);
		$rect->setAttribute('y', $topOfBlocks - 5);
		$rect->setAttribute('width', $leftOfBlocks);
		$rect->setAttribute('height', $numRows * 10 + 10);
		$rect->setAttribute('fill', 'none');
		$rect->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
	$svg->appendChild($rect);

	$rect = $dom->createElement("rect", null);
		$rect->setAttribute('x', $leftOfBlocks);
		$rect->setAttribute('y', $topOfBlocks - 5);
		$rect->setAttribute('width', $maxX - $leftOfBlocks);
		$rect->setAttribute('height', $numRows * 10 + 10);
		$rect->setAttribute('fill', 'none');
		$rect->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
	$svg->appendChild($rect);

	$rect = $dom->createElement("rect", null);
		$rect->setAttribute('x', 0);
		$rect->setAttribute('y', $topOfBlocks + $numRows * 10 + 5);
		$rect->setAttribute('width', $maxX);
		$rect->setAttribute('height', 35);
		$rect->setAttribute('fill', 'none');
		$rect->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
	$svg->appendChild($rect);
	 
	$svg->setAttribute('viewBox', "0 0 $maxX $maxY");
	$dom->appendChild($svg);
	//fclose($file);
	return $dom->saveXML();
	
	}

private function getColorOddsRatio($oddsRatio)
{
	if ($oddsRatio == - 1.0)
	{
		return "rgb(0,0,255)";;
	}

	if ($oddsRatio < 0.5)
	{
		return "rgb(0,255,0)";
	}
	else
	if ($oddsRatio < 0.9)
	{
		return "rgb(127,255,127)";
	}
	else
	if ($oddsRatio < 1.1)
	{
		return "rgb(255,255,255)";
	}
	else
	if ($oddsRatio < 2.0)
	{
		return "rgb(255, 127, 127)";
	}
	else

	// > 2.0

	{
		return "rgb(255,0,0)";
	}
}

private function getColorOddsRatioLighter($oddsRatio)
{
	if ($oddsRatio == - 1.0)
	{
		return "rgb(50,50,255)";;
	}

	if ($oddsRatio < 0.5)
	{
		return "rgb(50,255,50)";
	}
	else
	if ($oddsRatio < 0.9)
	{
		return "rgb(177,255,177)";
	}
	else
	if ($oddsRatio < 1.1)
	{
		return "rgb(255,255,255)";
	}
	else
	if ($oddsRatio < 2.0)
	{
		return "rgb(255, 177, 177)";
	}
	else
	if ($oddsRatio == 2.0)
	{
		return "rgb(0,0,0)";
	}
	else

	// > 2.0

	{
		return "rgb(255,50,50)";
	}
}

private function getColorOddsRatioDarker($oddsRatio)
{
	
	if ($oddsRatio == - 1.0)
	{
		return "rgb(0,0,205)";;
	}

	if ($oddsRatio < 0.5)
	{
		return "rgb(0,205,0)";
	}
	else
	if ($oddsRatio < 0.9)
	{
		return "rgb(77,205,77)";
	}
	else
	if ($oddsRatio < 1.1)
	{
		return "rgb(205,205,205)";
	}
	else
	if ($oddsRatio < 2.0)
	{
		return "rgb(205, 77, 77)";
	}
	else

	// > 2.0

	{
		return "rgb(205,0,0)";
	}
}


//

function ReadGenomeData($module, $chr, $row, $number){

			$sql = "SELECT ind FROM 
				".$this->module( $module ).".ind WHERE chr = ".$chr." AND nrow = ".$row."";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$m = $stmt->fetch(\PDO::FETCH_ASSOC);
		
			$ind = $m['ind'];
			
			$end = $ind + $number;
			$start = $ind - $number;
			$sql = "SELECT g.ind, g.gen, a.alias, p.pos FROM 
			  ".$this->module( $module ).".gen g 
			  JOIN ".$this->module( $module ).".alias a USING(ind) 
			  JOIN ".$this->module( $module ).".pos p USING(ind) WHERE ind <= ". $end ." AND  ind >= ". $start ."";
		 
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			while ($md = $stmt->fetch(\PDO::FETCH_ASSOC)){
				$pval = false;
			$q = "SELECT min(pval) as pval FROM 
			  	".$this->module( $module ).".pval p 
			  	JOIN ".$this->module( $module ).".v_ind a USING(v_ind) 
			  	WHERE ind = ". $md['ind'] ."";
			  	
			  	
			  	$res = $this->db->prepare($q);
				$res->execute();
			  	$pval = $res->fetch(\PDO::FETCH_ASSOC);
				 
				$md['pval'] = $pval['pval'];
				
				$data[] = $md;
			
			}
			
			return $data;

}



function lde_hwe($genpos, $genneg, $n, $d_prime, $r, $maxit)
{

$t = array(0,0,0,0,0,0,0,0,0);
for ($i = 0; $i < $n; $i++) {
    $pos = $genpos[$i]; 
    $neg = $genneg[$i];
    if ( ($pos <3) && ($neg < 3) ) {
	$t[$pos + 3 * $neg]++;
    }
}

$par = array(2 * $t[0] + $t[1] + $t[3], 
	     2 * $t[2] + $t[1] + $t[5],
	     2 * $t[6] + $t[3] + $t[7],
	     2 * $t[8] + $t[5] + $t[7]);
$fr = array (0,1,2,3);

$it = $this->frequency_estimate_impl($par, $t[4], $fr, $maxit);
 
$mar_pos = array($fr[0] + $fr[1], $fr[2] + $fr[3]);
$mar_neg = array($fr[0] + $fr[2], $fr[1] + $fr[3]);
$cov = $fr[0] - $mar_pos[0] * $mar_neg[0];

if ($cov >= 0.0) {
    $pr0 = $mar_pos[0] * $mar_neg[1];
    $pr1 = $mar_neg[0] * $mar_pos[1];
    $div = min ($pr0, $pr1);
    }
else {
    $pr0 = $mar_pos[0] * $mar_neg[0];
    $pr1 = $mar_pos[1] * $mar_neg[1];
    $div = min($pr0, $pr1);
    }
    
//check it
    if($div != 0)
	    $d_prime = $cov / $div;
    else
    	$d_prime = 0;
    
    if($pr0 * $pr1 != 0)	
    	$r = $cov / sqrt($pr0 * $pr1);
    else
    	$r = 0;

return $d_prime;

}

function  frequency_estimate_impl ( $in, $dh, &$out, $maxit )

{

$epsilon = 0.00000000000001;
$em_init= 0.01;

    $totp = $in[0] + $in[1] + $in[2] + $in[3];
    $tot = $totp  + 2 * $dh;
    $rtot = 1.0 / $tot;

if (!$dh) {
    $out[0] = $rtot * $in[0];
    $out[1] = $rtot * $in[1];
    $out[2] = $rtot * $in[2];
    $out[3] = $rtot * $in[3];
    return 0;
    }
$lh = 0.0;

    $rtot_init = 1.0 / ($totp + 4.0 * $em_init);

for ($i = 0; $i < 4; $i++) {
    $out[$i] = $rtot_init * ($in[$i] + $em_init); 
    $lh = $lh + ($in[$i] * log($out[$i]));
}
    $pr03 = $out[0] * $out[3];
    $pr12 = $out[1] * $out[2];
    $lh = $lh + $dh * log($pr03 + $pr12);
    $tol = abs ($lh * sqrt ($epsilon));
    if ($tol < $epsilon ) { $tol = $epsilon; }
    
    $it = 0;
    while ($it < $maxit) {
    $it++;
	$spr = 1.0 / ($pr03 + $pr12);
	$dh0 = $dh * $pr03 * $spr;
	$dh1 = $dh * $pr12 * $spr;
	$diff = array ($dh0, $dh1, $dh1, $dh0);
	$lht = 0.0;
	
    for ($i = 0; $i < 4; $i++) {
	$out[$i] = $rtot * ($in[$i] + $diff[$i]); 
	$lht = $lht + $in[$i] * log($out[$i]);
    }
	
	$pr03 = $out[0] * $out[3];
	$pr12 = $out[1] * $out[2];
	$lht = $lht + ($dh * log ($pr03 + $pr12));
	
	if (abs($lh - $lht) < $tol ) {
	    break;
	}
	$lh = $lht;
    }
    return $it;	
}

private function getLDColor($ld){ 
 
 //echo $ld;
	$c = '';
	if( $ld >= 0.8)
		  $c = '#fa0707';
		  
	if($ld < 0.8 && $ld >= 0.6) 
		 $c = '#f74848';
	
	if($ld < 0.6 && $ld >= 0.4) 
		  $c = '#f08484';
		  
	if($ld < 0.4  && $ld >= 0.2) 
		  $c = '#f2aaaa';
		  
	if($ld < 0.2  && $ld >= 0) 
		  $c = '#fcfafa';
		  
	if($ld < 0  && $ld >= -0.2) 
		  $c = '#f0f0fa';
		  
	if($ld < -0.2  && $ld >= -0.4) 
		  $c = '#aeb1f5';
		  
	if($ld < -0.4  && $ld >= -0.6) 
		  $c = '#5b63f0';
		  
	if($ld < -0.6  && $ld >= -0.8) 
		  $c = '#3841f2';
		  
	if($ld < -0.8 ) 
		  $c = '#2024fb';
//echo ' '. $c . '<br><br>';
 
	return $c;
}



// LDE snapshot
public function generateLDSnapshot($request)
{


		$row = $request->query->get('row');
		if (!isset($row))
		{
			$this->ReturnError("No row specified");
		}
		 
		$chr = $request->query->get('chr');
		if (!isset($chr))
		{
			$this->ReturnError("No chromosome specified");
		}

		$module = trim($request->query->get('module'));
		if (!$module)
		{
			$this->ReturnError("No module specified");
		}

		$quantity = 30; //number of snps

		$dataBlock = $this->ReadGenomeData($module, $chr, $row, $quantity);
	 
		$repTitle = "LD Snapshot - ".$dataBlock[$quantity]['alias'];

	$dom = new \DOMDocument('1.0', 'utf-8');

//	$dom->formatOutput = true; // TODO, remove this to save bandwidth later
	$svg = $dom->createElement( 'svg',  null);
		
	$svg->setAttribute('width', '29.7cm');
	$svg->setAttribute('height', '42.0cm');
	$svg->setAttribute('preserveAspectRatio', 'xMinYMin meet');
	$svg->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
	$svg->setAttribute('xmlns:xlink', "http://www.w3.org/1999/xlink");
	$svg->setAttribute('version', '1.1');
	$title = $dom->createElement( 'title',  $repTitle );
	$svg->appendChild($title);
 
//	$defs = $dom->createElement( 'defs');
//	$this->addDefBox($dom, $defs, 'w', 'white');
//	$this->addDefBox($dom, $defs, 'g', 'lightgrey');
//	$svg->appendChild($defs);
//	$style = $dom->createElement( 'style', 'text { font-size: 8pt; font-family: Arial, sans-serif }; ');
//	$svg->appendChild($style);
	 
  
	

	
	
	 
	
	$text = $dom->createElement("text", $repTitle);
	  
	$text->setAttribute('x', 10);
	$text->setAttribute('y', 25);
	$text->setAttribute('style', 'font-size:16pt');
	$svg->appendChild($text);
 		
	$cornerBox = $dom->createElement( "text", null);
	$cornerBox->setAttribute('x', 10);
	$cornerBox->setAttribute('y', 45);
	$cornerBox->setAttribute('style', 'font-size:14pt');
	 
	$text = $dom->createElement( "tspan", "Date:");
	$text->setAttribute('x', 10);
	$text->setAttribute('dy', '1.1em');
	$cornerBox->appendChild($text);
 
	 
 
 		$text = $dom->createElement(  "tspan", "Module: ".$module );
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	

 		$text = $dom->createElement(  "tspan", "Chr: $chr");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	

 		$text = $dom->createElement(  "tspan", "SNP: " . $dataBlock[sizeof($dataBlock) - 30]['alias']);
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	

 
 		$text = $dom->createElement(  "tspan", "From: " . $dataBlock[0]['alias']);
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	


 		$text = $dom->createElement(  "tspan", "To: " . $dataBlock[60]['alias']);
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	


 		$text = $dom->createElement(  "tspan", "Quantity: +/- " . $quantity . "SNP");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	

 

	$svg->appendChild($cornerBox);
 

	$cornerBox = $dom->createElement("text", null);
	$cornerBox->setAttribute('x', 110);
	$cornerBox->setAttribute('y', 45);
	$cornerBox->setAttribute('style', 'font-size:14px');
 
 	$text = $dom->createElement("tspan", '');
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	
  
	
 	$text = $dom->createElement("tspan", "");
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
 
 	$text = $dom->createElement("tspan", "");
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	$cornerBox->setAttribute('style', 'font-size:6pt');
	
	$color = '';
	$size = 10;
	$em_init = 0.01;
	$n = sizeof($dataBlock);
	$x0 = 800;
	
	// шкала позиций на хромосоме
	$y0 = 50;
	$yd = $n*2*$size;
	$ydd = $yd;
	$yn = $y0 + 9;
	
	
	
	$coords = array(
		array($x0+200, 50), array($x0+220, 45), array($x0+200, 50 + $yd),array($x0+220, 50  + $yd)
	);

	$positions = $dom->createElement( "polygon", null );
	$positions->setAttribute('points',  $coords[0][0] .','. $coords[0][1] .' '. $coords[1][0] .','. $coords[1][1] .' '. $coords[3][0] .','. $coords[3][1] .' '. $coords[2][0] .','. $coords[2][1] ); 
	$positions->setAttribute('fill',  'rgb(100,100,100)');
	$positions->setAttribute('style', 'stroke-width:1;rgb(100,100,100)');
	
	$svg->appendChild($positions);
	
	$coords = array(
		array($x0+300, 45), array($x0+302, 45), array($x0+300, 50 + $yd),array($x0+302, 50  + $yd)
	);
	
	
	$positions = $dom->createElement( "polygon", null );
	$positions->setAttribute('points',  $coords[0][0] .','. $coords[0][1] .' '. $coords[1][0] .','. $coords[1][1] .' '. $coords[3][0] .','. $coords[3][1] .' '. $coords[2][0] .','. $coords[2][1] ); 
	$positions->setAttribute('fill',  'rgb(100,100,100)');
	$positions->setAttribute('style', 'stroke-width:1;rgb(100,100,100)');
	
	$svg->appendChild($positions);

		
	$dpos = $dataBlock[sizeof($dataBlock)-1]['pos']- $dataBlock[0]['pos'];
	
	
	foreach($dataBlock as $key=>$value){
	
		$yy = 50 + round( ($ydd/$dpos )   *  ($value['pos']-$dataBlock[0]['pos']));
	//echo  '  '. $ydd. '  '. $dpos ."\n"; 
		$line = $dom->createElement('line', null);
		$line->setAttribute('x1', 1000);
		$line->setAttribute('y1', $yy);
		$line->setAttribute('x2', 1020);
		$line->setAttribute('y2', $yy);
		$line->setAttribute('style', 'stroke-width:1;stroke:rgb(200,200,200);');
		
		$svg->appendChild($line);
		
		$line = $dom->createElement('line', null);
		$line->setAttribute('x1', 930);
		$line->setAttribute('y1', $yn);
		$line->setAttribute('x2', 1000);
		$line->setAttribute('style', 'stroke-width:1;stroke:rgb(200,200,200);');
		$line->setAttribute('y2', $yy);

		$svg->appendChild($line);
		if($dataBlock[$key]['pval'] > 0){
			$line = $dom->createElement('line', null);
			$line->setAttribute('x1', 1000);
			$line->setAttribute('y1', $yy);
			$line->setAttribute('x2', round(1020 -log($dataBlock[$key]['pval']) * 100));
			$line->setAttribute('style', 'stroke-width:1;stroke:rgb(200,200,200);');
			$line->setAttribute('y2', $yy);

			$svg->appendChild($line);
		}
	 
 
		
		$yn+=(2*$size);
		
		$y0 = 58 + $size  * $key;
		$c = 0;
			
 		$cornerBox->setAttribute('style', 'font-size:'. $size .'px');
		$text = $dom->createElement("tspan", $value['alias'] . ', -log(p) - ' . -  log10($value['pval']));
		$text->setAttribute('x', $x0 + 10);
		$text->setAttribute('dy', 2*$size);	
		$text->setAttribute('onmouseover', "hlight('sss" . $dataBlock[$key]['pos'] ."')");	
		

		$cornerBox->appendChild($text);
		

	 	foreach($dataBlock as $key1=>$value1){
	 	
	 		if($key1 <= $key)continue;
	 		
	 		  $ld = $this->lde_hwe($dataBlock[$key]['gen'], $dataBlock[$key1]['gen'], $n, '', '',10000);
	   
	  		 
	  
	 		$xd = $x0 - $size  * $c ;
			$yd = $y0 + $size  * $key1 ;
	 	
	 		$x1 = $xd ;
			$x2 = $xd - $size;
			$x3 = $xd ;
			$x4 = $xd + $size;
	
			$y1 = $yd + $size;
			$y2 = $yd ;
			$y3 = $yd - $size;
			$y4 = $yd ;

			$c++; 
			
			$rect = $dom->createElement( "polygon", null );
			$rect->setAttribute('points', "$x1,$y1 $x2,$y2 $x3,$y3 $x4,$y4"); 
	 
			$rect->setAttribute('fill', $this->getLDColor($ld));
			$rect->setAttribute('style', 'stroke-width:1;rgb(100,100,100)');
	 		$rect->setAttribute('class',  "sss" . $dataBlock[$key1]['pos'] );
			$svg->appendChild($rect);

		}
		
		//break;
	}
	$js = $dom->createElement('script', null);
	$js->setAttribute('src', '/js/jquery-1.6.min.js');
	$js->setAttribute('type', 'text/javascript');
	$svg->appendChild($js);

	$js = $dom->createElement('script', null);
	$js->setAttribute('src', '/js/jquery-ui-1.8.12.custom.min.js');
	$js->setAttribute('type', 'text/javascript');
	$svg->appendChild($js);
	
	
	$js = $dom->createElement('script', "
	
	function cha(item, index){
		//alert(item);
		$('.sss3813568').css('border','10px solid red');
			
	}
	
	function hlight(element){
	
			var el = document.getElementsByClassName(element);
			//alert (el);
			cha(1,1);
			//el.forEach(cha);
		}	
	 ");
	 
	$js->setAttribute('type', 'text/javascript');
	$svg->appendChild($js);
	
	
 	$svg->appendChild($cornerBox);


 	
	

	$dom->appendChild($svg);

	return $dom->saveXML();


}




















// 3D snapshot

public function generateGWASSnapshot($request, $polarized)
{


		$reportRow = $request->query->get('row');
		if (!isset($reportRow))
		{
			$this->ReturnError("No row specified");
		}
	
	
	 
		$chr = $request->query->get('chr');
		if (!isset($chr))
		{
			$this->ReturnError("No chromosome specified");
		}

		$fileName = trim($request->query->get('module'));
		if (!$fileName)
		{
			$this->ReturnError("No module specified");
		}

		$file = $fileName;

		
		$stageMask = $request->query->get('stageMask');
	
		if (!isset($stageMask))
		{
			$stageMask = 0xffffffff;
		}

 	if ($polarized)
	{
	 
		//$meta = $this->parseMeta();
		$polFile = $this->getPolarization($file);
		$polData = $this->getPolarizationData($file, $chr, $reportRow);
		//ReturnError($polData);
		//print_r($polData);
	}
 
	// ReadVersionAndMagic( $file );
	$BuildAndPlatform = $this->GetBuildAndPlatform($fileName);
	$colInfo = $this->ReadColumnInfo($file);
	$numColumns = $colInfo["NumColumns"];
	$numDataColumns = $colInfo["NumDataColumns"];
	$rowCount = $this->ReadRowCount($file, $chr);
	$rowDataStart = $reportRow - 40;
	if ($rowDataStart < 0) $rowDataStart = 0;
	$rowDataCount = 80;
	$heightScale = 20;

	// false means return the data as height

	$dataBlock = $this->ReadRowData($file, $chr, $numDataColumns, $rowCount, $rowDataStart, $rowDataCount, 1, $heightScale * 1.5);
	$topOfBlocks = 40;
	$leftOfBlocks = 400;
	$dom = new \DOMDocument('1.0', 'utf-8');

//	$dom->formatOutput = true; // TODO, remove this to save bandwidth later
	$svg = $dom->createElement( 'svg',  null);
		
	$svg->setAttribute('width', '29.7cm');
	$svg->setAttribute('height', '21.0cm');
	$svg->setAttribute('preserveAspectRatio', 'xMinYMin meet');
	$svg->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
	$svg->setAttribute('xmlns:xlink', "http://www.w3.org/1999/xlink");
	$svg->setAttribute('version', '1.1');

	$defs = $dom->createElement( 'defs');
	$this->addDefBox($dom, $defs, 'w', 'white');
	$this->addDefBox($dom, $defs, 'g', 'lightgrey');
	$svg->appendChild($defs);
	$style = $dom->createElement( 'style', 'text { font-size: 8pt; font-family: Arial, sans-serif }');
	$svg->appendChild($style);
	$rows = $dataBlock["Rows"];
	$numRows = count($rows);
	$relativeRow = $reportRow - $rowDataStart;
	$repTitle = "3D Snapshot ";
	if ($polarized)
	{
		$repTitle.= " - Polarized";
	}
	
	$text = $dom->createElement("text", $repTitle);
	  
	$text->setAttribute('x', 10);
	$text->setAttribute('y', 25);
	$text->setAttribute('style', 'font-size:16pt');
	$svg->appendChild($text);
	
	$cornerBox = $dom->createElement( "text", null);
	$cornerBox->setAttribute('x', 10);
	$cornerBox->setAttribute('y', 45);
	$cornerBox->setAttribute('style', 'font-size:14pt');
	 
	$text = $dom->createElement( "tspan", "Date:");
	$text->setAttribute('x', 10);
	$text->setAttribute('dy', '1.1em');
	$cornerBox->appendChild($text);
 
	if($BuildAndPlatform['build'] !=''){
		$text = $dom->createElement(  "tspan", "Build:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);
 	}
	if($BuildAndPlatform['platform'] !=''){
 		$text = $dom->createElement(  "tspan", "Platform:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);
	}
 
 		$text = $dom->createElement(  "tspan", "Module:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	

 		$text = $dom->createElement(  "tspan", "Chr:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	

 		$text = $dom->createElement(  "tspan", "Rs:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	

 
 		$text = $dom->createElement(  "tspan", "From:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	


 		$text = $dom->createElement(  "tspan", "To:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	


 		$text = $dom->createElement(  "tspan", "Gene:");
		$text->setAttribute('x', 10);
		$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);	


	$svg->appendChild($cornerBox);



	$cornerBox = $dom->createElement("text", null);
	$cornerBox->setAttribute('x', 110);
	$cornerBox->setAttribute('y', 45);
	$cornerBox->setAttribute('style', 'font-size:14pt');
 
 	$text = $dom->createElement("tspan", date("Y-m-d"));
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	
 	$text = $dom->createElement("tspan", "$BuildAndPlatform[build]");
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	
 	$text = $dom->createElement("tspan",  "$BuildAndPlatform[platform]");
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	
 	$text = $dom->createElement("tspan", "$fileName");
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
 
 	$text = $dom->createElement("tspan", "$chr");
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	

 
//	if ($polarized)
//	{
		$text = $dom->createElement( "tspan", $rows[$reportRow - $rowDataStart]["Name"]);
			$text->setAttribute('x', 110);
			$text->setAttribute('dy', '1.1em');
		$cornerBox->appendChild($text);
//	}


 	$text = $dom->createElement("tspan", number_format($rows[0]["Coords"]));
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	
 	$text = $dom->createElement("tspan",  number_format($rows[$numRows - 1]["Coords"]));
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	
 	$text = $dom->createElement("tspan", $rows[$reportRow - $rowDataStart]["Gene"]);
	$text->setAttribute('x', 110);
	$text->setAttribute('dy', '1.1em');	
	$cornerBox->appendChild($text);
	



 
	$svg->appendChild($cornerBox);
	$maxX = $leftOfBlocks + $numColumns * 10 + 200;
	$maxY = $topOfBlocks + $numRows * 10 + 180;
	
	
	$rect = $dom->createElement( "rect", null );
	$rect->setAttribute('x', 2);
	$rect->setAttribute('y', 2);
	$rect->setAttribute('width', $maxX + 10);
	$rect->setAttribute('height', $maxY - 10);
	$rect->setAttribute('fill', 'none');
	$rect->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
	$svg->appendChild($rect);

	$colIndex = 0;
	$gap = false;
	$colHeadingPos = $topOfBlocks - 10 + ($numRows * 10) + 20;
	$skewValue = 20;
	$tanOfSkew = tan(deg2rad($skewValue));
	$rowSkewOffset = 10 * $tanOfSkew;
	for ($i = 0; $i < $numColumns; ++$i)
	{
		$dataColumn = $colInfo[$i]["DataColumn"];
		if ($dataColumn == - 1)
		{
			if ($gap == false)
			{
				$gap = true;
				$dataColumns[$colIndex] = $dataColumn;
				++$colIndex;
			}

			continue;
		}

		$dataColumns[$colIndex] = $dataColumn;
		$offset = $leftOfBlocks + $colIndex * 10 - ($rowSkewOffset * $numRows) - $tanOfSkew * $topOfBlocks + 8;
		$gap = false;
		$rotate = 90 - $skewValue;
		$text = $dom->createElement( 'text', $colInfo[$i]["Name"]);
		$text->setAttribute("transform", "translate( $offset, $colHeadingPos ) rotate( -$rotate ) ");
		$text->setAttribute('text-anchor', 'end');
		$text->setAttribute('style', 'font-size:7pt');
		 
		$svg->appendChild($text);
		++$colIndex;
	}

	// add row things on the end

	$startRowName = $leftOfBlocks + ($colIndex * 10) - 10;
	$rowIndex = 0;
	for ($i = $numRows - 1; $i >= 0; --$i)
	{
		$rowName = $rows[$i]["Name"];
		$text = $dom->createElement( 'text', $rowName);
			$text->setAttribute("x", $startRowName);
			$text->setAttribute("y", $topOfBlocks + 8 + $rowIndex * 10);
	 
		if ($i == $relativeRow)
		{
			$reportRowName = $rowName;
		}

		$svg->appendChild($text);
		$rowName = $rows[$i]["Name"];
		$text = $dom->createElement( 'text', number_format($rows[$i]["Coords"]));
			$text->setAttribute("x", $startRowName + 90);
			$text->setAttribute("y", $topOfBlocks + 8 + $rowIndex * 10);
	 
		if ($i == $relativeRow)
		{
			$reportRowName = $rowName;
		}

		$svg->appendChild($text);
		$rowName = $rows[$i]["Name"];
		if ($rows[$i]["MAF"] == 0 || $rows[$i]["MAF"] == - 1) $rows[$i]["MAF"] = '';
		$text = $dom->createElement( 'text', $rows[$i]["MAF"]);
			$text->setAttribute("x", $startRowName + 170);
			$text->setAttribute("y", $topOfBlocks + 8 + $rowIndex++ * 10);
	 
		if ($i == $relativeRow)
		{
			$reportRowName = $rowName;
		}

		$svg->appendChild($text);
		$startRowName-= $rowSkewOffset;
	}

	$titleString = "Report for Module $fileName - SNP $reportRowName";
	if ($polarized)
	{
		$titleString.= " - Polarized";
	}

	$title = $dom->createElement( 'title', $titleString);
	$svg->appendChild($title);
	$outerGroup = $dom->createElement( 'g', null);
	$outerGroup->setAttribute('transform', "skewX(-$skewValue)");
	 
	$rowIndex = 0;

	for ($row = $numRows - 1; $row >= 0; --$row)
	{
		$top = $topOfBlocks + $rowIndex++ * 10;
		$group = $dom->createElement( 'g', null);
			$group->setAttribute('transform', "translate(0,$top)");
	 
		$rowMod = ($row % 5) == 0;
		if(!isset($dataBlock[$row]))
			$dataBlock[$row] = true;
		$currentRowData = $dataBlock[$row];
		for ($i = 0; $i < $colIndex; ++$i)
		{
			$dc = $dataColumns[$i];
			if ($dc == - 1)
			{
				continue;
			}

			//$this->addRect($dom, $group, $leftOfBlocks + $i * 10, (($i % 5) == 0 || $rowMod) ? "w" : "g");
			$this->addRect($dom, $group, $leftOfBlocks + $i * 10, (($i % 5) == 0 || $rowMod) ? "w" : "g");
		}

		$outerGroup->appendChild($group);
	}

	$svg->appendChild($outerGroup);
	$rowIndex = 0;
	for ($row = $numRows - 1; $row >= 0; --$row)
	{
		$top = $topOfBlocks + $rowIndex++ * 10;
		$rowMod = ($row % 5) == 0;
		

		if(!isset($dataBlock[$row]))
			continue;

//			$dataBlock[$row] =  Array(  Array ( -1 , -1 ),  Array ( -1 , -1 ),  Array ( -1 , -1 ), Array ( -1 , -1 ));
//print_r( $dataBlock[$row] );
		$currentRowData = $dataBlock[$row];
		$fipOddsRatio = false;
		$grey = false;
		$white = false;
		if ($polarized && isset($polData))
		{
			$numPolSamples = $polData["NumPolSamples"];
			$rowRelativeToPolData = ($rowDataStart + $row) - $polData["Row"] + (($numPolSamples - 1) / 2);
			if (($rowRelativeToPolData < 0) || ($rowRelativeToPolData >= $numPolSamples))
			{

				// hide row, outside polarisation region

				continue;
			}

			$polDataForRow = $polData[$rowRelativeToPolData];
			if ($polDataForRow == - 3) // not in LD with anything
			{
				$grey = true;
			}

			if ($polDataForRow == - 2) // not in LD with anything
			{
				$white = true;
			}

			$fipOddsRatio = ($polDataForRow == - 1);
		}

		for ($i = 0; $i < $colIndex; ++$i)
		{
			$dc = $dataColumns[$i];
			if ($dc == - 1)
			{
				continue;
			}

			
			if (isset($currentRowData[$dc]))
			{
				$dataPoint = $currentRowData[$dc];
				$height = $dataPoint[0];
				$odds = $dataPoint[1];
				if ($fipOddsRatio)
				{
					if( $odds != 0)
						$odds = 1.0 / $odds;
				}

				$leftBar = $leftOfBlocks + $i * 10 - ($rowSkewOffset * $rowIndex) - $topOfBlocks * $tanOfSkew;
				$topBar = $top - $height + 10;
				$group = $dom->createElement( "g", null);
					$group->setAttribute("transform", "translate( $leftBar, $topBar )");
			 
				if (!$grey)
				{
					$color = $this->getColorOddsRatio($odds);
				}
				else
				{
					$color = "rgb(178,178,178)";
				}

				if ($white)
				{
					$color = "rgb(0,0,0)";
				}

				$bar = $dom->createElement("rect", null);
					$bar->setAttribute("width", 10);
					$bar->setAttribute("height", $height);
					$bar->setAttribute('fill', $color);
					$bar->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
			 
				$group->appendChild($bar);
				if (!$grey)
				{
					$color = $this->getColorOddsRatioLighter($odds);
				}
				else
				{
					$color = "rgb(228,228,228)";
				}

				if ($white)
				{
					$color = "rgb(0,0,0)";
				}

				$barTop = $dom->createElement( "rect", null);
					$barTop->setAttribute("width", 10);
					$barTop->setAttribute("height", 10);
					$barTop->setAttribute("y", - 10);
					$barTop->setAttribute("x", 0);
					$barTop->setAttribute('fill', $color);
					$barTop->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
					$barTop->setAttribute('transform', "skewX(-$skewValue)");
			 
				$group->appendChild($barTop);
				if (!$grey)
				{
					$color = $this->getColorOddsRatioDarker($odds);
				}
				else
				{
					$color = "rgb(128,128,128)";
				}

				if ($white)
				{
					$color = "rgb(0,0,0)";
				}

				$rs = $rows[$row]["Name"];
				if (preg_match('/-1/', $rs))
				{
					$color = 'rgb(1,1,1)';
				}

				$tanOfSkewPlus10 = 10 + $tanOfSkew * 10;
				$heightMinusTen = $height - 10;
				$barSide = $dom->createElement( "polygon", null);
					$barSide->setAttribute("points", "10,0  $tanOfSkewPlus10,-10  $tanOfSkewPlus10, $heightMinusTen  10, $height");
					$barSide->setAttribute('fill', $color);
					$barSide->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
				 
				$group->appendChild($barSide);
				$svg->appendChild($group);
			}
		}
	}

	$text = $dom->createElement( 'text', 'QAS');
		$text->setAttribute("x", 10);
		$text->setAttribute("y", 240);
		$text->setAttribute('style', 'font-size:15pt');
	$svg->appendChild($text);
	$labels = array(
		"0.1" => '<0.5',
		"0.7" => '0.5 - 0.9',
		"1.0" => '0.9 - 1.1',
		"1.5" => '1.1 - 2.0',
		"3.0" => '>2.0',
		"-1.0" => 'Undefined',
	);
	$labelIdx = 0;
	foreach($labels as $cm => $d)
	{
		 
		$top = $labelIdx * 30 + 250;
		$group = $dom->createElement( 'g', null);
			$group->setAttribute('transform', "translate(20,$top), scale(1.1)");
	 
		$box = $dom->createElement( "rect", null);
			$box->setAttribute("width", 30);
			$box->setAttribute("height", 10);
			$box->setAttribute("fill", $this->getColorOddsRatio($cm) );
			$box->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
		 
		$group->appendChild($box);
		$text = $dom->createElement('text', $d);
			$text->setAttribute('x', 45);
			$text->setAttribute('style', 'font-size:12pt');
			$text->setAttribute('y', 10);
		
		$group->appendChild($text);
		++$labelIdx;
		$svg->appendChild($group);
	}

	$text = $dom->createElement('text', "P-value");
		$text->setAttribute("x", 10);
		$text->setAttribute("y", 450);
		$text->setAttribute('style', 'font-size:15pt');
	$svg->appendChild($text);
	$labels = array(
		"0.00001" => 3,
		"0.0001" => 3,
		"0.001" => 3,
		"0.01" => 1,
		"" => 4,
		">0.01" => 1,
	);
	$labelIdx = 0;
	foreach($labels as $c => $d)
	{
		$top = $labelIdx * $heightScale + 470;
		$group = $dom->createElement( 'g', null);
			$group->setAttribute('transform', "translate(20,$top)");
		 
		$text = $dom->createElement('text', $c);
			$text->setAttribute('x', 65);
			$text->setAttribute('style', 'font-size:12pt');
			$text->setAttribute('text-anchor', 'end');
			$text->setAttribute('y', 10);
		 
		$group->appendChild($text);
		if ($d & 1)
		{
			$text = $dom->createElement('line', null);
				$text->setAttribute('x1', 70);
				$text->setAttribute('y1', 5);
				$text->setAttribute('x2', 80);
				$text->setAttribute('y2', 5);
				$text->setAttribute('style', 'stroke:rgb(0,0,0);stroke-width:2');
			 $group->appendChild($text);
		}

		if ($d & 2)
		{
			$text = $dom->createElement('line', null);
				$text->setAttribute('x1', 80);
				$text->setAttribute('y1', 5);
				$text->setAttribute('x2', 80);
				$text->setAttribute('y2', 5 + $heightScale);
				$text->setAttribute('style', 'stroke:rgb(0,0,0);stroke-width:2');
			 $group->appendChild($text);
		}

		if ($d & 4)
		{
			$text = $dom->createElement('line', null);
				$text->setAttribute('x1', 80);
				$text->setAttribute('y1', 5 - $heightScale);
				$text->setAttribute('x2', 80);
				$text->setAttribute('y2', 5 + $heightScale);
				$text->setAttribute('style', 'stroke:rgb(0,0,0);stroke-width:2;stroke-dasharray: 5, 5');
			 $group->appendChild($text);
		}

		++$labelIdx;
		$svg->appendChild($group);
	}

 


	$svg->setAttribute('viewBox', "0 0 $maxX $maxY");
	$dom->appendChild($svg);
	//fclose($file);
	return $dom->saveXML();
}

private function getColor($value, $type){



	switch ($type) {
	
	
	
		case 'pval':                               //bottom
			if (log10( $value ) > -2 ) return 'rgb(255, 255, 255)'; 
			
			
			elseif( log10($value) > -4 && log10( $value ) < -2) return 'rgb(255,0,0)';
			
				
			else return 'rgb(0,0,0)';

		break;
		
		
		
		case 'qas': //top
			if( $value > 2 ){
			
				 return 'rgb(0,0,0))';       
			}
	
			elseif($value > 1  && $value <= 2){
				 
				 return 'rgb(255, 255, 255)';
			}
			elseif($value < 1 && $value > 0 )
			{

				return 'rgb(150, 150, 150)';      
		
			}
	
		break;
  
	}

}
 	
private function generateNewTmpTable($module, $ind ){



	$table = '';
    $validCharacters = "abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ1234567890";
    $validCharNumber = strlen($validCharacters);
  	for ($i = 0; $i < 10; $i++) {

   	 	$index = mt_rand(0, $validCharNumber-1);

   	 $table .= $validCharacters[$index];

	}
	
/*	$sql = "SELECT distinct path  FROM `". $this->dbmanagemant ."`.`path`;";
		$pt = $this->db->prepare($sql);
		$pt->execute();
		while($phs = $pt->fetch(\PDO::FETCH_ASSOC)){
			$paths[] = 'path_'.$phs['path'];
			}
*/

	

    
    $sql = "SHOW TABLES FROM `".$this->module( $module )."` LIKE 'phen';";
    if($stmt = $this->db->prepare($sql)){
		$stmt->execute();
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		 
 		if(!$row)return false;	 
 			
 	} 
    $sql = "SHOW TABLES FROM `".$this->module( $module )."` LIKE 'gen';";
    if($stmt = $this->db->prepare($sql)){
		$stmt->execute();
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		 
 		if(!$row)return false;	 
 			
 	} 
    $sql = "SHOW TABLES FROM `".$this->module( $module )."` LIKE 'cov';";
    if($stmt = $this->db->prepare($sql)){
		$stmt->execute();
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		 
 		if(!$row)return false;	 
 			
 	} 
 	
 	
	$sql = "SELECT 
			gen as genotypes
		FROM 
			".$this->module( $module ).".gen 
		WHERE 
			ind = ".$ind.";";
	

 
	$stmt = $this->db->prepare($sql);
	$stmt->execute();
	if($row = $stmt->fetch(\PDO::FETCH_ASSOC)){

		$rows['genotypes'] = preg_split('//u', $row['genotypes'], NULL, PREG_SPLIT_NO_EMPTY);
		 
	} 
	else 
		return false;

	$paths[] = 'path_rawplink';
	$paths[] = 'path_rawvcf';			

	 
	$sql = "SELECT col, test,`ref.table`, `ref.col` FROM `".$this->module( $module )."`.`col` order by col ;";
    $stmt = $this->db->prepare($sql);
	$stmt->execute();
	
	if(!is_dir("../files/".$module."/trax/"))
		mkdir("../files/".$module."/trax/", 0755, true);
		
	$file = fopen("../files/".$module."/trax/links.csv","w");
	fputs($file,"col\ttest\tphen\n");
 

 



	while($ref = $stmt->fetch(\PDO::FETCH_ASSOC)){
		if( in_array( $ref['ref.table'], $paths ) ){
			
			$sql = "SELECT cov FROM `".$this->module( $module )."`.`cov` 
		WHERE 
			name LIKE (SELECT phenotypefield FROM `". $this->dbmanagemant ."`.`".$ref['ref.table']."` r WHERE module = ".$module." AND r.col = ".$ref['ref.col'].") 
		AND 
			src LIKE (SELECT phenotypefile FROM `". $this->dbmanagemant ."`.`".$ref['ref.table']."` r WHERE module = ".$module." AND r.col = ".$ref['ref.col'].");";
			$st = $this->db->prepare($sql);
			$st->execute();
			$res = $st->fetch(\PDO::FETCH_ASSOC);
			$col = $ref['col'];
			$covs[$col] = $res['cov'];     // we get name of phenotype	
		
			$sql1 = "SELECT typeoftest FROM `". $this->dbmanagemant ."`.`".$ref['ref.table']."` r WHERE module = ".$module." AND r.col = ".$ref['ref.col'];
			$stmt1 = $this->db->prepare($sql1);
			$stmt1->execute();
			$res1 = $stmt1->fetch(\PDO::FETCH_ASSOC);
			if($res1['typeoftest'] == 'LM')
				fputs($file,$col."\t". $ref['test']."\t". $res['cov']."\n");
		}
	
	} 
	fclose($file); 
	
	/*
//	print_r($paths );
//	echo $ref['ref.table'];
 

		
	if( in_array( $ref['ref.table'], $paths ) ){
		 
		$sql = "SELECT cov FROM `".$this->module( $module )."`.`cov` where name LIKE (SELECT phenotypefield FROM `". $this->dbmanagemant ."`.`".$ref['ref.table']."` r WHERE module = ".$module." AND r.col = ".$ref['ref.col'].");";
		$st = $this->db->prepare($sql);
		$st->execute();
		$res = $st->fetch(\PDO::FETCH_ASSOC);
		$cov = $res['cov'];     // we get name of phenotype
 		
 		
 	}
 	else{		
 		 
 		return false; 	
 	} 
 */	


	$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS  ".$this->module( $module ).".".$table."  AS (SELECT  *  FROM ".$this->module( $module ).".phen ORDER BY smp)";
	$stmt = $this->db->prepare($sql);
		if(	$stmt->execute()){
		
			$sql = "ALTER TABLE  ".$this->module( $module ).".".$table."  ADD `genotypes` INT(4);";
			
			$stmt = $this->db->prepare($sql);
		
			$stmt->execute();
		
		}
	else{
		return false;
	
	}
 
 
 	 

 
	foreach($rows['genotypes'] as $key=>$value){

		$k = $key + 1;

 		$sql = "UPDATE ".$this->module( $module ).".".$table." SET genotypes = $value WHERE smp = $k; ";
 		$stmt = $this->db->prepare($sql);
 		$stmt->execute();
 	
 	}
 	
 	
 	
 		$file = fopen("../files/".$module."/trax/data.csv","w");
			$sql = "SELECT * FROM `".$this->module( $module )."`.".$table."  order by smp ;";
	//		$stmt = $this->db->prepare($sql);
	//		$stmt->execute();
			
			
			$info = $this->db->fetchAll($sql);
	 
				foreach($info[0] as $key=>$value){
				
					fputs($file,$key."\t");  		
				
				}	
 				
 				fputs($file,"\n");
				foreach($info as $finfo){
					
					foreach($finfo as $value){
				
							fputs($file,$value."\t"); 

					}
				 
					fputs($file,"\n");
				}
 			 
			fclose($file); 

/* 	
 	if( $col == 49 ){
echo $ind;
		$sql = "SELECT *  FROM   ".$this->module( $module ).".".$table.";";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		while($rr = $stmt->fetch(\PDO::FETCH_ASSOC)){
  
			print_r($rr);
		}
		exit;
 	}
 */	
	return array($table, $covs);
	
}



private function rowInfo($module, $chr, $nrow){


 		$data = false;
	
 	$sql = "
 		SELECT 
 			a.alias,
 			c.chrname,
 			p.pos,
 			m.maf,
 			p.ind
 		FROM 
 			
 			".$this->module( $module ).".alias a 
 		JOIN 
 			".$this->module( $module ).".pos p using(ind)
 		JOIN 
 			".$this->module( $module ).".ind i USING(ind)
 		JOIN 
 			".$this->module( $module ).".chr c USING(chr) 
 		JOIN 
 			".$this->module( $module ).".maf m using(ind)
 		WHERE
			i.chr = ".$chr." AND  i.nrow = ". $nrow ."
		 
 		;";
 		
  		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
		
			 foreach($row as $key=>$value)
				$data[$key] = $value;
		
		
		} 
	//	print_r($data);
		if($data)
			return  $data;
		else 
			return false;

}



private function chartColor($index){
	
			switch($index){
	
				case 0:
					 return 'rgb(255,0,0)';
				break;
				case 1:
					 return 'rgb(0,255,0)';
 				break;
				case 2:
					 return 'rgb(0,0,255)';
 				break;
				default:
					 return 'rgb(255,255,255)';
 				break;	
	
			}
}




private function getCol($module, $test){


    $sql = "SELECT col FROM ".$this->module($module).".col WHERE test = '".$test."'";

    $data = $this->getFields( $test );
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    if($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
	$col = $row['col'];
	return $col;
    }
    else
	return false;


}






private function addChart( $table, $links, $testData, $module, $ind, $chr, $x, $y, $maxwidth){
 	
	$height = 400;
	$width = 600;
	$barW = 35;
	$i = 0;
	
	$freq = array();
	$data = array();
	
	foreach($testData as $key=>$value){
	
	
    $colnumber = $this->getCol($module, $key);
    
  
    
    if( !isset( $links[$colnumber]) ) {
	
		continue;
    
    }
    $link = $links[$colnumber];
    
 // include pictures
 	
 	$newkey = str_replace('/','_',$key);
 	
 	
    if(is_file("../files/$module/trax/$newkey.png")){
 	/*
		$myurl = "../files/$module/trax/$newkey.pdf";
		$image = new \Imagick($myurl);
		$image->setResolution( 300, 300 );
		$image->setImageFormat( "png" );
		$image->writeImage("../files/$module/trax/$newkey.png");
 			*/	
 		
 		 if(is_file("../files/$module/trax/$newkey.txt")){
 		 	
 		 	$datatable = file_get_contents("../files/$module/trax/$newkey.txt");
 		 	
 		 	$datatable = substr_replace($datatable, '',  -1);
 		 	
 		 	$datatable = str_replace('"', "", $datatable );
 		 
 		 }
 
 		$lines = explode("\n", $datatable);
 		$c = 0;
 		foreach($lines as $line){
 			
 			$values = explode("\t", $line);
 			
 			foreach($values as $val){
 			
 				$bottomtable[$c][] = $val;
 			
 			}
 			
 			$c++;
 			
 		}
 	
		$testname = $key;
		$s = $x + ($width + 100) * $i;

		$toptable = '<h3>'. $key . '</h3><br>' . '-log(p) = '. -1 * round(log10($value['pval']),3) . '<br>' . 'QAS =  -' . round($value['qas'], 3) ;
							 
					
		$dom = new \DOMDocument( '1.0', 'utf-8' );
		$svg = $dom->createElement( 'svg',  null); 
		$svg->setAttribute('width', $width);
		$svg->setAttribute('height', $height);





		$box = $dom->createElement( 'image', null );
		$box->setAttribute('width', $width);
		$box->setAttribute('xlink:href',  'data:image/png;base64,' . base64_encode(file_get_contents("../files/$module/trax/$newkey.png")) );
		$box->setAttribute('height', $height);
		$svg->appendChild($box);
		$dom->appendChild($svg);		
		 
		$data[$i]['image'] = $dom->saveXML();		
		$data[$i]['bottom'] = $bottomtable;		
		$data[$i]['top'] = $toptable;
		$bottomtable =  false;
		$i++; 
		
		continue;

	}
    
 	else{
 	
 	
 		$dom = new \DOMDocument( '1.0', 'utf-8' );
		$svg = $dom->createElement( 'svg',  null); 
		$svg->setAttribute('width', $width);
		$svg->setAttribute('height', $height);

		$y = 0;
		$x = 0;

		$freq = $this->getFreq($module, $table, $key, $link);
		
		if(false === $freq)
			continue;
		
	//	print_r($freq); exit;
 	/*
		if(($x + ($width + 100) * ($i + 1)) > $maxwidth){

			$i = 0;
			$y = $y + $height + 100;


		}
		*/
		$testname = $key;

		$toptable =  '<br>' . '-log(p) = '. -1 * round(log10($value['pval']),3) . '<br>' . 'QAS =  -' . round($value['qas'], 3) ;


	//	 $y  =  $y  + 11;
		$box = $dom->createElement( 'rect', null);
		$box->setAttribute('width', $width);
		$box->setAttribute('height', $height);
		$box->setAttribute("fill", 'rgb(255,255,255)');
 		$box->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
		$svg->appendChild($box);
		
		
		$bars = false;
		$bars = $this->getBarsHeight( $freq, $key );
		$shift = ceil($width/(sizeof($bars)+1));
		$max = 0;
		foreach($bars as $key=>$value){

			$s = sizeof($value) - 1;
		 
			if($max < $value[$s]) $max=$value[$s];  
		 
		}
		
		$N = 0;
		
		for($c=0;$c<sizeof($bars);$c++){
			 
			 
			for($j = 0; $j < sizeof($bars[$c]) - 1; $j ++){
				
				$N = $bars[$c][$j] + $N;
				
				$box = $dom->createElement( 'rect', null);
				$box->setAttribute('width', $barW);
				$box->setAttribute('y', $y + $height - 1 - $bars[$c][$j]/$max * $height * 0.6);
				$box->setAttribute('x', $x  + $shift - 5 + $shift * $c + $j * $barW - $barW);
				$box->setAttribute('height', $bars[$c][$j]/$max * $height * 0.6);
				
				//$this->writeText( $dom, $svg, $bars[$c][$j] , $x + ($width + 100) * $i + $shift - 5 + $shift * $c ,   $y + 120 + 11 * $j, $style = 'font-size:8pt' );
		   
		   		$bottomtable[$j][$c] = $bars[$c][$j];
		   
				$box->setAttribute('style', 'stroke-width:.3;stroke:rgb(0,0,0)');
				$box->setAttribute("fill", $this->chartColor($j));
				$svg->appendChild($box);
			/*	
				if($c == 0 && $j != 0) {
					
					$box = $dom->createElement( 'line', null);
					$box->setAttribute('x1', $x + ($width + 100) * $i + $shift - 5 + $shift * $c  -  $shift/2);
					$box->setAttribute('y1', $y + $height  + $j * 10 + 13 );
					$box->setAttribute('x2', $x + ($width + 100) * $i + $shift - 5 + $shift * $c  + $width -  $shift) ;
					$box->setAttribute('y2', $y + $height  + $j * 10 + 13);
					$box->setAttribute('style', 'stroke-width:0.2;stroke:rgb(0,0,0)');
					$svg->appendChild($box);

				
				}
			*/	
				
			}
			$this->writeText( $dom, $svg, $bars[$c][sizeof($bars[$c]) - 1], $x +  $shift - 5 + $shift * $c ,   $y + 10 , $style = 'font-size:8pt' );
		}
			
		$keys = explode('/',$testname);
			
			
		if($keys[3] == 'A') $N = $N/2;
			//$this->writeText( $dom, $svg, 'N = ' . $N, $x + ($width + 100) * $i ,  $y - 11, $style = 'font-size:8pt' );
			 
		$toptable ='<strong>'.  $testname . '</strong><br><br>' . 'N = ' . $N . $toptable.'<br>';	
		
		$dom->appendChild($svg);
		 
		$data[$i]['image'] = $dom->saveXML();		
		$data[$i]['bottom'] = $bottomtable;	
		
		$bottomtable = array();
			
		$data[$i]['top'] = $toptable;	
			
 		$i++;
  
 		

 		
 		
 		}
	}
	//print_r($data);
	return $data;
	
}
private function getBarsHeight( $freq, $test ){
	$bars = array();
	$fields = $this->getFields( $test );
	 
	 	for($j = 0; $j < sizeof($freq); $j++){
	 	
			switch($fields['genmodel']){
	
				case "D":
				//if(sizeof($freq[$j])==2)$freq[$j][2] = 0;
					$bars[$j][0] = $freq[$j][0];
					$bars[$j][1] = $freq[$j][1] + $freq[$j][2];
 			
				break;
				case "R":
				//if(sizeof($freq[$j])==2)$freq[$j][2] = 0;
					$bars[$j][0] = $freq[$j][0] + $freq[$j][1];
					$bars[$j][1] = $freq[$j][2];
 				break;
				case "A":
			//	if(sizeof($freq[$j])==2)$freq[$j][2] = 0;
					$bars[$j][0] = 2 * $freq[$j][0] + $freq[$j][1];
					$bars[$j][1] = $freq[$j][1] + 2 * $freq[$j][2];
 				break;
				case "CD":
				 
			//		if(sizeof($freq[$j])==2)$freq[$j][2] = 0;
					$bars[$j][0] = $freq[$j][0];
					$bars[$j][1] = $freq[$j][1];
					$bars[$j][2] = $freq[$j][2];
 				break;	
	
			}
			$bars[$j][sizeof($bars[$j])] = array_sum($bars[$j]);
		}
 
 
 
	return $bars;
	
}



private function getFields( $test ){

	$fields = $data =  array();
	$fields = explode('/', $test);
	$data['testname'] = $fields[0];
	$data['cohort'] = $fields[1];
	$data['testtype'] = $fields[2];
	$data['genmodel'] = $fields[3];
	return $data;
}

private function getObject($module, $test){


	return '';
/*
	$sql = "SELECT object FROM ".$this->module($module).".col WHERE test = '".$test."'";

	$data = $this->getFields( $test );
	$stmt = $this->db->prepare($sql);
	$stmt->execute();
	if($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
		$object = $row['object'];
		return $object;
	}
	else
		return false;

 */
}

function stdToArray($obj){

	  $reaged = (array)$obj;
	  
	  foreach($reaged as $key => &$field){
	  
		if(is_object($field))$field = $this->stdToArray($field);
		
	  }
	  
	  return $reaged;
}

private function getFreq($module, $table, $test, $link){


		 
	
		$sql = "
					SELECT  
					 
						distinct `". $link ."` as `phenotypes` 
					FROM  
						".$this->module( $module ).".$table ph WHERE `". $link ."` NOT LIKE ''";

		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
		
			$phens[] = $row['phenotypes'];
		
		}
		

//print_r($phens); 
		$query = '';
		$c = 0;
		$gntps = array('0','1','2','3');
		
	
		
		
		foreach($phens as  $value){

			$query = " `". $link ."` = ". $value;
			
			foreach($gntps as $g){ 
			
				$freq[$c][$g] = 0;
				
				$sql = "
				SELECT  
					COUNT(*) as c
				FROM  
					".$this->module( $module ).".$table ph
				
				WHERE  genotypes = ". $g ." AND
					$query  ";
	 
				$stmt = $this->db->prepare($sql);
				$stmt->execute();
				$row = $stmt->fetch(\PDO::FETCH_ASSOC);
				$freq[$c][$g] = $row['c'];
	 
				$g . ' '. $row['c'] . "\n";
			}
	 
				$c++;
				
		}
			//print_r($freq);
 
		return $freq;

}


private function sortTests($testData, $sortkey){

	$keys = array();
	$newTests = array();
	
	foreach($testData as $key=>$value){
	
		$keyparts = explode("/", $key);
		
		$c = 0;
		
		foreach( $keyparts  as $k=>$v){
			
			$keys[$c][$v] = 1;
			
			$c++;
		
		}
		
	
	}
	
	
	foreach($keys[$sortkey] as $key=>$value){
		
		foreach($testData as $k=>$v){
	
			$keyparts = explode("/", $k);
		 
			if($keyparts[$sortkey] === $key){
		
				$newTests[$k] = $v;
		
			}
			
		}

	}
	
		return $newTests;
	
}

private function addDiagramLines($dom, $parent, $x, $y){


		$barW = 15;
		$barH = 40;  
	 	$width = 990;	
		$x = 80;


			$box = $dom->createElement( 'rect', null);
			$box->setAttribute('width', $width);
			$box->setAttribute('y', $y - 1);
			$box->setAttribute('x', $x);
			$box->setAttribute('height', .2);
			$box->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
		
			$parent->appendChild($box);
		
			$box = $dom->createElement( 'rect', null);
			$box->setAttribute('width', 1);
			$box->setAttribute('y', $y+4);
			$box->setAttribute('x', $x);
			$box->setAttribute('height', $barH);
			$box->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
			$parent->appendChild($box);
		
			$box = $dom->createElement( 'rect', null);
			$box->setAttribute('width', 1);
			$box->setAttribute('y', $y - $barH -1);
			$box->setAttribute('x', $x);
			$box->setAttribute('height', $barH);
			$box->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
			$parent->appendChild($box);

			$box = $dom->createElement( 'rect', null);
			$box->setAttribute('width', $width);
			$box->setAttribute('y', $y+4);
			$box->setAttribute('x', $x );
			$box->setAttribute('height', .2);
			$box->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
			$parent->appendChild($box);

	//lines
			$qasVal = array (2, 1.75, 1.5, 1.25, 1 );
			for($i = 0; $i<=4; $i++){	
			 
				$box = $dom->createElement( 'line', null);
				$box->setAttribute('x1', $x - 5 );
				$box->setAttribute('y1', ( $y - $barH - 1  )+ $i*$barH/4 );
				$box->setAttribute('x2', $x + $width   );
				$box->setAttribute('y2', ( $y - $barH - 1) + $i*$barH/4 );
				$box->setAttribute('style', 'stroke-width:0.2;stroke:rgb(0,0,0)');
				$parent->appendChild($box);
			
				$text = $dom->createElement( "text", $qasVal[$i]);
				$text->setAttribute('style', 'font-size:5pt');
				$text->setAttribute('x', $x - 20 );
				$text->setAttribute('y', ( $y - $barH - 2  )+ $i*$barH/4 );
				$parent->appendChild($text);
			
			 
			}
		
			$pvalVal = array (0, -1, -2, -3, -4 );
			for($i = 0; $i<=4; $i++){	
			 
				$box = $dom->createElement( 'line', null);
				$box->setAttribute('x1', $x - 5 );
				$box->setAttribute('y1', ( $y + $barH + 4  ) - $i*$barH/4 );
				$box->setAttribute('x2', $x + $width   );
				$box->setAttribute('y2', ( $y + $barH + 4 ) - $i*$barH/4 );
				$box->setAttribute('style', 'stroke-width:0.2;stroke:rgb(0,0,0)');
				$parent->appendChild($box);
			
			
				$text = $dom->createElement( "text", $pvalVal[$i]);
				$text->setAttribute('style', 'font-size:5pt');
				$text->setAttribute('x', $x - 20 );
				$text->setAttribute('y', ( $y + 8  )+ $i*$barH/4 );
				$parent->appendChild($text);
			
			 
			}
			//test labels	

		
			$this->writeText( $dom, $parent, 'log(p)', $x - 50, $y+$barH, $style = 'font-size:8pt' );
			$this->writeText( $dom, $parent, 'QAS', $x - 50, $y-$barH, $style = 'font-size:8pt' );
		


}

private function addDiagram( $testData ){
	
		$sortkey = 'testtype';   //    testname cohort testtype genmodel
		$barW = 15;
		$barH = 40;  
	 	$width = 990;	
		$x = 80; // left 
		$y = 10;
		$diagDy = 80; //distance between diagrams
		
		$data = array();
 /*		
		$dom = new \DOMDocument( '1.0', 'utf-8' );
		$svg = $dom->createElement( 'svg',  null); 
		$svg->setAttribute('width', '29.7cm');
		$svg->setAttribute('height', '17.0cm');
		$style = $dom->createElement( 'style', 'text { font-size: 8pt; font-family: Arial, sans-serif }');
	
		$svg->appendChild($style);
*/ 	

		
		$testData = $this->sortTests($testData, 2) ;
		//print_r($testData);
			 
			$h = 0;
			$c = 0;
			$prev = '';
			
			foreach($testData as $key => $value){
						
				$c++;
				$tests = $this->getFields( $key );
			
				if($prev !== $tests[$sortkey] ){ 
						$y = 10;

						if( $prev != '' ){
							$dom->appendChild($svg);
							$data[] = $dom->saveXML();
						}
						$y += $diagDy ;
						$dom = new \DOMDocument( '1.0', 'utf-8' );
						$svg = $dom->createElement( 'svg',  null);
						$svg->setAttribute('width', '29.7cm');
						$svg->setAttribute('height', '7.0cm');
						$style = $dom->createElement( 'style', 'text { font-size: 8pt; font-family: Arial, sans-serif }');
						$svg->appendChild($style);			
						$this->writeText( $dom, $svg, $tests['testtype'] . ' tests', 60,20 , $style = 'font-size:15pt' );
					    
						$this->addDiagramLines($dom, $svg, 80, $y );	
								
						
						$c = 0;
						$x = 80;				
						 
				}
				
				if($c > 25){
				$y = 10;
					$dom->appendChild($svg);
					$data[] = $dom->saveXML();
					
					$dom = new \DOMDocument( '1.0', 'utf-8' );
					$svg = $dom->createElement( 'svg',  null); 
					$svg->setAttribute('width', '29.7cm');
					$svg->setAttribute('height', '7.0cm');
					$style = $dom->createElement( 'style', 'text { font-size: 8pt; font-family: Arial, sans-serif }');
					$svg->appendChild($style);
					
					$c = 0;
					$y += $diagDy;
					$this->addDiagramLines($dom, $svg, 80,$y);	


					$x = 80;
						
					 
				}
			
				$prev = $tests[$sortkey];
			
	//top QAS
			
				if($value['qas'] > 2) $h = $barH;
				elseif($value['qas'] < 1 && $value['qas'] > 0) {
					$h = 1/$value['qas'] ;
			 		
					if($h > 2)$h = $barH;
					else $h = ($h - 1) * $barH;
				
				}
				elseif($value['qas'] < 0) $h = 0;
				else $h = ($value['qas'] -1) * $barH;
			 
				$box = $dom->createElement( 'rect', null);
				$box->setAttribute('width', $barW);
				$box->setAttribute('height', $h);
				$box->setAttribute('y', $y - $h - 1);
				$box->setAttribute('x', $x = $x+30);
				$box->setAttribute("fill", $this->getColor($value['qas'], 'qas') );
				$box->setAttribute('style', 'stroke-width:.3;stroke:rgb(0,0,0)');
				$svg->appendChild($box);

	//bottom

				if(log10($value['pval']) < -4) $h = $barH;
				elseif(log10($value['pval']) > -4 && log10($value['pval']) < -2) $h = log10($value['pval']) * $barH/4;
				else $h = log10($value['pval']) * $barH/4;
			
				$h = abs($h);
				 
			
				$box = $dom->createElement( 'rect', null);
				$box->setAttribute('width', $barW);
				$box->setAttribute('height', abs($h));
				$box->setAttribute('y', $y + 4);
				$box->setAttribute('x', $x );
				$box->setAttribute("fill", $this->getColor($value['pval'], 'pval') );
				$box->setAttribute('style', 'stroke-width:.3;stroke:rgb(0,0,0)');
				$svg->appendChild($box);
				
				 
			 
				$box = $dom->createElement( 'text', $key );
				$box->setAttribute('y', $y + 40 );
				$box->setAttribute('x', $x + 40);
				$box->setAttribute('transform', 'rotate(40 '. $x .','. $y .')');

				$box->setAttribute('style', 'font-size:8pt');
				$svg->appendChild($box);
		
	}
	$dom->appendChild($svg);
	$data[] = $dom->saveXML();

	return $data;
	
	
}

public function getDataListTraxReport( $module, $chr, $nrow ){

 		$data = false;
	
		$sql = "
 		SELECT 
 			c.test,
 			p.pval,
 			q.ratio
 		FROM 
 			".$this->module( $module ).".col c
 		JOIN 
 			".$this->module( $module ).".v_ind v USING(col)
 		JOIN 
 			".$this->module( $module ).".pval p USING(v_ind)
 		JOIN 
 			".$this->module( $module ).".ratio q using(v_ind)
 		JOIN 
 			".$this->module( $module ).".ind i using(ind)
 		
		WHERE
			i.chr = ".$chr." AND i.nrow = ".$nrow."
		ORDER BY
			c.col
 		;";
 		
  
 		
 		if($stmt = $this->db->prepare($sql)){
 			$stmt->execute();
 			while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
 			
 				$test = $row['test'];
 				$data[$test]['pval'] = $row['pval'];
 				$data[$test]['qas'] = $row['ratio'];
 			//	$data[$test]['col'] = $row['col'];
 			
 			
 			} 
  			return  $data;
 		
		}
		else 
			return false;

}






public function getProjectTitle( $module ){

 		$data = false;
	
		$sql = "
 		SELECT 
 			*
 		FROM 
			". $this->dbmanagemant .".project
		WHERE 
			id = '". $module ."'
 		
 		;";
 		
  
 		
 		if($stmt = $this->db->prepare($sql)){
 			$stmt->execute();
 			$row = $stmt->fetch(\PDO::FETCH_ASSOC);
 			
 			$data['title'] = $row['title'];
 			$data['description'] = $row['description'];
 			
 			
 			 
  			return  $data;
 		
		}
		else 
			return false;

}



public function getGenes( $chr, $pos ){

 		$str = false;
	
		$sql = "
 		SELECT 
 			*
 		FROM 
			". $this->dbmanagemant .".genes
		WHERE 
			chr = ". $chr ."
		AND
			posstart <= ".$pos."
		AND
			posend >= ".$pos."
 		
 		;";
 		
  
 		
 		if($stmt = $this->db->prepare($sql)){
 			$stmt->execute();
 			while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
 			
 			$str = $row['gene'] . " ";
 			
 			
 			
 			} 
  			return  $str;
 			
		}
		else 
			return false;

}






private function writeText( $dom, $parent, $text, $x, $y, $style = '' ){
 
		$box = $dom->createElement( 'text', $text );
		$box->setAttribute('x', $x);
		$box->setAttribute('y', $y);
		$box->setAttribute('dy', '0.1em');
 		$box->setAttribute('style', $style);
  
		$parent->appendChild($box);

}



private function writeTable( $dom, $parent, $datatable, $x, $y ){
 		$y+=10;
 		$arr = explode("\n",$datatable);
		foreach($arr as $line){
			$values = explode(" ",$line);
			$x0 = $x;
			foreach($values as $value){
		
				$value = stripslashes($value);
				if(is_double($value))$value = round($value, 3);
				$box = $dom->createElement( 'text', $value );
				$box->setAttribute('x', $x0+=40);
				$box->setAttribute('y', $y);
				$box->setAttribute('dy', '0.1em');

				$parent->appendChild($box);
			
			}
			
			$y+=10;
		}
		
}

// Removed generateTRAXreport method - not needed for new schema
/*
public function generateTRAXreport( $request )
{

 
 	 
		$chr = $request->query->get('chr');
		if (!isset($chr))
		{
			$this->ReturnError("No chromosome specified");
		}
		$row = $request->query->get('row');
		if (!isset($row))
		{
			$this->ReturnError("No row specified");
		}

		$module = trim($request->query->get('module'));
		if (!$module)
		{
			$this->ReturnError("No module specified");
		}
	 
		$pos = trim($request->query->get('pos'));
		if (!$pos)
		{
			$this->ReturnError("No position specified");
		}
	 
		
		
		
	$rowInfo = $this->rowInfo( $module, $chr, $row);
	
	

	$ind = $rowInfo['ind'];
	
	$description = $this->getProjectTitle( $module );
	$genes = $this->getGenes( $chr, $pos );
	
	$testData = $this->getDataListTraxReport( $module, $chr, $row);


	$dom = new \DOMDocument( '1.0', 'utf-8' );

	$svg = $dom->createElement( 'svg',  null); 
		
	$svg->setAttribute('width', '29.7cm');
	$svg->setAttribute('height', '7.0cm');
	 

	$style = $dom->createElement( 'style', 'text { font-size: 8pt; font-family: Arial, sans-serif }');
	$svg->appendChild($style);
 
 
	$titleString = "Trax Report for ". $rowInfo['alias'];

	$topBar = $dom->createElement( "rect", null);
	$topBar->setAttribute("width", 1100);
	$topBar->setAttribute("height", 150);
	$topBar->setAttribute('x', 10);
	$topBar->setAttribute('y', 10);
	$topBar->setAttribute("fill", 'rgb(255,255,255)' );
	$topBar->setAttribute('style', 'stroke-width:1;stroke:rgb(0,0,0)');
	$svg->appendChild($topBar);
	
	$this->writeText( $dom, $svg, 'Trax Report for '. $rowInfo['alias'], 25, 45, $style = 'font-size:20pt' );
	$this->writeText( $dom, $svg, 'Project: '.$description['title'], 25, 75, $style = 'font-size:12pt' );
	$this->writeText( $dom, $svg, 'SNP cluster ID (rs ID): '.$rowInfo['alias'], 25,105, $style = 'font-size:12pt' );
	$this->writeText( $dom, $svg, 'Chromosome: '.$rowInfo['chrname'], 325, 105, $style = 'font-size:12pt' );
	$this->writeText( $dom, $svg, 'Coordinates: '.$this->addCommas($rowInfo['pos']), 25, 135, $style = 'font-size:12pt' );
	if($genes)$this->writeText( $dom, $svg, 'Gene region: '. $genes, 550, 135, $style = 'font-size:12pt' );
	$this->writeText( $dom, $svg, 'MAF: '.round($rowInfo['maf'],3), 325, 135, $style = 'font-size:12pt' );
	
	
	$title = $dom->createElement( 'title', $titleString);
	$svg->appendChild($title);	
	

	$dom->appendChild($svg);
	$data['head'] = $dom->saveXML();


#############
	
	$data['diagram'] = $this->addDiagram( $testData );  

#############
	
	$data['legend'] = "Black: QAS > 2 or -log(P) < -4 <br> Red: -4 < -log(P) < -2 <br> Grey: 1/QAS <br> White: QAS or -log(P)";
#############
echo getcwd() . "\n";
	$res = $this->generateNewTmpTable( $module, $ind);
  		 
 	if($res){
 		$table = $res[0];	
 		$links = $res[1]; 	
 		exec('Rscript ../tools/trax_run_prepar.r ' . $module ." ". $ind .' 1 4'); 
		$data['charts'] = $this->addChart( $table, $links, $testData, $module, $ind, $chr,  100, 0, 1000);
	
	}
#############
	return $data;




}
*/

}