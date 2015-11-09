<?php
date_default_timezone_set("America/Chicago");

class VideoInterface
{
	private $_vars = array();

    private $_videos = array();
    private $_user = null;

    private $_didLike = 0;
    private $_didSub = 0;

	private $_username = "";
	private $_password = "";
	
    private $_userId = 0;
    private $_lastVid = 0;

    private $_mysqli = null;
    private $_isFirstStart = false;

    public function __construct($vars = array())
    {
		$this->_vars = $vars;
		
		try {
			throw new Exception("Please download new viewer, Thanks.");
			
			//Check user agent
			if ($_SERVER['HTTP_USER_AGENT'] != "Mozilla/4.0 (GrabViews; .NET base)")
				throw new Exception("Bad viewing state");
			
			//Checks if action is specified in the GET args
			if (!isset($vars['action']))
				throw new Exception("Specify the action");
			
			if ($vars['action'] == "video")
			{
				//Checks if username is specified in the GET args
				if (!isset($vars['username']))
					throw new Exception("Specify the username");
				
				//Checks if password is specified in the GET args
				if (!isset($vars['password']))
					throw new Exception("Specify the password");

				//Checks if version is specified in the GET args
				if (!isset($vars['version']))
					throw new Exception("Specify the version");
					
				$xml = simplexml_load_file("../Downloads/update.xml");
				$ver = $xml->Version;
				
				$version1 = explode(".", $vars['version']);
				$version2 = explode(".", $ver);

				//Checks the version in the GET args
				if ($vars['version'] != $ver)
					if (intval($version1[0]) * 10 + intval($version1[1]) < intval($version2[0]) * 10 + intval($version2[1]))
						throw new Exception("Outdated version");

				//Checks if user_id and last_vid is specified in the GET args
				if (!isset($vars['first_start']))
					throw new Exception("Specify the first_start");

				//Checks if user_id and last_vid are numeric
				if (!is_numeric($vars['first_start']))
					throw new Exception("first_start has to be a number");

				//Checks if did_like is numeric
				if (isset($vars['did_like']) && !is_numeric($vars['did_like']))
					throw new Exception("did_like has to be a number");

				//Checks if did_sub is numeric
				if (isset($vars['did_sub']) && !is_numeric($vars['did_sub']))
					throw new Exception("did_sub has to be a number");

				//Set the _username, _password, _isFirstStart, _didLike and _didSub
				$this->_username = $vars['username'];
				$this->_password = $vars['password'];
				$this->_isFirstStart = intval($vars['first_start']) == 1 ? true : false;
				$this->_didLike = isset($vars['did_sub']) ? intval($vars['did_like']) : 0;
				$this->_didSub = isset($vars['did_sub']) ? intval($vars['did_sub']) : 0;
				
				//Connect to the database
				$this->conDB();

				//Obtain the video array and user
				$this->obtainUser();
				$this->obtainVideos();

				//Echo the handled request
				echo $this->handleRequest();

				//Disconnect from the database
				$this->disDB();
			} else if ($vars['action'] == "version_check")
			{
				//Checks if version is specified in the GET args
				if (!isset($vars['version']))
					throw new Exception("Specify the version");
				
				$xml = simplexml_load_file("../Downloads/update.xml");
				$ver = $xml->Version;
				$fil = $xml->FileName;
				
				$version1 = explode(".", $vars['version']);
				$version2 = explode(".", $ver);
				
				//Checks the version in the GET args
				if ($vars['version'] != $ver)
					if (intval($version1[0]) * 10 + intval($version1[1]) < intval($version2[0]) * 10 + intval($version2[1]))
					{
						throw new Exception($ver . "|" . $fil);
					} else
					{
						echo "Good Version";
					}
				else
				{
					echo "Good Version";
				}
			} else if ($vars['action'] == "login_check")
			{
				//Checks if version is specified in the GET args
				if (!isset($vars['username']))
					throw new Exception("Specify the username");
				
				//Checks if version is specified in the GET args
				if (!isset($vars['password']))
					throw new Exception("Specify the password");
				
				//Connect to the database
				$this->conDB();
				
				//Get the credits, password, and salt if user exists
				$stmt = $this->_mysqli->prepare("SELECT credits, password, salt FROM users WHERE username = ? OR email = ?");
				$stmt->bind_param("ss", $vars['username'], $vars['username']);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($credits, $password, $salt);
				$stmt->fetch();
				
				if ($stmt->num_rows == 1)
				{
					$stmt->close();
					
					$pass_hash = hash('md5', $vars['password'] . hash('md5', $salt));
					
					//Checks submitted password to one in database
					if ($pass_hash == $password)
					{
						echo "Good|" . $credits;
					} else
					{
						throw new Exception("Wrong username, email or password");
					}
				} else
				{
					$stmt->close();
					
					throw new Exception("Wrong username, email or password");
				}
				
				//Disconnect from the database
				$this->disDB();
			} else
			{
				throw new Exception("Specify the action");
			}
		} catch (Exception $e) {
			echo "Error|" . $e->getMessage();
			return;
		}
    }
	
	private function getLoginString()
	{
		return hash('md5', $this->_username . $this->_password);
	}

    //Connects to the database
    private function conDB()
    {
        $this->_mysqli = new mysqli("localhost", "chiller", "Thisischill@19", "grabviews");
    }

    //Disconnects the database
    private function disDB()
    {
        $this->_mysqli->close();
    }

    //Obtains array of videos from database
    private function obtainVideos()
    {
        if ($stmt = $this->_mysqli->prepare("SELECT id, vid_id, vid_title, credits, `length`, `like`, likes, like_limit, subscribe, subscribes, sub_limit FROM videos WHERE user_id != ?"))
        {
            $stmt->bind_param("i", $this->_userId);
            $stmt->execute();

            $vid_args = array();
            $stmt->bind_result($vid_args['id'], $vid_args['vid_id'], $vid_args['vid_title'], $vid_args['credits'], $vid_args['length'], $vid_args['like'], $vid_args['likes'], $vid_args['like_limit'], $vid_args['subscribe'], $vid_args['subscribes'], $vid_args['sub_limit']);

            $i = 0;
            while ($stmt->fetch())
            {
				$this->_videos[$i] = $vid_args;

				$i += 1;

                $vid_args = array();
                $stmt->bind_result($vid_args['id'], $vid_args['vid_id'], $vid_args['vid_title'], $vid_args['credits'], $vid_args['length'], $vid_args['like'], $vid_args['likes'], $vid_args['like_limit'], $vid_args['subscribe'], $vid_args['subscribes'], $vid_args['sub_limit']);
            }

            $stmt->close();
        } else {
            throw new Exception("MySQL error: " . $this->_mysqli->error);
        }
    }

    //Obtains user from database
    private function obtainUser()
    {
        $stmt = $this->_mysqli->prepare("SELECT id, password, salt, credits, ip, last_vid_time, curr_vid_id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $this->_username, $this->_username);
        $stmt->execute();
		$stmt->store_result();

		if ($stmt->num_rows == 1)
		{
			$this->_user = array();
			$stmt->bind_result($this->_user['id'], $password, $salt, $this->_user['credits'], $this->_user['ip'], $this->_user['last_vid_time'], $this->_user['curr_vid_id']);

			$stmt->fetch();
			$stmt->close();
			
			if ($this->_user['ip'] != $_SERVER['REMOTE_ADDR'])
				throw new Exception("Try logging in to GrabViews.com and try again");
			
			$pass = hash('md5', $this->_password . hash('md5', $salt));
			
			if ($pass == $password)
			{
				$this->_lastVid = $this->_user['curr_vid_id'];
				$this->_userId = $this->_user['id'];
			} else
				throw new Exception("Wrong username, email or password");
		} else
		{
			throw new Exception("Wrong username, email or password");
		}
    }

    //Handles video request
    private function handleRequest()
    {
        if (count($this->_videos) == 0)
            throw new Exception("We do not have any videos for you to view");

        if ($this->_lastVid == 0)
        {
            $oldVideo = null;
            $video = $this->_videos[0];
        } else
        {
            //Goes through the videos and gets the next one
            for ($i = 0; $i < count($this->_videos); $i++)
            {
                if ($this->_videos[$i]['id'] == $this->_lastVid)
                {
                    $oldVideo = $this->_videos[$i];

                    if (isset($this->_videos[$i + 1]))
                        $video = $this->_videos[$i + 1];
                    else
                        $video = $this->_videos[0];

                    break;
                }
            }
        }

        if ($oldVideo != null)
        {
            //Gets the videos vars
            $oldVideo = $this->getVideoVars($oldVideo);
            $video = $this->getVideoVars($video);

            //Checks if the user is actually viewing the videos
            if ($this->_user['last_vid_time'] != 0 && time() - $this->_user['last_vid_time'] < $oldVideo['length'])
                throw new Exception("Viewing too fast:" . (int) ($oldVideo['length'] - (time() - $this->_user['last_vid_time'])));

            //Set the credit vars
            $videoCredits = $this->getVideoCredits($oldVideo);

            //Set the video vars
            $lastVidTime = time();
            $currVidId = $video['id'];

            //Update the user in the database
            $this->updateUser($this->_isFirstStart ? 0 : $videoCredits, $lastVidTime, $currVidId);

            //Update the video in the database
            if (!$this->_isFirstStart)
                $this->updateVideo($videoCredits, $oldVideo);
        } else
        {
            //Gets the videos vars
            $video = $this->getVideoVars($video);

            //Set the video vars
            $lastVidTime = time();
            $currVidId = $video['id'];

            //Update the user in the database
            $this->updateUser(0, $lastVidTime, $currVidId);
        }

        //Returns the next video
        return $this->encode("Good|" . $video['id'] . "|" . $video['vid_id'] . "|" . $video['vid_title'] . "|" . $video['length'] . "|" . ($video['like'] == 1 ? "true" : "false") . "|" . ($video['subscribe'] == 1 ? "true" : "false") . "|" . $this->_user['credits'], "login_string=" . $this->getLoginString());
    }

    //Gets the videos credits
    private function getVideoCredits($video)
    {
        $returnCredits = 0;

        switch ($video['length'])
        {
            case 30:
                $returnCredits = 1;
                break;
            case 60:
                $returnCredits = 2;
                break;
            case 90:
                $returnCredits = 3;
                break;
            case 120:
                $returnCredits = 4;
                break;
        }

        if ($video['like'] == 1 && $this->_didLike == 1) $returnCredits += 1;
        if ($video['subscribe'] == 1 && $this->_didSub == 1) $returnCredits += 1;

        return $returnCredits;
    }

    //Updates the user in the database
    private function updateUser($credits, $last_vid_time, $curr_vid_id)
    {
        $stmt = $this->_mysqli->prepare("UPDATE users SET credits = credits + ?,last_vid_time = ?,curr_vid_id = ? WHERE id = ?");
        $stmt->bind_param("iisi", $credits, $last_vid_time, $curr_vid_id, $this->_userId);
        $stmt->execute();
        $stmt->close();
		
		$stmt = $this->_mysqli->prepare("SELECT credits FROM users WHERE id = ?");
        $stmt->bind_param("i", $this->_userId);
        $stmt->execute();
		$stmt->bind_result($this->_user['credits']);
		$stmt->fetch();
        $stmt->close();
    }

    //Updates the video in the database
    private function updateVideo($credits, $video)
    {
        $stmt = $this->_mysqli->prepare("SELECT `date` FROM statistics_credits WHERE user_id = ? AND `date` = CURDATE()");
        $stmt->bind_param("i", $this->_userId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows != 0)
        {
            $stmt->close();

            $stmt = $this->_mysqli->prepare("UPDATE statistics_credits SET credits = credits + ? WHERE user_id = ? AND `date` = CURDATE()");
            $stmt->bind_param("ii", $credits, $this->_userId);
            $stmt->execute();
        } else
        {
            $stmt->close();

            $stmt = $this->_mysqli->prepare("INSERT INTO statistics_credits (user_id, `date`, credits) VALUES (?, CURDATE(), ?)");
            $stmt->bind_param("ii", $this->_userId, $credits);
            $stmt->execute();
        }

        $test_credits = $video['credits'] - $credits;

        $like = $video['like'] == 1 && $this->_didLike == 1 ? 1 : 0;
        $subscribe = $video['subscribe'] == 1 && $this->_didSub == 1 ? 1 : 0;
		
        $stmt = $this->_mysqli->prepare("SELECT user_id FROM videos WHERE id = ?");
        $stmt->bind_param("i", $video['id']);
        $stmt->execute();
        $stmt->bind_result($userId);
        $stmt->fetch();
        $stmt->close();

        if ($test_credits <= 0)
        {
            //Update the video in the database
            $stmt = $this->_mysqli->prepare("UPDATE videos SET views = views + 1, credits = 0, likes = likes + ?, subscribes = subscribes + ? WHERE id = ?");
            $stmt->bind_param("iii", $like, $subscribe, $video['id']);
            $stmt->execute();
            $stmt->close();

            //Put the video into the inactive table
            $stmt = $this->_mysqli->prepare("INSERT INTO inactive SELECT * FROM videos WHERE id = ?");
            $stmt->bind_param("i", $video['id']);
            $stmt->execute();
            $stmt->close();

            //Delete the video from the videos table
            $stmt = $this->_mysqli->prepare("DELETE FROM videos WHERE id = ?");
            $stmt->bind_param("i", $video['id']);
            $stmt->execute();
            $stmt->close();
        } else
        {
            //Update the video in the database
            $stmt = $this->_mysqli->prepare("UPDATE videos SET views = views + 1, credits = credits - ?, likes = likes + ?, subscribes = subscribes + ? WHERE id = ?");
            $stmt->bind_param("iiii", $credits, $like, $subscribe, $video['id']);
            $stmt->execute();
            $stmt->close();
        }

        $stmt = $this->_mysqli->prepare("SELECT `date` FROM statistics_views WHERE user_id = ? AND `date` = CURDATE()");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows != 0)
        {
            $stmt->close();

            $stmt = $this->_mysqli->prepare("UPDATE statistics_views SET views = views + 1 WHERE user_id = ? AND `date` = CURDATE()");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
        } else
        {
            $stmt->close();

            $stmt = $this->_mysqli->prepare("INSERT INTO statistics_views (user_id, `date`, views) VALUES (?, CURDATE(), 1)");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
        }
    }

    //Orgnizes the video vars
    private function getVideoVars($video)
    {
        //Checks if the video is past the limits
        if ($video['likes'] >= $video['like_limit'] && $video['like_limit'] != 0)
            $video['like'] = "0";
        if ($video['subscribes'] >= $video['sub_limit'] && $video['sub_limit'] != 0)
            $video['subscribe'] = "0";

        //Returns the organized vars
        return $video;
    }

    private function encode($string, $key)
    {
        $key = sha1($key);

        $strLen = strlen($string);
        $keyLen = strlen($key);

        $hash = "";
        $j = 0;

        for ($i = 0; $i < $strLen; $i++) {
            $ordStr = ord(substr($string, $i, 1));
            if ($j == $keyLen) { $j = 0; }
            $ordKey = ord(substr($key, $j, 1));
            $j++;
            $hash .= strrev(base_convert(dechex($ordStr + $ordKey), 16, 36));
        }
        return $hash;
    }

    private function decode($string, $key)
	{
        $key = sha1($key);

        $strLen = strlen($string);
        $keyLen = strlen($key);

        $hash = "";
        $j = 0;

        for ($i = 0; $i < $strLen; $i += 2)
		{
            $ordStr = hexdec(base_convert(strrev(substr($string, $i, 2)), 36, 16));
            if ($j == $keyLen) { $j = 0; }
            $ordKey = ord(substr($key, $j, 1));
            $j++;
            $hash .= chr($ordStr - $ordKey);
        }
        return $hash;
    }
}

new VideoInterface($_GET);