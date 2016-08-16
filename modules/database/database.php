<?php
include_once(dirname(__FILE__) . "/db_properties.php");
include_once(dirname(__FILE__) . "/../utils.php");

class Database {

	var $con = null;
	static $instance = null;
	
	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __clone() { }
	
	public function connect() {
		if($this->con === null) {
			global $db_name, $db_user, $db_password, $db_host;
			$this->con = mysqli_connect($db_host, $db_user, $db_password, $db_name);
			if (!$this->con) {
				$this->con = null;
				throw new Exception("SQLConnection error: " . mysqli_connect_error());
			}
		}
		else {
			return $this->con;
		}
	}
	
	public function disconnect() {
		if($this->con !== null) {
			mysqli_close($this->con);
			$this->con = null;
		}
	}
	
	private function insert_zone($id=0, $name="", $frozen="0") {
		$this->connect();
		$stmt = $this->con->prepare("insert into raid (id, name, frozen) values (?,?,?) ON DUPLICATE KEY UPDATE name=values(name), frozen=values(frozen);");
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if(empty($frozen)) {
				$frozen = "0";
			}
			
			if( !$stmt->bind_param("iss", $id, $name, $frozen) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$stmt->close();
			return mysqli_insert_id($this->con);
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
		
	}
	
	public function get_zones($frozen=null) {
		$this->connect();
		$sql = "select raid.id as id, raid.name as name, raid.frozen as frozen from raid";
		if(is_bool($frozen) === true) {
			$sql .= " where frozen=?"; 
		}
		$sql .= ";";
		
		$stmt = $this->con->prepare($sql);
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if(is_bool($frozen) === true) {
				$param = "0";
				if($frozen === true) {
					$param = "1";
				}
				if( !$stmt->bind_param("s", $param) ) {
					throw new Exception($stmt->error, $stmt->errno);
				}
			}
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	private function insert_encounter($id=0, $raid_id=0, $name="") {
		$this->connect();
		$stmt = $this->con->prepare("insert into encounter (id, raid_id, name) values (?,?,?) ON DUPLICATE KEY UPDATE raid_id = values(raid_id), name = values(name);");
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->bind_param("iis", $id, $raid_id, $name) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$stmt->close();
			return mysqli_insert_id($this->con);
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	private function insert_bracket($id=0, $raid_id=0, $name="", $rangeA=0, $rangeB=0) {
		$this->connect();
		$stmt = $this->con->prepare("insert into bracket (id, raid_id, name, rangeA, rangeB) values (?,?,?,?,?) ON DUPLICATE KEY UPDATE raid_id=values(raid_id), name=values(name), rangeA=values(rangeA), rangeB=values(rangeB);");
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->bind_param("iisii", $id, $raid_id, $name, $rangeA, $rangeB) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$stmt->close();
			return mysqli_insert_id($this->con);
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function init_zones(array $zones) {
		try {
			foreach($zones as $zone) {
				$zone = (array) $zone;
				$id = $zone['id'];
				$name = $zone['name'];
				$frozen = $zone['frozen'];
				$this->insert_zone($id, $name, $frozen);

				$encounters = (array) $zone['encounters'];
				foreach($encounters as $encounter) {
					$encounter = (array) $encounter;
					$eid = $encounter['id'];
					$ename = $encounter['name'];
					$this->insert_encounter($eid, $id, $ename);
				}
				
				if( array_key_exists('brackets', $zone) ) {
					$brackets = (array) $zone['brackets'];
					foreach($brackets as $bracket) {
						$bracket = (array) $bracket;
						$bid = $bracket['id'];
						$bname = $bracket['name'];
						preg_match('/^((?P<min>\d+)((\+)|(\-(?P<max>\d+))) Item Level)$/i', $bname, $matches);
						$min = isset($matches['min']) ? $matches['min'] : -1;
						$max = isset($matches['max']) ? $matches['max'] : -1;
						$this->insert_bracket($bid, $id, $bname, $min, $max);
					}
				}
			}
		} catch (Exception $e) {
			print $e;
		}
	}
	
	public function delete_rankings($character, $server, $region) {
		$this->connect();
		$exc = null;
		
		try {
		
			$stmt = $this->con->prepare("delete from dps_ranking where name = ? and server = ? and region = ?");		
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}

			if( !$stmt->bind_param("sss", $character, $server, $region) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			$stmt->close();
			
			$stmt = $this->con->prepare("delete from hps_ranking where name = ? and server = ? and region = ?");		
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}

			if( !$stmt->bind_param("sss", $character, $server, $region) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			$stmt->close();
			return true;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
		return false;
	}
	
	public function insert_dps_rankings($character, $server, $region, $rankings) {
		$this->connect();
		$stmt = $this->con->prepare("insert into dps_ranking (name, server, region, encounter_id, class, spec, guild, rank, outOf, duration, startTime, reportID, fightID, difficulty, size, itemLevel, total) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE rank=values(rank), outOf=values(outOf), total=values(total), guild=values(guild), spec=values(spec), itemLevel=values(itemLevel), size=values(size), fightID=values(fightID), reportID=values(reportID), startTime=values(startTime), duration=values(duration);");
		
		$exc = null;
		mysqli_autocommit($this->con, FALSE);
		
		if(!$stmt) {
			throw new Exception($this->con->error, $this->con->errno);
		}
	
		try {
			
			$affected_rows = 0;
			
			foreach($rankings as $ranking) {
				$ranking = (array) $ranking;
				$encounter_id = $ranking['encounter']; 
				$class = $ranking['class']; 
				$spec = $ranking['spec'];
				$guild = $ranking['guild'];
				$rank = $ranking['rank'];
				$outOf = $ranking['outOf'];
				$duration = $ranking['duration'];
				$startTime = $ranking['startTime'];
				$reportID = $ranking['reportID'];
				$fightID = $ranking['fightID'];
				$difficulty = $ranking['difficulty'];
				$size = $ranking['size'];
				$itemLevel = $ranking['itemLevel'];
				$total = $ranking['total'];

				if( !$stmt->bind_param("sssiiisiiiisiiiid", $character, $server, $region, $encounter_id, $class, $spec, $guild, $rank, $outOf, $duration, $startTime, $reportID, $fightID, $difficulty, $size, $itemLevel, $total) ) {
					throw new Exception($stmt->error, $stmt->errno);
				}
					
				if( !$stmt->execute() ) {
					throw new Exception($stmt->error, $stmt->errno);
				}
				
				$stmt->store_result();
				$affected_rows += ($stmt->affected_rows > 0 ? 1 : 0);
				$stmt->free_result();

			}
			
			mysqli_commit($this->con);
			mysqli_autocommit($this->con, TRUE);
			return $affected_rows;
		} 
		catch(Exception $e) {
			mysqli_rollback($this->con);
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		mysqli_autocommit($this->con, TRUE);
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function insert_hps_rankings($character, $server, $region, $rankings) {
		$this->connect();
		$stmt = $this->con->prepare("insert into hps_ranking (name, server, region, encounter_id, class, spec, guild, rank, outOf, duration, startTime, reportID, fightID, difficulty, size, itemLevel, total) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE rank=values(rank), outOf=values(outOf), total=values(total), guild=values(guild), spec=values(spec), itemLevel=values(itemLevel), size=values(size), fightID=values(fightID), reportID=values(reportID), startTime=values(startTime), duration=values(duration);");
		
		$exc = null;
		mysqli_autocommit($this->con, FALSE);
		
		if(!$stmt) {
			throw new Exception($this->con->error, $this->con->errno);
		}
	
		try {
		
			$affected_rows = 0;
			
			foreach($rankings as $ranking) {
				$ranking = (array) $ranking;
				$encounter_id = $ranking['encounter']; 
				$class = $ranking['class']; 
				$spec = $ranking['spec'];
				$guild = $ranking['guild'];
				$rank = $ranking['rank'];
				$outOf = $ranking['outOf'];
				$duration = $ranking['duration'];
				$startTime = $ranking['startTime'];
				$reportID = $ranking['reportID'];
				$fightID = $ranking['fightID'];
				$difficulty = $ranking['difficulty'];
				$size = $ranking['size'];
				$itemLevel = $ranking['itemLevel'];
				$total = $ranking['total'];

				if( !$stmt->bind_param("sssiiisiiiisiiiid", $character, $server, $region, $encounter_id, $class, $spec, $guild, $rank, $outOf, $duration, $startTime, $reportID, $fightID, $difficulty, $size, $itemLevel, $total) ) {
					throw new Exception($stmt->error, $stmt->errno);
				}
					
				if( !$stmt->execute() ) {
					throw new Exception($stmt->error, $stmt->errno);
				}
				
				$stmt->store_result();
				$affected_rows += ($stmt->affected_rows > 0 ? 1 : 0);
				$stmt->free_result();
			
			}
			
			mysqli_commit($this->con);
			mysqli_autocommit($this->con, TRUE);
			return $affected_rows;
		} 
		catch(Exception $e) {
			mysqli_rollback($this->con);
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		mysqli_autocommit($this->con, TRUE);
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_brackets($raid_id=0) {
		$this->connect();
		$stmt = $this->con->prepare("select id, name, rangeA, rangeB, raid_id from bracket where raid_id = ?;");
		$exc = null;

		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->bind_param("i", $raid_id) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_encounters($raid_id=0) {
		$this->connect();
		$stmt = $this->con->prepare("select id, name, raid_id as raid from encounter where raid_id = ?;");
		$exc = null;

		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->bind_param("i", $raid_id) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_rankings() {
		$this->connect();
		$sql = "select * from (select dps_ranking.region as region, dps_ranking.server as server, dps_ranking.name as name, count(*) as total, 'bossdps' as metric from dps_ranking group by dps_ranking.region, dps_ranking.server, dps_ranking.name UNION ALL select hps_ranking.region as region, hps_ranking.server as server, hps_ranking.name as name, count(*) as total, 'hps' as metric from hps_ranking group by hps_ranking.region, hps_ranking.server, hps_ranking.name) as tmp order by name asc;";
		
		$stmt = $this->con->prepare($sql);
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_dps_ranking($character, $server, $region, $raid_id = -1, $encounter_id = -1, $difficulty = -1) {
		$this->connect();
		
		$sql = "select dps_ranking.guild as guild, (1-((dps_ranking.rank-1)/dps_ranking.outOf)) as rank, dps_ranking.reportID as reportID, dps_ranking.fightID as fightID, dps_ranking.itemLevel as itemLevel, dps_ranking.total as total, encounter.name as encounter, dps_ranking.encounter_id as encounter_id, difficulty.name as difficulty, dps_ranking.duration as duration, dps_ranking.startTime as time, raid.name as raid, raid.id as raidId, 'bossdps' as metric from dps_ranking, difficulty, encounter, raid, spec, role where dps_ranking.difficulty = difficulty.id and dps_ranking.class = spec.class_id and dps_ranking.spec = spec.id and spec.role_id = role.id and role.metric = 'bossdps' and dps_ranking.encounter_id = encounter.id and encounter.raid_id = raid.id and dps_ranking.name = ? and dps_ranking.server = ? and dps_ranking.region = ?";
		
		if( !isset($raid_id) || $raid_id === null ) {
			$sql .= " and raid.id > ?";
			$raid_id = 0;
		}
		else {
			$sql .= " and raid.id = ?";
		}
		
		if( !isset($encounter_id) || $encounter_id === null ) {
			$sql .= " and encounter.id > ?";
			$encounter_id = 0;
		}
		else {
			$sql .= " and encounter.id = ?";
		}
		
		if( !isset($difficulty) || $difficulty === null ) {
			$sql .= " and dps_ranking.difficulty > ?";
			$difficulty = 0;
		}
		else {
			$sql .= " and dps_ranking.difficulty = ?";
		}
		
		//$sql .= " order by raid.id asc, dps_ranking.encounter_id asc, dps_ranking.difficulty asc, dps_ranking.startTime asc;";
		$sql .= " order by dps_ranking.startTime asc;";
		$stmt = $this->con->prepare($sql);
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->bind_param("sssiii", $character, $server, $region, $raid_id, $encounter_id, $difficulty) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_dps_rank($character, $server, $region, $raid_id, $encounter_id, $difficulty) {
		$this->connect();
		$sql = "select max(1-((dps_ranking.rank-1)/dps_ranking.outOf)) as max, avg(1-((dps_ranking.rank-1)/dps_ranking.outOf)) as avg, min(1-((dps_ranking.rank-1)/dps_ranking.outOf)) as min, encounter.name as encounter, difficulty.name as difficulty, raid.name as raid, raid.id as raidId, 'bossdps' as metric from dps_ranking, difficulty, encounter, raid, spec, role where dps_ranking.difficulty = difficulty.id and dps_ranking.class = spec.class_id and dps_ranking.spec = spec.id and spec.role_id = role.id and role.metric = 'bossdps' and dps_ranking.encounter_id = encounter.id and encounter.raid_id = raid.id and dps_ranking.name = ? and dps_ranking.server = ? and dps_ranking.region = ? and dps_ranking.difficulty > 2";

		if( !isset($raid_id) || $raid_id === null ) {
			$sql .= " and raid.id > ?";
			$raid_id = 0;
		}
		else {
			$sql .= " and raid.id = ?";
		}
		
		if( !isset($encounter_id) || $encounter_id === null ) {
			$sql .= " and encounter.id > ?";
			$encounter_id = 0;
		}
		else {
			$sql .= " and encounter.id = ?";
		}
		
		if( !isset($difficulty) || $difficulty === null ) {
			$sql .= " and dps_ranking.difficulty > ?";
			$difficulty = 0;
		}
		else {
			$sql .= " and dps_ranking.difficulty = ?";
		}
		$sql .= " group by dps_ranking.difficulty, dps_ranking.encounter_id order by raid.id asc, dps_ranking.difficulty asc, dps_ranking.encounter_id asc;";
		
		$stmt = $this->con->prepare($sql);
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->bind_param("sssiii", $character, $server, $region, $raid_id, $encounter_id, $difficulty) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_dps_performance($character, $server, $region, $raid_id, $difficulty) {
		$this->connect();
		$sql = "select max(1-((dps_ranking.rank-1)/dps_ranking.outOf)) as max, min(1-((dps_ranking.rank-1)/dps_ranking.outOf)) as min, avg(1-((dps_ranking.rank-1)/dps_ranking.outOf)) as avg, difficulty.name as difficulty, raid.name as raid, raid.id as raidId, 'bossdps' as metric from dps_ranking, difficulty, encounter, raid, spec, role where dps_ranking.difficulty = difficulty.id and dps_ranking.class = spec.class_id and dps_ranking.spec = spec.id and spec.role_id = role.id and role.metric = 'bossdps' and dps_ranking.encounter_id = encounter.id and encounter.raid_id = raid.id and dps_ranking.name = ? and dps_ranking.server = ? and dps_ranking.region = ? and dps_ranking.difficulty > 2";

		if( !isset($raid_id) || $raid_id === null ) {
			$sql .= " and raid.id > ?";
			$raid_id = 0;
		}
		else {
			$sql .= " and raid.id = ?";
		}
		
		if( !isset($difficulty) || $difficulty === null ) {
			$sql .= " and dps_ranking.difficulty > ?";
			$difficulty = 0;
		}
		else {
			$sql .= " and dps_ranking.difficulty = ?";
		}
		$sql .= " group by dps_ranking.difficulty, raid.id order by raid.id asc, dps_ranking.difficulty asc;";
		
		$stmt = $this->con->prepare($sql);
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->bind_param("sssii", $character, $server, $region, $raid_id, $difficulty) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_guild_dps_performance($raid_id, $difficulty) {
		$this->connect();
		
		$sql = "select temp.raid, temp.raidId, temp.name, temp.server, temp.region, temp.guild, temp.metric, max(temp.max) as max, min(temp.max) as min, avg(temp.max) as avg from (select max(1-((dps_ranking.rank-1)/dps_ranking.outOf)) as max, min(1-((dps_ranking.rank-1)/dps_ranking.outOf)) as min, avg(1-((dps_ranking.rank-1)/dps_ranking.outOf)) as avg, raid.name as raid, raid.id as raidId, 'bossdps' as metric, dps_ranking.guild as guild, dps_ranking.name as name, dps_ranking.server as server, dps_ranking.region as region from dps_ranking, difficulty, encounter, raid, spec, role, member where dps_ranking.difficulty = difficulty.id and dps_ranking.class = spec.class_id and dps_ranking.spec = spec.id and spec.role_id = role.id and role.metric = 'bossdps' and dps_ranking.encounter_id = encounter.id and encounter.raid_id = raid.id and dps_ranking.guild = 'Seelenwanderer' and dps_ranking.difficulty > 2 and dps_ranking.name = member.name and dps_ranking.server = member.server and dps_ranking.region = member.region";

		if( !isset($raid_id) || $raid_id === null ) {
			$sql .= " and raid.id > ?";
			$raid_id = 0;
		}
		else {
			$sql .= " and raid.id = ?";
		}
		
		if( !isset($difficulty) || $difficulty === null ) {
			$sql .= " and dps_ranking.difficulty > ?";
			$difficulty = 0;
		}
		else {
			$sql .= " and dps_ranking.difficulty = ?";
		}
		$sql .= " group by raid.id, dps_ranking.encounter_id, dps_ranking.name, dps_ranking.server, dps_ranking.region order by raid.id asc, avg asc) as temp group by raidId, name, server, region order by temp.raidId asc, avg asc;";
		
		$stmt = $this->con->prepare($sql);
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->bind_param("ii", $raid_id, $difficulty) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_encounter($id) {
		$this->connect();
		$stmt = $this->con->prepare("select name from encounter where id = ?;");
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->bind_param("i", $id) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			
			if(sizeof($rows) === 1)
				return $rows[0];
			return false;
		
		} catch(Exception $e) {
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function temp_rankings($rankings, $total, $duration1, $duration2, $itemLevel1, $itemLevel2, $size1, $size2) {
		$this->connect();
		$exc = null;
		$tableName = uniqid() . "_" . time(); 
		
		try {
		
			$this->con->autocommit(false);
			$this->con->begin_transaction();
			
			$this->con->query("create temporary table " . $tableName . " (id integer primary key auto_increment, total double, duration double, itemLevel integer, size integer);");
			$this->con->commit();
			$stmt = $this->con->prepare("insert into " . $tableName . " (total, duration, itemLevel, size) values (?, ?, ?, ?);");
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			foreach($rankings as $ranking) {
			
				$ranking = (array) $ranking;
			
				if( !$stmt->bind_param("ddii", $ranking['total'], $ranking['duration'], $ranking['itemLevel'], $ranking['size']) ) {
					throw new Exception($stmt->error, $stmt->errno);
				}
				
				if( !$stmt->execute() ) {
					throw new Exception($stmt->error, $stmt->errno);
				}
			
			}
			$this->con->commit();
			$stmt->close();
			
			$data = array();
			
			$stmt = $this->con->prepare("select count(*) as outOf from " . $tableName . " where duration >= ? and duration <= ? and itemLevel >= ? and itemLevel <= ? and size >= ? and size <= ?;");

			if( !$stmt->bind_param("ddiiii", $duration1, $duration2, $itemLevel1, $itemLevel2, $size1, $size2) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			if(sizeof($rows) === 1) {
				$data['outOf'] = $rows[0]['outOf'];
			}
			$stmt->close();
			
			$stmt = $this->con->prepare("select (1+count(*)) as rank from " . $tableName . " where total > ? and duration >= ? and duration <= ? and itemLevel >= ? and itemLevel <= ? and size >= ? and size <= ?;");
			
			if( !$stmt ) {
				throw new Exception($this->con->error, $this->con->errno);
			}

			if( !$stmt->bind_param("dddiiii", $total, $duration1, $duration2, $itemLevel1, $itemLevel2, $size1, $size2) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			if(sizeof($rows) === 1) {
				$data['rank'] = $rows[0]['rank'];
			}
			
			$this->con->query("drop temporary table " . $tableName . ";");
			$this->con->autocommit(true);
			
			return $data;
		
		} catch(Exception $e) {
			$this->con->rollback();
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		$this->con->query("drop temporary table " . $tableName . ";");
		$this->con->autocommit(true);
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_hps_ranking($character, $server, $region, $raid_id = -1, $encounter_id = -1, $difficulty = -1) {
		$this->connect();
		
		$sql = "select hps_ranking.guild as guild, (1-((hps_ranking.rank-1)/hps_ranking.outOf)) as rank, hps_ranking.reportID as reportID, hps_ranking.fightID as fightID, hps_ranking.itemLevel as itemLevel, hps_ranking.total as total, encounter.name as encounter, hps_ranking.encounter_id as encounter_id, difficulty.name as difficulty, hps_ranking.duration as duration, hps_ranking.startTime as time, raid.name as raid, raid.id as raidId, 'hps' as metric from hps_ranking, difficulty, encounter, raid, spec, role where hps_ranking.difficulty = difficulty.id and hps_ranking.class = spec.class_id and hps_ranking.spec = spec.id and spec.role_id = role.id and role.metric = 'hps' and hps_ranking.encounter_id = encounter.id and encounter.raid_id = raid.id and hps_ranking.name = ? and hps_ranking.server = ? and hps_ranking.region = ?";
		
		if( !isset($raid_id) || $raid_id === null ) {
			$sql .= " and raid.id > ?";
			$raid_id = 0;
		}
		else {
			$sql .= " and raid.id = ?";
		}
		
		if( !isset($encounter_id) || $encounter_id === null ) {
			$sql .= " and encounter.id > ?";
			$encounter_id = 0;
		}
		else {
			$sql .= " and encounter.id = ?";
		}
		
		if( !isset($difficulty) || $difficulty === null ) {
			$sql .= " and hps_ranking.difficulty > ?";
			$difficulty = 0;
		}
		else {
			$sql .= " and hps_ranking.difficulty = ?";
		}
		
		//$sql .= " order by raid.id asc, hps_ranking.encounter_id asc, hps_ranking.difficulty asc, hps_ranking.startTime asc;";
		$sql .= " order by hps_ranking.startTime asc;";
		$stmt = $this->con->prepare($sql);
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->bind_param("sssiii", $character, $server, $region, $raid_id, $encounter_id, $difficulty) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_hps_rank($character, $server, $region, $raid_id, $encounter_id, $difficulty) {
		$this->connect();
		$sql = "select max(1-((hps_ranking.rank-1)/hps_ranking.outOf)) as max, avg(1-((hps_ranking.rank-1)/hps_ranking.outOf)) as avg, min(1-((hps_ranking.rank-1)/hps_ranking.outOf)) as min, encounter.name as encounter, difficulty.name as difficulty, raid.name as raid, raid.id as raidId, 'hps' as metric from hps_ranking, difficulty, encounter, raid, spec, role where hps_ranking.difficulty = difficulty.id and hps_ranking.class = spec.class_id and hps_ranking.spec = spec.id and spec.role_id = role.id and role.metric = 'hps' and hps_ranking.encounter_id = encounter.id and encounter.raid_id = raid.id and hps_ranking.name = ? and hps_ranking.server = ? and hps_ranking.region = ? and hps_ranking.difficulty > 2";

		if( !isset($raid_id) || $raid_id === null ) {
			$sql .= " and raid.id > ?";
			$raid_id = 0;
		}
		else {
			$sql .= " and raid.id = ?";
		}
		
		if( !isset($encounter_id) || $encounter_id === null ) {
			$sql .= " and encounter.id > ?";
			$encounter_id = 0;
		}
		else {
			$sql .= " and encounter.id = ?";
		}
		
		if( !isset($difficulty) || $difficulty === null ) {
			$sql .= " and hps_ranking.difficulty > ?";
			$difficulty = 0;
		}
		else {
			$sql .= " and hps_ranking.difficulty = ?";
		}
		$sql .= " group by hps_ranking.difficulty, hps_ranking.encounter_id order by raid.id asc, hps_ranking.difficulty asc, hps_ranking.encounter_id asc;";
		
		$stmt = $this->con->prepare($sql);
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->bind_param("sssiii", $character, $server, $region, $raid_id, $encounter_id, $difficulty) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_hps_performance($character, $server, $region, $raid_id, $difficulty) {
		$this->connect();
		$sql = "select max(1-((hps_ranking.rank-1)/hps_ranking.outOf)) as max, min(1-((hps_ranking.rank-1)/hps_ranking.outOf)) as min, avg(1-((hps_ranking.rank-1)/hps_ranking.outOf)) as avg, difficulty.name as difficulty, raid.name as raid, raid.id as raidId, 'hps' as metric from hps_ranking, difficulty, encounter, raid, spec, role where hps_ranking.difficulty = difficulty.id and hps_ranking.class = spec.class_id and hps_ranking.spec = spec.id and spec.role_id = role.id and role.metric = 'hps' and hps_ranking.encounter_id = encounter.id and encounter.raid_id = raid.id and hps_ranking.name = ? and hps_ranking.server = ? and hps_ranking.region = ? and hps_ranking.difficulty > 2";

		if( !isset($raid_id) || $raid_id === null ) {
			$sql .= " and raid.id > ?";
			$raid_id = 0;
		}
		else {
			$sql .= " and raid.id = ?";
		}
		
		if( !isset($difficulty) || $difficulty === null ) {
			$sql .= " and hps_ranking.difficulty > ?";
			$difficulty = 0;
		}
		else {
			$sql .= " and hps_ranking.difficulty = ?";
		}
		$sql .= " group by hps_ranking.difficulty, raid.id order by raid.id asc, hps_ranking.difficulty asc;";
		
		$stmt = $this->con->prepare($sql);
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->bind_param("sssii", $character, $server, $region, $raid_id, $difficulty) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}

	public function get_guild_hps_performance($raid_id, $difficulty) {
		$this->connect();
		$sql = "select temp.raid, temp.raidId, temp.name, temp.server, temp.region, temp.guild, temp.metric, max(temp.max) as max, min(temp.max) as min, avg(temp.max) as avg from (select max(1-((hps_ranking.rank-1)/hps_ranking.outOf)) as max, min(1-((hps_ranking.rank-1)/hps_ranking.outOf)) as min, avg(1-((hps_ranking.rank-1)/hps_ranking.outOf)) as avg, raid.name as raid, raid.id as raidId, 'hps' as metric, hps_ranking.guild as guild, hps_ranking.name as name, hps_ranking.server as server, hps_ranking.region as region from hps_ranking, difficulty, encounter, raid, spec, role, member where hps_ranking.difficulty = difficulty.id and hps_ranking.class = spec.class_id and hps_ranking.spec = spec.id and spec.role_id = role.id and role.metric = 'hps' and hps_ranking.encounter_id = encounter.id and encounter.raid_id = raid.id and hps_ranking.difficulty > 2 and hps_ranking.name = member.name and hps_ranking.server = member.server and hps_ranking.region = member.region";

		if( !isset($raid_id) || $raid_id === null ) {
			$sql .= " and raid.id > ?";
			$raid_id = 0;
		}
		else {
			$sql .= " and raid.id = ?";
		}
		
		if( !isset($difficulty) || $difficulty === null ) {
			$sql .= " and hps_ranking.difficulty > ?";
			$difficulty = 0;
		}
		else {
			$sql .= " and hps_ranking.difficulty = ?";
		}
		$sql .= " group by raid.id, hps_ranking.encounter_id, hps_ranking.name, hps_ranking.server, hps_ranking.region order by raid.id asc, avg asc) as temp group by raidId, name, server, region order by temp.raidId asc, avg asc;";
		
		$stmt = $this->con->prepare($sql);
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception($this->con->error, $this->con->errno);
			}
			
			if( !$stmt->bind_param("ii", $raid_id, $difficulty) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function insert_member($character, $server, $region, $class) {
		$this->connect();
		$exc = null;
		
		try {
		
			$stmt = $this->con->prepare("INSERT INTO member(name, server, region, class, creation_date, update_date) VALUES (?, ?, ?, ?, now(), now()) ON DUPLICATE KEY UPDATE class=values(class), update_date=now();");

			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("sssi", $character, $server, $region, $class) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$stmt->close();
			return mysqli_insert_id($this->con);
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	
	}
	
	public function delete_member($character, $server, $region) {
		$this->connect();
		$exc = null;
		
		try {
		
			$stmt = $this->con->prepare("delete from member where name=? and server=? and region=?;");
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("sss", $character, $server, $region) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$stmt->close();
			return true;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	
	}
	
	public function get_members() {
		$this->connect();
		$stmt = $this->con->prepare("select member.name as name, member.server as server, member.region as region, class.name as class from member, class where member.class = class.id order by member.name;");
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}

			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function is_member($character, $server, $region) {
		$this->connect();
		$stmt = $this->con->prepare("select 1 from member where name=? and server=? and region=?;");
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("sss", $character, $server, $region) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}

			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$stmt->store_result();
			$result = $stmt->num_rows;
			$stmt->close();
			
			return $result > 0;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_member_info($character, $server, $region) {
		$this->connect();
		$stmt = $this->con->prepare("select member.name as name, member.server as server, member.region as region, class.name as class from member, class where member.class = class.id and member.name=? and member.server=? and member.region=?;");
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("sss", $character, $server, $region) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}

			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			
			if( sizeof($rows) === 1 )
				return $rows[0];
			return null;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_classes() {
		$this->connect();
		$stmt = $this->con->prepare("select * from class;");
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}

			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			
			return $rows;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_specs() {
		$this->connect();
		$stmt = $this->con->prepare("select spec.id as id, spec.name as name, class.name as class, class.id as class_id from spec, class where spec.class_id = class.id order by spec.class_id;");
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}

			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			
			return $rows;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_metric($class, $spec) {
		$this->connect();
		$stmt = $this->con->prepare("select role.metric as metric from spec, role where spec.role_id = role.id and spec.class_id=? and spec.id=?;");
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("ii", $class, $spec) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}

			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			
			if( sizeof($rows) === 1 )
				return $rows[0]['metric'];
			return null;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_characterinfo($character, $server, $region) {
		$this->connect();
		$stmt = $this->con->prepare("select class.name as class, role.name as role, spec.name as spec, role.metric as metric from member, class, spec, role where member.class = class.id and spec.class_id = class.id and spec.role_id = role.id and member.name=? and member.server=? and member.region=?;");
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("sss", $character, $server, $region) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}

			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			
			return $rows;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_guides($pending=null) {
		$this->connect();
		$stmt = $this->con->prepare("select * from guide" . (is_bool($pending) ? " where pending=?;" : ";"));
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( is_bool($pending) ) {
				if( !$stmt->bind_param("i", $pending) ) {
					throw new Exception($stmt->error, $stmt->errno);
				}
			}

			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			
			return $rows;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_guide($tag) {
		$this->connect();
		$stmt = $this->con->prepare("select * from guide where tag=?;");
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("s", $tag) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}

			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();

			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}

			if( count($rows) > 0 )
				return $rows[0];
			return false;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function insert_guide($name="", $author="", $path="") {
		$this->connect();
		$exc = null;
		
		try {
		
			$stmt = $this->con->prepare("INSERT INTO guide(tag, name, pending, author, path, creation_date, update_date) VALUES (?, ?, ?, ?, ?, now(), now()) ON DUPLICATE KEY UPDATE pending=values(pending), path=values(path), update_date=now();");

			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("ssiss", $tag, $name, $pending, $author, $path) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$tag = base64_encode("guide:" . $author . ":" . uniqid());
			$pending = true;
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$stmt->close();
			return $tag;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	
	}
	
	public function publish_guide($tag) {
		$this->connect();
		$exc = null;
		
		try {
			$stmt = $this->con->prepare("update guide set pending=?, update_date=now() where tag=?;");

			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("is", $pending, $tag) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$pending = false;
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$num = $stmt->affected_rows;
			$stmt->close();
			return $num > 0;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
		
	}
	
	public function unpublish_guide($tag) {
		$this->connect();
		$exc = null;
		
		try {
			$stmt = $this->con->prepare("update guide set pending=?, update_date=now() where tag=?;");

			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("is", $pending, $tag) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$pending = true;
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$num = $stmt->affected_rows;
			$stmt->close();
			return $num > 0;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
		
	}
	
	public function delete_guide($tag) {
		$this->connect();
		$exc = null;
		
		try {
		
			$stmt = $this->con->prepare("delete from guide where tag=?;");
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("s", $tag) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$stmt->close();
			return true;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	
	}
	
	public function get_challenges($raid_id=null) {
		$this->connect();	
		$stmt = $this->con->prepare( "select * from challenge, encounter, raid where challenge.encounter = encounter.id and encounter.raid = raid.id" . ( is_number($raid_id) ? " and raid.id = ?;" : ";" ) );
		$exc = null;

		try {
		
			if(!$stmt) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			if( is_number($raid_id) ) {
				if( !$stmt->bind_param("i", $raid_id) ) {
					throw new Exception($stmt->error, $stmt->errno);
				}
			}
			
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			return $rows;
		
		} catch(Exception $e) {
			$exc = new Exception($stmt->error, $stmt->errno);
		}
		
		try {
			$stmt->close();
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function insert_challenge($encounter=0, $difficulty=0, $class=0, $spec=0, $bracket=0, $E=0, $Ep=0, $s=0, $sp=0) {
		$this->connect();
		$exc = null;
		
		try {
		
			$stmt = $this->con->prepare("INSERT INTO challenge(encounter, difficulty, class, spec, bracket, E, Ep, s, sp, update_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, now()) ON DUPLICATE KEY UPDATE E=values(E), Ep=values(Ep), s=values(s), sp=values(sp), update_date=now();");

			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("iiiiidddd", $encounter, $difficulty, $class, $spec, $bracket, $E, $Ep, $s, $sp) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$stmt->close();
			return true;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	
	}
	
		public function get_videos($pending=null) {
		$this->connect();
		$stmt = $this->con->prepare("select * from video" . (is_bool($pending) ? " where pending=?;" : ";"));
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( is_bool($pending) ) {
				if( !$stmt->bind_param("i", $pending) ) {
					throw new Exception($stmt->error, $stmt->errno);
				}
			}

			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			
			return $rows;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function get_video($tag) {
		$this->connect();
		$stmt = $this->con->prepare("select * from video where tag=?;");
		$exc = null;
		
		try {
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("s", $tag) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}

			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$result = $stmt->get_result();
			$stmt->close();
			$rows = array();
			
			while($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
			
			if( sizeof($rows) === 1 )
				return $rows[0];
			return false;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	}
	
	public function insert_video($ytid="", $author="", $thumbnail="", $owner="", $title="") {
		$this->connect();
		$exc = null;
		
		try {
		
			$stmt = $this->con->prepare("INSERT INTO video(tag, ytid, pending, author, thumbnail, owner, title, creation_date, update_date) VALUES (?, ?, ?, ?, ?, ?, ?, now(), now()) ON DUPLICATE KEY UPDATE pending=values(pending), ytid=values(ytid), thumbnail=values(thumbnail), title=values(title), owner=values(owner), update_date=now();");

			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("ssissss", $tag, $ytid, $pending, $author, $thumbnail, $owner, $title) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$tag = base64_encode("video:" . $author . ":" . uniqid());
			$pending = true;
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$stmt->close();
			return $tag;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	
	}
	
	public function publish_video($tag) {
		$this->connect();
		$exc = null;
		
		try {
			$stmt = $this->con->prepare("update video set pending=?, update_date=now() where tag=?;");

			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("is", $pending, $tag) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$pending = false;
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$num = $stmt->affected_rows;
			$stmt->close();
			return $num > 0;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
		
	}
	
	public function unpublish_video($tag) {
		$this->connect();
		$exc = null;
		
		try {
			$stmt = $this->con->prepare("update video set pending=?, update_date=now() where tag=?;");

			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("is", $pending, $tag) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$pending = true;
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$num = $stmt->affected_rows;
			$stmt->close();
			return $num > 0;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
		
	}
	
	public function delete_video($tag) {
		$this->connect();
		$exc = null;
		
		try {
		
			$stmt = $this->con->prepare("delete from video where tag=?;");
		
			if(!$stmt) {
				throw new Exception("Invalid statement given!");
			}
			
			if( !$stmt->bind_param("s", $tag) ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
				
			if( !$stmt->execute() ) {
				throw new Exception($stmt->error, $stmt->errno);
			}
			
			$stmt->close();
			return true;
		
		} catch(Exception $e) {
			$exc = $e;
		}
		
		try {
			if($stmt) {
				$stmt->close();
			}
		} catch(Exception $e) {
		}
		
		if( $exc !== null ) {
			throw $exc;
		}
	
	}
}
?>