<?php

    include("config.php");

    // Database
    // =========================
    //$connection = mysqli_connect("localhost","root","","test");   

    // SET
    $_GET = array_merge($_GET, $_POST);

    /*
    // data - get class
    {
        $dataClass = file_get_contents("../data/class.json");
        $dataClass = json_decode($dataClass);
    }

    // data - package
    {
        $dataPackageList = array();
        $dataPackageListId = array();
        $stmt = $connection->prepare("SELECT * FROM package_tbl");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($obj = $result->fetch_object()) 
        {
            $dataPackageList[] = $obj;
            $dataPackageListId[$obj->id] = $obj;
        }
    }
    */


    // Mode
    // =========================
    if (!isset($_GET['mode']))
    {
        JSONSet2("error", "API Failed", "no method parameter");
    }

    // user - login
    if ($_GET['mode'] == "userlogin")
    {
        //
        $userData = new stdClass();
        $userTokenNew = GUID();

        // check
        {
            if (!isset($_GET['uuname']) || !ValidText($_GET['uuname'], 4, 15))
            {
                JSONSet2("error", "Login Failed", "Invalid username/password");
            }

            if (!isset($_GET['upword']) || !ValidText($_GET['upword'], 4, 15))
            {
                JSONSet2("error", "Login Failed", "Invalid username/password");
            }
        }

        // check
        {
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE binary user_uname = ? and binary user_pword = ?");
            $stmt->bind_param("ss", $_GET['uuname'], $_GET['upword']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0)
            {
                while ($obj = $result->fetch_object()) 
                {
                    //
                    $userData = $obj;
                }
            }
            else
            {
                JSONSet2("error", "Login Failed", "Invalid username/password");
            }
        }
        
        //
        $stmt = $connection->prepare("  UPDATE user_tbl SET
                                            user_token = ?
                                        WHERE 
                                            id = ?
        ");
        $stmt->bind_param("ss", $userTokenNew, $userData->id);
        $stmt->execute();

        //
        JSONSet2("ok", "", "", $userTokenNew);
    }

    // user - register
    if ($_GET['mode'] == "userregister")
    {
        //
        $userData = new stdClass();
        $newRef = GUID();

        // check
        {
            if (!isset($_GET['uuname']) || !ValidText($_GET['uuname']))
            {
                JSONSet2("error", "Register Failed", "Invalid entry of length / symbols 1");
            }

            if (!isset($_GET['upword']) || !ValidText($_GET['upword']))
            {
                JSONSet2("error", "Register Failed", "Invalid entry of length / symbols 2");
            }

            if (!isset($_GET['ufname']) || !ValidText($_GET['ufname']))
            {
                JSONSet2("error", "Register Failed", "Invalid entry of length / symbols 3");
            }
        }

        // check DB
        {
            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE binary user_uname = ?");
            $stmt->bind_param("s", $_GET['uuname']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                JSONSet2("error", "Register Failed", "Username already taken");
            }
        }

        //
        {
            $stmt = $connection->prepare("INSERT INTO user_tbl
                                            (
                                                user_uname,
                                                user_pword,
                                                user_fname
                                            )
                                        VALUES
                                            (
                                                ?,
                                                ?,
                                                ?
                                            )
            ");
            $stmt->bind_param("sss", $_GET['uuname'], $_GET['upword'], $_GET['ufname']);
            $stmt->execute();
            $getId = $connection->insert_id;
        }

        //
        JSONSet2("ok", "Registration Complete", "Please login to continue");
    }

    // user - view
    if ($_GET['mode'] == "userview")
    {
        //
        $userData = new stdClass();

        // check
        {
            if (!isset($_GET['utoken']) || !ValidText($_GET['utoken']))
            {
                JSONSet2("error", "View Failed", "Invalid entry of length / symbols 1");
            }
        }

        // check DB
        {
            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE binary user_token = ?");
            $stmt->bind_param("s", $_GET['utoken']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0)
            {
                while ($obj = $result->fetch_object()) 
                {
                    if ($obj->user_admin == "0")
                    {
                        $obj->user_admintext = "User";
                    }

                    if ($obj->user_admin == "1")
                    {
                        $obj->user_admintext = "Admininstrator";
                    }

                    $userData = $obj;
                }
            }
            else
            {   
                //
                JSONSet2("error", "View Failed", "Relogin to continue");
            }
        }

        // Claim
        {
            $userData->user_claimtotal = 0;

            $stmt = $connection->prepare("SELECT COUNT(*) as totalClaim FROM withdraw_tbl WHERE with_user = ?");
            $stmt->bind_param("i", $userData->id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                $userData->user_claimtotal = $obj->totalClaim;
            }
        }

        // Codes
        {
            $userData->user_codetotal = 0;

            $stmt = $connection->prepare("SELECT COUNT(*) as totalCodes FROM code_tbl WHERE code_user = ?");
            $stmt->bind_param("i", $userData->id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                $userData->user_codetotal = $obj->totalCodes;
            }
        }

        //
        JSONSet2("ok", "", "", $userData);
    }

    // user - code add
    if ($_GET['mode'] == "usercodeadd")
    {
        $userData = new stdClass();
        $codeData = new stdClass();
        $codeNew = GUID();

        // check
        {
            if (!isset($_GET['uid']) || !ValidText($_GET['uid'], 1, 15))
            {
                JSONSet2("error", "Code Add Failed", "Invalid User ID 1");
            }

            if (!isset($_GET['ccode']) || !ValidText($_GET['ccode'], 4, 15))
            {
                JSONSet2("error", "Code Add Failed", "Invalid code 1");
            }
        }

        // check
        {
            // user
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0)
            {
                while ($obj = $result->fetch_object()) 
                {
                    //
                    $userData = $obj;
                }
            }
            else
            {
                JSONSet2("error", "Code Add Failed", "Invalid User ID 2");
            }

            // code
            $stmt = $connection->prepare("SELECT * FROM code_tbl WHERE binary code_id = ?");
            $stmt->bind_param("s", $_GET['ccode']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0)
            {
                while ($obj = $result->fetch_object()) 
                {
                    if ($obj->code_taken != "0")
                    {
                        JSONSet2("error", "Code Add Failed", "Code already used");
                    }

                    //
                    $codeData = $obj;
                }
            }
            else
            {
                JSONSet2("error", "Code Add Failed", "Invalid code 2");
            }
        }

        // user
        {
            $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                user_points = user_points + ?,
                                                user_pointstotal = user_pointstotal + ?
                                            WHERE
                                                id = ?
            ");
            $stmt->bind_param("iii", $codeData->code_points, $codeData->code_points, $userData->id);
            $stmt->execute();
        }

        // code
        {
            $stmt = $connection->prepare("  UPDATE code_tbl SET
                                                code_taken = 1,
                                                code_user = ?
                                            WHERE
                                                id = ?
            ");
            $stmt->bind_param("ii", $userData->id, $codeData->id);
            $stmt->execute();
        }

        //
        JSONSet2("ok", "Code Add Success", "New points added to your balance");
    }

    // user - withdraw add
    if ($_GET['mode'] == "userwithdrawadd")
    {
        $userData = new stdClass();

        // check
        {
            if (!isset($_GET['uid']) || !ValidText($_GET['uid'], 1, 15))
            {
                JSONSet2("error", "Reward Failed", "Invalid User ID 1");
            }

            if (!isset($_GET['wpoints']) || !ValidText($_GET['wpoints'], 1, 15) || !is_numeric($_GET['wpoints']) || (int)$_GET['wpoints'] < 1)
            {
                JSONSet2("error", "Reward Failed", "Invalid points 1");
            }
        }

        // check
        {
            $pointsNeed = (int)$_GET['wpoints'] * 200;


            // user
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0)
            {
                while ($obj = $result->fetch_object()) 
                {
                    //
                    if ((int)$obj->user_points < $pointsNeed)
                    {
                        JSONSet2("error", "Reward Failed", "Invalid points 2");
                    }

                    //
                    $userData = $obj;
                }
            }
            else
            {
                JSONSet2("error", "Reward Failed", "Invalid User ID 2");
            }

            // device
            $stmt = $connection->prepare("SELECT * FROM dev_tbl");
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0)
            {
                while ($obj = $result->fetch_object()) 
                {
                    //
                    if ($obj->dev_rewardongoing != "0")
                    {
                        JSONSet2("error", "Reward Failed", "Device is currently dispensing");
                    }

                    //
                    if ($obj->dev_pointsongoing != "0")
                    {
                        JSONSet2("error", "Reward Failed", "Device is currently in-use");
                    }

                    //
                    if ((int)$obj->dev_reward != 0)
                    {
                        JSONSet2("error", "Reward Failed", "Please press the button in device to dispense");
                    }
                }
            }
        }

        // user
        {
            $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                user_points = user_points - ?
                                            WHERE
                                                id = ?
            ");
            $stmt->bind_param("ii", $pointsNeed, $userData->id);
            $stmt->execute();
        }

        // withdraw
        {
            $stmt = $connection->prepare("INSERT INTO withdraw_tbl
                                            (
                                                with_date,
                                                with_points,
                                                with_user
                                            )
                                        VALUES
                                            (
                                                ?,
                                                ?,
                                                ?
                                            )
            ");
            $stmt->bind_param("sii", $dateResult, $pointsNeed, $userData->id);
            $stmt->execute();
        }

        // device
        {
            $stmt = $connection->prepare("  UPDATE dev_tbl SET
                                                dev_reward = dev_reward + ?
            ");
            $stmt->bind_param("i", $pointsNeed);
            $stmt->execute();
        }

        //
        JSONSet2("ok", "Reward Success", "Please wait for the device to dispense");
    }





    // ards - dev reward status
    if ($_GET['mode'] == "devcodestatus")
    {
        $deviceData = new stdClass();

        // device
        {
            //
            $stmt = $connection->prepare("SELECT * FROM dev_tbl");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                //
                $deviceData = $obj;
            }

            //
            $stmt = $connection->prepare("  UPDATE dev_tbl SET
                                                dev_code = ''
            ");
            $stmt->execute();
        }

        //
        echo $deviceData->dev_code;
    }

    // ards - dev reward status
    if ($_GET['mode'] == "devrewardstatus")
    {
        $deviceData = new stdClass();

        // device
        {
            //
            $stmt = $connection->prepare("SELECT * FROM dev_tbl");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                //
                $deviceData = $obj;
            }

            //
            $stmt = $connection->prepare("  UPDATE dev_tbl SET
                                                dev_reward = 0
            ");
            $stmt->execute();
        }

        //
        echo $deviceData->dev_reward;
    }

    // ards - reward set
    if ($_GET['mode'] == "devrewardset")
    {
        // check
        {
            if (!isset($_GET['cpoints']) || !ValidText($_GET['cpoints'], 1, 15) || !is_numeric($_GET['cpoints']) || (int)$_GET['cpoints'] < 1)
            {
                //JSONSet2("error", "Points Set Failed", "Invalid points: " . $_GET['cpoints']);
            }
        }

        // device
        {
            $stmt = $connection->prepare("  UPDATE dev_tbl SET
                                                dev_reward = ?
            ");
            $stmt->bind_param("s", $_GET['cpoints']);
            $stmt->execute();
        }
    }

    // ards - code set
    if ($_GET['mode'] == "devcodeset")
    {
        $codeNew = GUID();

        // check
        {
            if (!isset($_GET['cpoints']))
            {
                JSONSet2("error", "Code Failed 1", "Invalid points: " . $_GET['cpoints']);
            }

            if (!ValidText($_GET['cpoints'], 1, 15))
            {
                JSONSet2("error", "Code Failed 2", "Invalid points: " . $_GET['cpoints']);
            }

            if (!is_numeric($_GET['cpoints']))
            {
                JSONSet2("error", "Code Failed 3", "Invalid points: " . $_GET['cpoints']);
            }

            if ((int)$_GET['cpoints'] < 1)
            {
                JSONSet2("error", "Code Failed 4", "Invalid points: " . $_GET['cpoints']);
            }
        }

        // code
        {
            $stmt = $connection->prepare("INSERT INTO code_tbl
                                            (
                                                code_id,
                                                code_date,
                                                code_points
                                            )
                                        VALUES
                                            (
                                                ?,
                                                ?,
                                                ?
                                            )
            ");
            $stmt->bind_param("ssi", $codeNew, $dateResult, $_GET['cpoints']);
            $stmt->execute();
        }

        // device
        {
            $stmt = $connection->prepare("  UPDATE dev_tbl SET
                                                dev_reward = 0,
                                                dev_code = ?
            ");
            $stmt->bind_param("s", $codeNew);
            $stmt->execute();
        }

        echo "OK1";
    }

    // ards - inuse reward set
    if ($_GET['mode'] == "devinuserewardset")
    {
        // device
        {
            $stmt = $connection->prepare("  UPDATE dev_tbl SET
                                                dev_rewardongoing = 1
            ");
            $stmt->execute();
        }
    }

    // ards - inuse points set
    if ($_GET['mode'] == "devinusepointsset")
    {
        // device
        {
            $stmt = $connection->prepare("  UPDATE dev_tbl SET
                                                dev_pointsongoing = 1
            ");
            $stmt->execute();
        }
    }

    // ards - inuse reward off set
    if ($_GET['mode'] == "devinuserewardoffset")
    {
        // device
        {
            $stmt = $connection->prepare("  UPDATE dev_tbl SET
                                                dev_rewardongoing = 0
            ");
            $stmt->execute();
        }
    }

    // ards - inuse points off set
    if ($_GET['mode'] == "devinusepointsoffset")
    {
        // device
        {
            $stmt = $connection->prepare("  UPDATE dev_tbl SET
                                                dev_pointsongoing = 0
            ");
            $stmt->execute();
        }
    }




    // Function
    // =========================



    // Outer Function
    // =========================
    function JSONGet()
    {
        /*
        // get json
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        // sanitize?
        {
            sanitize_array($data);
        }
       

        return $data;
         */
    }

    function JSONSet2($resStatus = "", $resTitle = "", $resMsg = "", $resData = "")
    {
        /*
            status:
                ok      - success
                error   - error

            title:
                return any notif title

            message:
                return any notif msg
            
            data:
                return any result
        */
        echo json_encode(array("status" => $resStatus, "title" => $resTitle, "message" => $resMsg, "data" => $resData));
        exit();
    }

    function JSONSetUnity($resStatus = "", $resTitle = "", $resMsg = "", $resData = "")
    {
        /*
            status:
                ok      - success
                error   - error

            title:
                return any notif title

            message:
                return any notif msg
            
            data:
                return any result
        */

        echo $resStatus . "#" . $resTitle . "#" . $resMsg. "#" . json_encode($resData);
        exit();
    }

    // IDs
    function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535));
    }

    // Spaces?
    function ValidText($text, $minText = 4, $maxText = 500)
    {
        $isValid = true;

        // spaces
        if (preg_match('/[\s]+/', $text)) 
        {
            $isValid = false;
        }

        // all space
        if (ctype_space($text))
        {
            $isValid = false;
        }   

        // min
        if (strlen($text) < $minText)
        {
            $isValid = false;
        }

        // max
        if (strlen($text) > $maxText)
        {
            $isValid = false;
        }

        /*
        // character?
        if (strpos($text, '#') !== false || strpos($text, ',') !== false || strpos($text, '|') !== false || strpos($text, '~') !== false || strpos($text, '!') !== false || strpos($text, '+') !== false || strpos($text, '/') !== false || strpos($text, '\\') !== false || strpos($text, '*') !== false || strpos($text, '&') !== false || strpos($text, '%') !== false || strpos($text, '^') !== false) 
        {
            $isValid = false;
        }
        */

        return $isValid;
    }
?>