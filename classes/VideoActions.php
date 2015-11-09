<?php
	if (!defined('INDEX'))
		exit('No direct script access allowed');

	class VideoAction
	{
		private $mainSystem;
		private $mysqli;
		
		public function __construct($mainSystem)
		{
			$this->mainSystem = $mainSystem;
			$this->mysqli = $mainSystem->databaseSystem->_mysqli;
		}

        public function getActiveVideosCount()
        {
            $result = $this->mysqli->query(
                "SELECT COUNT(id) as total FROM videos WHERE user_id='" . $this->mainSystem->user['id'] . "'"
            );

            $result = $result->fetch_assoc();

            return $result['total'];
        }

        public function getDisabledVideosCount()
        {
            $result = $this->mysqli->query(
                "SELECT COUNT(id) as total FROM inactive WHERE user_id='" . $this->mainSystem->user['id'] . "'"
            );

            $result = $result->fetch_assoc();

            return $result['total'];
        }
		
		public function getActiveVideos()
		{
			return $this->fetch_all_assoc(
				"SELECT * FROM videos WHERE user_id='" . $this->mainSystem->user['id'] . "' ORDER BY credits DESC"
			);
		}
		
		public function getDisabledVideos()
		{
			return $this->fetch_all_assoc(
				"SELECT * FROM inactive WHERE user_id='" . $this->mainSystem->user['id'] . "' ORDER BY credits DESC"
			);
		}
		
		public function getActiveVideo($id)
		{
			$result = $this->mysqli->query(
				"SELECT * FROM videos WHERE user_id='" . $this->mainSystem->user['id'] . "' AND id='" . $id . "'"
			);
			
			$res = $result->fetch_assoc();
			
			$res['vid_id'] = str_replace("\\", "", $res['vid_id']);
			
			return $res;
		}
		
		public function getDisabledVideo($id)
		{
			$result = $this->mysqli->query(
				"SELECT * FROM videos WHERE user_id='" . $this->mainSystem->user['id'] . "' AND id='" . $id . "'"
			);
			
			return $result->fetch_assoc();
		}
		
		public function updateUserVideo($video_id, $action, $value = 0)
		{
			if ($action == "credits")
			{
				$vid_ac = $this->mysqli->query("SELECT id FROM videos WHERE id = '" . $video_id . "'")->fetch_assoc();
				$vid_in = $this->mysqli->query("SELECT id FROM inactive WHERE id = '" . $video_id . "'")->fetch_assoc();
				
				$value = intval($value);
				
				if ($vid_ac != null || $vid_in != null)
				{
					$stmt = $this->mysqli->prepare("UPDATE " . ($vid_ac != null ? "videos" : "inactive") . " SET credits = ? WHERE id = ?");
					$stmt->bind_param("ii", $value, $video_id);
					$stmt->execute();
					$stmt->close();
				}
			} else if ($action == "Disable" && $this->mysqli->query("SELECT id FROM videos WHERE id = '" . $video_id . "'")->fetch_assoc() != null)
			{
				//Put the video into the inactive table
				$stmt = $this->mysqli->prepare("INSERT INTO inactive SELECT * FROM videos WHERE id = ?");
				$stmt->bind_param("i", $video_id);
				$stmt->execute();
				$stmt->close();

				//Delete the video from the videos table
				$stmt = $this->mysqli->prepare("DELETE FROM videos WHERE id = ?");
				$stmt->bind_param("i", $video_id);
				$stmt->execute();
				$stmt->close();
			} else if ($action == "Enable" && $this->mysqli->query("SELECT id FROM inactive WHERE id = '" . $video_id . "'")->fetch_assoc() != null)
			{
				//Put the video into the videos table
				$stmt = $this->mysqli->prepare("INSERT INTO videos SELECT * FROM inactive WHERE id = ?");
				$stmt->bind_param("i", $video_id);
				$stmt->execute();
				$stmt->close();

				//Delete the video from the inactive table
				$stmt = $this->mysqli->prepare("DELETE FROM inactive WHERE id = ?");
				$stmt->bind_param("i", $video_id);
				$stmt->execute();
				$stmt->close();
			} else if ($action == "Delete")
			{
				if ($this->mysqli->query("SELECT id FROM videos WHERE id = '" . $video_id . "'")->fetch_assoc() != null)
				{
					//Delete the video from the inactive table
					$stmt = $this->mysqli->prepare("DELETE FROM videos WHERE id = ?");
					$stmt->bind_param("i", $video_id);
					$stmt->execute();
					$stmt->close();
				} else if ($this->mysqli->query("SELECT id FROM inactive WHERE id = '" . $video_id . "'")->fetch_assoc() != null)
				{
					//Delete the video from the inactive table
					$stmt = $this->mysqli->prepare("DELETE FROM inactive WHERE id = ?");
					$stmt->bind_param("i", $video_id);
					$stmt->execute();
					$stmt->close();
				}
			}
		}
		
		public function getUserVideo($video_id)
		{
			if ($this->mainSystem->loginManager->isUserAdmin())
			{
				$return = array();
				
				if ($this->mysqli->query("SELECT * FROM videos WHERE id='" . $video_id . "'")->fetch_assoc() != null)
				{
					$query = $this->mysqli->query("SELECT * FROM videos WHERE id='" . $video_id . "'");
					$return = $query->fetch_assoc();
					
					$return['type'] = "active";
				} else if ($this->mysqli->query("SELECT * FROM inactive WHERE id='" . $video_id . "'")->fetch_assoc() != null)
				{
					$query = $this->mysqli->query("SELECT * FROM inactive WHERE id='" . $video_id . "'");
					$return = $query->fetch_assoc();
					
					$return['type'] = "inactive";
				}
				
				return $return;
			}
			
			return array();
		}
		
		public function getUserVideos($user_id)
		{
			if ($this->mainSystem->loginManager->isUserAdmin())
			{
				$videos = $this->fetch_all_assoc(
					"SELECT * FROM videos WHERE user_id='" . $user_id . "' ORDER BY credits DESC"
				);
				
				$inactives = $this->fetch_all_assoc(
					"SELECT * FROM inactive WHERE user_id='" . $user_id . "' ORDER BY credits DESC"
				);
				
				foreach ($videos as $key => $value)
				{
					$value["type"] = "active";
					$videos[$key] = $value;
				}
				
				foreach ($inactives as $key => $value)
				{
					$value["type"] = "inactive";
					$inactives[$key] = $value;
				}
				
				return array_merge($videos, $inactives);
			}
			
			return array();
		}
		
		public function getAllVideos()
		{
			if ($this->mainSystem->loginManager->isUserAdmin())
			{
				$videos = $this->fetch_all_assoc(
					"SELECT * FROM videos ORDER BY credits DESC"
				);
				
				$inactives = $this->fetch_all_assoc(
					"SELECT * FROM inactive ORDER BY credits DESC"
				);
				
				foreach ($videos as $key => $value)
				{
					$value["type"] = "active";
					$videos[$key] = $value;
				}
				
				foreach ($inactives as $key => $value)
				{
					$value["type"] = "inactive";
					$inactives[$key] = $value;
				}
				
				return array_merge($videos, $inactives);
			}
			
			return array();
		}
		
		public function fetch_all_assoc($query)
		{
			$result = $this->mysqli->query($query);
			$assoc = array();
			
            $init = 0;

			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$assoc[$init] = $row;

                $init = $init + 1;
			}
			
			$result->close();
			
			return $assoc;
		}
	}
?>