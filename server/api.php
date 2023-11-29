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

        // check
        {
            if (!isset($_GET['uuname']) || !ValidText($_GET['uuname'], 4, 15))
            {
                JSONSetUnity("error", "Login Failed", "Invalid username/password");
            }

            if (!isset($_GET['upword']) || !ValidText($_GET['upword'], 4, 15))
            {
                JSONSetUnity("error", "Login Failed", "Invalid username/password");
            }
        }

        //
        $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE binary user_uname = ? and binary user_pword = ?");
        $stmt->bind_param("ss", $_GET['uuname'], $_GET['upword']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0)
        {
            while ($obj = $result->fetch_object()) 
            {
                // 
                {
                    if ($obj->user_active == "0")
                    {
                        JSONSetUnity("error", "Login Failed", "This account temporarily blocked");
                    }
                }

                //
                $userData = $obj;
            }

            //
            JSONSetUnity("ok", "", "", $userData);
        }
        else
        {
            JSONSetUnity("error", "Login Failed", "Invalid username/password");
        }
    }

    // user - dashboard data
    if ($_GET['mode'] == "userdatadashboard")
    {
        //
        $userData = new stdClass();
        $weapData = new stdClass();

        // check
        {
            if (!isset($_GET['uid']))
            {
                JSONSetUnity("error", "View Failed", "Invalid ID");
            }
        }

        // user data
        {
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0)
            {
                while ($obj = $result->fetch_object()) 
                {
                    //
                    {
                        if ((int)$obj->user_nextclaim <= strtotime($dateResult))
                        {
                            $obj->user_nextclaim = "Claim Now!";
                        }
                        else
                        {
                            $obj->user_nextclaim = date("Y-m-d H:i:s", $obj->user_nextclaim);
                        }
                       
                    }

                    //
                    $userData = $obj;
                }
            }
            else
            {
                JSONSetUnity("error", "View Failed", "Not exist ID");
            }
        }
        
        // weapon data
        {
            $stmt = $connection->prepare("SELECT * FROM weapon_tbl WHERE id = ?");
            $stmt->bind_param("i", $userData->user_weap);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                //
                {

                }

                //
                $weapData = $obj;
            }
        }

        // subscription data
        {
            $userData->user_sub_id = "0";
            $userData->user_sub_name = "";
            $userData->user_sub_datestart = "";
            $userData->user_sub_dateend = "";

            $stmt = $connection->prepare("SELECT * FROM subscription_tbl WHERE id = ?");
            $stmt->bind_param("i", $userData->user_vip);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                //
                {
                    $userData->user_sub_id = $obj->id;
                    $userData->user_sub_name = $dataPackageListId[$obj->sub_packageid]->package_name;
                    $userData->user_sub_datestart = date("Y-m-d H:i:s", $obj->sub_start);
                    $userData->user_sub_dateend = date("Y-m-d H:i:s", $obj->sub_end);
                }
            }
        }

        // other data
        {
            // stats
            $userData->user_stats_tpower = (int)$dataClass[(int)$userData->user_class]->power + (int)$weapData->weapon_bpower + (int)$weapData->weapon_ppower;
            $userData->user_stats_tendurance = (int)$dataClass[(int)$userData->user_class]->endurance + (int)$weapData->weapon_bendurance + (int)$weapData->weapon_pendurance;
            $userData->user_stats_tspeed = (int)$dataClass[(int)$userData->user_class]->speed + (int)$weapData->weapon_bspeed + (int)$weapData->weapon_pspeed;
            $userData->user_stats_tconcentration = (int)$dataClass[(int)$userData->user_class]->concentration + (int)$weapData->weapon_bconcentration + (int)$weapData->weapon_pconcentration;
        
            $userData->user_weap_ability = $weapData->weapon_ability;
            $userData->user_weap_grade = $weapData->weapon_grade;

            // versus
            $userData->user_computed_hp = (int)((($userData->user_stats_tendurance * 2) * 100) / 100) + 110;
            $userData->user_computed_hpmax = $userData->user_computed_hp;
            $userData->user_computed_attack = (int)((($userData->user_stats_tpower * 2) * 100) / 100) + 5;
            $userData->user_computed_defense = (int)((($userData->user_stats_tendurance * 2) * 100) / 100) + 5;
            $userData->user_computed_speed = (int)((($userData->user_stats_tspeed * 2) * 100) / 100) + 5;
        
            $userData->user_computed_accuracy = (int)((($userData->user_stats_tconcentration * 2) * 100) / 100) + 5;
            $userData->user_computed_evasiveness = (int)((($userData->user_stats_tconcentration * 2) * 100) / 100) + 5;
            $userData->user_computed_critical = (int)((($userData->user_stats_tconcentration * 2) * 100) / 100) + 5;

            // weapon
            $userData->user_weap_bpower = $weapData->weapon_bpower;
            $userData->user_weap_bendurance = $weapData->weapon_bendurance;
            $userData->user_weap_bspeed = $weapData->weapon_bspeed;
            $userData->user_weap_bconcentration = $weapData->weapon_bconcentration;
            $userData->user_weap_ppower = $weapData->weapon_ppower;
            $userData->user_weap_pendurance = $weapData->weapon_pendurance;
            $userData->user_weap_pspeed = $weapData->weapon_pspeed;
            $userData->user_weap_pconcentration = $weapData->weapon_pconcentration;
        }
         
        //
        JSONSetUnity("ok", "", "", $userData);
    }

    // user - list data
    if ($_GET['mode'] == "userdatalist")
    {
        $userData = new stdClass();
        $listData = new stdClass();
        $depositDataList = array();
        $withdrawDataList = array();
        $subscriptionDataList = array();
        $arenaDataList = array();
        $practiceDataList = array();
        $referralDataList = array();

        // check
        {
            if (!isset($_GET['uid']))
            {
                JSONSetUnity("error", "View Failed", "Invalid ID");
            }
        }

        //
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
            JSONSetUnity("error", "View Failed", "Not exist ID");
        }

        // Deposit
        {
            $stmt = $connection->prepare("SELECT * FROM deposit_tbl WHERE dep_userid = ? ORDER BY id DESC LIMIT 100");
            $stmt->bind_param("i",$_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                //
                {
                    $newStatus = "";
                    if ($obj->dep_status == "0") 
                    {
                        $newStatus = $obj->dep_date . " - Pending";
                    }
                    if ($obj->dep_status == "1") 
                    {
                        $newStatus = $obj->dep_date . " - Completed";
                    }
                    if ($obj->dep_status == "2") 
                    {
                        $newStatus = $obj->dep_date . " - Rejected";
                    }

                    $obj->dep_amount = $obj->dep_amount . " USDT";
                    $obj->dep_status = $newStatus;
                }

                //
                $depositDataList[] = $obj;
            }
        }

        // Withdraw
        {
            $stmt = $connection->prepare("SELECT * FROM withdraw_tbl WHERE with_userid = ? ORDER BY id DESC LIMIT 100");
            $stmt->bind_param("i",$_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                //
                {
                    $newStatus = "";
                    if ($obj->with_status == "0") 
                    {
                        $newStatus = $obj->with_date . " - Pending";
                    }
                    if ($obj->with_status == "1") 
                    {
                        $newStatus = $obj->with_date . " - Completed";
                    }
                    if ($obj->with_status == "2") 
                    {
                        $newStatus = $obj->with_date . " - Rejected";
                    }

                    $obj->with_amount = $obj->with_amount . " USDT";
                    $obj->with_status = $newStatus;
                }

                //
                $withdrawDataList[] = $obj;
            }
        }

        // Subscription
        {
            $stmt = $connection->prepare("SELECT * FROM subscription_tbl WHERE sub_userid = ? ORDER BY id DESC LIMIT 100");
            $stmt->bind_param("i",$_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                //
                {
                    $obj->sub_start = date("Y-m-d H:i:s", $obj->sub_start);
                    $obj->sub_end = date("Y-m-d H:i:s", $obj->sub_end);
                }

                //
                $subscriptionDataList[] = $obj;
            }
        }

        // Arena
        {
            $posCounter = 1;

            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE user_arenawin > 0 ORDER BY user_arenawin DESC LIMIT 100");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                //
                {
                    $objNew = new stdClass();
                    $objNew->user_nickname = $obj->user_nickname;
                    $objNew->user_class = $obj->user_class;
                    $objNew->user_rank = $obj->user_rank;
                    $objNew->user_arenawin = $obj->user_arenawin;
                    $objNew->user_arenarank = $posCounter;

                    $posCounter = $posCounter + 1;
                }

                //
                $arenaDataList[] = $objNew;
            }
        }

        // Practice
        {
            $stmt = $connection->prepare("SELECT * FROM practice_tbl WHERE p_attackerid = ? OR p_defenderid = ? ORDER BY id DESC LIMIT 100");
            $stmt->bind_param("ii", $_GET['uid'], $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                //
                {
                    // attack?
                    if ($obj->p_attackerid == $_GET['uid'])
                    {
                        $obj->p_targetName = $obj->p_defendername;
                        $obj->p_targetClass = $obj->p_defenderclass;
                        $obj->p_targetRank = $obj->p_defenderrank;
                    }

                    // defend?
                    else
                    {
                        $obj->p_targetName = $obj->p_attackername;
                        $obj->p_targetClass = $obj->p_attackerclass;
                        $obj->p_targetRank = $obj->p_attackerrank;
                    }

                    // result win?
                    if ($obj->p_win == $_GET['uid'])
                    {
                        $obj->p_log = "Win - " . $obj->p_date;
                    }

                    // result lose?
                    else
                    {
                        $obj->p_log = "Lose - " . $obj->p_date;
                    }
                }

                //
                $practiceDataList[] = $obj;
            }
        }

        // Referral
        {
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE user_refby = ? ORDER BY id DESC");
            $stmt->bind_param("i",$_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                //
                {
                    $objNew = new stdClass();
                    $objNew->user_nickname = $obj->user_nickname;
                    $objNew->user_date = $obj->user_date;

                    if ($obj->user_vip == "0")
                    {
                        $objNew->user_vip = "-";
                    }
                    else
                    {
                        $objNew->user_vip = "Subscribed";
                    }
                }

                //
                $referralDataList[] = $objNew;
            }
        }
        
        //
        $listData->depositDataList = $depositDataList;
        $listData->withdrawDataList = $withdrawDataList;
        $listData->subscriptionDataList = $subscriptionDataList;
        $listData->arenaDataList = $arenaDataList;
        $listData->practiceDataList = $practiceDataList;
        $listData->referralDataList = $referralDataList;

        //
        JSONSetUnity("ok", "", "", $listData);
    }


    // user - register
    if ($_GET['mode'] == "userregister")
    {
        //
        $userData = new stdClass();
        $newRef = GUID();

        $refData = new stdClass();
        $refData->id = "0";

        // check
        {
            if (!isset($_GET['uuname']) || !ValidText($_GET['uuname'], 4, 15))
            {
                JSONSetUnity("error", "Register Failed", "Invalid entry of length / symbols 1");
            }

            if (!isset($_GET['upword']) || !ValidText($_GET['upword'], 4, 15))
            {
                JSONSetUnity("error", "Register Failed", "Invalid entry of length / symbols 2");
            }

            if (!isset($_GET['uemail']) || !ValidText($_GET['uemail'], 4, 30))
            {
                JSONSetUnity("error", "Register Failed", "Invalid entry of length / symbols 3");
            }

            if (!isset($_GET['unname']) || !ValidText($_GET['unname'], 4, 15))
            {
                JSONSetUnity("error", "Register Failed", "Invalid entry of length / symbols 4");
            }

            if (!isset($_GET['upolygon']) || !ValidText($_GET['upolygon'], 42, 42))
            {
                JSONSetUnity("error", "Register Failed", "Invalid entry of length / symbols 5");
            }

            if (!isset($_GET['uref']) || !ValidText($_GET['uref'], 0, 15))
            {
                JSONSetUnity("error", "Register Failed", "Invalid entry of length / symbols 6");
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
                JSONSetUnity("error", "Register Failed", "Username already taken");
            }

            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE user_email = ?");
            $stmt->bind_param("s", $_GET['uemail']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                JSONSetUnity("error", "Register Failed", "Email already taken");
            }

            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE binary user_nickname = ?");
            $stmt->bind_param("s", $_GET['unname']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                JSONSetUnity("error", "Register Failed", "Nickname already taken");
            }

            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE user_address_polygon = ?");
            $stmt->bind_param("s", $_GET['upolygon']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                JSONSetUnity("error", "Register Failed", "Metamask Polygon address already taken");
            }

            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE binary user_ref = ?");
            $stmt->bind_param("s", $_GET['uref']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                $refData = $obj;
            }
        }

        //
        {
            $stmt = $connection->prepare("INSERT INTO user_tbl
                                            (
                                                user_date,
                                                user_ref,
                                                user_refby,
                                                user_uname,
                                                user_pword,
                                                user_nickname,
                                                user_email,
                                                user_address_polygon
                                            )
                                        VALUES
                                            (
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?,
                                                ?
                                            )
            ");
            $stmt->bind_param("ssisssss", $dateResult, $newRef, $refData->id, $_GET['uuname'], $_GET['upword'], $_GET['unname'], $_GET['uemail'], $_GET['upolygon']);
            $stmt->execute();
            $getId = $connection->insert_id;
        }

        // Referral
        {
            //
            $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                user_refcount = user_refcount + 1
                                            WHERE
                                                id = ?
            ");
            $stmt->bind_param("i", $refData->id);
            $stmt->execute();
        }

        //
        {
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $getId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                $userData = $obj;
            }
        }

        // weapon
        {
            // delete
            $stmt = $connection->prepare("DELETE FROM weapon_tbl
                                        WHERE
                                            weapon_original = ? OR
                                            weapon_userid = ?
            ");
            $stmt->bind_param("ii", $userData->id, $userData->id);
            $stmt->execute();

            // insert
            $stmt = $connection->prepare("INSERT INTO weapon_tbl
                                            (
                                                weapon_date,
                                                weapon_original,
                                                weapon_userid
                                            )
                                        VALUES
                                            (
                                                ?,
                                                ?,
                                                ?
                                            )
            ");
            $stmt->bind_param("sii", $dateResult, $userData->id, $userData->id);
            $stmt->execute();
            $getWeaponId = $connection->insert_id;

            //
            $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                user_weap = ?
                                            WHERE
                                                id = ?
            ");
            $stmt->bind_param("ii", $getWeaponId, $userData->id);
            $stmt->execute();
        }

        //
        JSONSetUnity("ok", "Registration Complete", "Please login to continue");
    }

    // user - update
    if ($_GET['mode'] == "userupdate")
    {
        //
        $userData = new stdClass();

        // check
        {
            if (!isset($_GET['uid']))
            {
                JSONSetUnity("error", "Update Failed", "Invalid ID");
            }

            if (!isset($_GET['upword']) || !ValidText($_GET['upword'], 4, 15))
            {
                JSONSetUnity("error", "Update Failed", "Invalid entry of length / symbols");
            }

            if (!isset($_GET['uemail']) || !ValidText($_GET['uemail'], 4, 30))
            {
                JSONSetUnity("error", "Update Failed", "Invalid entry of length / symbols");
            }

            if (!isset($_GET['upolygon']) || !ValidText($_GET['upolygon'], 42, 42))
            {
                JSONSetUnity("error", "Update Failed", "Invalid entry of length / symbols");
            }
        }

        // check DB
        {
            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE user_email = ? and id != ?");
            $stmt->bind_param("si", $_GET['uemail'], $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                JSONSetUnity("error", "Update Failed", "Email already taken");
            }

            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE user_address_polygon = ? and id != ?");
            $stmt->bind_param("si", $_GET['upolygon'], $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                JSONSetUnity("error", "Update Failed", "Metasmask Polygon address already taken");
            }

            //
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0)
            {
                while ($obj = $result->fetch_object()) 
                {
                    //
                    {

                    }

                    //
                    $userData = $obj;
                }
            }
            else
            {
                JSONSetUnity("error", "View Failed", "Not exist ID");
            }
        }

        //
        $stmt = $connection->prepare("  UPDATE user_tbl SET
                                            user_pword = ?,
                                            user_email = ?,
                                            user_address_polygon = ?
                                        WHERE
                                            id = ?
        ");
        $stmt->bind_param("sssi", $_GET['upword'], $_GET['uemail'], $_GET['upolygon'], $userData->id);
        $stmt->execute();

        //
        JSONSetUnity("ok", "Update Complete", "Your new data saved successfully");
    }

    // user - forgot
    if ($_GET['mode'] == "userforgot")
    {
        //
        $userData = new stdClass();
        $newPass = GUID();

        // check
        {
            if (!isset($_GET['uemail']) || !ValidText($_GET['uemail'], 4, 30))
            {
                JSONSetUnity("error", "New Password Failed", "Invalid email");
            }
        }

        // check DB
        {
            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE user_email = ?");
            $stmt->bind_param("s", $_GET['uemail']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) 
            {
                while ($obj = $result->fetch_object()) 
                {
                    $userData = $obj;
                }
            }
            else
            {
                JSONSetUnity("error", "New Password Failed", "Invalid email / Not Exist");
            }
        }

        //
        $stmt = $connection->prepare("  UPDATE user_tbl SET
                                            user_pword = ?
                                        WHERE
                                            id = ?
        ");
        $stmt->bind_param("si", $newPass, $userData->id);
        $stmt->execute();

        // Email
        {

        }

        //
        JSONSetUnity("ok", "New Password Complete", "Check your registered email for your new password");
    }

    // user - select
    if ($_GET['mode'] == "userselect")
    {
        //
        $userData = new stdClass();
        $getWeaponId = "";

        // check
        {
            if (!isset($_GET['uid']))
            {
                JSONSetUnity("error", "Customize Failed", "Invalid ID");
            }

            if (!isset($_GET['uclass']))
            {
                JSONSetUnity("error", "Customize Failed", "Invalid class");
            }
        }

        // check DB
        {
            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) 
            {
                while ($obj = $result->fetch_object()) 
                {
                    //
                    if ($obj->user_class != "-1")
                    {
                        JSONSetUnity("error", "Customize Failed", "Please relogin");
                    }

                    $userData = $obj;
                }
            }
            else
            {
                JSONSetUnity("error", "Customize Failed", "Not exist ID");
            }
        }

        {
            //
            $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                user_class = ?
                                            WHERE
                                                id = ?
            ");
            $stmt->bind_param("ii", $_GET['uclass'], $userData->id);
            $stmt->execute();
        }

        //
        JSONSetUnity("ok", "Customize Complete", "");
    }


    // user - upgraderank
    if ($_GET['mode'] == "userupgraderank")
    {
        //
        $userData = new stdClass();
        $refData = new stdClass();

        //
        $upgradeStardustCost = 1000;

        // check
        {
            if (!isset($_GET['uid']))
            {
                JSONSetUnity("error", "Upgrade Failed", "Invalid ID");
            }
        }

        // check DB
        {
            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) 
            {
                while ($obj = $result->fetch_object()) 
                {
                    {
                        if ((int)$obj->user_rank >= 4)
                        {
                            JSONSetUnity("error", "Upgrade Failed", "Already reached max rank");
                        }

                        if ((int)$obj->user_stardust < $upgradeStardustCost)
                        {
                            JSONSetUnity("error", "Upgrade Failed", "Low Stardust");
                        }
                    }

                    $userData = $obj;
                }
            }
            else
            {
                JSONSetUnity("error", "Upgrade Failed", "Not exist ID");
            }

            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $userData->user_refby);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) 
            {
                while ($obj = $result->fetch_object()) 
                {
                    $refData = $obj;
                }
            }
        }

        // update user 
        {
            //
            $newStardust = (int)$userData->user_stardust - (int)$upgradeStardustCost;

            //
            $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                user_rank = user_rank + 1,
                                                user_stardust = ?
                                            WHERE
                                                id = ?
            ");
            $stmt->bind_param("ii", $newStardust, $userData->id);
            $stmt->execute();
        }

        // Reward Starlight - Referral (subs only)
        {
            // ref?
            if ($userData->user_refby != "0")
            {
                // sub?
                if ($refData->user_vip != "0")
                {
                    $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                        user_starlight = user_starlight + 1
                                                    WHERE
                                                        id = ?
                    ");
                    $stmt->bind_param("i", $refData->id);
                    $stmt->execute();
                }
            }
        }

        //
        JSONSetUnity("ok", "Upgrade Success", "Stardusts Power upgraded your rank!");
    }

    // user - weapon base
    if ($_GET['mode'] == "weaponupgradebase")
    {
        //
        $userData = new stdClass();
        $refData = new stdClass();
        $weaponData = new stdClass();

        //
        $upgradeSuccess = false;
        $upgradeStardustCost = 100;

        // check
        {
            if (!isset($_GET['uid']))
            {
                JSONSetUnity("error", "Upgrade Failed", "Invalid ID");
            }
        }

        // check DB
        {
            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) 
            {
                while ($obj = $result->fetch_object()) 
                {
                    {
                        if ((int)$obj->user_stardust < $upgradeStardustCost)
                        {
                            JSONSetUnity("error", "Upgrade Failed", "Low Stardust");
                        }
                    }

                    $userData = $obj;
                }
            }
            else
            {
                JSONSetUnity("error", "Upgrade Failed", "Not exist ID");
            }

            // exist?
            $stmt = $connection->prepare("SELECT * FROM weapon_tbl WHERE id = ?");
            $stmt->bind_param("i", $userData->user_weap);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) 
            {
                while ($obj = $result->fetch_object()) 
                {
                    $weaponData = $obj;
                }
            }
            else
            {
                JSONSetUnity("error", "Upgrade Failed", "Not exist ID");
            }

            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $userData->user_refby);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) 
            {
                while ($obj = $result->fetch_object()) 
                {
                    $refData = $obj;
                }
            }
        }

        // update weapon
        {
            // chance
            if (rand(0, 100) <= 25)
            {
                //
                $upgradeSuccess = true;
                $newBPower = (int)$weaponData->weapon_bpower + 1;
                $newBEndurance= (int)$weaponData->weapon_bendurance + 1;
                $newBSpeed = (int)$weaponData->weapon_bspeed + 1;
                $newBConcentration = (int)$weaponData->weapon_bconcentration + 1;

                //
                $stmt = $connection->prepare("  UPDATE weapon_tbl SET
                                                    weapon_bpower = ?,
                                                    weapon_bendurance = ?,
                                                    weapon_bspeed = ?,
                                                    weapon_bconcentration = ?
                                                WHERE
                                                    id = ?
                ");
                $stmt->bind_param("iiiii", $newBPower, $newBEndurance, $newBSpeed, $newBConcentration, $weaponData->id);
                $stmt->execute();
            }
        }

        // update user
        {
            //
            $newStardust = (int)$userData->user_stardust - (int)$upgradeStardustCost;

            //
            $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                user_stardust = ?
                                            WHERE
                                                id = ?
            ");
            $stmt->bind_param("ii", $newStardust, $userData->id);
            $stmt->execute();
        }

        // Reward Starlight - Referral (subs only)
        {
            // ref?
            if ($userData->user_refby != "0")
            {
                // sub?
                if ($refData->user_vip != "0")
                {
                    $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                        user_starlight = user_starlight + 1
                                                    WHERE
                                                        id = ?
                    ");
                    $stmt->bind_param("i", $refData->id);
                    $stmt->execute();
                }
            }
        }

        // exit
        {
            if ($upgradeSuccess)
            {
                //
                JSONSetUnity("ok", "Upgrade Success", "Stardusts Power upgraded your weapon successfully!");
            }

            //
            JSONSetUnity("ok", "Upgrade Failed", "Stardusts Power failed to upgrade your weapon");
        }
    }

    // user - weapon purity
    if ($_GET['mode'] == "weaponreforge")
    {
        //
        $userData = new stdClass();
        $refData = new stdClass();
        $weaponData = new stdClass();

        //
        $upgradeSuccess = false;
        $upgradeStardustCost = 100;

        // check
        {
            if (!isset($_GET['uid']))
            {
                JSONSetUnity("error", "Upgrade Failed", "Invalid ID");
            }
        }

        // check DB
        {
            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) 
            {
                while ($obj = $result->fetch_object()) 
                {
                    {
                        if ((int)$obj->user_stardust < $upgradeStardustCost)
                        {
                            JSONSetUnity("error", "Upgrade Failed", "Low Stardust");
                        }
                    }

                    $userData = $obj;
                }
            }
            else
            {
                JSONSetUnity("error", "Upgrade Failed", "Not exist ID");
            }


            // exist?
            $stmt = $connection->prepare("SELECT * FROM weapon_tbl WHERE id = ?");
            $stmt->bind_param("i", $userData->user_weap);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) 
            {
                while ($obj = $result->fetch_object()) 
                {
                    $weaponData = $obj;
                }
            }
            else
            {
                JSONSetUnity("error", "Upgrade Failed", "Not exist ID");
            }

            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $userData->user_refby);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) 
            {
                while ($obj = $result->fetch_object()) 
                {
                    $refData = $obj;
                }
            }
        }

        // update weapon
        {
            // chance
            if (rand(0, 100) <= 100)
            {
                //
                $upgradeSuccess = true;
                $newPPower = rand(1, 50);
                $newPEndurance= rand(1, 50);
                $newPSpeed = rand(1, 50);
                $newPConcentration = rand(1, 50);
                $newGrade = "1";
                $newAbility = rand(4, 15);

                // Grade
                {
                    $totalPStat = $newPPower + $newPEndurance + $newPSpeed + $newPConcentration;
                    if ($totalPStat >= 0 && $totalPStat <= 50)
                    {
                        $newGrade = "1";
                    }

                    if ($totalPStat >= 51 && $totalPStat <= 100)
                    {
                        $newGrade = "2";
                    }

                    if ($totalPStat >= 101 && $totalPStat <= 150)
                    {
                        $newGrade = "3";
                    }

                    if ($totalPStat >= 151 && $totalPStat <= 199)
                    {
                        $newGrade = "4";
                    }

                    if ($totalPStat >= 200)
                    {
                        $newGrade = "5";
                    }
                }
                
                //
                $stmt = $connection->prepare("  UPDATE weapon_tbl SET
                                                    weapon_ppower = ?,
                                                    weapon_pendurance = ?,
                                                    weapon_pspeed = ?,
                                                    weapon_pconcentration = ?,
                                                    weapon_grade = ?,
                                                    weapon_ability = ?
                                                WHERE
                                                    id = ?
                ");
                $stmt->bind_param("iiiiiii", $newPPower, $newPEndurance, $newPSpeed, $newPConcentration, $newGrade, $newAbility, $weaponData->id);
                $stmt->execute();
            }
        }

        // update user
        {
            //
            $newStardust = (int)$userData->user_stardust - (int)$upgradeStardustCost;

            //
            $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                user_stardust = ?
                                            WHERE
                                                id = ?
            ");
            $stmt->bind_param("ii", $newStardust, $userData->id);
            $stmt->execute();
        }

        // Reward Starlight - Referral (subs only)
        {
            // ref?
            if ($userData->user_refby != "0")
            {
                // sub?
                if ($refData->user_vip != "0")
                {
                    $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                        user_starlight = user_starlight + 1
                                                    WHERE
                                                        id = ?
                    ");
                    $stmt->bind_param("i", $refData->id);
                    $stmt->execute();
                }
            }
        }

        // exit
        {
            if ($upgradeSuccess)
            {
                //
                JSONSetUnity("ok", "Upgrade Success", "Stardusts Power reforged your weapon successfully!");
            }

            //
            JSONSetUnity("ok", "Upgrade Failed", "Stardusts Power failed to reforge your weapon");
        }
    }

    // user - SGIFT
    if ($_GET['mode'] == "sgiftadd")
    {
        //
        $userData = new stdClass();
        $packageData = new stdClass();

        // check
        {
            if (!isset($_GET['uid']))
            {
                JSONSetUnity("error", "S-GIFTS Claim Failed", "Invalid ID");
            }
        }

        // check DB
        {
            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) 
            {
                while ($obj = $result->fetch_object()) 
                {
                    {
                        if ((int)$obj->user_nextclaim > (int)strtotime($dateResult))
                        {
                            JSONSetUnity("error", "S-GIFTS Claim Failed", "Stars Gift is not yet ready, Please come back again");
                        }
                    }

                    $userData = $obj;
                }
            }
            else
            {
                JSONSetUnity("error", "S-GIFTS Claim Failed", "Not exist ID");
            }
        }

        // vip?
        {
            if ($userData->user_vip != "0")
            {
                $stmt = $connection->prepare("SELECT * FROM subscription_tbl WHERE id = ?");
                $stmt->bind_param("i", $userData->user_vip);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($obj = $result->fetch_object()) 
                {
                    $packageData = $dataPackageListId[$obj->sub_packageid];
                }
            }
            else
            {
                $packageData = $dataPackageListId[0];
            }
        }

        //
        {
            //
            $newGiftClaim = strtotime($dateResult) + 10;

            //
            $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                user_nextclaim = ?,
                                                user_starlight = user_starlight + ?,
                                                user_stardust = user_stardust + ?
                                            WHERE
                                                id = ?
            ");
            $stmt->bind_param("iiii", $newGiftClaim, $packageData->package_bstarlight, $packageData->package_bstardust, $userData->id);
            $stmt->execute();
        }

        //
        JSONSetUnity("ok", "S-GIFTS Claim Success", "", $packageData);
    }


    // user - withdraw add
    if ($_GET['mode'] == "withdrawadd")
    {
        //
        $userData = new stdClass();

        // check
        {
            if (!isset($_GET['uid']))
            {
                JSONSetUnity("error", "Withdraw Failed", "Invalid ID");
            }

            if (!isset($_GET['uamt']) || !is_numeric($_GET['uamt']) || (int)$_GET['uamt'] < 10 || (int)$_GET['uamt'] > 100)
            {
                JSONSetUnity("error", "Withdraw Failed", "Invalid Amount");
            }
        }

        // check DB
        {
            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) 
            {
                while ($obj = $result->fetch_object()) 
                {
                    {
                        if ((int)$obj->user_usdt < (int)$_GET['uamt'])
                        {
                            JSONSetUnity("error", "Withdraw Failed", "Low USDT");
                        }
                    }

                    $userData = $obj;
                }
            }
            else
            {
                JSONSetUnity("error", "Withdraw Failed", "Not exist ID");
            }
        }

        {
            //
            $stmt = $connection->prepare("  INSERT INTO withdraw_tbl 
                                                (
                                                    with_date,
                                                    with_userid,
                                                    with_address,
                                                    with_amount
                                                )
                                            VALUES
                                                (
                                                    ?,
                                                    ?,
                                                    ?,
                                                    ?
                                                )
            ");
            $stmt->bind_param("siss", $dateResult, $userData->id, $userData->user_address_polygon, $_GET['uamt']);
            $stmt->execute();
        }

        {
            //
            $newUSDT = (int)$userData->user_usdt - (int)$_GET['uamt'];

             //
            $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                user_usdt = ?
                                            WHERE
                                                id = ?
            ");
            $stmt->bind_param("ii", $newUSDT, $userData->id);
            $stmt->execute();
        }

        //
        JSONSetUnity("ok", "Withraw Requested", "Your request was successfully added");
    }

    // user - subscription add
    if ($_GET['mode'] == "subscriptionadd")
    {
        //
        $userData = new stdClass();
        $packageData = new stdClass();

        // check
        {
            if (!isset($_GET['uid']))
            {
                JSONSetUnity("error", "Subscription Failed", "Invalid ID");
            }

            if (!isset($_GET['sid']))
            {
                JSONSetUnity("error", "Subscription Failed", "Invalid ID");
            }

            if (!isset($dataPackageListId[$_GET['sid']]))
            {
                JSONSetUnity("error", "Subscription Failed", "Invalid Subscription");
            }
        }

        // check DB
        {
            //
            $packageData = $dataPackageListId[$_GET['sid']];
            $pDateStart = strtotime($dateResult);
            $pDateEnd = strtotime($dateResult . ' + ' . $packageData->package_days . ' days');
            
            // exist?
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) 
            {
                while ($obj = $result->fetch_object()) 
                {
                    {
                        if ($obj->user_vip != "0")
                        {
                            JSONSetUnity("error", "Subscription Failed", "Already subscribed. Thank you!");
                        }

                        if ((int)$obj->user_usdt < (int)$packageData->package_amount)
                        {
                            JSONSetUnity("error", "Subscription Failed", "Low USDT");
                        }
                    }

                    $userData = $obj;
                }
            }
            else
            {
                JSONSetUnity("error", "Subscription Failed", "Not exist ID");
            }
        }

        //
        {
            //
            $stmt = $connection->prepare("  INSERT INTO subscription_tbl 
                                                (
                                                    sub_date,
                                                    sub_userid,
                                                    sub_packageid,
                                                    sub_name,
                                                    sub_amount,
                                                    sub_start,
                                                    sub_end
                                                )
                                            VALUES
                                                (
                                                    ?,
                                                    ?,
                                                    ?,
                                                    ?,
                                                    ?,
                                                    ?,
                                                    ?
                                                )
            ");
            $stmt->bind_param("siisiii", $dateResult, $userData->id, $_GET['sid'], $packageData->package_name, $packageData->package_amount, $pDateStart, $pDateEnd);
            $stmt->execute();
            $getSubscriptionId = $connection->insert_id;
        }

        //
        {
            //
            $newUSDT = (int)$userData->user_usdt - (int)$packageData->package_amount;

            //
            $stmt = $connection->prepare("  UPDATE user_tbl SET
                                                user_vip = ?,
                                                user_usdt = ?,
                                                user_vipstart = ?,
                                                user_vipend = ?
                                            WHERE
                                                id = ?
            ");
            $stmt->bind_param("iiiii", $getSubscriptionId, $newUSDT, $pDateStart, $pDateEnd, $userData->id);
            $stmt->execute();
        }

        //
        JSONSetUnity("ok", "Subscription Complete", "You got blessings from the stars, Bonuses will be added in your next S-GIFT claim");
    }


    // user - arena
    if ($_GET['mode'] == "arenaadd")
    {
        //
        $userData1 = new stdClass();
        $userData2 = new stdClass();
        $versusData = new stdClass();

        // check
        {
            if (!isset($_GET['uid']))
            {
                JSONSetUnity("error", "Arena Failed", "Invalid ID");
            }
        }

        // check DB
        {
            
        }

        // Player 1
        {
            //
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id = ?");
            $stmt->bind_param("i", $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0)
            {
                while ($obj = $result->fetch_object()) 
                {
                    // 
                    {
                        if ((int)$obj->user_stardust < 1)
                        {
                            JSONSetUnity("error", "Arena Failed", "Low Stardust");
                        }

                        if ((int)$obj->user_arenanext > (int)strtotime($dateResult))
                        {
                            JSONSetUnity("error", "Arena Failed", "Preparing, Please try again");
                        }
                    }
                }
            }
            else
            {
                JSONSetUnity("error", "Arena Failed", "Not exist ID");
            }

            //
            $player1DataRaw = file_get_contents("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?mode=userdatadashboard&uid=" . $_GET['uid']);
            $player1DataResult = explode('#', $player1DataRaw);

            // check?
            if ($player1DataResult[0] != "ok")
            {
                JSONSetUnity("error", "Arena Failed", "Invalid Data (1)");
            }

            //
            $userData1 = json_decode($player1DataResult[3]);
        }

        // Player 2
        {
            //
            $getId = "";

            //
            $stmt = $connection->prepare("SELECT * FROM user_tbl WHERE id != ? AND user_class != -1 ORDER BY RAND() LIMIT 1");
            $stmt->bind_param("i", $_GET['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($obj = $result->fetch_object()) 
            {
                //
                $getId = $obj->id;
            }

            //
            $player2DataRaw = file_get_contents("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?mode=userdatadashboard&uid=" . $getId);
            $player2DataResult = explode('#', $player2DataRaw);

            // check?
            if ($player2DataResult[0] != "ok")
            {
                JSONSetUnity("error", "Arena Failed", "Invalid Data (2)");
            }

            //
            $userData2 = json_decode($player2DataResult[3]);
        }

        // versus
        {
            $versusData = VersusResult($userData1, $userData2);
        }

        // updates
        {
            // Pay
            {
                $stmt = $connection->prepare("  UPDATE user_tbl SET 
                                                    user_stardust = user_stardust - 1
                                                WHERE id = ?");
                $stmt->bind_param("i", $userData1->id);
                $stmt->execute();
            }

            // P1
            if ($userData1->id == $versusData->versusWinId)
            {
                //
                $arenaNext = strtotime($dateResult) + 10;

                //
                $stmt = $connection->prepare("  UPDATE user_tbl SET 
                                                    user_arenawin = user_arenawin + 1,
                                                    user_arenanext = ?
                                                WHERE id = ?");
                $stmt->bind_param("ii", $arenaNext, $userData1->id);
                $stmt->execute();
            }

            // P2
            if ($userData2->id == $versusData->versusWinId)
            {
                $stmt = $connection->prepare("  UPDATE user_tbl SET 
                                                    user_arenawin = user_arenawin + 1
                                                WHERE id = ?");
                $stmt->bind_param("i", $userData2->id);
                $stmt->execute();
            }
        }

        //
        JSONSetUnity("ok", "", "", $versusData);
    }

    // data - class
    if ($_GET['mode'] == "gclass")
    {
        $json_data = file_get_contents("../data/class.json");
        $json_data = json_decode($json_data);
        JSONSetUnity("ok", "", "", $json_data);
    }



    // Ingame Function
    // =========================
    function WeaponGenerateA($setStatBaseMin = 0, $setStatBaseMax = 50, $setStatRVMin = 0, $setStatRVMax = 50)
    {
        // power                        0
        // endurance                    1 
        // speed                        2
        // concentration                3

        // base     120
        // stats    280 - 70 max ea     (original) upgradeable
        // rv       200 - 50 max ea     (random)
        // total    600

        // set Base
        $weapBasePower = rand($setStatBaseMin, $setStatBaseMax);
        $weapBaseEndurance = rand($setStatBaseMin, $setStatBaseMax);
        $weapBaseSpeed = rand($setStatBaseMin, $setStatBaseMax);
        $weapBaseConcentration = rand($setStatBaseMin, $setStatBaseMax);
        
        // set RV
        $weapRVPower = rand($setStatRVMin, $setStatRVMax);
        $weapRVEndurance = rand($setStatRVMin, $setStatRVMax);
        $weapRVSpeed = rand($setStatRVMin, $setStatRVMax);
        $weapRVConcentration = rand($setStatRVMin, $setStatRVMax);

        // grade (RV)
        {
            // 
            // common   0               0
            // rare     10              1
            // epic     20              2
            // legend   30              3
            // mythical 40              4

            $weaponGrade = "1";
            $weaponStatTotal = $weapRVPower + $weapRVEndurance + $weapRVSpeed + $weapRVConcentration;

            if ($weaponStatTotal >= 0 && $weaponStatTotal <= 9)
            {
                $weaponGrade = "1";
            }

            if ($weaponStatTotal >= 10 && $weaponStatTotal <= 19)
            {
                $weaponGrade = "2";
            }

            if ($weaponStatTotal >= 20 && $weaponStatTotal <= 29)
            {
                $weaponGrade = "3";
            }

            if ($weaponStatTotal >= 30 && $weaponStatTotal <= 39)
            {
                $weaponGrade = "4";
            }

            if ($weaponStatTotal >= 40)
            {
                $weaponGrade = "5";
            }
        }

        // ability
        {
            /*
                - none                                      0

                - blaze                                     1
                20% increase attack if hp belows 50%

                - wonder guard                              2
                20% increase defense if hp belows 50%   

                - defeatist                                 3
                10% attack if hp belows 30%
                10% defense if hp belows 30%

                - intimidate (initial)                      4
                20% decrease on speed of enemy

                - pressure (initial)                        5
                20% decrease on attack of enemy

                - infiltrator (initial)                     6
                20% decrease on defense of enemy

                - keen eye (initial)                        7
                20% decrease hit / evasion

                - shear force (initial)                     8
                10% increase attack

                - overcoat (initial)                        9
                10% increase defense

                - reckless (initial)                        10
                10% increase speed

                - inner focus (initial)                     11
                10% increase hit rate
                10% increase evasion

                - lucky charm (initial)                     12
                10% increase crit chance

                - defiant                                   13
                prevent enemy for crit proc

                - vital spirit                              14
                prevent enemy for decreasing your stat

                - cursed                                    15
                prevent enemy for increasing stat
            */

            $weaponAbility = rand(4, 15);
        }

        // save
        $newWeaponStatData = new stdClass;
        $newWeaponStatData->weapBasePower = $weapBasePower;
        $newWeaponStatData->weapBaseEndurance = $weapBaseEndurance;
        $newWeaponStatData->weapBaseSpeed = $weapBaseSpeed;
        $newWeaponStatData->weapBaseConcentration = $weapBaseConcentration;
        $newWeaponStatData->weapRVPower = $weapRVPower;
        $newWeaponStatData->weapRVEndurance = $weapRVEndurance;
        $newWeaponStatData->weapRVSpeed = $weapRVSpeed;
        $newWeaponStatData->weapRVConcentration = $weapRVConcentration;
        $newWeaponStatData->weapStatTotal = $weaponStatTotal;
        $newWeaponStatData->weapGrade = $weaponGrade;
        $newWeaponStatData->weapAbility = $weaponAbility;

        //
        return $newWeaponStatData;
    }

    function SkillGenerate($pCharater)
    {
        //
        $skillSet = array();

        //
        if ($pCharater->class == 0)
        {
            $skillData = new stdClass();
            $skillData->name = "Struggle";
            $skillData->power = 30;
            $skillSet[] = $skillData;

            $skillData = new stdClass();
            $skillData->name = "Rush Attack";
            $skillData->power = 55;
            $skillSet[] = $skillData;

            $skillData = new stdClass();
            $skillData->name = "Meteor Assault";
            $skillData->power = 85;
            $skillSet[] = $skillData;

            $skillData = new stdClass();
            $skillData->name = "Hell's Gate";
            $skillData->power = 110;
            $skillSet[] = $skillData;
        }

         //
         if ($pCharater->class == 1)
         {
             $skillData = new stdClass();
             $skillData->name = "Pound";
             $skillData->power = 15;
             $skillSet[] = $skillData;
 
             $skillData = new stdClass();
             $skillData->name = "Reversal";
             $skillData->power = 30;
             $skillSet[] = $skillData;
 
             $skillData = new stdClass();
             $skillData->name = "Giga Impact";
             $skillData->power = 60;
             $skillSet[] = $skillData;
 
             $skillData = new stdClass();
             $skillData->name = "Guillotine";
             $skillData->power = 120;
             $skillSet[] = $skillData;
         }

          //
        if ($pCharater->class == 2)
        {
            $skillData = new stdClass();
            $skillData->name = "Spark";
            $skillData->power = 25;
            $skillSet[] = $skillData;

            $skillData = new stdClass();
            $skillData->name = "Discharge";
            $skillData->power = 50;
            $skillSet[] = $skillData;

            $skillData = new stdClass();
            $skillData->name = "Extreme Speed";
            $skillData->power = 75;
            $skillSet[] = $skillData;

            $skillData = new stdClass();
            $skillData->name = "Lightning Storm";
            $skillData->power = 100;
            $skillSet[] = $skillData;
        }

        //
        if ($pCharater->class == 3)
        {
            $skillData = new stdClass();
            $skillData->name = "Pursuit";
            $skillData->power = 30;
            $skillSet[] = $skillData;

            $skillData = new stdClass();
            $skillData->name = "Retaliation";
            $skillData->power = 50;
            $skillSet[] = $skillData;

            $skillData = new stdClass();
            $skillData->name = "Dark Void";
            $skillData->power = 70;
            $skillSet[] = $skillData;

            $skillData = new stdClass();
            $skillData->name = "Night Slash";
            $skillData->power = 90;
            $skillSet[] = $skillData;
        }

        return $skillSet;
    }

    function StatCompute($pCharater, $pWeapon)
    {
        $playerStat = new stdClass();
        $playerStat->hp = 0;
        $playerStat->hpmax = 0;
        $playerStat->attack = 0;
        $playerStat->defense = 0;
        $playerStat->speed = 0;
        $playerStat->speedrechance = 0;
        $playerStat->hit = 0;
        $playerStat->evade = 0;
        $playerStat->critical = 0;

        // Base + RV
        $pWeapon->weapPower = $pWeapon->weapBasePower + $pWeapon->weapRVPower;
        $pWeapon->weapEndurance = $pWeapon->weapBaseEndurance + $pWeapon->weapRVEndurance;
        $pWeapon->weapSpeed = $pWeapon->weapBaseSpeed + $pWeapon->weapRVSpeed;
        $pWeapon->weapConcentration = $pWeapon->weapBaseConcentration + $pWeapon->weapRVConcentration;


        // versus stats
        $playerStat->hp = (int)(((($pCharater->baseEndurance + $pCharater->baseEnduranceBonus + $pWeapon->weapEndurance) * 2) * 100) / 100) + 110;
        $playerStat->hpmax = $playerStat->hp;
        $playerStat->attack = (int)(((($pCharater->basePower + $pCharater->basePowerBonus + $pWeapon->weapPower) * 2) * 100) / 100) + 5;
        $playerStat->defense = (int)(((($pCharater->baseEndurance + $pCharater->baseEnduranceBonus + $pWeapon->weapEndurance) * 2) * 100) / 100) + 5;
        $playerStat->speed = (int)(((($pCharater->baseSpeed + $pCharater->baseSpeedBonus + $pWeapon->weapSpeed) * 2) * 100) / 100) + 5;
    
        $playerStat->hit = (int)(((($pCharater->baseConcentration + $pCharater->baseConcentrationBonus + $pWeapon->weapConcentration) * 2) * 100) / 100) + 5;
        $playerStat->evade = (int)(((($pCharater->baseConcentration + $pCharater->baseConcentrationBonus + $pWeapon->weapConcentration) * 2) * 100) / 100) + 5;
        $playerStat->critical = (int)(((($pCharater->baseConcentration + $pCharater->baseConcentrationBonus + $pWeapon->weapConcentration) * 2) * 100) / 100) + 5;

        //
        $playerStat->totalStat = (int)$pCharater->basePower + (int)$pCharater->baseEndurance + (int)$pCharater->baseSpeed + (int)$pCharater->baseConcentration + (int)$pCharater->basePowerBonus + (int)$pCharater->baseEnduranceBonus + (int)$pCharater->baseSpeedBonus + (int)$pCharater->baseConcentrationBonus + (int)$pWeapon->weapPower + (int)$pWeapon->weapEndurance + (int)$pWeapon->weapSpeed + (int)$pWeapon->weapConcentration;

        //
        return $playerStat;
    }

    // playerStat reqires playerWeaponData, playerVersusData (StatCompute), playerSkillData (SkillGenerate) property
    function VersusResult($playerStat1, $playerStat2)
    {
        // Versus PHP Data
        $p1Data = new stdClass();
        $p2Data = new stdClass();

        // Versus Unity Data
        $p1DataUnity = new stdClass();
        $p2DataUnity = new stdClass();

        //
        $versusResult = new stdClass();
        $versusResultLog = array();     
        $versusResultLogData = new stdClass();  // for unity     [ idp1 @ idp2 @ anim1 @ anim2 @ text1 @ text2 @ log ]

        $isComplete = false;

        // initial
        {
            // Debuff (Initial)
            {
                // Player 1
                {
                    if ($playerStat2->user_weap_ability != 14)
                    {
                        // Attack
                        if ($playerStat1->user_weap_ability == 5)
                        {
                            $playerStat2->user_computed_attack = (int)($playerStat2->user_computed_attack * 0.8);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat2->user_nickname . " lowers attack by 20%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Defense
                        if ($playerStat1->user_weap_ability == 6)
                        {
                            $playerStat2->user_computed_defense = (int)($playerStat2->user_computed_defense * 0.8);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat2->user_nickname . " lowers defense by 20%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Speed
                        if ($playerStat1->user_weap_ability == 4)
                        {
                            $playerStat2->user_computed_speed = (int)($playerStat2->user_computed_speed * 0.8);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat2->user_nickname . " lowers speed by 20%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Hit / Evasion
                        if ($playerStat1->user_weap_ability == 7)
                        {
                            $playerStat2->user_computed_accuracy = (int)($playerStat2->user_computed_accuracy * 0.8);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat2->user_nickname . " lowers accuracy by 20%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Hit / Evasion
                        if ($playerStat1->user_weap_ability == 7)
                        {
                            $playerStat2->user_computed_evasiveness = (int)($playerStat2->user_computed_evasiveness * 0.8);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat2->user_nickname . " lowers evasiveness by 20%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Crit
                        if ($playerStat1->user_weap_ability == 13)
                        {
                            $playerStat2->user_computed_critical = 0;

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat2->user_nickname . " cant land critical hits anymore";
                            $versusResultLog[] = $versusResultLogData;
                        }
                    }  
                    else
                    {
                        $versusResultLogData = new stdClass();
                        $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                        $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                        $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                        $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                        $versusResultLogData->log = $playerStat2->user_nickname . " blocks all debuff";
                        $versusResultLog[] = $versusResultLogData;
                    }
                }
                 
                // Player 2
                {
                    if ($playerStat1->user_weap_ability != 14)
                    {
                        // Attack
                        if ($playerStat2->user_weap_ability == 5)
                        {
                            $playerStat1->user_computed_attack = (int)($playerStat1->user_computed_attack * 0.8);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat1->user_nickname . " lowers attack by 20%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Defense
                        if ($playerStat2->user_weap_ability == 6)
                        {
                            $playerStat1->user_computed_defense = (int)($playerStat1->user_computed_defense * 0.8);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat1->user_nickname . " lowers defense by 20%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Speed
                        if ($playerStat2->user_weap_ability == 4)
                        {
                            $playerStat1->user_computed_speed = (int)($playerStat1->user_computed_speed * 0.8);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat1->user_nickname . " lowers speed by 20%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Hit / Evasion
                        if ($playerStat2->user_weap_ability == 7)
                        {
                            $playerStat1->user_computed_accuracy = (int)($playerStat1->user_computed_accuracy * 0.8);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat1->user_nickname . " lowers accuracy by 20%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Hit / Evasion
                        if ($playerStat2->user_weap_ability == 7)
                        {
                            $playerStat1->user_computed_evasiveness = (int)($playerStat1->user_computed_evasiveness * 0.8);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat1->user_nickname . " lowers evasiveness by 20%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Crit
                        if ($playerStat2->user_weap_ability == 13)
                        {
                            $playerStat1->user_computed_critical = 0;

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat1->user_nickname . " cant land critical hits anymore";
                            $versusResultLog[] = $versusResultLogData;
                        }
                    }  
                    else
                    {
                        $versusResultLogData = new stdClass();
                        $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                        $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                        $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                        $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                        $versusResultLogData->log = $playerStat1->user_nickname . " blocks all debuff";
                        $versusResultLog[] = $versusResultLogData;
                    }
                }
            }

            // Buff (Initial)
            {
                // Player 1
                {
                    if ($playerStat2->user_weap_ability != 15)
                    {
                        // Attack
                        if ($playerStat1->user_weap_ability == 8)
                        {
                            $playerStat1->user_computed_attack = (int)($playerStat1->user_computed_attack * 1.1);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat1->user_nickname . " increase attack by 10%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Defense
                        if ($playerStat1->user_weap_ability == 9)
                        {
                            $playerStat1->user_computed_defense = (int)($playerStat1->user_computed_defense * 1.1);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat1->user_nickname . " increase defense by 10%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Speed
                        if ($playerStat1->user_weap_ability == 10)
                        {
                            $playerStat1->user_computed_speed = (int)($playerStat1->user_computed_speed * 1.1);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat1->user_nickname . " increase speed by 10%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Hit / Evasion
                        if ($playerStat1->user_weap_ability == 11)
                        {
                            $playerStat1->user_computed_accuracy = (int)($playerStat1->user_computed_accuracy * 1.1);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat1->user_nickname . " increase accuracy by 10%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Hit / Evasion
                        if ($playerStat1->user_weap_ability == 11)
                        {
                            $playerStat1->user_computed_evasiveness = (int)($playerStat1->user_computed_evasiveness * 1.1);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat1->user_nickname . " increase evasiveneess by 10%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Crit
                        if ($playerStat1->user_weap_ability == 12)
                        {
                            $playerStat1->user_computed_critical = (int)($playerStat1->user_computed_critical * 1.1);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat1->user_nickname . " increase critical chance by 10%";
                            $versusResultLog[] = $versusResultLogData;
                        }
                    }  
                    else
                    {
                        $versusResultLogData = new stdClass();
                        $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                        $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                        $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                        $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                        $versusResultLogData->log = $playerStat1->user_nickname . " was prevented to receive buffs";
                        $versusResultLog[] = $versusResultLogData;
                    }
                }

                // Player 2
                {
                    if ($playerStat1->user_weap_ability != 15)
                    {
                        // Attack
                        if ($playerStat2->user_weap_ability == 8)
                        {
                            $playerStat2->user_computed_attack = (int)($playerStat2->user_computed_attack * 1.1);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat2->user_nickname . " increase attack by 10%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Defense
                        if ($playerStat2->user_weap_ability == 9)
                        {
                            $playerStat2->user_computed_defense = (int)($playerStat2->user_computed_defense * 1.1);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat2->user_nickname . " increase defense by 10%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Speed
                        if ($playerStat2->user_weap_ability == 10)
                        {
                            $playerStat2->user_computed_speed = (int)($playerStat2->user_computed_speed * 1.1);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat2->user_nickname . " increase speed by 10%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Hit / Evasion
                        if ($playerStat2->user_weap_ability == 11)
                        {
                            $playerStat2->user_computed_accuracy = (int)($playerStat2->user_computed_accuracy * 1.1);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat2->user_nickname . " increase accuracy by 10%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Hit / Evasion
                        if ($playerStat2->user_weap_ability == 11)
                        {
                            $playerStat2->user_computed_evasiveness = (int)($playerStat2->user_computed_evasiveness * 1.1);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat2->user_nickname . " increase evasiveneess by 10%";
                            $versusResultLog[] = $versusResultLogData;
                        }

                        // Crit
                        if ($playerStat2->user_weap_ability == 12)
                        {
                            $playerStat2->user_computed_critical = (int)($playerStat2->user_computed_critical * 1.1);

                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                            $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                            $versusResultLogData->log = $playerStat2->user_nickname . " increase critical chance by 10%";
                            $versusResultLog[] = $versusResultLogData;
                        }
                    }  
                    else
                    {
                        $versusResultLogData = new stdClass();
                        $versusResultLogData->{"anim" . $playerStat1->id} = "-1";
                        $versusResultLogData->{"anim" . $playerStat2->id} = "-1";
                        $versusResultLogData->{"text" . $playerStat1->id} = "-1";
                        $versusResultLogData->{"text" . $playerStat2->id} = "-1";
                        $versusResultLogData->log = $playerStat2->user_nickname . " was prevented to receive buffs";
                        $versusResultLog[] = $versusResultLogData;
                    }
                }
            }
        }

        // speed
        {
            if ($playerStat1->user_computed_speed >= $playerStat2->user_computed_speed)
            {
                $p1Data = $playerStat1;
                $p2Data = $playerStat2;
            }
            else
            {
                $p1Data = $playerStat2;
                $p2Data = $playerStat1;
            }

            $versusResultLogData = new stdClass();
            $versusResultLogData->{"anim" . $p1Data->id} = "-1";
            $versusResultLogData->{"anim" . $p2Data->id} = "-1";
            $versusResultLogData->{"text" . $p1Data->id} = "-1";
            $versusResultLogData->{"text" . $p2Data->id} = "-1";
            $versusResultLogData->log = $p1Data->user_nickname . " will take the first turn!";
            $versusResultLog[] = $versusResultLogData;
        }

        // stats
        {
            
        }

        // battle
        {
            //
            while(!$isComplete)
            {
                // hp?
                if ($p1Data->user_computed_hp > 0 && $p2Data->user_computed_hp > 0)
                {
                    //
                    { 
                        $powerDamage = 40 + ($p1Data->user_rank * 10);  
                        $finalDamage = (int)((((42 * $powerDamage * ($p1Data->user_computed_attack / $p2Data->user_computed_defense)) / 50) + 2) * (rand(80, 100) / 100));
                    }

                    // crit?
                    {
                        $isCritical = false;

                        if (rand(0, 100) <= (int)(($p1Data->user_computed_critical / 1000) * 100))
                        {
                            $finalDamage = (int)($finalDamage * 1.5);

                            $isCritical = true;
                        }
                    }

                    // hit / evade?
                    {
                        // hit
                        if (rand(0, 100) <= (int)(($p1Data->user_computed_accuracy / $p2Data->user_computed_evasiveness) * 100))
                        {
                            //
                            $p2Data->user_computed_hp = $p2Data->user_computed_hp - (int)$finalDamage;

                            if ((int)$p2Data->user_computed_hp <= 0)
                            {
                                $p2Data->user_computed_hp =- 0;
                            }

                            // crit?
                            if ($isCritical)
                            {
                                $versusResultLogData = new stdClass();
                                $versusResultLogData->{"anim" . $p1Data->id} = rand(5, 8);
                                $versusResultLogData->{"anim" . $p2Data->id} = "4";
                                $versusResultLogData->{"text" . $p1Data->id} = "-1";
                                $versusResultLogData->{"text" . $p2Data->id} = $finalDamage;
                                $versusResultLogData->{"hp" . $p1Data->id} = $p1Data->user_computed_hp;
                                $versusResultLogData->{"hp" . $p2Data->id} = $p2Data->user_computed_hp;
                                $versusResultLogData->log = $p1Data->user_nickname . " landed " . $finalDamage . " crit damage! " . $p2Data->user_nickname . " remaining HP: " . $p2Data->user_computed_hp . " / " . $p2Data->user_computed_hpmax;
                                $versusResultLog[] = $versusResultLogData;
                            }
                            
                            // normal?
                            else
                            {
                                $versusResultLogData = new stdClass();
                                $versusResultLogData->{"anim" . $p1Data->id} = rand(5, 8);
                                $versusResultLogData->{"anim" . $p2Data->id} = "4";
                                $versusResultLogData->{"text" . $p1Data->id} = "-1";
                                $versusResultLogData->{"text" . $p2Data->id} = $finalDamage;
                                $versusResultLogData->{"hp" . $p1Data->id} = $p1Data->user_computed_hp;
                                $versusResultLogData->{"hp" . $p2Data->id} = $p2Data->user_computed_hp;
                                $versusResultLogData->log = $p1Data->user_nickname . " landed " . $finalDamage . " damage! " . $p2Data->user_nickname . " remaining HP: " . $p2Data->user_computed_hp . " / " . $p2Data->user_computed_hpmax;
                                $versusResultLog[] = $versusResultLogData;
                            }
                        }

                        // miss
                        else
                        {
                            $versusResultLogData = new stdClass();
                            $versusResultLogData->{"anim" . $p1Data->id} = rand(5, 8);
                            $versusResultLogData->{"anim" . $p2Data->id} = "3";
                            $versusResultLogData->{"text" . $p1Data->id} = "-1";
                            $versusResultLogData->{"text" . $p2Data->id} = "Miss";
                            $versusResultLogData->{"hp" . $p1Data->id} = $p1Data->user_computed_hp;
                            $versusResultLogData->{"hp" . $p2Data->id} = $p2Data->user_computed_hp;
                            $versusResultLogData->log = $p1Data->user_nickname . " attack missed!";
                            $versusResultLog[] = $versusResultLogData;
                        }
                    }

                    // switch
                    $p1DataTemp = $p1Data;
                    $p1Data = $p2Data;
                    $p2Data = $p1DataTemp;
                }

                // end?
                else
                {
                    if ($p1Data->user_computed_hp > 0)
                    {
                        $versusResultLogData = new stdClass();
                        $versusResultLogData->{"anim" . $p1Data->id} = "-1";
                        $versusResultLogData->{"anim" . $p2Data->id} = "9";
                        $versusResultLogData->{"text" . $p1Data->id} = "-1";
                        $versusResultLogData->{"text" . $p2Data->id} = "-1";
                        $versusResultLogData->{"hp" . $p1Data->id} = $p1Data->user_computed_hp;
                        $versusResultLogData->{"hp" . $p2Data->id} = $p2Data->user_computed_hp;
                        $versusResultLogData->log = $p1Data->user_nickname . " wins";
                        $versusResultLog[] = $versusResultLogData;

                        $isComplete = true;

                        //
                        $versusResult->versusWinId = $p1Data->id;
                        $versusResult->versusLoseId = $p2Data->id;
                    }
                    
                    if ($p2Data->user_computed_hp > 0)
                    {
                        $versusResultLogData = new stdClass();
                        $versusResultLogData->{"anim" . $p1Data->id} = "9";
                        $versusResultLogData->{"anim" . $p2Data->id} = "-1";
                        $versusResultLogData->{"text" . $p1Data->id} = "-1";
                        $versusResultLogData->{"text" . $p2Data->id} = "-1";
                        $versusResultLogData->{"hp" . $p1Data->id} = $p1Data->user_computed_hp;
                        $versusResultLogData->{"hp" . $p2Data->id} = $p2Data->user_computed_hp;
                        $versusResultLogData->log = $p2Data->user_nickname . " wins";
                        $versusResultLog[] = $versusResultLogData;

                        $isComplete = true;

                        //
                        $versusResult->versusWinId = $p2Data->id;
                        $versusResult->versusLoseId = $p1Data->id;
                    }
                }
            }
        }
        
        // data
        {
            // this is for unity arena UI
            // Note: Get this first in unity, and know id of player and target before going to logs one by one, so unity knows which model play base on id obj . {id}

            $p1DataUnity->id = $playerStat1->id;
            $p1DataUnity->user_nickname = $playerStat1->user_nickname;
            $p1DataUnity->user_class = $playerStat1->user_class;
            $p1DataUnity->user_rank = $playerStat1->user_rank;
            $p1DataUnity->user_computed_hpmax = $playerStat1->user_computed_hpmax;

            $p2DataUnity->id = $playerStat2->id;
            $p2DataUnity->user_nickname = $playerStat2->user_nickname;
            $p2DataUnity->user_class = $playerStat2->user_class;
            $p2DataUnity->user_rank = $playerStat2->user_rank;
            $p2DataUnity->user_computed_hpmax = $playerStat2->user_computed_hpmax;
        }

        //
        $versusResult->p1DataUnity = $p1DataUnity;
        $versusResult->p2DataUnity = $p2DataUnity;
        $versusResult->log = $versusResultLog;

        return $versusResult;
    }



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
    function ValidText($text, $minText = 4, $maxText = 15)
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