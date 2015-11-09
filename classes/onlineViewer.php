<?php
	class OnlineViewer
	{
		private $_user   = null;
		private $_mysqli = null;
		private $_method = null;
		
		private $_now       = 0;
		private $_credits   = 0;
		private $_prev_time = 0;
		
		private $_videos     = array();
		private $_curr_video = array();
		private $_prev_video = 0;
		
		public function __construct($mainSystem)
		{
			if (!isset($_POST['a']))
			{
				if (isset($_GET['a']))
				{
					$_POST['a'] = $_GET['a'];
				} else
				{
					exit();
				}
			}
			
			//$this->_user   = $mainSystem->user;
			$this->_mysqli = $mainSystem->databaseSystem->_mysqli;
			$this->_method = $_POST['a'] == "to" ? "auto" : ($_POST['a'] == "er" ? "power" : "");
			
			if (isset($_GET['d']) || isset($_POST['d']))
			{
				$userId = isset($_GET['d']) ? $_GET['d'] : $_POST['d'];
				
				$this->_user = $mainSystem->loginManager->getUserData($userId);
				
				$this->_prev_time  = $this->_user['last_vid_time'];
				$this->_prev_video = $this->_user['curr_vid_id'];
			}
			
			$this->_now = time();
		}
		
		private function getUser($ip)
		{
			if ($query = $this->_mysqli->query("SELECT * FROM viewers WHERE ip_address = '" . $this->_mysqli->real_escape_string($ip) . "'"))
			{
				$result = $query->fetch_assoc();
				
				return $result;
			}

			return null;
		}
		
		public function getViewer()
		{
			if ($this->_method == "auto" || $this->_method == "power")
			{
				if (!$this->getVideos())
					return json_encode(array("a" => "n", "Message" => "There are no videos to be watched"));
				
				if (!$this->getPrevAndCurrVideos())
					return json_encode(array("a" => "n", "Message" => "There are no videos to be watched"));
				
				return $this->getReturnString();
			} else
				return json_encode(array("a" => "n", "Message" => "No viewing method specified"));
		}
		
		private function getVideos()
		{
			$stmt = $this->_mysqli->prepare("SELECT id, vid_id, views, credits, length, `like`, likes, like_limit, subscribe, subscribes, sub_limit FROM videos WHERE user_id != ?");
			$stmt->bind_param("i", $this->_user['id']);
			$stmt->execute();
			
			$video_args = array();
			$stmt->bind_result($video_args['id'], $video_args['vid_id'], $video_args['views'], $video_args['credits'], $video_args['length'], $video_args['like'], $video_args['likes'], $video_args['like_limit'], $video_args['subscribe'], $video_args['subscribes'], $video_args['sub_limit']);
			
			$vid = 0;
			while ($stmt->fetch())
			{
				$this->_videos[$vid] = $video_args;
				
				$vid++;
				
				$video_args = array();
				$stmt->bind_result($video_args['id'], $video_args['vid_id'], $video_args['views'], $video_args['credits'], $video_args['length'], $video_args['like'], $video_args['likes'], $video_args['like_limit'], $video_args['subscribe'], $video_args['subscribes'], $video_args['sub_limit']);
			}
			
			$stmt->close();
			
			return true;
		}
		
		private function getPrevAndCurrVideos()
		{
			if ($this->_prev_video != 0)
			{
				for ($vid = 0; $vid < count($this->_videos); $vid++)
				{
					if ($this->_videos[$vid]['id'] == $this->_prev_video)
					{
						if (isset($this->_videos[$vid + 1]))
						{
							$this->_curr_video = $this->_videos[$vid + 1];
						} else if (isset($this->_videos[0]))
						{
							$this->_curr_video = $this->_videos[0];
						} else
							return false;
					}
				}
			} else
			{
				if (isset($this->_videos[0]))
				{
					$this->_curr_video = $this->_videos[0];
				} else
					return false;
			}
			
			return true;
		}
		
		public function updatePreviousVideo()
		{
			$this->getVideos();
			$this->getPrevAndCurrVideos();
			
			if (isset($_POST['a']) && isset($_POST['d']) && isset($_POST['x']) && $_POST['x'] == "ng" && $this->_curr_video != null)
			{
				if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], "youtube.com/watch?v=" . $this->_curr_video['vid_id']) > -1)
				{
					if ($this->isWithinTime())
					{
						$this->getCredits();
						
						$this->updateDatabase();
						
						$this->updateCreditsStats();
						$this->updateViewsStats();
					}
				}
			}
			
			$this->updateUser();
		}
		
		private function isWithinTime()
		{
			return $this->_now > $this->_prev_time + $this->_curr_video['length'] && $this->_now <= $this->_prev_time + $this->_curr_video['length'] + 10;
		}
		
		private function getCredits()
		{
			$credits = floor($this->_curr_video['length'] / 30);
			
			/*
			switch ($this->_prev_video['length'])
			{
				case 30:
					$credits = 1;
					break;
				case 60:
					$credits = 2;
					break;
				case 90:
					$credits = 3;
					break;
				case 120:
					$credits = 4;
					break;
			}
			*/
			
			if ($this->_method == "power")
			{
				if ($this->_curr_video['like'] == "1" && ($this->_curr_video['likes'] < $this->_curr_video['like_limit'] || $this->_curr_video['like_limit'] == 0) && isset($_POST['b']) && $_POST['b'] == "true")
					$credits += 1;
				else
					$this->_curr_video['like'] = "0";
				
				if ($this->_curr_video['subscribe'] == "1" && ($this->_curr_video['subscribes'] < $this->_curr_video['sub_limit'] || $this->_curr_video['sub_limit'] == 0) && isset($_POST['c']) && $_POST['c'] == "true")
					$credits += 1;
				else
					$this->_curr_video['subscribe'] = "0";
			} else
			{
				$this->_curr_video['like'] = "0";
				$this->_curr_video['subscribe'] = "0";
			}
			
			$this->_credits = $credits;
		}
		
		private function updateUser()
		{
			$stmt = $this->_mysqli->prepare("UPDATE users SET credits = credits+?, last_vid_time = ?, curr_vid_id = ? WHERE id = ?");
			$stmt->bind_param("iisi", $this->_credits, $this->_now, $this->_curr_video['id'], $this->_user['id']);
			$stmt->execute();
			$stmt->close();
		}
		
		private function updateDatabase()
		{
			if ($this->_curr_video['credits'] - $this->_credits <= 0)
			{
				$stmt = $this->_mysqli->prepare("UPDATE videos SET views = views+1, credits = 0, likes = likes+?, subscribes = subscribes+? WHERE id = ?");
				$stmt->bind_param("iii", $this->_curr_video['like'], $this->_curr_video['subscribe'], $this->_curr_video['id']);
				$stmt->execute();
				$stmt->close();
				
				$stmt = $this->_mysqli->prepare("INSERT INTO inactive SELECT * FROM videos WHERE id = ?");
				$stmt->bind_param("i", $this->_curr_video['id']);
				$stmt->execute();
				$stmt->close();
				
				$stmt = $this->_mysqli->prepare("DELETE FROM videos WHERE id = ?");
				$stmt->bind_param("i", $this->_curr_video['id']);
				$stmt->execute();
				$stmt->close();
			} else
			{
				$stmt = $this->_mysqli->prepare("UPDATE videos SET views = views+1, credits = credits-?, likes = likes+?, subscribes = subscribes+? WHERE id = ?");
				$stmt->bind_param("iiii", $this->_credits, $this->_curr_video['like'], $this->_curr_video['subscribe'], $this->_curr_video['id']);
				$stmt->execute();
				$stmt->close();
			}
		}
		
		private function updateCreditsStats()
		{
			$stmt = $this->_mysqli->prepare("SELECT `date` FROM statistics_credits WHERE user_id = ? AND `date` = CURDATE()");
			$stmt->bind_param("i", $this->_user['id']);
			$stmt->execute();
			$stmt->store_result();

			if ($stmt->num_rows != 0)
			{
				$stmt->close();

				$stmt = $this->_mysqli->prepare("UPDATE statistics_credits SET credits = credits + ? WHERE user_id = ? AND `date` = CURDATE()");
				$stmt->bind_param("ii", $this->_credits, $this->_user['id']);
				$stmt->execute();
				$stmt->close();
			} else
			{
				$stmt->close();

				$stmt = $this->_mysqli->prepare("INSERT INTO statistics_credits (user_id, `date`, credits) VALUES (?, CURDATE(), ?)");
				$stmt->bind_param("ii", $this->_user['id'], $this->_credits);
				$stmt->execute();
				$stmt->close();
			}
		}
		
		private function updateViewsStats()
		{
			$stmt = $this->_mysqli->prepare("SELECT `date` FROM statistics_views WHERE user_id = ? AND `date` = CURDATE()");
			$stmt->bind_param("i", $this->_curr_video['user_id']);
			$stmt->execute();
			$stmt->store_result();

			if ($stmt->num_rows != 0)
			{
				$stmt->close();

				$stmt = $this->_mysqli->prepare("UPDATE statistics_views SET views = views + 1 WHERE user_id = ? AND `date` = CURDATE()");
				$stmt->bind_param("i", $this->_curr_video['user_id']);
				$stmt->execute();
				$stmt->close();
			} else
			{
				$stmt->close();

				$stmt = $this->_mysqli->prepare("INSERT INTO statistics_views (user_id, `date`, views) VALUES (?, CURDATE(), 1)");
				$stmt->bind_param("i", $this->_curr_video['user_id']);
				$stmt->execute();
				$stmt->close();
			}
		}
		
		private function getReturnString()
		{
			$status    = "g";
			$method    = $this->_method == "auto" ? "to" : ($this->_method == "power" ? "er" : "");
			$vid_id    = $this->_curr_video['vid_id'];
			$length    = $this->_curr_video['length'];
			$like      = $this->_curr_video['like'] == 1 ? "true" : "false";
			$subscribe = $this->_curr_video['subscribe'] == 1 ? "true" : "false";
			
			$array = array(
				"a" => $status,
				"b" => $method,
				"c" => $vid_id,
				"d" => $length
			);
			
			if ($this->_method == "power")
			{
				$array['e'] = $like;
				$array['f'] = $subscribe;
			} else
			{
				$array['e'] = "false";
				$array['f'] = "false";
			}
			
			return json_encode($array);
		}
	}
?>